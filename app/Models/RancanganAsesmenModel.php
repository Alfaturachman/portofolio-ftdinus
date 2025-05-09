<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganAsesmenModel extends Model
{
    protected $table      = 'rancangan_asesmen';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'id_cpmk', 'id_scpmk', 'tugas', 'uts', 'uas'];

    protected $useTimestamps = false;

    public function getAssessmentData($idPorto)
    {
        // Assuming you have a table 'rancangan_asesmen' with these fields
        $builder = $this->db->table('rancangan_asesmen');
        $builder->select('rancangan_asesmen.*, cpmk.id as id_cpmk, cpmk.no_cpmk, sub_cpmk.id as id_scpmk, sub_cpmk.no_scpmk');
        $builder->join('cpmk', 'cpmk.id = rancangan_asesmen.id_cpmk');
        $builder->join('sub_cpmk', 'sub_cpmk.id = rancangan_asesmen.id_scpmk');
        $builder->where('cpmk.id_porto', $idPorto);

        return $builder->get()->getResultArray();
    }
}
