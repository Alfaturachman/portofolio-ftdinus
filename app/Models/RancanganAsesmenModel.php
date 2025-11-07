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

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
    protected $deletedField  = 'deleted_at';

    public function getAssessmentData($idPorto)
    {
        $builder = $this->db->table('rancangan_asesmen');
        $builder->select('rancangan_asesmen.*, cpmk.id as id_cpmk, cpmk.no_cpmk, sub_cpmk.id as id_scpmk, sub_cpmk.no_scpmk');
        $builder->join('cpmk', 'cpmk.id = rancangan_asesmen.id_cpmk', 'left');
        $builder->join('sub_cpmk', 'sub_cpmk.id = rancangan_asesmen.id_scpmk', 'left');
        $builder->where('rancangan_asesmen.id_porto', $idPorto);

        return $builder->get()->getResultArray();
    }
}
