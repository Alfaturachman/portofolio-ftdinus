<?php

namespace App\Models;

use CodeIgniter\Model;

class RancanganSoalModel extends Model
{
    protected $table            = 'rancangan_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_porto',
        'id_cpmk',
        'kategori_soal', // Field baru yang ditambahkan
        'no_soal',
        'nilai'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_porto' => 'permit_empty|integer',
        'id_cpmk'  => 'permit_empty|integer',
        'kategori_soal' => 'permit_empty|in_list[Tugas,UTS,UAS]', // Validasi untuk kategori_soal
        'no_soal'  => 'permit_empty|max_length[255]',
        'nilai'    => 'permit_empty|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'id_porto' => [
            'integer' => 'ID Portofolio harus berupa angka'
        ],
        'id_cpmk' => [
            'integer' => 'ID CPMK harus berupa angka'
        ],
        'kategori_soal' => [
            'in_list' => 'Kategori soal harus berupa Tugas, UTS, atau UAS'
        ],
        'no_soal' => [
            'max_length' => 'Nomor soal tidak boleh lebih dari 255 karakter'
        ],
        'nilai' => [
            'in_list' => 'Nilai harus 0 atau 1'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get rancangan soal by portofolio ID
     *
     * @param int $portofolioId
     * @return array
     */
    public function getRancanganSoalByPortofolio($portofolioId)
    {
        return $this->where('id_porto', $portofolioId)
            ->orderBy('no_soal', 'ASC')
            ->findAll();
    }

    /**
     * Get rancangan soal by portofolio ID with CPMK details
     *
     * @param int $portofolioId
     * @return array
     */
    public function getRancanganSoalWithCpmk($portofolioId)
    {
        return $this->select('rancangan_soal.*, cpmk.no_cpmk, cpmk.isi_cpmk')
            ->join('cpmk', 'cpmk.id = rancangan_soal.id_cpmk', 'left')
            ->where('rancangan_soal.id_porto', $portofolioId)
            ->orderBy('rancangan_soal.no_soal', 'ASC')
            ->findAll();
    }

    /**
     * Get rancangan soal by assessment type
     *
     * @param int $portofolioId
     * @param string $assessmentType (Tugas, UTS, UAS)
     * @return array
     */
    public function getRancanganSoalByAssessmentType($portofolioId, $assessmentType)
    {
        return $this->where('id_porto', $portofolioId)
            ->like('no_soal', $assessmentType, 'after')
            ->orderBy('no_soal', 'ASC')
            ->findAll();
    }

    /**
     * Get rancangan soal grouped by assessment type
     *
     * @param int $portofolioId
     * @return array
     */
    public function getRancanganSoalGrouped($portofolioId)
    {
        $data = $this->getRancanganSoalWithCpmk($portofolioId);

        $grouped = [
            'Tugas' => [],
            'UTS' => [],
            'UAS' => []
        ];

        foreach ($data as $item) {
            $noSoal = $item['no_soal'];

            if (strpos($noSoal, 'Tugas') === 0) {
                $grouped['Tugas'][] = $item;
            } elseif (strpos($noSoal, 'UTS') === 0) {
                $grouped['UTS'][] = $item;
            } elseif (strpos($noSoal, 'UAS') === 0) {
                $grouped['UAS'][] = $item;
            }
        }

        return $grouped;
    }

    /**
     * Get rancangan soal statistics by portofolio
     *
     * @param int $portofolioId
     * @return array
     */
    public function getRancanganSoalStats($portofolioId)
    {
        $builder = $this->db->table($this->table);

        // Total soal
        $totalSoal = $builder->where('id_porto', $portofolioId)->countAllResults(false);

        // Total soal yang tercentang (nilai = 1)
        $totalTercentang = $builder->where('id_porto', $portofolioId)
            ->where('nilai', 1)
            ->countAllResults(false);

        // Total soal per kategori
        $stats = [
            'total_soal' => $totalSoal,
            'total_tercentang' => $totalTercentang,
            'tugas' => $this->countSoalByType($portofolioId, 'Tugas'),
            'uts' => $this->countSoalByType($portofolioId, 'UTS'),
            'uas' => $this->countSoalByType($portofolioId, 'UAS')
        ];

        return $stats;
    }

    /**
     * Count soal by assessment type
     *
     * @param int $portofolioId
     * @param string $type
     * @return int
     */
    private function countSoalByType($portofolioId, $type)
    {
        return $this->where('id_porto', $portofolioId)
            ->like('no_soal', $type, 'after')
            ->countAllResults();
    }

    /**
     * Delete rancangan soal by portofolio ID
     *
     * @param int $portofolioId
     * @return bool
     */
    public function deleteByPortofolio($portofolioId)
    {
        return $this->where('id_porto', $portofolioId)->delete();
    }

    /**
     * Get CPMK mapping for specific soal
     *
     * @param int $portofolioId
     * @param string $noSoal
     * @return array
     */
    public function getCpmkMappingForSoal($portofolioId, $noSoal)
    {
        return $this->select('rancangan_soal.*, cpmk.no_cpmk, cpmk.isi_cpmk')
            ->join('cpmk', 'cpmk.id = rancangan_soal.id_cpmk', 'left')
            ->where('rancangan_soal.id_porto', $portofolioId)
            ->where('rancangan_soal.no_soal', $noSoal)
            ->where('rancangan_soal.nilai', 1)
            ->findAll();
    }

    /**
     * Update nilai rancangan soal
     *
     * @param int $portofolioId
     * @param string $noSoal
     * @param int $cpmkId
     * @param int $nilai
     * @return bool
     */
    public function updateNilaiSoal($portofolioId, $noSoal, $cpmkId, $nilai)
    {
        return $this->where('id_porto', $portofolioId)
            ->where('no_soal', $noSoal)
            ->where('id_cpmk', $cpmkId)
            ->set('nilai', $nilai)
            ->update();
    }

    /**
     * Check if soal exists
     *
     * @param int $portofolioId
     * @param string $noSoal
     * @param int $cpmkId
     * @return bool
     */
    public function soalExists($portofolioId, $noSoal, $cpmkId)
    {
        $result = $this->where('id_porto', $portofolioId)
            ->where('no_soal', $noSoal)
            ->where('id_cpmk', $cpmkId)
            ->first();

        return $result !== null;
    }

    /**
     * Get unique soal numbers by assessment type
     *
     * @param int $portofolioId
     * @param string $assessmentType
     * @return array
     */
    public function getUniqueSoalNumbers($portofolioId, $assessmentType)
    {
        $results = $this->select('no_soal')
            ->where('id_porto', $portofolioId)
            ->like('no_soal', $assessmentType, 'after')
            ->groupBy('no_soal')
            ->orderBy('no_soal', 'ASC')
            ->findAll();

        return array_column($results, 'no_soal');
    }

    /**
     * Get all soal for a specific portofolio
     *
     * @param int $idPorto
     * @return array
     */
    public function getAssessmentSoalData($idPorto)
    {
        $builder = $this->db->table('rancangan_soal');
        $builder->select('rancangan_soal.*, cpmk.no_cpmk');
        $builder->join('cpmk', 'cpmk.id = rancangan_soal.id_cpmk', 'left');
        $builder->where('rancangan_soal.id_porto', $idPorto);
        $builder->orderBy('kategori_soal, no_soal');

        return $builder->get()->getResultArray();
    }
}
