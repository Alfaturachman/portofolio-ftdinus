<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaKelasModel extends Model
{
    protected $table = 'mahasiswa_kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nim', 'nama', 'kode_matkul', 'matkul', 
        'kelp_matkul', 'kode_ts', 'ins_time', 'upd_time'
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