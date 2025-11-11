<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CplModel;
use App\Models\RpsModel;
use App\Models\CpmkModel;
use App\Models\SubCpmkModel;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\PortofolioModel;
use App\Controllers\BaseController;
use App\Models\HasilAsesmenModel;
use App\Models\MappingCpmkScpmkModel;
use App\Models\RancanganAsesmenModel;
use App\Models\RancanganAsesmenFileModel;
use App\Models\PelaksanaanPerkuliahanModel;
use App\Models\RancanganSoalModel;

class Cetak extends BaseController
{
    public function index($idPorto)
    {
        $viewData = $this->prepareViewData($idPorto);
        return view('backend/pdf/cetak-portofolio', $viewData);
    }

    private function prepareViewData($idPorto)
    {
        // Load semua model
        $portofolioModel = new PortofolioModel();
        $cplModel = new CplModel();
        $cpmkModel = new CpmkModel();
        $subCpmkModel = new SubCpmkModel();
        $mappingModel = new MappingCpmkScpmkModel();
        $asesmenModel = new RancanganAsesmenModel();
        $asesmenSoalModel = new RancanganSoalModel();

        // Ambil data dari model
        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);
        $cplPiData = $cplModel->getCplPiByPortoId($idPorto);
        $cplData = $cplModel->getCplByPortoId($idPorto);
        $cpmkData = $cpmkModel->getCpmkByPorto($idPorto);
        $subCpmkData = $subCpmkModel->getSubCpmkByPorto($idPorto);
        $assessmentData = $asesmenModel->getAssessmentData($idPorto);
        $assessmentSoalData = $asesmenSoalModel->getAssessmentSoalData($idPorto);

        $chartImageBase64 = $this->generateChartImage($cpmkData);

        // Ambil data mapping
        $mappingData = $mappingModel->getMapping($idPorto);

        // Dapatkan semua nomor Sub-CPMK untuk header tabel
        $subCpmkNumbers = array_column($subCpmkData, 'no_scpmk');

        return [
            'portofolioData' => $portofolioData,
            'cplPiData' => $cplPiData,
            'cplData' => $cplData,
            'cpmkData' => $cpmkData,
            'chartImageBase64' => $chartImageBase64,
            'subCpmkData' => $subCpmkData,
            'mappingData' => $mappingData,
            'subCpmkNumbers' => $subCpmkNumbers,
            'assessmentData' => $assessmentData,
            'assessmentSoalData' => $assessmentSoalData
        ];
    }

    public function generatePdf($idPorto)
    {
        // 1. Ambil data untuk view
        $viewData = $this->prepareViewData($idPorto);

        // 2. Render view menjadi HTML
        $html = view('backend/pdf/cetak-portofolio', $viewData);

        // 3. Ambil file RPS dari database
        $rpsModel = new RpsModel();
        $rpsData = $rpsModel->where('id_porto', $idPorto)->first();

        if (!$rpsData || empty($rpsData['file_rps'])) {
            return $this->response->setStatusCode(404, 'File RPS tidak ditemukan dalam database');
        }

        $existingRpsPdf = WRITEPATH . 'uploads/rps/' . $rpsData['file_rps'];
        if (!file_exists($existingRpsPdf)) {
            return $this->response->setStatusCode(404, 'File PDF yang dimaksud tidak ditemukan di server');
        }

        // 4. Ambil file tambahan berdasarkan kategori dari database
        $rancanganAsesmenFileModel = new RancanganAsesmenFileModel();
        $additionalFiles = $rancanganAsesmenFileModel->where('id_porto', $idPorto)->findAll();

        // 5. Ambil file pelaksanaan perkuliahan dari database
        $pelaksanaanPerkuliahanModel = new PelaksanaanPerkuliahanModel();
        $pelaksanaanData = $pelaksanaanPerkuliahanModel->where('id_porto', $idPorto)->first();

        // 6. Ambil file hasil asesmen dari database
        $hasilAsesmenModel = new HasilAsesmenModel();
        $hasilAsesmenData = $hasilAsesmenModel->where('id_porto', $idPorto)->first();

        // 7. Kelompokkan file berdasarkan kategori
        $filePaths = [
            'RPS'                => $existingRpsPdf,
            'TUGAS'              => null,
            'UTS'                => null,
            'UAS'                => null,
            'HASIL_TUGAS'        => null,
            'HASIL_UTS'          => null,
            'HASIL_UAS'          => null,
            'NILAI_MATA_KULIAH'  => null,
            'NILAI_CPMK'         => null,
        ];

        // Tambahkan file rancangan asesmen
        foreach ($additionalFiles as $file) {
            $filePath = WRITEPATH . $file['file_pdf'];
            if (file_exists($filePath) && isset($file['kategori'])) {
                switch (strtoupper($file['kategori'])) {
                    case 'TUGAS':
                        $filePaths['TUGAS'] = $filePath;
                        break;
                    case 'UTS':
                        $filePaths['UTS'] = $filePath;
                        break;
                    case 'UAS':
                        $filePaths['UAS'] = $filePath;
                        break;
                    case 'HASIL_TUGAS':
                        $filePaths['HASIL_TUGAS'] = $filePath;
                        break;
                    case 'HASIL_UTS':
                        $filePaths['HASIL_UTS'] = $filePath;
                        break;
                    case 'HASIL_UAS':
                        $filePaths['HASIL_UAS'] = $filePath;
                        break;
                    case 'NILAI_MATA_KULIAH':
                        $filePaths['NILAI_MATA_KULIAH'] = $filePath;
                        break;
                    case 'NILAI_CPMK':
                        $filePaths['NILAI_CPMK'] = $filePath;
                        break;
                    default:
                        break;
                }
            }
        }

        // Tambahkan file pelaksanaan perkuliahan
        if ($pelaksanaanData) {
            $pelaksanaanFields = [
                'file_kontrak'   => 'KONTRAK',
                'file_realisasi' => 'REALISASI',
                'file_kehadiran' => 'KEHADIRAN'
            ];

            foreach ($pelaksanaanFields as $dbField => $key) {
                if (!empty($pelaksanaanData[$dbField])) {
                    $path = WRITEPATH . $pelaksanaanData[$dbField];
                    if (file_exists($path)) {
                        $filePaths[$key] = $path;
                    }
                }
            }
        }

        // Tambahkan file hasil asesmen
        if ($hasilAsesmenData) {
            $hasilFields = [
                'file_tugas'        => 'HASIL_TUGAS',
                'file_uts'          => 'HASIL_UTS',
                'file_uas'          => 'HASIL_UAS',
                'file_nilai_mk'     => 'NILAI_MATA_KULIAH',
                'file_nilai_cpmk'   => 'NILAI_CPMK',
            ];

            foreach ($hasilFields as $dbField => $key) {
                if (!empty($hasilAsesmenData[$dbField])) {
                    $path = WRITEPATH . $hasilAsesmenData[$dbField];
                    if (file_exists($path)) {
                        $filePaths[$key] = $path;
                    }
                }
            }
        }

        // 8. Konfigurasi Dompdf untuk membuat PDF dari HTML
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 9. Simpan hasil PDF sementara
        $generatedPdfPath = WRITEPATH . 'uploads/generated.pdf';
        file_put_contents($generatedPdfPath, $dompdf->output());

        // 10. Marker untuk posisi penyisipan file tambahan
        $markersToFind = [
            'INSERT_PDF_RPS'                => ['search' => 'INSERT_PDF_RPS', 'heading' => '6. DOKUMEN RENCANA PEMBELAJARAN SEMESTER'],
            'INSERT_PDF_TUGAS'              => ['search' => 'INSERT_PDF_TUGAS', 'heading' => '7.1 TUGAS'],
            'INSERT_PDF_UTS'                => ['search' => 'INSERT_PDF_UTS', 'heading' => '7.2 UJIAN TENGAH SEMESTER'],
            'INSERT_PDF_UAS'                => ['search' => 'INSERT_PDF_UAS', 'heading' => '7.3 UJIAN AKHIR SEMESTER'],
            'INSERT_PDF_HASIL_TUGAS'        => ['search' => 'INSERT_PDF_HASIL_TUGAS', 'heading' => '1. HASIL TUGAS'],
            'INSERT_PDF_HASIL_UTS'          => ['search' => 'INSERT_PDF_HASIL_UTS', 'heading' => '2. HASIL UJIAN TENGAH SEMESTER'],
            'INSERT_PDF_HASIL_UAS'          => ['search' => 'INSERT_PDF_HASIL_UAS', 'heading' => '3. HASIL UJIAN AKHIR SEMESTER'],
            'INSERT_PDF_NILAI_MATA_KULIAH'  => ['search' => 'INSERT_PDF_NILAI_MATA_KULIAH', 'heading' => '4. NILAI MATA KULIAH'],
            'INSERT_PDF_NILAI_CPMK'         => ['search' => 'INSERT_PDF_NILAI_CPMK', 'heading' => '5. NILAI CPMK'],
        ];

        if (isset($filePaths['KONTRAK'])) {
            $markersToFind['INSERT_PDF_KONTRAK'] = ['search' => 'INSERT_PDF_KONTRAK', 'heading' => 'KONTRAK PERKULIAHAN'];
        }
        if (isset($filePaths['REALISASI'])) {
            $markersToFind['INSERT_PDF_REALISASI'] = ['search' => 'INSERT_PDF_REALISASI', 'heading' => 'REALISASI PERKULIAHAN'];
        }
        if (isset($filePaths['KEHADIRAN'])) {
            $markersToFind['INSERT_PDF_KEHADIRAN'] = ['search' => 'INSERT_PDF_KEHADIRAN', 'heading' => 'KEHADIRAN PERKULIAHAN'];
        }

        // 11. Cari semua posisi marker hanya sekali
        $insertPositions = $this->findAllInsertPositions($generatedPdfPath, $markersToFind);

        // 12. Gabungkan semua file PDF
        $mergedPdfPath = WRITEPATH . 'uploads/merged.pdf';
        $repairedPdfPath = WRITEPATH . 'uploads/repaired_temp.pdf';

        $this->mergePdfsWithMultipleInsertPoints(
            $generatedPdfPath,
            $filePaths,
            $insertPositions,
            $mergedPdfPath,
            $repairedPdfPath
        );

        // 13. Bersihkan file sementara setelah diunduh
        register_shutdown_function(function () use ($generatedPdfPath, $mergedPdfPath, $repairedPdfPath) {
            foreach ([$generatedPdfPath, $mergedPdfPath, $repairedPdfPath] as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        });

        // 14. Kembalikan hasil PDF ke browser
        return $this->response->download($mergedPdfPath, null);
    }

    /**
     * Fungsi baru yang efisien untuk mem-parsing PDF SATU KALI
     * dan menemukan semua posisi marker.
     */
    private function findAllInsertPositions($pdfPath, $markers)
    {
        // Atur batas memori di sini, HANYA SEKALI untuk satu proses parsing berat
        ini_set('memory_limit', '512M');

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $positions = [];
        $foundMarkers = []; // Untuk melacak marker yang sudah ditemukan

        // Inisialisasi semua posisi marker ke halaman terakhir (sebagai fallback)
        foreach ($markers as $markerKey => $texts) {
            $positions[$markerKey] = $pageCount;
        }

        // Loop setiap halaman PDF (satu kali)
        for ($i = 0; $i < $pageCount; $i++) {
            $text = $pages[$i]->getText();
            $currentPage = $i + 1; // Halaman dimulai dari 1

            // Periksa semua marker di halaman ini
            foreach ($markers as $markerKey => $texts) {
                // Jika marker ini sudah ditemukan di halaman sebelumnya, lewati
                if (in_array($markerKey, $foundMarkers)) {
                    continue;
                }

                $searchText = $texts['search'];
                $headingText = $texts['heading'];

                // Cek apakah searchText atau headingText ada di halaman ini
                if (strpos($text, $searchText) !== false || strpos($text, $headingText) !== false) {
                    $positions[$markerKey] = $currentPage;
                    $foundMarkers[] = $markerKey; // Tandai bahwa marker ini sudah ditemukan
                }
            }
        }

        return $positions;
    }


    /**
     * FUNGSI BARU UNTUK MEMPERBAIKI PDF
     * Menggunakan Ghostscript untuk mengonversi PDF ke format yang kompatibel (PDF 1.4)
     * Ini memerlukan Ghostscript (gs) terinstal di server.
     */
    private function repairPdf($sourcePath, $tempPath)
    {
        // Perintah Ghostscript untuk mengonversi PDF ke format yang kompatibel (PDF 1.4)
        // Ini sering memperbaiki masalah kompresi atau versi PDF
        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($tempPath) . " " . escapeshellarg($sourcePath);

        @shell_exec($command);

        // Cek apakah file hasil perbaikan berhasil dibuat dan punya isi
        if (file_exists($tempPath) && filesize($tempPath) > 0) {
            return $tempPath; // Gunakan file yang sudah diperbaiki
        }

        // Kembalikan file asli jika perbaikan gagal (misal: gs tidak terinstal)
        return $sourcePath;
    }

    private function mergePdfsWithMultipleInsertPoints($mainPdf, $filePaths, $insertPositions, $outputPath, $repairedPdfPath)
    {
        // Atur batas memori juga untuk proses merge Fpdi, karena ini juga berat
        ini_set('memory_limit', '1024M');

        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Hitung jumlah halaman dari PDF utama SEKALI saja di awal
        $mainPageCount = $pdf->setSourceFile($mainPdf);

        // Urutkan posisi insert dari awal ke akhir dokumen
        asort($insertPositions);

        // Variabel untuk melacak halaman terakhir yang sudah ditambahkan
        $lastAddedPage = 0;

        // Iterasi melalui semua posisi insert yang diurutkan
        foreach ($insertPositions as $markerType => $position) {
            // Tambahkan halaman dari PDF utama hingga posisi insert saat ini
            $startPage = $lastAddedPage + 1;
            $endPage = min($position, $mainPageCount);

            if ($startPage <= $endPage) {
                // *** PERBAIKAN: Set source ke main PDF sebelum import halaman main PDF ***
                $pdf->setSourceFile($mainPdf);

                for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {
                    $template = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($template);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation);
                    $pdf->useTemplate($template, 0, 0, null, null, true);
                }
            }

            // Update halaman terakhir yang sudah ditambahkan
            $lastAddedPage = $endPage;

            // Tentukan jenis file berdasarkan marker
            $fileType = str_replace('INSERT_PDF_', '', $markerType);

            // Sisipkan file PDF yang sesuai jika tersedia
            if (isset($filePaths[$fileType]) && $filePaths[$fileType] !== null && file_exists($filePaths[$fileType])) {

                // Coba "perbaiki" PDF terlebih dahulu ke format yang kompatibel
                $sourceFileToUse = $this->repairPdf($filePaths[$fileType], $repairedPdfPath);

                try {
                    // *** PERBAIKAN KRUSIAL: Set source file ke file yang BERBEDA (bukan main PDF) ***
                    $insertFilePageCount = $pdf->setSourceFile($sourceFileToUse);

                    // Import semua halaman dari file yang akan disisipkan
                    for ($pageNo = 1; $pageNo <= $insertFilePageCount; $pageNo++) {
                        $template = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($template);
                        $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                        $pdf->AddPage($orientation);
                        $pdf->useTemplate($template, 0, 0, null, null, true);
                    }
                } catch (\Exception $e) {
                    // Tangani error jika PDF yang akan disisipkan rusak
                    // Buat halaman placeholder yang berisi pesan error
                    $pdf->AddPage('P');
                    $pdf->SetFont('Helvetica', 'B', 12);
                    $pdf->SetTextColor(255, 0, 0); // Merah
                    $pdf->MultiCell(0, 10, "Error: Gagal menyisipkan file PDF untuk {$fileType}.\nFile: {$filePaths[$fileType]}\n\nPesan error: " . $e->getMessage(), 0, 'C');
                    $pdf->SetTextColor(0, 0, 0); // Reset ke hitam
                }

                // Hapus file perbaikan sementara jika ada
                if (file_exists($repairedPdfPath)) {
                    @unlink($repairedPdfPath);
                }
            } else {
                // Debug: File tidak ditemukan
                if (!isset($filePaths[$fileType])) {
                    error_log("File type {$fileType} tidak ada dalam array filePaths");
                } elseif ($filePaths[$fileType] === null) {
                    error_log("File type {$fileType} bernilai NULL");
                } elseif (!file_exists($filePaths[$fileType])) {
                    error_log("File {$filePaths[$fileType]} tidak ditemukan di server");
                }
            }
        }

        // Tambahkan sisa halaman dari PDF utama jika masih ada
        if ($lastAddedPage < $mainPageCount) {
            $pdf->setSourceFile($mainPdf);
            for ($pageNo = $lastAddedPage + 1; $pageNo <= $mainPageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation);
                $pdf->useTemplate($template, 0, 0, null, null, true);
            }
        }

        $pdf->Output($outputPath, 'F');
    }

    private function generateChartImage($cpmkData)
    {
        // Siapkan data untuk chart
        $labels = [];
        $values = [];
        foreach ($cpmkData as $cpmk) {
            $labels[] = 'CPMK ' . $cpmk['no_cpmk'];
            $values[] = $cpmk['avg_cpmk'];
        }

        // Hitung nilai max secara dinamis untuk menentukan batas atas
        $maxValue = !empty($values) ? max($values) : 0;

        // Set yMin selalu 0 dan yMax dengan buffer
        $yMin = 0.0; // Selalu mulai dari 0
        $yMax = min(4, ceil(($maxValue + 0.5) * 2) / 2); // Bulatkan ke atas ke kelipatan 0.5 dengan buffer

        // Pastikan yMax minimal 2.5 untuk memberikan ruang yang cukup
        if ($yMax < 2.5) {
            $yMax = 2.5;
        }

        // Konfigurasi chart menggunakan QuickCharts
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Nilai CPMK',
                        'data' => $values,
                        'backgroundColor' => 'rgba(15, 76, 146, 0.1)',
                        'borderColor' => 'rgba(15, 76, 146, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'min' => 0.0,
                        'max' => $yMax,
                        'ticks' => [
                            'stepSize' => 0.5,
                            'callback' => 'function(value) { return value.toFixed(2); }'
                        ],
                        'grid' => [
                            'drawOnChartArea' => true,
                            'color' => 'rgba(0, 0, 0, 0.1)'
                        ]
                    ]
                ],
                'plugins' => [
                    'legend' => [
                        'position' => 'top'
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => 'function(context) { return "Nilai: " + context.raw.toFixed(2); }'
                        ]
                    ],
                    'annotation' => [
                        'annotations' => [
                            'line1' => [
                                'type' => 'line',
                                'yMin' => 2.00,
                                'yMax' => 2.00,
                                'borderColor' => 'red',
                                'borderWidth' => 2,
                                'borderDash' => [5, 5],
                                'scaleID' => 'y',
                                'label' => [
                                    'display' => true,
                                    'content' => 'Nilai minimum (2.00)',
                                    'position' => 'end',
                                    'backgroundColor' => 'rgba(255, 0, 0, 0.7)',
                                    'font' => [
                                        'size' => 12,
                                        'weight' => 'bold'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Encode konfigurasi chart ke URL
        $chartConfigEncoded = urlencode(json_encode($chartConfig));

        // Generate URL gambar chart dari QuickChart
        $chartUrl = "https://quickchart.io/chart?c={$chartConfigEncoded}&w=500&h=300";

        // Ambil gambar chart sebagai base64
        $imageData = file_get_contents($chartUrl);
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    public function show($filename)
    {
        // Path ke file dalam writable/uploads/
        $path = WRITEPATH . 'uploads/assessment/' . $filename;

        // Periksa apakah file ada
        if (!file_exists($path)) {
            return $this->response->setStatusCode(404, 'File Not Found');
        }

        // Set header untuk menampilkan file PDF
        return $this->response
            ->setContentType('application/pdf')
            ->setBody(file_get_contents($path));
    }
}
