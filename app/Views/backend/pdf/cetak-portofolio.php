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

        .sub-list {
            list-style-type: none;
            counter-reset: sub-counter;
            padding-left: 20px;
        }

        .sub-list>li {
            counter-increment: sub-counter;
        }

        .sub-list>li::before {
            content: "7." counter(sub-counter) " ";
        }

        .page-break {
            page-break-after: always;
        }

        .pdf-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
        }

        .insert-pdf {
            font-size: 0.1pt;
            color: white;
        }

        .chart-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="first-page" style="position: relative; min-height: 100vh; display: flex; flex-direction: column;">
        <div>
            <h1>PORTOFOLIO MATA KULIAH</h1>
            <h2><?= $portofolioData['nama_matkul'] ?></h2>
            <h3 id="tahunAkademik">TAHUN AKADEMIK 2024/2025</h3>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const now = new Date();
                    const tahunAwal = now.getMonth() >= 6 ? now.getFullYear() : now.getFullYear() - 1;
                    const tahunAkhir = tahunAwal + 1;

                    document.getElementById("tahunAkademik").textContent = `TAHUN AKADEMIK ${tahunAwal}/${tahunAkhir}`;
                });
            </script>

        </div>

        <div style="margin-top: 100px; margin-bottom: 100px;">
            <?php
            $path = WRITEPATH . 'uploads/logo_udinus.png';
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $base64 = '';
            }
            ?>
            <img src="<?= $base64 ?>" alt="Logo" style="width: 170px;">
        </div>

        <div>
            <h4>Dosen Pengampu:</h4>
            <p><?= $portofolioData['nama_dosen'] ?></p>
        </div>

        <div style="position: absolute; bottom: 0; width: 100%; text-align: center; margin-bottom: 2cm;">
            <h4>PROGRAM STUDI <?= $portofolioData['prodi'] ?></h4>
            <h4>FAKULTAS TEKNIK UNIVERSITAS DIAN NUSWANTORO</h4>
        </div>
    </div>

    <div class="page-break"></div>

    <h2>DAFTAR ISI</h2>
    DAFTAR ISI
    <ol class="mb-8" style="list-style-type: upper-alpha;">
        <li style="margin-top: 10px;">RENCANA KEGIATAN PEMBELAJARAN SEMESTER
            <ol class="pl-8" style="list-style-type: decimal;">
                <li>Identitas Mata Kuliah (MK)</li>
                <li>Topik Perkuliahan</li>
                <li>Capaian Pembelajaran Lulusan (CPL) & Indikator Kinerja Capaian Pembelajaran (IKCP)</li>
                <li>Capaian Pembelajaran Mata Kuliah (CPMK) dan Sub Capaian Pembelajaran Mata Kuliah (Sub CPMK)</li>
                <li>Pemetaan CPL -- CPMK -- Sub CPMK</li>
                <li>Dokumen Rencana Pembelajaran Semester (RPS)</li>
                <li>Rancangan Asesmen
                    <ol class="sub-list">
                        <li>Tugas</li>
                        <li>Ujian Tengah Semester</li>
                        <li>Ujian Akhir Semester</li>
                    </ol>
                </li>
            </ol>
        </li>
        <li style="margin-top: 10px;">PELAKSANAAN PERKULIAHAN
            <ol class="pl-8" style="list-style-type: decimal;">
                <li>Kontrak Kuliah</li>
                <li>Realisasi Mengajar</li>
                <li>Kehadiran Mahasiswa</li>
            </ol>
        </li>
        <li style="margin-top: 10px;">HASIL PERKULIAHAN
            <ol class="pl-8" style="list-style-type: decimal;">
                <li>Hasil Tugas</li>
                <li>Hasil Ujian Tengah Semester</li>
                <li>Hasil Ujian Akhir Semester</li>
                <li>Nilai Mata Kuliah</li>
                <li>Nilai CPMK</li>
            </ol>
        </li>
        <li style="margin-top: 10px;">EVALUASI PERKULIAHAN</li>
    </ol>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">A. RENCANA KEGIATAN PEMBELAJARAN SEMESTER</h2>

    <h3>1. IDENTITAS MATA KULIAH (MK)</h3>
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 25%; text-align: left; padding: 2px 0; border: none;">Nama Mata Kuliah</td>
            <td style="width: 2%; text-align: center; padding: 2px 0; border: none;">:</td>
            <td style="width: 73%; text-align: left; padding: 2px 0; border: none;"><?= $portofolioData['nama_matkul'] ?></td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: left; padding: 2px 0; border: none;">Kode MK</td>
            <td style="width: 2%; text-align: center; padding: 2px 0; border: none;">:</td>
            <td style="width: 73%; text-align: left; padding: 2px 0; border: none;"><?= $portofolioData['kode_mk'] ?></td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: left; padding: 2px 0; border: none;">Kelompok MK</td>
            <td style="width: 2%; text-align: center; padding: 2px 0; border: none;">:</td>
            <td style="width: 73%; text-align: left; padding: 2px 0; border: none;"><?= $portofolioData['kelp_matkul'] ?></td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: left; padding: 2px 0; border: none;">SKS</td>
            <td style="width: 2%; text-align: center; padding: 2px 0; border: none;">:</td>
            <td style="width: 73%; text-align: left; padding: 2px 0; border: none;"><?= $portofolioData['teori'] ?> T/ <?= $portofolioData['praktek'] ?> P</td>
        </tr>
        <tr>
            <td style="width: 25%; text-align: left; padding: 2px 0; border: none;">MK Prasyarat</td>
            <td style="width: 2%; text-align: center; padding: 2px 0; border: none;">:</td>
            <td style="width: 73%; text-align: left; padding: 2px 0; border: none;"><?= $portofolioData['prasyarat_mk'] ?></td>
        </tr>
    </table>

    <h3>2. TOPIK PERKULIAHAN</h3>
    <p><?= $portofolioData['topik_perkuliahan'] ?></p>

    <h3>3. CAPAIAN PEMBELAJARAN LULUSAN (CPL) & INDIKATOR KINERJA CAPAIAN PEMBELAJARAN (IKCP)</h3>
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

    <h3>4. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK) DAN SUB CAPAIAN PEMBELAJARAN MATA KULIAH (Sub CPMK)</h3>

    <h4 class="font-bold mb-2">Tabel 2 Capaian Pembelajaran Mata Kuliah</h4>
    <table>
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
    <table>
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

    <h3>5. PEMETAAN CPL -- CPMK -- Sub CPMK</h3>
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

    <h3>6. DOKUMEN RENCANA PEMBELAJARAN SEMESTER (RPS)</h3>
    <p>Terlampir</p>
    <p class="insert-pdf">INSERT_PDF_RPS</p>

    <div class="page-break"></div>

    <h3>7. RANCANGAN ASESMEN</h3>
    <p>Rancangan Asesmen</p>
    <table class="table table-bordered">
        <thead class="text-white" style="background-color: #0f4c92;">
            <tr class="align-middle text-center">
                <th>CPMK</th>
                <th>TUGAS</th>
                <th>UTS</th>
                <th>UAS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($assessmentData)) : ?>
                <?php
                // Buat lookup CPMK saja
                $cpmkLookup = [];
                foreach ($cpmkData as $cpmk) {
                    $cpmkLookup[$cpmk['id']] = $cpmk['isi_cpmk'];
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
                        <td style="text-align: center; vertical-align: middle;"><?= $row['tugas'] ? 'v' : '' ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $row['uts'] ? 'v' : '' ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $row['uas'] ? 'v' : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data asesmen yang tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $kategoriList = ['Tugas', 'UTS', 'UAS'];
    $groupedSoal = [];

    foreach ($assessmentSoalData as $soal) {
        $groupedSoal[$soal['kategori_soal']][] = $soal;
    }
    ?>

    <?php foreach ($kategoriList as $index => $kategori) : ?>
        <h5><?= ($index + 1) . '. ' . $kategori ?></h5>
        <table class="table table-bordered">
            <thead class="text-white text-center" style="background-color: #0f4c92;">
                <tr>
                    <th>Soal No</th>
                    <?php foreach ($cpmkData as $cpmk) : ?>
                        <th><?= 'CPMK ' . $cpmk['no_cpmk'] ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($groupedSoal[$kategori]) && !empty($groupedSoal[$kategori])) : ?>
                    <?php
                    // Kelompokkan berdasarkan no_soal
                    $bySoal = [];
                    foreach ($groupedSoal[$kategori] as $item) {
                        $bySoal[$item['no_soal']][] = $item;
                    }
                    ?>
                    <?php foreach ($bySoal as $noSoal => $soalItems) : ?>
                        <tr class="text-center align-middle">
                            <td><?= esc($noSoal) ?></td>
                            <?php foreach ($cpmkData as $cpmk) : ?>
                                <?php
                                $nilai = false;
                                foreach ($soalItems as $item) {
                                    if ($item['id_cpmk'] == $cpmk['id']) {
                                        $nilai = $item['nilai'];
                                        break;
                                    }
                                }
                                ?>
                                <td><?= $nilai ? 'v' : '-' ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?= count($cpmkData) + 2 ?>" class="text-center">Belum ada soal untuk kategori ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>7.1 TUGAS</h3>
        <p>Mengacu pada contoh rancangan jadwal pada tabel 5, maka dibuat lima rancangan tugas</p>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_TUGAS</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>7.2 UJIAN TENGAH SEMESTER</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_UTS</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>7.3 UJIAN AKHIR SEMESTER</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_UAS</p>
    </div>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">B. PELAKSANAAN PERKULIAHAN</h2>

    <div class="pdf-image">
        <h3>1. KONTRAK KULIAH</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_KONTRAK_KULIAH</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>2. REALISASI MENGAJAR</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_REALISASI_MENGAJAR</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>3. KEHADIRAN MAHASISWA</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_KEHADIRAN_MAHASISWA</p>
    </div>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">C. HASIL PERKULIAHAN</h2>

    <div class="pdf-image">
        <h3>1. HASIL TUGAS</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_HASIL_TUGAS</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>2. HASIL UJIAN TENGAH SEMESTER</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_HASIL_UTS</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>3. HASIL UJIAN AKHIR SEMESTER</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_HASIL_UAS</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>4. NILAI MATA KULIAH</h3>
        <p style="margin: 0; padding: 0;">Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_NILAI_MATA_KULIAH</p>
    </div>

    <div class="page-break"></div>

    <div class="pdf-image">
        <h3>5. NILAI CPMK</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_NILAI_CPMK</p>
    </div>

    <div class="page-break"></div>

    <h2 class="text-xl font-bold mb-4">D. EVALUASI PERKULIAHAN</h2>
    <p style="text-align: justify;"><?= $portofolioData['isi_evaluasi'] ?></p>

    <div class="chart-container">
        <img src="<?= $chartImageBase64 ?>" alt="Grafik Nilai CPMK" style="width: 100%; max-width: 500px;">
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <div style="text-align: right;">
        <div style="display: inline-block; text-align: left; margin: 0; padding: 0;">
            <p style="margin: 0; padding: 0;">Disusun Oleh</p>
            <p style="margin: 0; padding: 0;">Dosen Koord/Pengampu MK</p>
            <br>
            <br>
            <br>
            <p style="margin: 0; padding: 0;"><?= $portofolioData['nama_dosen'] ?></p>
            <p style="margin: 0; padding: 0;">NPP: <?= $portofolioData['npp'] ?></p>
        </div>
    </div>
</body>

</html>