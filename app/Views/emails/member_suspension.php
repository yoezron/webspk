<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Pembekuan Keanggotaan</title>
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
            background: #dc3545;
            color: #ffffff;
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
        .alert-box {
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
            margin: 10px 5px;
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
            <h1>⚠️ Pemberitahuan Pembekuan Keanggotaan</h1>
        </div>

        <div class="content">
            <p>Yth. <strong><?= esc($name) ?></strong>,</p>

            <div class="alert-box">
                <strong>PEMBERITAHUAN PENTING:</strong><br>
                Keanggotaan Anda di Serikat Pekerja Kampus telah dibekukan sementara karena tunggakan pembayaran iuran.
            </div>

            <p>Dengan ini kami sampaikan bahwa keanggotaan Anda dengan nomor anggota <strong><?= esc($member_number) ?></strong> telah dibekukan (suspended) karena tunggakan iuran melebihi batas waktu yang ditentukan.</p>

            <div class="details">
                <h3 style="margin-top: 0;">Detail Tunggakan:</h3>
                <table>
                    <tr>
                        <td>Nomor Anggota</td>
                        <td><?= esc($member_number) ?></td>
                    </tr>
                    <tr>
                        <td>Jumlah Tunggakan</td>
                        <td><strong style="color: #dc3545;"><?= $arrears_months ?> Bulan</strong></td>
                    </tr>
                    <tr>
                        <td>Total Nominal</td>
                        <td><strong style="color: #dc3545;">Rp <?= $arrears_amount ?></strong></td>
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

            <h3>Dampak Pembekuan:</h3>
            <ul>
                <li>Tidak dapat mengakses fasilitas anggota</li>
                <li>Tidak dapat mengikuti kegiatan organisasi</li>
                <li>Tidak mendapatkan perlindungan dan advokasi</li>
                <li>Hak suara dalam forum ditangguhkan</li>
            </ul>

            <h3>Cara Mengaktifkan Kembali:</h3>
            <ol>
                <li>Lunasi seluruh tunggakan iuran sebesar <strong>Rp <?= $arrears_amount ?></strong></li>
                <li>Upload bukti pembayaran melalui dashboard member</li>
                <li>Tunggu verifikasi dari bendahara (maksimal 3 hari kerja)</li>
                <li>Keanggotaan akan aktif kembali setelah pembayaran diverifikasi</li>
            </ol>

            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= $payment_url ?>" class="button">Bayar Tunggakan</a>
                <a href="<?= $contact_url ?>" class="button" style="background: #6c757d;">Hubungi Kami</a>
            </div>

            <p style="font-size: 14px; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px;">
                <strong>Catatan:</strong> Jika Anda mengalami kesulitan finansial atau ada kendala lain, silakan hubungi kami untuk solusi terbaik. Kami siap membantu anggota yang mengalami kesulitan.
            </p>

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
