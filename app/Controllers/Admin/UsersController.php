<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Users;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UsersController extends BaseController
{
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new Users();
    }

    public function index()
    {
        return view('admin/users');
    }

    public function getData()
    {
        $data = $this->usersModel->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'npp'          => 'required|is_unique[users.npp]',
            'nama_lengkap' => 'required|min_length[3]',
            'password'     => 'required|min_length[6]',
            'role'         => 'required|in_list[admin,dosen,mahasiswa]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $this->usersModel->save([
            'npp'          => $this->request->getPost('npp'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'         => $this->request->getPost('role'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User berhasil ditambahkan.']);
    }

    public function show($npp)
    {
        $user = $this->usersModel->find($npp);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $user]);
    }

    public function update($npp)
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'role'         => 'required|in_list[admin,dosen,mahasiswa]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role'         => $this->request->getPost('role'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => ['password' => 'Password minimal 6 karakter.'],
                ]);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->usersModel->update($npp, $data);
        return $this->response->setJSON(['status' => 'success', 'message' => 'User berhasil diperbarui.']);
    }

    public function delete($npp)
    {
        if (!$this->usersModel->find($npp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan.']);
        }
        $this->usersModel->delete($npp);
        return $this->response->setJSON(['status' => 'success', 'message' => 'User berhasil dihapus.']);
    }

    // ── Import Excel ─────────────────────────────────────
    // Kolom template: A=NPP, B=Nama Lengkap, C=Role
    public function import()
    {
        $file = $this->request->getFile('user_file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File tidak valid atau tidak diunggah.',
            ]);
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Format file harus .xlsx atau .xls.',
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $inserted = 0;
            $skipped  = 0;
            $errors   = [];

            $validRoles = ['admin', 'dosen', 'mahasiswa'];

            foreach ($rows as $i => $row) {
                if ($i === 1) continue; // skip header

                $npp          = trim($row['A'] ?? '');
                $nama_lengkap = trim($row['B'] ?? '');
                $role         = strtolower(trim($row['C'] ?? ''));

                // Baris kosong → lewati
                if (empty($npp) && empty($nama_lengkap)) continue;

                // Validasi kolom wajib
                if (empty($npp) || empty($nama_lengkap) || empty($role)) {
                    $errors[] = "Baris {$i}: Semua kolom (NPP, Nama Lengkap, Role) wajib diisi.";
                    continue;
                }

                // Validasi role
                if (!in_array($role, $validRoles)) {
                    $errors[] = "Baris {$i}: Role '{$role}' tidak valid. Gunakan: admin, dosen, atau mahasiswa.";
                    continue;
                }

                // Cek duplikat NPP
                if ($this->usersModel->find($npp)) {
                    $skipped++;
                    continue;
                }

                $this->usersModel->save([
                    'npp'          => $npp,
                    'nama_lengkap' => $nama_lengkap,
                    'password'     => password_hash($npp, PASSWORD_DEFAULT), // default password = NPP
                    'role'         => $role,
                ]);

                $inserted++;
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "{$inserted} user berhasil diimport, {$skipped} dilewati (NPP duplikat).",
                'errors'  => $errors,
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $path = ROOTPATH . 'public/templates/template_import_users.xlsx';
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_users.xlsx"')
            ->setBody(file_get_contents($path));
    }
}