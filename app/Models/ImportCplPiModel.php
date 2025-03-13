<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportCplPiModel extends Model
{
    protected $table = 'cpl_pi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kurikulum', 'matkul', 'kode_matkul', 
        'id_matkul', 'no_cpl', 'cpl_indo', 'cpl_inggris', 'id_cpl', 
        'no_pi', 'isi_pi', 'id_pi', 'ins_time', 'upd_time'
    ];
    protected $useTimestamps = false;
    
    public function insertBatchData(array $data)
    {
        // Set batch size for insertion
        $batchSize = 1000;  // Increased batch size since SimpleXLSX is more efficient
        
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