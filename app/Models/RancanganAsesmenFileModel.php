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

    protected $allowedFields = ['id_porto', 'kategori', 'kategori_file', 'file_pdf'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
