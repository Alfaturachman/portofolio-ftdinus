<?php

namespace App\Models;

use CodeIgniter\Model;

class InformasiMK extends Model
{
    protected $table            = 'informasi_mk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_portofolio',
        'topik_perkuliahan',
        'mk_prasyarat',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_portofolio' => 'required|integer|is_not_unique[portofolio.id]',
        'topik_perkuliahan' => 'permit_empty|string',
        'mk_prasyarat' => 'permit_empty|string'
    ];

    protected $validationMessages   = [
        'id_portofolio' => [
            'required' => 'ID Portofolio harus diisi',
            'integer' => 'ID Portofolio harus berupa angka',
            'is_not_unique' => 'ID Portofolio tidak ditemukan'
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
     * Mendapatkan informasi MK berdasarkan ID portofolio
     *
     * @param int $id_portofolio
     * @return array|null
     */
    public function getByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->findAll();
    }

    /**
     * Menyimpan data informasi MK
     *
     * @param array $data
     * @return bool|int
     */
    public function saveInformasiMK($data)
    {
        // Bersihkan data jika ada field yang tidak diperlukan
        if (isset($data['id'])) {
            return $this->update($data['id'], $data);
        }

        return $this->insert($data);
    }

    /**
     * Menghapus informasi MK berdasarkan ID portofolio
     *
     * @param int $id_portofolio
     * @return bool
     */
    public function deleteByIdPortofolio($id_portofolio)
    {
        return $this->where('id_portofolio', $id_portofolio)->delete();
    }
}
