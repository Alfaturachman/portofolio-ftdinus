<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCpmkModel extends Model
{
    protected $table      = 'sub_cpmk';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_scpmk', 'isi_scmpk'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
