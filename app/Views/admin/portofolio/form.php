<?= $this->extend('template') ?>

<?= $this->section('title') ?>Form Portofolio Mata Kuliah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* ── Stepper ── */
    .stepper-wrap {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 24px 28px 20px;
        margin-bottom: 28px;
        overflow-x: auto;
    }

    .stepper {
        display: flex;
        align-items: flex-start;
        min-width: 860px;
        /* The connector lives here as a pseudo on the row itself */
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        cursor: pointer;
    }

    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 17px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: var(--border);
        z-index: 0;
    }

    .step-item.completed:not(:last-child)::after {
        background: var(--primary);
    }

    .step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: grid;
        place-items: center;
        font-size: 12px;
        font-weight: 700;
        background: #fff;
        color: var(--text-muted);
        position: relative;
        z-index: 1;
        transition: all .25s;
        flex-shrink: 0;
    }

    .step-item.active .step-circle {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .18);
    }

    .step-item.completed .step-circle {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
    }

    .step-item.completed .step-circle::after {
        content: '✓';
    }

    .step-item.completed .step-circle .step-num {
        display: none;
    }

    .step-label {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 6px;
        text-align: center;
        line-height: 1.3;
        max-width: 72px;
    }

    .step-item.active .step-label {
        color: var(--primary);
    }

    .step-item.completed .step-label {
        color: var(--text-sub);
    }

    /* ── Cards ── */
    .form-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
    }

    .form-card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .form-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--primary-light);
        display: grid;
        place-items: center;
        color: var(--primary);
        font-size: 20px;
        flex-shrink: 0;
    }

    .form-card-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .form-card-subtitle {
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .form-card-body {
        padding: 24px;
    }

    /* ── Steps ── */
    .step-panel {
        display: none;
    }

    .step-panel.active {
        display: block;
    }

    /* ── CPL Table ── */
    .cpl-table thead th {
        background: var(--accent) !important;
        color: #fff;
        font-size: 13px;
    }

    .cpl-table tbody td {
        font-size: 13px;
        vertical-align: middle;
    }

    .cpl-tag {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
    }

    /* ── CPMK Builder ── */
    .cpmk-block {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 16px;
        background: #fafbfc;
        transition: border-color .2s;
    }

    .cpmk-block:hover {
        border-color: var(--primary);
    }

    .cpmk-block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .cpmk-num {
        font-size: 13px;
        font-weight: 700;
        color: var(--primary);
        background: var(--primary-light);
        padding: 3px 12px;
        border-radius: 99px;
    }

    .sub-cpmk-list {
        margin-top: 12px;
        border-top: 1px dashed var(--border);
        padding-top: 12px;
    }

    .sub-cpmk-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .sub-num {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        min-width: 60px;
    }

    /* ── Mapping Table ── */
    .mapping-table th,
    .mapping-table td {
        text-align: center;
        vertical-align: middle;
        font-size: 12.5px;
    }

    .mapping-table thead th {
        background: var(--accent);
        color: #fff;
    }

    .mapping-table .cpl-col,
    .mapping-table .cpmk-col {
        text-align: left;
    }

    .mapping-table input[type=checkbox] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    /* ── Assessment Table ── */
    .assess-table th {
        background: var(--accent);
        color: #fff;
        font-size: 13px;
    }

    .assess-table td {
        vertical-align: middle;
        font-size: 13px;
    }

    .assess-table input[type=checkbox] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    /* ── Upload area ── */
    .upload-zone {
        border: 2px dashed var(--border);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #fafbfc;
    }

    .upload-zone:hover,
    .upload-zone.has-file {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .upload-zone i {
        font-size: 28px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .upload-zone.has-file i {
        color: var(--success);
    }

    .upload-zone p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 0;
    }

    .upload-zone .file-name {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--success);
    }

    .upload-zone input[type=file] {
        display: none;
    }

    /* ── Navigation ── */
    .step-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .progress-info {
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* ── Section label ── */
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .section-dot {
        width: 4px;
        height: 22px;
        background: var(--primary);
        border-radius: 2px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
    }

    /* ── Chart ── */
    .chart-wrap {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    /* ── Readonly badge ── */
    .readonly-badge {
        background: #f1f5f9;
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 8px 14px;
        font-size: 13px;
        color: var(--text-sub);
    }

    /* ── File status ── */
    .file-status {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        padding: 8px 12px;
        border-radius: 7px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: var(--success);
        margin-top: 6px;
    }

    /* Soal item */
    .soal-item {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 10px;
        background: #fff;
    }

    .soal-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    /* Kurikulum alert info */
    .info-strip {
        background: var(--primary-light);
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: var(--text-sub);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="fw-bold" style="font-size:20px;">Form Portofolio Mata Kuliah</div>
        <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">Lengkapi semua tahapan untuk menyusun portofolio perkuliahan</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:12px;padding:6px 14px;" id="statusBadge">Tahap 1 dari 10</span>
        <button class="btn btn-sm btn-outline-secondary" onclick="resetForm()"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
    </div>
</div>

<!-- Stepper -->
<div class="stepper-wrap">
    <div class="stepper" id="stepper">
        <div class="step-item active" onclick="goToStep(1)">
            <div class="step-circle"><span class="step-num">1</span></div>
            <div class="step-label">Upload RPS</div>
        </div>
        <div class="step-item" onclick="goToStep(2)">
            <div class="step-circle"><span class="step-num">2</span></div>
            <div class="step-label">Info Matakuliah</div>
        </div>
        <div class="step-item" onclick="goToStep(3)">
            <div class="step-circle"><span class="step-num">3</span></div>
            <div class="step-label">CPL & PI</div>
        </div>
        <div class="step-item" onclick="goToStep(4)">
            <div class="step-circle"><span class="step-num">4</span></div>
            <div class="step-label">CPMK & Sub CPMK</div>
        </div>
        <div class="step-item" onclick="goToStep(5)">
            <div class="step-circle"><span class="step-num">5</span></div>
            <div class="step-label">Pemetaan CPL-CPMK</div>
        </div>
        <div class="step-item" onclick="goToStep(6)">
            <div class="step-circle"><span class="step-num">6</span></div>
            <div class="step-label">Rancangan Asesmen</div>
        </div>
        <div class="step-item" onclick="goToStep(7)">
            <div class="step-circle"><span class="step-num">7</span></div>
            <div class="step-label">Rancangan Soal</div>
        </div>
        <div class="step-item" onclick="goToStep(8)">
            <div class="step-circle"><span class="step-num">8</span></div>
            <div class="step-label">Pelaksanaan</div>
        </div>
        <div class="step-item" onclick="goToStep(9)">
            <div class="step-circle"><span class="step-num">9</span></div>
            <div class="step-label">Hasil Asesmen</div>
        </div>
        <div class="step-item" onclick="goToStep(10)">
            <div class="step-circle"><span class="step-num">10</span></div>
            <div class="step-label">Evaluasi</div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 1: Upload RPS ═══════════════ -->
<div class="step-panel active" id="step-1">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-file-upload"></i></div>
            <div>
                <div class="form-card-title">Upload RPS</div>
                <div class="form-card-subtitle">Unggah dokumen Rencana Pembelajaran Semester sebagai dasar portofolio</div>
            </div>
        </div>
        <div class="form-card-body">

            <?php if (!empty($rps['file_rps'])): ?>
                <!-- ── RPS sudah ada ── -->
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-check-circle"></i>
                    RPS sudah diupload sebelumnya. Anda dapat langsung melanjutkan atau mengganti file jika diperlukan.
                </div>

                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="section-header">
                            <div class="section-dot"></div>
                            <div class="section-title">File RPS Tersimpan</div>
                        </div>

                        <!-- Info file existing -->
                        <div class="file-status mb-3" style="display:flex;">
                            <i class="fas fa-check-circle"></i>
                            <span id="rpsFileName"><?= esc($rps['file_rps']) ?></span>
                            <span class="ms-auto" style="color:var(--text-muted);font-size:11.5px;">
                                Diupload: <?= date('d M Y H:i', strtotime($rps['created_at'])) ?>
                            </span>
                        </div>

                        <!-- Area upload pengganti (hidden by default) -->
                        <div id="replaceRPSArea">
                            <div class="upload-zone" id="rpsZone" onclick="document.getElementById('rps_file').click()">
                                <input type="file" id="rps_file"
                                    accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    onchange="handleFileSelect(this, 'rpsZone', 'rpsPreview')">
                                <i class="fas fa-cloud-upload-alt" id="rpsIcon"></i>
                                <p class="fw-semibold" style="color:var(--text-sub);font-size:13.5px;margin-bottom:4px;">Klik untuk memilih file pengganti</p>
                                <p>Format: PDF / DOC / DOCX • Maksimal 10MB</p>
                                <div id="rpsPreview" style="display:none;" class="file-name mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau PDF existing dari server -->
                    <div class="col-lg-12">
                        <div class="section-header">
                            <div class="section-dot"></div>
                            <div class="section-title">Pratinjau RPS</div>
                        </div>
                        <div id="rpsPdfViewer" style="height:420px;border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                            <iframe src="<?= base_url('admin/portofolio/rps/' . esc($rps['file_rps'])) ?>"
                                width="100%"
                                height="100%"
                                style="border:none;">
                            </iframe>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- ── Belum ada RPS ── -->
                <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-info-circle"></i>
                    Silahkan upload file RPS sebelum melanjutkan ke tahap berikutnya.
                </div>

                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="section-header">
                            <div class="section-dot"></div>
                            <div class="section-title">File RPS</div>
                        </div>
                        <div class="upload-zone" id="rpsZone" onclick="document.getElementById('rps_file').click()">
                            <input type="file" id="rps_file"
                                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                onchange="handleFileSelect(this, 'rpsZone', 'rpsPreview')">
                            <i class="fas fa-cloud-upload-alt" id="rpsIcon"></i>
                            <p class="fw-semibold" style="color:var(--text-sub);font-size:13.5px;margin-bottom:4px;">Klik untuk memilih file</p>
                            <p>Format: PDF / DOC / DOCX • Maksimal 10MB</p>
                            <div id="rpsPreview" style="display:none;" class="file-name mt-2"></div>
                        </div>
                        <div id="rpsStatus" style="display:none;" class="file-status mt-2">
                            <i class="fas fa-check-circle"></i>
                            <span id="rpsFileNameNew">file.pdf</span>
                            <span class="ms-auto" id="rpsFileSize" style="color:var(--text-muted);"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="section-header">
                            <div class="section-dot"></div>
                            <div class="section-title">Pratinjau RPS</div>
                        </div>
                        <div id="rpsPdfViewer" style="height:320px;border:1px solid var(--border);border-radius:10px;overflow:hidden;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                            <div class="text-center text-muted">
                                <i class="fas fa-file-pdf" style="font-size:48px;margin-bottom:10px;opacity:.3;"></i>
                                <p style="font-size:13px;">Pratinjau file RPS akan muncul di sini</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="step-nav">
                <span class="progress-info">Tahap 1 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep1AndNext(this)">
                    Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 2: Info Mata Kuliah ═══════════════ -->
<div class="step-panel" id="step-2">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-book"></i></div>
            <div>
                <div class="form-card-title">Informasi Mata Kuliah</div>
                <div class="form-card-subtitle">Informasi mata kuliah diambil otomatis dari data perkuliahan Anda</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-info-circle"></i>
                Data mata kuliah ditampilkan berdasarkan perkuliahan yang dipilih saat membuat portofolio.
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Mata Kuliah</label>
                    <input type="text" class="form-control bg-light" value="<?= esc($porto['nama_mk']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kode MK</label>
                    <input type="text" class="form-control bg-light" value="<?= esc($porto['kode_mk']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKS Teori</label>
                    <input type="number" class="form-control bg-light" value="<?= esc($porto['teori']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKS Praktik</label>
                    <input type="number" class="form-control bg-light" value="<?= esc($porto['praktek']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kurikulum</label>
                    <input type="text" class="form-control bg-light" value="<?= esc($porto['nama_kurikulum']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Semester MK</label>
                    <input type="text" class="form-control bg-light" value="Semester <?= esc($porto['semester']) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tahun Akademik</label>
                    <input type="text" class="form-control bg-light" value="<?= esc($porto['tahun_akademik']) ?>" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Mata Kuliah Prasyarat</label>
                    <textarea class="form-control" id="mk_prasyarat" rows="2"
                        placeholder="Isi jika ada prasyarat, kosongkan jika tidak ada"><?= esc($info_mk['mk_prasyarat'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Topik Perkuliahan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="topik_mk" rows="3"
                        placeholder="Deskripsikan topik-topik yang akan dibahas dalam perkuliahan ini"><?= esc($info_mk['topik_perkuliahan'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 2 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep2AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 3: CPL & PI ═══════════════ -->
<div class="step-panel" id="step-3">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-project-diagram"></i></div>
            <div>
                <div class="form-card-title">Capaian Pembelajaran Lulusan (CPL) & Performance Index (PI)</div>
                <div class="form-card-subtitle">Data CPL dan PI ditarik otomatis berdasarkan mata kuliah yang dipilih</div>
            </div>
        </div>
        <div class="form-card-body">
            <div id="cplInfoStrip" class="info-strip">
                <i class="fas fa-check-circle text-success"></i>
                <span>CPL dan PI ditampilkan berdasarkan: <strong id="cplMKName">—</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered cpl-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:15%">No. CPL</th>
                            <th style="width:45%">Capaian Pembelajaran Lulusan</th>
                            <th style="width:40%">Performance Index (PI)</th>
                        </tr>
                    </thead>
                    <tbody id="cplTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 3 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep3AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 4: CPMK & Sub CPMK ═══════════════ -->
<div class="step-panel" id="step-4">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-sitemap"></i></div>
            <div>
                <div class="form-card-title">CPMK & Sub Capaian Pembelajaran Mata Kuliah</div>
                <div class="form-card-subtitle">Buat struktur capaian pembelajaran mata kuliah dan kaitkan dengan CPL</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-info-circle"></i>
                Tambahkan CPMK, pilih CPL yang terkait, isi narasi, lalu tambahkan Sub CPMK.
            </div>
            <div id="cpmkContainer">
                <!-- CPMK blocks rendered by JS -->
            </div>
            <button class="btn btn-success" onclick="addCPMK()">
                <i class="fas fa-plus me-1"></i> Tambah CPMK
            </button>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 4 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep4AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 5: Pemetaan CPL-CPMK-SubCPMK ═══════════════ -->
<div class="step-panel" id="step-5">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-table"></i></div>
            <div>
                <div class="form-card-title">Pemetaan CPL – CPMK – Sub CPMK</div>
                <div class="form-card-subtitle">Centang keterkaitan antara CPMK dengan Sub CPMK untuk setiap CPL</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-info-circle"></i>
                Tandai (☑) Sub CPMK yang berkaitan dengan setiap CPMK.
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mapping-table" id="mappingTable">
                    <thead>
                        <tr id="mappingHeaderRow">
                            <th class="cpl-col">CPL</th>
                            <th class="cpmk-col">CPMK</th>
                            <!-- Sub CPMK headers added by JS -->
                        </tr>
                    </thead>
                    <tbody id="mappingBody">
                    </tbody>
                </table>
            </div>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 5 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep5AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 6: Rancangan Asesmen ═══════════════ -->
<div class="step-panel" id="step-6">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="form-card-title">Rancangan Asesmen & Jadwal Asesmen</div>
                <div class="form-card-subtitle">Tentukan jenis asesmen per CPMK dan upload soal & rubrik penilaian</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="section-header">
                <div class="section-dot"></div>
                <div class="section-title">Rancangan Jadwal Asesmen</div>
            </div>
            <div class="table-responsive mb-4">
                <table class="table table-bordered assess-table" id="assessTable">
                    <thead>
                        <tr>
                            <th>CPMK</th>
                            <th style="width:120px;">Tugas</th>
                            <th style="width:120px;">UTS</th>
                            <th style="width:120px;">UAS</th>
                        </tr>
                    </thead>
                    <tbody id="assessBody">
                    </tbody>
                </table>
            </div>

            <!-- Upload area per asesmen -->
            <div id="assessUploadSection">
                <!-- Tugas -->
                <div id="tugasUploadArea" class="mb-4" style="display:none;">
                    <div class="section-header">
                        <div class="section-dot" style="background:#f59e0b;"></div>
                        <div class="section-title">Tugas</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Soal Tugas (PDF)</label>
                            <div id="tugasSoalExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="tugasSoalFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('tugasSoal')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="tugasSoalUploadZone" onclick="document.getElementById('soal_tugas').click()">
                                <input type="file" id="soal_tugas" accept="application/pdf" onchange="handleFileUpload(this,'soalTugasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalTugasStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div id="tugasRubrikExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="tugasRubrikFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('tugasRubrik')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="tugasRubrikUploadZone" onclick="document.getElementById('rubrik_tugas').click()">
                                <input type="file" id="rubrik_tugas" accept="application/pdf" onchange="handleFileUpload(this,'rubrikTugasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Rubrik</p>
                            </div>
                            <div id="rubrikTugasStatus" style="display:none;" class="file-status"></div>
                        </div>
                    </div>
                </div>

                <!-- UTS -->
                <div id="utsUploadArea" class="mb-4" style="display:none;">
                    <div class="section-header">
                        <div class="section-dot" style="background:#0ea5e9;"></div>
                        <div class="section-title">UTS</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Soal UTS (PDF)</label>
                            <div id="utsSoalExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="utsSoalFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('utsSoal')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="utsSoalUploadZone" onclick="document.getElementById('soal_uts').click()">
                                <input type="file" id="soal_uts" accept="application/pdf" onchange="handleFileUpload(this,'soalUtsStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalUtsStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div id="utsRubrikExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="utsRubrikFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('utsRubrik')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="utsRubrikUploadZone" onclick="document.getElementById('rubrik_uts').click()">
                                <input type="file" id="rubrik_uts" accept="application/pdf" onchange="handleFileUpload(this,'rubrikUtsStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Rubrik</p>
                            </div>
                            <div id="rubrikUtsStatus" style="display:none;" class="file-status"></div>
                        </div>
                    </div>
                </div>

                <!-- UAS -->
                <div id="uasUploadArea" class="mb-4" style="display:none;">
                    <div class="section-header">
                        <div class="section-dot" style="background:#8b5cf6;"></div>
                        <div class="section-title">UAS</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Soal UAS (PDF)</label>
                            <div id="uasSoalExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="uasSoalFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('uasSoal')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="uasSoalUploadZone" onclick="document.getElementById('soal_uas').click()">
                                <input type="file" id="soal_uas" accept="application/pdf" onchange="handleFileUpload(this,'soalUasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalUasStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div id="uasRubrikExisting" style="display:none;" class="mb-2">
                                <div class="file-status" style="display:flex;align-items:center;gap:8px;">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="uasRubrikFileName">—</span>
                                    <!-- <a href="#" class="ms-2" onclick="showPdfPreview('uasRubrik')" title="Lihat PDF"><i class="fas fa-eye"></i></a> -->
                                </div>
                            </div>
                            <div class="upload-zone" id="uasRubrikUploadZone" onclick="document.getElementById('rubrik_uas').click()">
                                <input type="file" id="rubrik_uas" accept="application/pdf" onchange="handleFileUpload(this,'rubrikUasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Rubrik</p>
                            </div>
                            <div id="rubrikUasStatus" style="display:none;" class="file-status"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 6 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep6AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 7: Rancangan Soal ═══════════════ -->
<div class="step-panel" id="step-7">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-question-circle"></i></div>
            <div>
                <div class="form-card-title">Rancangan Soal</div>
                <div class="form-card-subtitle">Susun detail soal sesuai asesmen yang telah dipilih</div>
            </div>
        </div>
        <div class="form-card-body">
            <div id="soalContainer">
                <!-- Soal sections rendered by JS -->
            </div>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 7 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep7AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 8: Pelaksanaan ═══════════════ -->
<div class="step-panel" id="step-8">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-chalkboard"></i></div>
            <div>
                <div class="form-card-title">Pelaksanaan Perkuliahan</div>
                <div class="form-card-subtitle">Dokumentasi pelaksanaan perkuliahan</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-info-circle"></i>
                Upload dokumentasi pelaksanaan perkuliahan (kontrak kuliah, realisasi mengajar, kehadiran).
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kontrak Kuliah (PDF)</label>
                    <div class="upload-zone" onclick="document.getElementById('file_kontrak_kuliah').click()">
                        <input type="file" id="file_kontrak_kuliah" accept="application/pdf" onchange="handleFileUpload(this,'kontrakKuliahStatus')">
                        <i class="fas fa-file-signature"></i>
                        <p>Upload Kontrak Kuliah</p>
                    </div>
                    <div id="kontrakKuliahStatus" style="display:none;" class="file-status"></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Realisasi Mengajar (PDF)</label>
                    <div class="upload-zone" onclick="document.getElementById('file_realisasi_mengajar').click()">
                        <input type="file" id="file_realisasi_mengajar" accept="application/pdf" onchange="handleFileUpload(this,'realisasiMengajarStatus')">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Upload Realisasi Mengajar</p>
                    </div>
                    <div id="realisasiMengajarStatus" style="display:none;" class="file-status"></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kehadiran (PDF)</label>
                    <div class="upload-zone" onclick="document.getElementById('file_kehadiran').click()">
                        <input type="file" id="file_kehadiran" accept="application/pdf" onchange="handleFileUpload(this,'kehadiranStatus')">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Upload Kehadiran</p>
                    </div>
                    <div id="kehadiranStatus" style="display:none;" class="file-status"></div>
                </div>
            </div>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 8 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep8AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 9: Hasil Asesmen ═══════════════ -->
<div class="step-panel" id="step-9">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="form-card-title">Hasil Asesmen</div>
                <div class="form-card-subtitle">Upload hasil jawaban mahasiswa untuk setiap asesmen</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-info-circle"></i>
                Upload rekapitulasi jawaban mahasiswa untuk Tugas, UTS, dan/atau UAS.
            </div>
            <div id="hasilAsesmenContainer" class="row g-3">
                <!-- Rendered by JS -->
            </div>
            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 9 dari 10</span>
                <button class="btn btn-primary px-4" onclick="saveStep9AndNext(this)">Selanjutnya <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ STEP 10: Evaluasi ═══════════════ -->
<div class="step-panel" id="step-10">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon"><i class="fas fa-chart-bar"></i></div>
            <div>
                <div class="form-card-title">Evaluasi & Kesimpulan</div>
                <div class="form-card-subtitle">Analisis capaian pembelajaran dan evaluasi perkuliahan</div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-check-circle"></i>
                Tahap akhir! Isi nilai setiap CPMK dan lihat grafik capaian.
            </div>

            <div class="section-header mb-3">
                <div class="section-dot"></div>
                <div class="section-title">Nilai per CPMK</div>
            </div>
            <div class="row g-3 mb-4" id="cpmkValueInputs">
                <!-- Rendered by JS -->
            </div>

            <div class="chart-wrap">
                <canvas id="cpmkChart" height="100"></canvas>
            </div>

            <div class="section-header mb-3">
                <div class="section-dot"></div>
                <div class="section-title">Ringkasan Portofolio</div>
            </div>
            <div class="row g-3 p-3" style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;" id="summaryContent">
                <!-- Rendered by JS -->
            </div>

            <div class="step-nav">
                <button class="btn btn-outline-secondary px-4" onclick="prevStep()"><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <span class="progress-info">Tahap 10 dari 10</span>
                <button class="btn btn-success px-4" onclick="submitForm(this)"><i class="fas fa-save me-1"></i>Simpan Portofolio</button>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL ALERT MODAL -->
<div class="modal fade" id="globalAlertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title" id="globalAlertTitle">
                    <i class="fas fa-exclamation-circle me-2"></i> Informasi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="globalAlertMessage" style="font-size:14px;">
                Pesan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Data CPL dari server, di-encode sebagai JSON
    const CPL_DATA = <?= json_encode($cpls ?? []) ?>;

    let cpmkCounter = 0;
    let chartInstance = null;

    // ══════════════════════════════════════════
    // Show Modal Alert
    // ══════════════════════════════════════════
    function showModalAlert(message, type = 'warning') {

        const modalEl = document.getElementById('globalAlertModal');
        const titleEl = document.getElementById('globalAlertTitle');
        const messageEl = document.getElementById('globalAlertMessage');
        const header = modalEl.querySelector('.modal-header');

        // Reset warna header
        header.classList.remove('bg-primary', 'bg-danger', 'bg-warning', 'bg-success');

        // Set warna sesuai tipe
        if (type === 'danger') {
            header.classList.add('bg-danger');
            titleEl.innerHTML = '<i class="fas fa-times-circle me-2"></i> Kesalahan';
        } else if (type === 'success') {
            header.classList.add('bg-success');
            titleEl.innerHTML = '<i class="fas fa-check-circle me-2"></i> Berhasil';
        } else if (type === 'warning') {
            header.classList.add('bg-warning');
            titleEl.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Perhatian';
        } else {
            header.classList.add('bg-primary');
            titleEl.innerHTML = '<i class="fas fa-info-circle me-2"></i> Informasi';
        }

        messageEl.innerHTML = message;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // ══════════════════════════════════════════
    //  NAVIGATION
    // ══════════════════════════════════════════
    function goToStep(n) {
        if (n < 1 || n > TOTAL_STEPS) return;
        document.getElementById(`step-${currentStep}`).classList.remove('active');
        currentStep = n;
        document.getElementById(`step-${currentStep}`).classList.add('active');
        updateStepper();
        updateStatusBadge();
        onStepEnter(n);
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function nextStep() {
        if (currentStep < TOTAL_STEPS) goToStep(currentStep + 1);
    }

    function prevStep() {
        if (currentStep > 1) goToStep(currentStep - 1);
    }

    function updateStepper() {
        document.querySelectorAll('.step-item').forEach((el, i) => {
            el.classList.remove('active', 'completed');
            if (i + 1 < currentStep) el.classList.add('completed');
            if (i + 1 === currentStep) el.classList.add('active');
        });
    }

    function updateStatusBadge() {
        document.getElementById('statusBadge').textContent = `Tahap ${currentStep} dari ${TOTAL_STEPS}`;
    }

    function onStepEnter(n) {
        if (n === 1) loadDataRPS();
        if (n === 2) renderMKDropdown();
        if (n === 3) loadDataCPLPI();
        if (n === 4) loadDataMapping();
        if (n === 5) loadDataPemetaan();
        if (n === 6) loadDataRancanganAsesmen();
        if (n === 7) loadDataRancanganSoal();
        if (n === 8) renderPelaksanaan();
        if (n === 9) loadDataHasilAsesmen();
        if (n === 10) loadDataEvaluasi();
    }

    // ══════════════════════════════════════════
    //  STEP 1 — RPS
    // ══════════════════════════════════════════
    function loadDataRPS() {
        const DB_RPS = <?= json_encode($rps ?? []) ?>;

        if (!DB_RPS || !DB_RPS.file_rps) return; // Belum ada RPS, tampilan default sudah benar

        // Update file name display
        const rpsFileNameEl = document.getElementById('rpsFileName');
        if (rpsFileNameEl) {
            rpsFileNameEl.textContent = DB_RPS.file_rps;
        }

        // Update iframe preview jika sudah ada iframe
        const viewer = document.getElementById('rpsPdfViewer');
        if (!viewer) return;

        // Cek apakah iframe sudah ada dengan src yang benar
        const existingIframe = viewer.querySelector('iframe');
        const correctSrc = BASE_URL + 'admin/portofolio/rps/' + DB_RPS.file_rps;

        if (existingIframe) {
            // Iframe sudah ada, pastikan src-nya benar
            if (existingIframe.src !== correctSrc) {
                existingIframe.src = correctSrc;
            }
        } else {
            // Iframe belum ada (misal user baru pilih file non-PDF sebelumnya)
            viewer.style.height = '420px';
            viewer.style.background = '';
            viewer.style.display = '';
            viewer.innerHTML = `
            <iframe src="${correctSrc}"
                width="100%"
                height="100%"
                style="border:none;">
            </iframe>
        `;
        }
    }

    // ══════════════════════════════════════════
    //  STEP 2 — Info Mata Kuliah
    // ══════════════════════════════════════════
    function saveMK() {
        state.mk.mk_prasyarat = document.getElementById('mk_prasyarat').value;
        state.mk.topik_mk = document.getElementById('topik_mk').value;
    }

    // ══════════════════════════════════════════
    //  STEP 3 — CPL & PI
    // ══════════════════════════════════════════
    function loadDataCPLPI() {
        // Gunakan state.cpl yang sudah diinisialisasi di DOMContentLoaded
        const data = state.cpl || [];

        document.getElementById('cplMKName').textContent =
            '<?= esc($porto['nama_mk']) ?> (<?= esc($porto['kode_mk']) ?>)';

        const tbody = document.getElementById('cplTableBody');
        tbody.innerHTML = '';

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            Tidak ada data CPL untuk mata kuliah ini.
        </td></tr>`;
            return;
        }

        data.forEach(cpl => {
            const rowspan = cpl.pis.length || 1;

            if (!cpl.pis.length) {
                // CPL tanpa PI
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="text-center align-middle"><span class="cpl-tag">${cpl.no_cpl}</span></td>
                <td class="align-middle" style="font-size:13px;">${cpl.narasi}</td>
                <td class="text-muted" style="font-size:12px;">—</td>`;
                tbody.appendChild(tr);
                return;
            }

            cpl.pis.forEach((pi, i) => {
                const tr = document.createElement('tr');
                if (i === 0) {
                    tr.innerHTML = `
                    <td rowspan="${rowspan}" class="text-center align-middle">
                        <span class="cpl-tag">${cpl.no_cpl}</span>
                    </td>
                    <td rowspan="${rowspan}" class="align-middle" style="font-size:13px;">${cpl.narasi}</td>
                    <td style="font-size:13px;"><strong>${pi.no_pi}</strong> — ${pi.isi}</td>`;
                } else {
                    tr.innerHTML = `
                    <td style="font-size:13px;"><strong>${pi.no_pi}</strong> — ${pi.isi}</td>`;
                }
                tbody.appendChild(tr);
            });
        });
    }

    // ══════════════════════════════════════════
    //  STEP 4 — CPMK & Sub CPMK
    // ══════════════════════════════════════════
    function loadDataMapping() {
        const container = document.getElementById('cpmkContainer');

        // Sudah ada isi di DOM (misal balik dari step 5) → jangan render ulang
        if (container.children.length > 0) return;

        if (DB_CPMKS.length > 0) {
            // ── Ada data dari DB → render dari DB ──
            DB_CPMKS.forEach((cpmk) => {
                addCPMKFromData(cpmk);
            });
        } else if (state.cpmkList.length > 0) {
            // ── Ada data di state (pindah step bolak-balik) → render dari state ──
            state.cpmkList.forEach((cpmk) => {
                addCPMKFromData(cpmk);
            });
        } else {
            // ── Belum ada data sama sekali → tambah 1 block kosong ──
            addCPMK();
        }
    }

    function addCPMKFromData(cpmk) {
        const container = document.getElementById('cpmkContainer');

        const cplOptions = (state.cpl || []).map((c) =>
            `<option value="${c.id}" ${String(c.id) === String(cpmk.id_cpl) ? 'selected' : ''}>${c.no_cpl}</option>`
        ).join('');

        const blockId = `cpmk_${Date.now()}_${Math.random().toString(36).slice(2, 6)}`;
        const div = document.createElement('div');
        div.className = 'cpmk-block';
        div.id = blockId;
        div.innerHTML = `
        <div class="cpmk-block-header">
            <span class="cpmk-num cpmk-label">CPMK ?</span>
            <button class="btn btn-sm btn-outline-danger" onclick="removeCPMK('${blockId}')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:12.5px;">Terkait CPL</label>
                <select class="form-select form-select-sm" id="${blockId}_cpl">${cplOptions}</select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold" style="font-size:12.5px;">Narasi CPMK</label>
                <input type="text" class="form-control form-control-sm" id="${blockId}_narasi"
                    placeholder="Deskripsikan capaian pembelajaran mata kuliah ini..."
                    value="${escHtml(cpmk.narasi || '')}">
            </div>
        </div>
        <div class="sub-cpmk-list">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <small class="fw-bold text-muted" style="font-size:12px;">Sub CPMK</small>
                <button class="btn btn-sm btn-outline-primary" style="font-size:11px;"
                    onclick="addSubCPMK('${blockId}')">
                    <i class="fas fa-plus me-1"></i>Tambah Sub
                </button>
            </div>
            <div id="${blockId}_subs"></div>
        </div>`;
        container.appendChild(div);
        renumberCPMK();

        // Render sub CPMK
        if (cpmk.subs && cpmk.subs.length > 0) {
            cpmk.subs.forEach((sub) => addSubCPMKFromData(blockId, sub));
        } else {
            addSubCPMK(blockId); // default 1 sub kosong
        }

        // Simpan mapping DB id ke state
        if (cpmk.id) {
            const no = document.querySelectorAll('.cpmk-block').length; // nomor urut setelah append
            state.cpmkIdMap[no] = cpmk.id;
            (cpmk.subs || []).forEach((s) => {
                state.subIdMap[`${no}_${s.no}`] = s.id;
            });
        }
    }

    /**
     * Render satu Sub CPMK dengan nilai yang sudah terisi
     */
    function addSubCPMKFromData(cpmkBlockId, sub) {
        const subsDiv = document.getElementById(`${cpmkBlockId}_subs`);
        if (!subsDiv) return;

        const subId = `sub_${Date.now()}_${Math.random().toString(36).slice(2, 6)}`;
        const div = document.createElement('div');
        div.className = 'sub-cpmk-item';
        div.id = subId;
        div.innerHTML = `
        <div style="display:flex;align-items:center;gap:10px;width:100%;">
            <span class="sub-num" style="white-space:nowrap;font-size:12px;font-weight:700;color:var(--text-muted);">Sub</span>
            <input type="number"
                class="form-control form-control-sm sub-no-input"
                style="width:70px;flex-shrink:0;"
                value="${sub.no}"
                min="1"
                title="Nomor Sub CPMK">
            <input type="text"
                class="form-control form-control-sm sub-narasi-input"
                placeholder="Narasi Sub CPMK..."
                value="${escHtml(sub.narasi || '')}">
            <button class="btn btn-sm btn-outline-secondary"
                style="flex-shrink:0;"
                onclick="removeSubCPMK('${subId}', '${cpmkBlockId}')">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
        subsDiv.appendChild(div);
    }

    /** Escape HTML untuk value attribute agar tidak XSS / break HTML */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function addCPMK() {
        const container = document.getElementById('cpmkContainer');

        const cplOptions = (state.cpl || []).map(c =>
            `<option value="${c.id}">${c.no_cpl}</option>`
        ).join('');

        const blockId = `cpmk_${Date.now()}`; // unique ID, bukan counter
        const div = document.createElement('div');
        div.className = 'cpmk-block';
        div.id = blockId;
        div.innerHTML = `
        <div class="cpmk-block-header">
            <span class="cpmk-num cpmk-label">CPMK ?</span>
            <button class="btn btn-sm btn-outline-danger" onclick="removeCPMK('${blockId}')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:12.5px;">Terkait CPL</label>
                <select class="form-select form-select-sm" id="${blockId}_cpl">${cplOptions}</select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold" style="font-size:12.5px;">Narasi CPMK</label>
                <input type="text" class="form-control form-control-sm" id="${blockId}_narasi"
                    placeholder="Deskripsikan capaian pembelajaran mata kuliah ini...">
            </div>
        </div>
        <div class="sub-cpmk-list">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <small class="fw-bold text-muted" style="font-size:12px;">Sub CPMK</small>
                <button class="btn btn-sm btn-outline-primary" style="font-size:11px;"
                    onclick="addSubCPMK('${blockId}')">
                    <i class="fas fa-plus me-1"></i>Tambah Sub
                </button>
            </div>
            <div id="${blockId}_subs"></div>
        </div>`;
        container.appendChild(div);

        renumberCPMK(); // assign nomor setelah append
        addSubCPMK(blockId); // langsung tambah 1 sub default
    }

    function removeCPMK(id) {
        document.getElementById(id)?.remove();
        renumberCPMK();
    }

    /** Update label "CPMK N" di setiap block sesuai urutan DOM */
    function renumberCPMK() {
        document.querySelectorAll('.cpmk-block').forEach((block, i) => {
            const no = i + 1;
            const label = block.querySelector('.cpmk-label');
            if (label) label.textContent = `CPMK ${no}`;
        });
    }

    /** Tambah Sub CPMK ke dalam block tertentu, dengan input nomor yang bisa diubah */
    function addSubCPMK(cpmkBlockId) {
        const subsDiv = document.getElementById(`${cpmkBlockId}_subs`);
        if (!subsDiv) return;

        const subCount = subsDiv.children.length + 1;
        const subId = `sub_${Date.now()}_${subCount}`;

        const div = document.createElement('div');
        div.className = 'sub-cpmk-item';
        div.id = subId;
        div.innerHTML = `
        <div style="display:flex;align-items:center;gap:10px;width:100%;">
            <span class="sub-num" style="white-space:nowrap;font-size:12px;font-weight:700;color:var(--text-muted);">Sub</span>
            <input type="number"
                class="form-control form-control-sm sub-no-input"
                style="width:70px;flex-shrink:0;"
                value="${subCount}"
                min="1"
                title="Nomor Sub CPMK (bisa diubah)">
            <input type="text"
                class="form-control form-control-sm sub-narasi-input"
                placeholder="Narasi Sub CPMK ${subCount}...">
            <button class="btn btn-sm btn-outline-secondary"
                style="flex-shrink:0;"
                onclick="removeSubCPMK('${subId}', '${cpmkBlockId}')">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
        subsDiv.appendChild(div);
    }

    /** Hapus sub, lalu renumber otomatis semua sub dalam block yang sama */
    function removeSubCPMK(subId, cpmkBlockId) {
        document.getElementById(subId)?.remove();
        renumberSubCPMK(cpmkBlockId);
    }

    /** Update value input nomor Sub CPMK sesuai urutan DOM dalam satu block */
    function renumberSubCPMK(cpmkBlockId) {
        const subsDiv = document.getElementById(`${cpmkBlockId}_subs`);
        if (!subsDiv) return;
        subsDiv.querySelectorAll('.sub-cpmk-item').forEach((item, i) => {
            const noInput = item.querySelector('.sub-no-input');
            const narasiInput = item.querySelector('.sub-narasi-input');
            if (noInput) noInput.value = i + 1;
            if (narasiInput && !narasiInput.value) {
                narasiInput.placeholder = `Narasi Sub CPMK ${i + 1}...`;
            }
        });
    }

    function saveCPMK() {
        state.cpmkList = [];
        document.querySelectorAll('.cpmk-block').forEach((block, i) => {
            const id = block.id;
            const no = i + 1; // nomor urutan DOM, bukan dari input
            const cpl = document.getElementById(`${id}_cpl`)?.value;
            const narasi = document.getElementById(`${id}_narasi`)?.value;
            const subs = [];
            block.querySelectorAll('.sub-cpmk-item').forEach((subItem) => {
                const noInput = subItem.querySelector('.sub-no-input');
                const narasiInput = subItem.querySelector('.sub-narasi-input');
                subs.push({
                    no: noInput ? parseInt(noInput.value) || (subs.length + 1) : subs.length + 1,
                    narasi: narasiInput ? narasiInput.value : ''
                });
            });
            state.cpmkList.push({
                no,
                cpl,
                narasi,
                subs
            });
        });
    }

    // ══════════════════════════════════════════
    //  STEP 5 — Pemetaan CPL-CPMK-SubCPMK
    // ══════════════════════════════════════════
    function loadDataPemetaan() {
        console.log('=== RENDER MAPPING TABLE START ===');
        console.log('Current state.cpmkList:', state.cpmkList);
        console.log('Current state.mapping:', state.mapping);
        console.log('Current state.cpmkIdMap:', state.cpmkIdMap);
        console.log('Current state.subIdMap:', state.subIdMap);
        console.log('DB_MAPPINGS:', typeof DB_MAPPINGS !== 'undefined' ? DB_MAPPINGS : 'undefined');

        const hasDomCPMK = document.querySelectorAll('.cpmk-block').length > 0;
        if (hasDomCPMK) {
            saveCPMK();
        }

        if (!state.cpmkList.length) {
            console.warn('state.cpmkList kosong');
            return;
        }

        // Buat map id_cpl → no_cpl untuk label
        const cplLabelMap = {};
        (state.cpl || []).map(c => {
            cplLabelMap[c.id] = c.no_cpl;
        });
        console.log('cplLabelMap:', cplLabelMap);

        // ── Kumpulkan SEMUA nomor sub yang ada di SEMUA CPMK (global unique set) ──
        const allSubNos = new Set();
        state.cpmkList.forEach(c => {
            console.log(`CPMK ${c.no} memiliki subs:`, c.subs);
            c.subs.forEach(s => allSubNos.add(parseInt(s.no)));
        });
        console.log('allSubNos:', Array.from(allSubNos));

        const maxSub = allSubNos.size > 0 ? Math.max(...allSubNos) : 0;
        const subColumns = Array.from({
            length: maxSub
        }, (_, i) => i + 1);
        console.log('subColumns (yang akan ditampilkan):', subColumns);

        // ── Build header ──
        const headerRow = document.getElementById('mappingHeaderRow');
        if (!headerRow) {
            console.error('❌ mappingHeaderRow tidak ditemukan!');
            return;
        }

        headerRow.innerHTML = `
        <th rowspan="2" class="align-middle" style="background:var(--accent);color:#fff;min-width:160px;">CPL</th>
        <th rowspan="2" class="align-middle" style="background:var(--accent);color:#fff;min-width:180px;">CPMK</th>
        <th colspan="${subColumns.length}" class="text-center" style="background:var(--accent);color:#fff;">Sub CPMK</th>`;

        // Sub-header row (nomor sub)
        let subHeaderRow = document.getElementById('mappingSubHeaderRow');
        if (!subHeaderRow) {
            subHeaderRow = document.createElement('tr');
            subHeaderRow.id = 'mappingSubHeaderRow';
            headerRow.parentNode.insertBefore(subHeaderRow, headerRow.nextSibling);
        }
        subHeaderRow.innerHTML = subColumns.map(n =>
            `<th class="text-center" style="background:var(--accent);color:#fff;min-width:60px;">${n}</th>`
        ).join('');

        // ── Group CPMK by CPL ──
        const byCpl = {};
        state.cpmkList.forEach(c => {
            const key = c.cpl;
            if (!byCpl[key]) byCpl[key] = [];
            byCpl[key].push(c);
        });
        console.log('CPMKs grouped by CPL:', byCpl);

        const tbody = document.getElementById('mappingBody');
        if (!tbody) {
            console.error('❌ mappingBody tidak ditemukan!');
            return;
        }
        tbody.innerHTML = '';

        Object.entries(byCpl).forEach(([cplId, cpmks]) => {
            const cplLabel = cplLabelMap[cplId] || 'CPL ' + cplId;
            console.log(`Processing CPL ${cplLabel} (ID: ${cplId}) dengan ${cpmks.length} CPMK`);

            cpmks.forEach((cpmk, i) => {
                const tr = document.createElement('tr');
                let html = '';

                // CPL cell dengan rowspan
                if (i === 0) {
                    html += `<td rowspan="${cpmks.length}" class="align-middle" style="font-size:13px;">
                    <span class="cpl-tag">${cplLabel}</span>
                </td>`;
                }

                // CPMK cell
                html += `<td class="align-middle" style="font-size:13px;">
                <strong>CPMK ${cpmk.no}</strong><br>
                <small style="color:var(--text-muted);font-size:11.5px;">${cpmk.narasi || ''}</small>
            </td>`;

                // Sub CPMK cells — SEMUA kolom (1..maxSub) untuk setiap CPMK
                subColumns.forEach(subNo => {
                    const mappingExists = state.mapping &&
                        state.mapping[String(cplId)] &&
                        state.mapping[String(cplId)][String(cpmk.no)] &&
                        state.mapping[String(cplId)][String(cpmk.no)].includes(subNo);

                    // Debug untuk mapping tertentu
                    if (mappingExists) {
                        console.log(`✅ Mapping ditemukan: CPL ${cplId} - CPMK ${cpmk.no} - Sub ${subNo}`);
                    }

                    const isChecked = mappingExists || false;

                    // Cari ID untuk data attribute
                    const idCpmk = state.cpmkIdMap[cpmk.no] || '';
                    const idSub = state.subIdMap[`${cpmk.no}_${subNo}`] || '';

                    html += `<td class="text-center align-middle">
                    <input type="checkbox"
                        class="mapping-checkbox form-check-input"
                        style="width:20px;height:20px;cursor:pointer;accent-color:var(--primary);"
                        data-cpl="${cplId}"
                        data-cpmk="${cpmk.no}"
                        data-sub="${subNo}"
                        data-id-cpmk="${idCpmk}"
                        data-id-sub="${idSub}"
                        ${isChecked ? 'checked' : ''}>
                </td>`;
                });

                tr.innerHTML = html;
                tbody.appendChild(tr);
            });
        });

        console.log('✅ loadDataPemetaan selesai. Total checkbox:', document.querySelectorAll('.mapping-checkbox').length);
        console.log('=== RENDER MAPPING TABLE END ===\n');
    }

    // Di bagian inisialisasi DOMContentLoaded, tambahkan console.log untuk melihat data dari database:

    document.addEventListener('DOMContentLoaded', () => {
        // ── 1. Inisialisasi state.cpl dari CPL_DATA ────────────────────────────
        if (typeof CPL_DATA !== 'undefined' && CPL_DATA.length > 0) {
            const grouped = {};
            CPL_DATA.forEach(row => {
                if (!grouped[row.id]) {
                    grouped[row.id] = {
                        id: row.id,
                        no_cpl: row.no_cpl,
                        narasi: row.cpl_indo,
                        pis: []
                    };
                }
                if (row.id_pi) {
                    grouped[row.id].pis.push({
                        id: row.id_pi,
                        no_pi: row.no_pi,
                        isi: row.isi_pi
                    });
                }
            });
            state.cpl = Object.values(grouped);
        }

        // ── 2. Isi state.cpmkList & ID map dari DB_CPMKS ──────────────────────
        if (typeof DB_CPMKS !== 'undefined' && DB_CPMKS.length > 0) {

            state.cpmkIdMap = {};
            state.subIdMap = {};

            state.cpmkList = DB_CPMKS.map((c, i) => {
                const no = i + 1;
                state.cpmkIdMap[no] = c.id;

                (c.subs || []).forEach((s) => {
                    // key: "cpmkNo_subNo" → db sub id
                    state.subIdMap[`${no}_${s.no}`] = s.id;
                });

                return {
                    no,
                    cpl: String(c.id_cpl),
                    narasi: c.narasi,
                    subs: (c.subs || []).map(s => ({
                        no: s.no,
                        narasi: s.narasi,
                    })),
                };
            });
        }

        // ── 3. Bangun state.mapping dari DB_MAPPINGS ──────────────────────────
        if (typeof DB_MAPPINGS !== 'undefined' && DB_MAPPINGS.length > 0) {

            const mappingMap = {};

            DB_MAPPINGS.forEach(map => {
                const {
                    id_cpl,
                    id_cpmk,
                    id_sub_cpmk
                } = map;

                // Cari cpmkNo (1,2,3,...) dari state.cpmkIdMap
                const cpmkNo = Object.keys(state.cpmkIdMap).find(
                    key => Number(state.cpmkIdMap[key]) === Number(id_cpmk)
                );
                if (!cpmkNo) {
                    return;
                }

                // Cari subNo dari state.subIdMap
                const subKey = Object.keys(state.subIdMap).find(
                    key => Number(state.subIdMap[key]) === Number(id_sub_cpmk)
                );
                if (!subKey) {
                    console.warn('⚠️ DB_MAPPINGS: id_sub_cpmk tidak ditemukan di subIdMap:', id_sub_cpmk, state.subIdMap);
                    return;
                }
                const subNo = parseInt(subKey.split('_')[1]);

                const cplKey = String(id_cpl);

                if (!mappingMap[cplKey]) mappingMap[cplKey] = {};
                if (!mappingMap[cplKey][String(cpmkNo)]) mappingMap[cplKey][String(cpmkNo)] = [];
                mappingMap[cplKey][String(cpmkNo)].push(subNo);
            });

            state.mapping = mappingMap;
        } else {
            console.log('DB_MAPPINGS tidak ada atau kosong');
        }

        // ── 4. Auto-resume ke last_step ────────────────────────────────────────
        if (typeof LAST_STEP !== 'undefined' && LAST_STEP > 1) {
            goToStep(LAST_STEP);
        }
    });


    /**
     * Kumpulkan semua checkbox yang dicentang, lalu bangun array mappingData yang akan dikirim ke server
     */
    function saveMapping() {
        const checkboxes = document.querySelectorAll('.mapping-checkbox:checked');

        state.mappingData = [];

        checkboxes.forEach(cb => {

            const idCpl = cb.dataset.cpl;
            const idCpmk = cb.dataset.idCpmk;
            const idSub = cb.dataset.idSub;

            if (!idCpl || !idCpmk || !idSub) return;

            state.mappingData.push({
                id_cpl: parseInt(idCpl),
                id_cpmk: parseInt(idCpmk),
                id_sub_cpmk: parseInt(idSub),
            });
        });

        console.log('Mapping yang akan dikirim:', state.mappingData);
    }

    // ══════════════════════════════════════════
    //  STEP 6 — Rancangan Asesmen
    // ══════════════════════════════════════════
    function loadDataRancanganAsesmen() {
        const tbody = document.getElementById('assessBody');
        tbody.innerHTML = '';

        // Ensure assessmentByCpmk exists in state
        if (!state.assessmentByCpmk) {
            state.assessmentByCpmk = {};
        }

        state.cpmkList.forEach(c => {
            const tr = document.createElement('tr');

            // Check which assessments are already selected for this CPMK
            const globalAssessments = [];
            if (state.assessment.tugas) globalAssessments.push('tugas');
            if (state.assessment.uts) globalAssessments.push('uts');
            if (state.assessment.uas) globalAssessments.push('uas');
            const selectedAssessments = state.assessmentByCpmk?.[c.no] || globalAssessments;

            const tugasChecked = selectedAssessments.includes('tugas') ? 'checked' : '';
            const utsChecked = selectedAssessments.includes('uts') ? 'checked' : '';
            const uasChecked = selectedAssessments.includes('uas') ? 'checked' : '';

            tr.innerHTML = `
            <td class="align-middle"><strong>CPMK ${c.no}</strong><br><small style="color:var(--text-muted);font-size:11.5px;">${c.narasi || ''}</small></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="tugas" data-cpmk="${c.no}" onchange="updateAssessUpload()" ${tugasChecked}></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="uts" data-cpmk="${c.no}" onchange="updateAssessUpload()" ${utsChecked}></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="uas" data-cpmk="${c.no}" onchange="updateAssessUpload()" ${uasChecked}></td>`;
            tbody.appendChild(tr);
        });

        // After rendering, trigger updateAssessUpload
        updateAssessUpload();

        // Force show existing files for all types
        ['Tugas', 'Uts', 'Uas'].forEach(function(type) {
            showExistingFileForType(type);
        });
    }

    function updateAssessUpload() {
        const tugas = document.querySelector('.assess-cb[data-type="tugas"]:checked');
        const uts = document.querySelector('.assess-cb[data-type="uts"]:checked');
        const uas = document.querySelector('.assess-cb[data-type="uas"]:checked');

        const tugasVisible = tugas ? 'block' : 'none';
        const utsVisible = uts ? 'block' : 'none';
        const uasVisible = uas ? 'block' : 'none';

        document.getElementById('tugasUploadArea').style.display = tugasVisible;
        document.getElementById('utsUploadArea').style.display = utsVisible;
        document.getElementById('uasUploadArea').style.display = uasVisible;

        state.assessment.tugas = !!tugas;
        state.assessment.uts = !!uts;
        state.assessment.uas = !!uas;

        // Show existing files when area becomes visible
        if (tugasVisible === 'block') {
            showExistingFileForType('Tugas');
        }
        if (utsVisible === 'block') {
            showExistingFileForType('Uts');
        }
        if (uasVisible === 'block') {
            showExistingFileForType('Uas');
        }
    }

    /**
     * Show existing files for a specific assessment type
     */
    function showExistingFileForType(type) {
        const fileTypes = ['Soal', 'Rubrik'];

        fileTypes.forEach(function(ft) {
            const key = type + ft;
            const fileName = state.existingFiles[key];

            if (fileName) {
                showExistingFile(type, ft, fileName);
            }
        });
    }

    function saveAssessment() {
        updateAssessUpload();
        if (!state.soalData) state.soalData = {};
    }

    // ══════════════════════════════════════════
    //  STEP 7 — Rancangan Soal
    // ══════════════════════════════════════════
    function loadDataRancanganSoal() {
        const container = document.getElementById('soalContainer');
        container.innerHTML = '';

        if (!state.cpmkList.length) {
            container.innerHTML = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle me-2"></i>
            Data CPMK belum tersedia. Kembali ke Tahap 4.
        </div>`;
            return;
        }

        // Urutan jenis asesmen
        const orderedTypes = [];
        let idx = 1;
        if (state.assessment.tugas) orderedTypes.push({
            key: 'tugas',
            label: idx++ + '. Tugas',
            color: '#f59e0b'
        });
        if (state.assessment.uts) orderedTypes.push({
            key: 'uts',
            label: idx++ + '. Ujian Tengah Semester (UTS)',
            color: '#0ea5e9'
        });
        if (state.assessment.uas) orderedTypes.push({
            key: 'uas',
            label: idx++ + '. Ujian Akhir Semester (UAS)',
            color: '#8b5cf6'
        });

        if (!orderedTypes.length) {
            container.innerHTML = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle me-2"></i>
            Belum ada jenis asesmen yang dipilih. Kembali ke Tahap 6.
        </div>`;
            return;
        }

        // Buat mapping nomor CPMK untuk header
        const cpmkNos = state.cpmkList.map(c => c.no);

        // Gunakan data dari state.soalData yang sudah dikonversi saat DOMContentLoaded
        // Jangan langsung pakai DB_SOAL karena strukturnya berbeda
        const soalDataFromDB = typeof DB_SOAL !== 'undefined' ? DB_SOAL : {};

        orderedTypes.forEach(type => {
            // Inisialisasi state.soalData[type.key] jika belum ada
            if (!state.soalData[type.key]) {
                state.soalData[type.key] = [];

                // Jika ada data dari database, konversi ke format yang sesuai
                if (soalDataFromDB[type.key]) {
                    Object.keys(soalDataFromDB[type.key]).forEach(nomorSoal => {
                        const soalData = soalDataFromDB[type.key][nomorSoal];
                        const soal = {
                            soal_no: parseInt(nomorSoal),
                            cpmk_mappings: {}
                        };

                        // Tandai CPMK yang tercakup dalam soal ini
                        if (soalData.cpmk_list && soalData.cpmk_list.length > 0) {
                            soalData.cpmk_list.forEach(cpmk => {
                                // Cari nomor CPMK berdasarkan id
                                const cpmkNo = Object.keys(state.cpmkIdMap).find(
                                    key => Number(state.cpmkIdMap[key]) === Number(cpmk.id_cpmk)
                                );
                                if (cpmkNo) {
                                    soal.cpmk_mappings[cpmkNo] = true;
                                }
                            });
                        }

                        state.soalData[type.key].push(soal);
                    });

                    // Urutkan berdasarkan nomor soal
                    state.soalData[type.key].sort((a, b) => a.soal_no - b.soal_no);
                } else {
                    // Default: 1 soal
                    state.soalData[type.key] = [{
                        soal_no: 1,
                        cpmk_mappings: {}
                    }];
                }
            }

            const soalList = state.soalData[type.key];

            // Header CPMK
            const cpmkHeaders = cpmkNos.map(no =>
                `<th class="text-center align-middle" style="min-width:80px;font-size:12px;">CPMK ${no}</th>`
            ).join('');

            // Buat rows untuk setiap soal
            const rows = soalList.map((soal, i) => {
                // Untuk setiap CPMK, cek apakah soal ini untuk CPMK tersebut
                const cells = cpmkNos.map(cno => {
                    // Cek apakah CPMK ini tercakup dalam soal
                    const isChecked = soal.cpmk_mappings && soal.cpmk_mappings[cno] ? true : false;

                    return `
                <td class="text-center align-middle">
                    <input type="checkbox"
                        class="soal-cb form-check-input"
                        style="width:18px;height:18px;cursor:pointer;accent-color:var(--primary);"
                        data-type="${type.key}"
                        data-soal="${i}"
                        data-cpmk="${cno}"
                        ${isChecked ? 'checked' : ''}>
                </td>`;
                }).join('');

                const deleteBtn = soalList.length > 1 ?
                    `<button type="button" class="btn btn-sm btn-outline-danger px-2"
                    onclick="removeRancanganSoal('${type.key}', ${i})">
                    <i class="fas fa-trash-alt" style="font-size:11px;"></i>
                </button>` :
                    `<span style="color:var(--text-muted);font-size:11px;">—</span>`;

                return `
            <tr>
                <td class="text-center align-middle fw-bold" style="font-size:13px;">
                    Soal no ${soal.soal_no}
                </td>
                ${cells}
                <td class="text-center align-middle">${deleteBtn}</td>
            </tr>`;
            }).join('');

            const section = document.createElement('div');
            section.className = 'mb-5';
            section.setAttribute('data-type', type.key);
            section.innerHTML = `
        <div class="section-header">
            <div class="section-dot" style="background:${type.color};"></div>
            <div class="section-title">${type.label}</div>
        </div>
        <div class="alert alert-primary d-flex align-items-center gap-2 py-2 mb-3" style="font-size:13px;">
            <i class="fas fa-info-circle"></i>
            Centang CPMK yang diukur oleh masing-masing soal.
        </div>
        <div class="table-responsive mb-3">
            <table class="table table-bordered mb-0">
                <thead style="background:#0f4c92;" class="text-white">
                    <tr class="align-middle text-center">
                        <th style="min-width:110px;font-size:12px;">Soal No</th>
                        ${cpmkHeaders}
                        <th style="min-width:70px;font-size:12px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
        <button type="button" class="btn btn-secondary btn-sm"
            onclick="addRancanganSoal('${type.key}')">
            <i class="fas fa-plus me-1"></i> Tambah Soal
        </button>`;

            container.appendChild(section);
        });
    }

    function addRancanganSoal(type) {
        saveRancanganSoalMapping();

        if (!state.soalData[type]) state.soalData[type] = [];

        const existing = state.soalData[type];

        // Cari nomor terbesar yang ada
        const maxNo = existing.length ?
            Math.max(...existing.map(s => s.soal_no)) :
            0;

        existing.push({
            soal_no: maxNo + 1,
            cpmk_mappings: {}
        });

        loadDataRancanganSoal();
    }

    function removeRancanganSoal(type, idx) {
        // WAJIB: sync checkbox DOM → state dulu sebelum render ulang
        saveRancanganSoalMapping();

        state.soalData[type].splice(idx, 1);
        state.soalData[type].forEach(function(s, i) {
            s.soal_no = i + 1;
        });

        loadDataRancanganSoal();
    }

    function saveRancanganSoalMapping() {
        document.querySelectorAll('.soal-cb').forEach(function(cb) {
            var type = cb.dataset.type;
            var soalIdx = parseInt(cb.dataset.soal); // index array (0-based)
            var cpmk = cb.dataset.cpmk;

            if (state.soalData[type] && state.soalData[type][soalIdx] !== undefined) {
                if (!state.soalData[type][soalIdx].cpmk_mappings) {
                    state.soalData[type][soalIdx].cpmk_mappings = {};
                }
                state.soalData[type][soalIdx].cpmk_mappings[cpmk] = cb.checked;
            }
        });
    }

    // ══════════════════════════════════════════
    //  STEP 8 — Pelaksanaan Perkuliahan
    // ══════════════════════════════════════════
    function renderPelaksanaan() {
        const files = [{
                key: 'kontrak_kuliah',
                label: 'Kontrak Kuliah',
                inputId: 'file_kontrak_kuliah',
                statusId: 'kontrakKuliahStatus',
                icon: 'fa-file-signature',
                color: '#0ea5e9',
                dbField: 'file_kontrak_kuliah',
            },
            {
                key: 'realisasi_mengajar',
                label: 'Realisasi Mengajar',
                inputId: 'file_realisasi_mengajar',
                statusId: 'realisasiMengajarStatus',
                icon: 'fa-chalkboard-teacher',
                color: '#10b981',
                dbField: 'file_realisasi_mengajar',
            },
            {
                key: 'kehadiran',
                label: 'Kehadiran Mahasiswa',
                inputId: 'file_kehadiran',
                statusId: 'kehadiranStatus',
                icon: 'fa-clipboard-list',
                color: '#f59e0b',
                dbField: 'file_kehadiran',
            },
        ];

        files.forEach(({
            key,
            label,
            inputId,
            statusId,
            icon,
            color,
            dbField
        }) => {
            const existingFileName = DB_PELAKSANAAN?.[dbField] ?? null;
            const existingId = `existing_${key}`;
            const zoneId = `zone_${key}`;

            // Cari container upload zone
            const inputEl = document.getElementById(inputId);
            if (!inputEl) return;

            const uploadZone = inputEl.closest('.upload-zone');
            if (!uploadZone) return;

            const col = uploadZone.closest('.col-md-4');
            if (!col) return;

            // Hapus existing badge sebelumnya jika ada (hindari duplikat saat masuk ulang)
            const oldBadge = col.querySelector(`#${existingId}`);
            if (oldBadge) oldBadge.remove();

            if (existingFileName) {
                // ── Ada file tersimpan → tampilkan info file + zone ganti ──
                const badge = document.createElement('div');
                badge.id = existingId;
                badge.className = 'mb-2';
                badge.innerHTML = `
                <div class="file-status" style="display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;">
                    <i class="fas fa-check-circle" style="color:#16a34a;"></i>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:12.5px;color:#166534;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            ${existingFileName}
                        </div>
                        <div style="font-size:11px;color:#6b7280;margin-top:1px;">
                            File tersimpan — upload ulang untuk mengganti
                        </div>
                    </div>
                    <a href="${BASE_URL}admin/portofolio/preview-pelaksanaan/${existingFileName}"
                        target="_blank"
                        title="Lihat PDF"
                        class="btn btn-sm btn-outline-success"
                        style="flex-shrink:0;font-size:11px;padding:2px 8px;">
                        <i class="fas fa-eye me-1"></i>Lihat
                    </a>
                </div>`;

                // Sisipkan sebelum upload-zone
                uploadZone.parentNode.insertBefore(badge, uploadZone);

                // Ubah tampilan zone menjadi "ganti"
                uploadZone.querySelector('i').className = `fas ${icon}`;
                const zoneP = uploadZone.querySelectorAll('p');
                if (zoneP[0]) zoneP[0].textContent = 'Klik untuk mengganti file';
                if (zoneP[1]) zoneP[1].textContent = 'Format PDF • Maks 10MB';
                uploadZone.style.borderColor = color;
                uploadZone.style.background = '#f8fafc';
            }
        });
    }


    // ══════════════════════════════════════════
    //  STEP 9 — Hasil Asesmen
    // ══════════════════════════════════════════
    function loadDataHasilAsesmen() {
        const container = document.getElementById('hasilAsesmenContainer');
        container.innerHTML = '';

        const types = [];
        if (state.assessment.tugas) types.push({
            key: 'tugas',
            label: 'Hasil Tugas',
            color: '#f59e0b'
        });
        if (state.assessment.uts) types.push({
            key: 'uts',
            label: 'Hasil Ujian Tengah Semester',
            color: '#0ea5e9'
        });
        if (state.assessment.uas) types.push({
            key: 'uas',
            label: 'Hasil Ujian Akhir Semester',
            color: '#8b5cf6'
        });

        if (!types.length) {
            container.innerHTML = `<div class="alert alert-warning mb-3">Belum ada asesmen yang dipilih.</div>`;
            return;
        }

        types.forEach((type, idx) => {
            const div = document.createElement('div');
            div.className = 'col-md-4 mb-3';
            div.innerHTML = `
            <div class="section-header"><div class="section-dot" style="background:${type.color};"></div><div class="section-title">${idx + 1}. ${type.label}</div></div>
            <div class="upload-zone" onclick="document.getElementById('jawaban_${type.key}').click()">
                <input type="file" id="jawaban_${type.key}" accept="application/pdf" onchange="handleFileUpload(this,'hasil_${type.key}_status')">
                <i class="fas fa-file-alt"></i>
                <p>Jawaban Mahasiswa</p>
                <p style="font-size:11px;">Contoh: jawaban benar, sedang, salah</p>
            </div>
            <div id="hasil_${type.key}_status" style="display:none;" class="file-status"></div>`;
            container.appendChild(div);
        });

        // Wrap in row
        const row = document.createElement('div');
        row.className = 'row g-3';
        while (container.firstChild) row.appendChild(container.firstChild);
        container.appendChild(row);
    }

    // ══════════════════════════════════════════
    //  STEP 10 — Evaluasi Perkuliahan
    // ══════════════════════════════════════════
    function loadDataEvaluasi() {
        const container = document.getElementById('cpmkValueInputs');
        container.innerHTML = '';
        state.cpmkList.forEach(c => {
            const div = document.createElement('div');
            div.className = 'col-md-3 col-sm-4 col-6';
            div.innerHTML = `
            <div class="form-floating">
                <input type="number" class="form-control cpmk-val-input" id="cpmkVal_${c.no}" min="0" max="100" step="0.1"
                    placeholder="CPMK ${c.no}" value="${state.cpmkValues[c.no] || ''}" oninput="updateChart()">
                <label>CPMK ${c.no}</label>
            </div>`;
            container.appendChild(div);
        });

        initChart();
        renderSummary();
    }

    function initChart() {
        const ctx = document.getElementById('cpmkChart').getContext('2d');
        if (chartInstance) chartInstance.destroy();

        const labels = state.cpmkList.map(c => `CPMK ${c.no}`);
        const values = state.cpmkList.map(c => state.cpmkValues[c.no] || 0);

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Rata-Rata Nilai',
                    data: values,
                    backgroundColor: labels.map((_, i) => `hsla(${200 + i * 30}, 80%, 55%, 0.8)`),
                    borderColor: labels.map((_, i) => `hsla(${200 + i * 30}, 80%, 45%, 1)`),
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function updateChart() {
        state.cpmkList.forEach(c => {
            const el = document.getElementById(`cpmkVal_${c.no}`);
            if (el) state.cpmkValues[c.no] = parseFloat(el.value) || 0;
        });
        if (chartInstance) {
            chartInstance.data.datasets[0].data = state.cpmkList.map(c => state.cpmkValues[c.no] || 0);
            chartInstance.update();
        }
    }

    function renderSummary() {
        const div = document.getElementById('summaryContent');
        const mk = state.mk;
        div.innerHTML = `
                <div class="col-md-4"><div style="font-size:12px;color:var(--text-muted);">Mata Kuliah</div><div style="font-size:13px;font-weight:600;">${mk.nama_mk || '—'} (${mk.kode_mk || '—'})</div></div>
                <div class="col-md-4"><div style="font-size:12px;color:var(--text-muted);">CPMK Dibuat</div><div style="font-size:13px;font-weight:600;">${state.cpmkList.length} CPMK</div></div>
                <div class="col-md-4"><div style="font-size:12px;color:var(--text-muted);">Asesmen Dipilih</div><div style="font-size:13px;font-weight:600;">${[state.assessment.tugas?'Tugas':'', state.assessment.uts?'UTS':'', state.assessment.uas?'UAS':''].filter(Boolean).join(', ') || '—'}</div></div>
            `;
    }

    // ══════════════════════════════════════════
    //  FILE HANDLERS
    // ══════════════════════════════════════════
    function handleFileSelect(input, zoneId, previewId) {
        const file = input.files[0];
        if (!file) return;

        const preview = document.getElementById(previewId);
        if (preview) {
            preview.style.display = 'block';
            preview.innerText = file.name;
        }

        // PREVIEW PDF
        if (file.type === "application/pdf") {
            const reader = new FileReader();
            reader.onload = function(e) {
                const viewer = document.getElementById('rpsPdfViewer');
                if (viewer) {
                    viewer.innerHTML = `
                    <iframe src="${e.target.result}"
                        width="100%"
                        height="100%"
                        style="border:none;">
                    </iframe>
                `;
                }
            };
            reader.readAsDataURL(file);
        } else {
            // For non-PDF files, show a message
            const viewer = document.getElementById('rpsPdfViewer');
            if (viewer) {
                viewer.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-file" style="font-size:48px;margin-bottom:10px;opacity:.3;"></i>
                        <p style="font-size:13px;">Preview tidak tersedia untuk format ${file.type}</p>
                    </div>
                `;
            }
        }
    }

    function handleFileUpload(input, statusId) {
        const file = input.files[0];
        const statusEl = document.getElementById(statusId);
        const zone = input.closest('.upload-zone');

        // Hide existing file status if any
        const existingEl = zone.parentElement.querySelector('[id$="Existing"]');
        if (existingEl) {
            existingEl.style.display = 'none';
        }

        if (file && statusEl) {
            statusEl.style.display = 'flex';
            statusEl.innerHTML = `<i class="fas fa-check-circle"></i><span>${file.name}</span><span class="ms-auto" style="color:var(--text-muted);">${(file.size/1024).toFixed(1)} KB</span>`;
            if (zone) {
                zone.classList.add('has-file');
                zone.querySelector('i').className = 'fas fa-check-circle';
            }
        }
    }

    /**
     * Show existing file in upload area
     */
    function showExistingFile(type, fileType, fileName) {
        const existingContainer = document.getElementById(
            type.toLowerCase() + fileType + 'Existing'
        );

        const fileNameSpan = document.getElementById(
            type.toLowerCase() + fileType + 'FileName'
        );

        if (existingContainer && fileNameSpan) {
            fileNameSpan.textContent = fileName;
            existingContainer.style.display = 'block';
        }
    }

    /**
     * Show PDF preview in modal
     */
    function showPdfPreview(fileKey) {
        const fileMap = {
            'tugasSoal': 'soal_tugas',
            'tugasRubrik': 'rubrik_tugas',
            'utsSoal': 'soal_uts',
            'utsRubrik': 'rubrik_uts',
            'uasSoal': 'soal_uas',
            'uasRubrik': 'rubrik_uas'
        };

        const fileName = state.existingFiles[fileKey] || '';
        if (!fileName) {
            showModalAlert('File tidak ditemukan.', 'danger');
            return;
        }

        // Use the same endpoint as RPS preview
        const previewUrl = BASE_URL + 'admin/portofolio/preview-asesmen/' + fileName;

        const modalHtml = `
            <div class="modal fade" id="pdfPreviewModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-pdf me-2"></i>Pratinjau ${fileKey.replace(/([A-Z])/g, ' $1').trim()}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe src="${previewUrl}" width="100%" height="600px" style="border:none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        modal.show();

        // Cleanup modal after hidden
        document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    // ══════════════════════════════════════════
    //  SUBMIT & RESET
    // ══════════════════════════════════════════
    function submitForm() {
        updateChart();
        const toastHtml = `
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999" id="liveToastWrap">
            <div id="liveToast" class="toast show align-items-center text-bg-success border-0 shadow-lg" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-semibold"><i class="fas fa-check-circle me-2"></i>Portofolio berhasil disimpan!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('liveToastWrap').remove()"></button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => document.getElementById('liveToastWrap')?.remove(), 4000);

        // Mark all steps completed
        document.querySelectorAll('.step-item').forEach(el => el.classList.add('completed'));
    }

    function resetForm() {
        if (!confirm('Reset semua data form? Tindakan ini tidak dapat diurungkan.')) return;
        Object.assign(state, {
            rpsFile: null,
            mk: {},
            cpl: [],
            cpmkList: [],
            assessment: {
                tugas: false,
                uts: false,
                uas: false
            },
            soalData: {},
            mapping: {},
            cpmkValues: {}
        });
        cpmkCounter = 0;
        document.getElementById('cpmkContainer').innerHTML = '';
        goToStep(1);
    }

    // ══════════════════════════════════════════
    //  SIDEBAR
    // ══════════════════════════════════════════
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').classList.add('show');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('show');
    }
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) closeSidebar();
    });
</script>
<script>
    // Inject PHP
    const BASE_URL = '<?= base_url() ?>';
    const PORTO_ID = <?= json_encode($porto['id'] ?? '') ?>;
    const LAST_STEP = <?= (int)($last_step ?? 1) ?>;
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';
    const PERKULIAHAN_ID = <?= (int)($porto['id_perkuliahan'] ?? 0) ?>;
    const DB_CPMKS = <?= json_encode(
                            array_map(function ($cpmk) {
                                return [
                                    'id'     => (int) $cpmk['id'],
                                    // Ambil angka dari "CPMK-01" → 1
                                    'no'     => (int) ltrim(str_replace('CPMK-', '', $cpmk['no_cpmk']), '0') ?: 1,
                                    'id_cpl' => (int) $cpmk['id_cpl'],
                                    'narasi' => $cpmk['narasi_cpmk'],
                                    'subs'   => array_map(function ($sub) {
                                        return [
                                            'id'     => (int) $sub['id'],
                                            // Ambil angka dari "Sub-01" → 1
                                            'no'     => (int) ltrim(str_replace('Sub-', '', $sub['no_sub_cpmk']), '0') ?: 1,
                                            'narasi' => $sub['narasi_sub_cpmk'],
                                        ];
                                    }, $cpmk['subs'] ?? []),
                                ];
                            }, $cpmks ?? [])
                        ) ?>;
    const DB_MAPPINGS = <?= json_encode($mapping ?? []) ?>;
    const DB_ASSESSMEN = <?= json_encode($asesmen ?? []) ?>;
    const DB_PELAKSANAAN = <?= json_encode($pelaksanaan ?? []) ?>;
    const DB_SOAL = <?= json_encode($soal ?? []) ?>;

    document.addEventListener('DOMContentLoaded', () => {
        // ── 1. Inisialisasi state.cpl dari CPL_DATA ────────────────────────────
        if (typeof CPL_DATA !== 'undefined' && CPL_DATA.length > 0) {
            const grouped = {};
            CPL_DATA.forEach(row => {
                if (!grouped[row.id]) {
                    grouped[row.id] = {
                        id: row.id,
                        no_cpl: row.no_cpl,
                        narasi: row.cpl_indo,
                        pis: []
                    };
                }
                if (row.id_pi) {
                    grouped[row.id].pis.push({
                        id: row.id_pi,
                        no_pi: row.no_pi,
                        isi: row.isi_pi
                    });
                }
            });
            state.cpl = Object.values(grouped);
        }

        // ── 2. Isi state.cpmkList & ID map dari DB_CPMKS ──────────────────────
        if (typeof DB_CPMKS !== 'undefined' && DB_CPMKS.length > 0) {
            state.cpmkIdMap = {};
            state.subIdMap = {};

            state.cpmkList = DB_CPMKS.map((c, i) => {
                const no = i + 1; // nomor urut (1-based)
                state.cpmkIdMap[no] = c.id; // no → db_id

                (c.subs || []).forEach((s) => {
                    // key: "cpmkNo_subNo" → db sub id
                    state.subIdMap[`${no}_${s.no}`] = s.id;
                });

                return {
                    no,
                    cpl: String(c.id_cpl),
                    narasi: c.narasi,
                    subs: (c.subs || []).map(s => ({
                        no: s.no,
                        narasi: s.narasi,
                    })),
                };
            });
        }

        // ── 3. Bangun state.mapping dari DB_MAPPINGS ──────────────────────────
        // HARUS dijalankan SETELAH state.cpmkIdMap & state.subIdMap terisi (step 2)
        if (typeof DB_MAPPINGS !== 'undefined' && DB_MAPPINGS.length > 0) {
            const mappingMap = {};

            DB_MAPPINGS.forEach(map => {
                const {
                    id_cpl,
                    id_cpmk,
                    id_sub_cpmk
                } = map;

                // Cari cpmkNo (1,2,3,...) dari state.cpmkIdMap
                // state.cpmkIdMap = { 1: dbId, 2: dbId, ... }
                const cpmkNo = Object.keys(state.cpmkIdMap).find(
                    key => Number(state.cpmkIdMap[key]) === Number(id_cpmk)
                );
                if (!cpmkNo) {
                    console.warn('DB_MAPPINGS: id_cpmk tidak ditemukan di cpmkIdMap:', id_cpmk, state.cpmkIdMap);
                    return;
                }

                // Cari subNo dari state.subIdMap
                // state.subIdMap = { "1_1": dbSubId, "1_2": dbSubId, ... }
                const subKey = Object.keys(state.subIdMap).find(
                    key => Number(state.subIdMap[key]) === Number(id_sub_cpmk)
                );
                if (!subKey) {
                    console.warn('DB_MAPPINGS: id_sub_cpmk tidak ditemukan di subIdMap:', id_sub_cpmk, state.subIdMap);
                    return;
                }
                const subNo = parseInt(subKey.split('_')[1]);

                // Key di mappingMap harus String(id_cpl) agar cocok dengan loadDataPemetaan
                const cplKey = String(id_cpl);

                if (!mappingMap[cplKey]) mappingMap[cplKey] = {};
                // cpmkNo sebagai String (key object selalu string di JS)
                if (!mappingMap[cplKey][String(cpmkNo)]) mappingMap[cplKey][String(cpmkNo)] = [];
                mappingMap[cplKey][String(cpmkNo)].push(subNo);
            });

            state.mapping = mappingMap;
        }

        // ── 4. Load existing assessment data from DB_ASSESSMEN ─────────────────
        if (typeof DB_ASSESSMEN !== 'undefined' && DB_ASSESSMEN.length > 0) {
            // Reset dulu
            state.assessment.tugas = false;
            state.assessment.uts = false;
            state.assessment.uas = false;
            state.asesmenIdMap = {};

            DB_ASSESSMEN.forEach(a => {
                const jenis = a.jenis_asesmen.toLowerCase();

                if (jenis === 'tugas') state.assessment.tugas = true;
                if (jenis === 'uts') state.assessment.uts = true;
                if (jenis === 'uas') state.assessment.uas = true;

                const cpmkNo = Object.keys(state.cpmkIdMap).find(
                    key => Number(state.cpmkIdMap[key]) === Number(a.id_cpmk)
                );
                if (cpmkNo) {
                    state.asesmenIdMap[`${jenis}_${cpmkNo}`] = a.id;

                    if (!state.assessmentByCpmk) state.assessmentByCpmk = {};
                    if (!state.assessmentByCpmk[cpmkNo]) state.assessmentByCpmk[cpmkNo] = [];
                    if (!state.assessmentByCpmk[cpmkNo].includes(jenis)) {
                        state.assessmentByCpmk[cpmkNo].push(jenis);
                    }
                }

                // Simpan nama file soal & rubrik untuk masing-masing jenis asesmen
                const typeCapitalized = jenis.charAt(0).toUpperCase() + jenis.slice(1);
                if (a.file_soal) {
                    state.existingFiles[typeCapitalized + 'Soal'] = a.file_soal;
                }
                if (a.file_rubrik) {
                    state.existingFiles[typeCapitalized + 'Rubrik'] = a.file_rubrik;
                }
            });

            console.log('state.assessment:', state.assessment);
            console.log('state.asesmenIdMap:', state.asesmenIdMap);
        }

        // ── Load state.soalData dari DB_SOAL ──────────────────────
        if (typeof DB_SOAL !== 'undefined' && Object.keys(DB_SOAL).length > 0) {
            state.soalData = {};

            // Loop setiap jenis asesmen
            Object.keys(DB_SOAL).forEach(jenis => {
                state.soalData[jenis] = [];

                // Loop setiap nomor soal dalam jenis tersebut
                Object.keys(DB_SOAL[jenis]).forEach(nomorSoal => {
                    const soalData = DB_SOAL[jenis][nomorSoal];

                    const soal = {
                        soal_no: parseInt(nomorSoal),
                        cpmk_mappings: {}
                    };

                    // Tandai CPMK yang tercakup dalam soal ini
                    if (soalData.cpmk_list && soalData.cpmk_list.length > 0) {
                        soalData.cpmk_list.forEach(cpmk => {
                            // Cari nomor CPMK berdasarkan id
                            const cpmkNo = Object.keys(state.cpmkIdMap).find(
                                key => Number(state.cpmkIdMap[key]) === Number(cpmk.id_cpmk)
                            );
                            if (cpmkNo) {
                                soal.cpmk_mappings[cpmkNo] = true;
                            }
                        });
                    }

                    state.soalData[jenis].push(soal);
                });

                // Urutkan berdasarkan nomor soal
                state.soalData[jenis].sort((a, b) => a.soal_no - b.soal_no);
            });
        }

        // ── Auto-resume ke last_step ────────────────────────────────────────
        if (typeof LAST_STEP !== 'undefined' && LAST_STEP > 1) {
            goToStep(LAST_STEP);
        }
    });

    // ══════════════════════════════════════════
    //  STATE
    // ══════════════════════════════════════════
    let currentStep = 1;
    const TOTAL_STEPS = 10;

    const state = {
        rpsFile: null,
        mk: {},
        cpl: [],
        cpmkList: [],
        assessment: {
            tugas: false,
            uts: false,
            uas: false
        },
        soalData: {},
        mapping: {},
        cpmkValues: {},
        existingFiles: {},
    };

    const API = {
        rps: BASE_URL + 'admin/portofolio/step/rps',
        infoMK: BASE_URL + 'admin/portofolio/step/info-mk',
        cpl: BASE_URL + 'admin/portofolio/step/cpl',
        cpmk: BASE_URL + 'admin/portofolio/step/cpmk',
        mapping: BASE_URL + 'admin/portofolio/step/mapping',
        asesmen: BASE_URL + 'admin/portofolio/step/asesmen',
        soal: BASE_URL + 'admin/portofolio/step/soal',
        pelaksanaan: BASE_URL + 'admin/portofolio/step/pelaksanaan',
        hasilAsesmen: BASE_URL + 'admin/portofolio/step/hasil-asesmen',
        evaluasi: BASE_URL + 'admin/portofolio/step/evaluasi',
    };

    // Track DB IDs returned from server
    state.cpmkIdMap = {};
    state.subIdMap = {};
    state.asesmenIdMap = {};

    // ── Helper: POST JSON ─────────────────────────────────────────
    async function postJSON(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                ...payload,
                id_portofolio: PORTO_ID,
                [CSRF_TOKEN]: CSRF_HASH
            }),
        });
        const result = await res.json();
        // Update CSRF hash dari response header untuk request berikutnya
        const newHash = res.headers.get('x-csrf-hash');
        if (newHash) {
            window.CSRF_HASH = newHash;
        }
        return result;
    }

    // ── Helper: POST FormData (for file uploads) ──────────────────
    async function postForm(url, formData) {
        formData.append('id_portofolio', PORTO_ID);
        formData.append(CSRF_TOKEN, CSRF_HASH);
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
        });
        return res.json();
    }

    // ── Helper: show saving spinner on button ─────────────────────
    function setBtnLoading(btn, loading) {
        if (loading) {
            btn.disabled = true;
            btn.dataset.origText = btn.innerHTML;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.origText;
        }
    }


    // ══════════════════════════════════════════════════════════════
    //  STEP 1 — Upload RPS
    // ══════════════════════════════════════════════════════════════
    /**
     * Serve file RPS untuk ditampilkan di iframe (hanya pemilik)
     */

    async function saveStep1AndNext(btn) {
        // Validate PORTO_ID
        if (!PORTO_ID) {
            showModalAlert('ID Portofolio tidak valid. Silakan refresh halaman atau mulai ulang.');
            console.error('PORTO_ID is empty or invalid');
            return;
        }

        const HAS_RPS = <?= !empty($rps['file_rps']) ? 'true' : 'false' ?>;
        const fileInput = document.getElementById('rps_file');
        const hasNewFile = fileInput && fileInput.files.length > 0;

        // Jika sudah ada RPS dan tidak ada file baru → langsung next
        if (HAS_RPS && !hasNewFile) {
            nextStep();
            return;
        }

        // Jika belum ada RPS dan tidak ada file dipilih → wajib upload
        if (!HAS_RPS && !hasNewFile) {
            showModalAlert('Pilih file RPS terlebih dahulu.');
            return;
        }

        // Ada file baru → upload
        const file = fileInput.files[0];

        // Validate file type
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (!allowedTypes.includes(file.type)) {
            showModalAlert('Format file tidak diizinkan. Gunakan PDF/DOC/DOCX.');
            return;
        }

        // Validate file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            showModalAlert('Ukuran file maksimal 10 MB.');
            return;
        }

        setBtnLoading(btn, true);
        const fd = new FormData();
        fd.append('id_portofolio', PORTO_ID);
        fd.append('file_rps', file);

        try {
            const res = await postForm(API.rps, fd);
            setBtnLoading(btn, false);

            if (res.status === 'success') {
                showToast(res.message);
                // Reload page to show updated RPS preview
                setTimeout(() => {
                    nextStep();
                }, 500);
            } else {
                showToast(res.message || 'Gagal menyimpan RPS.', 'danger');
            }
        } catch (error) {
            setBtnLoading(btn, false);
            console.error('Upload error:', error);
            showModalAlert('Terjadi kesalahan saat mengupload file. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 2 — Info Mata Kuliah
    // ══════════════════════════════════════════════════════════════
    async function saveStep2AndNext(btn) {
        const topik = document.getElementById('topik_mk').value.trim();

        if (!topik) {
            showModalAlert('Topik perkuliahan wajib diisi.');
            return;
        }

        setBtnLoading(btn, true);

        const res = await postJSON(API.infoMK, {
            id_portofolio: PORTO_ID,
            mk_prasyarat: document.getElementById('mk_prasyarat').value,
            topik_perkuliahan: topik
        });

        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showModalAlert(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 3 — CPL & PI
    // ══════════════════════════════════════════════════════════════
    async function saveStep3AndNext(btn) {
        setBtnLoading(btn, true);
        const res = await postJSON(API.cpl, {});
        setBtnLoading(btn, false);
        if (res.status === 'success') nextStep();
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 4 — CPMK & Sub CPMK
    // ══════════════════════════════════════════════════════════════
    async function saveStep4AndNext(btn) {
        saveCPMK(); // sync DOM → state.cpmkList

        if (!state.cpmkList.length) {
            showModalAlert('Tambahkan minimal satu CPMK.');
            return;
        }

        // Validasi narasi wajib diisi
        for (const c of state.cpmkList) {
            if (!c.narasi || !c.narasi.trim()) {
                showModalAlert(`CPMK ${c.no} belum diisi narasinya.`);
                return;
            }
        }

        // Payload: cpmk_list sudah mengandung id_cpl dan subs[]
        // Controller akan otomatis insert mapping dari relasi CPL → CPMK → Sub
        const payload = {
            cpmk_list: state.cpmkList.map((c) => ({
                no: c.no,
                id_cpl: c.cpl, // id CPL dari <select>
                narasi: c.narasi.trim(),
                subs: (c.subs || []).map((s) => ({
                    no: s.no,
                    narasi: s.narasi || '',
                })),
            })),
        };

        setBtnLoading(btn, true);
        const res = await postJSON(API.cpmk, payload);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            // Simpan ID dari DB ke state supaya step 6+ bisa pakai
            state.cpmkIdMap = {};
            state.subIdMap = {};

            (res.cpmks || []).forEach((c) => {
                state.cpmkIdMap[c.no] = c.id;
                (c.subs || []).forEach((s) => {
                    state.subIdMap[`${c.no}_${s.no}`] = s.id;
                });
            });

            showToast(res.message);
            nextStep();
        } else {
            showModalAlert(res.message || 'Gagal menyimpan CPMK.', 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 5 — Pemetaan CPL-CPMK-SubCPMK
    // ══════════════════════════════════════════════════════════════
    async function saveStep5AndNext(btn) {
        saveMapping(); // Kumpulkan mapping dari checkbox

        if (!state.mappingData || state.mappingData.length === 0) {
            showModalAlert('Minimal satu pemetaan CPL-CPMK-Sub CPMK harus dipilih.');
            return;
        }

        setBtnLoading(btn, true);

        // Kirim data mapping ke server menggunakan postJSON
        const res = await postJSON(API.mapping, {
            id_portofolio: PORTO_ID,
            mappings: state.mappingData
        });

        setBtnLoading(btn, false);

        if (res.status === 'success') {
            // Update state.mapping agar data tetap ada saat refresh atau kembali ke step 5
            updateStateMappingFromSaved(state.mappingData);
            showToast(res.message);
            nextStep();
        } else {
            showModalAlert(res.message || 'Gagal menyimpan pemetaan.', 'danger');
        }
    }

    /**
     * Update state.mapping dari data yang baru disimpan
     * agar checkbox tetap tercentang saat refresh/kembali ke step 5
     */
    function updateStateMappingFromSaved(mappings) {
        const mappingMap = {};
        mappings.forEach(map => {
            const {
                id_cpl,
                id_cpmk,
                id_sub_cpmk
            } = map;

            // Cari no_cpmk dari state.cpmkIdMap
            const cpmkNo = Object.keys(state.cpmkIdMap).find(key => state.cpmkIdMap[key] == id_cpmk);
            if (!cpmkNo) return;

            // Cari no_sub dari state.subIdMap
            const subKey = Object.keys(state.subIdMap).find(key => state.subIdMap[key] == id_sub_cpmk);
            const subNo = subKey ? parseInt(subKey.split('_')[1]) : null;
            if (!subNo) return;

            const cplKey = String(id_cpl);
            if (!mappingMap[cplKey]) {
                mappingMap[cplKey] = {};
            }
            if (!mappingMap[cplKey][String(cpmkNo)]) {
                mappingMap[cplKey][String(cpmkNo)] = [];
            }
            mappingMap[cplKey][String(cpmkNo)].push(subNo);
        });
        state.mapping = mappingMap;
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 6 — Rancangan Asesmen
    // ══════════════════════════════════════════════════════════════
    async function saveStep6AndNext(btn) {
        updateAssessUpload();
        var asesmenData = [];
        document.querySelectorAll('.assess-cb:checked').forEach(function(cb) {
            asesmenData.push({
                id_cpmk: state.cpmkIdMap[parseInt(cb.dataset.cpmk)] || parseInt(cb.dataset.cpmk),
                jenis_asesmen: cb.dataset.type,
            });
        });
        if (!asesmenData.length) {
            showModalAlert('Pilih minimal satu jenis asesmen.');
            return;
        }

        var fd = new FormData();
        fd.append('asesmen_data', JSON.stringify(asesmenData));
        ['soal_tugas', 'rubrik_tugas', 'soal_uts', 'rubrik_uts', 'soal_uas', 'rubrik_uas'].forEach(function(f) {
            var el = document.getElementById(f);
            if (el && el.files[0]) fd.append('file_' + f, el.files[0]);
        });

        setBtnLoading(btn, true);
        var res = await postForm(API.asesmen, fd);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            state.asesmenIdMap = {};
            state.assessmentByCpmk = {};
            state.existingFiles = {};

            // Rebuild assessmentByCpmk and existingFiles from saved data
            (res.asesmen || []).forEach(function(a) {
                var cpmkNo = Object.keys(state.cpmkIdMap).find(function(k) {
                    return state.cpmkIdMap[k] == a.id_cpmk;
                });
                if (cpmkNo) {
                    state.asesmenIdMap[a.jenis_asesmen + '_' + cpmkNo] = a.id;
                    if (!state.assessmentByCpmk[cpmkNo]) {
                        state.assessmentByCpmk[cpmkNo] = [];
                    }
                    state.assessmentByCpmk[cpmkNo].push(a.jenis_asesmen);

                    // Store file names with capitalized type
                    var typeCapitalized = a.jenis_asesmen.charAt(0).toUpperCase() + a.jenis_asesmen.slice(1);
                    if (a.file_soal) {
                        state.existingFiles[typeCapitalized + 'Soal'] = a.file_soal;
                    }
                    if (a.file_rubrik) {
                        state.existingFiles[typeCapitalized + 'Rubrik'] = a.file_rubrik;
                    }
                }
            });
            showToast(res.message);
            nextStep();
        } else showToast(res.message, 'danger');
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 7 — Rancangan Soal
    // ══════════════════════════════════════════════════════════════
    async function saveStep7AndNext(btn) {
        saveSoalMapping();

        var soalList = [];
        var seen = new Set(); // untuk deduplikasi

        Object.entries(state.soalData).forEach(function([jenis, soals]) {
            soals.forEach(function(soal) {
                Object.entries(soal.cpmk_mappings || {}).forEach(function([cpmkNo, checked]) {
                    if (!checked) return;

                    // Dapatkan id_asesmen dari mapping
                    var id_asesmen = state.asesmenIdMap[jenis + '_' + cpmkNo];

                    // Dapatkan id_cpmk dari state.cpmkIdMap
                    var id_cpmk = state.cpmkIdMap[parseInt(cpmkNo)];

                    if (!id_asesmen || !id_cpmk) {
                        console.warn('Data tidak lengkap untuk:', jenis + '_' + cpmkNo);
                        return;
                    }

                    // Buat key unik untuk menghindari duplikasi
                    var key = id_asesmen + '_' + soal.soal_no;
                    if (seen.has(key)) return;
                    seen.add(key);

                    soalList.push({
                        id_asesmen: id_asesmen,
                        id_cpmk: id_cpmk,
                        nomor_soal: soal.soal_no
                    });
                });
            });
        });

        console.log('soalList yang akan dikirim:', soalList);

        if (!soalList.length) {
            showModalAlert('Centang minimal satu CPMK pada soal yang tersedia.');
            return;
        }

        setBtnLoading(btn, true);
        var res = await postJSON(API.soal, {
            soal_list: soalList
        });
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message || 'Gagal menyimpan soal.', 'danger');
        }
    }

    function saveSoalMapping() {
        document.querySelectorAll('.soal-cb').forEach(function(cb) {
            var type = cb.dataset.type;
            var soalIdx = parseInt(cb.dataset.soal); // index array (0-based)
            var cpmk = cb.dataset.cpmk;

            if (state.soalData[type] && state.soalData[type][soalIdx] !== undefined) {
                if (!state.soalData[type][soalIdx].cpmk_mappings) {
                    state.soalData[type][soalIdx].cpmk_mappings = {};
                }
                state.soalData[type][soalIdx].cpmk_mappings[cpmk] = cb.checked;
            }
        });
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 8 — Pelaksanaan Perkuliahan
    // ══════════════════════════════════════════════════════════════
    async function saveStep8AndNext(btn) {
        const fd = new FormData();
        const fileFields = [
            'kontrak_kuliah',
            'realisasi_mengajar',
            'kehadiran_mahasiswa',
        ];
        fileFields.forEach((f) => {
            const el = document.getElementById(f);
            if (el && el.files[0]) {
                // map to controller field names
                const fieldMap = {
                    kontrak_kuliah: 'file_kontrak_kuliah',
                    realisasi_mengajar: 'file_realisasi_mengajar',
                    kehadiran_mahasiswa: 'file_kehadiran',
                };
                fd.append(fieldMap[f], el.files[0]);
            }
        });

        setBtnLoading(btn, true);
        const res = await postForm(API.pelaksanaan, fd);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 9 — Hasil Asesmen
    // ══════════════════════════════════════════════════════════════
    async function saveStep9AndNext(btn) {
        const fd = new FormData();
        const types = ['tugas', 'uts', 'uas'];
        types.forEach((t) => {
            const el = document.getElementById(`jawaban_${t}`);
            if (el && el.files[0]) fd.append(`file_jawaban_${t}`, el.files[0]);
        });

        const elNilaiMK = document.getElementById('nilai_mk');
        const elNilaiCPMK = document.getElementById('nilai_cpmk');
        if (elNilaiMK && elNilaiMK.files[0])
            fd.append('file_nilai_matkul', elNilaiMK.files[0]);
        if (elNilaiCPMK && elNilaiCPMK.files[0])
            fd.append('file_nilai_cpmk', elNilaiCPMK.files[0]);

        setBtnLoading(btn, true);
        const res = await postForm(API.hasilAsesmen, fd);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 10 — Evaluasi (Final Submit)
    // ══════════════════════════════════════════════════════════════
    async function submitForm(btn) {
        updateChart(); // sync state.cpmkValues

        const evalList = state.cpmkList.map((c) => ({
            id_cpmk: state.cpmkIdMap[c.no] || c.no,
            rata_rata: state.cpmkValues[c.no] || 0,
            isi_cpmk: '', // extend if you add a textarea per CPMK
        }));

        if (!evalList.length) {
            showModalAlert('Data CPMK tidak ditemukan.');
            return;
        }

        setBtnLoading(btn, true);
        const res = await postJSON(API.evaluasi, {
            evaluasi_list: evalList
        });
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            document
                .querySelectorAll('.step-item')
                .forEach((el) => el.classList.add('completed'));
            showToast(res.message);
            setTimeout(() => {
                window.location.href = BASE_URL + 'admin/portofolio';
            }, 2500);
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  AUTO-RESUME — Go to last saved step on page load
    // ══════════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof LAST_STEP !== 'undefined' && LAST_STEP > 1) {
            goToStep(LAST_STEP);
        }
    });

    function showToast(message, type = 'success') {
        // Buat elemen toast
        const toastId = 'toast_' + Date.now();
        const toastHtml = `
    <div id="${toastId}" class="position-fixed start-50 translate-middle-x" style="top: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
        <div class="toast show align-items-center text-white bg-${type} border-0 shadow-lg mx-auto" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${toastId}').remove()"></button>
            </div>
        </div>
    </div>`;

        // Hapus toast sebelumnya jika ada (opsional)
        const existingToasts = document.querySelectorAll('[id^="toast_"]');
        existingToasts.forEach(toast => toast.remove());

        // Tambahkan toast baru ke body
        document.body.insertAdjacentHTML('beforeend', toastHtml);

        // Auto hilangkan setelah 5 detik
        setTimeout(() => {
            const toastEl = document.getElementById(toastId);
            if (toastEl) {
                // Animasi fade out
                toastEl.style.transition = 'opacity 0.3s ease';
                toastEl.style.opacity = '0';
                setTimeout(() => toastEl.remove(), 300);
            }
        }, 5000);
    }
</script>
<?= $this->endSection() ?>