<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            min-height: 100vh;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 16px 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }

        .divider span {
            position: relative;
            background: #fff;
            padding: 0 12px;
            color: #adb5bd;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <div class="card p-4">
        <!-- Logo & Title -->
        <div class="text-center mb-4">

            <h5 class="fw-bold mt-3 mb-1">Selamat Datang</h5>
            <small class="text-muted">Masuk ke akun Anda</small>
        </div>

        <!-- Form -->
        <form action="<?= base_url('login/proses') ?>" method="post">
            <div class="mb-3">
                <label class="form-label fw-medium small">NPP</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fa-regular fa-id-card text-muted"></i>
                    </span>
                    <input type="text" name="npp" class="form-control border-start-0 ps-0" placeholder="0686.." />
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium small">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fa-solid fa-lock text-muted"></i>
                    </span>
                    <input type="password" id="pwd" name="password" class="form-control border-start-0 border-end-0 ps-0"
                        placeholder="••••••••" />
                    <span class="input-group-text bg-light border-start-0" style="cursor:pointer" onclick="togglePwd()">
                        <i id="eye" class="fa-regular fa-eye text-muted"></i>
                    </span>
                </div>
            </div>



            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk
            </button>
        </form>
    </div>

    <script>
        function togglePwd() {
            const pwd = document.getElementById('pwd');
            const eye = document.getElementById('eye');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                eye.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>