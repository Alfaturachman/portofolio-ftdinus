<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Portofolio extends BaseController
{
    public function index(): string
    {
        return view('backend/form-portofolio/index');
    }

    public function info_matkul(): string
    {
        return view('backend/form-portofolio/info-matkul');
    }

    public function saveInfoMatkul()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_mk' => 'required',
            'kode_mk' => 'required',
            'kelompok_mk' => 'required',
            'sks_teori' => 'required|numeric',
            'sks_praktik' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil data dari request
        $data = [
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'kelompok_mk' => $this->request->getPost('kelompok_mk'),
            'sks_teori' => $this->request->getPost('sks_teori'),
            'sks_praktik' => $this->request->getPost('sks_praktik'),
            'mk_prasyarat' => $this->request->getPost('mk_prasyarat'),
        ];

        // Simpan ke session
        session()->set('info_matkul', $data);

        // Menampilkan log data yang disimpan ke session
        log_message('info', 'Data Mata Kuliah disimpan ke session: ' . json_encode($data));

        // Redirect ke langkah berikutnya
        return redirect()->to('portofolio-form/topik-perkuliahan');
    }

    public function topik_perkuliahan(): string
    {
        return view('backend/form-portofolio/topik-perkuliahan');
    }
}
