<?php

namespace App\Models;

use CodeIgniter\Model;

class MK extends Model
{
    protected $table         = 'mk';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'kode_mk',
        'nama_mk',
        'kelp_mk',
        'teori',
        'praktek'
    ];
}