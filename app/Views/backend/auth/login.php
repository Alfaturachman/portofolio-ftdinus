<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('') ?>backend/src/assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="<?= base_url('') ?>backend/src/assets/css/styles.min.css" />
</head>

<body style="background: linear-gradient(135deg, #1161b8 0%, #1f7ed8 100%);">
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="./index.html" class="text-nowrap logo-img text-center d-block pt-3 pb-2 w-100">
                                    <img src="<?= base_url('') ?>backend/src/assets/images/logos/dark-logo.svg" width="240" alt="">
                                </a>
                                <p class="text-center">Silahkan untuk login terlebih dahulu</p>
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                                <?php endif; ?>
                                <form action="<?= base_url('login/process-login') ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" value="1" id="rememberMe">
                                            <label class="form-check-label text-dark" for="rememberMe">
                                                Ingat di perangkat ini
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('') ?>backend/src/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="<?= base_url('') ?>backend/src/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>