<?php

namespace App\Models;

use CodeIgniter\Model;

class CplModel extends Model
{
    protected $table      = 'cpl';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_porto', 'no_cpl', 'isi_cpl'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';

    public function getCplByPortoId($idPorto)
    {
        return $this->select('id, no_cpl, isi_cpl')
            ->where('id_porto', $idPorto)
            ->orderBy('no_cpl', 'ASC')
            ->findAll();
    }

    public function getCplPiByPortoId($idPorto)
    {
        $result = [];

        // Ambil semua CPL berdasarkan id_porto
        $cplData = $this->select('id, no_cpl, isi_cpl')
            ->where('id_porto', $idPorto)
            ->orderBy('no_cpl', 'ASC')
            ->findAll();

        if (empty($cplData)) {
            return $result;
        }

        // Buat instance model PI
        $piModel = new \CodeIgniter\Model();
        $piModel->setTable('pi');

        foreach ($cplData as $cpl) {
            // Ambil semua PI untuk setiap CPL
            $piList = $piModel->select('isi_ikcp')
                ->where('id_cpl', $cpl['id'])
                ->orderBy('no_pi', 'ASC')
                ->findAll();

            // Format data CPL dan PI
            $piTextList = array_map(function ($pi) {
                return $pi['isi_ikcp'];
            }, $piList);

            $result[$cpl['no_cpl']] = [
                'cpl_indo' => $cpl['isi_cpl'],
                'pi_list' => $piTextList
            ];
        }

        return $result;
    }
}
