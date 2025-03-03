<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganAsesmenFileModel extends Model
{
    protected $table      = 'rancangan_asesmen_file';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_asesmen', 'kategori', 'kategori_file', 'nama_file'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
