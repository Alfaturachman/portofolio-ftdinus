<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluasiKesimpulan extends Model
{
    protected $table            = 'evaluasi_kesimpulan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'kesimpulan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
