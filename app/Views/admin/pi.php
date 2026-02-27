<?= $this->extend('template') ?>
<?= $this->section('title') ?>PI<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container {
        width: 100% !important;
    }

    .pi-text {
        max-width: 350px;
        white-space: normal;
        font-size: 12.5px;
        line-height: 1.4;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">PI (Performansi Indikator)</div>
        <div class="page-subtitle">Manajemen data performansi indikator</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah PI
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblPI" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>No PI</th>
                <th>Prodi</th>
                <th>Kurikulum</th>
                <th>Isi PI</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalPI" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah PI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Program Studi <span class="text-danger">*</span></label>
                        <select id="id_prodi" class="form-select form-select-sm select2-prodi">
                            <option value="">-- Pilih Prodi --</option>
                            <?php foreach ($prodi as $p): ?>
                                <option value="<?= $p['id'] ?>">[<?= $p['kode_prodi'] ?>] <?= $p['nama_prodi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Kurikulum <span class="text-danger">*</span></label>
                        <select id="id_kurikulum" class="form-select form-select-sm select2-kurikulum">
                            <option value="">-- Pilih Kurikulum --</option>
                            <?php foreach ($kurikulum as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['tahun_ajaran'] ?> — <?= $k['nama_kurikulum'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-500">No PI <span class="text-danger">*</span></label>
                        <input type="text" id="no_pi" class="form-control form-control-sm" placeholder="Contoh: PI-01-01">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-500">Isi PI <span class="text-danger">*</span></label>
                        <textarea id="isi_pi" class="form-control form-control-sm" rows="3" placeholder="Deskripsi performansi indikator"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="savePI()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import PI dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Gunakan template berikut:
                    <a href="<?= base_url('admin/pi/template') ?>" class="fw-600 text-decoration-none">
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

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:40px;color:#ef4444"><i class="bi bi-trash3"></i></div>
                <p class="fw-600 mb-1">Hapus PI?</p>
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
    const modal = new bootstrap.Modal('#modalPI');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    $(document).ready(() => {
        // Init Select2
        $('.select2-prodi').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Prodi --',
            allowClear: true,
            dropdownParent: $('#modalPI'),
        });
        $('.select2-kurikulum').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Kurikulum --',
            allowClear: true,
            dropdownParent: $('#modalPI'),
        });

        dt = $('#tblPI').DataTable({
            ajax: {
                url: BASE + '/admin/pi/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'no_pi',
                    render: v => `<strong>${v}</strong>`
                },
                {
                    data: null,
                    render: r => `[${r.kode_prodi ?? '-'}] ${r.nama_prodi ?? '-'}`
                },
                {
                    data: null,
                    render: r => `${r.tahun_ajaran ?? ''}<br><span class="text-muted small">${r.nama_kurikulum ?? '-'}</span>`
                },
                {
                    data: 'isi_pi',
                    render: v => `<div class="pi-text">${v}</div>`
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editPI(${id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deletePI(${id})"><i class="bi bi-trash"></i></button>
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
        document.getElementById('modalTitle').textContent = edit ? 'Edit PI' : 'Tambah PI';
        if (!edit) {
            document.getElementById('editId').value = '';
            document.getElementById('no_pi').value = '';
            document.getElementById('isi_pi').value = '';
            $('#id_prodi').val(null).trigger('change');
            $('#id_kurikulum').val(null).trigger('change');
        }
        modal.show();
    }

    function editPI(id) {
        fetch(BASE + '/admin/pi/' + id).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editId').value = d.id;
            document.getElementById('no_pi').value = d.no_pi;
            document.getElementById('isi_pi').value = d.isi_pi;
            $('#id_prodi').val(d.id_prodi).trigger('change');
            $('#id_kurikulum').val(d.id_kurikulum).trigger('change');
            openModal(true);
        });
    }

    function savePI() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            id_prodi: $('#id_prodi').val(),
            id_kurikulum: $('#id_kurikulum').val(),
            no_pi: document.getElementById('no_pi').value,
            isi_pi: document.getElementById('isi_pi').value,
        });
        const url = isEdit ? `${BASE}/admin/pi/update/${id}` : `${BASE}/admin/pi/store`;
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

    function deletePI(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/pi/delete/${document.getElementById('deleteId').value}`, {
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
        fetch(`${BASE}/admin/pi/import`, {
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