<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCPMK extends Model
{
    protected $table            = 'sub_cpmk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_cpmk',
        'no_sub_cpmk',
        'narasi_sub_cpmk',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_cpmk' => 'required|integer|is_not_unique[cpmk.id]',
        'no_sub_cpmk' => 'required|max_length[10]',
        'narasi_sub_cpmk' => 'required'
    ];

    protected $validationMessages = [
        'id_cpmk' => [
            'required' => 'ID CPMK harus diisi',
            'is_not_unique' => 'CPMK tidak ditemukan'
        ],
        'no_sub_cpmk' => [
            'required' => 'Nomor Sub-CPMK harus diisi'
        ],
        'narasi_sub_cpmk' => [
            'required' => 'Narasi Sub-CPMK harus diisi'
        ]
    ];

    /**
     * Mendapatkan Sub CPMK berdasarkan ID CPMK
     */
    public function getByIdCPMK($id_cpmk)
    {
        return $this->where('id_cpmk', $id_cpmk)->findAll();
    }

    /**
     * Mendapatkan Sub CPMK dengan data CPMK
     */
    public function getWithCPMK($id = null)
    {
        $this->select('sub_cpmk.*, cpmk.no_cpmk, cpmk.narasi_cpmk');
        $this->join('cpmk', 'cpmk.id = sub_cpmk.id_cpmk');

        if ($id !== null) {
            return $this->find($id);
        }

        return $this->findAll();
    }
}
