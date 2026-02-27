<?php

namespace App\Models;

use CodeIgniter\Model;

class MkCplPi extends Model
{
    protected $table         = 'mk_cpl_pi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mk',
        'id_pi',
        'id_cpl',
        'id_kurikulum',
        'id_prodi'
    ];
}