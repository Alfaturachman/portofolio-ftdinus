<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportMatkulModel extends Model
{
    protected $table = 'info_matkul';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'matakuliah', 'kode_matkul', 'kelp_matkul', 'smt_matkul',
        'jenis_matkul', 'teori', 'praktek', 'tipe_matkul',
        'kurikulum', 'prodi', 'jenjang', 'fakultas',
        'ins_time', 'upd_time'
    ];
    protected $useTimestamps = false;
    
    public function insertBatchData(array $data)
    {
        // Set batch size for insertion
        $batchSize = 1000;
        
        if (count($data) <= $batchSize) {
            return $this->insertBatch($data);
        } else {
            // Split large datasets into smaller chunks
            $chunks = array_chunk($data, $batchSize);
            $affected = 0;
            
            foreach ($chunks as $chunk) {
                $affected += $this->insertBatch($chunk);
            }
            
            return $affected;
        }
    }
}