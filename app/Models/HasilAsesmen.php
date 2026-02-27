<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilAsesmen extends Model
{
    protected $table            = 'hasil_asesmen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'jenis_asesmen',
        'file_jawaban',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'jenis_asesmen' => 'required|in_list[tugas,uts,uas]',
        'file_jawaban' => 'required|max_length[255]'
    ];

    /**
     * Mendapatkan hasil asesmen berdasarkan jenis
     */
    public function getByJenis($id_portofolio, $jenis)
    {
        return $this->where('id_portofolio', $id_portofolio)
            ->where('jenis_asesmen', $jenis)
            ->findAll();
    }
}
