<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->orderBy('id', 'ASC')->findAll();
        return view('backend/users/index', $data);
    }

    public function create()
    {
        return view('backend/users/create');
    }

    public function store()
    {
        // Validasi input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'homebase' => 'required',
            'nama' => 'required',
            'username' => 'required|is_unique[users.username]',
            'status' => 'required',
            'id_staf' => 'required',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,progdi,dekan,dosen,super admin]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $data = [
            'homebase' => $this->request->getPost('homebase'),
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'status' => $this->request->getPost('status'),
            'id_staf' => $this->request->getPost('id_staf'),
            'password' => $this->userModel->encryptPassword($this->request->getPost('password')),
            'role' => $this->request->getPost('role')
        ];

        // Simpan ke database
        if ($this->userModel->save($data)) {
            return redirect()->to('/users')->with('success', 'User berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
        }
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        $data['user'] = $user;
        return view('backend/users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        // Validasi input
        $validation = \Config\Services::validation();

        // Jika username diubah, pastikan itu unik (tapi abaikan user saat ini)
        $usernameRule = 'required';
        $postedUsername = $this->request->getPost('username');
        if ($postedUsername !== $user['username']) {
            $usernameRule .= '|is_unique[users.username]';
        }

        $validation->setRules([
            'homebase' => 'required',
            'nama' => 'required',
            'username' => $usernameRule,
            'status' => 'required',
            'id_staf' => 'required',
            'role' => 'required|in_list[admin,progdi,dekan,dosen,super admin]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari form
        $data = [
            'homebase' => $this->request->getPost('homebase'),
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'status' => $this->request->getPost('status'),
            'id_staf' => $this->request->getPost('id_staf'),
            'role' => $this->request->getPost('role')
        ];

        // Update ke database
        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/users')->with('success', 'User berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user');
        }
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to('/users')->with('error', 'Gagal menghapus user');
        }
    }

    public function changePassword($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        $data['user'] = $user;
        return view('backend/users/change_password', $data);
    }

    public function updatePassword($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        // Validasi input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Update password
        $data = [
            'password' => $this->userModel->encryptPassword($this->request->getPost('password'))
        ];

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/users')->with('success', 'Password berhasil diubah');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah password');
        }
    }
}