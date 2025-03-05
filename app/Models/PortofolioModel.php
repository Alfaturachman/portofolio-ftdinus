<?php

namespace App\Models;

use CodeIgniter\Model;

class PortofolioModel extends Model
{
    protected $table      = 'portofolio';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_user', 'kode_mk', 'nama_mk', 'npp', 'ins_time', 'upd_time'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';

    public function getPortofolioWithUserDetails($npp)
    {
        // Query untuk mendapatkan kode mata kuliah yang diampu oleh user saat ini
        $matkulDiampu = $this->db->table('matkul_diampu')
            ->select('kode_matkul')
            ->where('npp', $npp)
            ->get()
            ->getResultArray();

        // Ekstrak kode mata kuliah menjadi array
        $kodeMatkulArray = array_column($matkulDiampu, 'kode_matkul');

        // Jika tidak ada mata kuliah yang diampu, kembalikan array kosong
        if (empty($kodeMatkulArray)) {
            return [];
        }

        return $this->select('portofolio.id, portofolio.kode_mk, portofolio.nama_mk, users.nama as dosen_nama, portofolio.npp, portofolio.ins_time')
            // Join with users to get dosen name
            ->join('users', 'portofolio.npp = users.username')
            // Filter portofolio berdasarkan mata kuliah yang diampu user saat ini
            ->whereIn('portofolio.kode_mk', $kodeMatkulArray)
            // Group to avoid duplicates
            ->groupBy('portofolio.id, portofolio.kode_mk, portofolio.nama_mk, users.nama, portofolio.npp, portofolio.ins_time')
            // Ensure distinct portfolios
            ->distinct()
            ->findAll();
    }
}