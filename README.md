# WebSPK - Sistem Informasi Keanggotaan Serikat Pekerja Kampus

Portal web lengkap untuk pengelolaan keanggotaan serikat pekerja kampus yang mencakup registrasi, pengelolaan anggota, penerbitan kartu, forum komunikasi, survei, dan statistik.

## Fitur Utama

### 1. Manajemen Keanggotaan
- **Registrasi Anggota**: Portal pendaftaran online untuk calon anggota
- **Pengelolaan Data**: CRUD (Create, Read, Update, Delete) untuk data anggota
- **Persetujuan Keanggotaan**: Workflow approval untuk pendaftaran baru
- **Penerbitan Nomor Anggota**: Sistem otomatis untuk generate nomor anggota unik
- **Status Keanggotaan**: Tracking status (Pending, Aktif, Diberhentikan)

### 2. Kartu Anggota
- **Penerbitan Kartu**: Generate kartu anggota otomatis dengan QR code
- **Download Kartu**: Anggota dapat mengunduh kartu digital mereka
- **Validasi QR Code**: Setiap kartu dilengkapi QR code untuk verifikasi

### 3. Forum Komunikasi
- **Diskusi Anggota**: Platform untuk komunikasi antar anggota
- **Posting & Balasan**: Buat topik diskusi dan berikan tanggapan
- **Moderasi**: Admin dapat menghapus konten yang tidak sesuai

### 4. Sistem Survei
- **Buat Survei**: Admin dapat membuat survei dengan berbagai tipe pertanyaan
- **Isi Survei**: Anggota dapat mengisi survei yang tersedia
- **Analisis Hasil**: Dashboard hasil survei dengan statistik dan visualisasi
- **Tipe Pertanyaan**: Text, Multiple Choice, Rating

### 5. Statistik & Reporting
- **Dashboard Admin**: Ringkasan statistik keanggotaan
- **Statistik Departemen**: Distribusi anggota per departemen
- **Statistik Status**: Jumlah anggota berdasarkan status
- **Data Real-time**: Semua data diperbarui secara real-time

## Teknologi

- **Backend**: Flask (Python)
- **Database**: SQLAlchemy (SQLite/PostgreSQL)
- **Authentication**: Flask-Login
- **Frontend**: Bootstrap 5 + Bootstrap Icons
- **Image Processing**: Pillow (PIL)
- **QR Code**: qrcode library
- **PDF Generation**: ReportLab

## Instalasi

### Prasyarat
- Python 3.8+
- pip
- virtualenv (opsional tapi disarankan)

### Langkah Instalasi

1. Clone repository:
```bash
git clone https://github.com/yoezron/webspk.git
cd webspk
```

2. Buat dan aktifkan virtual environment:
```bash
python -m venv venv
source venv/bin/activate  # Linux/Mac
# atau
venv\Scripts\activate  # Windows
```

3. Install dependencies:
```bash
pip install -r requirements.txt
```

4. Setup environment variables:
```bash
cp .env.example .env
# Edit .env dengan konfigurasi Anda
```

5. Inisialisasi database:
```bash
python -c "from app import create_app, db; app = create_app(); app.app_context().push(); db.create_all()"
```

6. (Opsional) Buat admin user:
```bash
python -c "from app import create_app, db; from app.models import User; app = create_app(); app.app_context().push(); u = User(username='admin', email='admin@spk.com', is_admin=True); u.set_password('admin123'); db.session.add(u); db.session.commit(); print('Admin created!')"
```

## Menjalankan Aplikasi

### Development Mode
```bash
python run.py
```

Aplikasi akan berjalan di `http://localhost:5000`

### Production Mode
Gunakan server WSGI seperti Gunicorn:
```bash
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:8000 wsgi:app
```

## Struktur Proyek

```
webspk/
├── app/
│   ├── __init__.py          # Factory aplikasi Flask
│   ├── models.py            # Model database
│   ├── utils.py             # Utility functions
│   ├── routes/              # Blueprint routes
│   │   ├── __init__.py
│   │   ├── main.py         # Route utama
│   │   ├── auth.py         # Authentication
│   │   ├── members.py      # Manajemen anggota
│   │   ├── admin.py        # Panel admin
│   │   ├── forum.py        # Forum komunikasi
│   │   └── survey.py       # Sistem survei
│   ├── templates/           # Template HTML
│   │   ├── base.html
│   │   ├── index.html
│   │   ├── auth/
│   │   ├── members/
│   │   ├── admin/
│   │   ├── forum/
│   │   └── survey/
│   └── static/              # File statis (CSS, JS, images)
├── uploads/                 # Direktori upload
├── static/cards/           # Kartu anggota yang digenerate
├── requirements.txt         # Python dependencies
├── .env.example            # Template environment variables
├── .gitignore
├── run.py                  # Development server
├── wsgi.py                 # Production WSGI
└── README.md
```

## Penggunaan

### Untuk Anggota

1. **Registrasi**: 
   - Klik "Daftar" di halaman utama
   - Isi form registrasi dengan username, email, dan password
   - Login dengan akun yang sudah dibuat

2. **Pendaftaran Keanggotaan**:
   - Setelah login, isi formulir pendaftaran anggota
   - Tunggu persetujuan dari admin
   - Setelah disetujui, Anda akan mendapat nomor anggota

3. **Download Kartu**:
   - Setelah kartu diterbitkan, buka profil Anda
   - Klik tombol "Download Kartu"

4. **Forum**:
   - Akses menu Forum
   - Buat post baru atau balas post yang ada

5. **Survei**:
   - Akses menu Survei
   - Isi survei yang tersedia

### Untuk Admin

1. **Persetujuan Anggota**:
   - Akses Admin Dashboard
   - Lihat daftar pendaftaran pending
   - Approve atau reject pendaftaran

2. **Penerbitan Kartu**:
   - Di daftar anggota, klik "Terbitkan Kartu" untuk anggota aktif
   - Kartu akan digenerate otomatis

3. **Buat Survei**:
   - Akses menu Survei
   - Klik "Buat Survei Baru"
   - Tambahkan pertanyaan sesuai kebutuhan

4. **Lihat Statistik**:
   - Akses Admin Dashboard
   - Klik "Lihat Statistik Detail"

## Konfigurasi

### Environment Variables (.env)

```
SECRET_KEY=your-secret-key-here
DATABASE_URL=sqlite:///webspk.db
FLASK_ENV=development
FLASK_DEBUG=True
```

### Production Settings

Untuk production, pastikan:
- Ganti `SECRET_KEY` dengan nilai yang aman
- Gunakan database production (PostgreSQL/MySQL)
- Set `FLASK_ENV=production`
- Set `FLASK_DEBUG=False`
- Gunakan HTTPS
- Setup proper logging

## Keamanan

- Password di-hash menggunakan Werkzeug security
- Session management dengan Flask-Login
- CSRF protection dengan Flask-WTF
- Input validation dan sanitization
- Role-based access control (Admin/Member)

## Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Project ini dibuat untuk keperluan Serikat Pekerja Kampus.

## Support

Untuk pertanyaan atau issue, silakan buka issue di GitHub repository.
