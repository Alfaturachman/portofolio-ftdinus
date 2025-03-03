<?php

namespace App\Models;

use CodeIgniter\Model;

class CpmkModel extends Model
{
    protected $table      = 'cpmk';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_cpmk', 'isi_cpmk'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
