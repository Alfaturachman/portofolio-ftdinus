<?php

namespace App\Models;

use CodeIgniter\Model;

class Pemetaan extends Model
{
    protected $table            = 'pemetaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id_portofolio',
        'id_cpl',
        'id_cpmk',
        'id_sub_cpmk',
        'is_active',
    ];

    // Timestamps - disabled because table doesn't have updated_at
    protected $useTimestamps = false;
}
