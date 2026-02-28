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

    public function getCpmkByPorto($idPorto)
    {
        return $this->db->table('cpmk c')
            ->select('c.id, c.no_cpmk, c.narasi_cpmk AS isi_cpmk,
                     IFNULL(e.rata_rata, 0) AS avg_cpmk')
            ->join('evaluasi e', 'e.id_cpmk = c.id AND e.id_portofolio = c.id_portofolio', 'left')
            ->where('c.id_portofolio', $idPorto)
            ->orderBy('c.no_cpmk')
            ->get()->getResultArray();
    }
}
