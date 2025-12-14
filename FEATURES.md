# WebSPK - Daftar Fitur Lengkap

## 1. Portal Registrasi Keanggotaan

### Registrasi User
- Form registrasi dengan validasi
- Username, email, dan password
- Hash password menggunakan Werkzeug security
- Email validation

### Registrasi Anggota
- Form lengkap untuk data anggota:
  - Nama lengkap
  - Nomor identitas (KTP/NIP)
  - Nomor telepon
  - Alamat
  - Departemen/Unit kerja
  - Jabatan/Posisi
- Status tracking: pending → active → terminated
- Timestamp otomatis untuk join_date

## 2. Pengelolaan Keanggotaan

### CRUD Operations
- **Create**: Pendaftaran anggota baru
- **Read**: Lihat profil dan daftar anggota
- **Update**: Admin dapat mengubah status anggota
- **Delete**: Pemberhentian keanggotaan (soft delete dengan status 'terminated')

### Admin Functions
- Persetujuan pendaftaran (Approve/Reject)
- Pemberhentian keanggotaan
- Filter anggota berdasarkan status
- Pencarian dan manajemen data anggota

### Member Status
- **Pending**: Menunggu persetujuan admin
- **Active**: Anggota aktif dengan nomor anggota
- **Terminated**: Keanggotaan diberhentikan
- **Rejected**: Pendaftaran ditolak

## 3. Penerbitan Nomor Anggota

### Automatic Generation
- Format: SPK-YYYYMMDDHHMMSS
- Unique untuk setiap anggota
- Diterbitkan saat approval
- Tersimpan permanent di database

### Display
- Ditampilkan di profil anggota
- Tercantum di kartu anggota
- Dapat dicari oleh admin

## 4. Penerbitan Kartu Anggota

### Card Generation
- Generate otomatis menggunakan Pillow (PIL)
- Ukuran: 800x500 pixel
- Design profesional dengan border dan header

### Card Content
- Header: "KARTU ANGGOTA - Serikat Pekerja Kampus"
- Nomor anggota
- Nama lengkap
- Departemen
- Jabatan
- Tanggal bergabung
- QR Code untuk verifikasi

### QR Code
- Generate menggunakan library qrcode
- Berisi nomor anggota
- Dapat di-scan untuk verifikasi
- Ukuran 120x120 pixel

### Download
- Format: PNG
- Simpan di folder static/cards/
- Member dapat download kartu sendiri
- Admin dapat melihat semua kartu

## 5. Penerimaan Anggota

### Approval Workflow
1. User mendaftar akun
2. User mengisi form keanggotaan
3. Status set ke 'pending'
4. Admin review pendaftaran
5. Admin approve/reject
6. Jika approve: generate nomor anggota
7. Status berubah menjadi 'active'

### Admin Dashboard
- Counter pending registrations
- Notification untuk pendaftaran baru
- Quick action buttons (Approve/Reject)
- Bulk operations support

## 6. Pemberhentian Anggota

### Termination Process
- Admin dapat menghentikan keanggotaan
- Status berubah ke 'terminated'
- Data tetap tersimpan (soft delete)
- Timestamp updated_at diperbarui

### History Tracking
- Semua perubahan status tercatat
- Created_at dan updated_at timestamp
- Audit trail untuk compliance

## 7. Forum Komunikasi Anggota

### Forum Posts
- **Create**: Buat topik diskusi baru
- **Title & Content**: Judul dan isi post
- **Author**: Username yang membuat post
- **Timestamp**: Waktu posting
- **Delete**: Hapus post (author atau admin)

### Replies
- **Reply to posts**: Balas diskusi
- **Nested discussions**: Thread-based replies
- **Author tracking**: Siapa yang membalas
- **Timestamp**: Waktu reply

### Features
- View all posts dengan preview
- View detail post dengan semua replies
- Edit post (future enhancement)
- Moderasi konten oleh admin

## 8. Tempat Survei Anggota

### Create Survey (Admin)
- Judul dan deskripsi survei
- Multiple questions support
- Dynamic question addition
- Question ordering

### Question Types
1. **Text**: Jawaban bebas text area
2. **Multiple Choice**: Pilihan ganda
3. **Rating**: Skala 1-5

### Fill Survey (Member)
- List survei aktif
- Form pengisian dinamis
- One-time submission per user
- Validation required fields

### View Results (Admin)
- Total responden
- Summary per pertanyaan
- Visualisasi dengan progress bar
- Percentage calculation
- Export capability (future)

## 9. Pengumpulan dan Pengolahan Data Statistik

### Member Statistics
- Total anggota
- Pending approval count
- Active members count
- Terminated members count

### Department Distribution
- Jumlah anggota per departemen
- Grafik distribusi
- Query optimization dengan GROUP BY

### Status Distribution
- Breakdown berdasarkan status
- Real-time updates
- Historical tracking

### Survey Analytics
- Total responses per survey
- Answer distribution
- Rating averages
- Text response compilation

## 10. Informasi yang Relevan bagi Anggota

### Dashboard Member
- Status keanggotaan
- Nomor anggota
- Link ke profil
- Link ke forum
- Link ke survei
- Informasi penting

### Dashboard Admin
- Statistik overview
- Quick actions
- Recent activities
- System health

### Notifications (Future)
- Pendaftaran disetujui
- Kartu telah diterbitkan
- Survei baru
- Forum mentions

## Fitur Keamanan

### Authentication
- Session-based login dengan Flask-Login
- Secure password hashing (Werkzeug)
- Login required decorators
- Logout functionality

### Authorization
- Role-based access control (Admin/Member)
- admin_required decorator
- User-specific data access
- Permission checks

### Data Protection
- SQL injection prevention (SQLAlchemy ORM)
- XSS protection (Jinja2 auto-escaping)
- CSRF protection (Flask-WTF)
- Secure session cookies

### Input Validation
- Form validation
- Required field checks
- Email validation
- Type checking

## Fitur UI/UX

### Responsive Design
- Bootstrap 5
- Mobile-friendly
- Adaptive layout
- Touch-friendly buttons

### Icons
- Bootstrap Icons
- Consistent iconography
- Visual hierarchy

### Notifications
- Flash messages
- Success/Error/Warning/Info
- Auto-dismissible alerts
- User feedback

### Navigation
- Top navbar with dropdowns
- Breadcrumb navigation
- Quick links
- User menu

## Database Schema

### Tables
1. **user**: User accounts (auth)
2. **member**: Member profiles
3. **forum_post**: Forum discussions
4. **forum_reply**: Forum replies
5. **survey**: Survey definitions
6. **survey_question**: Survey questions
7. **survey_response**: Survey answers

### Relationships
- One-to-One: User ↔ Member
- One-to-Many: User → ForumPost
- One-to-Many: User → ForumReply
- One-to-Many: ForumPost → ForumReply
- One-to-Many: Survey → SurveyQuestion
- One-to-Many: Survey → SurveyResponse
- One-to-Many: User → SurveyResponse

## API Endpoints

### Public Routes
- `/` - Landing page
- `/auth/login` - Login
- `/auth/register` - Register

### Member Routes (Login Required)
- `/dashboard` - Member dashboard
- `/members/register` - Register membership
- `/members/profile` - View profile
- `/members/card/<id>` - Download card
- `/forum/` - Forum index
- `/forum/post/<id>` - View post
- `/forum/create` - Create post
- `/forum/post/<id>/reply` - Reply to post
- `/survey/` - Survey list
- `/survey/<id>` - View survey
- `/survey/<id>/submit` - Submit survey

### Admin Routes (Admin Only)
- `/admin/dashboard` - Admin dashboard
- `/admin/member/<id>/approve` - Approve member
- `/admin/member/<id>/reject` - Reject member
- `/admin/member/<id>/terminate` - Terminate member
- `/admin/member/<id>/issue-card` - Issue card
- `/admin/statistics` - View statistics
- `/members/list` - List all members
- `/survey/create` - Create survey
- `/survey/<id>/results` - View results

## Deployment Options

### Development
- Built-in Flask server
- SQLite database
- Debug mode
- Hot reload

### Production
- Gunicorn WSGI server
- PostgreSQL/MySQL database
- Nginx reverse proxy
- SSL/HTTPS
- Systemd service
- Backup automation

## Future Enhancements

### Phase 2
- [ ] Email notifications
- [ ] File upload (photo profile)
- [ ] Advanced search
- [ ] Export data (CSV/PDF)
- [ ] Batch operations
- [ ] Activity logs

### Phase 3
- [ ] REST API
- [ ] Mobile app
- [ ] Real-time notifications (WebSocket)
- [ ] Document management
- [ ] Event calendar
- [ ] Payment integration

### Phase 4
- [ ] Multi-language support
- [ ] Advanced analytics
- [ ] Machine learning insights
- [ ] Integration with external systems
- [ ] Custom workflows
- [ ] Reporting dashboard

## Support

Untuk pertanyaan atau masalah, silakan:
- Buka issue di GitHub
- Hubungi admin sistem
- Lihat dokumentasi lengkap di README.md
