<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Portofolio extends BaseController
{
    // ══════════════════════════════════════════════════════
    //  VIEWS
    // ══════════════════════════════════════════════════════
    public function index()
    {
        $db  = \Config\Database::connect();
        $npp = session()->get('npp');

        log_message('debug', 'NPP SESSION: ' . $npp);

        $query = $db->table('perkuliahan per')
            ->select('
            per.id as id_perkuliahan,
            p.id as id_portofolio,
            p.last_step,
            mk.nama_mk, 
            mk.kode_mk,
            k.nama_kurikulum,
            per.tahun_akademik, 
            per.semester, 
            per.kode_kelas,
            u.nama_lengkap
        ')
            ->join('portofolio p',   'p.id_perkuliahan = per.id', 'left')
            ->join('mk',             'mk.id = per.id_mk')
            ->join('users u',        'u.npp = per.id_users')
            ->join('kurikulum k',    'k.id = per.id_kurikulum')
            ->where('per.id_users',  $npp)
            ->orderBy('per.id', 'DESC')
            ->get();

        $data['portofolios'] = $query->getResultArray();

        log_message('debug', 'TOTAL DATA: ' . count($data['portofolios']));

        return view('admin/portofolio/index', $data);
    }

    public function start($id_perkuliahan)
    {
        $portofolioModel = new \App\Models\Portofolio();

        // 🔎 Cek apakah sudah ada portofolio untuk perkuliahan ini
        $existing = $portofolioModel
            ->where('id_perkuliahan', $id_perkuliahan)
            ->first();

        if ($existing) {
            // ✅ Kalau sudah ada → langsung ke form pakai ID lama
            return redirect()->to(
                base_url('admin/portofolio/form/' . $existing['id'])
            );
        }

        // ❌ Kalau belum ada → insert baru
        // ✅ Generate ID 16 karakter hex
        $id_portofolio = substr(bin2hex(random_bytes(8)), 0, 16);

        $portofolioModel->insert([
            'id'             => $id_portofolio,
            'id_perkuliahan' => $id_perkuliahan,
            'last_step'      => 1
        ]);

        $id_portofolio = $portofolioModel->getInsertID();

        // 🚀 Redirect ke form
        return redirect()->to(
            base_url('admin/portofolio/form/' . $id_portofolio)
        );
    }

    /**
     * Form add (new portofolio).
     * Creates a new portofolio record and redirects to form step 1.
     */
    public function add()
    {
        $db = \Config\Database::connect();
        $npp = session()->get('npp');

        // Get first perkuliahan for this user as default
        // User can change MK later in step 2 (Info MK)
        $perkuliahan = $db->table('perkuliahan per')
            ->select('per.id, per.id_mk, per.id_kurikulum')
            ->join('mk', 'mk.id = per.id_mk')
            ->where('per.id_users', $npp)
            ->orderBy('per.id', 'DESC')
            ->get()->getRowArray();

        if (! $perkuliahan) {
            // No perkuliahan available, redirect to perkuliahan page
            return redirect()->to(base_url('admin/perkuliahan'))
                ->with('error', 'Anda belum memiliki data perkuliahan. Silahkan tambahkan perkuliahan terlebih dahulu.');
        }

        // Create portofolio skeleton
        $db->table('portofolio')->insert([
            'id_perkuliahan' => $perkuliahan['id'],
            'last_step'      => 1,
        ]);
        $portofolio_id = $db->insertID();

        return redirect()->to(base_url("admin/portofolio/form/{$portofolio_id}"));
    }

    /**
     * Show the multi-step form, resume at last_step.
     */
    public function form(string $id)
    {
        $db   = \Config\Database::connect();
        $npp  = session()->get('npp');

        $porto = $db->table('portofolio p')
            ->select('p.*, per.id_mk, per.id_kurikulum, per.id_users,
                      mk.nama_mk, mk.kode_mk, mk.kelp_mk, mk.teori, mk.praktek,
                      k.tahun_ajaran, k.nama_kurikulum,
                      per.semester, per.tahun_akademik, per.kode_kelas')
            ->join('perkuliahan per', 'per.id = p.id_perkuliahan')
            ->join('mk', 'mk.id = per.id_mk')
            ->join('kurikulum k', 'k.id = per.id_kurikulum')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (! $porto || $porto['id_users'] !== $npp) {
            return redirect()->to(base_url('admin/portofolio'))->with('error', 'Portofolio tidak ditemukan.');
        }

        // ── Existing step data (to prefill the form via JSON) ──
        $data['porto']     = $porto;
        $data['last_step'] = (int) $porto['last_step'];

        // RPS
        $data['rps'] = $db->table('rps')->where('id_portofolio', $id)->get()->getRowArray();

        // Informasi MK
        $data['info_mk'] = $db->table('informasi_mk')->where('id_portofolio', $id)->get()->getRowArray();

        // CPL & PI for this MK-kurikulum
        $data['cpls'] = $db->table('mk_cpl_pi mcp')
            ->select('cpl.id, cpl.no_cpl, cpl.cpl_indo, cpl.cpl_inggris, pi.id as id_pi, pi.no_pi, pi.isi_pi')
            ->join('cpl', 'cpl.id = mcp.id_cpl')
            ->join('pi', 'pi.id = mcp.id_pi')
            ->where('mcp.id_mk', $porto['id_mk'])
            ->where('mcp.id_kurikulum', $porto['id_kurikulum'])
            ->get()->getResultArray();

        // CPMK
        $cpmks = $db->table('cpmk')->where('id_portofolio', $id)->orderBy('no_cpmk')->get()->getResultArray();
        foreach ($cpmks as &$cpmk) {
            $cpmk['subs'] = $db->table('sub_cpmk')
                ->where('id_cpmk', $cpmk['id'])
                ->orderBy('no_sub_cpmk')
                ->get()->getResultArray();
        }
        $data['cpmks'] = $cpmks;

        // Pemetaan CPL-CPMK-SubCPMK
        $data['mapping'] = $db->table('pemetaan')
            ->where('id_portofolio', $id)->get()->getResultArray();

        // Rancangan Asesmen
        $data['asesmen'] = $db->table('rancangan_asesmen')
            ->where('id_portofolio', $id)->get()->getResultArray();

        // Rancangan Soal
        $data['soal'] = $db->table('rancangan_soal rs')
            ->select('rs.*, ra.jenis_asesmen, ra.id_cpmk')
            ->join('rancangan_asesmen ra', 'ra.id = rs.id_asesmen')
            ->where('rs.id_portofolio', $id)
            ->get()->getResultArray();

        // Pelaksanaan
        $data['pelaksanaan'] = $db->table('pelaksanaan')->where('id_portofolio', $id)->get()->getRowArray();

        // Hasil Asesmen
        $data['hasil_asesmen'] = $db->table('hasil_asesmen')->where('id_portofolio', $id)->get()->getResultArray();

        // Nilai MK & CPMK
        $data['nilai_matkul'] = $db->table('nilai_matkul')->where('id_portofolio', $id)->get()->getRowArray();
        $data['nilai_cpmk']   = $db->table('nilai_cpmk')->where('id_portofolio', $id)->get()->getRowArray();

        // Evaluasi
        $data['evaluasi'] = $db->table('evaluasi')->where('id_portofolio', $id)->get()->getResultArray();

        return view('admin/portofolio/form', $data);
    }

    // ══════════════════════════════════════════════════════
    //  STEP SAVERS  (called via AJAX POST, return JSON)
    // ══════════════════════════════════════════════════════

    /**
     * Step 1 – Upload RPS
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

    /**
     * Step 2 – Informasi Mata Kuliah
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
     * Step 3 – CPL & PI  (read-only display, nothing to save; just advance step)
     * POST /admin/portofolio/step/cpl
     */
    public function saveCPL()
    {
        $id = (int) $this->request->getPost('id_portofolio');

        $this->_updateLastStep($id, 3);

        return $this->_json(['status' => 'success', 'message' => 'Step 3 dicatat.']);
    }

    /**
     * Step 4 – CPMK & Sub CPMK
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
     * Step 5 – Pemetaan CPL-CPMK-SubCPMK
     * POST /admin/portofolio/step/mapping
     * Body (JSON): id_portofolio, mappings (array of {id_cpl, id_cpmk, id_sub_cpmk})
     */
    public function saveMapping()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        
        // Debug: log received data
        log_message('debug', 'saveMapping received: ' . json_encode($json));
        
        $id   = (string) ($json['id_portofolio'] ?? '');

        if (empty($id)) {
            return $this->_json(['status' => 'error', 'message' => 'ID Portofolio tidak valid.']);
        }

        $mappings = $json['mappings'] ?? [];
        if (empty($mappings)) {
            return $this->_json(['status' => 'error', 'message' => 'Minimal satu pemetaan harus dipilih.']);
        }

        // ── 1. Hapus data pemetaan lama ──────────────────────────────────────
        $db->table('pemetaan')->where('id_portofolio', $id)->delete();

        // ── 2. Insert pemetaan baru ──────────────────────────────────────────
        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        foreach ($mappings as $map) {
            $result = $db->table('pemetaan')->insert([
                'id_portofolio' => $id,
                'id_cpl'        => (int) $map['id_cpl'],
                'id_cpmk'       => (int) $map['id_cpmk'],
                'id_sub_cpmk'   => (int) $map['id_sub_cpmk'],
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            if ($result) {
                $inserted++;
            }
        }

        $this->_updateLastStep($id, 5);

        return $this->_json([
            'status' => 'success', 
            'message' => "Pemetaan berhasil disimpan. ({$inserted} data)",
            'inserted' => $inserted
        ]);
    }

    /**
     * Step 6 – Rancangan Asesmen
     * POST /admin/portofolio/step/asesmen  (multipart – may include files)
     * Fields: id_portofolio,
     *         asesmen_cpmk[]  => array of {id_cpmk, jenis_asesmen}
     *         file_soal_tugas, file_rubrik_tugas, file_soal_uts, file_rubrik_uts,
     *         file_soal_uas,  file_rubrik_uas
     */
    public function saveAsesmen()
    {
        $db = \Config\Database::connect();
        $id = (int) $this->request->getPost('id_portofolio');

        // Parse JSON string sent as a form field
        $asesmenData = json_decode($this->request->getPost('asesmen_data'), true) ?? [];

        // Fetch current asesmen (to update files only if new file uploaded)
        $existing = [];
        foreach ($db->table('rancangan_asesmen')->where('id_portofolio', $id)->get()->getResultArray() as $row) {
            $existing[$row['jenis_asesmen']] = $row; // keyed by jenis: tugas/uts/uas
        }

        // Map jenis => [soal_field, rubrik_field]
        $fileMap = [
            'tugas' => ['soal' => 'file_soal_tugas', 'rubrik' => 'file_rubrik_tugas'],
            'uts'   => ['soal' => 'file_soal_uts',   'rubrik' => 'file_rubrik_uts'],
            'uas'   => ['soal' => 'file_soal_uas',   'rubrik' => 'file_rubrik_uas'],
        ];

        // Collect which jenis are used
        $jenisUsed = [];
        foreach ($asesmenData as $row) {
            if (! in_array($row['jenis_asesmen'], $jenisUsed)) {
                $jenisUsed[] = $row['jenis_asesmen'];
            }
        }

        // Delete rows for jenis no longer selected
        foreach (array_keys($existing) as $jenis) {
            if (! in_array($jenis, $jenisUsed)) {
                $db->table('rancangan_asesmen')
                    ->where('id_portofolio', $id)
                    ->where('jenis_asesmen', $jenis)
                    ->delete();
            }
        }

        // Upsert per jenis_asesmen per id_cpmk
        $db->table('rancangan_asesmen')->where('id_portofolio', $id)->delete();

        foreach ($asesmenData as $row) {
            $jenis   = $row['jenis_asesmen'];
            $id_cpmk = (int) $row['id_cpmk'];

            $fileSoal   = null;
            $fileRubrik = null;

            // Only upload file once per jenis (first occurrence)
            if (isset($fileMap[$jenis])) {
                $soalFile   = $this->request->getFile($fileMap[$jenis]['soal']);
                $rubrikFile = $this->request->getFile($fileMap[$jenis]['rubrik']);

                if ($soalFile && $soalFile->isValid() && ! $soalFile->hasMoved()) {
                    $nm = 'soal_' . $id . '_' . $jenis . '_' . time() . '.' . $soalFile->getExtension();
                    $soalFile->move(WRITEPATH . 'uploads/asesmen/', $nm);
                    $fileSoal = $nm;
                    unset($fileMap[$jenis]['soal']); // prevent re-upload for same jenis
                } elseif (isset($existing[$jenis])) {
                    $fileSoal = $existing[$jenis]['file_soal'];
                }

                if ($rubrikFile && $rubrikFile->isValid() && ! $rubrikFile->hasMoved()) {
                    $nm = 'rubrik_' . $id . '_' . $jenis . '_' . time() . '.' . $rubrikFile->getExtension();
                    $rubrikFile->move(WRITEPATH . 'uploads/asesmen/', $nm);
                    $fileRubrik = $nm;
                    unset($fileMap[$jenis]['rubrik']);
                } elseif (isset($existing[$jenis])) {
                    $fileRubrik = $existing[$jenis]['file_rubrik'];
                }
            }

            $db->table('rancangan_asesmen')->insert([
                'id_portofolio'  => $id,
                'id_cpmk'        => $id_cpmk,
                'jenis_asesmen'  => $jenis,
                'file_soal'      => $fileSoal,
                'file_rubrik'    => $fileRubrik,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        $this->_updateLastStep($id, 6);

        return $this->_json(['status' => 'success', 'message' => 'Rancangan asesmen berhasil disimpan.']);
    }

    /**
     * Step 7 – Rancangan Soal
     * POST /admin/portofolio/step/soal
     * Body (JSON): id_portofolio, soal_list (array of {id_asesmen, nomor_soal})
     */
    public function saveSoal()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id   = (int) ($json['id_portofolio'] ?? 0);

        $db->table('rancangan_soal')->where('id_portofolio', $id)->delete();

        $soalList = $json['soal_list'] ?? [];
        foreach ($soalList as $soal) {
            $db->table('rancangan_soal')->insert([
                'id_portofolio' => $id,
                'id_asesmen'    => (int) $soal['id_asesmen'],
                'nomor_soal'    => (int) $soal['nomor_soal'],
            ]);
        }

        $this->_updateLastStep($id, 7);

        return $this->_json(['status' => 'success', 'message' => 'Rancangan soal berhasil disimpan.']);
    }

    /**
     * Step 8 – Pelaksanaan Perkuliahan
     * POST /admin/portofolio/step/pelaksanaan  (multipart)
     * Files: file_kontrak_kuliah, file_realisasi_mengajar, file_kehadiran
     */
    public function savePelaksanaan()
    {
        $db = \Config\Database::connect();
        $id = (int) $this->request->getPost('id_portofolio');

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
     * Step 9 – Hasil Asesmen
     * POST /admin/portofolio/step/hasil-asesmen  (multipart)
     * Files: file_jawaban_tugas, file_jawaban_uts, file_jawaban_uas
     *        file_nilai_matkul, file_nilai_cpmk
     */
    public function saveHasilAsesmen()
    {
        $db = \Config\Database::connect();
        $id = (int) $this->request->getPost('id_portofolio');

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
     * Step 10 – Evaluasi Perkuliahan
     * POST /admin/portofolio/step/evaluasi
     * Body (JSON): id_portofolio, evaluasi_list (array of {id_cpmk, rata_rata, isi_cpmk})
     */
    public function saveEvaluasi()
    {
        $db   = \Config\Database::connect();
        $json = $this->request->getJSON(true);
        $id   = (int) ($json['id_portofolio'] ?? 0);

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
