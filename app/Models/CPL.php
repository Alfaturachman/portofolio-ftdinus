<?php

namespace App\Models;

use CodeIgniter\Model;

class CPL extends Model
{
    protected $table         = 'cpl';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_prodi',
        'id_kurikulum',
        'no_cpl',
        'cpl_indo',
        'cpl_inggris',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}