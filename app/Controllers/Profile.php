<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProfileModel;
use CodeIgniter\HTTP\ResponseInterface;

class Profile extends BaseController
{
    protected $profileModel;

    public function __construct()
    {
        $this->profileModel = new ProfileModel();
    }

    public function index()
    {
        $userId = session()->get('user')['id'] ?? null;

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $profile = $this->profileModel->where('user_id', $userId)->first();

        return view('backend/profile/index', ['profile' => $profile]);
    }

    public function edit()
    {
        $userId = session()->get('user')['id'] ?? null;
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $profile = $this->profileModel->where('user_id', $userId)->first();
        return view('backend/profile/edit', ['profile' => $profile]);
    }

    public function update()
    {
        $userId = session()->get('user')['id'] ?? null;
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[10]|max_length[15]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->profileModel->where('user_id', $userId)->set([
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ])->update();

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
