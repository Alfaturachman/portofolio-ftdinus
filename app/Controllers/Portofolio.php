<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Models\PiModel;
use App\Models\CplModel;
use App\Models\RpsModel;
use App\Models\CpmkModel;
use App\Models\SubCpmkModel;
use App\Models\PortofolioModel;
use App\Models\HasilAsesmenModel;
use App\Models\MappingCpmkScpmkModel;
use App\Models\IdentitasMatkulModel;
use App\Models\RancanganAsesmenModel;
use App\Models\EvaluasiPerkuliahanModel;
use App\Models\RancanganAsesmenFileModel;
use App\Models\PelaksanaanPerkuliahanModel;

class Portofolio extends BaseController
{
    public function index()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get the current user's NPP from the session
        $currentUserNPP = session()->get('UserSession.username');

        $portofolioModel = new PortofolioModel();
        $data['portofolios'] = $portofolioModel->getAllPortofolio();

        return view('backend/portofolio-form/index', $data);
    }

    public function edit($id)
    {
        // Tambahkan logika untuk mengedit portofolio
    }

    public function cetak($id)
    {
        // Tambahkan logika untuk mencetak portofolio
    }

    public function upload_rps()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/upload-rps', [
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
        // Validasi file upload
        $validation = \Config\Services::validation();
        $validation->setRules([
            'rps_file' => [
                'label' => 'RPS File',
                'rules' => 'uploaded[rps_file]|ext_in[rps_file,pdf]|max_size[rps_file,10240]',
            ],
        ]);

        // Get the file directly from the request
        $file = $this->request->getFile('rps_file');

        // Check if file exists and is valid
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File upload failed. Please check the file and try again.',
                'error_details' => $file ? $file->getErrorString() : 'No file uploaded'
            ]);
        }

        // Run validation
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $validation->getError('rps_file'),
            ]);
        }

        // Handle upload file
        $newName = time() . '_' . $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/temp', $newName);

        // Simpan nama file ke session
        session()->set('uploaded_rps', $newName);

        // Return URL untuk iframe
        return $this->response->setJSON([
            'success' => true,
            'pdfUrl' => base_url('view-pdf/' . $newName),
            'redirect' => site_url('portofolio-form/info-matkul')
        ]);
    }

    public function info_matkul()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get data from database from info_matkul table
        $db = \Config\Database::connect();
        $mataKuliahData = $db->table('info_matkul')
            ->select('matakuliah as nama_mk, kode_matkul as kode_mk, kelp_matkul as kelompok_mk, 
                    fakultas, prodi as progdi, teori as sks_teori, praktek as sks_praktik')
            ->groupBy(['kode_matkul', 'matakuliah', 'kelp_matkul', 'fakultas', 'prodi', 'teori', 'praktek'])
            ->get()
            ->getResultArray();

        // Data tambahan dari session (jika ada)
        $infoMatkul = session()->get('info_matkul') ?? [];

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        // Kirim data ke view
        return view('backend/portofolio-form/info-matkul', [
            'mataKuliah' => $mataKuliahData,
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

        return view('backend/portofolio-form/cpl-pi', [
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

        return view('backend/portofolio-form/cpmk-subcpmk', [
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

        return view('backend/portofolio-form/pemetaan', [
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

        return view('backend/portofolio-form/rancangan-asesmen');
    }

    public function saveAssessmentToSession()
    {
        try {
            $json = $this->request->getJSON();
            $assessmentData = $json->assessment ?? null;

            if (!$assessmentData) {
                throw new \Exception('Data asesmen kosong atau tidak valid.');
            }

            // Convert the data to an array
            $assessmentArray = json_decode(json_encode($assessmentData), true);

            session()->set('assessment_data', $assessmentArray);
            session()->set('current_progress', 'assessment');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data asesmen berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveAssessmentWithFiles()
    {
        try {
            // Get assessment data from form
            $assessmentData = json_decode($this->request->getPost('assessment_data'), true);

            if (!$assessmentData) {
                throw new \Exception('Data asesmen kosong atau tidak valid.');
            }

            // Save assessment data to session
            session()->set('assessment_data', $assessmentData);

            // Check which assessment types are selected
            $tugasSelected = false;
            $utsSelected = false;
            $uasSelected = false;

            // Loop through the assessment data to check what's selected
            foreach ($assessmentData as $cpmkData) {
                foreach ($cpmkData as $subCpmkData) {
                    if (isset($subCpmkData['tugas']) && $subCpmkData['tugas']) {
                        $tugasSelected = true;
                    }
                    if (isset($subCpmkData['uts']) && $subCpmkData['uts']) {
                        $utsSelected = true;
                    }
                    if (isset($subCpmkData['uas']) && $subCpmkData['uas']) {
                        $uasSelected = true;
                    }
                }
            }

            // Check if files were changed
            $filesChanged = $this->request->getPost('files_changed') === 'true';

            // If files haven't changed, we can keep the existing files in session
            if (!$filesChanged) {
                session()->set('current_progress', 'assessment');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data asesmen berhasil disimpan'
                ]);
            }

            // Process file uploads
            $uploadedFiles = session()->get('assessment_files') ?? [];
            
            // Define file fields based on selected assessment types
            $fileFields = [];
            
            if ($tugasSelected) {
                $fileFields[] = 'soal_tugas';
                $fileFields[] = 'rubrik_tugas';
            }
            
            if ($utsSelected) {
                $fileFields[] = 'soal_uts';
                $fileFields[] = 'rubrik_uts';
            }
            
            if ($uasSelected) {
                $fileFields[] = 'soal_uas';
                $fileFields[] = 'rubrik_uas';
            }

            // Create upload directory if it doesn't exist
            $uploadDir = WRITEPATH . 'uploads/assessment/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($fileFields as $field) {
                $file = $this->request->getFile($field);

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $field . '_' . uniqid() . '.pdf';

                    // Move the file to the upload directory
                    $file->move($uploadDir, $newName);

                    // Store file information in session
                    $uploadedFiles[$field] = [
                        'name' => $file->getClientName(),
                        'path' => 'uploads/assessment/' . $newName,
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Save file data to session
            session()->set('assessment_files', $uploadedFiles);
            session()->set('current_progress', 'assessment');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data asesmen dan file berhasil disimpan',
                'redirect' => site_url('portofolio-form/pelaksanaan-perkuliahan')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function pelaksanaan_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if previous data exists in session
        if (!session()->get('assessment_data')) {
            return redirect()->to('/portofolio-form/rancangan-asesmen')
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        return view('backend/portofolio-form/pelaksanaan-perkuliahan');
    }

    public function savePelaksanaanPerkuliahan()
    {
        log_message('debug', 'savePelaksanaanPerkuliahan function called');

        try {
            // Process file uploads
            $uploadedFiles = session()->get('pelaksanaan_files') ?? [];
            $fileFields = [
                'kontrak_kuliah',
                'realisasi_mengajar',
                'kehadiran_mahasiswa'
            ];

            // Create upload directory if it doesn't exist
            $uploadDir = WRITEPATH . 'uploads/pelaksanaan/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($fileFields as $field) {
                $file = $this->request->getFile($field);

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $field . '_' . uniqid() . '.pdf';

                    // Move the file to the upload directory
                    $file->move($uploadDir, $newName);

                    // Store file information in session
                    $uploadedFiles[$field] = [
                        'name' => $file->getClientName(),
                        'path' => 'uploads/pelaksanaan/' . $newName,
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Save file data to session
            session()->set('pelaksanaan_files', $uploadedFiles);
            session()->set('current_progress', 'pelaksanaan');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pelaksanaan perkuliahan berhasil disimpan',
                'redirect' => site_url('portofolio-form/hasil-asesmen')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function hasil_asesmen()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if previous data exists in session
        if (!session()->get('pelaksanaan_files')) {
            return redirect()->to('/portofolio-form/pelaksanaan-perkuliahan')
                ->with('error', 'Silakan lengkapi pelaksanaan perkuliahan terlebih dahulu.');
        }

        return view('backend/portofolio-form/hasil-asesmen');
    }

    public function saveHasilAsesmen()
    {
        try {
            // Process file uploads
            $uploadedFiles = session()->get('hasil_asesmen_files') ?? [];
            $fileFields = [
                'jawaban_tugas',
                'jawaban_uts',
                'jawaban_uas',
                'nilai_mata_kuliah',
                'nilai_cpmk'
            ];

            // Create upload directory if it doesn't exist
            $uploadDir = WRITEPATH . 'uploads/hasil_asesmen/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($fileFields as $field) {
                $file = $this->request->getFile($field);

                // Skip optional file if not provided
                if ($field === 'nilai_mata_kuliah' && (!$file || $file->getError() === 4)) {
                    continue;
                }

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $field . '_' . uniqid() . '.pdf';

                    // Move the file to the upload directory
                    $file->move($uploadDir, $newName);

                    // Store file information in session
                    $uploadedFiles[$field] = [
                        'name' => $file->getClientName(),
                        'path' => 'uploads/hasil_asesmen/' . $newName,
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Save file data to session
            session()->set('hasil_asesmen_files', $uploadedFiles);
            session()->set('current_progress', 'hasil_asesmen');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data hasil asesmen berhasil disimpan',
                'redirect' => site_url('portofolio-form/evaluasi-perkuliahan')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function evaluasi_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if previous data exists in session
        if (!session()->get('hasil_asesmen_files')) {
            return redirect()->to('/portofolio-form/hasil-asesmen')
                ->with('error', 'Silakan lengkapi hasil asesmen terlebih dahulu.');
        }

        // Ambil data evaluasi dari session
        $evaluasi_perkuliahan = session()->get('evaluasi_perkuliahan') ?? '';
        
        // Ambil data CPMK dari session
        $cpmk_data = session()->get('cpmk_data')['cpmk'] ?? [];
        
        // Ambil nilai CPMK yang sudah disimpan (jika ada)
        $cpmk_nilai = session()->get('cpmk_nilai') ?? [];

        // Ambil PDF URL dari session jika ada
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/temp/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/evaluasi-perkuliahan', [
            'evaluasi_perkuliahan' => $evaluasi_perkuliahan,
            'cpmk_data' => $cpmk_data,
            'cpmk_nilai' => $cpmk_nilai,
            'pdfUrl' => $pdfUrl
        ]);
    }

    public function saveEvaluasiPerkuliahan()
    {
        try {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'evaluasi' => 'required'
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Evaluasi perkuliahan wajib diisi.'
                ]);
            }

            // Ambil data dari form
            $evaluasi = $this->request->getPost('evaluasi');

            // Simpan ke session
            session()->set('evaluasi_perkuliahan', $evaluasi);
            session()->set('current_progress', 'evaluasi_perkuliahan');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data evaluasi perkuliahan berhasil disimpan',
                'redirect' => site_url('portofolio-form/save-portofolio')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function savePortofolio()
    {
        $session = session();
        $sessionData = $session->get();

        // Simpan data ke tabel portofolio
        $portofolioModel = new PortofolioModel();
        $portofolioData = [
            'id_user' => $sessionData['UserSession']['id_user'],
            'kode_mk' => $sessionData['info_matkul']['kode_mk'],
            'nama_mk' => $sessionData['info_matkul']['nama_mk'],
            'npp' => $sessionData['UserSession']['username']
        ];
        $portofolioId = $portofolioModel->insert($portofolioData);

        // Simpan data ke tabel Rps
        $rpsModel = new RpsModel();
        $evaluasiPerkuliahanData = [
            'id_porto' => $portofolioId,
            'file_rps' => $sessionData['uploaded_rps']
        ];
        $rpsModel->insert($evaluasiPerkuliahanData);

        // Simpan data ke tabel identitas matkul
        $identitasMatkulModel = new IdentitasMatkulModel();
        $identitasMatkulData = [
            'id_porto' => $portofolioId,
            'prasyarat_mk' => $sessionData['info_matkul']['mk_prasyarat'],
            'topik_perkuliahan' => $sessionData['info_matkul']['topik_mk']
        ];
        $identitasMatkulModel->insert($identitasMatkulData);

        // Simpan data ke tabel cpl
        $cplModel = new CplModel();
        foreach ($sessionData['cpl_pi_data'] as $noCpl => $cpl) {
            $cplData = [
                'id_porto' => $portofolioId,
                'no_cpl' => $noCpl,
                'isi_cpl' => $cpl['cpl_indo']
            ];
            $cplId = $cplModel->insert($cplData);

            // Simpan data ke tabel pi
            $piModel = new PiModel();
            foreach ($cpl['pi_list'] as $noPi => $pi) {
                if ($pi !== "\N") {
                    $piData = [
                        'id_cpl' => $cplId,
                        'no_pi' => $noPi + 1,
                        'isi_ikcp' => $pi
                    ];
                    $piModel->insert($piData);
                }
            }
        }

        // Simpan data ke tabel cpmk
        $cpmkModel = new CpmkModel();
        $subCpmkModel = new SubCpmkModel();
        $cpmkMapping = []; // Untuk menyimpan mapping antara no_cpmk dan ID database
        $subCpmkMapping = []; // Untuk menyimpan mapping antara no_scpmk dan ID database

        foreach ($sessionData['cpmk_data']['cpmk'] as $noCpmk => $cpmk) {
            $cpmkData = [
                'id_porto' => $portofolioId,
                'no_cpmk' => $cpmk['selectedCpl'],
                'isi_cpmk' => $cpmk['narasi']
            ];
            $cpmkId = $cpmkModel->insert($cpmkData);
            $cpmkMapping[$noCpmk] = $cpmkId; // Simpan mapping antara no_cpmk dan ID database

            // Simpan Sub-CPMK untuk CPMK ini dan catat ID-nya
            foreach ($cpmk['sub'] as $noSubCpmk => $subCpmk) {
                $subCpmkData = [
                    'id_porto' => $portofolioId,
                    'no_scpmk' => $noSubCpmk,
                    'isi_scmpk' => $subCpmk
                ];
                $subCpmkId = $subCpmkModel->insert($subCpmkData);
                $subCpmkMapping[$noSubCpmk] = $subCpmkId; // Simpan mapping antara no_scpmk dan ID database
            }
        }

        // Convert stdClass objects to arrays
        $mappingDataArray = json_decode(json_encode($sessionData['mapping_data']), true);

        // Simpan data ke tabel mapping_cpmk_scpmk menggunakan ID yang benar
        $mappingCpmkScpmkModel = new MappingCpmkScpmkModel();

        // Lookup table to map CPL ID to actual CPMK ID
        $cplToCpmkMap = [];
        foreach ($sessionData['cpmk_data']['cpmk'] as $noCpmk => $cpmk) {
            $cplToCpmkMap[$cpmk['selectedCpl']] = $noCpmk;
        }

        foreach ($mappingDataArray as $cplId => $cpmkMappings) {
            // Get the corresponding CPMK ID for this CPL ID
            $sessionCpmkId = $cplToCpmkMap[$cplId] ?? null;

            if ($sessionCpmkId) {
                // Cari ID database untuk CPMK ini
                $actualCpmkId = $cpmkMapping[$sessionCpmkId] ?? null;

                if ($actualCpmkId) {
                    foreach ($cpmkMappings as $innerKey => $subCpmkValues) {
                        foreach ($subCpmkValues as $sessionScpmkId => $nilai) {
                            // Cari ID database untuk Sub-CPMK ini
                            $actualScpmkId = $subCpmkMapping[$sessionScpmkId] ?? null;

                            if ($actualScpmkId && $nilai == 1) {
                                $mappingData = [
                                    'id_cpmk' => $actualCpmkId,
                                    'id_scpmk' => $actualScpmkId,
                                    'nilai' => $nilai
                                ];
                                $mappingCpmkScpmkModel->insert($mappingData);
                            }
                        }
                    }
                }
            }
        }

        // Simpan data ke tabel rancangan_asesmen
        $rancanganAsesmenModel = new RancanganAsesmenModel();
        foreach ($sessionData['assessment_data'] as $cpmkId => $assessment) {
            // Cari ID database untuk CPMK ini
            $actualCpmkId = $cpmkMapping[$cpmkId] ?? null;

            if ($actualCpmkId) {
                foreach ($assessment as $scpmkId => $kategori) {
                    // Cari ID database untuk Sub-CPMK ini
                    $actualScpmkId = $subCpmkMapping[$scpmkId] ?? null;

                    if ($actualScpmkId) {
                        $rancanganAsesmenData = [
                            'id_porto' => $portofolioId,
                            'id_cpmk' => $actualCpmkId,
                            'id_scpmk' => $actualScpmkId,
                            'tugas' => isset($kategori['tugas']) && $kategori['tugas'] ? 1 : 0,
                            'uts' => isset($kategori['uts']) && $kategori['uts'] ? 1 : 0,
                            'uas' => isset($kategori['uas']) && $kategori['uas'] ? 1 : 0
                        ];
                        $rancanganAsesmenModel->insert($rancanganAsesmenData);
                    }
                }
            }
        }

        // Simpan data ke tabel rancangan asesmen file
        $rancanganAsesmenFileModel = new RancanganAsesmenFileModel();
        foreach ($sessionData['assessment_files'] as $kategori => $file) {
            // Kategori_file berdasarkan prefix "soal_" atau "rubrik_"
            if (strpos($kategori, 'soal_') === 0) {
                $kategoriFile = 'Soal';
            } elseif (strpos($kategori, 'rubrik_') === 0) {
                $kategoriFile = 'Rubrik';
            } else {
                $kategoriFile = 'Lainnya';
            }

            // Kategori berdasarkan suffix
            if (strpos($kategori, '_tugas') !== false) {
                $kategoriAsesmen = 'Tugas';
            } elseif (strpos($kategori, '_uts') !== false) {
                $kategoriAsesmen = 'UTS';
            } elseif (strpos($kategori, '_uas') !== false) {
                $kategoriAsesmen = 'UAS';
            } else {
                $kategoriAsesmen = 'Lainnya';
            }

            $rancanganAsesmenFileData = [
                'id_porto' => $portofolioId,
                'kategori' => $kategoriAsesmen,
                'kategori_file' => $kategoriFile,
                'file_pdf' => $file['path']
            ];
            $rancanganAsesmenFileModel->insert($rancanganAsesmenFileData);
        }

        // Simpan data ke tabel pelaksanaan perkuliahan
        $pelaksanaanModel = new PelaksanaanPerkuliahanModel();
        $sessionFiles = session()->get('pelaksanaan_files');
        if (is_string($sessionFiles)) {
            $sessionFiles = unserialize($sessionFiles);
        }
        $pelaksanaanData = [
            'id_porto' => $portofolioId,
            'file_kontrak' => isset($sessionFiles['kontrak_kuliah']) ? $sessionFiles['kontrak_kuliah']['path'] : null,
            'file_realisasi' => isset($sessionFiles['realisasi_mengajar']) ? $sessionFiles['realisasi_mengajar']['path'] : null,
            'file_kehadiran' => isset($sessionFiles['kehadiran_mahasiswa']) ? $sessionFiles['kehadiran_mahasiswa']['path'] : null
        ];
        $pelaksanaanModel->insert($pelaksanaanData);

        // Simpan data ke tabel hasil asesmen
        $hasilAsesmenModel = new HasilAsesmenModel();
        $sessionFiles = session()->get('hasil_asesmen_files');
        if (is_string($sessionFiles)) {
            $sessionFiles = unserialize($sessionFiles);
        }
        $hasilAsesmenData = [
            'id_porto' => $portofolioId,
            'file_tugas' => isset($sessionFiles['jawaban_tugas']) ? $sessionFiles['jawaban_tugas']['path'] : null,
            'file_uts' => isset($sessionFiles['jawaban_uts']) ? $sessionFiles['jawaban_uts']['path'] : null,
            'file_uas' => isset($sessionFiles['jawaban_uas']) ? $sessionFiles['jawaban_uas']['path'] : null,
            'file_nilai_mk' => isset($sessionFiles['nilai_mata_kuliah']) ? $sessionFiles['nilai_mata_kuliah']['path'] : null,
            'file_nilai_cpmk' => isset($sessionFiles['nilai_cpmk']) ? $sessionFiles['nilai_cpmk']['path'] : null
        ];
        $hasilAsesmenModel->insert($hasilAsesmenData);

        // Simpan data ke tabel hasil asesmen
        $hasilAsesmenModel = new HasilAsesmenModel();
        $sessionFiles = session()->get('hasil_asesmen_files');
        if (is_string($sessionFiles)) {
            $sessionFiles = unserialize($sessionFiles);
        }
        $hasilAsesmenData = [
            'id_porto' => $portofolioId,
            'file_tugas' => isset($sessionFiles['jawaban_tugas']) ? $sessionFiles['jawaban_tugas']['path'] : null,
            'file_uts' => isset($sessionFiles['jawaban_uts']) ? $sessionFiles['jawaban_uts']['path'] : null,
            'file_uas' => isset($sessionFiles['jawaban_uas']) ? $sessionFiles['jawaban_uas']['path'] : null,
            'file_nilai_mk' => isset($sessionFiles['nilai_mata_kuliah']) ? $sessionFiles['nilai_mata_kuliah']['path'] : null,
            'file_nilai_cpmk' => isset($sessionFiles['nilai_cpmk']) ? $sessionFiles['nilai_cpmk']['path'] : null
        ];
        $hasilAsesmenModel->insert($hasilAsesmenData);

        // Simpan data ke tabel evaluasi perkuliahan
        $evaluasiPerkuliahanModel = new EvaluasiPerkuliahanModel();
        $evaluasiPerkuliahanData = [
            'id_porto' => $portofolioId,
            'isi_evaluasi' => $sessionData['evaluasi_perkuliahan']
        ];
        $evaluasiPerkuliahanModel->insert($evaluasiPerkuliahanData);

        // Clear session data except user session
        $this->clearSessionExceptUser();

        return redirect()->to('/portofolio-form')->with('success', 'Portofolio berhasil disimpan.');
    }

    public function clearSessionExceptUser()
    {
        $session = session();

        // Keep user session data
        $userSession = isset($_SESSION['UserSession']) ? $_SESSION['UserSession'] : null;

        // Get all session keys
        $allKeys = array_keys($_SESSION);

        // Remove all keys except UserSession
        foreach ($allKeys as $key) {
            if ($key !== 'UserSession') {
                $session->remove($key);
            }
        }
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }

    public function tes_cetak()
    {
        return view('backend/pdf/test-cetak');
    }
}
