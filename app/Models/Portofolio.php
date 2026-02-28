<?php

namespace App\Models;

use CodeIgniter\Model;

class Portofolio extends Model
{
    protected $table         = 'portofolio';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mapping',
        'id_perkuliahan',
        'last_step'
    ];

    protected $useAutoIncrement = false;

    public function getPortofolioCetakDetails($idPorto)
    {
        return $this->db->table('portofolio p')
            ->select('
            p.id, p.last_step,
            mk.nama_mk AS nama_matkul,
            mk.kode_mk, mk.kelp_mk AS kelp_matkul,
            mk.teori, mk.praktek,
            pk.semester, pk.kode_kelas, pk.tahun_akademik,
            u.npp, u.nama_lengkap AS nama_dosen,
            pr.nama_prodi AS prodi,
            imk.topik_perkuliahan, imk.mk_prasyarat AS prasyarat_mk
        ')
            ->join('perkuliahan pk', 'pk.id = p.id_perkuliahan')
            ->join('mk', 'mk.id = pk.id_mk')
            ->join('users u', 'u.npp = pk.id_users')

            // ambil prodi lewat mk_cpl_pi
            ->join('mk_cpl_pi mcp', 'mcp.id_mk = mk.id', 'left')
            ->join('prodi pr', 'pr.id = mcp.id_prodi', 'left')

            ->join('informasi_mk imk', 'imk.id_portofolio = p.id', 'left')
            ->where('p.id', $idPorto)
            ->get()
            ->getRowArray();
    }
}
