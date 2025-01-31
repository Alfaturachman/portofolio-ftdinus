<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Field yang boleh diisi
    protected $allowedFields = ['username', 'email', 'password'];

    // Otomatis mengisi timestamp (created_at dan updated_at)
    protected $useTimestamps = true;

    // Format penyimpanan tanggal
    protected $dateFormat = 'datetime';

    // Hash password sebelum disimpan ke database
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Verifikasi password
    public function verifyPassword($inputPassword, $hashedPassword)
    {
        return password_verify($inputPassword, $hashedPassword);
    }
}
