<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Perkuliahan;
use App\Models\MK;
use App\Models\Users;
use App\Models\Kurikulum;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PerkuliahanController extends BaseController
{
    protected $model;
    protected $mkModel;
    protected $usersModel;
    protected $kurikulumModel;

    public function __construct()
    {
        $this->model          = new Perkuliahan();
        $this->mkModel        = new MK();
        $this->usersModel     = new Users();
        $this->kurikulumModel = new Kurikulum();
    }

    public function index()
    {
        $data = [
            'mk'        => $this->mkModel->findAll(),
            'users'     => $this->usersModel->findAll(),
            'kurikulum' => $this->kurikulumModel->findAll(),
        ];
        return view('admin/perkuliahan', $data);
    }

    public function getData()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('perkuliahan p')
            ->select('p.*, mk.kode_mk, mk.nama_mk, u.nama_lengkap, u.npp, k.nama_kurikulum, k.tahun_ajaran')
            ->join('mk',        'mk.id = p.id_mk',         'left')
            ->join('users u',   'u.npp = p.id_users',      'left')
            ->join('kurikulum k','k.id = p.id_kurikulum',  'left')
            ->get()->getResultArray();

        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'id_mk'         => 'required|integer',
            'id_users'      => 'required',
            'id_kurikulum'  => 'required|integer',
            'semester'      => 'required',
            'kode_kelas'    => 'required',
            'tahun_akademik'=> 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        // Cek duplikat kombinasi MK + Dosen + Kelas + Tahun Akademik
        $exists = $this->model
            ->where('id_mk',          $this->request->getPost('id_mk'))
            ->where('id_users',       $this->request->getPost('id_users'))
            ->where('kode_kelas',     $this->request->getPost('kode_kelas'))
            ->where('tahun_akademik', $this->request->getPost('tahun_akademik'))
            ->countAllResults();

        if ($exists > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data perkuliahan ini sudah ada.']);
        }

        $this->model->save([
            'id_mk'          => $this->request->getPost('id_mk'),
            'id_users'       => $this->request->getPost('id_users'),
            'id_kurikulum'   => $this->request->getPost('id_kurikulum'),
            'semester'       => $this->request->getPost('semester'),
            'kode_kelas'     => $this->request->getPost('kode_kelas'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data perkuliahan berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $row = $this->model->find($id);
        if (!$row) return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        return $this->response->setJSON(['status' => 'success', 'data' => $row]);
    }

    public function update($id)
    {
        $rules = [
            'id_mk'         => 'required|integer',
            'id_users'      => 'required',
            'id_kurikulum'  => 'required|integer',
            'semester'      => 'required',
            'kode_kelas'    => 'required',
            'tahun_akademik'=> 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->model->update($id, [
            'id_mk'          => $this->request->getPost('id_mk'),
            'id_users'       => $this->request->getPost('id_users'),
            'id_kurikulum'   => $this->request->getPost('id_kurikulum'),
            'semester'       => $this->request->getPost('semester'),
            'kode_kelas'     => $this->request->getPost('kode_kelas'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data perkuliahan berhasil diperbarui.']);
    }

    public function delete($id)
    {
        if (!$this->model->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Data perkuliahan berhasil dihapus.']);
    }

    // ── Import Excel ──────────────────────────────────────
    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid.']);
        }
        if (!in_array(strtolower($file->getClientExtension()), ['xlsx', 'xls'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format file harus .xlsx atau .xls.']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $inserted = 0;
            $skipped  = 0;
            $errors   = [];

            // Cache lookup
            $mkCache        = [];
            $kurikulumCache = [];

            foreach ($rows as $i => $row) {
                if ($i === 1) continue;

                $kode_mk        = trim($row['A'] ?? '');
                $npp            = trim($row['B'] ?? '');
                $nama_kurikulum = trim($row['C'] ?? '');
                $semester       = trim($row['D'] ?? '');
                $kode_kelas     = trim($row['E'] ?? '');
                $tahun_akademik = trim($row['F'] ?? '');

                if (empty($kode_mk) && empty($npp)) continue;

                // ── Lookup MK ──
                if (!isset($mkCache[$kode_mk])) {
                    $mk = $this->mkModel->where('kode_mk', $kode_mk)->first();
                    $mkCache[$kode_mk] = $mk ? $mk['id'] : null;
                }
                if (!$mkCache[$kode_mk]) {
                    $errors[] = "Baris {$i}: kode_mk '{$kode_mk}' tidak ditemukan.";
                    continue;
                }

                // ── Lookup Users (by NPP) ──
                $user = $this->usersModel->find($npp);
                if (!$user) {
                    $errors[] = "Baris {$i}: NPP '{$npp}' tidak ditemukan.";
                    continue;
                }

                // ── Lookup Kurikulum ──
                if (!isset($kurikulumCache[$nama_kurikulum])) {
                    $k = $this->kurikulumModel->where('nama_kurikulum', $nama_kurikulum)->first();
                    $kurikulumCache[$nama_kurikulum] = $k ? $k['id'] : null;
                }
                if (!$kurikulumCache[$nama_kurikulum]) {
                    $errors[] = "Baris {$i}: nama_kurikulum '{$nama_kurikulum}' tidak ditemukan.";
                    continue;
                }

                if (empty($semester) || empty($kode_kelas) || empty($tahun_akademik)) {
                    $errors[] = "Baris {$i}: semester, kode_kelas, tahun_akademik tidak boleh kosong.";
                    continue;
                }

                $id_mk        = $mkCache[$kode_mk];
                $id_kurikulum = $kurikulumCache[$nama_kurikulum];

                // ── Cek duplikat ──
                $exists = $this->model
                    ->where('id_mk',          $id_mk)
                    ->where('id_users',       $npp)
                    ->where('kode_kelas',     $kode_kelas)
                    ->where('tahun_akademik', $tahun_akademik)
                    ->countAllResults();

                if ($exists > 0) { $skipped++; continue; }

                $this->model->save([
                    'id_mk'          => $id_mk,
                    'id_users'       => $npp,
                    'id_kurikulum'   => $id_kurikulum,
                    'semester'       => $semester,
                    'kode_kelas'     => $kode_kelas,
                    'tahun_akademik' => $tahun_akademik,
                ]);
                $inserted++;
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "{$inserted} data berhasil diimport, {$skipped} dilewati (duplikat).",
                'errors'  => $errors,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        $path = ROOTPATH . 'public/templates/template_import_perkuliahan.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_perkuliahan.xlsx"')
            ->setBody(file_get_contents($path));
    }
}