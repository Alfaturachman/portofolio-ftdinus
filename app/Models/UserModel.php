<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Field yang boleh diisi
    protected $allowedFields = ['username', 'password'];

    // Otomatis mengisi timestamp (created_at dan updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
    protected $dateFormat    = 'datetime';

    // Fungsi untuk enkripsi password
    public function encryptPassword($password)
    {
        return sha1('jksdhf832746aiH{}{()&(*&(*' . md5($password) . 'HdfevgyDDw{}{}{;;*766&*&*');
    }

    // Fungsi untuk verifikasi password
    public function verifyPassword($inputPassword, $storedPassword)
    {
        return $this->encryptPassword($inputPassword) === $storedPassword;
    }
}
