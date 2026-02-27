<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Kurikulum;

class KurikulumController extends BaseController
{
    protected $kurikulumModel;

    public function __construct()
    {
        $this->kurikulumModel = new Kurikulum();
    }

    public function index()
    {
        return view('admin/kurikulum');
    }

    public function getData()
    {
        $data = $this->kurikulumModel->findAll();
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $rules = [
            'tahun_ajaran'   => 'required',
            'nama_kurikulum' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $this->kurikulumModel->save([
            'tahun_ajaran'   => $this->request->getPost('tahun_ajaran'),
            'nama_kurikulum' => $this->request->getPost('nama_kurikulum'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Kurikulum berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $kurikulum = $this->kurikulumModel->find($id);
        if (!$kurikulum) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kurikulum tidak ditemukan.']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $kurikulum]);
    }

    public function update($id)
    {
        $rules = [
            'tahun_ajaran'   => 'required',
            'nama_kurikulum' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getErrors(),
            ]);
        }

        $this->kurikulumModel->update($id, [
            'tahun_ajaran'   => $this->request->getPost('tahun_ajaran'),
            'nama_kurikulum' => $this->request->getPost('nama_kurikulum'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Kurikulum berhasil diperbarui.']);
    }

    public function delete($id)
    {
        $kurikulum = $this->kurikulumModel->find($id);
        if (!$kurikulum) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kurikulum tidak ditemukan.']);
        }

        $this->kurikulumModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Kurikulum berhasil dihapus.']);
    }
}