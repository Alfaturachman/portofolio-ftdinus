<?php

namespace App\Models;

use CodeIgniter\Model;

class PiModel extends Model
{
    protected $table      = 'pi';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_cpl', 'no_pi', 'isi_ikcp'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
