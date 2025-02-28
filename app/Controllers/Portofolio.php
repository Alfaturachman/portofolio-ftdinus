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
        // Validasi file upload
        $validation = \Config\Services::validation();
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

        // Get data from database
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
        return view('backend/form-portofolio/info-matkul', [
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
                'message' => 'Data asesmen berhasil disimpan',
                'redirect' => site_url('portofolio-form/pelaksanaan-perkuliahan')
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
            $fileFields = [
                'soal_tugas',
                'rubrik_tugas',
                'soal_uts',
                'rubrik_uts',
                'soal_uas',
                'rubrik_uas'
            ];

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
                'message' => 'Data asesmen dan file berhasil disimpan'
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

        return view('backend/form-portofolio/pelaksanaan-perkuliahan');
    }

    public function savePelaksanaanPerkuliahan()
    {
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

        return view('backend/form-portofolio/hasil-asesmen');
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

        return view('backend/form-portofolio/evaluasi-perkuliahan', [
            'evaluasi_perkuliahan' => $evaluasi_perkuliahan
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
                'redirect' => site_url('portofolio-form')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }
}
