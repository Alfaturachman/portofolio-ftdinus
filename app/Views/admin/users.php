<?= $this->extend('template') ?>
<?= $this->section('title') ?>Users<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .badge-role {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 99px;
        font-weight: 600;
    }

    .badge-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-dosen {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-mahasiswa {
        background: #dcfce7;
        color: #166534;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Users</div>
        <div class="page-subtitle">Manajemen data pengguna sistem</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success btn-sm px-3" onclick="openImportModal()">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah User
        </button>
    </div>
</div>

<div class="card-box">
    <table id="tblUsers" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>NPP</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah / Edit -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editNpp">
                <div class="mb-3">
                    <label class="form-label fw-500">NPP <span class="text-danger">*</span></label>
                    <input type="text" id="npp" class="form-control form-control-sm" placeholder="Contoh: 198501012010121001">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="nama_lengkap" class="form-control form-control-sm" placeholder="Nama lengkap">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Password <span class="text-danger" id="passRequired">*</span></label>
                    <input type="password" id="password" class="form-control form-control-sm" placeholder="Min. 6 karakter">
                    <div class="form-text d-none" id="passHint">Kosongkan jika tidak ingin mengubah password.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Role <span class="text-danger">*</span></label>
                    <select id="role" class="form-select form-select-sm">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="dosen">Dosen</option>
                        <option value="mahasiswa">Mahasiswa</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="saveUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Pengguna dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Gunakan template berikut:
                    <a href="<?= base_url('admin/users/template') ?>" class="fw-600 text-decoration-none">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a><br>
                    <span class="text-muted">Kolom: <strong>A</strong>=NPP &nbsp;|&nbsp; <strong>B</strong>=Nama Lengkap &nbsp;|&nbsp; <strong>C</strong>=Role</span><br>
                    <span class="text-muted">Password default: NPP masing-masing pengguna.</span>
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
                <p class="fw-600 mb-1">Hapus User?</p>
                <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan.</p>
                <input type="hidden" id="deleteNpp">
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
    const modalUser = new bootstrap.Modal('#modalUser');
    const modalDelete = new bootstrap.Modal('#modalDelete');
    const modalImport = new bootstrap.Modal('#modalImport');

    $(document).ready(function() {
        dt = $('#tblUsers').DataTable({
            ajax: {
                url: BASE + '/admin/users/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'npp'
                },
                {
                    data: 'nama_lengkap'
                },
                {
                    data: 'role',
                    render: r =>
                        `<span class="badge-role badge-${r}">${r.charAt(0).toUpperCase() + r.slice(1)}</span>`
                },
                {
                    data: 'created_at',
                    render: d => d ? d.substring(0, 10) : '-'
                },
                {
                    data: 'npp',
                    render: npp =>
                        `<div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editUser('${npp}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser('${npp}')"><i class="bi bi-trash"></i></button>
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
        document.getElementById('modalTitle').textContent = edit ? 'Edit User' : 'Tambah User';
        document.getElementById('npp').disabled = edit;
        document.getElementById('passHint').classList.toggle('d-none', !edit);
        document.getElementById('passRequired').style.display = edit ? 'none' : 'inline';
        if (!edit) {
            ['npp', 'nama_lengkap', 'password'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('role').selectedIndex = 0;
            document.getElementById('editNpp').value = '';
        }
        modalUser.show();
    }

    function editUser(npp) {
        fetch(BASE + '/admin/users/' + npp).then(r => r.json()).then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            document.getElementById('editNpp').value = d.npp;
            document.getElementById('npp').value = d.npp;
            document.getElementById('nama_lengkap').value = d.nama_lengkap;
            document.getElementById('password').value = '';
            document.getElementById('role').value = d.role;
            openModal(true);
        });
    }

    function saveUser() {
        const npp = document.getElementById('editNpp').value || document.getElementById('npp').value;
        const body = new URLSearchParams({
            npp: document.getElementById('npp').value,
            nama_lengkap: document.getElementById('nama_lengkap').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value,
        });
        const url = isEdit ? `${BASE}/admin/users/update/${npp}` : `${BASE}/admin/users/store`;
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
                    modalUser.hide();
                    dt.ajax.reload();
                    showToast(res.message);
                } else {
                    const box = document.getElementById('alertBox');
                    box.classList.remove('d-none');
                    box.innerHTML = typeof res.message === 'object' ? Object.values(res.message).join('<br>') : res.message;
                }
            });
    }

    function deleteUser(npp) {
        document.getElementById('deleteNpp').value = npp;
        modalDelete.show();
    }

    function confirmDelete() {
        fetch(`${BASE}/admin/users/delete/${document.getElementById('deleteNpp').value}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(r => r.json()).then(res => {
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
        fd.append('user_file', file);
        document.getElementById('importSpinner').classList.remove('d-none');
        document.getElementById('btnImport').disabled = true;
        fetch(`${BASE}/admin/users/import`, {
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