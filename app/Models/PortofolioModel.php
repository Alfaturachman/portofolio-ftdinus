<?php

namespace App\Models;

use CodeIgniter\Model;

class PortofolioModel extends Model
{
    protected $table      = 'portofolio';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_user', 'kode_mk', 'nama_mk', 'kelp_mk', 'npp', 'tahun', 'semester', 'smt_matkul', 'ins_time', 'upd_time'];

    protected $useTimestamps = true;
    protected $createdField  = 'ins_time';
    protected $updatedField  = 'upd_time';

    public function getAllPortofolio($npp)
    {
        $sql = "
        SELECT 
            MIN(portofolio.id) as id,
            portofolio.kode_mk,
            portofolio.kelp_mk,
            portofolio.nama_mk,
            portofolio.tahun,
            portofolio.semester,
            portofolio.smt_matkul,
            users.nama AS dosen_nama,
            portofolio.npp,
            MAX(portofolio.ins_time) as ins_time,
            matkul_diampu.kelp_matkul,
            matkul_diampu.id_kelas,
            matkul_diampu.kode_ts
        FROM portofolio
        JOIN users 
            ON portofolio.npp = users.username
        JOIN matkul_diampu 
            ON matkul_diampu.kode_matkul = portofolio.kode_mk
            AND matkul_diampu.kelp_matkul = portofolio.kelp_mk
            AND matkul_diampu.tahun = portofolio.tahun
            AND matkul_diampu.semester = portofolio.semester
        WHERE portofolio.npp = ?
        GROUP BY 
            portofolio.kode_mk,
            portofolio.kelp_mk,
            portofolio.nama_mk,
            portofolio.tahun,
            portofolio.semester,
            portofolio.smt_matkul,
            users.nama,
            portofolio.npp,
            matkul_diampu.kelp_matkul,
            matkul_diampu.id_kelas,
            matkul_diampu.kode_ts
        ORDER BY ins_time DESC
    ";

        return $this->db->query($sql, [$npp])->getResultArray();
    }

    public function getPortofolio($kode_mk, $tahun, $semester)
    {
        return $this->select('portofolio.*, users.nama')
            ->join('users', 'portofolio.npp = users.username', 'left')
            ->where('portofolio.kode_mk', $kode_mk)
            ->where('portofolio.tahun', $tahun)
            ->where('portofolio.semester', $semester)
            ->orderBy('portofolio.ins_time', 'DESC')
            ->findAll();
    }

    public function getMatkulDiampuByUser($npp)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('matkul_diampu');

        // Fix: Include all selected columns in the GROUP BY clause
        return $builder->select('matkul_diampu.kode_matkul, matkul_diampu.matkul, matkul_diampu.kelp_matkul, matkul_diampu.tahun, matkul_diampu.semester, matkul_diampu.kode_ts')
            ->where('matkul_diampu.npp', $npp)
            ->groupBy('matkul_diampu.kelp_matkul, matkul_diampu.kode_matkul, matkul_diampu.matkul, matkul_diampu.tahun, matkul_diampu.semester, matkul_diampu.kode_ts')
            ->get()
            ->getResultArray();
    }

    public function getPortofolioCetakDetails($idPorto)
    {
        return $this->select('
        portofolio.id,
        portofolio.kode_mk,
        matkul_diampu.matkul AS nama_matkul,
        matkul_diampu.kelp_matkul,
        info_matkul.smt_matkul,
        info_matkul.jenis_matkul,
        info_matkul.tipe_matkul,
        info_matkul.kurikulum,
        info_matkul.prodi,
        info_matkul.jenjang,
        info_matkul.fakultas,
        info_matkul.teori,
        info_matkul.praktek,
        users.id AS user_id,
        users.nama AS nama_dosen,
        portofolio.npp,
        portofolio.ins_time,
        identitas_matkul.prasyarat_mk,
        identitas_matkul.topik_perkuliahan,
        evaluasi_perkuliahan.isi_evaluasi
    ')
            ->join('matkul_diampu', 'portofolio.kode_mk = matkul_diampu.kode_matkul', 'left')
            ->join('info_matkul', 'portofolio.kode_mk = info_matkul.kode_matkul', 'left')
            ->join('users', 'portofolio.npp = users.username', 'left')
            ->join('identitas_matkul', 'portofolio.id = identitas_matkul.id_porto', 'left')
            ->join('evaluasi_perkuliahan', 'portofolio.id = evaluasi_perkuliahan.id_porto', 'left')
            ->where('portofolio.id', $idPorto)
            ->first();
    }

    public function getMatkulDetail($kode_matkul, $tahun, $semester)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('matkul_diampu');

        return $builder->select('kode_matkul, matkul, tahun, semester, npp')
            ->where('kode_matkul', $kode_matkul)
            ->where('tahun', $tahun)
            ->where('semester', $semester)
            ->get()
            ->getRowArray();
    }

    public function getMatkulByKodeTS($kode_mk, $kode_ts)
    {
        return $this->db->table('matkul_diampu')
            ->where('kode_matkul', $kode_mk)
            ->where('kode_ts', $kode_ts)
            ->get()
            ->getRowArray();
    }


    public function getPortofolioByMKTS($kode_mk, $tahun, $semester)
    {
        return $this->where('kode_mk', $kode_mk)
            ->where('tahun', $tahun)
            ->where('semester', $semester)
            ->orderBy('ins_time', 'DESC')
            ->findAll();
    }

    public function getPortofolioByKodeMK($kode_matkul)
    {
        return $this->select('portofolio.id, portofolio.kode_mk, portofolio.nama_mk, portofolio.npp, users.nama, portofolio.ins_time')
            ->join('users', 'portofolio.npp = users.username')
            ->where('portofolio.kode_mk', $kode_matkul)
            ->findAll();
    }

    public function checkMahasiswaKelasExists($kode_matkul, $kelp_matkul, $kode_ts)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('mahasiswa_kelas');

        $result = $builder->where([
            'kode_matkul' => $kode_matkul,
            'kelp_matkul' => $kelp_matkul,
            'kode_ts' => $kode_ts
        ])
            ->countAllResults();

        return $result > 0;
    }

    /**
     * Get all data related to a specific portfolio by ID for editing
     * This method retrieves complete portfolio information to populate edit forms
     * 
     * @param int $idPorto Portfolio ID
     * @return array Complete portfolio data
     */
    public function getPortofolioById($idPorto)
    {
        // Get main portfolio data
        $portfolio = $this->where('id', $idPorto)->first();
        if (!$portfolio) {
            return null;
        }

        // Get RPS data
        $rpsModel = new \App\Models\RpsModel();
        $rps = $rpsModel->where('id_porto', $idPorto)->first();
        $portfolio['rps'] = $rps;

        // Get identitas matkul
        $identitasMatkulModel = new \App\Models\IdentitasMatkulModel();
        $identitasMatkul = $identitasMatkulModel->where('id_porto', $idPorto)->first();
        $portfolio['identitas_matkul'] = $identitasMatkul;

        // Get CPL and PI data
        $cplModel = new \App\Models\CplModel();
        $cplPiModel = new \App\Models\PiModel();
        $cplData = $cplModel->where('id_porto', $idPorto)->orderBy('no_cpl', 'ASC')->findAll();
        $cplList = [];
        foreach ($cplData as $cpl) {
            $piList = $cplPiModel->where('id_cpl', $cpl['id'])->orderBy('no_pi', 'ASC')->findAll();
            $cplList[$cpl['no_cpl']] = [
                'id' => $cpl['id'],
                'isi_cpl' => $cpl['isi_cpl'],
                'pi_list' => $piList
            ];
        }
        $portfolio['cpl'] = $cplList;

        // Get CPMK and Sub-CPMK data
        $cpmkModel = new \App\Models\CpmkModel();
        $subCpmkModel = new \App\Models\SubCpmkModel();
        $cpmkData = $cpmkModel->where('id_porto', $idPorto)->orderBy('no_cpmk', 'ASC')->findAll();
        $cpmkList = [];
        foreach ($cpmkData as $cpmk) {
            $subCpmkList = $subCpmkModel->where('id_porto', $idPorto)->orderBy('no_scpmk', 'ASC')->findAll();
            $cpmkList[$cpmk['id']] = [
                'id' => $cpmk['id'],
                'no_cpmk' => $cpmk['no_cpmk'],
                'isi_cpmk' => $cpmk['isi_cpmk'],
                'avg_cpmk' => $cpmk['avg_cpmk'],
                'sub_cpmk' => $subCpmkList
            ];
        }
        $portfolio['cpmk'] = $cpmkList;

        // Get mapping CPMK-SubCPMK data
        $mappingCpmkScpmkModel = new \App\Models\MappingCpmkScpmkModel();
        $mappingData = $mappingCpmkScpmkModel->getMapping($idPorto);
        $portfolio['mapping_cpmk_scpmk'] = $mappingData;

        // Get assessment data
        $rancanganAsesmenModel = new \App\Models\RancanganAsesmenModel();
        $assessmentData = $rancanganAsesmenModel->getAssessmentData($idPorto);
        $portfolio['assessment'] = $assessmentData;

        // Get assessment file data
        $rancanganAsesmenFileModel = new \App\Models\RancanganAsesmenFileModel();
        $assessmentFileData = $rancanganAsesmenFileModel->where('id_porto', $idPorto)->findAll();
        $portfolio['assessment_files'] = $assessmentFileData;

        // Get soal data
        $rancanganSoalModel = new \App\Models\RancanganSoalModel();
        $soalData = $rancanganSoalModel->getRancanganSoalWithCpmk($idPorto);
        $portfolio['soal'] = $soalData;

        // Get pelaksanaan perkuliahan data
        $pelaksanaanModel = new \App\Models\PelaksanaanPerkuliahanModel();
        $pelaksanaanData = $pelaksanaanModel->where('id_porto', $idPorto)->first();
        $portfolio['pelaksanaan'] = $pelaksanaanData;

        // Get hasil asesmen data
        $hasilAsesmenModel = new \App\Models\HasilAsesmenModel();
        $hasilAsesmenData = $hasilAsesmenModel->where('id_porto', $idPorto)->first();
        $portfolio['hasil_asesmen'] = $hasilAsesmenData;

        // Get evaluasi perkuliahan data
        $evaluasiPerkuliahanModel = new \App\Models\EvaluasiPerkuliahanModel();
        $evaluasiData = $evaluasiPerkuliahanModel->where('id_porto', $idPorto)->first();
        $portfolio['evaluasi'] = $evaluasiData;

        return $portfolio;
    }

    /**
     * Get portfolio data in the format required by edit forms
     * This method formats data similar to how it would be stored in session during creation
     * 
     * @param int $idPorto Portfolio ID
     * @return array Formatted data for edit forms
     */
    public function getPortofolioForEdit($idPorto)
    {
        $portfolioData = $this->getPortofolioById($idPorto);
        if (!$portfolioData) {
            return null;
        }

        $formattedData = [];

        // Format info_matkul data (similar to session data)
        $formattedData['info_matkul'] = [
            'fakultas' => $portfolioData['fakultas'] ?? '',
            'progdi' => $portfolioData['progdi'] ?? '',
            'nama_mk' => $portfolioData['nama_mk'] ?? '',
            'kode_mk' => $portfolioData['kode_mk'] ?? '',
            'kelompok_mk' => $portfolioData['kelompok_mk'] ?? '',
            'sks_teori' => $portfolioData['teori'] ?? $portfolioData['sks_teori'] ?? '',
            'sks_praktik' => $portfolioData['praktek'] ?? $portfolioData['sks_praktik'] ?? '',
            'tahun' => $portfolioData['tahun'] ?? '',
            'semester' => $portfolioData['semester'] ?? '',
            'smt_matkul' => $portfolioData['smt_matkul'] ?? '',
            'mk_prasyarat' => $portfolioData['identitas_matkul']['prasyarat_mk'] ?? '',
            'topik_mk' => $portfolioData['identitas_matkul']['topik_perkuliahan'] ?? ''
        ];

        // Format CPL-PI data
        $cplPiData = [];
        foreach ($portfolioData['cpl'] as $noCpl => $cpl) {
            $piList = [];
            foreach ($cpl['pi_list'] as $pi) {
                $piList[] = $pi['isi_ikcp'];
            }
            $cplPiData[$noCpl] = [
                'cpl_indo' => $cpl['isi_cpl'],
                'pi_list' => $piList
            ];
        }
        $formattedData['cpl_pi_data'] = $cplPiData;

        // Format CPMK data
        $cpmkData = [];
        $globalSubCpmkCounter = 1;
        foreach ($portfolioData['cpmk'] as $cpmkId => $cpmk) {
            $subCpmkList = [];
            foreach ($cpmk['sub_cpmk'] as $subCpmk) {
                $subCpmkList[$subCpmk['no_scpmk']] = $subCpmk['isi_scmpk'];
                if ($subCpmk['no_scpmk'] > $globalSubCpmkCounter) {
                    $globalSubCpmkCounter = $subCpmk['no_scpmk'];
                }
            }
            $cpmkData[$cpmk['no_cpmk']] = [
                'id' => $cpmk['id'],
                'no_cpmk' => $cpmk['no_cpmk'],
                'narasi' => $cpmk['isi_cpmk'],
                'avg_cpmk' => $cpmk['avg_cpmk'],
                'sub' => $subCpmkList
            ];
        }
        $formattedData['cpmk_data'] = [
            'cpmk' => $cpmkData,
            'globalSubCpmkCounter' => $globalSubCpmkCounter
        ];

        // Format assessment data
        $assessmentData = [];
        if (!empty($portfolioData['assessment'])) {
            foreach ($portfolioData['assessment'] as $assessment) {
                $cpmkNo = $assessment['no_cpmk'];
                if (!isset($assessmentData[$cpmkNo])) {
                    $assessmentData[$cpmkNo] = [
                        'tugas' => 0,
                        'uts' => 0,
                        'uas' => 0,
                    ];
                }
                if ($assessment['tugas'] == 1) $assessmentData[$cpmkNo]['tugas'] = 1;
                if ($assessment['uts'] == 1) $assessmentData[$cpmkNo]['uts'] = 1;
                if ($assessment['uas'] == 1) $assessmentData[$cpmkNo]['uas'] = 1;
            }
        }
        $formattedData['assessment_data'] = $assessmentData;

        // Format assessment files
        $assessmentFiles = [];
        if (!empty($portfolioData['assessment_files'])) {
            foreach ($portfolioData['assessment_files'] as $file) {
                $kategori = $file['kategori'];
                $kategoriFile = $file['kategori_file'];
                $field = '';
                switch ($kategori) {
                    case 'Tugas':
                        $field = ($kategoriFile == 'Soal') ? 'soal_tugas' : 'rubrik_tugas';
                        break;
                    case 'UTS':
                        $field = ($kategoriFile == 'Soal') ? 'soal_uts' : 'rubrik_uts';
                        break;
                    case 'UAS':
                        $field = ($kategoriFile == 'Soal') ? 'soal_uas' : 'rubrik_uas';
                        break;
                }
                if ($field) {
                    $assessmentFiles[$field] = [
                        'name' => basename($file['file_pdf']),
                        'path' => $file['file_pdf'],
                        'size' => 0 // We don't have size in the table, but keeping the format
                    ];
                }
            }
        }
        $formattedData['assessment_files'] = $assessmentFiles;

        // Format soal mapping data
        $soalMappingData = [
            'tugas' => [],
            'uts' => [],
            'uas' => []
        ];
        if (!empty($portfolioData['soal'])) {
            $groupedSoal = [];
            foreach ($portfolioData['soal'] as $soal) {
                $kategori = $soal['kategori_soal'];
                $noSoal = $soal['no_soal'];
                $cpmkNo = $soal['no_cpmk'];

                if (!isset($groupedSoal[$kategori][$noSoal])) {
                    $groupedSoal[$kategori][$noSoal] = [
                        'soal_no' => $noSoal,
                        'cpmk_mappings' => []
                    ];
                }

                $groupedSoal[$kategori][$noSoal]['cpmk_mappings'][$cpmkNo] = $soal['nilai'];
            }

            foreach ($groupedSoal as $kategori => $kategoriSoal) {
                foreach ($kategoriSoal as $noSoal => $data) {
                    $soalMappingData[$kategori][] = $data;
                }
            }
        }
        $formattedData['soal_mapping_data'] = $soalMappingData;

        // Format pelaksanaan perkuliahan files
        if (isset($portfolioData['pelaksanaan'])) {
            $pelaksanaanFiles = [];
            if ($portfolioData['pelaksanaan']['file_kontrak']) {
                $pelaksanaanFiles['kontrak_kuliah'] = [
                    'name' => basename($portfolioData['pelaksanaan']['file_kontrak']),
                    'path' => $portfolioData['pelaksanaan']['file_kontrak']
                ];
            }
            if ($portfolioData['pelaksanaan']['file_realisasi']) {
                $pelaksanaanFiles['realisasi_mengajar'] = [
                    'name' => basename($portfolioData['pelaksanaan']['file_realisasi']),
                    'path' => $portfolioData['pelaksanaan']['file_realisasi']
                ];
            }
            if ($portfolioData['pelaksanaan']['file_kehadiran']) {
                $pelaksanaanFiles['kehadiran_mahasiswa'] = [
                    'name' => basename($portfolioData['pelaksanaan']['file_kehadiran']),
                    'path' => $portfolioData['pelaksanaan']['file_kehadiran']
                ];
            }
            $formattedData['pelaksanaan_files'] = $pelaksanaanFiles;
        }

        // Format hasil asesmen files
        if (isset($portfolioData['hasil_asesmen'])) {
            $hasilAsesmenFiles = [];
            if ($portfolioData['hasil_asesmen']['file_tugas']) {
                $hasilAsesmenFiles['jawaban_tugas'] = [
                    'name' => basename($portfolioData['hasil_asesmen']['file_tugas']),
                    'path' => $portfolioData['hasil_asesmen']['file_tugas']
                ];
            }
            if ($portfolioData['hasil_asesmen']['file_uts']) {
                $hasilAsesmenFiles['jawaban_uts'] = [
                    'name' => basename($portfolioData['hasil_asesmen']['file_uts']),
                    'path' => $portfolioData['hasil_asesmen']['file_uts']
                ];
            }
            if ($portfolioData['hasil_asesmen']['file_uas']) {
                $hasilAsesmenFiles['jawaban_uas'] = [
                    'name' => basename($portfolioData['hasil_asesmen']['file_uas']),
                    'path' => $portfolioData['hasil_asesmen']['file_uas']
                ];
            }
            if ($portfolioData['hasil_asesmen']['file_nilai_mk']) {
                $hasilAsesmenFiles['nilai_mata_kuliah'] = [
                    'name' => basename($portfolioData['hasil_asesmen']['file_nilai_mk']),
                    'path' => $portfolioData['hasil_asesmen']['file_nilai_mk']
                ];
            }
            if ($portfolioData['hasil_asesmen']['file_nilai_cpmk']) {
                $hasilAsesmenFiles['nilai_cpmk'] = [
                    'name' => basename($portfolioData['hasil_asesmen']['file_nilai_cpmk']),
                    'path' => $portfolioData['hasil_asesmen']['file_nilai_cpmk']
                ];
            }
            $formattedData['hasil_asesmen_files'] = $hasilAsesmenFiles;
        }

        // Format evaluasi perkuliahan
        $formattedData['evaluasi_perkuliahan'] = $portfolioData['evaluasi']['isi_evaluasi'] ?? '';
        $formattedData['uploaded_rps'] = $portfolioData['rps']['file_rps'] ?? '';

        return $formattedData;
    }

    private function getMappingWithCplInfo($idPorto)
    {
        $db = \Config\Database::connect();

        // Try to get with selected_cpl field, fallback to default if column doesn't exist
        $query = $db->table('mapping_cpmk_scpmk m')
            ->select('m.id_cpmk, m.id_scpmk, m.nilai, c.no_cpmk, COALESCE(c.selected_cpl, c.selectedCpl, 1) as selected_cpl')
            ->join('cpmk c', 'c.id = m.id_cpmk')
            ->where('c.id_porto', $idPorto);

        $result = $query->get()->getResultArray();

        $mapping = [];
        foreach ($result as $row) {
            $mapping[] = [
                'id_cpmk' => $row['id_cpmk'],
                'id_sub_cpmk' => $row['id_scpmk'],
                'value' => $row['nilai'],
                'no_cpmk' => $row['no_cpmk'],
                'no_cpl' => $row['selected_cpl'] // Will be 1 if the field doesn't exist
            ];
        }

        return $mapping;
    }
}
