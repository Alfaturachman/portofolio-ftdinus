<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PortofolioModel;

class Portofolio extends BaseController
{
    public function index()
    {
        $npp = session()->get('npp');

        log_message('debug', 'NPP SESSION: ' . $npp);

        $portofolioModel = new PortofolioModel();
        $data['portofolios'] = $portofolioModel->getPortofolioByNpp($npp);

        log_message('debug', 'TOTAL DATA: ' . count($data['portofolios']));

        return view('admin/portofolio/index', $data);
    }


    /**
     * Form add (new portofolio).
     * Creates a new portofolio record and redirects to form step 1.
     */
    public function add($id_perkuliahan)
    {
        $portofolioModel = new PortofolioModel();

        // Cek existing
        $existing = $portofolioModel->findByPerkuliahan($id_perkuliahan);

        if ($existing) {
            return redirect()->to(
                base_url('admin/portofolio/form/' . $existing['id'])
            );
        }

        // Buat baru lewat model
        $id_portofolio = $portofolioModel->createPortofolio($id_perkuliahan);

        return redirect()->to(
            base_url('admin/portofolio/form/' . $id_portofolio)
        );
    }

    /**
     * Show the multi-step form, resume at last_step.
     */
    public function form(string $id)
    {
        $npp = session()->get('npp');
        $model = new PortofolioModel();

        $data = $model->getFormData($id);

        if (!$data || $data['porto']['id_users'] !== $npp) {
            return redirect()->to(base_url('admin/portofolio'))
                ->with('error', 'Portofolio tidak ditemukan.');
        }

        log_message('debug', 'Step 5: Loaded ' . count($data['mapping']) . ' mappings for id_portofolio=' . $id);

        return view('admin/portofolio/form', $data);
    }

    // ══════════════════════════════════════════════════════
    //  STEP SAVERS  (called via AJAX POST, return JSON)
    // ══════════════════════════════════════════════════════

    /**
     * STEP 1 – Upload RPS
     * POST /admin/portofolio/step/rps
     * Multipart: id_portofolio, file_rps
     */
    public function saveRPS()
    {
        $db = \Config\Database::connect();
        $id = (string) $this->request->getPost('id_portofolio');

        $file = $this->request->getFile('file_rps');
        if (! $file || ! $file->isValid()) {
            return $this->_json(['status' => 'error', 'message' => 'File RPS tidak valid.']);
        }

        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (! in_array($file->getMimeType(), $allowedTypes)) {
            return $this->_json(['status' => 'error', 'message' => 'Format file tidak diizinkan. Gunakan PDF/DOC/DOCX.']);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return $this->_json(['status' => 'error', 'message' => 'Ukuran file maksimal 10 MB.']);
        }

        $newName = 'rps_' . $id . '_' . time() . '.' . $file->getExtension();
        $file->move(WRITEPATH . 'uploads/rps/', $newName);

        $existing = $db->table('rps')->where('id_portofolio', $id)->get()->getRowArray();
        if ($existing) {
            // Delete old file
            @unlink(WRITEPATH . 'uploads/rps/' . $existing['file_rps']);
            $db->table('rps')->where('id_portofolio', $id)->update([
                'file_rps'   => $newName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('rps')->insert([
                'id_portofolio' => $id,
                'file_rps'      => $newName,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $this->_updateLastStep($id, 1);

        return $this->_json(['status' => 'success', 'message' => 'RPS berhasil disimpan.', 'file' => $newName]);
    }

    // Server-side file serving for RPS (to ensure only owner can access)
    public function serveRPS(string $filename)
    {
        $db  = \Config\Database::connect();
        $npp = session()->get('npp');

        $path = WRITEPATH . 'uploads/rps/' . $filename;

        // Pastikan file milik user yang login
        $row = $db->table('rps r')
            ->select('r.file_rps')
            ->join('portofolio p', 'p.id = r.id_portofolio')
            ->join('perkuliahan per', 'per.id = p.id_perkuliahan')
            ->where('r.file_rps', $filename)
            ->where('per.id_users', $npp)
            ->get()
            ->getRowArray();

        if (!$row || !is_file($path)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File tidak ditemukan.');
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($path));
    }

    // Server-side file serving for asesmen files
    public function serveAsesmen(string $filename)
    {
        $db  = \Config\Database::connect();
        $npp = session()->get('npp');

        $path = WRITEPATH . 'uploads/asesmen/' . $filename;

        // Pastikan file milik user yang login
        $row = $db->table('rancangan_asesmen ra')
            ->select('ra.file_soal, ra.file_rubrik')
            ->join('portofolio p', 'p.id = ra.id_portofolio')
            ->join('perkuliahan per', 'per.id = p.id_perkuliahan')
            ->where('(ra.file_soal = "' . $db->escape($filename) . '" OR ra.file_rubrik = "' . $db->escape($filename) . '")')
            ->where('per.id_users', $npp)
            ->get()
            ->getRowArray();

        if (!$row || !is_file($path)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File tidak ditemukan.');
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($path));
    }

    /**
     * STEP 2 – Informasi Mata Kuliah
     * POST /admin/portofolio/step/info-mk
     * Body: id_portofolio, mk_prasyarat, topik_perkuliahan
     */
    public function saveInfoMK()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id   = (string) ($json['id_portofolio'] ?? '');

        if (empty($id)) {
            return $this->_json(['status' => 'error', 'message' => 'ID Portofolio tidak valid.']);
        }

        $mk_prasyarat      = $json['mk_prasyarat'] ?? '';
        $topik_perkuliahan = $json['topik_perkuliahan'] ?? '';

        if (empty(trim($topik_perkuliahan))) {
            return $this->_json(['status' => 'error', 'message' => 'Topik perkuliahan wajib diisi.']);
        }

        $existing = $db->table('informasi_mk')->where('id_portofolio', $id)->get()->getRowArray();
        $payload  = [
            'mk_prasyarat'      => $mk_prasyarat,
            'topik_perkuliahan' => $topik_perkuliahan,
        ];

        if ($existing) {
            $db->table('informasi_mk')->where('id_portofolio', $id)->update($payload);
        } else {
            $db->table('informasi_mk')->insert(array_merge(['id_portofolio' => $id], $payload));
        }

        $this->_updateLastStep($id, 2);

        return $this->_json(['status' => 'success', 'message' => 'Informasi MK berhasil disimpan.']);
    }

    /**
     * STEP 3 – CPL & PI  (read-only display, nothing to save; just advance step)
     * POST /admin/portofolio/step/cpl
     */
    public function saveCPL()
    {
        $id = (int) $this->request->getPost('id_portofolio');

        $this->_updateLastStep($id, 3);

        return $this->_json(['status' => 'success', 'message' => 'Step 3 dicatat.']);
    }

    /**
     * STEP 4 – CPMK & Sub CPMK
     * POST /admin/portofolio/step/cpmk
     * Body (JSON): id_portofolio, cpmk_list (array of {id_cpl, no_cpmk, narasi_cpmk, subs[]})
     */
    public function saveCPMK()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id   = (string) ($json['id_portofolio'] ?? '');

        if (empty($id)) {
            return $this->_json(['status' => 'error', 'message' => 'ID Portofolio tidak valid.']);
        }

        $cpmkList = $json['cpmk_list'] ?? [];
        if (empty($cpmkList)) {
            return $this->_json(['status' => 'error', 'message' => 'Minimal satu CPMK harus diisi.']);
        }

        // ── 1. Hapus data lama ────────────────────────────────────────────
        $existingCpmks = $db->table('cpmk')
            ->select('id')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        foreach ($existingCpmks as $row) {
            $db->table('sub_cpmk')->where('id_cpmk', $row['id'])->delete();
        }
        $db->table('mapping_cpl_cpmk_scpmk')->where('id_portofolio', $id)->delete();
        $db->table('cpmk')->where('id_portofolio', $id)->delete();

        // ── 2. Insert CPMK + Sub CPMK + Mapping sekaligus ────────────────
        $savedCpmks = [];

        foreach ($cpmkList as $c) {
            // Insert CPMK
            $db->table('cpmk')->insert([
                'id_portofolio' => $id,
                'no_cpmk'       => 'CPMK-' . str_pad($c['no'], 2, '0', STR_PAD_LEFT),
                'id_cpl'        => (int) $c['id_cpl'],
                'narasi_cpmk'   => trim($c['narasi']),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $cpmk_id = (int) $db->insertID();

            // Insert Sub CPMK
            $subs = [];
            foreach (($c['subs'] ?? []) as $s) {
                $db->table('sub_cpmk')->insert([
                    'id_cpmk'         => $cpmk_id,
                    'no_sub_cpmk'     => 'Sub-' . str_pad($s['no'], 2, '0', STR_PAD_LEFT),
                    'narasi_sub_cpmk' => trim($s['narasi']),
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $sub_id = (int) $db->insertID();

                // Insert Mapping CPL → CPMK → Sub CPMK langsung di sini
                $db->table('mapping_cpl_cpmk_scpmk')->insert([
                    'id_portofolio' => $id,
                    'id_cpl'        => (int) $c['id_cpl'],
                    'id_cpmk'       => $cpmk_id,
                    'id_sub_cpmk'   => $sub_id,
                ]);

                $subs[] = [
                    'id'     => $sub_id,
                    'no'     => (int) $s['no'],
                    'narasi' => trim($s['narasi']),
                ];
            }

            $savedCpmks[] = [
                'id'     => $cpmk_id,
                'no'     => (int) $c['no'],
                'narasi' => trim($c['narasi']),
                'subs'   => $subs,
            ];
        }

        // ── 3. Advance last_step ─────────────────────────────────────────
        $this->_updateLastStep($id, 4);

        return $this->_json([
            'status'  => 'success',
            'message' => 'CPMK, Sub CPMK, dan Pemetaan berhasil disimpan.',
            'cpmks'   => $savedCpmks,
        ]);
    }

    /**
     * STEP 5 – Pemetaan CPL-CPMK-SubCPMK
     * POST /admin/portofolio/step/mapping
     * Body (JSON): id_portofolio, mappings (array of {id_cpl, id_cpmk, id_sub_cpmk})
     */
    public function saveMapping()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);

        log_message('debug', 'saveMapping received: ' . json_encode($json));

        $id = (string) ($json['id_portofolio'] ?? '');

        if (empty($id)) {
            log_message('error', 'saveMapping: ID Portofolio kosong');
            return $this->_json(['status' => 'error', 'message' => 'ID Portofolio tidak valid.']);
        }

        $mappings = $json['mappings'] ?? [];
        if (empty($mappings)) {
            log_message('error', 'saveMapping: Mappings kosong untuk id_portofolio=' . $id);
            return $this->_json(['status' => 'error', 'message' => 'Minimal satu pemetaan harus dipilih.']);
        }

        // ── 1. Validasi portofolio milik user yang login ──────────────────────
        $npp = session()->get('npp');
        $portoExists = $db->table('portofolio p')
            ->join('perkuliahan per', 'per.id = p.id_perkuliahan')
            ->where('p.id', $id)
            ->where('per.id_users', $npp)
            ->countAllResults();

        if (!$portoExists) {
            return $this->_json(['status' => 'error', 'message' => 'Portofolio tidak ditemukan atau Anda tidak memiliki akses.']);
        }

        // ── 2. Hapus data pemetaan lama ───────────────────────────────────────
        $db->table('pemetaan')->where('id_portofolio', $id)->delete();

        // ── 3. Insert pemetaan baru ───────────────────────────────────────────
        $now      = date('Y-m-d H:i:s');
        $inserted = 0;
        $errors   = [];

        foreach ($mappings as $idx => $map) {
            // Validasi field wajib
            if (
                !isset($map['id_cpl'])      || empty($map['id_cpl']) ||
                !isset($map['id_cpmk'])     || empty($map['id_cpmk']) ||
                !isset($map['id_sub_cpmk']) || empty($map['id_sub_cpmk'])
            ) {
                $errors[] = "Mapping ke-{$idx} tidak lengkap";
                log_message('warning', "saveMapping: mapping ke-{$idx} tidak lengkap");
                continue;
            }

            $rowData = [
                'id_portofolio' => $id,
                'id_cpl'        => (int) $map['id_cpl'],
                'id_cpmk'       => (int) $map['id_cpmk'],
                'id_sub_cpmk'   => (int) $map['id_sub_cpmk'],
                'is_active'     => 1,
                'created_at'    => $now,
            ];

            $result = $db->table('pemetaan')->insert($rowData);

            if ($result === false) {
                $dbError = $db->error();
                $errMsg = "Gagal insert mapping ke-{$idx}: " . json_encode($dbError);
                $errors[] = $errMsg;
                log_message('error', 'saveMapping: ' . $errMsg);
            } else {
                $inserted++;
            }
        }

        // ── 4. Tentukan response berdasarkan hasil insert ─────────────────────
        if ($inserted === 0) {
            log_message('error', 'saveMapping: tidak ada data berhasil diinsert. Errors: ' . implode('; ', $errors));
            return $this->_json([
                'status'  => 'error',
                'message' => 'Pemetaan gagal disimpan. ' . ($errors ? implode(', ', $errors) : 'Cek log untuk detail.'),
            ]);
        }

        if (!empty($errors)) {
            log_message('warning', 'saveMapping partial errors: ' . implode('; ', $errors));
        }

        $this->_updateLastStep($id, 5);

        return $this->_json([
            'status'   => 'success',
            'message'  => "Pemetaan berhasil disimpan. ({$inserted} data)",
            'inserted' => $inserted,
        ]);
    }

    /**
     * STEP 6 – Rancangan Asesmen
     * POST /admin/portofolio/step/asesmen  (multipart – may include files)
     * Fields: id_portofolio,
     *         asesmen_cpmk[]  => array of {id_cpmk, jenis_asesmen}
     *         file_soal_tugas, file_rubrik_tugas, file_soal_uts, file_rubrik_uts,
     *         file_soal_uas,  file_rubrik_uas
     */
    public function saveAsesmen()
    {
        $db = \Config\Database::connect();
        $id = (string) $this->request->getPost('id_portofolio');

        if (empty($id)) {
            return $this->_json(['status' => 'error', 'message' => 'ID Portofolio tidak valid.']);
        }

        $asesmenData = json_decode($this->request->getPost('asesmen_data'), true) ?? [];

        // Ambil data existing untuk fallback file lama
        $existing = [];
        foreach ($db->table('rancangan_asesmen')->where('id_portofolio', $id)->get()->getResultArray() as $row) {
            $existing[$row['jenis_asesmen']] = $row;
        }

        // ── Upload file SEKALI per jenis (di luar loop CPMK) ──────────────
        $uploadedFiles = []; // ['tugas' => ['soal' => 'nama_file', 'rubrik' => 'nama_file'], ...]

        $fileMap = [
            'tugas' => ['soal' => 'file_soal_tugas', 'rubrik' => 'file_rubrik_tugas'],
            'uts'   => ['soal' => 'file_soal_uts',   'rubrik' => 'file_rubrik_uts'],
            'uas'   => ['soal' => 'file_soal_uas',   'rubrik' => 'file_rubrik_uas'],
        ];

        foreach ($fileMap as $jenis => $fields) {
            $uploadedFiles[$jenis] = [
                'soal'   => $existing[$jenis]['file_soal']   ?? null, // default: file lama
                'rubrik' => $existing[$jenis]['file_rubrik'] ?? null,
            ];

            // Upload soal jika ada file baru
            $soalFile = $this->request->getFile($fields['soal']);
            if ($soalFile && $soalFile->isValid() && !$soalFile->hasMoved()) {
                $nm = 'soal_' . $id . '_' . $jenis . '_' . time() . '.' . $soalFile->getExtension();
                $soalFile->move(WRITEPATH . 'uploads/asesmen/', $nm);
                $uploadedFiles[$jenis]['soal'] = $nm;
            }

            // Upload rubrik jika ada file baru
            $rubrikFile = $this->request->getFile($fields['rubrik']);
            if ($rubrikFile && $rubrikFile->isValid() && !$rubrikFile->hasMoved()) {
                $nm = 'rubrik_' . $id . '_' . $jenis . '_' . time() . '.' . $rubrikFile->getExtension();
                $rubrikFile->move(WRITEPATH . 'uploads/asesmen/', $nm);
                $uploadedFiles[$jenis]['rubrik'] = $nm;
            }
        }

        // ── Hapus data lama & insert baru ─────────────────────────────────
        $db->table('rancangan_asesmen')->where('id_portofolio', $id)->delete();

        foreach ($asesmenData as $row) {
            $jenis   = $row['jenis_asesmen'];
            $id_cpmk = (int) $row['id_cpmk'];

            $db->table('rancangan_asesmen')->insert([
                'id_portofolio' => $id,
                'id_cpmk'       => $id_cpmk,
                'jenis_asesmen' => $jenis,
                'file_soal'     => $uploadedFiles[$jenis]['soal']   ?? null,
                'file_rubrik'   => $uploadedFiles[$jenis]['rubrik'] ?? null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // ── Ambil data tersimpan untuk response ───────────────────────────
        $savedAsesmen = $db->table('rancangan_asesmen')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        $this->_updateLastStep($id, 6);

        return $this->_json([
            'status'  => 'success',
            'message' => 'Rancangan asesmen berhasil disimpan.',
            'asesmen' => array_map(fn($a) => [
                'id'            => $a['id'],
                'id_cpmk'       => $a['id_cpmk'],
                'jenis_asesmen' => $a['jenis_asesmen'],
                'file_soal'     => $a['file_soal'],
                'file_rubrik'   => $a['file_rubrik'],
            ], $savedAsesmen),
        ]);
    }

    public function previewAsesmen($fileName)
    {
        $path = WRITEPATH . 'uploads/asesmen/' . $fileName;

        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setBody(file_get_contents($path));
    }

    /**
     * STEP 7 – Rancangan Soal
     * POST /admin/portofolio/step/soal
     * Body (JSON): id_portofolio, soal_list (array of {id_asesmen, id_cpmk, nomor_soal})
     */
    public function saveSoal()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id = (string) ($json['id_portofolio'] ?? 0);

        // Hapus data lama
        $db->table('rancangan_soal')->where('id_portofolio', $id)->delete();

        $soalList = $json['soal_list'] ?? [];
        $groupedSoals = [];

        // Kelompokkan berdasarkan id_asesmen, id_cpmk, dan nomor_soal
        foreach ($soalList as $soal) {
            $key = $soal['id_asesmen'] . '_' . $soal['id_cpmk'] . '_' . $soal['nomor_soal'];
            if (!isset($groupedSoals[$key])) {
                $groupedSoals[$key] = [
                    'id_portofolio' => $id,
                    'id_asesmen' => $soal['id_asesmen'],
                    'id_cpmk' => $soal['id_cpmk'],
                    'nomor_soal' => $soal['nomor_soal']
                ];
            }
        }

        // Insert ke rancangan_soal - setiap soal terkait dengan satu CPMK
        foreach ($groupedSoals as $item) {
            $db->table('rancangan_soal')->insert([
                'id_portofolio' => $item['id_portofolio'],
                'id_asesmen' => $item['id_asesmen'],
                'id_cpmk' => $item['id_cpmk'],
                'nomor_soal' => $item['nomor_soal'],
            ]);
        }

        $this->_updateLastStep($id, 7);
        return $this->_json(['status' => 'success', 'message' => 'Rancangan soal berhasil disimpan.']);
    }

    /**
     * STEP 8 – Pelaksanaan Perkuliahan
     * POST /admin/portofolio/step/pelaksanaan  (multipart)
     * Files: file_kontrak_kuliah, file_realisasi_mengajar, file_kehadiran
     */
    public function savePelaksanaan()
    {
        $db = \Config\Database::connect();
        $id = (string) $this->request->getPost('id_portofolio');

        $existing = $db->table('pelaksanaan')->where('id_portofolio', $id)->get()->getRowArray();

        $fields = ['file_kontrak_kuliah', 'file_realisasi_mengajar', 'file_kehadiran'];
        $payload = [];

        foreach ($fields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $nm = $field . '_' . $id . '_' . time() . '.' . $file->getExtension();
                $file->move(WRITEPATH . 'uploads/pelaksanaan/', $nm);
                $payload[$field] = $nm;
            } elseif ($existing) {
                $payload[$field] = $existing[$field]; // keep old file
            }
        }

        if ($existing) {
            $db->table('pelaksanaan')->where('id_portofolio', $id)->update($payload);
        } else {
            $db->table('pelaksanaan')->insert(array_merge(['id_portofolio' => $id, 'created_at' => date('Y-m-d H:i:s')], $payload));
        }

        $this->_updateLastStep($id, 8);

        return $this->_json(['status' => 'success', 'message' => 'Pelaksanaan perkuliahan berhasil disimpan.']);
    }

    /**
     * STEP 9 – Hasil Asesmen
     * POST /admin/portofolio/step/hasil-asesmen  (multipart)
     * Files: file_jawaban_tugas, file_jawaban_uts, file_jawaban_uas
     *        file_nilai_matkul, file_nilai_cpmk
     */
    public function saveHasilAsesmen()
    {
        $db = \Config\Database::connect();
        $id = (string) $this->request->getPost('id_portofolio');

        $jenisMap = [
            'tugas' => 'file_jawaban_tugas',
            'uts'   => 'file_jawaban_uts',
            'uas'   => 'file_jawaban_uas',
        ];

        foreach ($jenisMap as $jenis => $fieldName) {
            $file = $this->request->getFile($fieldName);
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $nm = 'jawaban_' . $jenis . '_' . $id . '_' . time() . '.' . $file->getExtension();
                $file->move(WRITEPATH . 'uploads/hasil_asesmen/', $nm);

                $existing = $db->table('hasil_asesmen')
                    ->where('id_portofolio', $id)
                    ->where('jenis_asesmen', $jenis)
                    ->get()->getRowArray();

                if ($existing) {
                    $db->table('hasil_asesmen')->where('id', $existing['id'])->update(['file_jawaban' => $nm]);
                } else {
                    $db->table('hasil_asesmen')->insert([
                        'id_portofolio' => $id,
                        'jenis_asesmen' => $jenis,
                        'file_jawaban'  => $nm,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // Nilai Matkul
        $nilaiMKFile = $this->request->getFile('file_nilai_matkul');
        if ($nilaiMKFile && $nilaiMKFile->isValid() && ! $nilaiMKFile->hasMoved()) {
            $nm = 'nilai_mk_' . $id . '_' . time() . '.' . $nilaiMKFile->getExtension();
            $nilaiMKFile->move(WRITEPATH . 'uploads/nilai/', $nm);
            $ex = $db->table('nilai_matkul')->where('id_portofolio', $id)->get()->getRowArray();
            if ($ex) {
                $db->table('nilai_matkul')->where('id_portofolio', $id)->update(['file_nilai_matkul' => $nm]);
            } else {
                $db->table('nilai_matkul')->insert(['id_portofolio' => $id, 'file_nilai_matkul' => $nm, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }

        // Nilai CPMK
        $nilaiCPMKFile = $this->request->getFile('file_nilai_cpmk');
        if ($nilaiCPMKFile && $nilaiCPMKFile->isValid() && ! $nilaiCPMKFile->hasMoved()) {
            $nm = 'nilai_cpmk_' . $id . '_' . time() . '.' . $nilaiCPMKFile->getExtension();
            $nilaiCPMKFile->move(WRITEPATH . 'uploads/nilai/', $nm);
            $ex = $db->table('nilai_cpmk')->where('id_portofolio', $id)->get()->getRowArray();
            if ($ex) {
                $db->table('nilai_cpmk')->where('id_portofolio', $id)->update(['file_nilai_cpmk' => $nm]);
            } else {
                $db->table('nilai_cpmk')->insert(['id_portofolio' => $id, 'file_nilai_cpmk' => $nm, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }

        $this->_updateLastStep($id, 9);

        return $this->_json(['status' => 'success', 'message' => 'Hasil asesmen berhasil disimpan.']);
    }

    /**
     * STEP 10 – Evaluasi Perkuliahan
     * POST /admin/portofolio/step/evaluasi
     * Body (JSON): id_portofolio, evaluasi_list (array of {id_cpmk, rata_rata, isi_cpmk})
     */
    public function saveEvaluasi()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id   = (string) ($json['id_portofolio'] ?? 0);

        $db->table('evaluasi')->where('id_portofolio', $id)->delete();

        $evalList = $json['evaluasi_list'] ?? [];
        foreach ($evalList as $e) {
            $db->table('evaluasi')->insert([
                'id_portofolio' => $id,
                'id_cpmk'       => (int) $e['id_cpmk'],
                'rata_rata'     => (float) $e['rata_rata'],
                'isi_cpmk'      => trim($e['isi_cpmk'] ?? ''),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // Mark as fully complete
        $this->_updateLastStep($id, 10);

        return $this->_json(['status' => 'success', 'message' => 'Portofolio berhasil disimpan sepenuhnya! 🎉']);
    }

    // ══════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════

    /**
     * Update last_step only if the new step is greater than current last_step (to prevent going backwards)
     */
    private function _updateLastStep($id, $step)
    {
        $db = \Config\Database::connect();
        return $db->table('portofolio')
            ->where('id', $id)
            ->update(['last_step' => $step]);
    }

    /**
     * JSON response
     */
    private function _json(array $data, int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }
}
