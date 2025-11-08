<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminRole implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Periksa apakah user sudah login
        if (!session()->get('UserSession') || !session()->get('UserSession')['logged_in']) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil informasi user dari session
        $userSession = session()->get('UserSession');
        $userId = $userSession['id_user'];

        // Load model user
        $userModel = new \App\Models\UserModel();
        
        // Cek apakah user memiliki role admin
        $user = $userModel->find($userId);
        
        if (!$user || $user['role'] !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Jika semua kondisi terpenuhi, lanjutkan ke controller
        return true;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah controller selesai dieksekusi
    }
}