<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MK;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MKController extends BaseController
{
    protected $mkModel;

    public function __construct()
    {
        $this->mkModel = new MK();
    }

    public function index()
    {
        return view('admin/mk');
    }

    public function getData()
    {
        $data = $this->mkModel->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'kode_mk'  => 'required|is_unique[mk.kode_mk]',
            'nama_mk'  => 'required|min_length[3]',
            'kelp_mk'  => 'required',
            'teori'    => 'required|integer',
            'praktek'  => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->mkModel->save([
            'kode_mk' => $this->request->getPost('kode_mk'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kelp_mk' => $this->request->getPost('kelp_mk'),
            'teori'   => (int) $this->request->getPost('teori'),
            'praktek' => (int) $this->request->getPost('praktek'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Matakuliah berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $mk = $this->mkModel->find($id);
        if (!$mk) return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        return $this->response->setJSON(['status' => 'success', 'data' => $mk]);
    }

    public function update($id)
    {
        $rules = [
            'kode_mk'  => "required|is_unique[mk.kode_mk,id,{$id}]",
            'nama_mk'  => 'required|min_length[3]',
            'kelp_mk'  => 'required',
            'teori'    => 'required|integer',
            'praktek'  => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $this->mkModel->update($id, [
            'kode_mk' => $this->request->getPost('kode_mk'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kelp_mk' => $this->request->getPost('kelp_mk'),
            'teori'   => (int) $this->request->getPost('teori'),
            'praktek' => (int) $this->request->getPost('praktek'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Matakuliah berhasil diperbarui.']);
    }

    public function delete($id)
    {
        if (!$this->mkModel->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        $this->mkModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Matakuliah berhasil dihapus.']);
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

            foreach ($rows as $i => $row) {
                if ($i === 1) continue; // skip header

                $kode_mk = trim($row['A'] ?? '');
                $nama_mk = trim($row['B'] ?? '');
                $kelp_mk = trim($row['C'] ?? '');
                $teori   = $row['D'] ?? 0;
                $praktek = $row['E'] ?? 0;

                if (empty($kode_mk) && empty($nama_mk)) continue;

                // Cek duplikat
                if ($this->mkModel->where('kode_mk', $kode_mk)->countAllResults() > 0) {
                    $skipped++;
                    continue;
                }

                if (empty($kode_mk) || empty($nama_mk) || empty($kelp_mk)) {
                    $errors[] = "Baris {$i}: kode_mk, nama_mk, kelp_mk tidak boleh kosong.";
                    continue;
                }

                $this->mkModel->save([
                    'kode_mk' => $kode_mk,
                    'nama_mk' => $nama_mk,
                    'kelp_mk' => $kelp_mk,
                    'teori'   => (int) $teori,
                    'praktek' => (int) $praktek,
                ]);
                $inserted++;
            }

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => "{$inserted} data berhasil diimport, {$skipped} dilewati (duplikat).",
                'errors'   => $errors,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        $path = ROOTPATH . 'public/templates/template_import_mk.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_mk.xlsx"')
            ->setBody(file_get_contents($path));
    }
}