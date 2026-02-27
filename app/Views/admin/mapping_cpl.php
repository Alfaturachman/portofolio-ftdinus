<?= $this->extend('template') ?>
<?= $this->section('title') ?>Mapping MK × CPL × PI<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container {
        width: 100% !important;
    }

    .tag-mk {
        background: #dbeafe;
        color: #1e40af;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-cpl {
        background: #fef9c3;
        color: #713f12;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-pi {
        background: #dcfce7;
        color: #166534;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .cell-wrap {
        max-width: 200px;
        white-space: normal;
        font-size: 12px;
        line-height: 1.4;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Mapping MK × CPL × PI</div>
        <div class="page-subtitle">Pemetaan Matakuliah dengan CPL dan Performansi Indikator</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mapping
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblMapping" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Matakuliah</th>
                <th>Kurikulum</th>
                <th>Prodi</th>
                <th>CPL</th>
                <th>PI</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- ── Modal Tambah / Edit ─────────────────────────── -->
<div class="modal fade" id="modalMapping" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">

                <div class="row g-3">
                    <!-- Kurikulum (harus dipilih dulu agar CPL & PI terfilter) -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kurikulum <span class="text-danger">*</span></label>
                        <select id="id_kurikulum" class="form-select form-select-sm s2-kurikulum">
                            <option value="">-- Pilih Kurikulum --</option>
                            <?php foreach ($kurikulum as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['tahun_ajaran'] ?> — <?= $k['nama_kurikulum'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Prodi -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Program Studi <span class="text-danger">*</span></label>
                        <select id="id_prodi" class="form-select form-select-sm s2-prodi">
                            <option value="">-- Pilih Prodi --</option>
                            <?php foreach ($prodi as $p): ?>
                                <option value="<?= $p['id'] ?>">[<?= $p['kode_prodi'] ?>] <?= $p['nama_prodi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- MK -->
                    <div class="col-12">
                        <label class="form-label fw-500">Matakuliah <span class="text-danger">*</span></label>
                        <select id="id_mk" class="form-select form-select-sm s2-mk">
                            <option value="">-- Pilih Matakuliah --</option>
                            <?php foreach ($mk as $m): ?>
                                <option value="<?= $m['id'] ?>">[<?= $m['kode_mk'] ?>] <?= $m['nama_mk'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- CPL — diisi via AJAX setelah kurikulum dipilih -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">
                            CPL <span class="text-danger">*</span>
                            <small class="text-muted ms-1">(pilih kurikulum dulu)</small>
                        </label>
                        <select id="id_cpl" class="form-select form-select-sm s2-cpl" disabled>
                            <option value="">-- Pilih CPL --</option>
                        </select>
                    </div>

                    <!-- PI — diisi via AJAX setelah kurikulum + prodi dipilih -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">
                            PI <span class="text-danger">*</span>
                            <small class="text-muted ms-1">(pilih kurikulum & prodi dulu)</small>
                        </label>
                        <select id="id_pi" class="form-select form-select-sm s2-pi" disabled>
                            <option value="">-- Pilih PI --</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="saveMapping()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Import ────────────────────────────────── -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Mapping dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Gunakan template berikut:
                    <a href="<?= base_url('admin/mapping_cpl/template') ?>" class="fw-600 text-decoration-none">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a>
                </div>
                <div id="importAlert" class="d-none"></div>
                <label class="form-label fw-500">Pilih File Excel</label>
                <input type="file" id="fileExcel" class="form-control form-control-sm" accept=".xlsx,.xls">
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success btn-sm px-4" id="btnImport" onclick="doImport()">
                    <span id="importSpinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                    Import
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Hapus ─────────────────────────────────── -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:40px;color:#ef4444"><i class="bi bi-trash3"></i></div>
                <p class="fw-600 mb-1">Hapus Mapping?</p>
                <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan.</p>
                <input type="hidden" id="deleteId">
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-3 gap-2">
                <button class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm px-4" onclick="confirmDelete()">Hapus</button>
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
    const BASE = '<?= base_url() ?>';
    let dt, isEdit = false;

    const modal = new bootstrap.Modal('#modalMapping');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    // ── Init Select2 ─────────────────────────────────────
    $(document).ready(function() {
        const s2Opts = (placeholder, parent) => ({
            theme: 'bootstrap-5',
            placeholder,
            allowClear: true,
            dropdownParent: $(parent),
        });

        $('.s2-kurikulum').select2(s2Opts('-- Pilih Kurikulum --', '#modalMapping'));
        $('.s2-prodi').select2(s2Opts('-- Pilih Prodi --', '#modalMapping'));
        $('.s2-mk').select2(s2Opts('-- Pilih Matakuliah --', '#modalMapping'));
        $('.s2-cpl').select2(s2Opts('-- Pilih CPL --', '#modalMapping'));
        $('.s2-pi').select2(s2Opts('-- Pilih PI --', '#modalMapping'));

        // Ketika kurikulum berubah → reload CPL & PI
        $('#id_kurikulum').on('change', function() {
            const idK = $(this).val();
            const idP = $('#id_prodi').val();
            loadCpl(idK);
            loadPi(idK, idP);
        });

        // Ketika prodi berubah → reload PI
        $('#id_prodi').on('change', function() {
            const idK = $('#id_kurikulum').val();
            const idP = $(this).val();
            loadPi(idK, idP);
        });

        // ── DataTable ────────────────────────────────────
        dt = $('#tblMapping').DataTable({
            ajax: {
                url: BASE + '/admin/mapping_cpl/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: null,
                    render: r => `<span class="tag-mk">${r.kode_mk}</span><div class="small text-muted mt-1">${r.nama_mk}</div>`
                },
                {
                    data: null,
                    render: r => `${r.tahun_ajaran ?? ''}<br><span class="text-muted small">${r.nama_kurikulum ?? '-'}</span>`
                },
                {
                    data: null,
                    render: r => `[${r.kode_prodi ?? '-'}] ${r.nama_prodi ?? '-'}`
                },
                {
                    data: null,
                    render: r => `<span class="tag-cpl">${r.no_cpl}</span><div class="cell-wrap text-muted mt-1">${r.cpl_indo ?? ''}</div>`
                },
                {
                    data: null,
                    render: r => `<span class="tag-pi">${r.no_pi}</span><div class="cell-wrap text-muted mt-1">${r.isi_pi ?? ''}</div>`
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" onclick="editMapping(${id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMapping(${id})"><i class="bi bi-trash"></i></button>
                    </div>`
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            responsive: true,
            scrollX: true,
        });
    });

    // ── AJAX loader CPL & PI ─────────────────────────────
    function loadCpl(idK) {
        const $sel = $('#id_cpl');
        $sel.prop('disabled', true).empty().append('<option value="">-- Pilih CPL --</option>');
        if (!idK) return;

        fetch(`${BASE}/admin/mapping_cpl/cpl/${idK}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(c => $sel.append(new Option(`[${c.no_cpl}] ${c.cpl_indo}`, c.id)));
                $sel.prop('disabled', false).trigger('change');
            });
    }

    function loadPi(idK, idP) {
        const $sel = $('#id_pi');
        $sel.prop('disabled', true).empty().append('<option value="">-- Pilih PI --</option>');
        if (!idK || !idP) return;

        fetch(`${BASE}/admin/mapping_cpl/pi/${idK}/${idP}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(p => $sel.append(new Option(`[${p.no_pi}] ${p.isi_pi}`, p.id)));
                $sel.prop('disabled', false).trigger('change');
            });
    }

    // ── Modal open / reset ───────────────────────────────
    function openModal(edit = false) {
        isEdit = edit;
        document.getElementById('alertBox').classList.add('d-none');
        document.getElementById('modalTitle').textContent = edit ? 'Edit Mapping' : 'Tambah Mapping';
        if (!edit) {
            document.getElementById('editId').value = '';
            ['#id_mk', '#id_kurikulum', '#id_prodi', '#id_cpl', '#id_pi'].forEach(sel => {
                $(sel).val(null).trigger('change');
            });
            $('#id_cpl, #id_pi').prop('disabled', true);
        }
        modal.show();
    }

    function editMapping(id) {
        fetch(`${BASE}/admin/mapping_cpl/${id}`).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editId').value = d.id;

            // Set kurikulum & prodi dulu, lalu load CPL & PI
            $('#id_kurikulum').val(d.id_kurikulum).trigger('change');
            $('#id_prodi').val(d.id_prodi).trigger('change');
            $('#id_mk').val(d.id_mk).trigger('change');

            // Tunggu AJAX selesai baru set nilai CPL & PI
            Promise.all([
                fetch(`${BASE}/admin/mapping_cpl/cpl/${d.id_kurikulum}`).then(r => r.json()),
                fetch(`${BASE}/admin/mapping_cpl/pi/${d.id_kurikulum}/${d.id_prodi}`).then(r => r.json()),
            ]).then(([cpls, pis]) => {
                const $cpl = $('#id_cpl').empty().append('<option value="">-- Pilih CPL --</option>');
                cpls.forEach(c => $cpl.append(new Option(`[${c.no_cpl}] ${c.cpl_indo}`, c.id)));
                $cpl.val(d.id_cpl).prop('disabled', false).trigger('change');

                const $pi = $('#id_pi').empty().append('<option value="">-- Pilih PI --</option>');
                pis.forEach(p => $pi.append(new Option(`[${p.no_pi}] ${p.isi_pi}`, p.id)));
                $pi.val(d.id_pi).prop('disabled', false).trigger('change');

                openModal(true);
            });
        });
    }

    function saveMapping() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            id_mk: $('#id_mk').val(),
            id_kurikulum: $('#id_kurikulum').val(),
            id_prodi: $('#id_prodi').val(),
            id_cpl: $('#id_cpl').val(),
            id_pi: $('#id_pi').val(),
        });
        const url = isEdit ? `${BASE}/admin/mapping_cpl/update/${id}` : `${BASE}/admin/mapping_cpl/store`;

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body
            })
            .then(r => r.json()).then(res => {
                if (res.status === 'success') {
                    modal.hide();
                    dt.ajax.reload();
                    showToast(res.message);
                } else {
                    const box = document.getElementById('alertBox');
                    box.classList.remove('d-none');
                    box.innerHTML = typeof res.message === 'object' ? Object.values(res.message).join('<br>') : res.message;
                }
            });
    }

    function deleteMapping(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/mapping_cpl/delete/${document.getElementById('deleteId').value}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json()).then(res => {
                modalDelete.hide();
                dt.ajax.reload();
                showToast(res.message, res.status === 'success' ? 'success' : 'danger');
            });
    }

    function openImportModal() {
        document.getElementById('fileExcel').value = '';
        document.getElementById('importAlert').className = 'd-none';
        modalImport.show();
    }

    function doImport() {
        const file = document.getElementById('fileExcel').files[0];
        if (!file) {
            alert('Pilih file terlebih dahulu.');
            return;
        }
        const fd = new FormData();
        fd.append('file_excel', file);
        document.getElementById('importSpinner').classList.remove('d-none');
        document.getElementById('btnImport').disabled = true;

        fetch(`${BASE}/admin/mapping_cpl/import`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            })
            .then(r => r.json()).then(res => {
                document.getElementById('importSpinner').classList.add('d-none');
                document.getElementById('btnImport').disabled = false;
                const box = document.getElementById('importAlert');
                box.className = `alert alert-${res.status === 'success' ? 'success' : 'danger'} py-2 small`;
                let html = res.message;
                if (res.errors?.length) html += '<ul class="mb-0 mt-1">' + res.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                box.innerHTML = html;
                if (res.status === 'success') dt.ajax.reload();
            });
    }

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.className = `toast align-items-center text-bg-${type} border-0 show position-fixed bottom-0 end-0 m-3`;
        t.style.zIndex = 9999;
        t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button></div>`;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }
</script>
<?= $this->endSection() ?>