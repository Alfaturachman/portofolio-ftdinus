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
use App\Controllers\BaseController;
use App\Models\IdentitasMatkulModel;
use App\Models\MappingCpmkScpmkModel;
use App\Models\RancanganAsesmenModel;
use App\Models\RancanganSoalModel;
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

        // Jika user masuk ke halaman index (tambah portofolio baru), pastikan session bersih
        // Hapus mode edit dan session data terkait sebelum melanjutkan
        $this->clearEditMode();

        // Bersihkan semua session data terkait proses sebelumnya
        $this->clearSessionExceptUser();

        // Ambil NPP user login
        $currentUserNPP = session()->get('UserSession.username');

        $portofolioModel = new PortofolioModel();

        // Ambil semua data portofolio milik dosen yang login
        $data['matkulList'] = $portofolioModel->getAllPortofolio($currentUserNPP);

        // Cek status import untuk tiap mata kuliah (opsional, tergantung fungsi checkMahasiswaKelasExists)
        $importStatus = [];
        foreach ($data['matkulList'] as $matkul) {
            $key = $matkul['kode_mk'] . '_' . $matkul['tahun'] . '_' . $matkul['semester'];
            $importStatus[$key] = $portofolioModel->checkMahasiswaKelasExists(
                $matkul['kode_mk'],
                $matkul['tahun'],
                $matkul['semester']
            );
        }
        $data['importStatus'] = $importStatus;

        return view('backend/portofolio-form/index', $data);
    }

    public function daftar($kode_matkul, $kode_ts, $semester)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $portofolioModel = new PortofolioModel();

        // Ambil tahun & semester dari kode_ts
        $detail = $portofolioModel->getMatkulByKodeTS($kode_matkul, $kode_ts);

        if (!$detail) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'portofolioList' => $portofolioModel->getPortofolio($kode_matkul, $detail['tahun'], $detail['semester']),
            'matkul' => $detail,
            'kode_matkul' => $kode_matkul,
            'tahun' => $detail['tahun'],
            'semester' => $detail['semester']
        ];

        return view('backend/portofolio-form/daftar-portofolio', $data);
    }

    // Method untuk menghandle file Uplod RPS (Rencana Pembelajaran Semester)


    // Method form informasi mata kuliah
    public function info_matkul()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Get data from database from info_matkul table
        $db = \Config\Database::connect();
        $mataKuliahData = $db->table('info_matkul im')
            ->select('
        im.matakuliah AS nama_mk,
        im.kode_matkul AS kode_mk,
        md.kelp_matkul AS kelompok_mk,
        im.fakultas,
        im.smt_matkul,
        im.prodi AS progdi,
        im.teori AS sks_teori,
        im.praktek AS sks_praktik,
        md.tahun,
        md.semester,
        md.dosen
    ')
            ->join('matkul_diampu md', 'md.kode_matkul = im.kode_matkul', 'left')
            ->groupBy([
                'im.kode_matkul',
                'im.matakuliah',
                'md.kelp_matkul',
                'im.smt_matkul',
                'im.fakultas',
                'im.prodi',
                'im.teori',
                'im.praktek',
                'md.tahun',
                'md.semester',
                'md.dosen'
            ])
            ->orderBy('im.matakuliah', 'ASC')
            ->get()
            ->getResultArray();

        // Data tambahan dari session (jika ada)
        $infoMatkul = session()->get('info_matkul') ?? [];

        // Cek apakah ada file yang disimpan di session
        $rpsFile = session()->get('uploaded_rps');
        $pdfUrl = $rpsFile ? base_url('uploads/rps/' . $rpsFile) : '';

        // Update waktu aktifitas terakhir sebelum menampilkan view
        $this->updateLastActivity();

        // Kirim data ke view
        return view('backend/portofolio-form/info-matkul', [
            'mataKuliah' => $mataKuliahData,
            'infoMatkul' => $infoMatkul,
            'rpsFile' => $rpsFile,
            'pdfUrl' => $pdfUrl,
        ]);
    }

    public function saveInfoMatkul()
    {
        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Validasi file upload (jika ada file yang diupload)
        $rpsFile = $this->request->getFile('rps_file');
        if ($rpsFile && $rpsFile->isValid() && $rpsFile->getSize() > 0) {
            // Validasi file upload
            $validation = \Config\Services::validation();
            $validation->setRules([
                'rps_file' => [
                    'label' => 'RPS File',
                    'rules' => 'ext_in[rps_file,pdf]|max_size[rps_file,10240]',
                ],
            ]);

            // Jalankan validasi untuk file
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Handle upload file
            $newName = time() . '_' . $rpsFile->getRandomName();
            $rpsFile->move(WRITEPATH . 'uploads/rps', $newName);

            // Simpan nama file ke session
            session()->set('uploaded_rps', $newName);
        }

        // Validasi data form informasi mata kuliah
        $validation = \Config\Services::validation();
        $validation->setRules([
            'fakultas' => 'required',
            'progdi' => 'required',
            'nama_mk' => 'required',
            'kode_mk' => 'required',
            'kelompok_mk' => 'required',
            'sks_teori' => 'required|numeric',
            'sks_praktik' => 'required|numeric',
            'tahun' => 'required',
            'semester' => 'required',
            'smt_matkul' => 'required',
            'mk_prasyarat' => 'permit_empty',
            'topik_mk' => 'permit_empty',
        ]);

        // Jalankan validasi untuk data
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
            'tahun' => $this->request->getPost('tahun'),
            'semester' => $this->request->getPost('semester'),
            'smt_matkul' => $this->request->getPost('smt_matkul'),
            'mk_prasyarat' => $this->request->getPost('mk_prasyarat'),
            'topik_mk' => $this->request->getPost('topik_mk'),
        ];

        // Simpan data ke session
        session()->set('info_matkul', $data);

        // Log informasi penyimpanan data
        log_message('info', 'Data Mata Kuliah disimpan ke session: ' . json_encode($data));

        // Update waktu aktifitas terakhir setelah menyimpan data
        $this->updateLastActivity();

        // Periksa apakah ini mode edit
        if (session()->get('edit_mode')) {
            $idPorto = session()->get('edit_portofolio_id');
            // Redirect ke halaman selanjutnya dalam proses edit
            return redirect()->to('portofolio-form/cpl-pi-edit/' . $idPorto)->with('message', 'Data mata kuliah berhasil diperbarui.');
        } else {
            // Jika secara tidak sengaja masuk dalam mode edit dari proses sebelumnya
            if (session()->get('edit_mode')) {
                $this->clearEditMode();
                $this->clearSessionExceptUser();
            }
            // Redirect ke halaman berikutnya dengan pesan sukses (proses tambah baru)
            return redirect()->to('portofolio-form/cpl-pi')->with('message', 'Data mata kuliah dan RPS berhasil disimpan.');
        }
    }

    public function saveUploadRps()
    {
        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

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
        $file->move(WRITEPATH . 'uploads/rps', $newName);

        // Simpan nama file ke session
        session()->set('uploaded_rps', $newName);

        // Update waktu aktifitas terakhir setelah menyimpan data
        $this->updateLastActivity();

        // Return URL untuk iframe
        return $this->response->setJSON([
            'success' => true,
            'pdfUrl' => base_url('uploads/rps/' . $newName),
            'redirect' => site_url('portofolio-form/info-matkul')
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

    // Method untuk view uploaded PDF file
    public function view_uploaded_pdf($filename)
    {
        $path = WRITEPATH . 'uploads/rps/' . $filename;

        if (file_exists($path)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody(file_get_contents($path));
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('PDF not found');
        }
    }

    public function cpl_pi()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
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

        // Update waktu aktifitas terakhir setelah menyimpan data
        $this->updateLastActivity();

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/cpl-pi', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData
        ]);
    }

    // Method untuk halaman edit CPL-PI
    public function cpl_pi_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Pastikan ini mode edit dan ID sesuai
        if (!session()->get('edit_mode') || session()->get('edit_portofolio_id') != $idPorto) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Get CPL-PI data from session (already loaded during edit)
        $cplPiData = session()->get('cpl_pi_data') ?? [];

        // If session data is empty, it means we need to load from the database again
        if (empty($cplPiData)) {
            // We would need to fetch from database here if necessary
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['cpl'])) {
                foreach ($portofolioData['cpl'] as $cpl) {
                    $cplNo = $cpl['no_cpl'] ?? $cpl['noCpl'] ?? null;
                    if ($cplNo !== null) {
                        $pi_list = [];
                        foreach ($cpl['pi_list'] as $pi) {
                            $pi_list[] = $pi['isi_ikcp'];
                        }
                        $cplPiData[$cplNo] = [
                            'cpl_indo' => $cpl['isi_cpl'],
                            'pi_list' => $pi_list
                        ];
                    }
                }
                session()->set('cpl_pi_data', $cplPiData);
            }
        }

        // Update waktu aktifitas terakhir sebelum menampilkan view
        $this->updateLastActivity();

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/cpl-pi', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData,
            'idPorto' => $idPorto // Tambahkan ID portofolio untuk edit
        ]);
    }

    /**
     * Function to verify edit access
     * 
     * @param int $idPorto
     * @return bool
     */
    private function verifyEditAccess($idPorto)
    {
        return session()->get('edit_mode') && session()->get('edit_portofolio_id') == $idPorto;
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

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Get CPL-PI data from session
        $cplPiData = session()->get('cpl_pi_data') ?? [];

        // If session data is empty, get from database
        if (empty($cplPiData)) {
            $cplPiModel = new \App\Models\CplPiModel();
            $cplPiData = $cplPiModel->getCplGrouped();
        }

        // Get PDF URL from session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/cpmk-subcpmk', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData  // Pass CPL data to the view
        ]);
    }

    // Method untuk halaman edit CPMK-subCPMK
    public function cpmk_subcpmk_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Get data from database to refresh all session data during edit
        $portofolioModel = new PortofolioModel();
        $portofolioData = $portofolioModel->getPortofolioById($idPorto);

        // Get CPL-PI data from session
        $cplPiData = session()->get('cpl_pi_data') ?? [];

        // If session data is empty, get from database or stored during edit
        if (empty($cplPiData) && $portofolioData && isset($portofolioData['cpl'])) {
            foreach ($portofolioData['cpl'] as $cpl) {
                $cplNo = $cpl['no_cpl'] ?? $cpl['noCpl'] ?? null;
                if ($cplNo !== null) {
                    $pi_list = [];
                    foreach ($cpl['pi_list'] as $pi) {
                        $pi_list[] = $pi['isi_ikcp'];
                    }
                    $cplPiData[$cplNo] = [
                        'cpl_indo' => $cpl['isi_cpl'],
                        'pi_list' => $pi_list
                    ];
                }
            }
            session()->set('cpl_pi_data', $cplPiData);
        }

        // Get CPMK data from session
        $cpmkData = session()->get('cpmk_data') ?? [];

        // If CPMK session data is empty, get from stored data during edit
        if (empty($cpmkData) && $portofolioData && isset($portofolioData['cpmk'])) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['cpmk'])) {
                $cpmkArray = [];
                $globalSubCpmkCounter = 1;

                foreach ($portofolioData['cpmk'] as $cpmkId => $cpmk) {
                    $subCpmkList = [];
                    foreach ($cpmk['sub_cpmk'] as $subCpmk) {
                        $subCpmkNo = $subCpmk['no_scpmk'] ?? null;
                        if ($subCpmkNo !== null) {
                            $subCpmkList[$subCpmkNo] = $subCpmk['isi_scmpk'];
                            if ($subCpmkNo > $globalSubCpmkCounter) {
                                $globalSubCpmkCounter = $subCpmkNo;
                            }
                        }
                    }
                    $cpmkNo = $cpmk['no_cpmk'] ?? null;
                    if ($cpmkNo !== null) {
                        $cpmkArray[$cpmkNo] = [
                            'id' => $cpmk['id'],
                            'no_cpmk' => $cpmkNo,
                            'narasi' => $cpmk['isi_cpmk'],
                            'avg_cpmk' => $cpmk['avg_cpmk'],
                            'sub' => $subCpmkList
                        ];
                    }
                }

                $cpmkData = [
                    'cpmk' => $cpmkArray,
                    'globalSubCpmkCounter' => $globalSubCpmkCounter
                ];

                session()->set('cpmk_data', $cpmkData);
            }
        }

        // Get PDF URL from session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        // Update waktu aktifitas terakhir sebelum menampilkan view
        $this->updateLastActivity();

        return view('backend/portofolio-form/cpmk-subcpmk', [
            'pdfUrl' => $pdfUrl,
            'cplPiData' => $cplPiData,  // Pass CPL data to the view
            'cpmkData' => $cpmkData,    // Pass CPMK data to the view
            'idPorto' => $idPorto       // Tambahkan ID portofolio untuk edit
        ]);
    }

    public function saveCPMKToSession()
    {
        $json = $this->request->getJSON();
        $cpmkData = $json->cpmk ?? null;
        $cplData = $json->cpl ?? null;
        $globalSubCpmkCounter = $json->globalSubCpmkCounter ?? 1;

        if ($cpmkData) {
            // Convert to array if it's an object
            $cpmkArray = json_decode(json_encode($cpmkData), true);

            // Store CPMK data in session
            session()->set('cpmk_data', [
                'cpmk' => $cpmkArray,
                'globalSubCpmkCounter' => $globalSubCpmkCounter
            ]);

            // Check if cpl_pi_data exists in session
            $existingCplData = session()->get('cpl_pi_data');

            // If CPL data doesn't exist or is empty, save the CPL data from the form
            if (empty($existingCplData) && !empty($cplData)) {
                $cplArray = json_decode(json_encode($cplData), true);

                // Transform CPL data to match the expected format
                $formattedCplData = [];
                foreach ($cplArray as $cplNo => $cplInfo) {
                    $formattedCplData[$cplNo] = [
                        'cpl_indo' => $cplInfo['narasi'] ?? '',
                        'pi_list' => [] // Empty PI list since we don't have it
                    ];
                }

                session()->set('cpl_pi_data', $formattedCplData);
            } elseif (!empty($cplData)) {
                // If CPL data exists, merge with new CPL data from form
                $cplArray = json_decode(json_encode($cplData), true);
                $existingCplData = $existingCplData ?? [];

                foreach ($cplArray as $cplNo => $cplInfo) {
                    // Only add if CPL doesn't exist yet
                    if (!isset($existingCplData[$cplNo])) {
                        $existingCplData[$cplNo] = [
                            'cpl_indo' => $cplInfo['narasi'] ?? '',
                            'pi_list' => []
                        ];
                    }
                }

                session()->set('cpl_pi_data', $existingCplData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data CPMK dan CPL berhasil disimpan'
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

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Update waktu aktifitas terakhir sebelum menampilkan view
        $this->updateLastActivity();

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/pemetaan', [
            'pdfUrl' => $pdfUrl,
        ]);
    }

    // Method untuk halaman edit pemetaan
public function pemetaan_edit($idPorto)
{
    if (!session()->get('UserSession.logged_in')) {
        return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Validasi mode edit
    if (!session()->get('edit_mode') || session()->get('edit_portofolio_id') != $idPorto) {
        return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
    }

    $db = \Config\Database::connect();

    // === 1. Ambil data CPL berdasarkan portofolio ===
    $cpl = $db->table('cpl')
        ->where('id_porto', $idPorto)
        ->get()
        ->getResultArray();

    // === 2. Ambil data CPMK berdasarkan portofolio ===
    $cpmk = $db->table('cpmk')
        ->where('id_porto', $idPorto)
        ->get()
        ->getResultArray();

    // === 3. Ambil data mapping dari database ===
    $mappingRows = $db->table('mapping_cpmk_scpmk')
        ->where('id_portofolio', $idPorto)
        ->get()
        ->getResultArray();

    // === 4. Konversi ke array bertingkat [cpl][cpmk] = nilai (1/0) ===
    $mappingData = [];
    foreach ($mappingRows as $row) {
        $mappingData[$row['id_cpl']][$row['id_cpmk']] = (int)$row['nilai'];
    }

    // === 5. Simpan ke session ===
    session()->set('cpl_data', $cpl);
    session()->set('cpmk_data', $cpmk);
    session()->set('mapping_data', $mappingData);

    // === 6. Kirim ke view ===
    return view('backend/portofolio-form/pemetaan', [
        'idPorto' => $idPorto
    ]);
}


    public function saveMappingToSession()
    {
        try {
            $json = $this->request->getJSON();
            $mappingData = $json->mapping ?? null;
            $idPorto = $json->idPorto ?? null; // Tambahkan untuk mode edit

            // Allow empty mapping data - this can happen when all checkboxes are unchecked
            // or when initializing the mapping for the first time
            if ($mappingData === null) {
                throw new \Exception('Data pemetaan kosong atau tidak valid.');
            }

            session()->set('mapping_data', $mappingData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pemetaan berhasil disimpan',
                'redirect' => $idPorto ?
                    base_url('portofolio-form/rancangan-asesmen-edit/' . $idPorto) :
                    base_url('portofolio-form/rancangan-asesmen')
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

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Check if mapping data exists in session
        if (!session()->get('mapping_data')) {
            return redirect()->to('/portofolio-form/pemetaan')
                ->with('error', 'Silakan lengkapi pemetaan terlebih dahulu.');
        }

        return view('backend/portofolio-form/rancangan-asesmen');
    }

    // Method untuk halaman edit rancangan asesmen
    public function rancangan_asesmen_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Membersihkan session yang tidak aktif jika melebihi batas waktu
        $this->cleanupInactiveSession();

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Get data from database to refresh all session data during edit
        $portofolioModel = new PortofolioModel();
        $portofolioData = $portofolioModel->getPortofolioById($idPorto);

        if ($portofolioData) {
            // Load mapping data from database
            if (isset($portofolioData['mapping_cpmk_scpmk'])) {
                // Transform the mapping data from database IDs to the expected format (cpl->cpmk->subcpmk)
                $rawMappingData = $portofolioData['mapping_cpmk_scpmk'];
                $transformedMappingData = [];

                // Create lookup maps for CPMK ID to CPMK number and CPMK to CPL
                $cpmkIdToNumberMap = [];
                $cpmkNumberToCplMap = [];

                if (isset($portofolioData['cpmk']) && is_array($portofolioData['cpmk'])) {
                    foreach ($portofolioData['cpmk'] as $cpmkItem) {
                        $cpmkId = $cpmkItem['id'];
                        $cpmkNumber = $cpmkItem['no_cpmk'];
                        $selectedCpl = $cpmkItem['selectedCpl'] ?? $cpmkItem['selected_cpl'] ?? 1;

                        $cpmkIdToNumberMap[$cpmkId] = $cpmkNumber;
                        $cpmkNumberToCplMap[$cpmkNumber] = $selectedCpl;
                    }
                }

                // Create lookup map for Sub-CPMK ID to Sub-CPMK number
                $subCpmkIdToNumberMap = [];
                if (isset($portofolioData['cpmk']) && is_array($portofolioData['cpmk'])) {
                    foreach ($portofolioData['cpmk'] as $cpmkItem) {
                        if (isset($cpmkItem['sub_cpmk']) && is_array($cpmkItem['sub_cpmk'])) {
                            foreach ($cpmkItem['sub_cpmk'] as $subCpmk) {
                                $subCpmkId = $subCpmk['id'];
                                $subCpmkNumber = $subCpmk['no_scpmk'];
                                $subCpmkIdToNumberMap[$subCpmkId] = $subCpmkNumber;
                            }
                        }
                    }
                }

                // Transform the mapping data
                foreach ($rawMappingData as $idCpmk => $subCpmkMappings) {
                    $cpmkNumber = $cpmkIdToNumberMap[$idCpmk] ?? null;
                    if ($cpmkNumber === null) continue;

                    $cplNumber = $cpmkNumberToCplMap[$cpmkNumber] ?? null;
                    if ($cplNumber === null) continue;

                    if (!isset($transformedMappingData[$cplNumber])) {
                        $transformedMappingData[$cplNumber] = [];
                    }
                    if (!isset($transformedMappingData[$cplNumber][$cpmkNumber])) {
                        $transformedMappingData[$cplNumber][$cpmkNumber] = [];
                    }

                    foreach ($subCpmkMappings as $idScpmk => $value) {
                        $subCpmkNumber = $subCpmkIdToNumberMap[$idScpmk] ?? null;
                        if ($subCpmkNumber !== null) {
                            $transformedMappingData[$cplNumber][$cpmkNumber][$subCpmkNumber] = $value;
                        }
                    }
                }

                session()->set('mapping_data', $transformedMappingData);
            }

            // Get CPMK data for assessment
            if (isset($portofolioData['cpmk'])) {
                $cpmkData = [];
                foreach ($portofolioData['cpmk'] as $cpmk) {
                    $cpmkNo = $cpmk['no_cpmk'] ?? null;
                    if ($cpmkNo !== null) {
                        $cpmkData[$cpmkNo] = [
                            'no_cpmk' => $cpmkNo,
                            'narasi' => $cpmk['isi_cpmk']
                        ];
                    }
                }

                // Initialize assessment data if not in session
                if (!session()->get('assessment_data')) {
                    $assessmentData = [];
                    foreach ($cpmkData as $cpmkId => $cpmk) {
                        $assessmentData[$cpmkId] = [
                            'tugas' => 0,
                            'uts' => 0,
                            'uas' => 0
                        ];
                    }
                    session()->set('assessment_data', $assessmentData);
                }
            }
        }

        // Check if mapping data exists in session
        if (!session()->get('mapping_data')) {
            return redirect()->to('/portofolio-form/pemetaan-edit/' . $idPorto)
                ->with('error', 'Silakan lengkapi pemetaan terlebih dahulu.');
        }

        // Update waktu aktifitas terakhir sebelum menampilkan view
        $this->updateLastActivity();

        return view('backend/portofolio-form/rancangan-asesmen', [
            'idPorto' => $idPorto
        ]);
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
                if (isset($cpmkData['tugas']) && $cpmkData['tugas']) {
                    $tugasSelected = true;
                }
                if (isset($cpmkData['uts']) && $cpmkData['uts']) {
                    $utsSelected = true;
                }
                if (isset($cpmkData['uas']) && $cpmkData['uas']) {
                    $uasSelected = true;
                }
            }

            // Check if files were changed
            $filesChanged = $this->request->getPost('files_changed') === 'true';

            // If files haven't changed, we can keep the existing files in session
            if (!$filesChanged) {
                session()->set('current_progress', 'assessment');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data asesmen berhasil disimpan',
                    'redirect' => site_url('portofolio-form/rancangan-soal') // Changed redirect to rancangan-soal
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
                'redirect' => site_url('portofolio-form/rancangan-soal') // Changed redirect to rancangan-soal
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rancangan_soal()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Check if assessment data exists in session
        if (!session()->get('assessment_data')) {
            return redirect()->to('/portofolio-form/rancangan-asesmen')
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        // Initialize soal_mapping data in session if not exists
        if (!session()->has('soal_mapping_data')) {
            // Create default mapping with 1 soal for each assessment type
            $soalMapping = [
                'tugas' => [
                    ['soal_no' => 1, 'cpmk_mappings' => []]
                ],
                'uts' => [
                    ['soal_no' => 1, 'cpmk_mappings' => []]
                ],
                'uas' => [
                    ['soal_no' => 1, 'cpmk_mappings' => []]
                ]
            ];

            session()->set('soal_mapping_data', $soalMapping);
        }

        return view('backend/portofolio-form/rancangan-soal');
    }

    // Method untuk halaman edit rancangan soal
    public function rancangan_soal_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Check if assessment data exists in session
        if (!session()->get('assessment_data')) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['assessment'])) {
                $assessmentData = [];
                foreach ($portofolioData['assessment'] as $assessment) {
                    $cpmkNo = $assessment['no_cpmk'] ?? null;
                    if ($cpmkNo !== null) {
                        if (!isset($assessmentData[$cpmkNo])) {
                            $assessmentData[$cpmkNo] = [
                                'tugas' => 0,
                                'uts' => 0,
                                'uas' => 0,
                            ];
                        }
                        if ($assessment['tugas'] == 1) $assessmentData[$cpmkNo]['tugas'] = 1;
                        if ($assessment['uts'] == 1) $assessmentData[$cpmkNo]['uts'] = 1;
                        if ($assessment['uas'] == 1) $assessmentData[$cpmkNo]['uas'] = 1;
                    }
                }
                session()->set('assessment_data', $assessmentData);
            }

            return redirect()->to('/portofolio-form/rancangan-asesmen-edit/' . $idPorto)
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        // Initialize soal_mapping data in session if not exists
        if (!session()->has('soal_mapping_data')) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['soal'])) {
                $soalMapping = [
                    'tugas' => [],
                    'uts' => [],
                    'uas' => []
                ];

                $groupedSoal = [];
                foreach ($portofolioData['soal'] as $soal) {
                    $kategori = $soal['kategori_soal'] ?? null;
                    $noSoal = $soal['no_soal'] ?? null;
                    $cpmkNo = $soal['no_cpmk'] ?? null;

                    if ($kategori !== null && $noSoal !== null && $cpmkNo !== null) {
                        if (!isset($groupedSoal[$kategori][$noSoal])) {
                            $groupedSoal[$kategori][$noSoal] = [
                                'soal_no' => $noSoal,
                                'cpmk_mappings' => []
                            ];
                        }

                        $groupedSoal[$kategori][$noSoal]['cpmk_mappings'][$cpmkNo] = $soal['nilai'];
                    }
                }

                foreach ($groupedSoal as $kategori => $kategoriSoal) {
                    foreach ($kategoriSoal as $noSoal => $data) {
                        $soalMapping[$kategori][] = $data;
                    }
                }

                session()->set('soal_mapping_data', $soalMapping);
            } else {
                // Create default mapping if no data exists
                $soalMapping = [
                    'tugas' => [
                        ['soal_no' => 1, 'cpmk_mappings' => []]
                    ],
                    'uts' => [
                        ['soal_no' => 1, 'cpmk_mappings' => []]
                    ],
                    'uas' => [
                        ['soal_no' => 1, 'cpmk_mappings' => []]
                    ]
                ];
                session()->set('soal_mapping_data', $soalMapping);
            }
        }

        return view('backend/portofolio-form/rancangan-soal', [
            'idPorto' => $idPorto
        ]);
    }

    public function saveSoalMapping()
    {
        try {
            $json = $this->request->getJSON();
            $soalMappingData = $json->soal_mapping ?? null;

            if (!$soalMappingData) {
                throw new \Exception('Data mapping soal kosong atau tidak valid.');
            }

            // Convert the data to an array
            $soalMappingArray = json_decode(json_encode($soalMappingData), true);

            // Process the data before saving
            $processedData = [
                'tugas' => [],
                'uts' => [],
                'uas' => []
            ];

            foreach (['tugas', 'uts', 'uas'] as $type) {
                if (isset($soalMappingArray[$type]) && is_array($soalMappingArray[$type])) {
                    // Sort the items by soal_no to ensure consistent ordering
                    usort($soalMappingArray[$type], function ($a, $b) {
                        return $a['soal_no'] <=> $b['soal_no'];
                    });

                    $processedData[$type] = $soalMappingArray[$type];
                }
            }

            session()->set('soal_mapping_data', $processedData);
            session()->set('current_progress', 'soal_mapping');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data mapping soal berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addSoal()
    {
        try {
            $json = $this->request->getJSON();
            $assessmentType = $json->assessment_type ?? null;

            if (!$assessmentType || !in_array($assessmentType, ['tugas', 'uts', 'uas'])) {
                throw new \Exception('Tipe asesmen tidak valid.');
            }

            // Get current soal mapping data
            $soalMappingData = session()->get('soal_mapping_data') ?? [
                'tugas' => [],
                'uts' => [],
                'uas' => []
            ];

            // Find the highest soal number for this assessment type
            $maxSoalNo = 0;
            foreach ($soalMappingData[$assessmentType] as $soal) {
                if ($soal['soal_no'] > $maxSoalNo) {
                    $maxSoalNo = $soal['soal_no'];
                }
            }

            // Add new soal with number incremented by 1
            $soalMappingData[$assessmentType][] = [
                'soal_no' => $maxSoalNo + 1,
                'cpmk_mappings' => []
            ];

            // Save updated data back to session
            session()->set('soal_mapping_data', $soalMappingData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Soal berhasil ditambahkan',
                'soal_no' => $maxSoalNo + 1
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function nilai_soal()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if soal_mapping data exists in session
        if (!session()->get('soal_mapping_data')) {
            return redirect()->to('/portofolio-form/rancangan-soal')
                ->with('error', 'Silakan lengkapi rancangan soal terlebih dahulu.');
        }

        // Check if assessment data exists in session
        if (!session()->get('assessment_data')) {
            return redirect()->to('/portofolio-form/rancangan-asesmen')
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        // Check if CPMK and mapping data exists in session
        if (!session()->get('cpmk_data') || !session()->get('mapping_data')) {
            return redirect()->to('/portofolio-form/cpmk-cpl')
                ->with('error', 'Silakan lengkapi data CPMK dan CPL terlebih dahulu.');
        }

        return view('backend/portofolio-form/nilai-soal');
    }

    public function getMahasiswaByKelas($kelasId)
    {
        if (!session()->get('UserSession.logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        $db = \Config\Database::connect();

        // Get kelas data
        $kelasData = $db->table('matkul_diampu')
            ->where('id', $kelasId)
            ->get()
            ->getRowArray();

        if (!$kelasData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas tidak ditemukan']);
        }

        // Get mahasiswa data
        $mahasiswaData = $db->table('mahasiswa_kelas')
            ->select('nim, nama')
            ->where('kode_matkul', $kelasData['kode_matkul'])
            ->where('kelp_matkul', $kelasData['kelp_matkul'])
            ->where('kode_ts', $kelasData['kode_ts'] ?? null)
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($mahasiswaData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada mahasiswa di kelas ini']);
        }

        return $this->response->setJSON(['success' => true, 'mahasiswa' => $mahasiswaData]);
    }

    public function saveNilaiSoal()
    {
        if (!session()->get('UserSession.logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.'
            ]);
        }

        // Get kelas ID from POST data
        $kelasId = $this->request->getPost('kelas_id');
        if (!$kelasId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Kelas tidak ditemukan'
            ]);
        }

        // Get nilai data from POST
        $nilaiData = $this->request->getPost('nilai');
        if (!$nilaiData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data nilai yang diterima'
            ]);
        }

        // Initialize arrays to store grades and averages
        $gradesData = [];
        $cpmkAverages = [];

        // Get assessment types with soal
        $soalMappingData = session()->get('soal_mapping_data') ?? [
            'tugas' => [],
            'uts' => [],
            'uas' => []
        ];

        // Process each assessment type (tugas, uts, uas)
        foreach ($nilaiData as $type => $mahasiswaData) {
            // Skip if no soal for this type
            if (empty($soalMappingData[$type])) {
                continue;
            }

            $gradesData[$type] = [];
            $cpmkAverages[$type] = [];

            // Get list of CPMK numbers used in this assessment type
            $cpmkNumbers = [];
            foreach ($soalMappingData[$type] as $soal) {
                foreach ($soal['cpmk_mappings'] as $cpmkNo => $isUsed) {
                    if ($isUsed && !in_array($cpmkNo, $cpmkNumbers)) {
                        $cpmkNumbers[] = $cpmkNo;
                    }
                }
            }

            // Calculate CPMK averages for this assessment type
            foreach ($cpmkNumbers as $cpmkNo) {
                // Get all soal mapped to this CPMK
                $soalNumbers = [];
                foreach ($soalMappingData[$type] as $soal) {
                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                        $soalNumbers[] = $soal['soal_no'];
                    }
                }

                // Calculate average for each soal in this CPMK
                $soalAverages = [];
                foreach ($soalNumbers as $soalNo) {
                    $sum = 0;
                    $count = 0;

                    // Sum up all student grades for this soal and CPMK
                    foreach ($mahasiswaData as $nim => $studentData) {
                        if (isset($studentData[$cpmkNo][$soalNo]) && $studentData[$cpmkNo][$soalNo] !== '') {
                            $sum += floatval($studentData[$cpmkNo][$soalNo]);
                            $count++;
                        }
                    }

                    // Calculate average for this soal
                    $soalAverages[$soalNo] = $count > 0 ? $sum / $count : 0;
                }

                // Calculate average of all soal for this CPMK
                $totalAvg = 0;
                $validCount = 0;
                foreach ($soalAverages as $avg) {
                    if ($avg > 0) {
                        $totalAvg += $avg;
                        $validCount++;
                    }
                }

                // Store CPMK average
                $cpmkAverages[$type][$cpmkNo] = $validCount > 0 ?
                    number_format($totalAvg / $validCount, 2) : 0;
            }

            // Store individual student grades
            $gradesData[$type] = $mahasiswaData;
        }

        // Store data in session
        session()->set('nilai_data', [
            'kelas_id' => $kelasId,
            'grades' => $gradesData,
            'cpmk_averages' => $cpmkAverages
        ]);

        // Return success response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data nilai berhasil disimpan'
        ]);
    }

    public function pelaksanaan_perkuliahan()
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Check if previous data exists in session
        if (!session()->get('assessment_data')) {
            return redirect()->to('/portofolio-form/rancangan-asesmen')
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        return view('backend/portofolio-form/pelaksanaan-perkuliahan');
    }

    // Method untuk halaman edit pelaksanaan perkuliahan
    public function pelaksanaan_perkuliahan_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Check if previous data exists in session
        if (!session()->get('assessment_data')) {
            return redirect()->to('/portofolio-form/rancangan-asesmen-edit/' . $idPorto)
                ->with('error', 'Silakan lengkapi rancangan asesmen terlebih dahulu.');
        }

        // Check if pelaksanaan files exist in session
        if (!session()->get('pelaksanaan_files')) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['pelaksanaan'])) {
                $pelaksanaanFiles = [];
                if ($portofolioData['pelaksanaan']['file_kontrak']) {
                    $pelaksanaanFiles['kontrak_kuliah'] = [
                        'name' => basename($portofolioData['pelaksanaan']['file_kontrak']),
                        'path' => $portofolioData['pelaksanaan']['file_kontrak']
                    ];
                }
                if ($portofolioData['pelaksanaan']['file_realisasi']) {
                    $pelaksanaanFiles['realisasi_mengajar'] = [
                        'name' => basename($portofolioData['pelaksanaan']['file_realisasi']),
                        'path' => $portofolioData['pelaksanaan']['file_realisasi']
                    ];
                }
                if ($portofolioData['pelaksanaan']['file_kehadiran']) {
                    $pelaksanaanFiles['kehadiran_mahasiswa'] = [
                        'name' => basename($portofolioData['pelaksanaan']['file_kehadiran']),
                        'path' => $portofolioData['pelaksanaan']['file_kehadiran']
                    ];
                }
                session()->set('pelaksanaan_files', $pelaksanaanFiles);
            }
        }

        return view('backend/portofolio-form/pelaksanaan-perkuliahan', [
            'idPorto' => $idPorto
        ]);
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

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
        }

        // Check if previous data exists in session
        if (!session()->get('pelaksanaan_files')) {
            return redirect()->to('/portofolio-form/pelaksanaan-perkuliahan')
                ->with('error', 'Silakan lengkapi pelaksanaan perkuliahan terlebih dahulu.');
        }

        return view('backend/portofolio-form/hasil-asesmen');
    }

    // Method untuk halaman edit hasil asesmen
    public function hasil_asesmen_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Check if previous data exists in session
        if (!session()->get('pelaksanaan_files')) {
            return redirect()->to('/portofolio-form/pelaksanaan-perkuliahan-edit/' . $idPorto)
                ->with('error', 'Silakan lengkapi pelaksanaan perkuliahan terlebih dahulu.');
        }

        // Check if hasil asesmen files exist in session
        if (!session()->get('hasil_asesmen_files')) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['hasil_asesmen'])) {
                $hasilAsesmenFiles = [];
                if ($portofolioData['hasil_asesmen']['file_tugas']) {
                    $hasilAsesmenFiles['jawaban_tugas'] = [
                        'name' => basename($portofolioData['hasil_asesmen']['file_tugas']),
                        'path' => $portofolioData['hasil_asesmen']['file_tugas']
                    ];
                }
                if ($portofolioData['hasil_asesmen']['file_uts']) {
                    $hasilAsesmenFiles['jawaban_uts'] = [
                        'name' => basename($portofolioData['hasil_asesmen']['file_uts']),
                        'path' => $portofolioData['hasil_asesmen']['file_uts']
                    ];
                }
                if ($portofolioData['hasil_asesmen']['file_uas']) {
                    $hasilAsesmenFiles['jawaban_uas'] = [
                        'name' => basename($portofolioData['hasil_asesmen']['file_uas']),
                        'path' => $portofolioData['hasil_asesmen']['file_uas']
                    ];
                }
                if ($portofolioData['hasil_asesmen']['file_nilai_mk']) {
                    $hasilAsesmenFiles['nilai_mata_kuliah'] = [
                        'name' => basename($portofolioData['hasil_asesmen']['file_nilai_mk']),
                        'path' => $portofolioData['hasil_asesmen']['file_nilai_mk']
                    ];
                }
                if ($portofolioData['hasil_asesmen']['file_nilai_cpmk']) {
                    $hasilAsesmenFiles['nilai_cpmk'] = [
                        'name' => basename($portofolioData['hasil_asesmen']['file_nilai_cpmk']),
                        'path' => $portofolioData['hasil_asesmen']['file_nilai_cpmk']
                    ];
                }
                session()->set('hasil_asesmen_files', $hasilAsesmenFiles);
            }
        }

        return view('backend/portofolio-form/hasil-asesmen', [
            'idPorto' => $idPorto
        ]);
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

        // Jika dalam mode edit, bersihkan session dan mulai dari awal untuk mencegah konflik data
        if (session()->get('edit_mode')) {
            $this->clearEditMode();
            $this->clearSessionExceptUser();
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
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        return view('backend/portofolio-form/evaluasi-perkuliahan', [
            'evaluasi_perkuliahan' => $evaluasi_perkuliahan,
            'cpmk_data' => $cpmk_data,
            'cpmk_nilai' => $cpmk_nilai,
            'pdfUrl' => $pdfUrl
        ]);
    }

    // Method untuk halaman edit evaluasi perkuliahan
    public function evaluasi_perkuliahan_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan ini mode edit dan ID sesuai
        if (!$this->verifyEditAccess($idPorto)) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Check if previous data exists in session
        if (!session()->get('hasil_asesmen_files')) {
            return redirect()->to('/portofolio-form/hasil-asesmen-edit/' . $idPorto)
                ->with('error', 'Silakan lengkapi hasil asesmen terlebih dahulu.');
        }

        // Ambil data evaluasi dari session
        $evaluasi_perkuliahan = session()->get('evaluasi_perkuliahan') ?? '';

        // Ambil data CPMK dari session
        $cpmk_data = session()->get('cpmk_data')['cpmk'] ?? [];

        // Ambil nilai CPMK yang sudah disimpan (jika ada)
        $cpmk_nilai = session()->get('cpmk_nilai') ?? [];

        // Ambil PDF URL dari session jika ada
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        // Jika belum ada data CPMK dari session, ambil dari database
        if (empty($cpmk_data)) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['cpmk'])) {
                $cpmkArray = [];
                foreach ($portofolioData['cpmk'] as $cpmkId => $cpmk) {
                    $cpmkNo = $cpmk['no_cpmk'] ?? null;
                    if ($cpmkNo !== null) {
                        $cpmkArray[$cpmkNo] = [
                            'id' => $cpmk['id'],
                            'no_cpmk' => $cpmkNo,
                            'narasi' => $cpmk['isi_cpmk'],
                            'avg_cpmk' => $cpmk['avg_cpmk']
                        ];
                    }
                }

                $cpmkData = [
                    'cpmk' => $cpmkArray
                ];

                session()->set('cpmk_data', $cpmkData);
                $cpmk_data = $cpmkArray;
            }
        }

        // Jika belum ada evaluasi dari session, ambil dari database
        if (empty($evaluasi_perkuliahan)) {
            $portofolioModel = new PortofolioModel();
            $portofolioData = $portofolioModel->getPortofolioById($idPorto);

            if ($portofolioData && isset($portofolioData['evaluasi'])) {
                $evaluasi_perkuliahan = $portofolioData['evaluasi']['isi_evaluasi'];
                session()->set('evaluasi_perkuliahan', $evaluasi_perkuliahan);
            }
        }

        return view('backend/portofolio-form/evaluasi-perkuliahan', [
            'evaluasi_perkuliahan' => $evaluasi_perkuliahan,
            'cpmk_data' => $cpmk_data,
            'cpmk_nilai' => $cpmk_nilai,
            'pdfUrl' => $pdfUrl,
            'idPorto' => $idPorto
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
            $cpmk_nilai = $this->request->getPost('cpmk_nilai') ?? [];

            // Simpan ke session
            session()->set('evaluasi_perkuliahan', $evaluasi);
            session()->set('cpmk_nilai', $cpmk_nilai);
            session()->set('current_progress', 'evaluasi_perkuliahan');

            // Simpan semua data yang diperlukan ke dalam database menggunakan direct route savePortofolio
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
            'kelp_mk' => $sessionData['info_matkul']['kelompok_mk'],
            'tahun' => $sessionData['info_matkul']['tahun'],
            'semester' => $sessionData['info_matkul']['semester'],
            'smt_matkul' => $sessionData['info_matkul']['smt_matkul'],
            'npp' => $sessionData['UserSession']['username'],
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

        // Ambil nilai CPMK dari session
        $cpmkNilai = $sessionData['cpmk_nilai'] ?? [];

        foreach ($sessionData['cpmk_data']['cpmk'] as $noCpmk => $cpmk) {
            // Cek apakah ada nilai untuk CPMK ini
            $avgCpmk = null;
            if (isset($cpmkNilai[$noCpmk])) {
                $avgCpmk = floatval($cpmkNilai[$noCpmk]);
            }

            $cpmkData = [
                'id_porto' => $portofolioId,
                'no_cpmk' => $cpmk['no_cpmk'],
                'isi_cpmk' => $cpmk['narasi'],
                'avg_cpmk' => $avgCpmk
            ];
            $cpmkId = $cpmkModel->insert($cpmkData);
            $cpmkMapping[$noCpmk] = $cpmkId;

            // Simpan Sub-CPMK untuk CPMK ini
            foreach ($cpmk['sub'] as $noSubCpmk => $subCpmk) {
                $subCpmkData = [
                    'id_porto' => $portofolioId,
                    'no_scpmk' => $noSubCpmk,
                    'isi_scmpk' => $subCpmk
                ];
                $subCpmkId = $subCpmkModel->insert($subCpmkData);
                $subCpmkMapping[$noSubCpmk] = $subCpmkId;
            }
        }

        // Convert stdClass objects to arrays
        $mappingDataArray = json_decode(json_encode($sessionData['mapping_data']), true);

        // Simpan data ke tabel mapping_cpmk_scpmk menggunakan ID yang benar
        $mappingCpmkScpmkModel = new MappingCpmkScpmkModel();

        // Lookup table to map CPL ID to actual CPMK ID
        $cplToCpmkMap = [];
        foreach ($sessionData['cpmk_data']['cpmk'] as $noCpmk => $cpmk) {
            $selectedCpl = $cpmk['selectedCpl'] ?? $cpmk['selected_cpl'] ?? 1; // Default to 1
            $cplToCpmkMap[$selectedCpl] = $noCpmk;
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

        // Simpan data ke tabel rancangan_asesmen (MODIFIED VERSION)
        $rancanganAsesmenModel = new RancanganAsesmenModel();

        // Get assessment data from session
        $assessmentData = $sessionData['assessment_data'] ?? [];

        // Convert assessment data to array if it's not already an array
        if (is_object($assessmentData)) {
            $assessmentData = json_decode(json_encode($assessmentData), true);
        }

        // Loop through assessment data (CPMK level only)
        foreach ($assessmentData as $sessionCpmkId => $assessmentTypes) {
            // Find the actual CPMK ID from the mapping
            $actualCpmkId = $cpmkMapping[$sessionCpmkId] ?? null;

            if ($actualCpmkId && is_array($assessmentTypes)) {
                // Create one record per CPMK (without subcpmk)
                $rancanganAsesmenData = [
                    'id_porto' => $portofolioId,
                    'id_cpmk' => $actualCpmkId,
                    'id_scpmk' => null, // Set to null since we're not using subcpmk
                    'tugas' => isset($assessmentTypes['tugas']) && $assessmentTypes['tugas'] ? 1 : 0,
                    'uts' => isset($assessmentTypes['uts']) && $assessmentTypes['uts'] ? 1 : 0,
                    'uas' => isset($assessmentTypes['uas']) && $assessmentTypes['uas'] ? 1 : 0
                ];

                $rancanganAsesmenModel->insert($rancanganAsesmenData);
            }
        }

        // Simpan data ke tabel rancangan_soal
        $rancanganSoalModel = new RancanganSoalModel();
        $soalMappingData = $sessionData['soal_mapping_data'] ?? [];

        // Loop through each assessment type (tugas, uts, uas)
        foreach ($soalMappingData as $assessmentType => $soalList) {
            // Determine the category based on assessment type
            $kategoriSoal = '';
            switch ($assessmentType) {
                case 'tugas':
                    $kategoriSoal = 'Tugas';
                    break;
                case 'uts':
                    $kategoriSoal = 'UTS';
                    break;
                case 'uas':
                    $kategoriSoal = 'UAS';
                    break;
            }

            // Loop through each soal in the assessment type
            foreach ($soalList as $soal) {
                $soalNo = $soal['soal_no'];
                $cpmkMappings = $soal['cpmk_mappings'] ?? [];

                // Loop through each CPMK mapping for this soal
                foreach ($cpmkMappings as $sessionCpmkNo => $isChecked) {
                    // Find the actual CPMK ID from the mapping
                    $actualCpmkId = $cpmkMapping[$sessionCpmkNo] ?? null;

                    if ($actualCpmkId) {
                        $rancanganSoalData = [
                            'id_porto' => $portofolioId,
                            'id_cpmk' => $actualCpmkId,
                            'kategori_soal' => $kategoriSoal, // Simpan kategori soal ke field kategori_soal
                            'no_soal' => $soalNo, // Simpan hanya nomor soal tanpa prefix kategori
                            'nilai' => $isChecked ? 1 : 0
                        ];

                        $rancanganSoalModel->insert($rancanganSoalData);
                    }
                }
            }
        }

        // Simpan data ke tabel rancangan_asesmen_file
        $rancanganAsesmenFileModel = new RancanganAsesmenFileModel();
        if (isset($sessionData['assessment_files']) && is_array($sessionData['assessment_files'])) {
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

        // Duplikasi data hasil asesmen (seperti di kode asli)
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

    /**
     * Function to clear edit mode and related session data
     */
    public function clearEditMode()
    {
        $session = session();

        // Remove edit mode specific data
        $session->remove('edit_mode');
        $session->remove('edit_portofolio_id');
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        // Juga hapus mode edit jika ada
        $this->clearEditMode();
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }

    public function tes_cetak()
    {
        return view('backend/pdf/test-cetak');
    }

    // Method untuk edit portofolio
    public function edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $portofolioModel = new PortofolioModel();
        $portofolioData = $portofolioModel->getPortofolioForEdit($idPorto);

        if (!$portofolioData) {
            return redirect()->back()->with('error', 'Data portofolio tidak ditemukan.');
        }

        // Hapus semua session data yang terkait dengan proses tambah portofolio sebelumnya
        $this->clearSessionExceptUser();

        // Tandai bahwa ini mode edit
        session()->set('edit_mode', true);
        session()->set('edit_portofolio_id', $idPorto);

        // Load data ke session untuk digunakan di form
        foreach ($portofolioData as $key => $value) {
            session()->set($key, $value);
        }

        // Redirect ke halaman pertama edit (info_matkul)
        return redirect()->to('portofolio-form/info-matkul-edit/' . $idPorto);
    }

    // Method untuk menangani update portofolio
    public function update($idPorto)
    {
        $session = session();
        $sessionData = $session->get();

        // Update data ke tabel portofolio
        $portofolioModel = new PortofolioModel();
        $portofolioData = [
            'kode_mk' => $sessionData['info_matkul']['kode_mk'],
            'nama_mk' => $sessionData['info_matkul']['nama_mk'],
            'tahun' => $sessionData['info_matkul']['tahun'],
            'semester' => $sessionData['info_matkul']['semester'],
            'smt_matkul' => $sessionData['info_matkul']['smt_matkul'],
        ];
        $portofolioModel->update($idPorto, $portofolioData);

        // Update data RPS jika ada
        $rpsModel = new RpsModel();
        $rpsData = $rpsModel->where('id_porto', $idPorto)->first();
        if ($rpsData) {
            $rpsModel->update($rpsData['id'], [
                'file_rps' => $sessionData['uploaded_rps']
            ]);
        } else {
            // Insert jika belum ada
            $rpsModel->insert([
                'id_porto' => $idPorto,
                'file_rps' => $sessionData['uploaded_rps']
            ]);
        }

        // Update data identitas matkul
        $identitasMatkulModel = new IdentitasMatkulModel();
        $identitasData = $identitasMatkulModel->where('id_porto', $idPorto)->first();
        if ($identitasData) {
            $identitasMatkulModel->update($identitasData['id'], [
                'prasyarat_mk' => $sessionData['info_matkul']['mk_prasyarat'],
                'topik_perkuliahan' => $sessionData['info_matkul']['topik_mk']
            ]);
        } else {
            // Insert jika belum ada
            $identitasMatkulModel->insert([
                'id_porto' => $idPorto,
                'prasyarat_mk' => $sessionData['info_matkul']['mk_prasyarat'],
                'topik_perkuliahan' => $sessionData['info_matkul']['topik_mk']
            ]);
        }

        // Hapus dan simpan ulang data CPL
        $cplModel = new CplModel();
        $piModel = new PiModel();
        // Hapus data lama
        $existingCpl = $cplModel->where('id_porto', $idPorto)->findAll();
        foreach ($existingCpl as $cpl) {
            $piModel->where('id_cpl', $cpl['id'])->delete();
        }
        $cplModel->where('id_porto', $idPorto)->delete();

        // Simpan data CPL dan PI baru
        foreach ($sessionData['cpl_pi_data'] as $noCpl => $cpl) {
            $cplData = [
                'id_porto' => $idPorto,
                'no_cpl' => $noCpl,
                'isi_cpl' => $cpl['cpl_indo']
            ];
            $cplId = $cplModel->insert($cplData);

            $piCounter = 1;
            foreach ($cpl['pi_list'] as $pi) {
                if ($pi !== "\N") {
                    $piData = [
                        'id_cpl' => $cplId,
                        'no_pi' => $piCounter,
                        'isi_ikcp' => $pi
                    ];
                    $piModel->insert($piData);
                    $piCounter++;
                }
            }
        }

        // Hapus dan simpan ulang data CPMK dan Sub-CPMK
        $cpmkModel = new CpmkModel();
        $subCpmkModel = new SubCpmkModel();
        $mappingCpmkScpmkModel = new MappingCpmkScpmkModel();
        // Hapus data lama
        $existingCpmk = $cpmkModel->where('id_porto', $idPorto)->findAll();
        foreach ($existingCpmk as $cpmk) {
            $subCpmkModel->where('id_porto', $idPorto)->delete();
        }
        $cpmkModel->where('id_porto', $idPorto)->delete();
        $mappingCpmkScpmkModel->where('id_porto', $idPorto)->delete(); // Perlu join untuk mapping

        // Simpan data CPMK dan Sub-CPMK baru
        $cpmkMapping = []; // Untuk menyimpan mapping antara no_cpmk dan ID database
        $subCpmkMapping = []; // Untuk menyimpan mapping antara no_scpmk dan ID database

        foreach ($sessionData['cpmk_data']['cpmk'] as $noCpmk => $cpmk) {
            $cpmkData = [
                'id_porto' => $idPorto,
                'no_cpmk' => $cpmk['no_cpmk'],
                'isi_cpmk' => $cpmk['narasi'],
                'avg_cpmk' => $cpmk['avg_cpmk'] ?? null
            ];
            $cpmkId = $cpmkModel->insert($cpmkData);
            $cpmkMapping[$noCpmk] = $cpmkId;

            foreach ($cpmk['sub'] as $noSubCpmk => $subCpmk) {
                $subCpmkData = [
                    'id_porto' => $idPorto,
                    'no_scpmk' => $noSubCpmk,
                    'isi_scmpk' => $subCpmk
                ];
                $subCpmkId = $subCpmkModel->insert($subCpmkData);
                $subCpmkMapping[$noSubCpmk] = $subCpmkId;
            }
        }

        // Simpan data mapping CPMK-SubCPMK
        $mappingCpmkScpmkModel = new MappingCpmkScpmkModel();
        $mappingData = $sessionData['mapping_data'] ?? [];
        foreach ($mappingData as $cplId => $cpmkMappings) {
            foreach ($cpmkMappings as $innerKey => $subCpmkValues) {
                foreach ($subCpmkValues as $sessionScpmkId => $nilai) {
                    $actualCpmkId = $cpmkMapping[$innerKey] ?? null;
                    $actualScpmkId = $subCpmkMapping[$sessionScpmkId] ?? null;

                    if ($actualCpmkId && $actualScpmkId && $nilai == 1) {
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

        // Update data rancangan asesmen
        $rancanganAsesmenModel = new RancanganAsesmenModel();
        // Hapus data lama
        $rancanganAsesmenModel->where('id_porto', $idPorto)->delete();

        // Simpan data baru
        $assessmentData = $sessionData['assessment_data'] ?? [];
        foreach ($assessmentData as $sessionCpmkId => $assessmentTypes) {
            $actualCpmkId = $cpmkMapping[$sessionCpmkId] ?? null;
            if ($actualCpmkId && is_array($assessmentTypes)) {
                $rancanganAsesmenData = [
                    'id_porto' => $idPorto,
                    'id_cpmk' => $actualCpmkId,
                    'id_scpmk' => null,
                    'tugas' => isset($assessmentTypes['tugas']) && $assessmentTypes['tugas'] ? 1 : 0,
                    'uts' => isset($assessmentTypes['uts']) && $assessmentTypes['uts'] ? 1 : 0,
                    'uas' => isset($assessmentTypes['uas']) && $assessmentTypes['uas'] ? 1 : 0
                ];
                $rancanganAsesmenModel->insert($rancanganAsesmenData);
            }
        }

        // Update data rancangan soal
        $rancanganSoalModel = new RancanganSoalModel();
        // Hapus data lama
        $rancanganSoalModel->where('id_porto', $idPorto)->delete();

        // Simpan data baru
        $soalMappingData = $sessionData['soal_mapping_data'] ?? [];
        foreach ($soalMappingData as $assessmentType => $soalList) {
            $kategoriSoal = '';
            switch ($assessmentType) {
                case 'tugas':
                    $kategoriSoal = 'Tugas';
                    break;
                case 'uts':
                    $kategoriSoal = 'UTS';
                    break;
                case 'uas':
                    $kategoriSoal = 'UAS';
                    break;
            }

            foreach ($soalList as $soal) {
                $soalNo = $soal['soal_no'];
                $cpmkMappings = $soal['cpmk_mappings'] ?? [];

                foreach ($cpmkMappings as $sessionCpmkNo => $isChecked) {
                    $actualCpmkId = $cpmkMapping[$sessionCpmkNo] ?? null;

                    if ($actualCpmkId) {
                        $rancanganSoalData = [
                            'id_porto' => $idPorto,
                            'id_cpmk' => $actualCpmkId,
                            'kategori_soal' => $kategoriSoal,
                            'no_soal' => $soalNo,
                            'nilai' => $isChecked ? 1 : 0
                        ];

                        $rancanganSoalModel->insert($rancanganSoalData);
                    }
                }
            }
        }

        // Update data rancangan asesmen file
        $rancanganAsesmenFileModel = new RancanganAsesmenFileModel();
        // Hapus data lama
        $rancanganAsesmenFileModel->where('id_porto', $idPorto)->delete();

        // Simpan data baru
        if (isset($sessionData['assessment_files']) && is_array($sessionData['assessment_files'])) {
            foreach ($sessionData['assessment_files'] as $kategori => $file) {
                $kategoriFile = '';
                $kategoriAsesmen = '';

                if (strpos($kategori, 'soal_') === 0) {
                    $kategoriFile = 'Soal';
                } elseif (strpos($kategori, 'rubrik_') === 0) {
                    $kategoriFile = 'Rubrik';
                } else {
                    $kategoriFile = 'Lainnya';
                }

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
                    'id_porto' => $idPorto,
                    'kategori' => $kategoriAsesmen,
                    'kategori_file' => $kategoriFile,
                    'file_pdf' => $file['path']
                ];
                $rancanganAsesmenFileModel->insert($rancanganAsesmenFileData);
            }
        }

        // Update data pelaksanaan perkuliahan
        $pelaksanaanModel = new PelaksanaanPerkuliahanModel();
        $pelaksanaanData = $pelaksanaanModel->where('id_porto', $idPorto)->first();
        if ($pelaksanaanData) {
            $pelaksanaanModel->update($pelaksanaanData['id'], [
                'file_kontrak' => isset($sessionData['pelaksanaan_files']['kontrak_kuliah']) ? $sessionData['pelaksanaan_files']['kontrak_kuliah']['path'] : null,
                'file_realisasi' => isset($sessionData['pelaksanaan_files']['realisasi_mengajar']) ? $sessionData['pelaksanaan_files']['realisasi_mengajar']['path'] : null,
                'file_kehadiran' => isset($sessionData['pelaksanaan_files']['kehadiran_mahasiswa']) ? $sessionData['pelaksanaan_files']['kehadiran_mahasiswa']['path'] : null
            ]);
        } else {
            $pelaksanaanModel->insert([
                'id_porto' => $idPorto,
                'file_kontrak' => isset($sessionData['pelaksanaan_files']['kontrak_kuliah']) ? $sessionData['pelaksanaan_files']['kontrak_kuliah']['path'] : null,
                'file_realisasi' => isset($sessionData['pelaksanaan_files']['realisasi_mengajar']) ? $sessionData['pelaksanaan_files']['realisasi_mengajar']['path'] : null,
                'file_kehadiran' => isset($sessionData['pelaksanaan_files']['kehadiran_mahasiswa']) ? $sessionData['pelaksanaan_files']['kehadiran_mahasiswa']['path'] : null
            ]);
        }

        // Update data hasil asesmen
        $hasilAsesmenModel = new HasilAsesmenModel();
        $hasilAsesmenData = $hasilAsesmenModel->where('id_porto', $idPorto)->first();
        if ($hasilAsesmenData) {
            $hasilAsesmenModel->update($hasilAsesmenData['id'], [
                'file_tugas' => isset($sessionData['hasil_asesmen_files']['jawaban_tugas']) ? $sessionData['hasil_asesmen_files']['jawaban_tugas']['path'] : null,
                'file_uts' => isset($sessionData['hasil_asesmen_files']['jawaban_uts']) ? $sessionData['hasil_asesmen_files']['jawaban_uts']['path'] : null,
                'file_uas' => isset($sessionData['hasil_asesmen_files']['jawaban_uas']) ? $sessionData['hasil_asesmen_files']['jawaban_uas']['path'] : null,
                'file_nilai_mk' => isset($sessionData['hasil_asesmen_files']['nilai_mata_kuliah']) ? $sessionData['hasil_asesmen_files']['nilai_mata_kuliah']['path'] : null,
                'file_nilai_cpmk' => isset($sessionData['hasil_asesmen_files']['nilai_cpmk']) ? $sessionData['hasil_asesmen_files']['nilai_cpmk']['path'] : null
            ]);
        } else {
            $hasilAsesmenModel->insert([
                'id_porto' => $idPorto,
                'file_tugas' => isset($sessionData['hasil_asesmen_files']['jawaban_tugas']) ? $sessionData['hasil_asesmen_files']['jawaban_tugas']['path'] : null,
                'file_uts' => isset($sessionData['hasil_asesmen_files']['jawaban_uts']) ? $sessionData['hasil_asesmen_files']['jawaban_uts']['path'] : null,
                'file_uas' => isset($sessionData['hasil_asesmen_files']['jawaban_uas']) ? $sessionData['hasil_asesmen_files']['jawaban_uas']['path'] : null,
                'file_nilai_mk' => isset($sessionData['hasil_asesmen_files']['nilai_mata_kuliah']) ? $sessionData['hasil_asesmen_files']['nilai_mata_kuliah']['path'] : null,
                'file_nilai_cpmk' => isset($sessionData['hasil_asesmen_files']['nilai_cpmk']) ? $sessionData['hasil_asesmen_files']['nilai_cpmk']['path'] : null
            ]);
        }

        // Update data evaluasi perkuliahan
        $evaluasiPerkuliahanModel = new EvaluasiPerkuliahanModel();
        $evaluasiData = $evaluasiPerkuliahanModel->where('id_porto', $idPorto)->first();
        if ($evaluasiData) {
            $evaluasiPerkuliahanModel->update($evaluasiData['id'], [
                'isi_evaluasi' => $sessionData['evaluasi_perkuliahan']
            ]);
        } else {
            $evaluasiPerkuliahanModel->insert([
                'id_porto' => $idPorto,
                'isi_evaluasi' => $sessionData['evaluasi_perkuliahan']
            ]);
        }

        // Clear session data except user session
        $this->clearSessionExceptUser();

        // Hapus mode edit dari session
        session()->remove('edit_mode');
        session()->remove('edit_portofolio_id');

        // Redirect ke halaman daftar portofolio dengan informasi MK
        $portofolio = $portofolioModel->find($idPorto);
        if ($portofolio) {
            return redirect()->to('/portofolio-form/daftar/' . $portofolio['kode_mk'] . '/' . $portofolio['tahun'] . '/' . $portofolio['semester'])
                ->with('success', 'Portofolio berhasil diperbarui.');
        } else {
            return redirect()->to('/portofolio-form')->with('success', 'Portofolio berhasil diperbarui.');
        }
    }

    // Method untuk halaman edit info matkul
    public function info_matkul_edit($idPorto)
    {
        if (!session()->get('UserSession.logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan ini mode edit dan ID sesuai
        if (!session()->get('edit_mode') || session()->get('edit_portofolio_id') != $idPorto) {
            return redirect()->to('/portofolio-form')->with('error', 'Akses tidak sah.');
        }

        // Get data from database from info_matkul table
        $db = \Config\Database::connect();
        $mataKuliahData = $db->table('info_matkul im')
            ->select('
        im.matakuliah AS nama_mk,
        im.kode_matkul AS kode_mk,
        md.kelp_matkul AS kelompok_mk,
        im.fakultas,
        im.smt_matkul,
        im.prodi AS progdi,
        im.teori AS sks_teori,
        im.praktek AS sks_praktik,
        md.tahun,
        md.semester,
        md.dosen
    ')
            ->join('matkul_diampu md', 'md.kode_matkul = im.kode_matkul', 'left')
            ->groupBy([
                'im.kode_matkul',
                'im.matakuliah',
                'md.kelp_matkul',
                'im.smt_matkul',
                'im.fakultas',
                'im.prodi',
                'im.teori',
                'im.praktek',
                'md.tahun',
                'md.semester',
                'md.dosen'
            ])
            ->orderBy('im.matakuliah', 'ASC')
            ->get()
            ->getResultArray();

        // Data tambahan dari session (jika ada)
        $infoMatkul = session()->get('info_matkul') ?? [];

        // Cek apakah ada file yang disimpan di session
        $pdfUrl = session()->get('uploaded_rps') ? base_url('uploads/rps/' . session()->get('uploaded_rps')) : '';

        // Kirim data ke view dengan menambahkan ID portofolio
        return view('backend/portofolio-form/info-matkul', [
            'mataKuliah' => $mataKuliahData,
            'infoMatkul' => $infoMatkul,
            'pdfUrl' => $pdfUrl,
            'idPorto' => $idPorto // Tambahkan ID portofolio untuk edit
        ]);
    }

    /**
     * Fungsi untuk membersihkan session yang sudah kadaluarsa (30 menit tidak aktif)
     */
    private function cleanupInactiveSession()
    {
        // Ambil waktu terakhir aktivitas dari session
        $lastActivity = session()->get('last_activity');

        // Jika tidak ada session aktif, tidak perlu membersihkan
        if (!$lastActivity) {
            return;
        }

        // Hitung selisih waktu
        $timeDiff = time() - $lastActivity;

        // Jika lebih dari 30 menit (1800 detik), hapus session terkait
        if ($timeDiff > 1800) { // 30 menit = 30 * 60 detik
            // Hapus semua session terkait portofolio
            $sessionKeys = [
                'info_matkul',
                'cpl_pi_data',
                'cpmk_data',
                'mapping_data',
                'assessment_data',
                'assessment_files',
                'soal_mapping',
                'nilai_soal',
                'pelaksanaan_perkuliahan',
                'hasil_asesmen',
                'evaluasi_perkuliahan',
                'uploaded_rps'
            ];

            foreach ($sessionKeys as $key) {
                session()->remove($key);
            }

            log_message('info', 'Session yang tidak aktif telah dihapus');
        }
    }

    /**
     * Fungsi untuk memperbarui waktu terakhir aktivitas
     */
    private function updateLastActivity()
    {
        session()->set('last_activity', time());
    }
}
