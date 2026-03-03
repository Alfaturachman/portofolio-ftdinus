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
use App\Models\EvaluasiKesimpulan;

class Cetak extends BaseController
{
    // =========================================================
    // PREVIEW / INDEX
    // =========================================================
    public function index($idPorto)
    {
        $viewData = $this->prepareViewData($idPorto);
        if (!$viewData) {
            return redirect()->back()->with('error', 'Data portofolio tidak ditemukan.');
        }
        return $this->generatePdf($idPorto);
    }

    // =========================================================
    // GENERATE PDF
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
        $options->set('chroot', FCPATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $generatedPdfPath = WRITEPATH . 'uploads/temp_generated_' . $idPorto . '.pdf';
        file_put_contents($generatedPdfPath, $dompdf->output());

        $filePaths     = $this->collectAttachmentFiles($idPorto);
        $mergedPdfPath = WRITEPATH . 'uploads/merged_' . $idPorto . '.pdf';

        $this->mergePdfsWithMarkers($generatedPdfPath, $filePaths, $mergedPdfPath);

        if (file_exists($generatedPdfPath)) {
            @unlink($generatedPdfPath);
        }

        // Hapus file temp asesmen jika ada
        foreach (glob(WRITEPATH . 'uploads/temp_asesmen_*_' . $idPorto . '.pdf') as $tmp) {
            @unlink($tmp);
        }

        $portoData = $viewData['portofolioData'];
        $filename  = 'Portofolio_' . str_replace(' ', '_', $portoData['nama_matkul']) . '_' . date('Ymd') . '.pdf';
        $content   = file_get_contents($mergedPdfPath);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    // =========================================================
    // MERGE PDF — STRATEGI PRE-IMPORT
    //
    // BUG LAMA (versi sebelumnya):
    //   Loop: for pageNo = 1..N
    //     importPage($pageNo)  ← dari mainPdf
    //     [kalau ada lampiran] setSourceFile(lampiran) → import → setSourceFile(mainPdf)
    //     importPage($pageNo+1) ← MASALAH: setelah setSourceFile(mainPdf),
    //                              FPDI reset internal pointer, tapi $pageNo
    //                              sudah bertambah → halaman yang diimport
    //                              meleset 1 posisi per lampiran yang disisipkan.
    //
    // SOLUSI (versi ini):
    //   Fase 1 — PRE-IMPORT: Import SEMUA halaman mainPdf ke dalam
    //            array $mainTemplates[] sebelum menyentuh file lampiran.
    //            Setelah fase ini, FPDI sudah punya semua template
    //            dari mainPdf dalam memori internalnya.
    //   Fase 2 — RENDER: Loop $mainTemplates, render ke output PDF.
    //            Karena mainPdf sudah selesai, setSourceFile(lampiran)
    //            tidak akan mengganggu urutan halaman mainPdf.
    // =========================================================
    private function mergePdfsWithMarkers(string $mainPdf, array $filePaths, string $outputPath): void
    {
        $markerMap = [
            'INSERT_PDF_RPS'               => 'RPS',
            'INSERT_PDF_TUGAS'             => 'TUGAS',
            'INSERT_PDF_UTS'               => 'UTS',
            'INSERT_PDF_UAS'               => 'UAS',
            'INSERT_PDF_KONTRAK'           => 'KONTRAK',
            'INSERT_PDF_REALISASI'         => 'REALISASI',
            'INSERT_PDF_KEHADIRAN'         => 'KEHADIRAN',
            'INSERT_PDF_HASIL_TUGAS'       => 'HASIL_TUGAS',
            'INSERT_PDF_HASIL_UTS'         => 'HASIL_UTS',
            'INSERT_PDF_HASIL_UAS'         => 'HASIL_UAS',
            'INSERT_PDF_NILAI_MATA_KULIAH' => 'NILAI_MATA_KULIAH',
            'INSERT_PDF_NILAI_CPMK'        => 'NILAI_CPMK',
        ];

        // --- LANGKAH 1: Deteksi marker per halaman ---
        $pageMarkerMap = $this->detectMarkersPerPage($mainPdf, $markerMap);
        log_message('info', 'pageMarkerMap: ' . json_encode($pageMarkerMap));

        // --- LANGKAH 2: Buat FPDI instance ---
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // --- LANGKAH 3: PRE-IMPORT semua halaman mainPdf ---
        $pageCount     = $pdf->setSourceFile($mainPdf);
        $mainTemplates = [];

        for ($p = 1; $p <= $pageCount; $p++) {
            $tplId           = $pdf->importPage($p);
            $size            = $pdf->getTemplateSize($tplId);
            $mainTemplates[] = [
                'tplId'  => $tplId,
                'width'  => $size['width'],
                'height' => $size['height'],
                'pageNo' => $p,
            ];
        }

        // --- LANGKAH 4: RENDER — mainPdf sudah selesai di-import ---
        foreach ($mainTemplates as $tpl) {
            $pageNo      = $tpl['pageNo'];
            $orientation = ($tpl['width'] > $tpl['height']) ? 'L' : 'P';

            $pdf->AddPage($orientation, [$tpl['width'], $tpl['height']]);
            $pdf->useTemplate($tpl['tplId'], 0, 0, $tpl['width'], $tpl['height'], true);

            // Sisipkan lampiran jika halaman ini punya marker
            if (!empty($pageMarkerMap[$pageNo])) {
                foreach ($pageMarkerMap[$pageNo] as $fileKey) {
                    if (!empty($filePaths[$fileKey]) && file_exists($filePaths[$fileKey])) {
                        try {
                            $attachCount = $pdf->setSourceFile($filePaths[$fileKey]);

                            for ($ap = 1; $ap <= $attachCount; $ap++) {
                                $attTplId  = $pdf->importPage($ap);
                                $attSize   = $pdf->getTemplateSize($attTplId);
                                $attOrient = ($attSize['width'] > $attSize['height']) ? 'L' : 'P';
                                $pdf->AddPage($attOrient, [$attSize['width'], $attSize['height']]);
                                $pdf->useTemplate($attTplId, 0, 0, $attSize['width'], $attSize['height'], true);
                            }

                            log_message('info', "Sisipkan [{$fileKey}] setelah halaman {$pageNo}");
                        } catch (\Exception $e) {
                            log_message('error', "Gagal sisipkan [{$fileKey}]: " . $e->getMessage());
                        }
                    } else {
                        log_message('warning', "File [{$fileKey}] tidak ada, dilewati.");
                    }
                }
            }
        }

        $pdf->Output($outputPath, 'F');
    }

    // =========================================================
    // DETEKSI MARKER PER HALAMAN DARI RAW PDF
    // =========================================================
    private function detectMarkersPerPage(string $pdfPath, array $markerMap): array
    {
        $result = [];

        try {
            $raw = file_get_contents($pdfPath);
            if (!$raw) {
                throw new \RuntimeException('File tidak dapat dibaca.');
            }

            $allStreams     = $this->extractAllStreams($raw);
            $pageContentMap = $this->buildPageContentMap($raw, $allStreams);

            if (!empty($pageContentMap)) {
                foreach ($pageContentMap as $pageNo => $pageText) {
                    foreach ($markerMap as $marker => $fileKey) {
                        if (strpos($pageText, $marker) !== false) {
                            $result[$pageNo][] = $fileKey;
                            log_message('info', "Marker [{$marker}] ditemukan di halaman {$pageNo}");
                        }
                    }
                }
            }

            if (empty($result)) {
                log_message('warning', 'Tidak ada marker terdeteksi via parsing, gunakan fallback.');
                $result = $this->fallbackDetect($allStreams, $markerMap);
            }
        } catch (\Exception $e) {
            log_message('error', 'detectMarkersPerPage error: ' . $e->getMessage());
        }

        return $result;
    }

    // =========================================================
    // EKSTRAK SEMUA STREAM (objNum => decoded text)
    // =========================================================
    private function extractAllStreams(string $raw): array
    {
        $streams = [];

        preg_match_all(
            '/(\d+)\s+\d+\s+obj\b(.*?)stream\r?\n(.*?)\r?\nendstream/s',
            $raw,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $objNum   = (int) $m[1];
            $dictPart = $m[2];
            $rawBytes = $m[3];
            $decoded  = $rawBytes;

            if (
                strpos($dictPart, 'FlateDecode') !== false ||
                strpos($dictPart, '/Fl ') !== false
            ) {
                $try = @gzuncompress($rawBytes);
                if ($try === false) {
                    $try = @gzdecode($rawBytes);
                }
                if ($try !== false) {
                    $decoded = $try;
                }
            }

            $streams[$objNum] = $decoded;
        }

        return $streams;
    }

    // =========================================================
    // BANGUN MAP {pageNo => teks konten halaman}
    // =========================================================
    // =========================================================
    // BANGUN MAP {pageNo => teks konten halaman}
    // Mendukung XObject/TPL template (TCPDF/FPDI pattern):
    //   allStreams[pageObjNum] = Contents stream halaman (off-by-one regex trick)
    //   TPL XObject streams juga di-include untuk menemukan marker
    // =========================================================
    private function buildPageContentMap(string $raw, array $allStreams): array
    {
        $pageContentMap = [];

        // Kumpulkan semua obj blocks beserta offset byte
        preg_match_all(
            '/(\d+)\s+\d+\s+obj\b(.*?)endobj/s',
            $raw,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        // --- Kumpulkan page objects (offset => [objNum, block]) ---
        $pageObjects = [];
        foreach ($matches as $m) {
            $block  = $m[2][0];
            $offset = (int) $m[0][1];
            $objNum = (int) $m[1][0];

            if (
                preg_match('/\/Type\s*\/Page\b/', $block) &&
                !preg_match('/\/Type\s*\/Pages\b/', $block)
            ) {
                $pageObjects[$offset] = ['objNum' => $objNum, 'block' => $block];
            }
        }

        if (empty($pageObjects)) {
            return [];
        }

        ksort($pageObjects); // urut byte offset = urut halaman fisik

        // --- Bangun XObject map: /TPLx => obj ref ---
        $xobjMap = []; // tplIndex => objRef
        foreach ($matches as $m) {
            $block = $m[2][0];
            if (preg_match('/\/XObject\s*<<(.*?)>>/s', $block, $xm)) {
                preg_match_all('/\/TPL(\d+)\s+(\d+)\s+\d+\s+R/', $xm[1], $tplMatches);
                foreach ($tplMatches[1] as $k => $tplIdx) {
                    $xobjMap[(int) $tplIdx] = (int) $tplMatches[2][$k];
                }
            }
        }

        // --- Untuk setiap halaman, kumpulkan teks ---
        $pageIndex = 1;
        foreach ($pageObjects as $info) {
            $pageObjNum = $info['objNum'];
            $block      = $info['block'];

            // Kunci utama: allStreams[pageObjNum] = Contents stream halaman ini
            // (regex off-by-one: \d+ obj ... stream dari obj berikutnya ter-capture
            //  di key pageObjNum karena page dict tidak punya stream sendiri)
            $pageText = isset($allStreams[$pageObjNum]) ? $allStreams[$pageObjNum] : '';

            // Tambahkan teks dari XObject TPL yang dipanggil di stream ini
            if (!empty($xobjMap)) {
                preg_match_all('/\/TPL(\d+)\s+Do/', $pageText, $tplCalls);
                foreach ($tplCalls[1] as $tplIdx) {
                    $tplIdx = (int) $tplIdx;
                    if (isset($xobjMap[$tplIdx], $allStreams[$xobjMap[$tplIdx]])) {
                        $pageText .= $allStreams[$xobjMap[$tplIdx]];
                    }
                }
            }

            // Fallback: juga cek /Contents ref secara langsung (untuk PDF non-TCPDF)
            if (preg_match('/\/Contents\s+(\d+)\s+\d+\s+R\b/', $block, $cm)) {
                $cObjNum = (int) $cm[1];
                if (isset($allStreams[$cObjNum]) && $allStreams[$cObjNum] !== $pageText) {
                    $pageText .= $allStreams[$cObjNum];
                    if (!empty($xobjMap)) {
                        preg_match_all('/\/TPL(\d+)\s+Do/', $allStreams[$cObjNum], $tplCalls2);
                        foreach ($tplCalls2[1] as $tplIdx) {
                            $tplIdx = (int) $tplIdx;
                            if (isset($xobjMap[$tplIdx], $allStreams[$xobjMap[$tplIdx]])) {
                                $pageText .= $allStreams[$xobjMap[$tplIdx]];
                            }
                        }
                    }
                }
            }

            $pageContentMap[$pageIndex] = $pageText;
            $pageIndex++;
        }

        return $pageContentMap;
    }

    // =========================================================
    // FALLBACK DETEKSI
    // =========================================================
    private function fallbackDetect(array $allStreams, array $markerMap): array
    {
        $result = [];
        ksort($allStreams);

        $pageNo = 0;
        foreach ($allStreams as $text) {
            if (!preg_match('/[A-Za-z]{4,}/', $text)) {
                continue;
            }
            $pageNo++;
            foreach ($markerMap as $marker => $fileKey) {
                if (strpos($text, $marker) !== false) {
                    $result[$pageNo][] = $fileKey;
                    log_message('info', "[FALLBACK] [{$marker}] di ~halaman {$pageNo}");
                }
            }
        }

        return $result;
    }

    // =========================================================
    // PREPARE VIEW DATA (perbaikan)
    // =========================================================
    private function prepareViewData($idPorto)
    {
        $portofolioModel         = new PortofolioModel();
        $cplModel                = new CPL();
        $cpmkModel               = new CPMK();
        $subCpmkModel            = new SubCPMK();
        $mappingModel            = new MappingCPLCPMKSCPMK();
        $asesmenModel            = new RancanganAsesmen();
        $soalModel               = new RancanganSoal();
        $evaluasiModel           = new Evaluasi();
        $evaluasiKesimpulanModel = new EvaluasiKesimpulan();

        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);
        if (!$portofolioData) {
            return null;
        }

        $cplPiData          = $cplModel->getCplPiByPortoId($idPorto);
        $cplData            = $cplModel->getCplByPortoId($idPorto);
        $cpmkData           = $cpmkModel->getCpmkByPorto($idPorto);
        $subCpmkData        = $subCpmkModel->getSubCpmkByPorto($idPorto);

        // PERBAIKAN: Pastikan assessmentData tidak kosong dan memiliki struktur yang benar
        $assessmentData     = $asesmenModel->getAssessmentData($idPorto);
        if (empty($assessmentData)) {
            // Jika tidak ada data asesmen, buat default berdasarkan data soal yang ada
            $assessmentSoalData = $soalModel->getAssessmentSoalData($idPorto);
            $availableKategori = array_unique(array_column($assessmentSoalData, 'kategori_soal'));

            $assessmentData = [];
            if (!empty($cpmkData)) {
                $defaultRow = [
                    'id_cpmk' => $cpmkData[0]['id'],
                    'no_cpmk' => $cpmkData[0]['no_cpmk'],
                    'tugas' => in_array('tugas', $availableKategori) ? 1 : 0,
                    'uts' => in_array('uts', $availableKategori) ? 1 : 0,
                    'uas' => in_array('uas', $availableKategori) ? 1 : 0
                ];
                $assessmentData[] = $defaultRow;
            }
        }

        $assessmentSoalData = $soalModel->getAssessmentSoalData($idPorto);
        $mappingData        = $mappingModel->getMapping($idPorto);
        $evaluasiData       = $evaluasiModel->getEvaluasiByPorto($idPorto);
        $evaluasi           = $evaluasiKesimpulanModel->getEvaluasiKesimpulan($idPorto);

        $chartImageBase64 = $this->generateChartImage($cpmkData);
        $subCpmkNumbers   = array_column($subCpmkData, 'no_scpmk');
        $logoBase64       = $this->getLogoBase64();

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
            'evaluasi'           => $evaluasi,
        ];
    }

    // =========================================================
    // KUMPULKAN FILE LAMPIRAN
    // =========================================================
    private function collectAttachmentFiles($idPorto): array
    {
        $filePaths = [];

        // RPS
        $rpsModel = new RPS();
        $rpsData  = $rpsModel->where('id_portofolio', $idPorto)->first();
        if ($rpsData && !empty($rpsData['file_rps'])) {
            $path = WRITEPATH . 'uploads/rps/' . $rpsData['file_rps'];
            if (file_exists($path)) {
                $filePaths['RPS'] = $path;
            }
        }

        // Pelaksanaan
        $pelaksanaanModel = new Pelaksanaan();
        $pelaksanaan      = $pelaksanaanModel->where('id_portofolio', $idPorto)->first();
        if ($pelaksanaan) {
            foreach (
                [
                    'file_kontrak_kuliah'     => 'KONTRAK',
                    'file_realisasi_mengajar' => 'REALISASI',
                    'file_kehadiran'          => 'KEHADIRAN',
                ] as $col => $key
            ) {
                if (!empty($pelaksanaan[$col])) {
                    $path = WRITEPATH . 'uploads/pelaksanaan/' . $pelaksanaan[$col];
                    if (file_exists($path)) {
                        $filePaths[$key] = $path;
                    }
                }
            }
        }

        // Rancangan Asesmen — gabungkan file_soal + file_rubrik per jenis
        // agar tidak saling menimpa (bug lama: hanya satu yang muncul)
        $rancanganAsesmenModel = new RancanganAsesmen();
        $rancanganData         = $rancanganAsesmenModel->where('id_portofolio', $idPorto)->findAll();

        $asesmenFiles = []; // [ 'TUGAS' => [path1, path2], 'UTS' => [...], ... ]
        foreach ($rancanganData as $item) {
            $jenis = strtoupper($item['jenis_asesmen']);
            foreach (['file_soal', 'file_rubrik'] as $col) {
                if (!empty($item[$col])) {
                    $path = WRITEPATH . 'uploads/asesmen/' . $item[$col];
                    if (file_exists($path)) {
                        $asesmenFiles[$jenis][] = $path;
                    }
                }
            }
        }

        foreach ($asesmenFiles as $jenis => $paths) {
            $paths = array_unique($paths);
            if (count($paths) === 1) {
                $filePaths[$jenis] = $paths[0];
            } elseif (count($paths) > 1) {
                $tmpOut = WRITEPATH . 'uploads/temp_asesmen_' . $jenis . '_' . $idPorto . '.pdf';
                $merged = $this->mergeMultiplePdfs($paths, $tmpOut);
                if ($merged) {
                    $filePaths[$jenis] = $merged;
                }
            }
        }

        // Hasil Asesmen
        $hasilAsesmenModel = new HasilAsesmen();
        $hasilData         = $hasilAsesmenModel->where('id_portofolio', $idPorto)->findAll();
        foreach ($hasilData as $item) {
            $jenis = strtoupper($item['jenis_asesmen']);
            if (!empty($item['file_jawaban'])) {
                $path = WRITEPATH . 'uploads/hasil_asesmen/' . $item['file_jawaban'];
                if (file_exists($path)) {
                    $filePaths['HASIL_' . $jenis] = $path;
                }
            }
        }

        // Nilai CPMK
        $nilaiCpmkModel = new NilaiCPMK();
        $nilaiCpmk      = $nilaiCpmkModel->where('id_portofolio', $idPorto)->first();
        if ($nilaiCpmk && !empty($nilaiCpmk['file_nilai_cpmk'])) {
            $path = WRITEPATH . 'uploads/nilai/' . $nilaiCpmk['file_nilai_cpmk'];
            if (file_exists($path)) {
                $filePaths['NILAI_CPMK'] = $path;
            }
        }

        // Nilai Mata Kuliah
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
    // GABUNGKAN BEBERAPA PDF MENJADI SATU
    // =========================================================
    private function mergeMultiplePdfs(array $paths, string $outputPath): ?string
    {
        try {
            $pdf = new Fpdi();
            $pdf->SetAutoPageBreak(false);
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);

            foreach ($paths as $path) {
                $pageCount = $pdf->setSourceFile($path);
                for ($p = 1; $p <= $pageCount; $p++) {
                    $tplId = $pdf->importPage($p);
                    $size  = $pdf->getTemplateSize($tplId);
                    $ori   = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($ori, [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height'], true);
                }
            }

            $pdf->Output($outputPath, 'F');
            return $outputPath;
        } catch (\Exception $e) {
            log_message('error', 'mergeMultiplePdfs: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================
    // GENERATE CHART IMAGE
    // =========================================================
    private function generateChartImage(array $cpmkData): string
    {
        $labels = [];
        $values = [];

        foreach ($cpmkData as $cpmk) {
            $labels[] = 'CPMK ' . $cpmk['no_cpmk'];
            $values[] = (float) ($cpmk['avg_cpmk'] ?? 0);
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

        $chartUrl = 'https://quickchart.io/chart?c='
            . urlencode(json_encode($chartConfig))
            . '&width=500&height=300&format=png';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $chartUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $imageData = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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
        $path = FCPATH . 'assets/images/logo_udinus.png';
        if (!file_exists($path)) {
            $path = WRITEPATH . 'uploads/logo_udinus.png';
            if (!file_exists($path)) {
                return '';
            }
        }

        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default       => 'image/' . $ext,
        };

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    // =========================================================
    // SHOW — tampilkan PDF inline
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
