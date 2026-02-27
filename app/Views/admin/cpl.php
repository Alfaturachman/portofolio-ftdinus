<?= $this->extend('template') ?>
<?= $this->section('title') ?>CPL<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container {
        width: 100% !important;
    }

    .cpl-text {
        max-width: 260px;
        white-space: normal;
        font-size: 12.5px;
        line-height: 1.4;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">CPL (Capaian Pembelajaran Lulusan)</div>
        <div class="page-subtitle">Manajemen data CPL</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah CPL
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblCPL" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>No CPL</th>
                <th>Prodi</th>
                <th>Kurikulum</th>
                <th>CPL Indonesia</th>
                <th>CPL Inggris</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalCPL" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah CPL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Prodi <span class="text-danger">*</span></label>
                        <select id="id_prodi" class="form-select form-select-sm select2-prodi">
                            <option value="">-- Pilih Prodi --</option>
                            <?php foreach ($prodi as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['kode_prodi']) ?> – <?= esc($p['nama_prodi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kurikulum <span class="text-danger">*</span></label>
                        <select id="id_kurikulum" class="form-select form-select-sm select2-kurikulum">
                            <option value="">-- Pilih Kurikulum --</option>
                            <?php foreach ($kurikulum as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_kurikulum']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-500">No CPL <span class="text-danger">*</span></label>
                        <input type="text" id="no_cpl" class="form-control form-control-sm" placeholder="Contoh: CPL-01">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-500">CPL (Bahasa Indonesia) <span class="text-danger">*</span></label>
                        <textarea id="cpl_indo" class="form-control form-control-sm" rows="3" placeholder="Deskripsi CPL dalam Bahasa Indonesia"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-500">CPL (Bahasa Inggris) <span class="text-danger">*</span></label>
                        <textarea id="cpl_inggris" class="form-control form-control-sm" rows="3" placeholder="CPL description in English"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="saveCPL()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import CPL dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Gunakan template berikut:
                    <a href="<?= base_url('admin/cpl/template') ?>" class="fw-600 text-decoration-none">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a><br>
                    <span class="text-muted">Kolom: <strong>A</strong>=No CPL &nbsp;|&nbsp; <strong>B</strong>=CPL Indo &nbsp;|&nbsp; <strong>C</strong>=CPL Inggris &nbsp;|&nbsp; <strong>D</strong>=Kode Prodi &nbsp;|&nbsp; <strong>E</strong>=Nama Kurikulum</span>
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

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:40px;color:#ef4444"><i class="bi bi-trash3"></i></div>
                <p class="fw-600 mb-1">Hapus CPL?</p>
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
    const modal = new bootstrap.Modal('#modalCPL');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    $(document).ready(() => {
        $('.select2-prodi').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalCPL')
        });
        $('.select2-kurikulum').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalCPL')
        });

        dt = $('#tblCPL').DataTable({
            ajax: {
                url: BASE + '/admin/cpl/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'no_cpl',
                    render: v => `<strong>${v}</strong>`
                },
                {
                    data: 'kode_prodi',
                    render: (v, t, r) => `<span class="badge bg-secondary">${v ?? '-'}</span><br><small>${r.nama_prodi ?? ''}</small>`
                },
                {
                    data: 'nama_kurikulum',
                    render: v => v ?? '-'
                },
                {
                    data: 'cpl_indo',
                    render: v => `<div class="cpl-text">${v}</div>`
                },
                {
                    data: 'cpl_inggris',
                    render: v => `<div class="cpl-text">${v}</div>`
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editCPL(${id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCPL(${id})"><i class="bi bi-trash"></i></button>
                        </div>`
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            responsive: true,
        });
    });

    function openModal(edit = false) {
        isEdit = edit;
        document.getElementById('alertBox').classList.add('d-none');
        document.getElementById('modalTitle').textContent = edit ? 'Edit CPL' : 'Tambah CPL';
        if (!edit) {
            document.getElementById('editId').value = '';
            document.getElementById('no_cpl').value = '';
            document.getElementById('cpl_indo').value = '';
            document.getElementById('cpl_inggris').value = '';
            $('#id_prodi').val('').trigger('change');
            $('#id_kurikulum').val('').trigger('change');
        }
        modal.show();
    }

    function editCPL(id) {
        fetch(BASE + '/admin/cpl/' + id).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editId').value = d.id;
            document.getElementById('no_cpl').value = d.no_cpl;
            document.getElementById('cpl_indo').value = d.cpl_indo;
            document.getElementById('cpl_inggris').value = d.cpl_inggris;
            $('#id_prodi').val(d.id_prodi).trigger('change');
            $('#id_kurikulum').val(d.id_kurikulum).trigger('change');
            openModal(true);
        });
    }

    function saveCPL() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            id_prodi: $('#id_prodi').val(),
            id_kurikulum: $('#id_kurikulum').val(),
            no_cpl: document.getElementById('no_cpl').value,
            cpl_indo: document.getElementById('cpl_indo').value,
            cpl_inggris: document.getElementById('cpl_inggris').value,
        });
        const url = isEdit ? `${BASE}/admin/cpl/update/${id}` : `${BASE}/admin/cpl/store`;
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

    function deleteCPL(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/cpl/delete/${document.getElementById('deleteId').value}`, {
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
        fetch(`${BASE}/admin/cpl/import`, {
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