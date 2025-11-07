<?php

namespace App\Controllers;

use App\Models\ImportCplPiModel;
use App\Models\ImportMatkulModel;

use App\Libraries\ChunkReadFilter;


class ImportData extends BaseController
{
    public function index()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get the current user's NPP from the session
        $currentUserNPP = session()->get('UserSession.username');

        return view('backend/import-data/index');
    }

    public function saveImportCplPi()
    {
        // Tetapkan batas memori yang cukup
        ini_set('memory_limit', '512M');
        
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validate file 
        $validationRules = [
            'file_cpl_pi' => [
                'rules' => 'uploaded[file_cpl_pi]|ext_in[file_cpl_pi,xls,xlsx]|max_size[file_cpl_pi,51200]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'ext_in' => 'Format file harus xls atau xlsx',
                    'max_size' => 'Ukuran file maksimal 50MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to('/import-data')->with('error', $this->validator->getError('file_cpl_pi'));
        }

        $file = $this->request->getFile('file_cpl_pi');
        
        if (!$file->isValid()) {
            return redirect()->to('/import-data')->with('error', 'File gagal diupload');
        }

        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/excel/', $fileName);
        $filePath = WRITEPATH . 'uploads/excel/' . $fileName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';
            
            // Gunakan Box/Spout untuk membaca file Excel secara streaming
            $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($filePath);
            
            // Buka file
            $reader->open($filePath);
            
            // Ambil header (baris pertama)
            $headerRow = null;
            $columnMap = [];
            $headers = [];
            
            // Prepare untuk database
            $model = new \App\Models\ImportCplPiModel();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            
            // Baca file baris demi baris
            $isFirstRow = true;
            $rowNumber = 0;
            $batchData = [];
            $batchSize = 500;
            
            // Iterate through all sheets
            foreach ($reader->getSheetIterator() as $sheet) {
                // Baca hanya sheet pertama
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    
                    // Baca header di baris pertama
                    if ($isFirstRow) {
                        $headerRow = $row->getCells();
                        foreach ($headerRow as $colIndex => $cell) {
                            $headerValue = $cell->getValue();
                            $headers[$colIndex] = $headerValue;
                            
                            if (in_array($headerValue, [
                                'kurikulum', 'matkul', 'kode_matkul', 
                                'id_matkul', 'no_cpl', 'cpl_indo', 'cpl_inggris', 'id_cpl', 
                                'no_pi', 'isi_pi', 'id_pi'
                            ])) {
                                $columnMap[$colIndex] = $headerValue;
                            }
                        }
                        
                        // Validasi required fields
                        $requiredFields = ['kurikulum', 'kode_matkul', 'no_cpl', 'no_pi'];
                        $missingFields = array_diff($requiredFields, array_values($columnMap));
                        
                        if (!empty($missingFields)) {
                            $reader->close();
                            @unlink($filePath);
                            return redirect()->to('/import-data')->with('error', 'Format file tidak sesuai. Field yang diperlukan: ' . implode(', ', $missingFields));
                        }
                        
                        $isFirstRow = false;
                        continue;
                    }
                    
                    // Proses data baris
                    $rowData = [];
                    $isEmpty = true;
                    $cells = $row->getCells();
                    
                    foreach ($columnMap as $colIndex => $fieldName) {
                        $cellValue = isset($cells[$colIndex]) ? $cells[$colIndex]->getValue() : '';
                        
                        if (!empty($cellValue) || $cellValue === '0') {
                            $isEmpty = false;
                        }
                        
                        $rowData[$fieldName] = $cellValue;
                    }
                    
                    // Skip baris kosong
                    if ($isEmpty) {
                        continue;
                    }
                    
                    // Tambahkan timestamp
                    $rowData['ins_time'] = date('Y-m-d H:i:s');
                    $rowData['upd_time'] = date('Y-m-d H:i:s');
                    
                    $batchData[] = $rowData;
                    
                    // Proses batch jika sudah mencapai ukuran batch
                    if (count($batchData) >= $batchSize) {
                        try {
                            $model->insertBatchData($batchData);
                            $successCount += count($batchData);
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorMessages[] = "Error pada baris sekitar " . ($rowNumber - count($batchData)) . ": " . $e->getMessage();
                        }
                        
                        // Reset batch data
                        $batchData = [];
                        
                        // Force garbage collection
                        gc_collect_cycles();
                    }
                }
                
                // Kita hanya perlu sheet pertama
                break;
            }
            
            // Proses sisa data
            if (count($batchData) > 0) {
                try {
                    $model->insertBatchData($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                }
            }
            
            // Tutup reader
            $reader->close();
            
            // Hapus file
            @unlink($filePath);
            
            if ($errorCount > 0) {
                return redirect()->to('/import-data')->with('error', 'Terdapat error saat import data. ' . implode('<br>', $errorMessages));
            } else {
                return redirect()->to('/import-data')->with('success', "Berhasil mengimpor $successCount data ke database.");
            }
            
        } catch (\Exception $e) {
            // Bersihkan resource
            if (isset($reader) && $reader) {
                $reader->close();
            }
            
            @unlink($filePath);
            return redirect()->to('/import-data')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveImportMatkul()
    {
        // Tetapkan batas memori yang cukup
        ini_set('memory_limit', '512M');
        
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validate file 
        $validationRules = [
            'file_mata_kuliah' => [
                'rules' => 'uploaded[file_mata_kuliah]|ext_in[file_mata_kuliah,xls,xlsx]|max_size[file_mata_kuliah,51200]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'ext_in' => 'Format file harus xls atau xlsx',
                    'max_size' => 'Ukuran file maksimal 50MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to('/import-data')->with('error', $this->validator->getError('file_mata_kuliah'));
        }

        $file = $this->request->getFile('file_mata_kuliah');
        
        if (!$file->isValid()) {
            return redirect()->to('/import-data')->with('error', 'File gagal diupload');
        }

        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/excel/', $fileName);
        $filePath = WRITEPATH . 'uploads/excel/' . $fileName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';
            
            // Gunakan Box/Spout untuk membaca file Excel secara streaming
            $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($filePath);
            
            // Buka file
            $reader->open($filePath);
            
            // Ambil header (baris pertama)
            $headerRow = null;
            $columnMap = [];
            $headers = [];
            
            // Prepare untuk database
            $model = new \App\Models\ImportMatkulModel();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            
            // Baca file baris demi baris
            $isFirstRow = true;
            $rowNumber = 0;
            $batchData = [];
            $batchSize = 500;
            
            // Iterate through all sheets
            foreach ($reader->getSheetIterator() as $sheet) {
                // Baca hanya sheet pertama
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    
                    // Baca header di baris pertama
                    if ($isFirstRow) {
                        $headerRow = $row->getCells();
                        foreach ($headerRow as $colIndex => $cell) {
                            $headerValue = $cell->getValue();
                            $headers[$colIndex] = $headerValue;
                            
                            if (in_array($headerValue, [
                                'matakuliah', 'kode_matkul', 'kelp_matkul', 'smt_matkul',
                                'jenis_matkul', 'teori', 'praktek', 'tipe_matkul',
                                'kurikulum', 'prodi', 'jenjang', 'fakultas'
                            ])) {
                                $columnMap[$colIndex] = $headerValue;
                            }
                        }
                        
                        // Validasi required fields
                        $requiredFields = ['matakuliah', 'kode_matkul', 'kurikulum'];
                        $missingFields = array_diff($requiredFields, array_values($columnMap));
                        
                        if (!empty($missingFields)) {
                            $reader->close();
                            @unlink($filePath);
                            return redirect()->to('/import-data')->with('error', 'Format file tidak sesuai. Field yang diperlukan: ' . implode(', ', $missingFields));
                        }
                        
                        $isFirstRow = false;
                        continue;
                    }
                    
                    // Proses data baris
                    $rowData = [];
                    $isEmpty = true;
                    $cells = $row->getCells();
                    
                    foreach ($columnMap as $colIndex => $fieldName) {
                        $cellValue = isset($cells[$colIndex]) ? $cells[$colIndex]->getValue() : '';
                        
                        if (!empty($cellValue) || $cellValue === '0') {
                            $isEmpty = false;
                        }
                        
                        $rowData[$fieldName] = $cellValue;
                    }
                    
                    // Skip baris kosong
                    if ($isEmpty) {
                        continue;
                    }
                    
                    // Pastikan kolom numerik adalah angka
                    $numericColumns = ['teori', 'praktek', 'smt_matkul'];
                    foreach ($numericColumns as $column) {
                        if (isset($rowData[$column])) {
                            $rowData[$column] = !empty($rowData[$column]) ? (int)$rowData[$column] : 0;
                        } else {
                            $rowData[$column] = 0;
                        }
                    }
                    
                    // Tambahkan timestamp
                    $rowData['ins_time'] = date('Y-m-d H:i:s');
                    $rowData['upd_time'] = date('Y-m-d H:i:s');
                    
                    $batchData[] = $rowData;
                    
                    // Proses batch jika sudah mencapai ukuran batch
                    if (count($batchData) >= $batchSize) {
                        try {
                            $model->insertBatchData($batchData);
                            $successCount += count($batchData);
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorMessages[] = "Error pada baris sekitar " . ($rowNumber - count($batchData)) . ": " . $e->getMessage();
                        }
                        
                        // Reset batch data
                        $batchData = [];
                        
                        // Force garbage collection
                        gc_collect_cycles();
                    }
                }
                
                // Kita hanya perlu sheet pertama
                break;
            }
            
            // Proses sisa data
            if (count($batchData) > 0) {
                try {
                    $model->insertBatchData($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                }
            }
            
            // Tutup reader
            $reader->close();
            
            // Hapus file
            @unlink($filePath);
            
            if ($errorCount > 0) {
                return redirect()->to('/import-data')->with('error', 'Terdapat error saat import data. ' . implode('<br>', $errorMessages));
            } else {
                return redirect()->to('/import-data')->with('success', "Berhasil mengimpor $successCount data mata kuliah ke database.");
            }
            
        } catch (\Exception $e) {
            // Bersihkan resource
            if (isset($reader) && $reader) {
                $reader->close();
            }
            
            @unlink($filePath);
            return redirect()->to('/import-data')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveImportMatkulDiampu()
    {
        // Tetapkan batas memori yang cukup
        ini_set('memory_limit', '512M');
        
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validate file 
        $validationRules = [
            'file_mata_kuliah_diampu' => [
                'rules' => 'uploaded[file_mata_kuliah_diampu]|ext_in[file_mata_kuliah_diampu,xls,xlsx]|max_size[file_mata_kuliah_diampu,51200]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'ext_in' => 'Format file harus xls atau xlsx',
                    'max_size' => 'Ukuran file maksimal 50MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to('/import-data')->with('error', $this->validator->getError('file_mata_kuliah_diampu'));
        }

        $file = $this->request->getFile('file_mata_kuliah_diampu');
        
        if (!$file->isValid()) {
            return redirect()->to('/import-data')->with('error', 'File gagal diupload');
        }

        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/excel/', $fileName);
        $filePath = WRITEPATH . 'uploads/excel/' . $fileName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';
            
            // Gunakan Box/Spout untuk membaca file Excel secara streaming
            $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($filePath);
            
            // Buka file
            $reader->open($filePath);
            
            // Ambil header (baris pertama)
            $headerRow = null;
            $columnMap = [];
            $headers = [];
            
            // Prepare untuk database
            $model = new \App\Models\ImportMatkulDiampuModel();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            
            // Baca file baris demi baris
            $isFirstRow = true;
            $rowNumber = 0;
            $batchData = [];
            $batchSize = 500;
            
            // Iterate through all sheets
            foreach ($reader->getSheetIterator() as $sheet) {
                // Baca hanya sheet pertama
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    
                    // Baca header di baris pertama
                    if ($isFirstRow) {
                        $headerRow = $row->getCells();
                        foreach ($headerRow as $colIndex => $cell) {
                            $headerValue = $cell->getValue();
                            $headers[$colIndex] = $headerValue;
                            
                            if (in_array($headerValue, [
                                'matkul', 'kode_matkul', 'id_matkul', 'kelp_matkul', 
                                'id_kelas', 'dosen', 'npp', 'id_dosen'
                            ])) {
                                $columnMap[$colIndex] = $headerValue;
                            }
                        }
                        
                        // Validasi required fields
                        $requiredFields = ['matkul', 'kode_matkul', 'dosen', 'npp'];
                        $missingFields = array_diff($requiredFields, array_values($columnMap));
                        
                        if (!empty($missingFields)) {
                            $reader->close();
                            @unlink($filePath);
                            return redirect()->to('/import-data')->with('error', 'Format file tidak sesuai. Field yang diperlukan: ' . implode(', ', $missingFields));
                        }
                        
                        $isFirstRow = false;
                        continue;
                    }
                    
                    // Proses data baris
                    $rowData = [];
                    $isEmpty = true;
                    $cells = $row->getCells();
                    
                    foreach ($columnMap as $colIndex => $fieldName) {
                        $cellValue = isset($cells[$colIndex]) ? $cells[$colIndex]->getValue() : '';
                        
                        if (!empty($cellValue) || $cellValue === '0') {
                            $isEmpty = false;
                        }
                        
                        $rowData[$fieldName] = $cellValue;
                    }
                    
                    // Skip baris kosong
                    if ($isEmpty) {
                        continue;
                    }
                    
                    // Konversi ID fields ke bigint jika ada
                    $idFields = ['id_matkul', 'id_kelas', 'id_dosen'];
                    foreach ($idFields as $field) {
                        if (isset($rowData[$field]) && $rowData[$field] !== '') {
                            $rowData[$field] = (int)$rowData[$field];
                        } else {
                            // Atur NULL jika kosong (akan dikonversi ke NULL di database)
                            $rowData[$field] = null;
                        }
                    }
                    
                    // Tambahkan timestamp
                    $rowData['ins_time'] = date('Y-m-d H:i:s');
                    $rowData['upd_time'] = date('Y-m-d H:i:s');
                    
                    $batchData[] = $rowData;
                    
                    // Proses batch jika sudah mencapai ukuran batch
                    if (count($batchData) >= $batchSize) {
                        try {
                            $model->insertBatchData($batchData);
                            $successCount += count($batchData);
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorMessages[] = "Error pada baris sekitar " . ($rowNumber - count($batchData)) . ": " . $e->getMessage();
                        }
                        
                        // Reset batch data
                        $batchData = [];
                        
                        // Force garbage collection
                        gc_collect_cycles();
                    }
                }
                
                // Kita hanya perlu sheet pertama
                break;
            }
            
            // Proses sisa data
            if (count($batchData) > 0) {
                try {
                    $model->insertBatchData($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                }
            }
            
            // Tutup reader
            $reader->close();
            
            // Hapus file
            @unlink($filePath);
            
            if ($errorCount > 0) {
                return redirect()->to('/import-data')->with('error', 'Terdapat error saat import data mata kuliah diampu. ' . implode('<br>', $errorMessages));
            } else {
                return redirect()->to('/import-data')->with('success', "Berhasil mengimpor $successCount data mata kuliah diampu ke database.");
            }
            
        } catch (\Exception $e) {
            // Bersihkan resource
            if (isset($reader) && $reader) {
                $reader->close();
            }
            
            @unlink($filePath);
            return redirect()->to('/import-data')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveMahasiswaKelas()
    {
        // Set memory limit
        ini_set('memory_limit', '512M');
        
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validate file
        $validationRules = [
            'importFile' => [
                'rules' => 'uploaded[importFile]|ext_in[importFile,xls,xlsx]|max_size[importFile,51200]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'ext_in' => 'Format file harus xls atau xlsx',
                    'max_size' => 'Ukuran file maksimal 50MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->with('error', $this->validator->getError('importFile'));
        }

        $file = $this->request->getFile('importFile');
        
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File gagal diupload');
        }

        // Get course information from form
        $kodeMatkul = $this->request->getPost('kode_matkul');
        $matkul = $this->request->getPost('matkul');
        $kelpMatkul = $this->request->getPost('kelp_matkul');
        $kodeTs = $this->request->getPost('kode_ts');
        
        // Validate required course data
        if (empty($kodeMatkul) || empty($matkul)) {
            return redirect()->back()->with('error', 'Data mata kuliah tidak lengkap');
        }

        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/excel/', $fileName);
        $filePath = WRITEPATH . 'uploads/excel/' . $fileName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';
            
            // Use PhpSpreadsheet to read Excel file
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Prepare for database
            $model = new \App\Models\MahasiswaKelasModel();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            
            // Define column indexes
            $nimColIndex = null;
            $namaColIndex = null;
            $startDataRow = null;
            
            // Find header row containing "NIM" or similar
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Look for header row (up to row 20)
            for ($row = 1; $row <= min(20, $highestRow); ++$row) {
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    // Convert column index to column letter
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $cellValue = trim((string)$worksheet->getCell($columnLetter . $row)->getValue());
                    
                    if ($cellValue === 'NIM') {
                        $nimColIndex = $col;
                        $startDataRow = $row + 1; // Data starts after header
                    }
                    
                    if ($cellValue === 'Nama Mahasiswa' || $cellValue === 'Nama') {
                        $namaColIndex = $col;
                    }
                }
                
                if ($nimColIndex !== null) {
                    break;
                }
            }
            
            // If NIM column not found
            if ($nimColIndex === null) {
                @unlink($filePath);
                return redirect()->back()->with('error', 'Format file tidak sesuai. Kolom NIM tidak ditemukan.');
            }
            
            // If Nama column not found, try to find any column with "Nama" in it
            if ($namaColIndex === null) {
                for ($row = 1; $row <= min(20, $highestRow); ++$row) {
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $cellValue = trim((string)$worksheet->getCell($columnLetter . $row)->getValue());
                        
                        if (strpos(strtolower($cellValue), 'nama') !== false) {
                            $namaColIndex = $col;
                            break 2; // Exit both loops
                        }
                    }
                }
            }
            
            // If still not found, assume it's the column after NIM
            if ($namaColIndex === null) {
                $namaColIndex = $nimColIndex + 1;
            }
            
            // Process data rows
            $batchData = [];
            $batchSize = 500;
            
            for ($row = $startDataRow; $row <= $highestRow; ++$row) {
                // Get the row number or first cell to check if it's a data row
                $firstColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1);
                $rowNumber = trim((string)$worksheet->getCell($firstColLetter . $row)->getValue());
                
                // Get NIM and Nama values
                $nimColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nimColIndex);
                $namaColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($namaColIndex);
                
                $nim = trim((string)$worksheet->getCell($nimColLetter . $row)->getValue());
                $nama = trim((string)$worksheet->getCell($namaColLetter . $row)->getValue());
                
                // Skip if NIM is empty
                if (empty($nim)) {
                    continue;
                }
                
                // Prepare data for insertion
                $rowData = [
                    'nim' => $nim,
                    'nama' => $nama,
                    'kode_matkul' => $kodeMatkul,
                    'matkul' => $matkul,
                    'kelp_matkul' => $kelpMatkul,
                    'kode_ts' => $kodeTs,
                    'ins_time' => date('Y-m-d H:i:s'),
                    'upd_time' => date('Y-m-d H:i:s')
                ];
                
                $batchData[] = $rowData;
                
                // Process batch if reached batch size
                if (count($batchData) >= $batchSize) {
                    try {
                        $model->insertBatchData($batchData);
                        $successCount += count($batchData);
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMessages[] = "Error pada baris sekitar " . ($row - count($batchData)) . ": " . $e->getMessage();
                    }
                    
                    // Reset batch data
                    $batchData = [];
                    
                    // Force garbage collection
                    gc_collect_cycles();
                }
            }
            
            // Process remaining data
            if (count($batchData) > 0) {
                try {
                    $model->insertBatchData($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                }
            }
            
            // Clean up
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            @unlink($filePath);
            
            if ($successCount == 0) {
                return redirect()->back()->with('error', 'Tidak ada data mahasiswa yang berhasil diimpor. Periksa format file Excel Anda.');
            } else if ($errorCount > 0) {
                return redirect()->back()->with('error', 'Terdapat error saat import data mahasiswa. ' . implode('<br>', $errorMessages));
            } else {
                return redirect()->back()->with('success', "Berhasil mengimpor $successCount data mahasiswa ke database.");
            }
            
        } catch (\Exception $e) {
            // Clean up resources
            @unlink($filePath);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplateCplPi()
    {
        // Load library untuk membuat file Excel
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $headers = [
            'kurikulum', 'matkul', 'kode_matkul', 
            'id_matkul', 'no_cpl', 'cpl_indo', 'cpl_inggris', 'id_cpl', 
            'no_pi', 'isi_pi', 'id_pi'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Styling header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray($styleArray);
        
        // Contoh data
        
        // Auto size kolom
        foreach (range('A', chr(64 + count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'template_cpl_pi.xlsx';
        
        // Redirect output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function downloadTemplateMatkul()
    {
        // Load library untuk membuat file Excel
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $headers = [
            'matakuliah', 'kode_matkul', 'kelp_matkul', 
            'smt_matkul', 'jenis_matkul', 'teori', 'praktek', 'tipe_matkul', 
            'kurikulum', 'prodi', 'jenjang', 'fakultas'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Styling header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray($styleArray);
        
        // Contoh data
        
        // Auto size kolom
        foreach (range('A', chr(64 + count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'template_matkul.xlsx';
        
        // Redirect output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function saveImportUser()
    {
        // Tetapkan batas memori yang cukup
        ini_set('memory_limit', '512M');
        
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validate file 
        $validationRules = [
            'file_user' => [
                'rules' => 'uploaded[file_user]|ext_in[file_user,xls,xlsx]|max_size[file_user,51200]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'ext_in' => 'Format file harus xls atau xlsx',
                    'max_size' => 'Ukuran file maksimal 50MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->to('/import-data')->with('error', $this->validator->getError('file_user'));
        }

        $file = $this->request->getFile('file_user');
        
        if (!$file->isValid()) {
            return redirect()->to('/import-data')->with('error', 'File gagal diupload');
        }

        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/excel/', $fileName);
        $filePath = WRITEPATH . 'uploads/excel/' . $fileName;

        try {
            require_once ROOTPATH . 'vendor/autoload.php';
            
            // Gunakan Box/Spout untuk membaca file Excel secara streaming
            $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($filePath);
            
            // Buka file
            $reader->open($filePath);
            
            // Ambil header (baris pertama)
            $headerRow = null;
            $columnMap = [];
            $headers = [];
            
            // Prepare untuk database
            $model = new \App\Models\UserModel();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            $duplicateCount = 0;
            $duplicateUsers = [];
            
            // Baca file baris demi baris
            $isFirstRow = true;
            $rowNumber = 0;
            $batchData = [];
            $batchSize = 500;
            
            // Iterate through all sheets
            foreach ($reader->getSheetIterator() as $sheet) {
                // Baca hanya sheet pertama
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    
                    // Baca header di baris pertama
                    if ($isFirstRow) {
                        $headerRow = $row->getCells();
                        foreach ($headerRow as $colIndex => $cell) {
                            $headerValue = $cell->getValue();
                            $headers[$colIndex] = $headerValue;
                            
                            if (in_array($headerValue, [
                                'username', 'password'
                            ])) {
                                $columnMap[$colIndex] = $headerValue;
                            }
                        }
                        
                        // Validasi required fields
                        $requiredFields = ['username', 'password'];
                        $missingFields = array_diff($requiredFields, array_values($columnMap));
                        
                        if (!empty($missingFields)) {
                            $reader->close();
                            @unlink($filePath);
                            return redirect()->to('/import-data')->with('error', 'Format file tidak sesuai. Field yang diperlukan: ' . implode(', ', $missingFields));
                        }
                        
                        $isFirstRow = false;
                        continue;
                    }
                    
                    // Proses data baris
                    $rowData = [];
                    $isEmpty = true;
                    $cells = $row->getCells();
                    
                    foreach ($columnMap as $colIndex => $fieldName) {
                        $cellValue = isset($cells[$colIndex]) ? $cells[$colIndex]->getValue() : '';
                        
                        if (!empty($cellValue) || $cellValue === '0') {
                            $isEmpty = false;
                        }
                        
                        $rowData[$fieldName] = $cellValue;
                    }
                    
                    // Skip baris kosong
                    if ($isEmpty) {
                        continue;
                    }
                    
                    // Validate required fields
                    if (empty($rowData['username']) || empty($rowData['password'])) {
                        $errorCount++;
                        $errorMessages[] = "Error pada baris $rowNumber: Username dan password harus diisi.";
                        continue;
                    }

                    // Check if username already exists
                    $existingUser = $model->where('username', $rowData['username'])->first();
                    if ($existingUser) {
                        $duplicateCount++;
                        $duplicateUsers[] = $rowData['username'] . "(baris $rowNumber)";
                        continue;
                    }

                    // Encrypt password using the same method as UserModel
                    $rowData['password'] = $model->encryptPassword($rowData['password']);

                    // Tambahkan timestamp
                    $rowData['ins_time'] = date('Y-m-d H:i:s');
                    $rowData['upd_time'] = date('Y-m-d H:i:s');

                    $batchData[] = $rowData;
                    
                    // Proses batch jika sudah mencapai ukuran batch
                    if (count($batchData) >= $batchSize) {
                        try {
                            // Insert each record one by one to handle potential errors
                            foreach ($batchData as $userData) {
                                try {
                                    $model->insert($userData);
                                    $successCount++;
                                } catch (\Exception $e) {
                                    $errorCount++;
                                    $errorMessages[] = "Error pada baris saat insert: " . $e->getMessage();
                                }
                            }
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorMessages[] = "Error pada batch sekitar baris " . ($rowNumber - count($batchData)) . ": " . $e->getMessage();
                        }
                        
                        // Reset batch data
                        $batchData = [];
                        
                        // Force garbage collection
                        gc_collect_cycles();
                    }
                }
                
                // Kita hanya perlu sheet pertama
                break;
            }
            
            // Proses sisa data
            if (count($batchData) > 0) {
                try {
                    // Insert remaining records
                    foreach ($batchData as $userData) {
                        try {
                            $model->insert($userData);
                            $successCount++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                        }
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errorMessages[] = "Error pada batch terakhir: " . $e->getMessage();
                }
            }
            
            // Tutup reader
            $reader->close();
            
            // Hapus file
            @unlink($filePath);
            
            $message = "";
            if ($successCount > 0) {
                $message .= "Berhasil mengimpor $successCount data user ke database.";
            }
            
            if ($duplicateCount > 0) {
                $message .= " $duplicateCount user telah ada sebelumnya: " . implode(', ', $duplicateUsers);
            }
            
            if ($errorCount > 0) {
                $message .= " Terdapat error saat import data. " . implode('<br>', $errorMessages);
                return redirect()->to('/import-data')->with('error', $message);
            } else {
                return redirect()->to('/import-data')->with('success', $message);
            }
            
        } catch (\Exception $e) {
            // Bersihkan resource
            if (isset($reader) && $reader) {
                $reader->close();
            }
            
            @unlink($filePath);
            return redirect()->to('/import-data')->with('error', 'Error: ' . $e->getMessage());
        }
    }



    public function downloadTemplateMatkulDiampu()
    {
        // Load library untuk membuat file Excel
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $headers = [
            'matkul', 'kode_matkul', 'id_matkul', 
            'kelp_matkul', 'id_kelas', 'dosen', 'npp', 'id_dosen'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Styling header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray($styleArray);
        
        // Contoh data
        
        // Auto size kolom
        foreach (range('A', chr(64 + count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'template_matkul_diampu.xlsx';
        
        // Redirect output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function downloadTemplateUser()
    {
        // Load library untuk membuat file Excel
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $headers = [
            'username', 'password'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Styling header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
        ];
        
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray($styleArray);
        
        // Contoh data - menambahkan satu baris contoh
        $sheet->setCellValue('A2', 'admin');
        $sheet->setCellValue('B2', 'password123');
        $sheet->setCellValue('A3', 'user1');
        $sheet->setCellValue('B3', 'password456');
        
        // Auto size kolom
        foreach (range('A', chr(64 + count($headers))) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'template_user.xlsx';
        
        // Redirect output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}

?>