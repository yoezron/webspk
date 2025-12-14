<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Keanggotaan</title>
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
            background: linear-gradient(135deg, #834d9b 0%, #d04ed6 100%);
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
            background: #834d9b;
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
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pemberitahuan Status Keanggotaan</h1>
        <p>Serikat Pekerja Kampus</p>
    </div>

    <div class="content">
        <h2>Halo, <?= esc($name) ?>!</h2>

        <p>Terima kasih atas minat Anda untuk bergabung dengan Serikat Pekerja Kampus.</p>

        <p>Setelah melakukan review terhadap aplikasi keanggotaan Anda, dengan berat hati kami informasikan bahwa saat ini pendaftaran Anda <strong>belum dapat disetujui</strong>.</p>

        <div class="info-box">
            <p><strong>Alasan:</strong></p>
            <p><?= nl2br(esc($reason)) ?></p>
        </div>

        <p><strong>Apa yang bisa Anda lakukan?</strong></p>
        <ul>
            <li>Perbaiki data atau dokumen yang diminta</li>
            <li>Hubungi kami untuk klarifikasi lebih lanjut</li>
            <li>Daftar kembali setelah melengkapi persyaratan</li>
        </ul>

        <p>Kami sangat menghargai minat Anda dan berharap dapat menerima Anda sebagai anggota di masa mendatang.</p>

        <center>
            <a href="<?= $contact_url ?>" class="button">Hubungi Kami</a>
        </center>

        <p>Tim kami siap membantu Anda melalui:</p>
        <ul>
            <li>Email: <?= getenv('email.fromEmail') ?></li>
            <li>Website: <?= base_url('/') ?></li>
        </ul>

        <p>Terima kasih atas pengertian Anda.</p>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. All rights reserved.</p>
    </div>
</body>
</html>
