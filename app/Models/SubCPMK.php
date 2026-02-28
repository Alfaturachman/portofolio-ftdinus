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

    public function getSubCpmkByPorto($idPorto)
    {
        return $this->db->table('sub_cpmk sc')
            ->select('sc.id, sc.no_sub_cpmk AS no_scpmk,
                      sc.narasi_sub_cpmk AS isi_scmpk, sc.id_cpmk')
            ->join('cpmk c', 'c.id = sc.id_cpmk')
            ->where('c.id_portofolio', $idPorto)
            ->orderBy('sc.no_sub_cpmk')
            ->get()->getResultArray();
    }
}
