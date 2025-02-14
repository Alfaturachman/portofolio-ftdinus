<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('backend/auth/login');
    }

    public function processLogin()
    {
        // Debugging: Check if the method is being called
        log_message('debug', 'processLogin method called.');

        // Validasi input
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation failed: ' . print_r($this->validator->getErrors(), true));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Debugging: Check if the form data is being received
        log_message('debug', 'Form data received: ' . print_r($this->request->getPost(), true));

        // Ambil data dari form
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Debugging: Check the username and password
        log_message('debug', 'Username: ' . $username);
        log_message('debug', 'Password: ' . $password);

        // Cari user berdasarkan username
        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            log_message('debug', 'User not found: ' . $username);
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan');
        }

        // Debugging: Check the stored password
        log_message('debug', 'Stored password: ' . $user['password']);

        // Verifikasi password
        if (!$this->verifyPassword($password, $user['password'])) {
            log_message('debug', 'Password verification failed.');
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        // Simpan data user ke session HANYA jika password benar
        session()->set('user', $user);
        log_message('debug', 'User session set: ' . print_r($user, true));
        return redirect()->to('/');
    }

    public function register()
    {
        return view('backend/auth/register');
    }

    public function processRegister()
    {
        // Validasi input
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data dari form
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->encryptPassword($this->request->getPost('password')),
        ];

        // Simpan data ke database
        $userModel = new UserModel();
        try {
            $userModel->save($data);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->remove('user');
        return redirect()->to('/login');
    }

    private function encryptPassword($password)
    {
        return sha1('jksdhf832746aiH{}{()&(*&(*' . md5($password) . 'HdfevgyDDw{}{}{;;*766&*&*');
    }

    private function verifyPassword($inputPassword, $storedPassword)
    {
        return $this->encryptPassword($inputPassword) === $storedPassword;
    }
}
