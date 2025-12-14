<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keanggotaan Disetujui</title>
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            background: #11998e;
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
        .success-box {
            background: #d1e7dd;
            border: 1px solid #badbcc;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .member-number {
            font-size: 24px;
            font-weight: bold;
            color: #11998e;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Selamat!</h1>
        <p>Keanggotaan Anda Telah Disetujui</p>
    </div>

    <div class="content">
        <h2>Halo, <?= esc($name) ?>!</h2>

        <p>Kami dengan senang hati memberitahukan bahwa pendaftaran keanggotaan Anda di <strong>Serikat Pekerja Kampus</strong> telah <strong>DISETUJUI</strong>!</p>

        <div class="success-box">
            <p>Nomor Anggota Anda:</p>
            <div class="member-number"><?= esc($member_number) ?></div>
            <p style="font-size: 12px; color: #666;">Simpan nomor anggota ini untuk keperluan administrasi</p>
        </div>

        <p>Sekarang Anda adalah anggota resmi Serikat Pekerja Kampus dan dapat menikmati berbagai manfaat keanggotaan:</p>

        <ul>
            <li>✅ Akses penuh ke dashboard anggota</li>
            <li>✅ Perlindungan hak dan advokasi hukum</li>
            <li>✅ Akses ke pelatihan dan workshop</li>
            <li>✅ Jaringan profesional sesama pekerja kampus</li>
            <li>✅ Informasi dan update terbaru organisasi</li>
        </ul>

        <p>Silakan login ke dashboard Anda untuk mulai menggunakan layanan kami:</p>

        <center>
            <a href="<?= $login_url ?>" class="button">Login ke Dashboard</a>
        </center>

        <p><strong>Langkah Selanjutnya:</strong></p>
        <ol>
            <li>Login menggunakan email dan password yang sudah Anda daftarkan</li>
            <li>Lengkapi profil Anda jika ada data yang masih kurang</li>
            <li>Lakukan pembayaran iuran bulanan pertama Anda</li>
            <li>Jelajahi fitur-fitur yang tersedia di dashboard</li>
        </ol>

        <p>Terima kasih telah bergabung dengan kami. Mari bersama-sama memperjuangkan hak dan kesejahteraan pekerja kampus!</p>

        <p>Jika Anda memiliki pertanyaan, silakan hubungi kami:</p>
        <ul>
            <li>Email: <?= getenv('email.fromEmail') ?></li>
            <li>Website: <?= base_url('/') ?></li>
        </ul>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. All rights reserved.</p>
        <p>Solidaritas adalah kekuatan kita!</p>
    </div>
</body>
</html>
