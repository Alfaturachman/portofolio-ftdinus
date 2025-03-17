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
}

?>