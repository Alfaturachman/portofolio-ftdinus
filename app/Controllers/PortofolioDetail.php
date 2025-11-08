<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PortofolioModel;

class PortofolioDetail extends BaseController
{
    public function index()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Load helper if it exists
        helper('tanggal');

        $portofolioModel = new PortofolioModel();
        $currentUserNPP = session()->get('UserSession.username');
        $data['portofolioList'] = $portofolioModel->getAllPortofolio($currentUserNPP);

        return view('backend/portofolio-detail/index', $data);
    }

    // API endpoint untuk mengambil data portofolio (digunakan oleh JavaScript)
    public function getPortofolio()
    {
        if (!session()->get('UserSession.logged_in')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $portofolioModel = new PortofolioModel();
        $currentUserNPP = session()->get('UserSession.username');
        $portofolioList = $portofolioModel->getAllPortofolio($currentUserNPP);

        return $this->response->setJSON($portofolioList);
    }
}
