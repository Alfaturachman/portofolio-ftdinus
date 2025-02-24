<?php

namespace App\Controllers;

use GuzzleHttp\Client;

class Portofolio extends BaseController
{
    public function index(): string
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('backend/form-portofolio/index');
    }

    public function upload_rps()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/form-portofolio/upload-rps', [
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function view_uploaded_pdf($filename)
    {
        $path = WRITEPATH . 'uploads/temp/' . $filename;

        if (file_exists($path)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody(file_get_contents($path));
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('PDF not found');
        }
    }

    public function saveUploadRps()
    {
        $validation = \Config\Services::validation();

        // Validasi file upload
        $validation->setRules([
            'rps_file' => [
                'label' => 'RPS File',
                'rules' => 'uploaded[rps_file]|ext_in[rps_file,pdf]|max_size[rps_file,10240]',
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $validation->getError('rps_file'),
            ]);
        }

        // Handle upload file
        $file = $this->request->getFile('rps_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = time() . '_' . $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $newName);

            // Simpan nama file ke session
            session()->set('uploaded_rps', $newName);

            // Return URL untuk iframe
            return $this->response->setJSON([
                'success' => true,
                'pdfUrl' => base_url('view-pdf/' . $newName),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupload file.',
        ]);
    }

    public function info_matkul()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Data API dalam format JSON
        $apiResponse = '{
        "status": 1,
        "message": "success",
        "data": {
            "infoMatkul": {
                "fakultas": "Fakultas Teknik",
                "progdi": "Teknik Informatika",
                "nama_mk": "Pemrograman Web",
                "kode_mk": "IF123",
                "kelompok_mk": "Wajib",
                "sks_teori": 3,
                "sks_praktik": 1
            },
            "mataKuliah": [
                {
                    "nama_mk": "ALGORITMA DAN PEMROGRAMAN",
                    "kode_mk": "E113205",
                    "kelompok_mk": "Wajib",
                    "fakultas": "Fakultas Teknik",
                    "progdi": "Teknik Elektro",
                    "sks_teori": 3,
                    "sks_praktik": 0
                },
                {
                    "nama_mk": "ANALISIS KELAYAKAN PROYEK",
                    "kode_mk": "E1144907",
                    "kelompok_mk": "pilihan",
                    "fakultas": "Fakultas Teknik",
                    "progdi": "Teknik Elektro",
                    "sks_teori": 3,
                    "sks_praktik": 0
                }
            ]
        }
    }';

        // Decode JSON ke array PHP
        $data = json_decode($apiResponse, true);

        // Pastikan message API adalah 'success'
        if ($data['message'] === 'success') {
            $mataKuliah = $data['data']['mataKuliah'];
            $infoMatkul = $data['data']['infoMatkul'];
        } else {
            // Jika message tidak 'success', gunakan data statis sebagai fallback
            $mataKuliah = [
                [
                    'fakultas' => 'Teknik',
                    'progdi' => 'Teknik Elektro',
                    'nama_mk' => 'ALJABAR LINIER',
                    'kode_mk' => 'E1142001',
                    'kelompok_mk' => '01',
                    'sks_teori' => 3,
                    'sks_praktik' => 0
                ],
                [
                    'fakultas' => 'Teknik',
                    'progdi' => 'Teknik Elektro',
                    'nama_mk' => 'ANALISIS KELAYAKAN PROYEK',
                    'kode_mk' => 'E1144907',
                    'kelompok_mk' => '02',
                    'sks_teori' => 2,
                    'sks_praktik' => 1
                ],
            ];
            $infoMatkul = [];
        }

        // Data tambahan dari session (jika ada)
        $infoMatkul = array_merge($infoMatkul, session()->get('info_matkul') ?? []);

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        // Kirim data ke view
        return view('backend/form-portofolio/info-matkul', [
            'mataKuliah' => $mataKuliah,
            'infoMatkul' => $infoMatkul,
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function saveInfoMatkul()
    {
        // Inisialisasi validasi
        $validation = \Config\Services::validation();

        // Aturan validasi
        $validation->setRules([
            'fakultas' => 'required',
            'progdi' => 'required',
            'nama_mk' => 'required',
            'kode_mk' => 'required',
            'kelompok_mk' => 'required',
            'sks_teori' => 'required|numeric',
            'sks_praktik' => 'required|numeric',
            'mk_prasyarat' => 'permit_empty',
            'topik_mk' => 'permit_empty',
        ]);

        // Jalankan validasi
        if (!$validation->withRequest($this->request)->run()) {
            // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $data = [
            'fakultas' => $this->request->getPost('fakultas'),
            'progdi' => $this->request->getPost('progdi'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'kelompok_mk' => $this->request->getPost('kelompok_mk'),
            'sks_teori' => $this->request->getPost('sks_teori'),
            'sks_praktik' => $this->request->getPost('sks_praktik'),
            'mk_prasyarat' => $this->request->getPost('mk_prasyarat'),
            'topik_mk' => $this->request->getPost('topik_mk'),
        ];

        // Simpan data ke session
        session()->set('info_matkul', $data);

        // Log informasi penyimpanan data
        log_message('info', 'Data Mata Kuliah disimpan ke session: ' . json_encode($data));

        // Redirect ke halaman berikutnya dengan pesan sukses
        return redirect()->to('portofolio-form/cpl-pi')->with('message', 'Data mata kuliah berhasil disimpan.');
    }

    public function view_pdf(): object
    {
        // Path file PDF
        $filePath = WRITEPATH . 'uploads/RPS Sistem Robotika.pdf';

        // Cek apakah file ada
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="RPS Sistem Robotika.pdf"')
                ->setBody($fileContent);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('PDF not found.');
        }
    }

    public function topik_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Data tambahan dari session (jika ada)
        $topikPerkuliahan = session()->get('topik_perkuliahan') ?? [];

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        // Kirim data ke view
        return view('backend/form-portofolio/topik-perkuliahan', [
            'topikPerkuliahan' => $topikPerkuliahan,
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function saveTopikPerkuliahan()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'topik_mk' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data topik perkuliahan dari form
        $topik = $this->request->getPost('topik_mk');

        // Simpan data topik perkuliahan ke session
        $data = array_merge(['topik_mk' => $topik]);

        // Set data ke session
        session()->set('topik_perkuliahan', $data);

        log_message('info', 'Topik Perkuliahan disimpan ke session: ' . json_encode($data));

        return redirect()->to('portofolio-form/cpl-pi');
    }

    public function cpl_pi()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get kode_matkul from session
        $infoMatkul = session()->get('info_matkul');
        $kodeMatkul = $infoMatkul['kode_mk'] ?? '';

        // Create model instance
        $db = \Config\Database::connect();

        // Query to get CPL and PI data
        $query = $db->table('cpl_pi')
            ->where('kode_matkul', $kodeMatkul)
            ->orderBy('no_cpl', 'ASC')
            ->orderBy('no_pi', 'ASC')
            ->get();

        // Process the results into a structured array
        $cplPiData = [];
        foreach ($query->getResultArray() as $row) {
            $cplNo = $row['no_cpl'];
            if (!isset($cplPiData[$cplNo])) {
                $cplPiData[$cplNo] = [
                    'cpl_indo' => $row['cpl_indo'],
                    'pi_list' => []
                ];
            }
            if (!empty($row['isi_pi'])) {
                $cplPiData[$cplNo]['pi_list'][] = $row['isi_pi'];
            }
        }

        // Store CPL-PI data in session
        session()->set('cpl_pi_data', $cplPiData);

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/form-portofolio/cpl-pi', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData
        ]);
    }

    // Add new method to get CPL-PI data from session
    public function getCplPiFromSession()
    {
        $sessionData = session()->get('cpl_pi_data');

        if ($sessionData === null) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($sessionData);
    }

    public function cpmk_subcpmk()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get CPL-PI data from session
        $cplPiData = session()->get('cpl_pi_data') ?? [];

        // Get PDF URL from session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/form-portofolio/cpmk-subcpmk', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData  // Pass CPL data to the view
        ]);
    }


    public function saveCPMKToSession()
    {
        $json = $this->request->getJSON();
        $cpmkData = $json->cpmk ?? null;
        $globalSubCpmkCounter = $json->globalSubCpmkCounter ?? 1;

        if ($cpmkData) {
            // Convert to array if it's an object
            $cpmkArray = json_decode(json_encode($cpmkData), true);

            // Store in session
            session()->set('cpmk_data', [
                'cpmk' => $cpmkArray,
                'globalSubCpmkCounter' => $globalSubCpmkCounter
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data CPMK berhasil disimpan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Data CPMK tidak valid'
        ]);
    }

    public function getCPMKFromSession()
    {
        $sessionData = session()->get('cpmk_data');

        if ($sessionData === null) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($sessionData);
    }

    public function pemetaan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/form-portofolio/pemetaan', [
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function saveMappingToSession()
    {
        try {
            $json = $this->request->getJSON();
            $mappingData = $json->mapping ?? null;

            if (!$mappingData || empty((array)$mappingData)) {
                throw new \Exception('Data pemetaan kosong atau tidak valid.');
            }

            session()->set('mapping_data', $mappingData);
            session()->set('current_progress', 'pemetaan');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pemetaan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rancangan_asesmen()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if mapping data exists in session
        if (!session()->get('mapping_data')) {
            return redirect()->to('/portofolio-form/pemetaan')
                ->with('error', 'Silakan lengkapi pemetaan terlebih dahulu.');
        }

        return view('backend/form-portofolio/rancangan-asesmen');
    }

    public function pelaksanaan_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('backend/form-portofolio/pelaksanaan-perkuliahan');
    }

    public function hasil_asesmen()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('backend/form-portofolio/hasil-asesmen');
    }

    public function evaluasi_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('backend/form-portofolio/evaluasi-perkuliahan');
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }
}
