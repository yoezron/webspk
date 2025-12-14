<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #667eea;
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
        .payment-details {
            background: white;
            border: 2px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .payment-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Pembayaran Diterima</h1>
        <p>Serikat Pekerja Kampus</p>
    </div>

    <div class="content">
        <h2>Halo, <?= esc($name) ?>!</h2>

        <p>Terima kasih! Pembayaran iuran Anda telah kami terima dan diverifikasi.</p>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #667eea;">Detail Pembayaran</h3>
            <div class="payment-row">
                <span>Periode:</span>
                <span><strong><?= esc($period) ?></strong></span>
            </div>
            <div class="payment-row">
                <span>Jumlah:</span>
                <span><strong><?= esc($amount) ?></strong></span>
            </div>
            <div class="payment-row">
                <span>Tanggal:</span>
                <span><strong><?= date('d/m/Y H:i') ?></strong></span>
            </div>
            <div class="payment-row">
                <span>Status:</span>
                <span style="color: #28a745;"><strong>LUNAS ✓</strong></span>
            </div>
        </div>

        <p>Pembayaran Anda telah dicatat dalam sistem dan status keanggotaan Anda tetap aktif.</p>

        <p>Anda dapat melihat riwayat pembayaran lengkap di dashboard:</p>

        <center>
            <a href="<?= $dashboard_url ?>" class="button">Lihat Dashboard</a>
        </center>

        <p><strong>Catatan Penting:</strong></p>
        <ul>
            <li>Simpan email ini sebagai bukti pembayaran</li>
            <li>Pembayaran periode berikutnya akan jatuh tempo pada tanggal yang sama bulan depan</li>
            <li>Anda akan menerima reminder sebelum jatuh tempo</li>
        </ul>

        <p>Terima kasih atas dukungan dan kontribusi Anda untuk organisasi!</p>

        <p>Jika ada pertanyaan tentang pembayaran, hubungi kami:</p>
        <ul>
            <li>Email: <?= getenv('email.fromEmail') ?></li>
            <li>Website: <?= base_url('/') ?></li>
        </ul>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. All rights reserved.</p>
        <p>Email ini adalah konfirmasi resmi pembayaran Anda</p>
    </div>
</body>
</html>
