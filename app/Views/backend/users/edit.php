<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <!-- Page title -->
    <div class="row pt-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <!-- Page title -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h1 class="page-title">Edit User</h1>
                        </div>
                    </div>

                    <!-- Alert pesan error -->
                    <?php if (isset($errors)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Form edit user -->
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('users/update/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="homebase" class="form-label">Homebase</label>
                                            <input type="text" class="form-control" id="homebase" name="homebase" value="<?= old('homebase', $user['homebase']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $user['nama']) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="">Pilih Status</option>
                                                <option value="aktif" <?= (old('status', $user['status']) == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                <option value="tidak_aktif" <?= (old('status', $user['status']) == 'tidak_aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="id_staf" class="form-label">ID Staf</label>
                                            <input type="text" class="form-control" id="id_staf" name="id_staf" value="<?= old('id_staf', $user['id_staf']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select" id="role" name="role" required>
                                                <option value="">Pilih Role</option>
                                                <option value="admin" <?= (old('role', $user['role']) == 'admin') ? 'selected' : '' ?>>Admin</option>
                                                <option value="progdi" <?= (old('role', $user['role']) == 'progdi') ? 'selected' : '' ?>>Program Studi</option>
                                                <option value="dekan" <?= (old('role', $user['role']) == 'dekan') ? 'selected' : '' ?>>Dekan</option>
                                                <option value="dosen" <?= (old('role', $user['role']) == 'dosen') ? 'selected' : '' ?>>Dosen</option>
                                                <option value="super admin" <?= (old('role', $user['role']) == 'super admin') ? 'selected' : '' ?>>Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="<?= base_url('users') ?>" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>