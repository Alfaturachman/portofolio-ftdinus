<?php

namespace App\Models;

use CodeIgniter\Model;

class MappingCpmkPiModel extends Model
{
    protected $table      = 'mapping_cpmk_pi';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_cpmk', 'id_pi', 'nilai'];

    protected $useTimestamps = false;
}
