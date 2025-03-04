<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilAsesmenModel extends Model
{
    protected $table      = 'hasil_asesmen';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_porto',
        'file_tugas',
        'file_uts',
        'file_uas',
        'file_nilai_mk',
        'file_nilai_cpmk'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
}
