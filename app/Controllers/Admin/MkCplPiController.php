<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MkCplPi;
use App\Models\MK;
use App\Models\CPL;
use App\Models\Pi;
use App\Models\Kurikulum;
use App\Models\Prodi;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MkCplPiController extends BaseController
{
    protected $model;
    protected $mkModel;
    protected $cplModel;
    protected $piModel;
    protected $kurikulumModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->model          = new MkCplPi();
        $this->mkModel        = new MK();
        $this->cplModel       = new CPL();
        $this->piModel        = new Pi();
        $this->kurikulumModel = new Kurikulum();
        $this->prodiModel     = new Prodi();
    }

    public function index()
    {
        $data = [
            'mk'        => $this->mkModel->findAll(),
            'cpl'       => $this->cplModel->findAll(),
            'pi'        => $this->piModel->findAll(),
            'kurikulum' => $this->kurikulumModel->findAll(),
            'prodi'     => $this->prodiModel->findAll(),
        ];
        return view('admin/mapping_cpl', $data);
    }

    public function getData()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('mk_cpl_pi m')
            ->select('m.id, m.id_mk, m.id_pi, m.id_cpl, m.id_kurikulum, m.id_prodi,
                      mk.kode_mk, mk.nama_mk,
                      c.no_cpl, c.cpl_indo,
                      p.no_pi, p.isi_pi,
                      k.nama_kurikulum, k.tahun_ajaran,
                      pr.kode_prodi, pr.nama_prodi')
            ->join('mk',        'mk.id = m.id_mk',          'left')
            ->join('cpl c',     'c.id  = m.id_cpl',         'left')
            ->join('pi p',      'p.id  = m.id_pi',          'left')
            ->join('kurikulum k','k.id = m.id_kurikulum',   'left')
            ->join('prodi pr',  'pr.id = m.id_prodi',       'left')
            ->get()->getResultArray();

        return $this->response->setJSON($data);
    }

    // Endpoint untuk Select2 AJAX — filter CPL/PI berdasarkan kurikulum & prodi
    public function getCplByKurikulum($id_kurikulum)
    {
        $data = $this->cplModel
            ->where('id_kurikulum', $id_kurikulum)
            ->findAll();
        return $this->response->setJSON($data);
    }

    public function getPiByKurikulumProdi($id_kurikulum, $id_prodi)
    {
        $data = $this->piModel
            ->where('id_kurikulum', $id_kurikulum)
            ->where('id_prodi', $id_prodi)
            ->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'id_mk'        => 'required|integer',
            'id_cpl'       => 'required|integer',
            'id_pi'        => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'id_prodi'     => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        // Cek duplikat kombinasi
        $exists = $this->model
            ->where('id_mk',        $this->request->getPost('id_mk'))
            ->where('id_cpl',       $this->request->getPost('id_cpl'))
            ->where('id_pi',        $this->request->getPost('id_pi'))
            ->where('id_kurikulum', $this->request->getPost('id_kurikulum'))
            ->where('id_prodi',     $this->request->getPost('id_prodi'))
            ->countAllResults();

        if ($exists > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mapping ini sudah ada.']);
        }

        $this->model->save([
            'id_mk'        => $this->request->getPost('id_mk'),
            'id_cpl'       => $this->request->getPost('id_cpl'),
            'id_pi'        => $this->request->getPost('id_pi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'id_prodi'     => $this->request->getPost('id_prodi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Mapping berhasil ditambahkan.']);
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
            'id_mk'        => 'required|integer',
            'id_cpl'       => 'required|integer',
            'id_pi'        => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'id_prodi'     => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->model->update($id, [
            'id_mk'        => $this->request->getPost('id_mk'),
            'id_cpl'       => $this->request->getPost('id_cpl'),
            'id_pi'        => $this->request->getPost('id_pi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'id_prodi'     => $this->request->getPost('id_prodi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Mapping berhasil diperbarui.']);
    }

    public function delete($id)
    {
        if (!$this->model->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Mapping berhasil dihapus.']);
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
            $cplCache       = [];
            $piCache        = [];
            $kurikulumCache = [];
            $prodiCache     = [];

            foreach ($rows as $i => $row) {
                if ($i === 1) continue;

                $kode_mk        = trim($row['A'] ?? '');
                $no_pi          = trim($row['B'] ?? '');
                $no_cpl         = trim($row['C'] ?? '');
                $nama_kurikulum = trim($row['D'] ?? '');
                $kode_prodi     = trim($row['E'] ?? '');

                if (empty($kode_mk) && empty($no_cpl)) continue;

                // ── Lookup MK ──
                if (!isset($mkCache[$kode_mk])) {
                    $mk = $this->mkModel->where('kode_mk', $kode_mk)->first();
                    $mkCache[$kode_mk] = $mk ? $mk['id'] : null;
                }
                if (!$mkCache[$kode_mk]) {
                    $errors[] = "Baris {$i}: kode_mk '{$kode_mk}' tidak ditemukan.";
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

                // ── Lookup Prodi ──
                if (!isset($prodiCache[$kode_prodi])) {
                    $pr = $this->prodiModel->where('kode_prodi', $kode_prodi)->first();
                    $prodiCache[$kode_prodi] = $pr ? $pr['id'] : null;
                }
                if (!$prodiCache[$kode_prodi]) {
                    $errors[] = "Baris {$i}: kode_prodi '{$kode_prodi}' tidak ditemukan.";
                    continue;
                }

                $id_kurikulum = $kurikulumCache[$nama_kurikulum];
                $id_prodi     = $prodiCache[$kode_prodi];

                // ── Lookup CPL (unik per kurikulum) ──
                $cplKey = "{$no_cpl}_{$id_kurikulum}";
                if (!isset($cplCache[$cplKey])) {
                    $c = $this->cplModel
                        ->where('no_cpl', $no_cpl)
                        ->where('id_kurikulum', $id_kurikulum)
                        ->first();
                    $cplCache[$cplKey] = $c ? $c['id'] : null;
                }
                if (!$cplCache[$cplKey]) {
                    $errors[] = "Baris {$i}: no_cpl '{$no_cpl}' tidak ditemukan untuk kurikulum '{$nama_kurikulum}'.";
                    continue;
                }

                // ── Lookup PI (unik per kurikulum + prodi) ──
                $piKey = "{$no_pi}_{$id_kurikulum}_{$id_prodi}";
                if (!isset($piCache[$piKey])) {
                    $p = $this->piModel
                        ->where('no_pi', $no_pi)
                        ->where('id_kurikulum', $id_kurikulum)
                        ->where('id_prodi', $id_prodi)
                        ->first();
                    $piCache[$piKey] = $p ? $p['id'] : null;
                }
                if (!$piCache[$piKey]) {
                    $errors[] = "Baris {$i}: no_pi '{$no_pi}' tidak ditemukan untuk kurikulum '{$nama_kurikulum}' dan prodi '{$kode_prodi}'.";
                    continue;
                }

                $id_mk  = $mkCache[$kode_mk];
                $id_cpl = $cplCache[$cplKey];
                $id_pi  = $piCache[$piKey];

                // ── Cek duplikat ──
                $exists = $this->model
                    ->where('id_mk',        $id_mk)
                    ->where('id_cpl',       $id_cpl)
                    ->where('id_pi',        $id_pi)
                    ->where('id_kurikulum', $id_kurikulum)
                    ->where('id_prodi',     $id_prodi)
                    ->countAllResults();

                if ($exists > 0) { $skipped++; continue; }

                $this->model->save([
                    'id_mk'        => $id_mk,
                    'id_cpl'       => $id_cpl,
                    'id_pi'        => $id_pi,
                    'id_kurikulum' => $id_kurikulum,
                    'id_prodi'     => $id_prodi,
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
        $path = ROOTPATH . 'public/templates/template_import_mk_cpl_pi.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_mk_cpl_pi.xlsx"')
            ->setBody(file_get_contents($path));
    }
}