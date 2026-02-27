<?php

namespace App\Controllers;
use App\Models\Users;

use CodeIgniter\Controller;
class Login extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function proses()
{
    $session = session();
    $model   = new Users(); // pastikan pakai model yang benar

    $npp      = $this->request->getPost('npp');
    $password = $this->request->getPost('password');

    $user = $model->where('npp', $npp)->first();

    if ($user) {

        if (password_verify($password, $user['password'])) {

            // set session
            $session->set([
                'npp'          => $user['npp'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role'         => $user['role'],
                'isLoggedIn'   => true
            ]);

            // redirect berdasarkan role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($user['role'] === 'dosen') {
                return redirect()->to('/dosen/dashboard');
            }

        } else {
            return redirect()->back()->with('error', 'Password salah.');
        }

    } else {
        return redirect()->back()->with('error', 'Username tidak ditemukan.');
    }
}

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}