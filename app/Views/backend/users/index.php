<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <!-- Page title -->
    <div class="row pt-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <!-- Judul halaman -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h1 class="page-title">Manajemen User</h1>
                        </div>
                    </div>

                    <!-- Button tambah user -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                                <i class="ti ti-plus"></i> Tambah User
                            </a>
                        </div>
                    </div>

                    <!-- Alert pesan sukses/error -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($validation)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                <?php foreach ($validation->getErrors() as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Tabel daftar user -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Homebase</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>ID Staf</th>
                                            <th>Role</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($users)): ?>
                                            <?php foreach ($users as $key => $user): ?>
                                                <tr>
                                                    <td><?= $key + 1 ?></td>
                                                    <td><?= esc($user['homebase']) ?></td>
                                                    <td><?= esc($user['nama']) ?></td>
                                                    <td><?= esc($user['username']) ?></td>
                                                    <td><?= esc($user['status']) ?></td>
                                                    <td><?= esc($user['id_staf']) ?></td>
                                                    <td><?= esc($user['role']) ?></td>
                                                    <td><?= date('d-m-Y H:i:s', strtotime($user['ins_time'])) ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </a>
                                                            <a href="<?= base_url('users/change-password/' . $user['id']) ?>" class="btn btn-sm btn-info">
                                                                <i class="ti ti-lock"></i> Ganti Password
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $user['id'] ?>)">
                                                                <i class="ti ti-trash"></i> Hapus
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Tidak ada data user</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus user ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    let userIdToDelete = null;

    function confirmDelete(userId) {
        userIdToDelete = userId;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (userIdToDelete) {
            // Redirect ke URL hapus
            window.location.href = '<?= base_url('users/delete/') ?>' + userIdToDelete;
        }
    });
</script>

<?= $this->include('backend/partials/footer') ?>