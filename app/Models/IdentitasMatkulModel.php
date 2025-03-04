<?php

namespace App\Models;

use CodeIgniter\Model;

class IdentitasMatkulModel extends Model
{
    protected $table      = 'identitas_matkul';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'prasyarat_mk', 'topik_perkuliahan'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
