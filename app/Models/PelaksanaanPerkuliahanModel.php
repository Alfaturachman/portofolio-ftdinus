<?php

namespace App\Models;

use CodeIgniter\Model;

class PelaksanaanPerkuliahanModel extends Model
{
    protected $table      = 'pelaksanaan_perkuliahan';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_porto',
        'file_kontrak',
        'file_realisasi',
        'file_kehadiran'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
