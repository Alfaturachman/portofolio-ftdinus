<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CplModel;
use App\Models\CpmkModel;
use App\Models\SubCpmkModel;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\PortofolioModel;
use App\Controllers\BaseController;
use App\Models\MappingCpmkScpmkModel;
use App\Models\RancanganAsesmenModel;

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

        // Ambil data dari model
        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);
        $cplPiData = $cplModel->getCplPiByPortoId($idPorto);
        $cplData = $cplModel->getCplByPortoId($idPorto);
        $cpmkData = $cpmkModel->getCpmkByPorto($idPorto);
        $subCpmkData = $subCpmkModel->getSubCpmkByPorto($idPorto);
        $assessmentData = $asesmenModel->getAssessmentData($idPorto);

        // Ambil data mapping
        $mappingData = $mappingModel->getMapping($idPorto);

        // Dapatkan semua nomor Sub-CPMK untuk header tabel
        $subCpmkNumbers = array_column($subCpmkData, 'no_scpmk');

        return [
            'portofolioData' => $portofolioData,
            'cplPiData' => $cplPiData,
            'cplData' => $cplData,
            'cpmkData' => $cpmkData,
            'subCpmkData' => $subCpmkData,
            'mappingData' => $mappingData,
            'subCpmkNumbers' => $subCpmkNumbers,
            'assessmentData' => $assessmentData
        ];
    }

    public function generatePdf($idPorto)
    {
        $viewData = $this->prepareViewData($idPorto);

        // 1. Data dari view
        $html = view('backend/pdf/cetak-portofolio', $viewData);

        // 2. Konfigurasi Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 3. Simpan sementara hasil PDF yang dibuat
        $output = $dompdf->output();
        $htmlPdfPath = WRITEPATH . 'uploads/generated.pdf';
        file_put_contents($htmlPdfPath, $output);

        // 4. Gabungkan dengan PDF yang sudah ada
        $existingPdfPath = WRITEPATH . 'uploads/RPS Sistem Robotika.pdf';
        $mergedPdfPath = WRITEPATH . 'uploads/merged.pdf';

        $this->mergePdfs($htmlPdfPath, $existingPdfPath, $mergedPdfPath);

        // 5. Beri respons ke browser untuk mengunduh file hasil merge
        return $this->response->download($mergedPdfPath, null);
    }

    private function mergePdfs($htmlPdf, $existingPdf, $outputPath)
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Hitung jumlah halaman dari masing-masing PDF
        $htmlPageCount = $pdf->setSourceFile($htmlPdf);
        $existingPageCount = 0; // Kita akan menghitung ini nanti

        // Tentukan di halaman mana kita ingin menyisipkan PDF yang sudah ada
        $insertAfterPage = 4;

        // Tambahkan halaman HTML PDF sampai posisi insert
        for ($pageNo = 1; $pageNo <= min($insertAfterPage, $htmlPageCount); $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation);
            $pdf->useTemplate($template, 0, 0, null, null, true);
        }

        // Sisipkan PDF yang sudah ada
        $existingPageCount = $pdf->setSourceFile($existingPdf);
        for ($pageNo = 1; $pageNo <= $existingPageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation);
            $pdf->useTemplate($template, 0, 0, null, null, true);
        }

        // Tambahkan sisa halaman dari HTML PDF jika ada
        if ($insertAfterPage < $htmlPageCount) {
            $pdf->setSourceFile($htmlPdf);
            for ($pageNo = $insertAfterPage + 1; $pageNo <= $htmlPageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation);
                $pdf->useTemplate($template, 0, 0, null, null, true);
            }
        }

        $pdf->Output($outputPath, 'F');
    }
}
