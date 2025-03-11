<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCpmkModel extends Model
{
    protected $table      = 'sub_cpmk';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_scpmk', 'isi_scmpk'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';

    public function getSubCpmkByPorto($id_porto)
    {
        return $this->where('id_porto', $id_porto)->findAll();
    }

    public function getSubCpmkByCpmk($idPorto, $cpmkId)
    {
        // Untuk implementasi ini, kita perlu struktur tabel yang menghubungkan Sub-CPMK dengan CPMK
        // Jika belum ada, Anda perlu merancang relasi antara Sub-CPMK dan CPMK terlebih dahulu
        // Ini adalah contoh dasar dengan asumsi ada kolom id_cpmk di tabel sub_cpmk
        return $this->select('id, no_scpmk, isi_scmpk')
            ->where('id_porto', $idPorto)
            ->where('id_cpmk', $cpmkId)
            ->orderBy('no_scpmk', 'ASC')
            ->findAll();
    }
}
