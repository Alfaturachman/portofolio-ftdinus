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

class Cetak extends BaseController
{
    public function index($idPorto)
    {
        // Load semua model
        $portofolioModel = new PortofolioModel();
        $cplModel = new CplModel();
        $cpmkModel = new CpmkModel();
        $subCpmkModel = new SubCpmkModel();
        $mappingModel = new MappingCpmkScpmkModel();


        $portofolioData = $portofolioModel->getPortofolioCetakDetails($idPorto);
        $cplPiData = $cplModel->getCplPiByPortoId($idPorto);
        $cpmkData = $cpmkModel->getCpmkByPortoId($idPorto);
        $cplData = $cplModel->getCplByPortoId($idPorto);
        $cpmkData = $cpmkModel->getCpmkByPortoId($idPorto);
        $subCpmkData = $subCpmkModel->getSubCpmkByPortoId($idPorto);
        $mappingData = $mappingModel->getAllMappings();

        // Load model Sub-CPMK
        $subCpmkModel = new SubCpmkModel();
        $subCpmkData = $subCpmkModel->getSubCpmkByPortoId($idPorto);

        $viewData = [
            'portofolioData' => $portofolioData,
            'cplPiData' => $cplPiData,
            'cplData' => $cplData,
            'cpmkData' => $cpmkData,
            'subCpmkData' => $subCpmkData,
            'mappingData' => $mappingData
        ];

        // Persiapkan data mapping dalam format yang mudah diakses di view
        $formattedMapping = [];
        foreach ($mappingData as $mapping) {
            $formattedMapping[$mapping['id_cpmk']][$mapping['id_scpmk']] = $mapping['nilai'];
        }
        $viewData['mappingData'] = $formattedMapping;

        // Dapatkan semua nomor Sub-CPMK untuk header tabel
        $subCpmkNumbers = [];
        foreach ($subCpmkData as $subCpmk) {
            $subCpmkNumbers[] = $subCpmk['no_scpmk'];
        }
        $viewData['subCpmkNumbers'] = $subCpmkNumbers;

        return view('backend/pdf/test-cetak', $viewData);
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
