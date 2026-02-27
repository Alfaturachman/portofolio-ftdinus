<?php

namespace App\Models;

use CodeIgniter\Model;

class Portofolio extends Model
{
    protected $table         = 'portofolio';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mapping',
        'id_perkuliahan',
        'last_step'
    ];
}
