<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganSoal extends Model
{
    protected $table            = 'rancangan_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'id_asesmen',
        'nomor_soal',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'id_asesmen' => 'required|integer|is_not_unique[rancangan_asesmen.id]',
        'nomor_soal' => 'required|integer'
    ];

    /**
     * Mendapatkan soal dengan detail asesmen
     */
    public function getWithAsesmen($id_portofolio = null)
    {
        $this->select('rancangan_soal.*, rancangan_asesmen.jenis_asesmen');
        $this->join('rancangan_asesmen', 'rancangan_asesmen.id = rancangan_soal.id_asesmen');

        if ($id_portofolio !== null) {
            $this->where('rancangan_soal.id_portofolio', $id_portofolio);
        }

        return $this->orderBy('nomor_soal', 'ASC')->findAll();
    }
}
