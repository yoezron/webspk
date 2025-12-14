# Quick Start Guide - WebSPK

## Instalasi Cepat (5 Menit)

### 1. Clone dan Setup
```bash
git clone https://github.com/yoezron/webspk.git
cd webspk
```

### 2. Install Dependencies
```bash
pip install -r requirements.txt
```

### 3. Setup Database
```bash
python3 -c "from app import create_app, db; app = create_app(); app.app_context().push(); db.create_all()"
```

### 4. Buat Admin User
```bash
python3 -c "from app import create_app, db; from app.models import User; app = create_app(); app.app_context().push(); u = User(username='admin', email='admin@spk.com', is_admin=True); u.set_password('admin123'); db.session.add(u); db.session.commit(); print('Admin created: username=admin, password=admin123')"
```

### 5. Jalankan Aplikasi
```bash
python3 run.py
```

Buka browser: **http://localhost:5000**

## Login Credentials

### Admin
- Username: `admin`
- Password: `admin123`

**PENTING**: Ganti password setelah login pertama kali!

## Fitur Yang Dapat Digunakan

### Sebagai Admin
1. **Dashboard Admin** - Lihat statistik keanggotaan
2. **Kelola Anggota** - Approve/reject pendaftaran, terbitkan kartu
3. **Buat Survei** - Membuat survei untuk anggota
4. **Lihat Statistik** - Analisis data keanggotaan

### Sebagai Anggota
1. **Daftar Akun** - Registrasi user baru
2. **Daftar Keanggotaan** - Isi formulir keanggotaan
3. **Forum** - Diskusi dengan anggota lain
4. **Isi Survei** - Partisipasi dalam survei
5. **Download Kartu** - Setelah kartu diterbitkan

## Flow Penggunaan Standar

### Untuk Anggota Baru
1. Klik "Daftar" → Buat akun
2. Login dengan akun baru
3. Isi "Formulir Pendaftaran Anggota"
4. Tunggu admin approve
5. Setelah disetujui, dapatkan nomor anggota
6. Download kartu anggota (setelah diterbitkan)

### Untuk Admin
1. Login sebagai admin
2. Buka "Admin Dashboard"
3. Lihat pendaftaran pending
4. Approve pendaftaran → Nomor anggota otomatis digenerate
5. Terbitkan kartu anggota
6. Buat survei jika diperlukan

## Troubleshooting Cepat

### Port sudah digunakan
```bash
# Ganti port di run.py atau
python3 -c "from app import create_app; app = create_app(); app.run(port=5001)"
```

### Error ModuleNotFoundError
```bash
pip install -r requirements.txt --upgrade
```

### Reset Database
```bash
rm -f webspk.db
python3 -c "from app import create_app, db; app = create_app(); app.app_context().push(); db.create_all()"
```

## Struktur Menu

```
Halaman Utama
├── Login/Register (Publik)
├── Dashboard (Setelah Login)
│   ├── Profil Anggota
│   ├── Forum Komunikasi
│   └── Survei
└── Admin (Khusus Admin)
    ├── Dashboard Admin
    ├── Kelola Anggota
    ├── Buat Survei
    └── Statistik
```

## Screenshot Features

### 1. Halaman Utama
Portal dengan informasi sistem dan tombol registrasi/login

### 2. Dashboard Admin
Statistik keanggotaan dengan card untuk:
- Total Anggota
- Pending Approval
- Anggota Aktif
- Diberhentikan

### 3. Daftar Anggota
Tabel dengan fitur:
- Filter berdasarkan status
- Approve/Reject pendaftaran
- Terbitkan kartu anggota
- Hentikan keanggotaan

### 4. Forum
- Buat topik diskusi baru
- Balas diskusi
- Moderasi konten

### 5. Survei
- Buat survei dengan berbagai tipe pertanyaan
- Lihat hasil survei dengan statistik
- Dashboard analisis

### 6. Kartu Anggota
- Generate otomatis dengan QR code
- Download sebagai file PNG
- Berisi nomor anggota dan info lengkap

## Tips Penggunaan

1. **Backup Database**: Copy file `webspk.db` secara berkala
2. **Ganti Password**: Ganti password admin default segera
3. **Environment Variables**: Setup `.env` untuk production
4. **Static Files**: Folder `static/cards/` akan terisi dengan kartu yang digenerate
5. **Uploads**: Folder `uploads/` untuk file upload (jika ada)

## Support & Dokumentasi

- Dokumentasi lengkap: `README.md`
- Panduan instalasi detail: `INSTALL.md`
- Issues: GitHub repository issues

## Production Checklist

Sebelum deploy ke production:
- [ ] Ganti SECRET_KEY di .env
- [ ] Setup PostgreSQL/MySQL
- [ ] Ganti password admin
- [ ] Setup HTTPS
- [ ] Konfigurasi backup otomatis
- [ ] Setup monitoring
- [ ] Gunakan Gunicorn + Nginx
- [ ] Set FLASK_ENV=production

Untuk detail production deployment, lihat `INSTALL.md`.
