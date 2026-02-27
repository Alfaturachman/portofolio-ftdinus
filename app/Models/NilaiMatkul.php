<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiMatkul extends Model
{
    protected $table            = 'nilai_matkul';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'file_nilai_matkul',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'file_nilai_matkul' => 'required|max_length[255]'
    ];

    /**
     * Mendapatkan nilai matkul berdasarkan portofolio
     */
    public function getByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->first();
    }
}
