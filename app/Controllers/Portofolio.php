<?php

namespace App\Controllers;

class Portofolio extends BaseController
{
    public function index(): string
    {
        return view('backend/form-portofolio/index');
    }

    public function info_matkul(): string
    {
        // Data statis mata kuliah
        $mataKuliah = [
            ['nama_mk' => 'Pemrograman', 'kode_mk' => 'MK001', 'kelompok_mk' => 'A', 'sks_teori' => 3, 'sks_praktik' => 1],
            ['nama_mk' => 'Manajemen', 'kode_mk' => 'MK002', 'kelompok_mk' => 'B', 'sks_teori' => 2, 'sks_praktik' => 2],
        ];

        // Data tambahan dari session (jika ada)
        $infoMatkul = session()->get('info_matkul') ?? [];

        // Kirim data ke view
        return view('backend/form-portofolio/info-matkul', [
            'mataKuliah' => $mataKuliah,
            'infoMatkul' => $infoMatkul,
        ]);
    }

    public function saveInfoMatkul()
    {
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

        $data = [
            'nama_mk' => $this->request->getPost('nama_mk'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'kelompok_mk' => $this->request->getPost('kelompok_mk'),
            'sks_teori' => $this->request->getPost('sks_teori'),
            'sks_praktik' => $this->request->getPost('sks_praktik'),
            'mk_prasyarat' => $this->request->getPost('mk_prasyarat'),
        ];

        session()->set('info_matkul', $data);

        log_message('info', 'Data Mata Kuliah disimpan ke session: ' . json_encode($data));

        return redirect()->to('portofolio-form/topik-perkuliahan');
    }

    public function topik_perkuliahan(): string
    {
        return view('backend/form-portofolio/topik-perkuliahan');
    }

    public function cpl_ikcp(): string
    {
        return view('backend/form-portofolio/cpl-ikcp');
    }

    public function cpmk_subcpmk(): string
    {
        return view('backend/form-portofolio/cpmk-subcpmk');
    }

    public function cetak(): string
    {
        return view('backend/form-portofolio/cetak');
    }

    public function upload_rps(): string
    {
        return view('backend/form-portofolio/upload-rps');
    }

    public function rancangan_asesmen(): string
    {
        return view('backend/form-portofolio/rancangan-asesmen');
    }

    public function deleteSession()
    {
        session()->remove('info_matkul');
        log_message('info', 'Session info_matkul telah dihapus.');
        return redirect()->to('portofolio-form/');
    }
}
