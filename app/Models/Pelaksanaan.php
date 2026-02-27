<?php

namespace App\Models;

use CodeIgniter\Model;

class Pelaksanaan extends Model
{
    protected $table            = 'pelaksanaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_portofolio',
        'file_kontrak_kuliah',
        'file_realisasi_mengajar',
        'file_kehadiran',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'file_kontrak_kuliah' => 'permit_empty|max_length[255]',
        'file_realisasi_mengajar' => 'permit_empty|max_length[255]',
        'file_kehadiran' => 'permit_empty|max_length[255]'
    ];

    /**
     * Mendapatkan data pelaksanaan berdasarkan portofolio
     */
    public function getByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->first();
    }

    /**
     * Update file tertentu
     */
    public function updateFile($id_portofolio, $jenis_file, $nama_file)
    {
        $data = [$jenis_file => $nama_file];
        return $this->where('id_portofolio', $id_portofolio)->set($data)->update();
    }
}
