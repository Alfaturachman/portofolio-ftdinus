<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportMatkulDiampuModel extends Model
{
    protected $table = 'matkul_diampu';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'matkul', 'kode_matkul', 'id_matkul', 'kelp_matkul',
        'id_kelas', 'dosen', 'npp', 'id_dosen',
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