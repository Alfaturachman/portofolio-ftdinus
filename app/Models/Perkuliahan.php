<?php

namespace App\Models;

use CodeIgniter\Model;

class Perkuliahan extends Model
{
    protected $table         = 'perkuliahan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mk',
        'id_users',
        'id_kurikulum',
        'semester',
        'kode_kelas',
        'tahun_akademik'
    ];
}