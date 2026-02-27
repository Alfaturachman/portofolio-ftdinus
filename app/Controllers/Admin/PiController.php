<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Pi;
use App\Models\Prodi;
use App\Models\Kurikulum;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PiController extends BaseController
{
    protected $piModel;
    protected $prodiModel;
    protected $kurikulumModel;

    public function __construct()
    {
        $this->piModel        = new Pi();
        $this->prodiModel     = new Prodi();
        $this->kurikulumModel = new Kurikulum();
    }

    public function index()
    {
        $prodi     = $this->prodiModel->findAll();
        $kurikulum = $this->kurikulumModel->findAll();
        return view('admin/pi', compact('prodi', 'kurikulum'));
    }

    public function getData()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('pi p')
            ->select('p.*, pr.nama_prodi, pr.kode_prodi, k.nama_kurikulum, k.tahun_ajaran')
            ->join('prodi pr', 'pr.id = p.id_prodi', 'left')
            ->join('kurikulum k', 'k.id = p.id_kurikulum', 'left')
            ->get()->getResultArray();

        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'id_prodi'     => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'no_pi'        => 'required',
            'isi_pi'       => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->piModel->save([
            'id_prodi'     => $this->request->getPost('id_prodi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'no_pi'        => $this->request->getPost('no_pi'),
            'isi_pi'       => $this->request->getPost('isi_pi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'PI berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $pi = $this->piModel->find($id);
        if (!$pi) return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        return $this->response->setJSON(['status' => 'success', 'data' => $pi]);
    }

    public function update($id)
    {
        $rules = [
            'id_prodi'     => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'no_pi'        => 'required',
            'isi_pi'       => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->piModel->update($id, [
            'id_prodi'     => $this->request->getPost('id_prodi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'no_pi'        => $this->request->getPost('no_pi'),
            'isi_pi'       => $this->request->getPost('isi_pi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'PI berhasil diperbarui.']);
    }

    public function delete($id)
    {
        if (!$this->piModel->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        $this->piModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'PI berhasil dihapus.']);
    }

    // ── Import Excel ─────────────────────────────────────
    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid.']);
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format file harus .xlsx atau .xls.']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $inserted = 0;
            $skipped  = 0;
            $errors   = [];

            // Cache agar tidak query berulang
            $prodiCache     = [];
            $kurikulumCache = [];

            foreach ($rows as $i => $row) {
                if ($i === 1) continue;

                $kode_prodi     = trim($row['A'] ?? '');
                $nama_kurikulum = trim($row['B'] ?? '');
                $no_pi          = trim($row['C'] ?? '');
                $isi_pi         = trim($row['D'] ?? '');

                if (empty($kode_prodi) && empty($no_pi)) continue;

                // Lookup id_prodi by kode_prodi (cache)
                if (!isset($prodiCache[$kode_prodi])) {
                    $prodi = $this->prodiModel
                        ->where('kode_prodi', $kode_prodi)
                        ->first();
                    $prodiCache[$kode_prodi] = $prodi ? $prodi['id'] : null;
                }

                $id_prodi = $prodiCache[$kode_prodi];

                if (!$id_prodi) {
                    $errors[] = "Baris {$i}: kode_prodi '{$kode_prodi}' tidak ditemukan di sistem.";
                    continue;
                }

                // Lookup id_kurikulum by nama_kurikulum (cache)
                if (!isset($kurikulumCache[$nama_kurikulum])) {
                    $kurikulum = $this->kurikulumModel
                        ->where('nama_kurikulum', $nama_kurikulum)
                        ->first();
                    $kurikulumCache[$nama_kurikulum] = $kurikulum ? $kurikulum['id'] : null;
                }

                $id_kurikulum = $kurikulumCache[$nama_kurikulum];

                if (!$id_kurikulum) {
                    $errors[] = "Baris {$i}: nama_kurikulum '{$nama_kurikulum}' tidak ditemukan di sistem.";
                    continue;
                }

                if (empty($no_pi) || empty($isi_pi)) {
                    $errors[] = "Baris {$i}: no_pi dan isi_pi tidak boleh kosong.";
                    continue;
                }

                // Cek duplikat
                $exists = $this->piModel
                    ->where('id_prodi', $id_prodi)
                    ->where('id_kurikulum', $id_kurikulum)
                    ->where('no_pi', $no_pi)
                    ->countAllResults();

                if ($exists > 0) {
                    $skipped++;
                    continue;
                }

                $this->piModel->save([
                    'id_prodi'     => $id_prodi,
                    'id_kurikulum' => $id_kurikulum,
                    'no_pi'        => $no_pi,
                    'isi_pi'       => $isi_pi,
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
        $path = ROOTPATH . 'public/templates/template_import_pi.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_pi.xlsx"')
            ->setBody(file_get_contents($path));
    }
}