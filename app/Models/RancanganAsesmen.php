<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganAsesmen extends Model
{
    protected $table            = 'rancangan_asesmen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'id_cpmk',
        'jenis_asesmen',
        'file_soal',
        'file_rubrik',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'id_cpmk' => 'required|integer|is_not_unique[cpmk.id]',
        'jenis_asesmen' => 'required|in_list[tugas,uts,uas]',
        'file_soal' => 'required|max_length[255]',
        'file_rubrik' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'jenis_asesmen' => [
            'required' => 'Jenis asesmen harus diisi',
            'in_list' => 'Jenis asesmen harus tugas, uts, atau uas'
        ]
    ];

    public function getAssessmentData($idPorto)
    {
        $rows = $this->db->table('rancangan_asesmen ra')
            ->select('ra.id_cpmk, c.no_cpmk,
               MAX(CASE WHEN ra.jenis_asesmen="tugas" THEN 1 ELSE 0 END) AS tugas,
               MAX(CASE WHEN ra.jenis_asesmen="uts"   THEN 1 ELSE 0 END) AS uts,
               MAX(CASE WHEN ra.jenis_asesmen="uas"   THEN 1 ELSE 0 END) AS uas')
            ->join('cpmk c', 'c.id = ra.id_cpmk')
            ->where('ra.id_portofolio', $idPorto)
            ->groupBy('ra.id_cpmk')
            ->get()->getResultArray();
        return $rows;
    }
}
