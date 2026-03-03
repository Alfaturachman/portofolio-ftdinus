<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Mata Kuliah - <?= esc($portofolioData['nama_matkul'] ?? '') ?></title>
    <style>
        /* =============================================
       RESET & BASE — DomPDF kompatibel (CSS 2.1)
       Tidak pakai: flex, grid, min-height, @media print
    ============================================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            /* Margin halaman diatur di sini — @page tidak reliable di DomPDF */
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* =============================================
       PAGE BREAK — pakai property standar
    ============================================= */
        .page-break {
            page-break-after: always;
        }

        /* =============================================
       COVER PAGE
       Ganti flex dengan table trick agar DomPDF render
    ============================================= */
        .cover-page {
            text-align: center;
            width: 100%;
            /* DomPDF tidak support min-height/flex,
           gunakan padding besar sebagai pengganti */
            padding: 60px 0 40px 0;
        }

        .cover-title {
            margin-bottom: 20px;
        }

        .cover-title h1 {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .cover-title h2 {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .cover-title h3 {
            font-size: 13pt;
            font-weight: normal;
        }

        .cover-logo {
            margin: 40px auto;
            text-align: center;
        }

        .cover-logo img {
            width: 150px;
            height: auto;
        }

        .cover-dosen {
            margin: 30px 0 20px 0;
            text-align: center;
        }

        .cover-dosen p {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .cover-footer {
            margin-top: 30px;
            text-align: center;
        }

        .cover-footer h4 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* =============================================
       HEADINGS
    ============================================= */
        h2.section-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 16px 0 10px 0;
        }

        h3.sub-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 14px 0 8px 0;
        }

        h4.table-caption {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        /* =============================================
       TABLE
    ============================================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10pt;
        }

        table th {
            background-color: #0f4c92;
            color: #fff;
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #000;
        }

        table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }

        table.no-border,
        table.no-border td {
            border: none;
            padding: 2px 0;
        }

        td.center,
        th.center {
            text-align: center;
        }

        td.middle {
            vertical-align: middle;
        }

        /* =============================================
       IDENTITAS MATKUL
    ============================================= */
        .identitas-table td:first-child {
            width: 30%;
        }

        .identitas-table td:nth-child(2) {
            width: 3%;
            text-align: center;
        }

        /* =============================================
       DAFTAR ISI
    ============================================= */
        .toc ol {
            padding-left: 20px;
            margin-top: 6px;
        }

        .toc li {
            margin-bottom: 4px;
            font-size: 11pt;
        }

        .toc .toc-sub {
            padding-left: 20px;
            list-style-type: decimal;
        }

        .toc .toc-sub-alpha {
            padding-left: 20px;
            list-style-type: lower-alpha;
        }

        /* =============================================
       MARKER INSERT PDF
       — tetap invisible tapi DomPDF tetap render teksnya
         agar bisa di-parse
    ============================================= */
        .insert-pdf {
            font-size: 1pt;
            color: #ffffff;
            /* putih = invisible di halaman putih */
            line-height: 1pt;
            height: 1pt;
            overflow: hidden;
        }

        /* =============================================
       CHART
    ============================================= */
        .chart-container {
            text-align: center;
            margin: 20px auto;
        }

        .chart-container img {
            width: 500px;
            /* DomPDF lebih stabil dengan lebar fixed daripada max-width */
        }

        /* =============================================
       TANDA TANGAN
    ============================================= */
        .ttd-section {
            margin-top: 40px;
            text-align: right;
        }

        .ttd-space {
            height: 60px;
        }

        /* =============================================
       SECTION LAMPIRAN
    ============================================= */
        .lampiran-section {
            text-align: center;
            padding: 30px 0;
        }

        .lampiran-section h3 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* =============================================
       EVALUASI
    ============================================= */
        .evaluasi-text {
            text-align: justify;
            margin-bottom: 16px;
        }
    </style>
</head>

<body style="padding-top: 2cm;">

    <!-- ======================================================
         HALAMAN 1: COVER
    ====================================================== -->
    <div class="cover-page">
        <div class="cover-title">
            <h1>Portofolio Mata Kuliah</h1>
            <h2><?= esc($portofolioData['nama_matkul'] ?? '-') ?></h2>
            <h3>Tahun Akademik <?= esc($portofolioData['tahun_akademik'] ?? date('Y') . '/' . (date('Y') + 1)) ?></h3>
        </div>

        <div class="cover-logo">
            <?php if (!empty($logoBase64)): ?>
                <img src="<?= $logoBase64 ?>" alt="Logo Universitas">
            <?php endif; ?>
        </div>

        <div class="cover-dosen">
            <p>Dosen Pengampu:</p>
            <p><?= esc($portofolioData['nama_dosen'] ?? '-') ?></p>
            <p>NPP: <?= esc($portofolioData['npp'] ?? '-') ?></p>
        </div>

        <div class="cover-footer">
            <h4>Program Studi <?= esc($portofolioData['prodi'] ?? '-') ?></h4>
            <h4>Fakultas Teknik Universitas Dian Nuswantoro</h4>
        </div>
    </div>

    <div class="page-break"></div>
    <div style="padding-left:1cm;padding-right:1cm;">

        <?php
        // Inisialisasi variabel untuk menampilkan bagian asesmen
        $showTugas = false;
        $showUts = false;
        $showUas = false;

        if (!empty($assessmentData)) {
            foreach ($assessmentData as $row) {
                if (!empty($row['tugas'])) $showTugas = true;
                if (!empty($row['uts'])) $showUts = true;
                if (!empty($row['uas'])) $showUas = true;
            }
        }
        ?>

        <!-- ======================================================
         HALAMAN 2: DAFTAR ISI
    ====================================================== -->
        <h2 class="section-title">Daftar Isi</h2>
        <div class="toc">
            <ol style="list-style-type: upper-alpha;">
                <li>
                    Rencana Kegiatan Pembelajaran Semester
                    <ol class="toc-sub">
                        <li>Identitas Mata Kuliah (MK)</li>
                        <li>Topik Perkuliahan</li>
                        <li>Capaian Pembelajaran Lulusan (CPL) &amp; Indikator Kinerja Capaian Pembelajaran (IKCP)</li>
                        <li>Capaian Pembelajaran Mata Kuliah (CPMK) dan Sub CPMK</li>
                        <li>Pemetaan CPL &ndash; CPMK &ndash; Sub CPMK</li>
                        <li>Dokumen Rencana Pembelajaran Semester (RPS)</li>
                        <li>
                            Rancangan Asesmen
                            <ol class="toc-sub-alpha">
                                <?php if ($showTugas): ?><li>Tugas</li><?php endif; ?>
                                <?php if ($showUts): ?><li>Ujian Tengah Semester</li><?php endif; ?>
                                <?php if ($showUas): ?><li>Ujian Akhir Semester</li><?php endif; ?>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li style="margin-top:8px;">
                    Pelaksanaan Perkuliahan
                    <ol class="toc-sub">
                        <li>Kontrak Kuliah</li>
                        <li>Realisasi Mengajar</li>
                        <li>Kehadiran Mahasiswa</li>
                    </ol>
                </li>
                <li style="margin-top:8px;">
                    Hasil Perkuliahan
                    <ol class="toc-sub">
                        <?php if ($showTugas): ?><li>Hasil Tugas</li><?php endif; ?>
                        <?php if ($showUts): ?><li>Hasil Ujian Tengah Semester</li><?php endif; ?>
                        <?php if ($showUas): ?><li>Hasil Ujian Akhir Semester</li><?php endif; ?>
                        <li>Nilai Mata Kuliah</li>
                        <li>Nilai CPMK</li>
                    </ol>
                </li>
                <li style="margin-top:8px;">Evaluasi Perkuliahan</li>
            </ol>
        </div>

        <div class="page-break"></div>

        <!-- ======================================================
         BAGIAN A: RENCANA KEGIATAN PEMBELAJARAN SEMESTER
    ====================================================== -->
        <h2 class="section-title">A. Rencana Kegiatan Pembelajaran Semester</h2>

        <!-- 1. Identitas Mata Kuliah -->
        <h3 class="sub-title">1. Identitas Mata Kuliah (MK)</h3>
        <table class="no-border identitas-table">
            <tr>
                <td>Nama Mata Kuliah</td>
                <td>:</td>
                <td><?= esc($portofolioData['nama_matkul'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Kode MK</td>
                <td>:</td>
                <td><?= esc($portofolioData['kode_mk'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Kelompok MK</td>
                <td>:</td>
                <td><?= esc($portofolioData['kelp_matkul'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>SKS</td>
                <td>:</td>
                <td><?= esc($portofolioData['teori'] ?? '0') ?> T / <?= esc($portofolioData['praktek'] ?? '0') ?> P</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td><?= esc($portofolioData['semester'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Kode Kelas</td>
                <td>:</td>
                <td><?= esc($portofolioData['kode_kelas'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>MK Prasyarat</td>
                <td>:</td>
                <td><?= esc($portofolioData['prasyarat_mk'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Dosen Pengampu</td>
                <td>:</td>
                <td><?= esc($portofolioData['nama_dosen'] ?? '-') ?></td>
            </tr>
        </table>

        <!-- 2. Topik Perkuliahan -->
        <h3 class="sub-title">2. Topik Perkuliahan</h3>
        <p style="text-align:justify;"><?= nl2br(esc($portofolioData['topik_perkuliahan'] ?? 'Belum diisi.')) ?></p>

        <!-- 3. CPL & IKCP -->
        <h3 class="sub-title">3. Capaian Pembelajaran Lulusan (CPL) &amp; Indikator Kinerja Capaian Pembelajaran (IKCP)</h3>
        <h4 class="table-caption">Tabel 1. CPL dan Performa Index</h4>
        <table>
            <thead>
                <tr>
                    <th colspan="2" style="width:35%;">Capaian Pembelajaran Lulusan</th>
                    <th style="width:65%;">Performa Index (PI)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cplPiData)): ?>
                    <tr>
                        <td colspan="3" class="center">Tidak ada data CPL dan PI.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cplPiData as $cplNo => $cplItem): ?>
                        <?php $rowCount = max(count($cplItem['pi_list']), 1); ?>
                        <tr>
                            <td rowspan="<?= $rowCount ?>" style="white-space:nowrap;font-weight:bold;">
                                CPL <?= esc($cplNo) ?>
                            </td>
                            <td rowspan="<?= $rowCount ?>">
                                <?= esc($cplItem['cpl_indo']) ?>
                            </td>
                            <td><?= !empty($cplItem['pi_list']) ? esc($cplItem['pi_list'][0]) : '-' ?></td>
                        </tr>
                        <?php for ($i = 1; $i < count($cplItem['pi_list']); $i++): ?>
                            <tr>
                                <td><?= esc($cplItem['pi_list'][$i]) ?></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 4. CPMK & Sub CPMK -->
        <h3 class="sub-title">4. Capaian Pembelajaran Mata Kuliah (CPMK) dan Sub CPMK</h3>

        <h4 class="table-caption">Tabel 2. Capaian Pembelajaran Mata Kuliah (CPMK)</h4>
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">No. CPMK</th>
                    <th>Narasi CPMK</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cpmkData)): ?>
                    <tr>
                        <td colspan="2" class="center">Tidak ada data CPMK.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cpmkData as $cpmk): ?>
                        <tr>
                            <td class="center">CPMK-<?= esc($cpmk['no_cpmk']) ?></td>
                            <td><?= esc($cpmk['isi_cpmk'] ?? $cpmk['narasi_cpmk'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h4 class="table-caption">Tabel 3. Sub Capaian Pembelajaran Mata Kuliah (Sub CPMK)</h4>
        <table>
            <thead>
                <tr>
                    <th style="width:15%;">No. Sub CPMK</th>
                    <th>Narasi Sub CPMK</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subCpmkData)): ?>
                    <tr>
                        <td colspan="2" class="center">Tidak ada data Sub CPMK.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subCpmkData as $sub): ?>
                        <tr>
                            <td class="center">SCPMK-<?= esc($sub['no_scpmk']) ?></td>
                            <td><?= esc($sub['isi_scmpk'] ?? $sub['narasi_sub_cpmk'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 5. Pemetaan CPL - CPMK - Sub CPMK -->
        <h3 class="sub-title">5. Pemetaan CPL &ndash; CPMK &ndash; Sub CPMK</h3>
        <h4 class="table-caption">Tabel 4. Pemetaan CPMK terhadap Sub CPMK</h4>
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width:35%;">CPMK</th>
                    <th colspan="<?= count($subCpmkData) ?>">Sub CPMK</th>
                </tr>
                <tr>
                    <?php foreach ($subCpmkData as $sub): ?>
                        <th><?= esc($sub['no_scpmk']) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cpmkData)): ?>
                    <tr>
                        <td colspan="<?= count($subCpmkData) + 1 ?>" class="center">Tidak ada data pemetaan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cpmkData as $cpmk): ?>
                        <tr>
                            <td>
                                <strong>CPMK <?= esc($cpmk['no_cpmk']) ?></strong><br>
                                <?= esc($cpmk['isi_cpmk'] ?? $cpmk['narasi_cpmk'] ?? '') ?>
                            </td>
                            <?php foreach ($subCpmkData as $sub): ?>
                                <td class="center middle">
                                    <?= isset($mappingData[$cpmk['id']][$sub['id']]) ? '✓' : '-' ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 6. Dokumen RPS -->
        <h3 class="sub-title">6. Dokumen Rencana Pembelajaran Semester (RPS)</h3>
        <p>Terlampir</p>
        <p class="insert-pdf">INSERT_PDF_RPS</p>

        <div class="page-break"></div>

        <!-- 7. Rancangan Asesmen -->
        <h3 class="sub-title">7. Rancangan Asesmen</h3>
        <h4 class="table-caption">Tabel 5. Rancangan Asesmen per CPMK</h4>
        <table>
            <thead>
                <tr>
                    <th>CPMK</th>
                    <th>Tugas</th>
                    <th>UTS</th>
                    <th>UAS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($assessmentData)): ?>
                    <?php
                    $cpmkLookup = [];
                    foreach ($cpmkData as $c) {
                        $cpmkLookup[$c['id']] = $c['isi_cpmk'] ?? $c['narasi_cpmk'] ?? '';
                    }
                    ?>
                    <?php foreach ($assessmentData as $row): ?>
                        <tr>
                            <td>
                                <?= esc($row['no_cpmk']) ?>
                                <?php if (!empty($cpmkLookup[$row['id_cpmk']])): ?>
                                    <br><small><?= esc($cpmkLookup[$row['id_cpmk']]) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="center"><?= $row['tugas'] ? '✓' : '-' ?></td>
                            <td class="center"><?= $row['uts'] ? '✓' : '-' ?></td>
                            <td class="center"><?= $row['uas'] ? '✓' : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="center">Tidak ada data asesmen.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
        // ======================================================
        // PERBAIKAN: Filter kategori soal berdasarkan Rancangan Asesmen
        // Tampilkan hanya Tugas/UTS/UAS yang dipilih di Rancangan Asesmen
        // ======================================================
        $availableKategori = [];
        if (!empty($assessmentData)) {
            // Cek setiap baris asesmen untuk menentukan kategori yang tersedia
            foreach ($assessmentData as $row) {
                if (!empty($row['tugas']) && !in_array('tugas', $availableKategori)) {
                    $availableKategori[] = 'tugas';
                }
                if (!empty($row['uts']) && !in_array('uts', $availableKategori)) {
                    $availableKategori[] = 'uts';
                }
                if (!empty($row['uas']) && !in_array('uas', $availableKategori)) {
                    $availableKategori[] = 'uas';
                }
            }
        }

        // Jika tidak ada data asesmen, gunakan data soal yang ada
        if (empty($availableKategori) && !empty($assessmentSoalData)) {
            $availableKategori = array_unique(array_column($assessmentSoalData, 'kategori_soal'));
        }

        // Kelompokkan data soal berdasarkan kategori
        $groupedSoal = [];
        foreach ($assessmentSoalData as $soal) {
            $groupedSoal[$soal['kategori_soal']][] = $soal;
        }
        $cpmkLookup = [];
        foreach ($cpmkData as $c) {
            $cpmkLookup[$c['id']] = $c;
        }

        // Mapping untuk display label
        $kategoriLabel = [
            'tugas' => 'Tugas',
            'uts' => 'UTS',
            'uas' => 'UAS'
        ];
        ?>

        <?php
        // Tampilkan hanya kategori yang tersedia (sesuai Rancangan Asesmen)
        $displayIndex = 6;
        
        // Jika assessmentSoalData kosong, pastikan tetap array
        if (!is_array($assessmentSoalData)) {
            $assessmentSoalData = [];
        }
        
        foreach ($availableKategori as $kategori):
            // Cek apakah ada data soal untuk kategori ini
            $hasData = !empty($groupedSoal[$kategori]);
            $labelKategori = $kategoriLabel[$kategori] ?? $kategori;
        ?>
            <h4 class="table-caption">Tabel <?= $displayIndex++ ?>. Distribusi Soal <?= esc($labelKategori) ?></h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">No. Soal</th>
                        <?php foreach ($cpmkData as $c): ?>
                            <th style="width: <?= floor(85 / count($cpmkData)) ?>%;">CPMK <?= esc($c['no_cpmk']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasData): ?>
                        <?php
                        // Kelompokkan soal berdasarkan nomor soal
                        $bySoal = [];
                        foreach ($groupedSoal[$kategori] as $item) {
                            $bySoal[$item['no_soal']][] = $item;
                        }
                        ?>
                        <?php foreach ($bySoal as $noSoal => $items): ?>
                            <tr>
                                <td class="center"><?= esc($noSoal) ?></td>
                                <?php foreach ($cpmkData as $c): ?>
                                    <?php
                                    $checked = false;
                                    foreach ($items as $item) {
                                        if ($item['id_cpmk'] == $c['id']) {
                                            $checked = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <td class="center"><?= $checked ? '✓' : '-' ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Tampilkan total bobot jika ada -->
                        <?php
                        $totalBobot = array_sum(array_column($groupedSoal[$kategori], 'nilai'));
                        if ($totalBobot > 0):
                        ?>
                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                <td class="center">Total Bobot</td>
                                <?php foreach ($cpmkData as $c): ?>
                                    <?php
                                    $bobotCpmk = 0;
                                    foreach ($groupedSoal[$kategori] as $item) {
                                        if ($item['id_cpmk'] == $c['id']) {
                                            $bobotCpmk += (float)$item['nilai'];
                                        }
                                    }
                                    ?>
                                    <td class="center"><?= $bobotCpmk > 0 ? number_format($bobotCpmk, 2) : '-' ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($cpmkData) + 1 ?>" class="center">
                                <em>Data soal untuk <?= esc($labelKategori) ?> belum diinput. Silakan lengkapi di Tahap 7 (Rancangan Soal).</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <?php if ($showTugas): ?>
        <div class="page-break"></div>

        <!-- 7.1 Tugas -->
        <div class="lampiran-section">
            <h3>7.1 Tugas</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_TUGAS</p>
        </div>
        <?php endif; ?>

        <?php if ($showUts): ?>
        <div class="page-break"></div>

        <!-- 7.2 UTS -->
        <div class="lampiran-section">
            <h3>7.2 Ujian Tengah Semester</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_UTS</p>
        </div>
        <?php endif; ?>

        <?php if ($showUas): ?>
        <div class="page-break"></div>

        <!-- 7.3 UAS -->
        <div class="lampiran-section">
            <h3>7.3 Ujian Akhir Semester</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_UAS</p>
        </div>
        <?php endif; ?>

        <div class="page-break"></div>

        <!-- ======================================================
         BAGIAN B: PELAKSANAAN PERKULIAHAN
    ====================================================== -->
        <h2 class="section-title">B. Pelaksanaan Perkuliahan</h2>

        <!-- B.1 Kontrak Kuliah -->
        <div class="lampiran-section">
            <h3>1. Kontrak Kuliah</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_KONTRAK</p>
        </div>

        <div class="page-break"></div>

        <!-- B.2 Realisasi Mengajar -->
        <div class="lampiran-section">
            <h3>2. Realisasi Mengajar</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_REALISASI</p>
        </div>

        <div class="page-break"></div>

        <!-- B.3 Kehadiran Mahasiswa -->
        <div class="lampiran-section">
            <h3>3. Kehadiran Mahasiswa</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_KEHADIRAN</p>
        </div>

        <div class="page-break"></div>

        <!-- ======================================================
         BAGIAN C: HASIL PERKULIAHAN
    ====================================================== -->
        <h2 class="section-title">C. Hasil Perkuliahan</h2>

        <?php if ($showTugas): ?>
        <!-- C.1 Hasil Tugas -->
        <div class="lampiran-section">
            <h3>1. Hasil Tugas</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_HASIL_TUGAS</p>
        </div>

        <div class="page-break"></div>
        <?php endif; ?>

        <?php if ($showUts): ?>
        <!-- C.2 Hasil UTS -->
        <div class="lampiran-section">
            <h3>2. Hasil Ujian Tengah Semester</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_HASIL_UTS</p>
        </div>

        <div class="page-break"></div>
        <?php endif; ?>

        <?php if ($showUas): ?>
        <!-- C.3 Hasil UAS -->
        <div class="lampiran-section">
            <h3>3. Hasil Ujian Akhir Semester</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_HASIL_UAS</p>
        </div>

        <div class="page-break"></div>
        <?php endif; ?>

        <!-- C.4 Nilai Mata Kuliah -->
        <div class="lampiran-section">
            <h3>4. Nilai Mata Kuliah</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_NILAI_MATA_KULIAH</p>
        </div>

        <div class="page-break"></div>

        <!-- C.5 Nilai CPMK -->
        <div class="lampiran-section">
            <h3>5. Nilai CPMK</h3>
            <p>Terlampir</p>
            <p class="insert-pdf">INSERT_PDF_NILAI_CPMK</p>
        </div>

        <div class="page-break"></div>

        <!-- ======================================================
         BAGIAN D: EVALUASI PERKULIAHAN
    ====================================================== -->
        <h2 class="section-title">D. Evaluasi Perkuliahan</h2>

        <p class="evaluasi-text">
            <?= nl2br(esc($evaluasi['kesimpulan'] ?? 'Belum ada evaluasi yang diisi.')) ?>
        </p>

        <?php if (!empty($chartImageBase64)): ?>
            <div class="chart-container">
                <p style="font-weight:bold; margin-bottom:8px;">Grafik Capaian Nilai CPMK</p>
                <img src="<?= $chartImageBase64 ?>" alt="Grafik Nilai CPMK">
            </div>
        <?php else: ?>
            <p><em>Grafik tidak tersedia (tidak ada data CPMK atau koneksi ke server chart gagal).</em></p>
        <?php endif; ?>

        <?php if (!empty($evaluasiData)): ?>
            <h4 class="table-caption" style="margin-top:20px;">Tabel Rekapitulasi Nilai CPMK</h4>
            <table>
                <thead>
                    <tr>
                        <th>CPMK</th>
                        <th>Rata-rata Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluasiData as $eval): ?>
                        <tr>
                            <td class="center">CPMK <?= esc($eval['no_cpmk'] ?? '-') ?></td>
                            <td class="center"><?= number_format((float)($eval['rata_rata'] ?? 0), 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="ttd-section">
            <div class="ttd-box">
                <p>Semarang, <?= date('d F Y') ?></p>
                <p>Disusun Oleh,</p>
                <p>Dosen Koordinator / Pengampu Mata Kuliah</p>
                <div class="ttd-space"></div>
                <p><strong><?= esc($portofolioData['nama_dosen'] ?? '-') ?></strong></p>
                <p>NPP: <?= esc($portofolioData['npp'] ?? '-') ?></p>
            </div>
        </div>

</body>

</html>