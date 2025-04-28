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

    public function getAllPortofolio()
    {
        return $this->select('portofolio.id, portofolio.kode_mk, portofolio.nama_mk, users.nama as dosen_nama, portofolio.npp, portofolio.ins_time')
            ->join('users', 'portofolio.npp = users.username')
            ->findAll();
    }

    public function getMatkulDiampuByUser($npp)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('matkul_diampu');
        
        // Fix: Include all selected columns in the GROUP BY clause
        return $builder->select('matkul_diampu.kode_matkul, matkul_diampu.matkul, matkul_diampu.kelp_matkul, matkul_diampu.tahun, matkul_diampu.semester, matkul_diampu.kode_ts')
            ->where('matkul_diampu.npp', $npp)
            ->groupBy('matkul_diampu.kelp_matkul, matkul_diampu.kode_matkul, matkul_diampu.matkul, matkul_diampu.tahun, matkul_diampu.semester, matkul_diampu.kode_ts')
            ->get()
            ->getResultArray();
    }

    public function getPortofolioCetakDetails($idPorto)
    {
        return $this->select('
        portofolio.id,
        portofolio.kode_mk,
        matkul_diampu.matkul AS nama_matkul,
        matkul_diampu.kelp_matkul,
        info_matkul.teori,
        info_matkul.praktek,
        users.id AS user_id,
        users.nama AS nama_dosen,
        portofolio.npp,
        portofolio.ins_time,
        identitas_matkul.prasyarat_mk,
        identitas_matkul.topik_perkuliahan,
        evaluasi_perkuliahan.isi_evaluasi
    ')
            ->join('matkul_diampu', 'portofolio.kode_mk = matkul_diampu.kode_matkul', 'left')
            ->join('info_matkul', 'portofolio.kode_mk = info_matkul.kode_matkul', 'left')
            ->join('users', 'portofolio.npp = users.username', 'left')
            ->join('identitas_matkul', 'portofolio.id = identitas_matkul.id_porto', 'left')
            ->join('evaluasi_perkuliahan', 'portofolio.id = evaluasi_perkuliahan.id_porto', 'left')
            ->where('portofolio.id', $idPorto)
            ->first();
    }

    public function getMatkulDetail($kode_matkul)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('matkul_diampu');
        
        return $builder->select('kode_matkul, matkul')
            ->where('kode_matkul', $kode_matkul)
            ->get()
            ->getRowArray();
    }

    public function getPortofolioByKodeMK($kode_matkul)
    {
        return $this->select('portofolio.id, portofolio.kode_mk, portofolio.nama_mk, portofolio.npp, users.nama, portofolio.ins_time')
            ->join('users', 'portofolio.npp = users.username')
            ->where('portofolio.kode_mk', $kode_matkul)
            ->findAll();
    }

    public function checkMahasiswaKelasExists($kode_matkul, $kelp_matkul, $kode_ts)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('mahasiswa_kelas');
        
        $result = $builder->where([
            'kode_matkul' => $kode_matkul,
            'kelp_matkul' => $kelp_matkul,
            'kode_ts' => $kode_ts
        ])
        ->countAllResults();
        
        return $result > 0;
    }
}