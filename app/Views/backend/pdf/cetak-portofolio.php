<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Mata Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 2cm 2cm 2cm 2cm;
        }

        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
            }
        }

        body {
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .first-page {
            text-align: center;
        }

        .first-page h4 {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            background-color: #0f4c92;
            color: white;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="first-page" style="position: relative; min-height: 100vh; display: flex; flex-direction: column;">
        <div>
            <h1>PORTOFOLIO MATA KULIAH</h1>
            <h2>NAMA MATA KULIAH</h2>
            <h3>TAHUN AKADEMIK 20XX/20XX</h3>
        </div>

        <div>
            <img src="/api/placeholder/180/180" alt="Logo" class="mx-auto mb-4">
        </div>

        <div>
            <h4>Dosen Pengampu:</h4>
            <p><?= $portofolioData['nama_dosen'] ?></p>
        </div>

        <div style="position: absolute; bottom: 0; width: 100%; text-align: center; margin-bottom: 2cm;">
            <h4>PROGRAM STUDI TEKNIK ELEKTRO</h4>
            <h4>FAKULTAS TEKNIK UNIVERSITAS DIAN NUSWANTORO</h4>
        </div>
    </div>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">DAFTAR ISI</h2>
    <ol class="list-decimal pl-8 mb-8">
        <li>A. RENCANA KEGIATAN PEMBELAJARAN SEMESTER
            <ol class="list-decimal pl-8">
                <li>Identitas Mata Kuliah (MK)</li>
                <li>Topik Perkuliahan</li>
                <li>Capaian Pembelajaran Lulusan (CPL) & Indikator Kinerja Capaian Pembelajaran (IKCP)</li>
                <li>Capaian Pembelajaran Mata Kuliah (CPMK) dan Sub Capaian Pembelajaran Mata Kuliah (Sub CPMK)</li>
                <li>Pemetaan CPL -- CPMK -- Sub CPMK</li>
                <li>Dokumen Rencana Pembelajaran Semester (RPS)</li>
                <li>Rancangan Asesmen
                    <ol class="list-decimal pl-8">
                        <li>Tugas</li>
                        <li>Ujian Tengah Semester</li>
                        <li>Ujian Akhir Semester</li>
                    </ol>
                </li>
            </ol>
        </li>
        <li>B. PELAKSANAAN PERKULIAHAN
            <ol class="list-decimal pl-8">
                <li>Kontrak Kuliah</li>
                <li>Realisasi Mengajar</li>
                <li>Kehadiran Mahasiswa</li>
            </ol>
        </li>
        <li>C. HASIL PERKULIAHAN
            <ol class="list-decimal pl-8">
                <li>Hasil Tugas</li>
                <li>Hasil Ujian Tengah Semester</li>
                <li>Hasil Ujian Akhir Semester</li>
                <li>Nilai Mata Kuliah</li>
                <li>Nilai CPMK</li>
            </ol>
        </li>
        <li>D. EVALUASI PERKULIAHAN</li>
    </ol>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">A. RENCANA KEGIATAN PEMBELAJARAN SEMESTER</h2>

    <h3 class="text-lg font-bold mb-2">1. IDENTITAS MATA KULIAH (MK)</h3>
    <div class="mb-8">
        <p>Nama Mata Kuliah : <?= $portofolioData['nama_matkul'] ?></p>
        <p>Kode MK : <?= $portofolioData['kode_mk'] ?></p>
        <p>Kelompok MK : <?= $portofolioData['kelp_matkul'] ?></p>
        <p>SKS : <?= $portofolioData['teori'] ?> T/ <?= $portofolioData['praktek'] ?> P</p>
        <p>MK Prasyarat : <?= $portofolioData['prasyarat_mk'] ?></p>
    </div>

    <h3 class="text-lg font-bold mb-2">2. TOPIK PERKULIAHAN</h3>
    <p class="mb-8"><?= $portofolioData['topik_perkuliahan'] ?></p>

    <h3 class="text-lg font-bold mb-2">3. CAPAIAN PEMBELAJARAN LULUSAN (CPL) & INDIKATOR KINERJA CAPAIAN PEMBELAJARAN (IKCP)</h3>
    <table class="table table-bordered">
        <thead class="text-white" style="background-color: #0f4c92;">
            <tr>
                <th style="width: 30%" colspan="2">Capaian Pembelajaran Lulusan</th>
                <th style="width: 60%">Performa Index</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cplPiData)): ?>
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data CPL dan PI untuk mata kuliah ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cplPiData as $cplNo => $cplData): ?>
                    <?php $rowCount = max(count($cplData['pi_list']), 1); ?>
                    <tr>
                        <td rowspan="<?= $rowCount ?>" style="white-space: nowrap;"><strong>CPL <?= $cplNo ?></strong></td>
                        <td rowspan="<?= $rowCount ?>"><?= esc($cplData['cpl_indo']) ?></td>
                        <?php if (!empty($cplData['pi_list'])): ?>
                            <td><?= esc($cplData['pi_list'][0]) ?></td>
                        <?php else: ?>
                            <td>-</td>
                        <?php endif; ?>
                    </tr>
                    <?php for ($i = 1; $i < count($cplData['pi_list']); $i++): ?>
                        <tr>
                            <td><?= esc($cplData['pi_list'][$i]) ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 class="text-lg font-bold mb-2">4. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK) DAN SUB CAPAIAN PEMBELAJARAN MATA KULIAH (Sub CPMK)</h3>

    <h4 class="font-bold mb-2">Tabel 2 Capaian Pembelajaran Mata Kuliah</h4>
    <table class="mb-8">
        <tbody>
            <?php if (empty($cpmkData)): ?>
                <tr>
                    <td>Tidak ada data CPMK untuk mata kuliah ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cpmkData as $cpmk): ?>
                    <tr>
                        <td>CPMK-<?= esc($cpmk['no_cpmk']) ?> <?= esc($cpmk['isi_cpmk']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h4 class="font-bold mb-2">Tabel 3 Sub Capaian Pembelajaran Mata Kuliah</h4>
    <table class="mb-8">
        <tbody>
            <?php if (empty($subCpmkData)): ?>
                <tr>
                    <td>Tidak ada data Sub-CPMK untuk mata kuliah ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subCpmkData as $subCpmk): ?>
                    <tr>
                        <td>SCPMK-<?= esc($subCpmk['no_scpmk']) ?> <?= esc($subCpmk['isi_scmpk']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 class="text-lg font-bold mb-2">5. PEMETAAN CPL -- CPMK -- Sub CPMK</h3>
    <table class="table table-bordered">
        <thead class="text-white" style="background-color: #0f4c92;">
            <tr class="align-middle text-center">
                <th style="width: 30%" rowspan="2">CPMK</th>
                <th colspan="<?= count($subCpmkData) ?>">Sub CPMK</th>
            </tr>
            <tr class="text-center">
                <?php foreach ($subCpmkData as $subCpmk): ?>
                    <th><?= esc($subCpmk['no_scpmk']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cpmkData)): ?>
                <tr>
                    <td colspan="<?= count($subCpmkData) + 1 ?>" class="text-center">Tidak ada data pemetaan yang tersedia.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cpmkData as $cpmk): ?>
                    <tr>
                        <td class="align-middle">
                            <strong>CPMK <?= esc($cpmk['no_cpmk']) ?></strong><br>
                            <?= esc($cpmk['isi_cpmk']) ?>
                        </td>

                        <?php foreach ($subCpmkData as $subCpmk): ?>
                            <td style="text-align: center; vertical-align: middle;">
                                <?php
                                // Perbaikan disini
                                $isChecked = isset($mappingData[$cpmk['id']][$subCpmk['id']]) ? 'v' : '-';
                                echo $isChecked;
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 class="text-lg font-bold mb-2">6. DOKUMEN RENCANA PEMBELAJARAN SEMESTER (RPS)</h3>
    <p class="mb-8">Terlampir</p>

    <div class="page-break"></div>

    <h3 class="text-lg font-bold mb-2">7. RANCANGAN ASESMEN</h3>
    <table class="table table-bordered">
        <thead class="text-white" style="background-color: #0f4c92;">
            <tr class="align-middle text-center">
                <th>CPMK</th>
                <th>Sub CPMK</th>
                <th>TUGAS</th>
                <th>UTS</th>
                <th>UAS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($assessmentData)) : ?>
                <?php
                // Create associative arrays for easier lookup
                $cpmkLookup = [];
                foreach ($cpmkData as $cpmk) {
                    $cpmkLookup[$cpmk['id']] = $cpmk['isi_cpmk'];
                }

                $subCpmkLookup = [];
                foreach ($subCpmkData as $subCpmk) {
                    $subCpmkLookup[$subCpmk['id']] = $subCpmk['isi_scmpk'];
                }
                ?>

                <?php foreach ($assessmentData as $row) : ?>
                    <tr class="text-center">
                        <td>
                            <?= 'CPMK ' . $row['no_cpmk'] ?>
                            <?php if (isset($cpmkLookup[$row['id_cpmk']])) : ?>
                                <br><span class="small text-muted"><?= $cpmkLookup[$row['id_cpmk']] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= 'Sub CPMK ' . $row['no_scpmk'] ?>
                            <?php if (isset($subCpmkLookup[$row['id_scpmk']])) : ?>
                                <br><span class="small text-muted"><?= $subCpmkLookup[$row['id_scpmk']] ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; vertical-align: middle;"><?= $row['tugas'] ? 'v' : '' ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $row['uts'] ? 'v' : '' ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $row['uas'] ? 'v' : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data asesmen yang tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 class="text-lg font-bold mb-2">7.1 TUGAS</h3>
    <p class="mb-8">Mengacu pada contoh rancangan jadwal pada tabel 5, maka dibuat lima rancangan tugas</p>

    <h3 class="text-lg font-bold mb-2">7.2 UJIAN TENGAH SEMESTER</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">7.3 UJIAN AKHIR SEMESTER</h3>
    <p class="mb-8"></p>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">B. PELAKSANAAN PERKULIAHAN</h2>

    <h3 class="text-lg font-bold mb-2">1. KONTRAK KULIAH</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">2. REALISASI MENGAJAR</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">3. KEHADIRAN MAHASISWA</h3>
    <p class="mb-8"></p>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">C. HASIL PERKULIAHAN</h2>

    <h3 class="text-lg font-bold mb-2">1. HASIL TUGAS</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">2. HASIL UJIAN TENGAH SEMESTER</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">3. HASIL UJIAN AKHIR SEMESTER</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">4. NILAI MATA KULIAH</h3>
    <p class="mb-8"></p>

    <h3 class="text-lg font-bold mb-2">5. NILAI CPMK</h3>
    <p class="mb-8"></p>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">D. EVALUASI PERKULIAHAN</h2>
    <p class="mb-8">................................................</p>

    <div style="text-align: right;">
        <div style="display: inline-block; text-align: left;">
            <p>Disusun Oleh</p>
            <p>Dosen Koord/Pengampu MK</p>
            <p>.......................................</p>
            <p>NPP: 0686.11.....................</p>
        </div>
    </div>
</body>

</html>