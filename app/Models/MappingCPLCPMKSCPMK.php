<?php

namespace App\Models;

use CodeIgniter\Model;

class MappingCPLCPMKSCPMK extends Model
{
    protected $table            = 'mapping_cpl_cpmk_scpmk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'id_cpl',
        'id_cpmk',
        'id_sub_cpmk',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'id_cpl' => 'required|integer|is_not_unique[cpl.id]',
        'id_cpmk' => 'permit_empty|integer|is_not_unique[cpmk.id]',
        'id_sub_cpmk' => 'permit_empty|integer|is_not_unique[sub_cpmk.id]'
    ];

    /**
     * Mendapatkan mapping lengkap dengan detail
     */
    public function getCompleteMapping($id_portofolio)
    {
        return $this->select('
                mapping_cpl_cpmk_scpmk.,
                cpl.kode_cpl,
                cpl.narasi_cpl,
                cpmk.no_cpmk,
                cpmk.narasi_cpmk,
                sub_cpmk.no_sub_cpmk,
                sub_cpmk.narasi_sub_cpmk
            ')
            ->join('cpl', 'cpl.id = mapping_cpl_cpmk_scpmk.id_cpl', 'left')
            ->join('cpmk', 'cpmk.id = mapping_cpl_cpmk_scpmk.id_cpmk', 'left')
            ->join('sub_cpmk', 'sub_cpmk.id = mapping_cpl_cpmk_scpmk.id_sub_cpmk', 'left')
            ->where('mapping_cpl_cpmk_scpmk.id_portofolio', $id_portofolio)
            ->findAll();
    }

    public function getMapping($idPorto)
    {
        $rows = $this->where('id_portofolio', $idPorto)->findAll();
        $map  = [];
        foreach ($rows as $row) {
            $map[$row['id_cpmk']][$row['id_sub_cpmk']] = true;
        }
        return $map;
    }
}
