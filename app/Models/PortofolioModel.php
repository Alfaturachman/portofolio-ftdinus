<?php

namespace App\Models;

use CodeIgniter\Model;

class PortofolioModel extends Model
{
    protected $table         = 'portofolio';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mapping',
        'id_perkuliahan',
        'last_step'
    ];

    protected $useAutoIncrement = false;

    // Get semua portofolio untuk dosen tertentu berdasarkan NPP
    public function getPortofolioByNpp($npp)
    {
        return $this->db->table('perkuliahan per')
            ->select('
                per.id as id_perkuliahan,
                p.id as id_portofolio,
                p.last_step,
                mk.nama_mk, 
                mk.kode_mk,
                k.nama_kurikulum,
                per.tahun_akademik, 
                per.semester, 
                per.kode_kelas,
                u.nama_lengkap
            ')
            ->join('portofolio p', 'p.id_perkuliahan = per.id', 'left')
            ->join('mk', 'mk.id = per.id_mk')
            ->join('users u', 'u.npp = per.id_users')
            ->join('kurikulum k', 'k.id = per.id_kurikulum')
            ->where('per.id_users', $npp)
            ->orderBy('per.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Cari portofolio berdasarkan ID perkuliahan
    public function findByPerkuliahan($id_perkuliahan)
    {
        return $this->where('id_perkuliahan', $id_perkuliahan)
            ->first();
    }

    // Add portofolio baru dengan ID perkuliahan, generate ID portofolio custom, dan set last_step ke 1
    public function createPortofolio($id_perkuliahan)
    {
        $id_portofolio = substr(bin2hex(random_bytes(8)), 0, 16);

        $this->insert([
            'id'             => $id_portofolio,
            'id_perkuliahan' => $id_perkuliahan,
            'last_step'      => 1
        ]);

        return $id_portofolio; // langsung return id custom
    }

    // Get detail portofolio seluruh data terkait berdasarkan ID portofolio
    public function getFormData($id)
    {
        $db = $this->db;

        $porto = $db->table('portofolio p')
            ->select('p.*, per.id_mk, per.id_kurikulum, per.id_users,
                  mk.nama_mk, mk.kode_mk, mk.kelp_mk, mk.teori, mk.praktek,
                  k.tahun_ajaran, k.nama_kurikulum,
                  per.semester, per.tahun_akademik, per.kode_kelas')
            ->join('perkuliahan per', 'per.id = p.id_perkuliahan')
            ->join('mk', 'mk.id = per.id_mk')
            ->join('kurikulum k', 'k.id = per.id_kurikulum')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$porto) {
            return null;
        }

        $data = [];
        $data['porto']     = $porto;
        $data['last_step'] = (int) $porto['last_step'];

        // RPS
        $data['rps'] = $db->table('rps')
            ->where('id_portofolio', $id)
            ->get()->getRowArray();

        // Informasi MK
        $data['info_mk'] = $db->table('informasi_mk')
            ->where('id_portofolio', $id)
            ->get()->getRowArray();

        // CPL & PI
        $data['cpls'] = $db->table('mk_cpl_pi mcp')
            ->select('cpl.id, cpl.no_cpl, cpl.cpl_indo, cpl.cpl_inggris,
                  pi.id as id_pi, pi.no_pi, pi.isi_pi')
            ->join('cpl', 'cpl.id = mcp.id_cpl')
            ->join('pi', 'pi.id = mcp.id_pi')
            ->where('mcp.id_mk', $porto['id_mk'])
            ->where('mcp.id_kurikulum', $porto['id_kurikulum'])
            ->get()->getResultArray();

        // CPMK + Sub
        $cpmks = $db->table('cpmk')
            ->where('id_portofolio', $id)
            ->orderBy('no_cpmk')
            ->get()->getResultArray();

        foreach ($cpmks as &$cpmk) {
            $cpmk['subs'] = $db->table('sub_cpmk')
                ->where('id_cpmk', $cpmk['id'])
                ->orderBy('no_sub_cpmk')
                ->get()->getResultArray();
        }

        $data['cpmks'] = $cpmks;

        // Mapping
        $data['mapping'] = $db->table('pemetaan')
            ->select('id, id_portofolio, id_cpl, id_cpmk, id_sub_cpmk, is_active')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        // Asesmen
        $data['asesmen'] = $db->table('rancangan_asesmen')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        // Soal
        $soal = $db->table('rancangan_soal rs')
            ->select('
                rs.id,
                rs.id_portofolio,
                rs.id_asesmen,
                rs.id_cpmk as id_cpmk_soal,
                rs.nomor_soal,
                rs.created_at,
                rs.updated_at,
                ra.jenis_asesmen,
                ra.file_soal,
                ra.file_rubrik,
                c.no_cpmk,
                c.narasi_cpmk
            ')
            ->join('rancangan_asesmen ra', 'ra.id = rs.id_asesmen')
            ->join('cpmk c', 'c.id = rs.id_cpmk')
            ->where('rs.id_portofolio', $id)
            ->orderBy('ra.jenis_asesmen, rs.nomor_soal, c.no_cpmk')
            ->get()
            ->getResultArray();

        // Kelompokkan data per jenis asesmen
        $soalPerJenis = [];
        foreach ($soal as $s) {
            $jenis = $s['jenis_asesmen']; // tugas, uts, uas

            if (!isset($soalPerJenis[$jenis])) {
                $soalPerJenis[$jenis] = [];
            }

            $nomorSoal = $s['nomor_soal'];

            // Inisialisasi array untuk nomor soal jika belum ada
            if (!isset($soalPerJenis[$jenis][$nomorSoal])) {
                $soalPerJenis[$jenis][$nomorSoal] = [
                    'nomor_soal' => $nomorSoal,
                    'cpmk_list' => [] // Dalam kasus 1 soal hanya 1 CPMK, array ini hanya berisi 1 item
                ];
            }

            // Tambahkan CPMK ke dalam soal (dalam kasus ini hanya 1 CPMK per soal)
            $soalPerJenis[$jenis][$nomorSoal]['cpmk_list'][] = [
                'id_cpmk' => $s['id_cpmk_soal'],
                'no_cpmk' => $s['no_cpmk'],
                'narasi' => $s['narasi_cpmk']
            ];
        }

        // Kirim ke view
        $data['soal'] = $soalPerJenis;

        // Pelaksanaan
        $data['pelaksanaan'] = $db->table('pelaksanaan')
            ->where('id_portofolio', $id)
            ->get()->getRowArray();

        // Hasil Asesmen
        $hasilAsesmen = $db->table('hasil_asesmen')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        $data['hasil_asesmen'] = [];
        foreach ($hasilAsesmen as $ha) {
            $data['hasil_asesmen'][$ha['jenis_asesmen']] = $ha['file_jawaban'];
        }

        // Nilai Matkul
        $data['nilai_matkul'] = $db->table('nilai_matkul')
            ->where('id_portofolio', $id)
            ->get()->getRowArray();

        // Nilai CPMK
        $data['nilai_cpmk'] = $db->table('nilai_cpmk')
            ->where('id_portofolio', $id)
            ->get()->getRowArray();

        // Evaluasi
        $data['evaluasi'] = $db->table('evaluasi')
            ->where('id_portofolio', $id)
            ->get()->getResultArray();

        return $data;
    }

    // Get detail portofolio untuk halaman cetak berdasarkan ID portofolio
    public function getPortofolioCetakDetails($idPorto)
    {
        return $this->db->table('portofolio p')
            ->select('
            p.id, p.last_step,
            mk.nama_mk AS nama_matkul,
            mk.kode_mk, mk.kelp_mk AS kelp_matkul,
            mk.teori, mk.praktek,
            pk.semester, pk.kode_kelas, pk.tahun_akademik,
            u.npp, u.nama_lengkap AS nama_dosen,
            pr.nama_prodi AS prodi,
            imk.topik_perkuliahan, imk.mk_prasyarat AS prasyarat_mk
        ')
            ->join('perkuliahan pk', 'pk.id = p.id_perkuliahan')
            ->join('mk', 'mk.id = pk.id_mk')
            ->join('users u', 'u.npp = pk.id_users')

            // ambil prodi lewat mk_cpl_pi
            ->join('mk_cpl_pi mcp', 'mcp.id_mk = mk.id', 'left')
            ->join('prodi pr', 'pr.id = mcp.id_prodi', 'left')

            ->join('informasi_mk imk', 'imk.id_portofolio = p.id', 'left')
            ->where('p.id', $idPorto)
            ->get()
            ->getRowArray();
    }
}
