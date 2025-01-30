<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #0f4c92 0%, #1f7ed8 100%);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .wrap {
            max-width: 600px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 6rem;
            margin: 0;
            font-weight: 600;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        p {
            font-size: 1.2rem;
            margin: 1rem 0;
        }

        .buttons {
            margin-top: 2rem;
        }

        .buttons a {
            text-decoration: none;
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            margin: 0 0.5rem;
            transition: background 0.3s ease;
        }

        .buttons a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>

<body>
    <div class="wrap">
        <h1>404</h1>
        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>
        <div class="buttons">
            <a href="/">Kembali ke Beranda</a>
        </div>
        <div class="footer">
            &copy; <?= date('Y') ?> - <?= lang('Fakultas Teknik UDINUS') ?>
        </div>
    </div>
</body>

</html>