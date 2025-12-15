<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Tunggakan Iuran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #ffc107;
            color: #000;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Peringatan Tunggakan Iuran</h1>
        </div>

        <div class="content">
            <p>Yth. <strong><?= esc($name) ?></strong>,</p>

            <div class="warning-box">
                <strong>PERINGATAN:</strong><br>
                Kami menemukan adanya tunggakan pembayaran iuran pada keanggotaan Anda. Segera lakukan pembayaran untuk menghindari pembekuan keanggotaan.
            </div>

            <p>Berdasarkan catatan kami, keanggotaan Anda dengan nomor <strong><?= esc($member_number) ?></strong> memiliki tunggakan iuran yang perlu segera diselesaikan.</p>

            <div class="details">
                <h3 style="margin-top: 0;">Detail Tunggakan:</h3>
                <table>
                    <tr>
                        <td>Nomor Anggota</td>
                        <td><?= esc($member_number) ?></td>
                    </tr>
                    <tr>
                        <td>Jumlah Tunggakan</td>
                        <td><strong style="color: #ffc107;"><?= $arrears_months ?> Bulan</strong></td>
                    </tr>
                    <tr>
                        <td>Total Nominal</td>
                        <td><strong style="color: #ffc107;">Rp <?= $arrears_amount ?></strong></td>
                    </tr>
                    <tr>
                        <td>Iuran Bulanan</td>
                        <td>Rp <?= $monthly_dues ?></td>
                    </tr>
                    <tr>
                        <td>Pembayaran Terakhir</td>
                        <td><?= $last_payment_date ?></td>
                    </tr>
                </table>
            </div>

            <div class="warning-box">
                <strong>PERHATIAN:</strong><br>
                Jika tunggakan mencapai 3 bulan atau lebih, keanggotaan Anda akan otomatis dibekukan (suspended) dan Anda tidak dapat mengakses fasilitas anggota hingga tunggakan dilunasi.
            </div>

            <h3>Cara Pembayaran:</h3>
            <ol>
                <li>Login ke dashboard member</li>
                <li>Pilih menu "Pembayaran" → "Submit Pembayaran"</li>
                <li>Lakukan transfer ke rekening organisasi</li>
                <li>Upload bukti transfer</li>
                <li>Tunggu verifikasi dari bendahara (maksimal 3 hari kerja)</li>
            </ol>

            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= $payment_url ?>" class="button">Bayar Sekarang</a>
            </div>

            <p style="font-size: 14px; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px;">
                <strong>Catatan:</strong> Jika Anda sudah melakukan pembayaran tetapi belum terverifikasi, mohon bersabar menunggu proses verifikasi. Jika ada kendala atau pertanyaan, jangan ragu untuk menghubungi kami.
            </p>

            <p>Terima kasih atas perhatian dan kerjasamanya.</p>

            <p>Hormat kami,<br>
            <strong>Serikat Pekerja Kampus</strong></p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            <p>&copy; <?= date('Y') ?> Serikat Pekerja Kampus. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
