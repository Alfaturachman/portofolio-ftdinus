<?= $this->extend('template') ?>
<?= $this->section('title') ?>Kurikulum<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Kurikulum</div>
        <div class="page-subtitle">Manajemen data kurikulum</div>
    </div>
    <button class="btn btn-primary btn-sm px-3" onclick="openModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kurikulum
    </button>
</div>

<div class="card-box">
    <table id="tblKurikulum" class="table table-hover align-middle w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Tahun Ajaran</th>
                <th>Nama Kurikulum</th>
                <th style="width:110px">Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah / Edit -->
<div class="modal fade" id="modalKurikulum" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-700" id="modalTitle">Tambah Kurikulum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertBox" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">

                <div class="mb-3">
                    <label class="form-label fw-500">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" id="tahun_ajaran" class="form-control form-control-sm" placeholder="Contoh: 2024/2025">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Nama Kurikulum <span class="text-danger">*</span></label>
                    <input type="text" id="nama_kurikulum" class="form-control form-control-sm" placeholder="Nama kurikulum">
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm px-4" onclick="saveKurikulum()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:40px; color:#ef4444;"><i class="bi bi-trash3"></i></div>
                <p class="fw-600 mb-1">Hapus Kurikulum?</p>
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
    let dt, isEdit = false,
        currentId = null;

    const modal = new bootstrap.Modal('#modalKurikulum');
    const modalDelete = new bootstrap.Modal('#modalDelete');

    $(document).ready(function() {
        dt = $('#tblKurikulum').DataTable({
            ajax: {
                url: BASE + '/admin/kurikulum/data',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'tahun_ajaran'
                },
                {
                    data: 'nama_kurikulum'
                },
                {
                    data: 'id',
                    render: id =>
                        `<div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editKurikulum(${id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteKurikulum(${id})"><i class="bi bi-trash"></i></button>
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
        document.getElementById('modalTitle').textContent = edit ? 'Edit Kurikulum' : 'Tambah Kurikulum';
        if (!edit) {
            document.getElementById('editId').value = '';
            document.getElementById('tahun_ajaran').value = '';
            document.getElementById('nama_kurikulum').value = '';
        }
        modal.show();
    }

    function editKurikulum(id) {
        fetch(BASE + '/admin/kurikulum/' + id)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return alert(res.message);
                const d = res.data;
                currentId = d.id;
                document.getElementById('editId').value = d.id;
                document.getElementById('tahun_ajaran').value = d.tahun_ajaran;
                document.getElementById('nama_kurikulum').value = d.nama_kurikulum;
                openModal(true);
            });
    }

    function saveKurikulum() {
        const id = document.getElementById('editId').value;
        const body = new URLSearchParams({
            tahun_ajaran: document.getElementById('tahun_ajaran').value,
            nama_kurikulum: document.getElementById('nama_kurikulum').value,
        });

        const url = isEdit ? BASE + '/admin/kurikulum/update/' + id : BASE + '/admin/kurikulum/store';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    modal.hide();
                    dt.ajax.reload();
                    showToast(res.message);
                } else {
                    const box = document.getElementById('alertBox');
                    box.classList.remove('d-none');
                    box.innerHTML = typeof res.message === 'object' ?
                        Object.values(res.message).join('<br>') :
                        res.message;
                }
            });
    }

    function deleteKurikulum(id) {
        document.getElementById('deleteId').value = id;
        modalDelete.show();
    }

    function confirmDelete() {
        const id = document.getElementById('deleteId').value;
        fetch(BASE + '/admin/kurikulum/delete/' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                modalDelete.hide();
                dt.ajax.reload();
                showToast(res.message, res.status === 'success' ? 'success' : 'danger');
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