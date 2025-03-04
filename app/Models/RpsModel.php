<?php

namespace App\Models;

use CodeIgniter\Model;

class RpsModel extends Model
{
    protected $table      = 'upload_rps';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'file_rps'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
