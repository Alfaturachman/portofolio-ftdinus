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

    public function getMapping($id_porto)
    {
        $query = $this->db->table($this->table)
            ->select('mapping_cpmk_scpmk.id_cpmk, mapping_cpmk_scpmk.id_scpmk, mapping_cpmk_scpmk.nilai')
            ->join('cpmk', 'cpmk.id = mapping_cpmk_scpmk.id_cpmk')
            ->where('cpmk.id_porto', $id_porto)
            ->get();

        $result = $query->getResultArray();

        // Format data untuk kebutuhan view
        $mapping = [];
        foreach ($result as $row) {
            $mapping[$row['id_cpmk']][$row['id_scpmk']] = $row['nilai'];
        }

        return $mapping;
    }

    public function getAllMappings()
    {
        return $this->findAll();
    }
}
