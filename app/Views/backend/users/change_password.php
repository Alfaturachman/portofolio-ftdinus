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
                            <h1 class="page-title">Ganti Password User</h1>
                        </div>
                    </div>

                    <!-- Informasi user -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>Informasi User</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="20%"><strong>Nama</strong></td>
                                    <td width="1%">:</td>
                                    <td><?= esc($user['nama']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username</strong></td>
                                    <td>:</td>
                                    <td><?= esc($user['username']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td>:</td>
                                    <td><?= esc($user['role']) ?></td>
                                </tr>
                            </table>
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

                    <!-- Form ganti password -->
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('users/update-password/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password Baru</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Ganti Password</button>
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