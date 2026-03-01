<?= $this->extend('template') ?>
<?= $this->section('title') ?>Portofolio Mata Kuliah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container {
        width: 100% !important;
    }

    /* Status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .status-draft {
        background: #fef9c3;
        color: #a16207;
    }

    .status-proses {
        background: #fef3c7;
        color: var(--warning);
    }

    .status-selesai {
        background: #dcfce7;
        color: #15803d;
    }

    .status-belum {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Progress bar mini */
    .mini-progress {
        height: 6px;
        border-radius: 3px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .mini-progress-bar {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, var(--primary), #60a5fa);
        transition: width .4s ease;
    }

    /* MK info cell */
    .mk-cell .mk-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px;
    }

    .mk-cell .mk-meta {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    .mk-code {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 10.5px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 4px;
        margin-right: 4px;
    }

    /* Step dots */
    .step-dots {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .step-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 9.5px;
        font-weight: 700;
        display: grid;
        place-items: center;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        background: #fff;
        transition: all .2s;
        cursor: default;
    }

    .step-dot.done {
        background: var(--success);
        border-color: var(--success);
        color: #fff;
    }

    .step-dot.current {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
    }

    .step-dot.skip {
        background: var(--bg);
    }

    /* Table tweaks */
    #tblPortofolio thead th {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        border-bottom: 2px solid var(--border);
        padding: 10px 12px;
    }

    #tblPortofolio tbody td {
        padding: 12px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    #tblPortofolio tbody tr:hover td {
        background: #f8fafc;
    }

    /* Modal detail tabs */
    .detail-tab {
        padding: 6px 14px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        background: #fff;
        color: var(--text-sub);
        transition: all .2s;
    }

    .detail-tab.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    /* Timeline for detail */
    .timeline {
        position: relative;
        padding-left: 26px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 4px;
        bottom: 4px;
        width: 2px;
        background: var(--border);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 16px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--border);
        border: 2px solid #fff;
    }

    .timeline-item.done::before {
        background: var(--success);
    }

    .timeline-item.current::before {
        background: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
    }

    .timeline-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text);
    }

    .timeline-sub {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* Stat cards */
    .stat-mini {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
    }

    .stat-mini-val {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary);
    }

    .stat-mini-label {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    /* Animate fade in */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeInUp .3s ease forwards;
    }

    .mk-compact {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mk-code-pill {
        background: var(--primary);
        color: white;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
    }

    .class-badge {
        background: #f1f5f9;
        color: #334155;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }

    .hover-card {
        position: relative;
    }

    .hover-card:hover::after {
        content: attr(data-info);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .hover-card:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
        margin-bottom: -4px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <div class="page-title">Portofolio Mata Kuliah</div>
        <div class="page-subtitle">Manajemen data portofolio perkuliahan dosen</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-secondary btn-sm px-3" onclick="openFilterModal()">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <a href="<?= base_url('admin/portofolio/add') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Buat Portofolio
        </a>
    </div>
</div>

<!-- Stat Mini Cards -->
<div class="row g-3 mb-4" id="statCards">
    <div class="col-6 col-lg-3 fade-in" style="animation-delay:.05s">
        <div class="stat-mini">
            <div class="stat-mini-val" id="statTotal">—</div>
            <div class="stat-mini-label">Total Portofolio</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in" style="animation-delay:.1s">
        <div class="stat-mini">
            <div class="stat-mini-val" style="color:var(--success);" id="statSelesai">—</div>
            <div class="stat-mini-label">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in" style="animation-delay:.15s">
        <div class="stat-mini">
            <div class="stat-mini-val" style="color:var(--warning);" id="statProses">—</div>
            <div class="stat-mini-label">Proses</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in" style="animation-delay:.15s">
        <div class="stat-mini">
            <div class="stat-mini-val" style="color:var(--secondary);" id="statBelum">—</div>
            <div class="stat-mini-label">Belum</div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card-box fade-in" style="animation-delay:.25s">
    <table id="tblPortofolio" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Tahun / Semester</th>
                <th>Progres Tahap</th>
                <th>Status</th>
                <th style="width:120px">Aksi</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Populated by JS -->
        </tbody>
    </table>
</div>

<!-- ══════════════════════ MODAL DETAIL ══════════════════════ -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header pb-2" style="background:linear-gradient(135deg,var(--accent),#1d6fb8);color:#fff;border-radius:8px 8px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="detailMKName">—</h5>
                    <div style="font-size:12px;opacity:.8;" id="detailMKMeta">—</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tab navigation -->
                <div class="d-flex gap-2 p-3 border-bottom flex-wrap" id="detailTabs">
                    <button class="detail-tab active" onclick="switchDetailTab('tahap',this)">Progres Tahap</button>
                    <button class="detail-tab" onclick="switchDetailTab('info',this)">Info MK</button>
                </div>

                <!-- Tab: Tahap -->
                <div id="tab-tahap" class="p-3">
                    <div class="row g-3 mb-3" id="detailStats"></div>
                    <div class="timeline" id="detailTimeline"></div>
                </div>

                <!-- Tab: Info MK -->
                <div id="tab-info" class="p-3 d-none">
                    <div class="row g-3" id="detailInfoGrid"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-primary btn-sm px-4" id="btnLanjut" onclick="lanjutkanPortofolio()"><i class="bi bi-pencil me-1"></i>Lanjutkan</button>
                <form id="formStart" method="post">
                    <button class="btn btn-success btn-sm px-4" id="btnDetailBikin" onclick="mulaiPortofolio()"><i class="bi bi-play-circle me-1"></i>Mulai</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════ MODAL FILTER ══════════════════════ -->
<div class="modal fade" id="modalFilter" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Filter Portofolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Program Studi</label>
                        <select class="form-select form-select-sm select2-filter" id="filterProdi">
                            <option value="">Semua Prodi</option>
                            <option>Informatika</option>
                            <option>Teknik Elektro</option>
                            <option>Sistem Informasi</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Tahun Ajaran</label>
                        <select class="form-select form-select-sm" id="filterTahun">
                            <option value="">Semua Tahun</option>
                            <option>2025/2026</option>
                            <option>2024/2025</option>
                            <option>2023/2024</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Semester</label>
                        <select class="form-select form-select-sm" id="filterSemester">
                            <option value="">Semua Semester</option>
                            <option>Ganjil</option>
                            <option>Genap</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:12.5px;">Status</label>
                        <select class="form-select form-select-sm" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option>Proses</option>
                            <option>Selesai</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" onclick="resetFilter()" data-bs-dismiss="modal">Reset</button>
                <button class="btn btn-primary btn-sm px-4" onclick="applyFilter()" data-bs-dismiss="modal">Terapkan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // ══════════════════════════════════
    //  DEMO DATA
    // ══════════════════════════════════
    const STEPS = ['Upload RPS', 'Info MK', 'CPL & PI', 'CPMK', 'Pemetaan', 'Asesmen', 'Rancangan Soal', 'Pelaksanaan', 'Hasil Asesmen', 'Evaluasi'];

    // Data dari controller (dinamis)
    const portofolioData = <?= json_encode(array_map(function ($d) {
                                return [
                                    'id' => !empty($d['id_portofolio'])
                                        ? (string) $d['id_portofolio']
                                        : null,
                                    'id_perkuliahan' => (int)$d['id_perkuliahan'],
                                    'nama_mk'       => $d['nama_mk'],
                                    'kode_mk'       => $d['kode_mk'],
                                    'dosen'         => $d['nama_lengkap'],
                                    'tahun'         => $d['tahun_akademik'],
                                    'semester'      => $d['semester'],
                                    'kurikulum'     => $d['nama_kurikulum'],
                                    'step_done'     => (int)($d['last_step'] ?? 0),
                                    'status'        => match (true) {
                                        (int)($d['last_step'] ?? 0) >= 10 => 'selesai',
                                        (int)($d['last_step'] ?? 0) > 1   => 'proses',
                                        (int)($d['last_step'] ?? 0) <= 1  => 'belum',
                                        default                           => 'belum',
                                    },
                                    // field opsional (isi default jika belum ada di query)
                                    'prodi'         => $d['prodi']        ?? '-',
                                    'kode_kelas'    => $d['kode_kelas']          ?? '-',
                                    'kelompok_mk'   => $d['kelompok_mk']  ?? '-',
                                    'kurikulum'     => $d['nama_kurikulum'],
                                    'mk_prasyarat'  => $d['mk_prasyarat'] ?? '-',
                                    'topik_mk'      => $d['topik_mk']     ?? '-',
                                    'sks'           => $d['sks']          ?? '-',
                                ];
                            }, $portofolios)) ?>;

    let currentDetailId = null;
    let currentIdPerkuliahan = null;
    const modalDetail = new bootstrap.Modal('#modalDetail');
    const modalFilter = new bootstrap.Modal('#modalFilter');
    let dt;

    // ══════════════════════════════════
    //  RENDER TABLE
    // ══════════════════════════════════
    function getStatusBadge(s) {
        const map = {
            belum: ['status-belum', 'Belum'],
            proses: ['status-proses', 'Proses'],
            selesai: ['status-selesai', 'Selesai'],
        };
        const [cls, label] = map[s] || ['Unknown'];
        return `<span class="status-badge ${cls}">${label}</span>`;
    }

    function getStepDots(done) {
        return STEPS.map((_, i) => {
            let cls = '';
            if (i < done) cls = 'done';
            else if (i === done && done < 10) cls = 'current';
            return `<div class="step-dot ${cls}" title="${STEPS[i]}">${i + 1}</div>`;
        }).join('');
    }

    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        data.forEach((d, idx) => {
            const pct = Math.round((d.step_done / 10) * 100);
            const tr = document.createElement('tr');
            tr.className = 'fade-in';
            tr.style.animationDelay = `${idx * 0.04}s`;
            tr.innerHTML = `
            <td style="font-size:12.5px;color:var(--text-muted);font-weight:600;">${idx + 1}</td>
                    <td>
            <div class="mk-cell">
                <div class="mk-name">${d.nama_mk}</div>
                <div class="mk-compact mt-1">
                    <span class="mk-code-pill hover-card" data-info="Kode MK: ${d.kode_mk}">
                        ${d.kode_mk}
                    </span>
                    <span class="class-badge hover-card" data-info="Kelas: ${d.kode_kelas}">
                        <i class="bi bi-people me-1"></i>${d.kode_kelas}
                    </span>
                </div>
            </div>
        </td>
            <td>
                <div style="font-size:13px;font-weight:600;">${d.dosen.split(',')[0]}</div>
                <div style="font-size:11.5px;color:var(--text-muted);">${d.dosen.split(',').slice(1).join(',').trim() || ''}</div>
            </td>
            <td>
                <div style="font-size:13px;font-weight:600;">${d.tahun}</div>
                <div style="font-size:11.5px;color:var(--text-muted);">${d.semester} &middot; ${d.kurikulum}</div>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="step-dots">${getStepDots(d.step_done)}</div>
                </div>
                <div class="mini-progress mt-1" style="width:140px;">
                    <div class="mini-progress-bar" style="width:${pct}%;background:${d.step_done===10?'var(--success)':'linear-gradient(90deg,var(--primary),#60a5fa)'};"></div>
                </div>
                <div style="font-size:10.5px;color:var(--text-muted);margin-top:2px;">${d.step_done} / 10 tahap &middot; ${pct}%</div>
            </td>
            <td>${getStatusBadge(d.status)}</td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary" title="Detail" onclick="openDetail(${d.id ? `'${d.id}'` : 'null'}, ${d.id_perkuliahan})"><i class="bi bi-eye"></i></button>
                    ${d.id ? `<a href="<?= base_url('admin/portofolio/form/') ?>${d.id}" class="btn btn-sm btn-outline-success" title="Lanjutkan"><i class="bi bi-pencil"></i></a>` : ''}
                    ${d.id ? `<a href="<?= base_url('admin/cetak/') ?>${d.id}" class="btn btn-sm btn-outline-info" title="Cetak"><i class="bi bi-printer"></i></a>` : ''}
                </div>
            </td>`;
            tbody.appendChild(tr);
        });

        // Update stats
        document.getElementById('statTotal').textContent = data.length;
        document.getElementById('statSelesai').textContent = data.filter(d => d.status === 'selesai').length;
        document.getElementById('statProses').textContent = data.filter(d => d.status === 'proses').length;
        document.getElementById('statBelum').textContent = data.filter(d => d.status === 'belum').length;
    }

    // ══════════════════════════════════
    //  DETAIL MODAL
    // ══════════════════════════════════
    function openDetail(id, id_perkuliahan) {
        console.log("Detail diklik", id, id_perkuliahan);
        console.log(portofolioData);
        console.log("id portofolio:", typeof id, id);
        console.log(typeof portofolioData[0].id_perkuliahan);
        console.log(typeof id_perkuliahan);
        let d;

        if (!id) {
            d = portofolioData.find(x => x.id_perkuliahan === parseInt(id_perkuliahan));
        } else {
            d = portofolioData.find(x => x.id === id);
        }
        if (!d) return;

        // Call showDetail to handle button visibility
        // Pass id_perkuliahan and id_portofolio (d.id)
        // Note: d.id is the portofolio ID (could be null/undefined for rows without portfolio)
        const idPortofolio = d.id || null;
        showDetail(d.id_perkuliahan, idPortofolio);

        document.getElementById('detailMKName').textContent = `${d.nama_mk}`;
        document.getElementById('detailMKMeta').textContent = `${d.kode_mk} — ${d.prodi} — ${d.tahun} ${d.semester}`;

        // Stats row
        const statsDiv = document.getElementById('detailStats');
        const pct = Math.round((d.step_done / 10) * 100);
        statsDiv.innerHTML = `
        <div class="col-4"><div class="stat-mini text-center">
            <div class="stat-mini-val">${d.step_done}/10</div>
            <div class="stat-mini-label">Tahap Selesai</div>
        </div></div>
        <div class="col-4"><div class="stat-mini text-center">
            <div class="stat-mini-val" style="color:var(--success);">${pct}%</div>
            <div class="stat-mini-label">Progress</div>
        </div></div>
        <div class="col-4"><div class="stat-mini text-center">
            <div class="stat-mini-val" style="font-size:14px;">${getStatusBadge(d.status)}</div>
            <div class="stat-mini-label">Status</div>
        </div></div>`;

        // Timeline
        const tlDiv = document.getElementById('detailTimeline');
        tlDiv.innerHTML = STEPS.map((s, i) => {
            const cls = i < d.step_done ? 'done' : (i === d.step_done && d.step_done < 10 ? 'current' : '');
            const icon = i < d.step_done ? '✓' : (i === d.step_done ? '●' : '○');
            return `<div class="timeline-item ${cls}">
            <div class="timeline-label">${icon} Tahap ${i + 1}: ${s}</div>
            <div class="timeline-sub">${i < d.step_done ? 'Selesai' : (i === d.step_done ? 'Sedang Dikerjakan' : 'Belum Dimulai')}</div>
        </div>`;
        }).join('');

        // Info tab
        document.getElementById('detailInfoGrid').innerHTML = `
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Nama Mata Kuliah</div><div style="font-size:13.5px;font-weight:700;">${d.nama_mk}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Kode MK</div><div style="font-size:13.5px;font-weight:700;">${d.kode_mk}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Dosen Pengampu</div><div style="font-size:13px;">${d.dosen}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Program Studi</div><div style="font-size:13px;">${d.prodi}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">SKS</div><div style="font-size:13px;">${d.sks}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Kurikulum</div><div style="font-size:13px;">${d.kurikulum}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">Tahun / Semester</div><div style="font-size:13px;">${d.tahun} — ${d.semester}</div></div>
        <div class="col-md-6"><div style="font-size:11.5px;color:var(--text-muted);">MK Prasyarat</div><div style="font-size:13px;">${d.mk_prasyarat || '-'}</div></div>
        <div class="col-12"><div style="font-size:11.5px;color:var(--text-muted);">Topik Perkuliahan</div><div style="font-size:13px;">${d.topik_mk || '-'}</div></div>
    `;

        // Switch back to first tab
        switchDetailTab('tahap', document.querySelector('.detail-tab'));

        modalDetail.show();
    }

    function switchDetailTab(tab, el) {
        document.querySelectorAll('.detail-tab').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        ['tahap', 'info'].forEach(t => {
            document.getElementById(`tab-${t}`).classList.toggle('d-none', t !== tab);
        });
    }

    function mulaiPortofolio() {
        modalDetail.hide();
        const form = document.getElementById('formStart');
        form.action = '<?= base_url('admin/portofolio/add/') ?>' + currentIdPerkuliahan;
        form.submit();
    }

    function lanjutkanPortofolio() {
        modalDetail.hide();
        window.location.href = '<?= base_url('admin/portofolio/form/') ?>' + currentDetailId;
    }

    // show Detail Button hanya jika belum ada portofolio
    function showDetail(id_perkuliahan, id) {
        currentIdPerkuliahan = id_perkuliahan;

        if (id) {
            // Sudah ada portofolio - tampilkan tombol Lanjutkan, sembunyikan Mulai
            currentDetailId = id; // id_portofolio
            document.getElementById('btnDetailBikin').classList.add('d-none');
            document.getElementById('btnLanjut').classList.remove('d-none');
        } else {
            // Belum ada portofolio - tampilkan tombol Mulai, sembunyikan Lanjutkan
            currentDetailId = id_perkuliahan;
            document.getElementById('btnDetailBikin').classList.remove('d-none');
            document.getElementById('btnLanjut').classList.add('d-none');
        }

        modalDetail.show();
    }

    function setStatus(s) {
        showToast(`Status diubah menjadi: ${s}`, 'warning');
        modalDetail.hide();
    }

    // ══════════════════════════════════
    //  FILTER
    // ══════════════════════════════════
    function openFilterModal() {
        modalFilter.show();
    }

    function applyFilter() {
        const prodi = document.getElementById('filterProdi').value.toLowerCase();
        const tahun = document.getElementById('filterTahun').value;
        const semester = document.getElementById('filterSemester').value;
        const status = document.getElementById('filterStatus').value.toLowerCase();
        const filtered = portofolioData.filter(d => {
            return (!prodi || d.prodi.toLowerCase().includes(prodi)) &&
                (!tahun || d.tahun === tahun) &&
                (!semester || d.semester === semester) &&
                (!status || d.status === status);
        });
        renderTable(filtered);
    }

    function resetFilter() {
        ['filterProdi', 'filterTahun', 'filterSemester', 'filterStatus'].forEach(id => document.getElementById(id).value = '');
        renderTable(portofolioData);
    }

    // ══════════════════════════════════
    //  TOAST & SIDEBAR
    // ══════════════════════════════════
    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.className = `toast align-items-center text-bg-${type} border-0 show position-fixed bottom-0 end-0 m-3`;
        t.style.zIndex = 9999;
        t.innerHTML = `<div class="d-flex"><div class="toast-body fw-semibold"><i class="fas fa-check-circle me-2"></i>${msg}</div><button class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button></div>`;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }

    // Init
    $(document).ready(function() {
        renderTable(portofolioData);

        dt = $('#tblPortofolio').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            responsive: true,
            scrollX: true,
            columnDefs: [{
                orderable: false,
                targets: [4, 6]
            }],
            pageLength: 10,
            drawCallback: function() {
                document.querySelectorAll('#tblPortofolio tbody tr').forEach((tr, i) => {
                    tr.style.animation = 'none';
                    tr.classList.add('fade-in');
                    tr.style.animationDelay = `${i * 0.03}s`;
                });
            }
        });

        // Init select2 for filter
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalFilter'),
        });
    });
</script>
<?= $this->endSection() ?>