<?php

namespace App\Models;

use CodeIgniter\Model;

class MappingCpmkScpmkModel extends Model
{
    protected $table      = 'mapping_cpmk_scpmk';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_cpmk', 'id_scpmk', 'nilai'];

    protected $useTimestamps = false;

    public function getMappingData($idCpmk, $idScpmk)
    {
        return $this->where('id_cpmk', $idCpmk)
            ->where('id_scpmk', $idScpmk)
            ->first();
    }

    public function getAllMappings()
    {
        return $this->findAll();
    }
}
