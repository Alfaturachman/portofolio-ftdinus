<?php

namespace App\Models;

use CodeIgniter\Model;

class Pi extends Model
{
    protected $table         = 'pi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_prodi',
        'id_kurikulum',
        'no_pi',
        'isi_pi'
    ];
}