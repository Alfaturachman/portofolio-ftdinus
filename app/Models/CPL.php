<?php

namespace App\Models;

use CodeIgniter\Model;

class CPL extends Model
{
    protected $table         = 'cpl';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_prodi',
        'id_kurikulum',
        'no_cpl',
        'cpl_indo',
        'cpl_inggris',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    public function getCplByPortoId($idPorto)
    {
        $rows = $this->db->table('portofolio p')
            ->select('c.no_cpl, c.cpl_indo, c.id AS id_cpl, pi.isi_pi')
            ->join('perkuliahan pk', 'pk.id = p.id_perkuliahan')
            ->join('mk_cpl_pi mcp', 'mcp.id_mk = pk.id_mk AND mcp.id_kurikulum = pk.id_kurikulum')
            ->join('cpl c', 'c.id = mcp.id_cpl')
            ->join('pi', 'pi.id = mcp.id_pi', 'left')
            ->where('p.id', $idPorto)
            ->orderBy('c.no_cpl')
            ->get()->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $no = $row['no_cpl'];
            if (!isset($grouped[$no])) {
                $grouped[$no] = [
                    'id_cpl'   => $row['id_cpl'],
                    'cpl_indo' => $row['cpl_indo'],
                    'pi_list'  => [],
                ];
            }
            if (!empty($row['isi_pi'])) {
                $grouped[$no]['pi_list'][] = $row['isi_pi'];
            }
        }
        return $grouped;
    }

    public function getCplPiByPortoId($idPorto)
    {
        $rows = $this->db->table('portofolio p')
            ->select('c.no_cpl, c.cpl_indo, c.id AS id_cpl, pi.isi_pi')
            ->join('perkuliahan pk', 'pk.id = p.id_perkuliahan')
            ->join('mk_cpl_pi mcp', 'mcp.id_mk = pk.id_mk AND mcp.id_kurikulum = pk.id_kurikulum')
            ->join('cpl c', 'c.id = mcp.id_cpl')
            ->join('pi', 'pi.id = mcp.id_pi', 'left')
            ->where('p.id', $idPorto)
            ->orderBy('c.no_cpl')
            ->get()->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $no = $row['no_cpl'];
            if (!isset($grouped[$no])) {
                $grouped[$no] = [
                    'id_cpl'   => $row['id_cpl'],
                    'cpl_indo' => $row['cpl_indo'],
                    'pi_list'  => [],
                ];
            }
            if (!empty($row['isi_pi'])) {
                $grouped[$no]['pi_list'][] = $row['isi_pi'];
            }
        }
        return $grouped;
    }
}
