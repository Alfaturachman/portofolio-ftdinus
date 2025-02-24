<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Profile Saya</h4>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <table class="table table-bordered">
                            <tr>
                                <th>Nama</th>
                                <td><?= esc($profile['nama'] ?? 'Tidak tersedia') ?></td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td><?= esc($profile['username'] ?? 'Tidak tersedia') ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= esc($profile['email'] ?? 'Tidak tersedia') ?></td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td><?= esc($profile['role'] ?? 'Tidak tersedia') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>