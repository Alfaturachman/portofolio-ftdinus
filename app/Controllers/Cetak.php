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

class Cetak extends BaseController
{
    public function index($idPorto)
    {
        // Load model Portofolio
        $portofolioModel = new PortofolioModel();
        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);

        // Load model CPL
        $cplPiModel = new CplModel();
        $cplPiData = $cplPiModel->getCplPiByPortoId($idPorto);

        // Load model CPMK
        $cpmkModel = new CpmkModel();
        $cpmkData = $cpmkModel->getCpmkByPortoId($idPorto);
        
        // Load model Sub-CPMK
        $subCpmkModel = new SubCpmkModel();
        $subCpmkData = $subCpmkModel->getSubCpmkByPortoId($idPorto);

        return view('backend/pdf/test-cetak', [
            'portofolioData' => $portofolioData,
            'cplPiData' => $cplPiData,
            'cpmkData' => $cpmkData,
            'subCpmkData' => $subCpmkData
        ]);
    }

    public function generatePdf($idPorto)
    {
        $portofolioModel = new PortofolioModel();
        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);

        $cplPiModel = new CplModel();
        $cplPiData = $cplPiModel->getCplPiByPortoId($idPorto);

        // 1. Data dari view
        $html = view('backend/pdf/test-cetak', [
            'portofolioData' => $portofolioData,
            'cplPiData' => $cplPiData
        ]);

        // 2. Konfigurasi Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 3. Simpan sementara hasil PDF yang dibuat
        $output = $dompdf->output();
        $pdfPath = WRITEPATH . 'uploads/generated.pdf';
        file_put_contents($pdfPath, $output);

        // 4. Gabungkan dengan PDF yang sudah ada
        $existingPdfPath = WRITEPATH . 'uploads/RPS Sistem Robotika.pdf';
        $mergedPdfPath = WRITEPATH . 'uploads/merged.pdf';

        $this->mergePdfs($pdfPath, $existingPdfPath, $mergedPdfPath);

        // 5. Beri respons ke browser untuk mengunduh file hasil merge
        return $this->response->download($mergedPdfPath, null);
    }

    private function mergePdfs($pdf1, $pdf2, $outputPath)
    {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Tambahkan semua halaman dari PDF pertama
        $pageCount = $pdf->setSourceFile($pdf1);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);

            // Tentukan orientasi berdasarkan dimensi halaman
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            // Tambahkan halaman dengan orientasi yang sesuai
            $pdf->AddPage($orientation);
            $pdf->useTemplate($template, 0, 0, null, null, true);
        }

        // Tambahkan semua halaman dari PDF kedua
        $pageCount = $pdf->setSourceFile($pdf2);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);

            // Tentukan orientasi berdasarkan dimensi halaman
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            // Tambahkan halaman dengan orientasi yang sesuai
            $pdf->AddPage($orientation);
            $pdf->useTemplate($template, 0, 0, null, null, true);
        }

        $pdf->Output($outputPath, 'F');
    }
}
