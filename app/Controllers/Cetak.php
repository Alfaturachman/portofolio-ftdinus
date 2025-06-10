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

        // Pastikan path file sesuai dengan direktori upload
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

        // 5. Ambil file hasil asesmen dari database
        $hasilAsesmenModel = new HasilAsesmenModel();
        $hasilAsesmenData = $hasilAsesmenModel->where('id_porto', $idPorto)->first();

        // 6. Kelompokkan file berdasarkan kategori
        $filePaths = [
            'RPS' => $existingRpsPdf,
            'TUGAS' => null,
            'UTS' => null,
            'UAS' => null,
            'HASIL_TUGAS' => null,
            'HASIL_UTS' => null,
            'HASIL_UAS' => null,
            'NILAI_MATA_KULIAH' => null,
            'NILAI_CPMK' => null
        ];

        // Tambahkan file rancangan asesment
        foreach ($additionalFiles as $file) {
            $filePath = WRITEPATH . '' . $file['file_pdf'];
            if (file_exists($filePath)) {
                // Tentukan kategori file (misalnya dari field kategori pada database)
                if (isset($file['kategori'])) {
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
                            // File tambahan lainnya bisa disimpan untuk digunakan nanti
                            break;
                    }
                }
            }
        }

        // Tambahkan file pelaksanaan perkuliahan jika ada
        if ($pelaksanaanData) {
            if (!empty($pelaksanaanData['file_kontrak'])) {
                $kontrakPath = WRITEPATH . '' . $pelaksanaanData['file_kontrak'];
                if (file_exists($kontrakPath)) {
                    $filePaths['KONTRAK'] = $kontrakPath;
                }
            }

            if (!empty($pelaksanaanData['file_realisasi'])) {
                $realisasiPath = WRITEPATH . '' . $pelaksanaanData['file_realisasi'];
                if (file_exists($realisasiPath)) {
                    $filePaths['REALISASI'] = $realisasiPath;
                }
            }

            if (!empty($pelaksanaanData['file_kehadiran'])) {
                $kehadiranPath = WRITEPATH . '' . $pelaksanaanData['file_kehadiran'];
                if (file_exists($kehadiranPath)) {
                    $filePaths['KEHADIRAN'] = $kehadiranPath;
                }
            }
        }

        // Tambahkan file hasil asesmen jika ada
        if ($hasilAsesmenData) {
            if (!empty($hasilAsesmenData['file_tugas'])) {
                $tugasPath = WRITEPATH . '' . $hasilAsesmenData['file_tugas'];
                if (file_exists($tugasPath)) {
                    $filePaths['HASIL_TUGAS'] = $tugasPath;
                }
            }

            if (!empty($hasilAsesmenData['file_uts'])) {
                $utsPath = WRITEPATH . '' . $hasilAsesmenData['file_uts'];
                if (file_exists($utsPath)) {
                    $filePaths['HASIL_UTS'] = $utsPath;
                }
            }

            if (!empty($hasilAsesmenData['file_uas'])) {
                $uasPath = WRITEPATH . '' . $hasilAsesmenData['file_uas'];
                if (file_exists($uasPath)) {
                    $filePaths['HASIL_UAS'] = $uasPath;
                }
            }

            if (!empty($hasilAsesmenData['file_nilai_mk'])) {
                $nilaiMkPath = WRITEPATH . '' . $hasilAsesmenData['file_nilai_mk'];
                if (file_exists($nilaiMkPath)) {
                    $filePaths['NILAI_MATA_KULIAH'] = $nilaiMkPath;
                }
            }

            if (!empty($hasilAsesmenData['file_nilai_cpmk'])) {
                $nilaiCpmkPath = WRITEPATH . '' . $hasilAsesmenData['file_nilai_cpmk'];
                if (file_exists($nilaiCpmkPath)) {
                    $filePaths['NILAI_CPMK'] = $nilaiCpmkPath;
                }
            }
        }

        // 7. Konfigurasi Dompdf untuk membuat PDF dari HTML
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $dompdf = new Dompdf($options);

        // Simpan HTML dengan marker untuk posisi penyisipan
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 8. Simpan hasil PDF sementara
        $generatedPdfPath = WRITEPATH . 'uploads/generated.pdf';
        file_put_contents($generatedPdfPath, $dompdf->output());

        // 9. Cari posisi marker untuk penyisipan semua dokumen
        $insertPositions = [
            'INSERT_PDF_RPS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_RPS', '6. DOKUMEN RENCANA PEMBELAJARAN SEMESTER'),
            'INSERT_PDF_TUGAS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_TUGAS', '7.1 TUGAS'),
            'INSERT_PDF_UTS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_UTS', '7.2 UJIAN TENGAH SEMESTER'),
            'INSERT_PDF_UAS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_UAS', '7.3 UJIAN AKHIR SEMESTER'),
            'INSERT_PDF_HASIL_TUGAS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_HASIL_TUGAS', '1. HASIL TUGAS'),
            'INSERT_PDF_HASIL_UTS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_HASIL_UTS', '2. HASIL UJIAN TENGAH SEMESTER'),
            'INSERT_PDF_HASIL_UAS' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_HASIL_UAS', '3. HASIL UJIAN AKHIR SEMESTER'),
            'INSERT_PDF_NILAI_MATA_KULIAH' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_NILAI_MATA_KULIAH', '4. NILAI MATA KULIAH'),
            'INSERT_PDF_NILAI_CPMK' => $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_NILAI_CPMK', '5. NILAI CPMK')
        ];

        // Tambahkan marker untuk file pelaksanaan perkuliahan jika diperlukan
        if (isset($filePaths['KONTRAK'])) {
            $insertPositions['INSERT_PDF_KONTRAK'] = $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_KONTRAK', 'KONTRAK PERKULIAHAN');
        }

        if (isset($filePaths['REALISASI'])) {
            $insertPositions['INSERT_PDF_REALISASI'] = $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_REALISASI', 'REALISASI PERKULIAHAN');
        }

        if (isset($filePaths['KEHADIRAN'])) {
            $insertPositions['INSERT_PDF_KEHADIRAN'] = $this->findInsertPosition($generatedPdfPath, 'INSERT_PDF_KEHADIRAN', 'KEHADIRAN PERKULIAHAN');
        }

        // 10. Gabungkan semua file PDF dengan posisi yang sesuai
        $mergedPdfPath = WRITEPATH . 'uploads/merged.pdf';
        $this->mergePdfsWithMultipleInsertPoints($generatedPdfPath, $filePaths, $insertPositions, $mergedPdfPath);

        // 11. Beri respons ke browser untuk mengunduh hasil merge
        return $this->response->download($mergedPdfPath, null);
    }

    private function findInsertPosition($pdfPath, $searchText, $headingText)
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        for ($i = 0; $i < count($pages); $i++) {
            $text = $pages[$i]->getText();
            if (strpos($text, $searchText) !== false) {
                return $i + 1;
            }
        }

        // Fallback: cari teks judul bagian
        for ($i = 0; $i < count($pages); $i++) {
            $text = $pages[$i]->getText();
            if (strpos($text, $headingText) !== false) {
                return $i + 1;
            }
        }

        // Jika tidak ditemukan, gunakan nilai default
        return count($pages); // Default ke akhir dokumen jika tidak ditemukan
    }

    private function mergePdfsWithMultipleInsertPoints($mainPdf, $filePaths, $insertPositions, $outputPath)
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Hitung jumlah halaman dari PDF utama
        $mainPageCount = $pdf->setSourceFile($mainPdf);

        // Urutkan posisi insert dari awal ke akhir dokumen
        asort($insertPositions);

        // Variabel untuk melacak halaman terakhir yang sudah ditambahkan
        $lastAddedPage = 0;

        // Iterasi melalui semua posisi insert yang diurutkan
        foreach ($insertPositions as $markerType => $position) {
            // Tambahkan halaman dari PDF utama hingga posisi insert saat ini
            $pdf->setSourceFile($mainPdf);
            for ($pageNo = $lastAddedPage + 1; $pageNo <= min($position, $mainPageCount); $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation);
                $pdf->useTemplate($template, 0, 0, null, null, true);
            }

            // Update halaman terakhir yang sudah ditambahkan
            $lastAddedPage = $position;

            // Tentukan jenis file berdasarkan marker
            $fileType = str_replace('INSERT_PDF_', '', $markerType);

            // Sisipkan file PDF yang sesuai jika tersedia
            if (isset($filePaths[$fileType]) && $filePaths[$fileType] !== null && file_exists($filePaths[$fileType])) {
                $insertFilePageCount = $pdf->setSourceFile($filePaths[$fileType]);
                for ($pageNo = 1; $pageNo <= $insertFilePageCount; $pageNo++) {
                    $template = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($template);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation);
                    $pdf->useTemplate($template, 0, 0, null, null, true);
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
        $maxValue = max($values);

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
