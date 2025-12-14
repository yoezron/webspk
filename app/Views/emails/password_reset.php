<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: #f5576c;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }
        .warning {
            background: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #842029;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reset Password</h1>
        <p>Serikat Pekerja Kampus</p>
    </div>

    <div class="content">
        <h2>Halo, <?= esc($name) ?>!</h2>

        <p>Kami menerima permintaan untuk mereset password akun Anda.</p>

        <p>Jika Anda yang meminta reset password, silakan klik tombol di bawah ini:</p>

        <center>
            <a href="<?= $reset_url ?>" class="button">Reset Password</a>
        </center>

        <p>Atau salin dan tempel link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background: #fff; padding: 10px; border: 1px solid #ddd;">
            <?= $reset_url ?>
        </p>

        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul>
                <li>Link reset password ini akan kadaluarsa dalam <strong><?= $expiry_hours ?> jam</strong></li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini dan password Anda tidak akan berubah</li>
                <li>Jangan bagikan link ini kepada siapapun untuk keamanan akun Anda</li>
            </ul>
        </div>

        <p>Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi kami:</p>
        <ul>
            <li>Email: <?= getenv('email.fromEmail') ?></li>
            <li>Website: <?= base_url('/') ?></li>
        </ul>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. All rights reserved.</p>
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
    </div>
</body>
</html>
