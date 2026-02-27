<?php

namespace App\Models;

use CodeIgniter\Model;

class CPMK extends Model
{
    protected $table            = 'cpmk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_portofolio',
        'no_cpmk',
        'id_cpl',
        'narasi_cpmk',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'no_cpmk' => 'required|max_length[10]',
        'id_cpl' => 'required|integer|is_not_unique[cpl.id]',
        'narasi_cpmk' => 'required'
    ];

    protected $validationMessages = [
        'id_portofolio' => [
            'required' => 'ID Portofolio harus diisi',
            'is_not_unique' => 'Portofolio tidak ditemukan'
        ],
        'no_cpmk' => [
            'required' => 'Nomor CPMK harus diisi',
            'max_length' => 'Nomor CPMK maksimal 10 karakter'
        ],
        'id_cpl' => [
            'required' => 'ID CPL harus diisi',
            'is_not_unique' => 'CPL tidak ditemukan'
        ],
        'narasi_cpmk' => [
            'required' => 'Narasi CPMK harus diisi'
        ]
    ];

    /**
     * Mendapatkan CPMK berdasarkan ID portofolio
     */
    public function getByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->findAll();
    }

    /**
     * Mendapatkan CPMK dengan relasi
     */
    public function getWithRelations($id_portofolio = null)
    {
        $this->select('cpmk.*, cpl.kode_cpl, cpl.narasi_cpl');
        $this->join('cpl', 'cpl.id = cpmk.id_cpl');

        if ($id_portofolio !== null) {
            $this->where('cpmk.id_portofolio', $id_portofolio);
        }

        return $this->findAll();
    }

    /**
     * Mendapatkan Sub CPMK berdasarkan CPMK
     */
    public function getWithSubCPMK($id_cpmk)
    {
        $subCPMKModel = new SubCPMK();
        $cpmk = $this->find($id_cpmk);

        if ($cpmk) {
            $cpmk['sub_cpmk'] = $subCPMKModel->where('id_cpmk', $id_cpmk)->findAll();
        }

        return $cpmk;
    }
}
