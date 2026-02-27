<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CPL;
use App\Models\Kurikulum;
use App\Models\Prodi;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CPLController extends BaseController
{
    protected $cplModel;
    protected $kurikulumModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->cplModel      = new CPL();
        $this->kurikulumModel = new Kurikulum();
        $this->prodiModel    = new Prodi();
    }

    public function index()
    {
        $kurikulum = $this->kurikulumModel->findAll();
        $prodi     = $this->prodiModel->findAll();
        return view('admin/cpl', [
            'kurikulum' => $kurikulum,
            'prodi'     => $prodi,
        ]);
    }

    public function getData()
    {
        // Join prodi & kurikulum untuk tampil nama di tabel
        $data = $this->cplModel
            ->select('cpl.*, prodi.nama_prodi, prodi.kode_prodi, kurikulum.nama_kurikulum')
            ->join('prodi', 'prodi.id = cpl.id_prodi', 'left')
            ->join('kurikulum', 'kurikulum.id = cpl.id_kurikulum', 'left')
            ->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'id_prodi'     => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'no_cpl'       => 'required',
            'cpl_indo'     => 'required|min_length[5]',
            'cpl_inggris'  => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors()
            ]);
        }

        $this->cplModel->save([
            'id_prodi'     => $this->request->getPost('id_prodi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'no_cpl'       => $this->request->getPost('no_cpl'),
            'cpl_indo'     => $this->request->getPost('cpl_indo'),
            'cpl_inggris'  => $this->request->getPost('cpl_inggris'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'CPL berhasil ditambahkan.'
        ]);
    }

    public function show($id)
    {
        $cpl = $this->cplModel->find($id);
        if (!$cpl)
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        return $this->response->setJSON(['status' => 'success', 'data' => $cpl]);
    }

    public function update($id)
    {
        $rules = [
            'id_prodi'     => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'no_cpl'       => 'required',
            'cpl_indo'     => 'required|min_length[5]',
            'cpl_inggris'  => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors()
            ]);
        }

        $this->cplModel->update($id, [
            'id_prodi'     => $this->request->getPost('id_prodi'),
            'id_kurikulum' => $this->request->getPost('id_kurikulum'),
            'no_cpl'       => $this->request->getPost('no_cpl'),
            'cpl_indo'     => $this->request->getPost('cpl_indo'),
            'cpl_inggris'  => $this->request->getPost('cpl_inggris'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'CPL berhasil diperbarui.'
        ]);
    }

    public function delete($id)
    {
        if (!$this->cplModel->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        $this->cplModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'CPL berhasil dihapus.']);
    }

    // ── Import Excel ─────────────────────────────────────
    // Kolom template: A=no_cpl, B=cpl_indo, C=cpl_inggris, D=kode_prodi, E=nama_kurikulum
    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File tidak valid.'
            ]);
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Format file harus .xlsx atau .xls.'
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $inserted = 0;
            $skipped  = 0;
            $errors   = [];

            foreach ($rows as $i => $row) {
                if ($i === 1) continue; // skip header

                $no_cpl        = trim($row['A'] ?? '');
                $cpl_indo      = trim($row['B'] ?? '');
                $cpl_inggris   = trim($row['C'] ?? '');
                $kode_prodi    = trim($row['D'] ?? '');
                $nama_kurikulum = trim($row['E'] ?? '');

                // Baris kosong → lewati
                if (empty($no_cpl) && empty($cpl_indo)) continue;

                // Validasi kolom wajib
                if (empty($no_cpl) || empty($cpl_indo) || empty($cpl_inggris) || empty($kode_prodi) || empty($nama_kurikulum)) {
                    $errors[] = "Baris {$i}: Semua kolom (A–E) wajib diisi.";
                    continue;
                }

                // Lookup id_prodi berdasarkan kode_prodi
                $prodi = $this->prodiModel->where('kode_prodi', $kode_prodi)->first();
                if (!$prodi) {
                    $errors[] = "Baris {$i}: Kode prodi '{$kode_prodi}' tidak ditemukan.";
                    continue;
                }

                // Lookup id_kurikulum berdasarkan nama_kurikulum
                $kurikulum = $this->kurikulumModel->where('nama_kurikulum', $nama_kurikulum)->first();
                if (!$kurikulum) {
                    $errors[] = "Baris {$i}: Kurikulum '{$nama_kurikulum}' tidak ditemukan.";
                    continue;
                }

                // Cek duplikat (kombinasi no_cpl + id_prodi + id_kurikulum)
                $exists = $this->cplModel
                    ->where('no_cpl', $no_cpl)
                    ->where('id_prodi', $prodi['id'])
                    ->where('id_kurikulum', $kurikulum['id'])
                    ->countAllResults();

                if ($exists > 0) {
                    $skipped++;
                    continue;
                }

                $this->cplModel->save([
                    'id_prodi'     => $prodi['id'],
                    'id_kurikulum' => $kurikulum['id'],
                    'no_cpl'       => $no_cpl,
                    'cpl_indo'     => $cpl_indo,
                    'cpl_inggris'  => $cpl_inggris,
                ]);

                $inserted++;
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "{$inserted} data berhasil diimport, {$skipped} dilewati (duplikat).",
                'errors'  => $errors,
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadTemplate()
    {
        $path = ROOTPATH . 'public/templates/template_import_cpl.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_cpl.xlsx"')
            ->setBody(file_get_contents($path));
    }
}