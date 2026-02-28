<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CPL;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\SubCPMK;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\Portofolio;
use App\Controllers\BaseController;
use App\Models\HasilAsesmen;
use App\Models\NilaiCpmk;
use App\Models\NilaiMatkul;
use App\Models\MappingCPLCPMKSCPMK;
use App\Models\RancanganAsesmen;
use App\Models\RancanganSoal;
use App\Models\Pelaksanaan;
use App\Models\Evaluasi;

class Cetak extends BaseController
{
    // =========================================================
    // PREVIEW - Tampilkan HTML di browser
    // =========================================================
    public function index($idPorto)
    {
        $viewData = $this->prepareViewData($idPorto);
        if (!$viewData) {
            return redirect()->back()->with('error', 'Data portofolio tidak ditemukan.');
        }
        return view('admin/cetak/cetak-portofolio', $viewData);
    }

    // =========================================================
    // GENERATE PDF - Download PDF hasil gabungan
    // =========================================================
    public function generatePdf($idPorto)
    {
        // 1. Siapkan data view
        $viewData = $this->prepareViewData($idPorto);
        if (!$viewData) {
            return $this->response->setStatusCode(404, 'Data portofolio tidak ditemukan.');
        }

        // 2. Render HTML dari view
        $html = view('admin/cetak/cetak-portofolio', $viewData);

        // 3. Generate PDF utama menggunakan Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 4. Simpan PDF sementara
        $generatedPdfPath = WRITEPATH . 'uploads/temp_generated_' . $idPorto . '.pdf';
        file_put_contents($generatedPdfPath, $dompdf->output());

        // 5. Kumpulkan file-file lampiran
        $filePaths = $this->collectAttachmentFiles($idPorto);

        // 6. Cari posisi insert marker di PDF
        $insertPositions = $this->findAllInsertPositions($generatedPdfPath);

        // 7. Gabungkan semua PDF
        $mergedPdfPath = WRITEPATH . 'uploads/merged_' . $idPorto . '.pdf';
        $this->mergePdfs($generatedPdfPath, $filePaths, $insertPositions, $mergedPdfPath);

        // 8. Hapus file temp
        if (file_exists($generatedPdfPath)) {
            @unlink($generatedPdfPath);
        }

        // 9. Return sebagai download
        $portoData = $viewData['portofolioData'];
        $filename  = 'Portofolio_' . str_replace(' ', '_', $portoData['nama_matkul']) . '_' . date('Ymd') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(file_get_contents($mergedPdfPath));
    }

    // =========================================================
    // PREPARE VIEW DATA
    // =========================================================
    private function prepareViewData($idPorto)
    {
        $portofolioModel = new Portofolio();
        $cplModel        = new CPL();
        $cpmkModel       = new CPMK();
        $subCpmkModel    = new SubCPMK();
        $mappingModel    = new MappingCPLCPMKSCPMK();
        $asesmenModel    = new RancanganAsesmen();
        $soalModel       = new RancanganSoal();
        $evaluasiModel   = new Evaluasi();

        // Ambil data portofolio utama
        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);
        if (!$portofolioData) {
            return null;
        }

        $cplPiData        = $cplModel->getCplPiByPortoId($idPorto);
        $cplData          = $cplModel->getCplByPortoId($idPorto);
        $cpmkData         = $cpmkModel->getCpmkByPorto($idPorto);
        $subCpmkData      = $subCpmkModel->getSubCpmkByPorto($idPorto);
        $assessmentData   = $asesmenModel->getAssessmentData($idPorto);
        $assessmentSoalData = $soalModel->getAssessmentSoalData($idPorto);
        $mappingData      = $mappingModel->getMapping($idPorto);
        $evaluasiData     = $evaluasiModel->getEvaluasiByPorto($idPorto);

        $chartImageBase64 = $this->generateChartImage($cpmkData);
        $subCpmkNumbers   = array_column($subCpmkData, 'no_scpmk');

        // Konversi logo ke base64
        $logoBase64 = $this->getLogoBase64();

        return [
            'portofolioData'     => $portofolioData,
            'cplPiData'          => $cplPiData,
            'cplData'            => $cplData,
            'cpmkData'           => $cpmkData,
            'subCpmkData'        => $subCpmkData,
            'subCpmkNumbers'     => $subCpmkNumbers,
            'mappingData'        => $mappingData,
            'assessmentData'     => $assessmentData,
            'assessmentSoalData' => $assessmentSoalData,
            'evaluasiData'       => $evaluasiData,
            'chartImageBase64'   => $chartImageBase64,
            'logoBase64'         => $logoBase64,
        ];
    }

    // =========================================================
    // KUMPULKAN FILE LAMPIRAN
    // =========================================================
    private function collectAttachmentFiles($idPorto): array
    {
        $filePaths = [
            'RPS'              => null,
            'TUGAS'            => null,
            'UTS'              => null,
            'UAS'              => null,
            'KONTRAK'          => null,
            'REALISASI'        => null,
            'KEHADIRAN'        => null,
            'HASIL_TUGAS'      => null,
            'HASIL_UTS'        => null,
            'HASIL_UAS'        => null,
            'NILAI_MATA_KULIAH' => null,
            'NILAI_CPMK'       => null,
        ];

        // File RPS
        $rpsModel = new RPS();
        $rpsData  = $rpsModel->where('id_portofolio', $idPorto)->first();
        if ($rpsData && !empty($rpsData['file_rps'])) {
            $path = WRITEPATH . 'uploads/rps/' . $rpsData['file_rps'];
            if (file_exists($path)) {
                $filePaths['RPS'] = $path;
            }
        }

        // File Pelaksanaan (Kontrak, Realisasi, Kehadiran)
        $pelaksanaanModel = new Pelaksanaan();
        $pelaksanaan      = $pelaksanaanModel->where('id_portofolio', $idPorto)->first();
        if ($pelaksanaan) {
            $map = [
                'KONTRAK'  => 'file_kontrak_kuliah',
                'REALISASI' => 'file_realisasi_mengajar',
                'KEHADIRAN' => 'file_kehadiran',
            ];
            foreach ($map as $key => $field) {
                if (!empty($pelaksanaan[$field])) {
                    $path = WRITEPATH . 'uploads/pelaksanaan/' . $pelaksanaan[$field];
                    if (file_exists($path)) {
                        $filePaths[$key] = $path;
                    }
                }
            }
        }

        // File Rancangan Asesmen (Soal Tugas, UTS, UAS)
        $rancanganAsesmenModel = new RancanganAsesmen();
        $rancanganData         = $rancanganAsesmenModel->where('id_portofolio', $idPorto)->findAll();
        foreach ($rancanganData as $item) {
            $jenis = strtoupper($item['jenis_asesmen']); // tugas/uts/uas
            if (!empty($item['file_soal'])) {
                $path = WRITEPATH . 'uploads/asesmen/' . $item['file_soal'];
                if (file_exists($path)) {
                    $filePaths[$jenis] = $path;
                }
            }
        }

        // File Hasil Asesmen
        $hasilAsesmenModel = new HasilAsesmen();
        $hasilData         = $hasilAsesmenModel->where('id_portofolio', $idPorto)->findAll();
        foreach ($hasilData as $item) {
            $jenis = strtoupper($item['jenis_asesmen']); // tugas/uts/uas
            if (!empty($item['file_jawaban'])) {
                $path = WRITEPATH . 'uploads/hasil_asesmen/' . $item['file_jawaban'];
                if (file_exists($path)) {
                    $filePaths['HASIL_' . $jenis] = $path;
                }
            }
        }

        // File Nilai CPMK
        $nilaiCpmkModel = new NilaiCPMK();
        $nilaiCpmk      = $nilaiCpmkModel->where('id_portofolio', $idPorto)->first();
        if ($nilaiCpmk && !empty($nilaiCpmk['file_nilai_cpmk'])) {
            $path = WRITEPATH . 'uploads/nilai/' . $nilaiCpmk['file_nilai_cpmk'];
            if (file_exists($path)) {
                $filePaths['NILAI_CPMK'] = $path;
            }
        }

        // File Nilai Mata Kuliah
        $nilaiMatkulModel = new NilaiMatkul();
        $nilaiMatkul      = $nilaiMatkulModel->where('id_portofolio', $idPorto)->first();
        if ($nilaiMatkul && !empty($nilaiMatkul['file_nilai_matkul'])) {
            $path = WRITEPATH . 'uploads/nilai/' . $nilaiMatkul['file_nilai_matkul'];
            if (file_exists($path)) {
                $filePaths['NILAI_MATA_KULIAH'] = $path;
            }
        }

        return $filePaths;
    }

    // =========================================================
    // CARI POSISI INSERT SEMUA MARKER
    // =========================================================
    private function findAllInsertPositions(string $pdfPath): array
    {
        $markers = [
            'RPS'              => ['INSERT_PDF_RPS', '6. DOKUMEN RENCANA PEMBELAJARAN SEMESTER'],
            'TUGAS'            => ['INSERT_PDF_TUGAS', '7.1 TUGAS'],
            'UTS'              => ['INSERT_PDF_UTS', '7.2 UJIAN TENGAH SEMESTER'],
            'UAS'              => ['INSERT_PDF_UAS', '7.3 UJIAN AKHIR SEMESTER'],
            'KONTRAK'          => ['INSERT_PDF_KONTRAK', '1. KONTRAK KULIAH'],
            'REALISASI'        => ['INSERT_PDF_REALISASI', '2. REALISASI MENGAJAR'],
            'KEHADIRAN'        => ['INSERT_PDF_KEHADIRAN', '3. KEHADIRAN MAHASISWA'],
            'HASIL_TUGAS'      => ['INSERT_PDF_HASIL_TUGAS', '1. HASIL TUGAS'],
            'HASIL_UTS'        => ['INSERT_PDF_HASIL_UTS', '2. HASIL UJIAN TENGAH SEMESTER'],
            'HASIL_UAS'        => ['INSERT_PDF_HASIL_UAS', '3. HASIL UJIAN AKHIR SEMESTER'],
            'NILAI_MATA_KULIAH' => ['INSERT_PDF_NILAI_MATA_KULIAH', '4. NILAI MATA KULIAH'],
            'NILAI_CPMK'       => ['INSERT_PDF_NILAI_CPMK', '5. NILAI CPMK'],
        ];

        $parser   = new \Smalot\PdfParser\Parser();
        $pdf      = $parser->parseFile($pdfPath);
        $pages    = $pdf->getPages();
        $total    = count($pages);
        $positions = [];

        foreach ($markers as $key => [$marker, $heading]) {
            $found = false;
            for ($i = 0; $i < $total; $i++) {
                $text = $pages[$i]->getText();
                if (strpos($text, $marker) !== false || strpos($text, $heading) !== false) {
                    $positions[$key] = $i + 1; // 1-based
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $positions[$key] = $total; // default akhir dokumen
            }
        }

        return $positions;
    }

    // =========================================================
    // MERGE PDF UTAMA + LAMPIRAN
    // =========================================================
    private function mergePdfs(string $mainPdf, array $filePaths, array $insertPositions, string $outputPath): void
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $mainPageCount = $pdf->setSourceFile($mainPdf);

        // Urutkan posisi insert dari kecil ke besar
        asort($insertPositions);

        $lastPage = 0;

        foreach ($insertPositions as $key => $position) {
            // Tambahkan halaman PDF utama hingga posisi ini
            $pdf->setSourceFile($mainPdf);
            $upTo = min($position, $mainPageCount);
            for ($p = $lastPage + 1; $p <= $upTo; $p++) {
                $tpl  = $pdf->importPage($p);
                $size = $pdf->getTemplateSize($tpl);
                $orient = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orient, [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl, 0, 0, null, null, true);
            }
            $lastPage = $upTo;

            // Sisipkan file lampiran jika ada
            if (!empty($filePaths[$key]) && file_exists($filePaths[$key])) {
                try {
                    $insertCount = $pdf->setSourceFile($filePaths[$key]);
                    for ($p = 1; $p <= $insertCount; $p++) {
                        $tpl  = $pdf->importPage($p);
                        $size = $pdf->getTemplateSize($tpl);
                        $orient = ($size['width'] > $size['height']) ? 'L' : 'P';
                        $pdf->AddPage($orient, [$size['width'], $size['height']]);
                        $pdf->useTemplate($tpl, 0, 0, null, null, true);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Gagal menyisipkan file ' . $key . ': ' . $e->getMessage());
                }
            }
        }

        // Tambahkan sisa halaman PDF utama
        if ($lastPage < $mainPageCount) {
            $pdf->setSourceFile($mainPdf);
            for ($p = $lastPage + 1; $p <= $mainPageCount; $p++) {
                $tpl  = $pdf->importPage($p);
                $size = $pdf->getTemplateSize($tpl);
                $orient = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orient, [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl, 0, 0, null, null, true);
            }
        }

        $pdf->Output($outputPath, 'F');
    }

    // =========================================================
    // GENERATE CHART IMAGE (QuickChart API)
    // =========================================================
    private function generateChartImage(array $cpmkData): string
    {
        $labels = [];
        $values = [];

        foreach ($cpmkData as $cpmk) {
            $labels[] = 'CPMK ' . $cpmk['no_cpmk'];
            $values[] = (float)($cpmk['avg_cpmk'] ?? 0);
        }

        if (empty($labels)) {
            return '';
        }

        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels'   => $labels,
                'datasets' => [[
                    'label'           => 'Nilai CPMK',
                    'data'            => $values,
                    'backgroundColor' => 'rgba(15, 76, 146, 0.2)',
                    'borderColor'     => 'rgba(15, 76, 146, 1)',
                    'borderWidth'     => 1,
                ]],
            ],
            'options' => [
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'beginAtZero' => true,
                            'min'         => 0,
                            'max'         => 100,
                            'stepSize'    => 10,
                        ],
                    ]],
                ],
                'legend' => ['position' => 'top'],
            ],
        ];

        $chartUrl  = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig)) . '&w=500&h=300';
        $imageData = @file_get_contents($chartUrl);

        if ($imageData === false) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    // =========================================================
    // LOGO BASE64
    // =========================================================
    private function getLogoBase64(): string
    {
        $path = WRITEPATH . 'uploads/logo_udinus.png';
        if (!file_exists($path)) {
            return '';
        }
        $ext  = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $ext . ';base64,' . base64_encode($data);
    }

    // =========================================================
    // SHOW - Tampilkan file PDF di browser (inline)
    // =========================================================
    public function show(string $folder, string $filename)
    {
        $path = WRITEPATH . 'uploads/' . $folder . '/' . $filename;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404, 'File tidak ditemukan.');
        }

        return $this->response
            ->setContentType('application/pdf')
            ->setBody(file_get_contents($path));
    }
}
