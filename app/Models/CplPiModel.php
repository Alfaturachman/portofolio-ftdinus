<?php

namespace App\Models;

use CodeIgniter\Model;

class CplPiModel extends Model
{
    protected $table = 'cpl_pi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kurikulum',
        'matkul',
        'kode_matkul',
        'id_matkul',
        'no_cpl',
        'cpl_indo',
        'cpl_inggris',
        'id_cpl',
        'no_pi',
        'isi_pi',
        'id_pi'
    ];

    /**
     * Get CPL data grouped by no_cpl
     * 
     * @return array
     */
    public function getCplGrouped()
    {
        // Using MAX or MIN to comply with ONLY_FULL_GROUP_BY
        $result = $this->select('no_cpl, MAX(cpl_indo) as cpl_indo, MAX(cpl_inggris) as cpl_inggris')
            ->groupBy('no_cpl')
            ->orderBy('no_cpl', 'ASC')
            ->findAll();

        $cplData = [];
        foreach ($result as $row) {
            $cplData[$row['no_cpl']] = [
                'cpl_indo' => $row['cpl_indo'],
                'cpl_inggris' => $row['cpl_inggris']
            ];
        }

        return $cplData;
    }

    public function getCplFullText($cplNo)
    {
        $result = $this->select('cpl_indo')
            ->where('no_cpl', $cplNo)
            ->first();

        return $result['cpl_indo'] ?? '';
    }
}
