<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganAsesmen extends Model
{
    protected $table            = 'rancangan_asesmen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'id_cpmk',
        'jenis_asesmen',
        'file_soal',
        'file_rubrik',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'id_cpmk' => 'required|integer|is_not_unique[cpmk.id]',
        'jenis_asesmen' => 'required|in_list[tugas,uts,uas]',
        'file_soal' => 'required|max_length[255]',
        'file_rubrik' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'jenis_asesmen' => [
            'required' => 'Jenis asesmen harus diisi',
            'in_list' => 'Jenis asesmen harus tugas, uts, atau uas'
        ]
    ];

    /**
     * Mendapatkan asesmen berdasarkan jenis
     */
    public function getByJenis($id_portofolio, $jenis)
    {
        return $this->where('id_portofolio', $id_portofolio)
            ->where('jenis_asesmen', $jenis)
            ->findAll();
    }

    /**
     * Mendapatkan asesmen dengan detail CPMK
     */
    public function getWithCPMK($id_portofolio = null)
    {
        $this->select('rancangan_asesmen.*, cpmk.no_cpmk, cpmk.narasi_cpmk');
        $this->join('cpmk', 'cpmk.id = rancangan_asesmen.id_cpmk');

        if ($id_portofolio !== null) {
            $this->where('rancangan_asesmen.id_portofolio', $id_portofolio);
        }

        return $this->findAll();
    }

    /**
     * Mendapatkan soal-soal dari asesmen
     */
    public function getWithSoal($id_asesmen)
    {
        $rancanganSoalModel = new RancanganSoal();
        $asesmen = $this->find($id_asesmen);

        if ($asesmen) {
            $asesmen['soal'] = $rancanganSoalModel->where('id_asesmen', $id_asesmen)
                ->orderBy('nomor_soal', 'ASC')
                ->findAll();
        }

        return $asesmen;
    }
}
