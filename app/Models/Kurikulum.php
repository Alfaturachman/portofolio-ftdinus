<?php

namespace App\Models;

use CodeIgniter\Model;

class Kurikulum extends Model
{
    protected $table            = 'kurikulum';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'tahun_ajaran',
        'nama_kurikulum'
    ];
}