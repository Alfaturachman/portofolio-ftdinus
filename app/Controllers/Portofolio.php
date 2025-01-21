<?php

namespace App\Controllers;

class Portofolio extends BaseController
{
    public function index(): string
    {
        return view('backend/form-portofolio/index');
    }

    public function upload_rps(): string
    {
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

    public function info_matkul(): string
    {
        // Data statis mata kuliah
        $mataKuliah = [
            [
                'fakultas' => 'Teknik',
                'progdi' => 'Teknik Elektro',
                'nama_mk' => 'Sistem Robotika',
                'kode_mk' => 'E1144902',
                'kelompok_mk' => '01',
                'sks_teori' => 1,
                'sks_praktik' => 2
            ],
            [
                'fakultas' => 'Teknik',
                'progdi' => 'Teknik Industri',
                'nama_mk' => 'Matematika Quantum',
                'kode_mk' => 'E1282576',
                'kelompok_mk' => '02',
                'sks_teori' => 2,
                'sks_praktik' => 1
            ],
        ];

        // Data tambahan dari session (jika ada)
        $infoMatkul = session()->get('info_matkul') ?? [];

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        // Kirim data ke view
        return view('backend/form-portofolio/info-matkul', [
            'mataKuliah' => $mataKuliah,
            'infoMatkul' => $infoMatkul,
            'pdfUrl' => $pdfUrl,
        ]);
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

    public function saveInfoMatkul()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'fakultas' => 'required',
            'progdi' => 'required',
            'nama_mk' => 'required',
            'kode_mk' => 'required',
            'kelompok_mk' => 'required',
            'sks_teori' => 'required|numeric',
            'sks_praktik' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'fakultas' => $this->request->getPost('fakultas'),
            'progdi' => $this->request->getPost('progdi'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'kelompok_mk' => $this->request->getPost('kelompok_mk'),
            'sks_teori' => $this->request->getPost('sks_teori'),
            'sks_praktik' => $this->request->getPost('sks_praktik'),
            'mk_prasyarat' => $this->request->getPost('mk_prasyarat'),
        ];

        session()->set('info_matkul', $data);

        log_message('info', 'Data Mata Kuliah disimpan ke session: ' . json_encode($data));

        return redirect()->to('portofolio-form/topik-perkuliahan');
    }

    public function topik_perkuliahan(): string
    {
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

    public function cpl_pi(): string
    {
        return view('backend/form-portofolio/cpl-pi');
    }

    public function cpmk_subcpmk(): string
    {
        return view('backend/form-portofolio/cpmk-subcpmk');
    }

    public function pemetaan(): string
    {
        return view('backend/form-portofolio/pemetaan');
    }

    public function rancangan_asesmen(): string
    {
        return view('backend/form-portofolio/rancangan-asesmen');
    }

    public function nilai_cpmk(): string
    {
        return view('backend/form-portofolio/nilai-cpmk');
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }
}
