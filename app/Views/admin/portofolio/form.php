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

<!-- ═══════════════ STEP 2: Info Matakuliah ═══════════════ -->
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

<!-- ═══════════════ STEP 5: Pemetaan ═══════════════ -->
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
                            <div class="upload-zone" onclick="document.getElementById('soal_tugas').click()">
                                <input type="file" id="soal_tugas" accept="application/pdf" onchange="handleFileUpload(this,'soalTugasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalTugasStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div class="upload-zone" onclick="document.getElementById('rubrik_tugas').click()">
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
                            <div class="upload-zone" onclick="document.getElementById('soal_uts').click()">
                                <input type="file" id="soal_uts" accept="application/pdf" onchange="handleFileUpload(this,'soalUtsStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalUtsStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div class="upload-zone" onclick="document.getElementById('rubrik_uts').click()">
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
                            <div class="upload-zone" onclick="document.getElementById('soal_uas').click()">
                                <input type="file" id="soal_uas" accept="application/pdf" onchange="handleFileUpload(this,'soalUasStatus')">
                                <i class="fas fa-file-pdf"></i>
                                <p>Upload Soal</p>
                            </div>
                            <div id="soalUasStatus" style="display:none;" class="file-status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rubrik Penilaian (PDF)</label>
                            <div class="upload-zone" onclick="document.getElementById('rubrik_uas').click()">
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
                Upload dokumentasi pelaksanaan perkuliahan (foto kegiatan, daftar hadir, dll).
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Foto Kegiatan (PDF)</label>
                    <div class="upload-zone" onclick="document.getElementById('foto_kegiatan').click()">
                        <input type="file" id="foto_kegiatan" accept="application/pdf" onchange="handleFileUpload(this,'fotoKegiatanStatus')">
                        <i class="fas fa-camera"></i>
                        <p>Upload Foto</p>
                    </div>
                    <div id="fotoKegiatanStatus" style="display:none;" class="file-status"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Daftar Hadir (PDF)</label>
                    <div class="upload-zone" onclick="document.getElementById('daftar_hadir').click()">
                        <input type="file" id="daftar_hadir" accept="application/pdf" onchange="handleFileUpload(this,'daftarHadirStatus')">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Upload Daftar Hadir</p>
                    </div>
                    <div id="daftarHadirStatus" style="display:none;" class="file-status"></div>
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

<!-- Toast Modal -->
<div class="modal fade" id="toastModal" tabindex="-1" aria-labelledby="toastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" id="toastModalHeader">
                <h5 class="modal-title" id="toastModalLabel">
                    <i class="fas fa-check-circle me-2"></i> Berhasil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="toastModalBody">
                Pesan akan ditampilkan di sini
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ── Demo Data ──
    const mkDatabase = [{
            nama_mk: 'Pemrograman Web',
            kode_mk: 'IF204',
            kelompok_mk: 'Wajib',
            fakultas: 'Fakultas Teknik',
            progdi: 'Informatika',
            sks_teori: 3,
            sks_praktik: 1,
            kurikulum: 'Kurikulum 2022',
            tahun: '2025',
            semester: 'Ganjil',
            smt_matkul: 3
        },
        {
            nama_mk: 'Basis Data',
            kode_mk: 'IF205',
            kelompok_mk: 'Wajib',
            fakultas: 'Fakultas Teknik',
            progdi: 'Informatika',
            sks_teori: 2,
            sks_praktik: 1,
            kurikulum: 'Kurikulum 2022',
            tahun: '2025',
            semester: 'Ganjil',
            smt_matkul: 3
        },
        {
            nama_mk: 'Algoritma & Pemrograman',
            kode_mk: 'IF101',
            kelompok_mk: 'Wajib',
            fakultas: 'Fakultas Teknik',
            progdi: 'Informatika',
            sks_teori: 3,
            sks_praktik: 1,
            kurikulum: 'Kurikulum 2022',
            tahun: '2025',
            semester: 'Genap',
            smt_matkul: 2
        },
        {
            nama_mk: 'Rekayasa Perangkat Lunak',
            kode_mk: 'IF301',
            kelompok_mk: 'Wajib',
            fakultas: 'Fakultas Teknik',
            progdi: 'Informatika',
            sks_teori: 3,
            sks_praktik: 0,
            kurikulum: 'Kurikulum 2022',
            tahun: '2025',
            semester: 'Ganjil',
            smt_matkul: 5
        },
        {
            nama_mk: 'Jaringan Komputer',
            kode_mk: 'IF302',
            kelompok_mk: 'Pilihan',
            fakultas: 'Fakultas Teknik',
            progdi: 'Informatika',
            sks_teori: 2,
            sks_praktik: 1,
            kurikulum: 'Kurikulum 2022',
            tahun: '2025',
            semester: 'Ganjil',
            smt_matkul: 5
        },
    ];

    const cplDatabase = {
        'IF204': [{
                no: '1',
                narasi: 'Mampu menerapkan pengetahuan matematika, ilmu pengetahuan, dan rekayasa',
                pi: ['PI 1.1: Mengidentifikasi konsep dasar pemrograman web', 'PI 1.2: Menerapkan logika algoritma dalam kode']
            },
            {
                no: '2',
                narasi: 'Mampu merancang dan mengimplementasikan sistem perangkat lunak berbasis web',
                pi: ['PI 2.1: Membuat antarmuka pengguna yang responsif', 'PI 2.2: Mengintegrasikan front-end dan back-end', 'PI 2.3: Menerapkan keamanan dasar aplikasi web']
            },
            {
                no: '3',
                narasi: 'Mampu bekerja secara mandiri maupun tim dalam proyek perangkat lunak',
                pi: ['PI 3.1: Berkolaborasi dalam pengembangan proyek', 'PI 3.2: Mendokumentasikan kode dengan baik']
            },
        ],
        'IF205': [{
                no: '1',
                narasi: 'Mampu merancang dan mengimplementasikan skema basis data relasional',
                pi: ['PI 1.1: Membuat ERD', 'PI 1.2: Normalisasi tabel hingga 3NF']
            },
            {
                no: '2',
                narasi: 'Mampu menulis query SQL yang efisien untuk manipulasi data',
                pi: ['PI 2.1: Menulis query SELECT kompleks', 'PI 2.2: Mengoptimasi performa query']
            },
        ],
        'DEFAULT': [{
                no: '1',
                narasi: 'Menguasai konsep dasar keilmuan',
                pi: ['PI 1.1: Memahami teori dasar', 'PI 1.2: Menerapkan konsep dalam praktik']
            },
            {
                no: '2',
                narasi: 'Mampu mengaplikasikan ilmu dalam pemecahan masalah',
                pi: ['PI 2.1: Analisis masalah', 'PI 2.2: Sintesis solusi']
            },
        ]
    };

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
        if (n === 2) renderMKDropdown();
        if (n === 3) renderCPLTable();
        if (n === 4) renderCPMKBuilder();
        if (n === 5) renderMappingTable();
        if (n === 6) renderAssessTable();
        if (n === 7) renderSoalSection();
        if (n === 9) renderHasilAsesmen();
        if (n === 10) renderEvaluation();
    }

    // ══════════════════════════════════════════
    //  STEP 2 — MK
    // ══════════════════════════════════════════
    function saveMK() {
        state.mk.mk_prasyarat = document.getElementById('mk_prasyarat').value;
        state.mk.topik_mk = document.getElementById('topik_mk').value;
    }

    // ══════════════════════════════════════════
    //  STEP 3 — CPL Table
    // ══════════════════════════════════════════
    function renderCPLTable() {
        const kode = state.mk.kode_mk || 'DEFAULT';
        const data = cplDatabase[kode] || cplDatabase['DEFAULT'];
        state.cpl = data;
        document.getElementById('cplMKName').textContent = state.mk.nama_mk ? `${state.mk.nama_mk} (${kode})` : '—';

        const tbody = document.getElementById('cplTableBody');
        tbody.innerHTML = '';
        data.forEach(cpl => {
            const rowspan = cpl.pi.length;
            cpl.pi.forEach((pi, i) => {
                const tr = document.createElement('tr');
                if (i === 0) {
                    tr.innerHTML = `
                    <td rowspan="${rowspan}" class="text-center align-middle"><span class="cpl-tag">CPL ${cpl.no}</span></td>
                    <td rowspan="${rowspan}" class="align-middle" style="font-size:13px;">${cpl.narasi}</td>
                    <td style="font-size:13px;">${pi}</td>`;
                } else {
                    tr.innerHTML = `<td style="font-size:13px;">${pi}</td>`;
                }
                tbody.appendChild(tr);
            });
        });
    }

    // ══════════════════════════════════════════
    //  STEP 4 — CPMK Builder
    // ══════════════════════════════════════════
    function renderCPMKBuilder() {
        if (document.getElementById('cpmkContainer').children.length === 0 && state.cpmkList.length === 0) {
            addCPMK();
        }
    }

    function addCPMK() {
        cpmkCounter++;
        const container = document.getElementById('cpmkContainer');
        const id = `cpmk_${cpmkCounter}`;
        const cplOptions = (state.cpl || []).map(c => `<option value="${c.no}">CPL ${c.no}</option>`).join('');

        const div = document.createElement('div');
        div.className = 'cpmk-block';
        div.id = id;
        div.innerHTML = `
                <div class="cpmk-block-header">
                    <span class="cpmk-num">CPMK ${cpmkCounter}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeCPMK('${id}')"><i class="fas fa-trash-alt"></i></button>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Terkait CPL</label>
                        <select class="form-select form-select-sm" id="${id}_cpl">${cplOptions}</select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Narasi CPMK</label>
                        <input type="text" class="form-control form-control-sm" id="${id}_narasi" placeholder="Deskripsikan capaian pembelajaran mata kuliah ini...">
                    </div>
                </div>
                <div class="sub-cpmk-list">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="fw-bold text-muted" style="font-size:12px;">Sub CPMK</small>
                        <button class="btn btn-sm btn-outline-primary" style="font-size:11px;" onclick="addSubCPMK('${id}')"><i class="fas fa-plus me-1"></i>Tambah Sub</button>
                    </div>
                    <div id="${id}_subs"></div>
                </div>`;
        container.appendChild(div);
        addSubCPMK(id);
    }

    function addSubCPMK(cpmkId) {
        const subsDiv = document.getElementById(`${cpmkId}_subs`);
        const subCount = subsDiv.children.length + 1;
        const subId = `${cpmkId}_sub_${subCount}`;
        const div = document.createElement('div');
        div.className = 'sub-cpmk-item';
        div.id = subId;
        div.innerHTML = `
        <span class="sub-num">Sub ${subCount}</span>
        <input type="text" class="form-control form-control-sm" placeholder="Narasi Sub CPMK ${subCount}...">
        <button class="btn btn-sm btn-outline-secondary" style="flex-shrink:0;" onclick="this.closest('.sub-cpmk-item').remove()"><i class="fas fa-times"></i></button>`;
        subsDiv.appendChild(div);
    }

    function removeCPMK(id) {
        document.getElementById(id)?.remove();
    }

    function saveCPMK() {
        state.cpmkList = [];
        document.querySelectorAll('.cpmk-block').forEach((block, i) => {
            const id = block.id;
            const no = i + 1;
            const cpl = document.getElementById(`${id}_cpl`)?.value;
            const narasi = document.getElementById(`${id}_narasi`)?.value;
            const subs = [];
            block.querySelectorAll('.sub-cpmk-item input[type=text]').forEach((inp, j) => {
                subs.push({
                    no: j + 1,
                    narasi: inp.value
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
    //  STEP 5 — Mapping Table
    // ══════════════════════════════════════════
    function renderMappingTable() {
        saveCPMK();
        if (!state.cpmkList.length) return;

        // Collect all sub CPMK numbers
        const allSubs = [];
        state.cpmkList.forEach(c => c.subs.forEach(s => {
            if (!allSubs.includes(s.no)) allSubs.push(s.no);
        }));
        allSubs.sort((a, b) => a - b);

        // Header
        const headerRow = document.getElementById('mappingHeaderRow');
        headerRow.innerHTML = `<th class="cpl-col" style="background:var(--accent);color:#fff;">CPL</th>
        <th class="cpmk-col" style="background:var(--accent);color:#fff;">CPMK</th>` +
            allSubs.map(s => `<th style="background:var(--accent);color:#fff;">Sub ${s}</th>`).join('');

        // Group by CPL
        const byCpl = {};
        state.cpmkList.forEach(c => {
            if (!byCpl[c.cpl]) byCpl[c.cpl] = [];
            byCpl[c.cpl].push(c);
        });

        const tbody = document.getElementById('mappingBody');
        tbody.innerHTML = '';
        Object.entries(byCpl).forEach(([cplNo, cpmks]) => {
            cpmks.forEach((cpmk, i) => {
                const tr = document.createElement('tr');
                let html = '';
                if (i === 0) {
                    html += `<td rowspan="${cpmks.length}" class="align-middle cpl-col"><span class="cpl-tag">CPL ${cplNo}</span></td>`;
                }
                html += `<td class="cpmk-col align-middle"><strong>CPMK ${cpmk.no}</strong><br><small style="color:var(--text-muted);font-size:11.5px;">${cpmk.narasi || ''}</small></td>`;
                allSubs.forEach(subNo => {
                    const hasSub = cpmk.subs.some(s => s.no === subNo);
                    html += `<td class="text-center align-middle">
                    ${hasSub ? `<input type="checkbox" class="mapping-checkbox" data-cpl="${cplNo}" data-cpmk="${cpmk.no}" data-sub="${subNo}">` : '<span style="color:#cbd5e1;">—</span>'}
                </td>`;
                });
                tr.innerHTML = html;
                tbody.appendChild(tr);
            });
        });
    }

    function saveMapping() {
        state.mapping = {};
        document.querySelectorAll('.mapping-checkbox:checked').forEach(cb => {
            const {
                cpl,
                cpmk,
                sub
            } = cb.dataset;
            if (!state.mapping[cpl]) state.mapping[cpl] = {};
            if (!state.mapping[cpl][cpmk]) state.mapping[cpl][cpmk] = [];
            state.mapping[cpl][cpmk].push(parseInt(sub));
        });
    }

    // ══════════════════════════════════════════
    //  STEP 6 — Assessment
    // ══════════════════════════════════════════
    function renderAssessTable() {
        const tbody = document.getElementById('assessBody');
        tbody.innerHTML = '';
        state.cpmkList.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td class="align-middle"><strong>CPMK ${c.no}</strong><br><small style="color:var(--text-muted);font-size:11.5px;">${c.narasi || ''}</small></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="tugas" data-cpmk="${c.no}" onchange="updateAssessUpload()"></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="uts" data-cpmk="${c.no}" onchange="updateAssessUpload()"></td>
            <td class="text-center"><input type="checkbox" class="assess-cb" data-type="uas" data-cpmk="${c.no}" onchange="updateAssessUpload()"></td>`;
            tbody.appendChild(tr);
        });
    }

    function updateAssessUpload() {
        const tugas = document.querySelector('.assess-cb[data-type="tugas"]:checked');
        const uts = document.querySelector('.assess-cb[data-type="uts"]:checked');
        const uas = document.querySelector('.assess-cb[data-type="uas"]:checked');
        document.getElementById('tugasUploadArea').style.display = tugas ? 'block' : 'none';
        document.getElementById('utsUploadArea').style.display = uts ? 'block' : 'none';
        document.getElementById('uasUploadArea').style.display = uas ? 'block' : 'none';
        state.assessment.tugas = !!tugas;
        state.assessment.uts = !!uts;
        state.assessment.uas = !!uas;
    }

    function saveAssessment() {
        updateAssessUpload();
        if (!state.soalData) state.soalData = {};
    }

    // ══════════════════════════════════════════
    //  STEP 7 — Rancangan Soal (Matrix Table)
    // ══════════════════════════════════════════
    function renderSoalSection() {
        const container = document.getElementById('soalContainer');
        container.innerHTML = '';

        const types = [];
        if (state.assessment.tugas) types.push({
            key: 'tugas',
            label: '1. Tugas',
            color: '#f59e0b',
            num: 1
        });
        if (state.assessment.uts) types.push({
            key: 'uts',
            label: `${types.length+1}. Ujian Tengah Semester`,
            color: '#0ea5e9',
            num: types.length + 1
        });
        if (state.assessment.uas) types.push({
            key: 'uas',
            label: `${types.length+1}. Ujian Akhir Semester`,
            color: '#8b5cf6',
            num: types.length + 1
        });

        // Recompute labels with correct numbering
        let idx = 1;
        const orderedTypes = [];
        if (state.assessment.tugas) {
            orderedTypes.push({
                key: 'tugas',
                label: `${idx++}. Tugas`,
                color: '#f59e0b'
            });
        }
        if (state.assessment.uts) {
            orderedTypes.push({
                key: 'uts',
                label: `${idx++}. Ujian Tengah Semester (UTS)`,
                color: '#0ea5e9'
            });
        }
        if (state.assessment.uas) {
            orderedTypes.push({
                key: 'uas',
                label: `${idx++}. Ujian Akhir Semester (UAS)`,
                color: '#8b5cf6'
            });
        }

        if (!orderedTypes.length) {
            container.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-circle me-2"></i>Belum ada jenis asesmen yang dipilih. Kembali ke Tahap 6 untuk memilih.</div>`;
            return;
        }

        // Get unique CPMK numbers from the mapping state
        const cpmkNos = state.cpmkList.map(c => c.no);

        orderedTypes.forEach(type => {
            // Initialise soal list for this type if needed
            if (!state.soalData[type.key] || !state.soalData[type.key].length) {
                state.soalData[type.key] = [{
                    soal_no: 1,
                    cpmk_mappings: {}
                }];
            }

            const soalList = state.soalData[type.key];

            // Build CPMK header cells
            const cpmkHeaders = cpmkNos.map(no =>
                `<th class="text-center align-middle" style="min-width:80px;">CPMK ${no}</th>`
            ).join('');

            // Build rows
            const rows = soalList.map((soal, i) => {
                const cells = cpmkNos.map(cno => {
                    const checked = soal.cpmk_mappings && soal.cpmk_mappings[cno] ? 'checked' : '';
                    return `<td class="text-center align-middle">
                    <input type="checkbox" class="soal-cb form-check-input"
                        style="width:18px;height:18px;cursor:pointer;accent-color:var(--primary);"
                        data-type="${type.key}" data-soal="${i}" data-cpmk="${cno}" ${checked}>
                </td>`;
                }).join('');

                return `<tr>
                <td class="text-center align-middle fw-bold" style="white-space:nowrap;font-size:13px;">
                    Soal no ${soal.soal_no}
                </td>
                ${cells}
                <td class="text-center align-middle">
                    ${soalList.length > 1
                        ? `<button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="removeSoal('${type.key}',${i})" title="Hapus Soal">
                              <i class="fas fa-trash-alt" style="font-size:11px;"></i>
                           </button>`
                        : `<span style="color:var(--text-muted);font-size:11px;">—</span>`
                    }
                </td>
            </tr>`;
            }).join('');

            const section = document.createElement('div');
            section.className = 'mb-5';
            section.id = `soalSection_${type.key}`;
            section.innerHTML = `
            <div class="section-header">
                <div class="section-dot" style="background:${type.color};"></div>
                <div class="section-title">${type.label}</div>
            </div>
            <div class="alert alert-primary d-flex align-items-center gap-2 py-2 mb-3" style="font-size:13px;">
                <i class="fas fa-info-circle"></i>
                Silahkan untuk menentukan pemetaan soal terhadap CPMK sebelum melanjutkan!
            </div>
            <div class="table-responsive mb-3">
                <table class="table table-bordered mb-0" id="soalTable_${type.key}">
                    <thead style="background-color:#0f4c92;" class="text-white">
                        <tr class="align-middle text-center">
                            <th class="align-middle" style="min-width:110px;">Soal No</th>
                            ${cpmkHeaders}
                            <th class="align-middle" style="min-width:70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="soalBody_${type.key}">
                        ${rows}
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addSoal('${type.key}')">
                <i class="fas fa-plus me-1"></i> Tambah Soal
            </button>`;
            container.appendChild(section);
        });
    }

    function addSoal(type) {
        if (!state.soalData[type]) state.soalData[type] = [];
        const nextNo = state.soalData[type].length + 1;
        state.soalData[type].push({
            soal_no: nextNo,
            cpmk_mappings: {}
        });
        renderSoalSection();
    }

    function removeSoal(type, idx) {
        state.soalData[type].splice(idx, 1);
        // Renumber
        state.soalData[type].forEach((s, i) => {
            s.soal_no = i + 1;
        });
        renderSoalSection();
    }

    function saveSoalMapping() {
        // Sync checkbox state into soalData before moving on
        document.querySelectorAll('.soal-cb').forEach(cb => {
            const {
                type,
                soal,
                cpmk
            } = cb.dataset;
            if (state.soalData[type] && state.soalData[type][parseInt(soal)]) {
                state.soalData[type][parseInt(soal)].cpmk_mappings[cpmk] = cb.checked;
            }
        });
    }

    // ══════════════════════════════════════════
    //  STEP 9 — Hasil Asesmen
    // ══════════════════════════════════════════
    function renderHasilAsesmen() {
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
    //  STEP 10 — Evaluation
    // ══════════════════════════════════════════
    function renderEvaluation() {
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
        if (file && statusEl) {
            statusEl.style.display = 'flex';
            statusEl.innerHTML = `<i class="fas fa-check-circle"></i><span>${file.name}</span><span class="ms-auto" style="color:var(--text-muted);">${(file.size/1024).toFixed(1)} KB</span>`;
            if (zone) {
                zone.classList.add('has-file');
                zone.querySelector('i').className = 'fas fa-check-circle';
            }
        }
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
    // Inject PHP constants - handle case where $porto may not exist
    const BASE_URL = '<?= base_url() ?>';
    const PORTO_ID = <?= json_encode($porto['id'] ?? '') ?>;
    const LAST_STEP = <?= (int)($last_step ?? 1) ?>;
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';
    const PERKULIAHAN_ID = <?= (int)($porto['id_perkuliahan'] ?? 0) ?>;

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
    state.cpmkIdMap = {}; // { no_cpmk: db_id }
    state.subIdMap = {}; // { cpmk_no + '_' + sub_no: db_id }
    state.asesmenIdMap = {}; // { 'tugas_1': db_id, ... }

    // ── Helper: POST JSON ─────────────────────────────────────────
    async function postJSON(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                [CSRF_TOKEN]: CSRF_HASH,
            },
            body: JSON.stringify({
                ...payload,
                id_portofolio: PORTO_ID
            }),
        });
        return res.json();
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
    //  STEP 3 — CPL & PI (advance only)
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
        saveCPMK(); // sync state.cpmkList

        if (!state.cpmkList.length) {
            showModalAlert('Tambahkan minimal satu CPMK.');
            return;
        }

        const payload = {
            cpmk_list: state.cpmkList.map((c) => ({
                no: c.no,
                id_cpl: c.cpl, // no_cpl string used as id_cpl — in production use actual id
                narasi: c.narasi,
                subs: c.subs.map((s) => ({
                    no: s.no,
                    narasi: s.narasi
                })),
            })),
        };

        setBtnLoading(btn, true);
        const res = await postJSON(API.cpmk, payload);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            // Store returned DB IDs for later steps
            res.cpmks.forEach((c) => {
                state.cpmkIdMap[c.no] = c.id;
                c.subs.forEach((s) => {
                    state.subIdMap[`${c.no}_${s.no}`] = s.id;
                });
            });
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 5 — Pemetaan CPL-CPMK-SubCPMK
    // ══════════════════════════════════════════════════════════════
    async function saveStep5AndNext(btn) {
        saveMapping(); // sync state.mapping

        // Build flat list from state.mapping { cpl: { cpmk: [sub, ...] } }
        const mappings = [];
        Object.entries(state.mapping).forEach(([cplNo, cpmkMap]) => {
            Object.entries(cpmkMap).forEach(([cpmkNo, subs]) => {
                subs.forEach((subNo) => {
                    mappings.push({
                        id_cpl: cplNo,
                        id_cpmk: state.cpmkIdMap[cpmkNo] || cpmkNo,
                        id_sub_cpmk: state.subIdMap[`${cpmkNo}_${subNo}`] || subNo,
                    });
                });
            });
        });

        setBtnLoading(btn, true);
        const res = await postJSON(API.mapping, {
            mappings
        });
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 6 — Rancangan Asesmen
    // ══════════════════════════════════════════════════════════════
    async function saveStep6AndNext(btn) {
        saveAssessment(); // sync state.assessment

        // Build asesmen_data array from checked checkboxes
        const asesmenData = [];
        document.querySelectorAll('.assess-cb:checked').forEach((cb) => {
            const cpmkNo = parseInt(cb.dataset.cpmk);
            const jenis = cb.dataset.type;
            asesmenData.push({
                id_cpmk: state.cpmkIdMap[cpmkNo] || cpmkNo,
                jenis_asesmen: jenis,
            });
        });

        if (!asesmenData.length) {
            showModalAlert('Pilih minimal satu jenis asesmen.');
            return;
        }

        const fd = new FormData();
        fd.append('asesmen_data', JSON.stringify(asesmenData));

        // Attach files
        const fileFields = [
            'soal_tugas',
            'rubrik_tugas',
            'soal_uts',
            'rubrik_uts',
            'soal_uas',
            'rubrik_uas',
        ];
        fileFields.forEach((f) => {
            const el = document.getElementById(f);
            if (el && el.files[0]) fd.append('file_' + f, el.files[0]);
        });

        setBtnLoading(btn, true);
        const res = await postForm(API.asesmen, fd);
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  STEP 7 — Rancangan Soal
    // ══════════════════════════════════════════════════════════════
    async function saveStep7AndNext(btn) {
        saveSoalMapping(); // sync state.soalData from checkboxes

        // Get asesmen IDs from DB (needed to link soal -> rancangan_asesmen)
        // In production, these come back from step 6 response or re-fetched.
        // Here we rely on state.asesmenIdMap populated after step 6.
        const soalList = [];

        Object.entries(state.soalData).forEach(([jenis, soals]) => {
            soals.forEach((soal) => {
                // For each CPMK this soal maps to, link via rancangan_asesmen
                Object.entries(soal.cpmk_mappings).forEach(([cpmkNo, checked]) => {
                    if (!checked) return;
                    const asesmenKey = `${jenis}_${cpmkNo}`;
                    const id_asesmen = state.asesmenIdMap[asesmenKey];
                    if (id_asesmen) {
                        soalList.push({
                            id_asesmen,
                            nomor_soal: soal.soal_no,
                        });
                    }
                });
            });
        });

        setBtnLoading(btn, true);
        const res = await postJSON(API.soal, {
            soal_list: soalList
        });
        setBtnLoading(btn, false);

        if (res.status === 'success') {
            showToast(res.message);
            nextStep();
        } else {
            showToast(res.message, 'danger');
        }
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
        const modalEl = document.getElementById('toastModal');
        const modalHeader = document.getElementById('toastModalHeader');
        const modalTitle = document.getElementById('toastModalLabel');
        const modalBody = document.getElementById('toastModalBody');

        // Reset classes
        modalHeader.className = 'modal-header';

        // Set style berdasarkan type
        if (type === 'danger') {
            modalHeader.classList.add('bg-danger', 'text-white');
            modalTitle.innerHTML = '<i class="fas fa-times-circle me-2"></i> Gagal';
        } else if (type === 'warning') {
            modalHeader.classList.add('bg-warning', 'text-dark');
            modalTitle.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Peringatan';
        } else if (type === 'info') {
            modalHeader.classList.add('bg-info', 'text-white');
            modalTitle.innerHTML = '<i class="fas fa-info-circle me-2"></i> Informasi';
        } else {
            modalHeader.classList.add('bg-success', 'text-white');
            modalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i> Berhasil';
        }

        // Set message
        modalBody.innerHTML = message;

        // Show modal
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Auto close after 3 seconds
        setTimeout(() => {
            modal.hide();
        }, 3000);
    }
</script>
<?= $this->endSection() ?>