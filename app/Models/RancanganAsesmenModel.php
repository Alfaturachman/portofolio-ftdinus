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

    protected $allowedFields = ['id_cpmk', 'tugas', 'uts', 'uas'];

    protected $useTimestamps = false;
}
