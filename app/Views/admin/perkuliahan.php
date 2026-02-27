<?= $this->extend('template') ?>
<?= $this->section('title') ?>Perkuliahan<?= $this->endSection() ?>

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

    .tag-smt {
        background: #f3e8ff;
        color: #6b21a8;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-kelas {
        background: #fef9c3;
        color: #713f12;
        border-radius: 5px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Perkuliahan</div>
        <div class="page-subtitle">Manajemen data jadwal perkuliahan</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Perkuliahan
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblPerkuliahan" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Matakuliah</th>
                <th>Dosen</th>
                <th>Kurikulum</th>
                <th>Semester</th>
                <th>Kelas</th>
                <th>Tahun Akademik</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- ── Modal Tambah / Edit ─────────────────────────── -->
<div class="modal fade" id="modalPerkuliahan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Perkuliahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">

                <div class="row g-3">
                    <!-- MK -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Matakuliah <span class="text-danger">*</span></label>
                        <select id="id_mk" class="form-select form-select-sm s2-mk">
                            <option value="">-- Pilih Matakuliah --</option>
                            <?php foreach ($mk as $m): ?>
                                <option value="<?= $m['id'] ?>">[<?= $m['kode_mk'] ?>] <?= $m['nama_mk'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dosen / Users -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Dosen (NPP) <span class="text-danger">*</span></label>
                        <select id="id_users" class="form-select form-select-sm s2-users">
                            <option value="">-- Pilih Dosen --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['npp'] ?>">[<?= $u['npp'] ?>] <?= $u['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Kurikulum -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kurikulum <span class="text-danger">*</span></label>
                        <select id="id_kurikulum" class="form-select form-select-sm s2-kurikulum">
                            <option value="">-- Pilih Kurikulum --</option>
                            <?php foreach ($kurikulum as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['tahun_ajaran'] ?> — <?= $k['nama_kurikulum'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Semester <span class="text-danger">*</span></label>
                        <select id="semester" class="form-select form-select-sm s2-semester">
                            <option value="">-- Pilih Semester --</option>
                            <?php for ($s = 1; $s <= 8; $s++): ?>
                                <option value="<?= $s ?>">Semester <?= $s ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Kode Kelas -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kode Kelas <span class="text-danger">*</span></label>
                        <input type="text" id="kode_kelas" class="form-control form-control-sm" placeholder="Contoh: A, B, C">
                    </div>

                    <!-- Tahun Akademik -->
                    <div class="col-md-6">
                        <label class="form-label fw-500">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" id="tahun_akademik" class="form-control form-control-sm" placeholder="Contoh: 2024/2025 Ganjil">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="savePerkuliahan()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Import ────────────────────────────────── -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Perkuliahan dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Gunakan template berikut:
                    <a href="<?= base_url('admin/perkuliahan/template') ?>" class="fw-600 text-decoration-none">
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
                <p class="fw-600 mb-1">Hapus Data Perkuliahan?</p>
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

    const modal = new bootstrap.Modal('#modalPerkuliahan');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    $(document).ready(function() {
        const s2Opts = (placeholder) => ({
            theme: 'bootstrap-5',
            placeholder,
            allowClear: true,
            dropdownParent: $('#modalPerkuliahan'),
        });

        $('.s2-mk').select2(s2Opts('-- Pilih Matakuliah --'));
        $('.s2-users').select2(s2Opts('-- Pilih Dosen --'));
        $('.s2-kurikulum').select2(s2Opts('-- Pilih Kurikulum --'));
        $('.s2-semester').select2(s2Opts('-- Pilih Semester --'));

        dt = $('#tblPerkuliahan').DataTable({
            ajax: {
                url: BASE + '/admin/perkuliahan/data',
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
                    render: r => `${r.nama_lengkap ?? '-'}<div class="small text-muted">${r.npp ?? ''}</div>`
                },
                {
                    data: null,
                    render: r => `${r.tahun_ajaran ?? ''}<br><span class="text-muted small">${r.nama_kurikulum ?? '-'}</span>`
                },
                {
                    data: 'semester',
                    render: v => `<span class="tag-smt">Smt ${v}</span>`
                },
                {
                    data: 'kode_kelas',
                    render: v => `<span class="tag-kelas">${v}</span>`
                },
                {
                    data: 'tahun_akademik'
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" onclick="editPerkuliahan(${id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePerkuliahan(${id})"><i class="bi bi-trash"></i></button>
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

    function openModal(edit = false) {
        isEdit = edit;
        document.getElementById('alertBox').classList.add('d-none');
        document.getElementById('modalTitle').textContent = edit ? 'Edit Perkuliahan' : 'Tambah Perkuliahan';
        if (!edit) {
            document.getElementById('editId').value = '';
            document.getElementById('kode_kelas').value = '';
            document.getElementById('tahun_akademik').value = '';
            ['#id_mk', '#id_users', '#id_kurikulum', '#semester'].forEach(sel => {
                $(sel).val(null).trigger('change');
            });
        }
        modal.show();
    }

    function editPerkuliahan(id) {
        fetch(`${BASE}/admin/perkuliahan/${id}`).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editId').value = d.id;
            document.getElementById('kode_kelas').value = d.kode_kelas;
            document.getElementById('tahun_akademik').value = d.tahun_akademik;
            $('#id_mk').val(d.id_mk).trigger('change');
            $('#id_users').val(d.id_users).trigger('change');
            $('#id_kurikulum').val(d.id_kurikulum).trigger('change');
            $('#semester').val(d.semester).trigger('change');
            openModal(true);
        });
    }

    function savePerkuliahan() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            id_mk: $('#id_mk').val(),
            id_users: $('#id_users').val(),
            id_kurikulum: $('#id_kurikulum').val(),
            semester: $('#semester').val(),
            kode_kelas: document.getElementById('kode_kelas').value,
            tahun_akademik: document.getElementById('tahun_akademik').value,
        });
        const url = isEdit ? `${BASE}/admin/perkuliahan/update/${id}` : `${BASE}/admin/perkuliahan/store`;

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

    function deletePerkuliahan(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/perkuliahan/delete/${document.getElementById('deleteId').value}`, {
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

        fetch(`${BASE}/admin/perkuliahan/import`, {
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