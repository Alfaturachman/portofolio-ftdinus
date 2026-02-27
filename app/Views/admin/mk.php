<?= $this->extend('template') ?>
<?= $this->section('title') ?>Matakuliah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .sks-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag {
        background: #f1f5f9;
        color: #475569;
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
        <div class="page-title">Matakuliah</div>
        <div class="page-subtitle">Manajemen data matakuliah</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah MK
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblMK" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Kode MK</th>
                <th>Nama Matakuliah</th>
                <th>Kelompok</th>
                <th>SKS</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalMK" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Matakuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-500">Kode MK <span class="text-danger">*</span></label>
                    <input type="text" id="kode_mk" class="form-control form-control-sm" placeholder="Contoh: MK001">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Nama Matakuliah <span class="text-danger">*</span></label>
                    <input type="text" id="nama_mk" class="form-control form-control-sm" placeholder="Nama matakuliah">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Kelompok MK <span class="text-danger">*</span></label>
                    <input type="text" id="kelp_mk" class="form-control form-control-sm" placeholder="Contoh: Inti / Pilihan">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-500">SKS Teori <span class="text-danger">*</span></label>
                        <input type="number" id="teori" class="form-control form-control-sm" min="0" max="6" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-500">SKS Praktek <span class="text-danger">*</span></label>
                        <input type="number" id="praktek" class="form-control form-control-sm" min="0" max="6" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="saveMK()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Matakuliah dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Pastikan file sesuai format template.
                    <a href="<?= base_url('admin/mk/template') ?>" class="fw-600 text-decoration-none">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a>
                </div>
                <div id="importAlert" class="d-none"></div>
                <div class="mb-3">
                    <label class="form-label fw-500">Pilih File Excel <span class="text-danger">*</span></label>
                    <input type="file" id="fileExcel" class="form-control form-control-sm" accept=".xlsx,.xls">
                </div>
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
                <p class="fw-600 mb-1">Hapus Matakuliah?</p>
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
<script>
    const BASE = '<?= base_url() ?>';
    let dt, isEdit = false;
    const modal = new bootstrap.Modal('#modalMK');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    $(document).ready(() => {
        dt = $('#tblMK').DataTable({
            ajax: {
                url: BASE + '/admin/mk/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'kode_mk'
                },
                {
                    data: 'nama_mk'
                },
                {
                    data: 'kelp_mk',
                    render: v => `<span class="tag">${v}</span>`
                },
                {
                    data: null,
                    render: r => {
                        const total = (+r.teori || 0) + (+r.praktek || 0);
                        return `<span class="sks-badge">
                            <span class="tag">T:${r.teori}</span>
                            <span class="tag">P:${r.praktek}</span>
                            <strong>${total} SKS</strong>
                        </span>`;
                    }
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editMK(${id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteMK(${id})"><i class="bi bi-trash"></i></button>
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
        document.getElementById('modalTitle').textContent = edit ? 'Edit Matakuliah' : 'Tambah Matakuliah';
        if (!edit)['editId', 'kode_mk', 'nama_mk', 'kelp_mk'].forEach(id => document.getElementById(id).value = '');
        if (!edit) {
            document.getElementById('teori').value = 0;
            document.getElementById('praktek').value = 0;
        }
        modal.show();
    }

    function editMK(id) {
        fetch(BASE + '/admin/mk/' + id).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editId').value = d.id;
            document.getElementById('kode_mk').value = d.kode_mk;
            document.getElementById('nama_mk').value = d.nama_mk;
            document.getElementById('kelp_mk').value = d.kelp_mk;
            document.getElementById('teori').value = d.teori;
            document.getElementById('praktek').value = d.praktek;
            openModal(true);
        });
    }

    function saveMK() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            kode_mk: document.getElementById('kode_mk').value,
            nama_mk: document.getElementById('nama_mk').value,
            kelp_mk: document.getElementById('kelp_mk').value,
            teori: document.getElementById('teori').value,
            praktek: document.getElementById('praktek').value,
        });
        const url = isEdit ? `${BASE}/admin/mk/update/${id}` : `${BASE}/admin/mk/store`;
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

    function deleteMK(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/mk/delete/${document.getElementById('deleteId').value}`, {
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

        fetch(`${BASE}/admin/mk/import`, {
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
                if (res.errors && res.errors.length) html += '<ul class="mb-0 mt-1">' + res.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                box.innerHTML = html;

                if (res.status === 'success') {
                    dt.ajax.reload();
                }
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