<?php

namespace App\Models;

use CodeIgniter\Model;

class CplModel extends Model
{
    protected $table      = 'cpl';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_cpl', 'isi_cpl'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
