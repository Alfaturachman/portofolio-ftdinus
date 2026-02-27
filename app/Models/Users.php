<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'npp';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'npp',
        'password',
        'nama_lengkap',
        'role',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}
