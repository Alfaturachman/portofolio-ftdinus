<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CPL;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\SubCPMK;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\PortofolioModel;
use App\Controllers\BaseController;
use App\Models\HasilAsesmen;
use App\Models\NilaiCPMK;
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
        $viewData = $this->prepareViewData($idPorto);
        if (!$viewData) {
            return $this->response->setStatusCode(404, 'Data portofolio tidak ditemukan.');
        }

        $html = view('admin/cetak/cetak-portofolio', $viewData);

        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isJavascriptEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $generatedPdfPath = WRITEPATH . 'uploads/temp_generated_' . $idPorto . '.pdf';
        file_put_contents($generatedPdfPath, $dompdf->output());

        $filePaths = $this->collectAttachmentFiles($idPorto);

        $mergedPdfPath = WRITEPATH . 'uploads/merged_' . $idPorto . '.pdf';
        $this->mergePdfsWithMarkers($generatedPdfPath, $filePaths, $mergedPdfPath);

        if (file_exists($generatedPdfPath)) {
            @unlink($generatedPdfPath);
        }

        $portoData = $viewData['portofolioData'];
        $filename  = 'Portofolio_' . str_replace(' ', '_', $portoData['nama_matkul']) . '_' . date('Ymd') . '.pdf';

        $content = file_get_contents($mergedPdfPath);
        // Opsional: hapus merged setelah dibaca
        // @unlink($mergedPdfPath);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    // =========================================================
    // MERGE PDF DENGAN DETEKSI MARKER PER HALAMAN
    // =========================================================
    private function mergePdfsWithMarkers(string $mainPdf, array $filePaths, string $outputPath): void
    {
        // Map marker text → key file lampiran
        $markerMap = [
            'INSERT_PDF_RPS'              => 'RPS',
            'INSERT_PDF_TUGAS'            => 'TUGAS',
            'INSERT_PDF_UTS'              => 'UTS',
            'INSERT_PDF_UAS'              => 'UAS',
            'INSERT_PDF_KONTRAK'          => 'KONTRAK',
            'INSERT_PDF_REALISASI'        => 'REALISASI',
            'INSERT_PDF_KEHADIRAN'        => 'KEHADIRAN',
            'INSERT_PDF_HASIL_TUGAS'      => 'HASIL_TUGAS',
            'INSERT_PDF_HASIL_UTS'        => 'HASIL_UTS',
            'INSERT_PDF_HASIL_UAS'        => 'HASIL_UAS',
            'INSERT_PDF_NILAI_MATA_KULIAH' => 'NILAI_MATA_KULIAH',
            'INSERT_PDF_NILAI_CPMK'       => 'NILAI_CPMK',
        ];

        // 1. Baca teks per halaman dari PDF utama menggunakan Smalot parser
        //    atau cara sederhana: cari string marker di raw PDF bytes
        $pdfContent = file_get_contents($mainPdf);

        // 2. Buat FPDI instance untuk output
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pageCount = $pdf->setSourceFile($mainPdf);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size       = $pdf->getTemplateSize($templateId);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);

            // Cek apakah halaman ini mengandung marker
            // Ekstrak teks halaman ini dari raw PDF (pendekatan sederhana)
            $pageText = $this->extractPageText($mainPdf, $pageNo);

            foreach ($markerMap as $marker => $fileKey) {
                if (strpos($pageText, $marker) !== false) {
                    // Sisipkan lampiran setelah halaman ini
                    if (!empty($filePaths[$fileKey]) && file_exists($filePaths[$fileKey])) {
                        try {
                            $attachCount = $pdf->setSourceFile($filePaths[$fileKey]);
                            for ($p = 1; $p <= $attachCount; $p++) {
                                $attTplId   = $pdf->importPage($p);
                                $attSize    = $pdf->getTemplateSize($attTplId);
                                $attOrient  = ($attSize['width'] > $attSize['height']) ? 'L' : 'P';
                                $pdf->AddPage($attOrient, [$attSize['width'], $attSize['height']]);
                                $pdf->useTemplate($attTplId, 0, 0, $attSize['width'], $attSize['height'], true);
                            }
                            // Reset source kembali ke main PDF
                            $pdf->setSourceFile($mainPdf);
                            log_message('info', "Sisipkan $fileKey setelah halaman $pageNo");
                        } catch (\Exception $e) {
                            log_message('error', "Gagal sisipkan $fileKey: " . $e->getMessage());
                            // Reset source
                            $pdf->setSourceFile($mainPdf);
                        }
                    }
                }
            }
        }

        $pdf->Output($outputPath, 'F');
    }

    // =========================================================
    // EKSTRAK TEKS DARI HALAMAN TERTENTU (Raw PDF parsing)
    // =========================================================
    private function extractPageText(string $pdfPath, int $targetPage): string
    {
        // Pendekatan: baca stream teks dari raw PDF
        // Ini bukan parsing sempurna tapi cukup untuk marker teks ASCII
        $content = file_get_contents($pdfPath);

        // Cari semua stream dalam PDF
        $streams = [];
        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $content, $matches);

        if (empty($matches[1])) {
            return '';
        }

        // Estimasi: ambil stream yang relevan dengan halaman
        // (PDF page streams tidak selalu berurutan persis, ini pendekatan heuristik)
        $streamIndex = $targetPage - 1;
        if (isset($matches[1][$streamIndex])) {
            $raw = $matches[1][$streamIndex];
            // Dekode jika FlateDecode (terkompresi)
            $decoded = @gzuncompress($raw);
            if ($decoded !== false) {
                return $decoded;
            }
            return $raw;
        }

        return '';
    }

    // =========================================================
    // PREPARE VIEW DATA
    // =========================================================
    private function prepareViewData($idPorto)
    {
        $portofolioModel = new PortofolioModel();
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
        $filePaths = [];

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
            if (!empty($pelaksanaan['file_kontrak_kuliah'])) {
                $path = WRITEPATH . 'uploads/pelaksanaan/' . $pelaksanaan['file_kontrak_kuliah'];
                if (file_exists($path)) {
                    $filePaths['KONTRAK'] = $path;
                }
            }
            if (!empty($pelaksanaan['file_realisasi_mengajar'])) {
                $path = WRITEPATH . 'uploads/pelaksanaan/' . $pelaksanaan['file_realisasi_mengajar'];
                if (file_exists($path)) {
                    $filePaths['REALISASI'] = $path;
                }
            }
            if (!empty($pelaksanaan['file_kehadiran'])) {
                $path = WRITEPATH . 'uploads/pelaksanaan/' . $pelaksanaan['file_kehadiran'];
                if (file_exists($path)) {
                    $filePaths['KEHADIRAN'] = $path;
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

        $chartUrl  = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig)) . '&width=500&height=300&format=png';

        // Gunakan cURL untuk lebih reliable
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $chartUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($imageData === false || $httpCode !== 200) {
            log_message('error', 'Gagal mengambil chart dari QuickChart');
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    // =========================================================
    // LOGO BASE64
    // =========================================================
    private function getLogoBase64(): string
    {
        $path = FCPATH . 'assets/images/logo_udinus.png'; // Sesuaikan dengan path logo Anda

        if (!file_exists($path)) {
            // Coba path alternatif
            $path = WRITEPATH . 'uploads/logo_udinus.png';
            if (!file_exists($path)) {
                return '';
            }
        }

        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = ($ext == 'png') ? 'image/png' : (($ext == 'jpg' || $ext == 'jpeg') ? 'image/jpeg' : 'image/' . $ext);
        $data = file_get_contents($path);

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    // =========================================================
    // SHOW - Tampilkan file PDF di browser (inline)
    // =========================================================
    public function show($folder, $filename)
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
