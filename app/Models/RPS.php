<?php

namespace App\Models;

use CodeIgniter\Model;

class RPS extends Model
{
    protected $table            = 'rps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_portofolio',
        'file_rps',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    /**
     * Mendapatkan RPS berdasarkan ID portofolio
     */
    public function getByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->first();
    }

    /**
     * Mendapatkan RPS dengan data portofolio
     */
    public function getWithPortofolio($id = null)
    {
        $this->select('rps.*, portofolio.judul as judul_portofolio');
        $this->join('portofolio', 'portofolio.id = rps.id_portofolio');

        if ($id !== null) {
            return $this->find($id);
        }

        return $this->findAll();
    }
}
