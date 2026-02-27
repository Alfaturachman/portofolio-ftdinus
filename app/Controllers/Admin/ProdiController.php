<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Prodi;

class ProdiController extends BaseController
{
    protected $prodiModel;

    public function __construct()
    {
        $this->prodiModel = new Prodi();
    }

    public function index()
    {
        return view('admin/prodi');
    }

    public function getData()
    {
        $data = $this->prodiModel->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'kode_prodi' => 'required|is_unique[prodi.kode_prodi]',
            'nama_prodi' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $this->prodiModel->save([
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'nama_prodi' => $this->request->getPost('nama_prodi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Prodi berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $prodi = $this->prodiModel->find($id);
        if (!$prodi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Prodi tidak ditemukan.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $prodi]);
    }

    public function update($id)
    {
        $rules = [
            'kode_prodi' => "required|is_unique[prodi.kode_prodi,id,{$id}]",
            'nama_prodi' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $this->prodiModel->update($id, [
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'nama_prodi' => $this->request->getPost('nama_prodi'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Prodi berhasil diperbarui.']);
    }

    public function delete($id)
    {
        $prodi = $this->prodiModel->find($id);
        if (!$prodi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Prodi tidak ditemukan.']);
        }

        $this->prodiModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Prodi berhasil dihapus.']);
    }
}