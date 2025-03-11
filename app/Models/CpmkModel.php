<?php

namespace App\Models;

use CodeIgniter\Model;

class CpmkModel extends Model
{
    protected $table      = 'cpmk';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_cpmk', 'isi_cpmk'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';

    public function getCpmkByPorto($id_porto)
    {
        return $this->where('id_porto', $id_porto)->findAll();
    }

    public function getCpmkByCpl($idPorto, $cplId)
    {
        // Untuk implementasi ini, kita perlu struktur tabel yang menghubungkan CPMK dengan CPL
        // Jika belum ada, Anda perlu merancang relasi antara CPMK dan CPL terlebih dahulu
        // Ini adalah contoh dasar dengan asumsi ada kolom id_cpl di tabel cpmk
        return $this->select('id, no_cpmk, isi_cpmk')
            ->where('id_porto', $idPorto)
            ->where('id_cpl', $cplId)
            ->orderBy('no_cpmk', 'ASC')
            ->findAll();
    }
}
