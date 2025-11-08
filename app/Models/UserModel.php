<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi
    protected $allowedFields = [
        'homebase',
        'nama',
        'username',
        'status',
        'id_staf',
        'password',
        'role'
    ];

    // Otomatis isi timestamp (ins_time dan upd_time)
    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
    protected $dateFormat    = 'datetime';

    // Fungsi untuk enkripsi password
    public function encryptPassword($password)
    {
        // Disarankan pakai password_hash(), tapi kalau kamu mau tetap custom, biarkan seperti ini
        return sha1('jksdhf832746aiH{}{()&(*&(*' . md5($password) . 'HdfevgyDDw{}{}{;;*766&*&*');
    }

    // Fungsi untuk verifikasi password
    public function verifyPassword($inputPassword, $storedPassword)
    {
        return $this->encryptPassword($inputPassword) === $storedPassword;
    }

    // Fungsi untuk mencari user berdasarkan username
    public function getByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    // Fungsi untuk mengecek role
    public function hasRole($userId, $role)
    {
        return $this->where(['id' => $userId, 'role' => $role])->first() !== null;
    }

    // Fungsi untuk mengambil semua user berdasarkan role
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }
}