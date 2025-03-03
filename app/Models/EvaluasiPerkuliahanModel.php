<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluasiPerkuliahanModel extends Model
{
    protected $table      = 'evaluasi_perkuliahan';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'isi_evaluasi'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
