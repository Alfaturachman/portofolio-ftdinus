<?php

namespace App\Models;

use CodeIgniter\Model;

class Evaluasi extends Model
{
    protected $table            = 'evaluasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'id_cpmk',
        'rata_rata',
        'isi_cpmk',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'id_cpmk' => 'required|integer|is_not_unique[cpmk.id]',
        'rata_rata' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'isi_cpmk' => 'required'
    ];

    protected $validationMessages = [
        'rata_rata' => [
            'required' => 'Rata-rata nilai harus diisi',
            'decimal' => 'Rata-rata harus berupa angka desimal',
            'greater_than_equal_to' => 'Rata-rata minimal 0',
            'less_than_equal_to' => 'Rata-rata maksimal 100'
        ]
    ];

    /**
     * Mendapatkan evaluasi dengan detail CPMK
     */
    public function getWithCPMK($id_portofolio = null)
    {
        $this->select('evaluasi.*, cpmk.no_cpmk, cpmk.narasi_cpmk');
        $this->join('cpmk', 'cpmk.id = evaluasi.id_cpmk');

        if ($id_portofolio !== null) {
            $this->where('evaluasi.id_portofolio', $id_portofolio);
        }

        return $this->findAll();
    }

    /**
     * Mendapatkan statistik evaluasi per portofolio
     */
    public function getStatistik($id_portofolio)
    {
        return $this->select('
                AVG(rata_rata) as rata_rata_total,
                MIN(rata_rata) as rata_rata_terendah,
                MAX(rata_rata) as rata_rata_tertinggi,
                COUNT(*) as total_cpmk_dievaluasi
            ')
            ->where('id_portofolio', $id_portofolio)
            ->first();
    }
}
