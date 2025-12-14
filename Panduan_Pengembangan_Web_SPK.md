# PANDUAN PENGEMBANGAN WEB SERIKAT PEKERJA KAMPUS (SPK)

**Framework:** CodeIgniter 4  
**CSS:** Tailwind CSS  
**Template:** Edura  
**Database:** MySQL 8.0+  
**Versi Dokumen:** 1.0  
**Tanggal:** Desember 2024

---

## DAFTAR ISI

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Sprint Planning & Timeline](#2-sprint-planning--timeline)
3. [Struktur Database](#3-struktur-database)
4. [Arsitektur Sistem](#4-arsitektur-sistem)
5. [RBAC (Role-Based Access Control)](#5-rbac-role-based-access-control)
6. [Alur Registrasi & Onboarding](#6-alur-registrasi--onboarding)
7. [Modul CMS](#7-modul-cms)
8. [Modul Keuangan (Iuran)](#8-modul-keuangan-iuran)
9. [API Endpoints](#9-api-endpoints)
10. [Keamanan & Best Practices](#10-keamanan--best-practices)

---

## 1. RINGKASAN EKSEKUTIF

### 1.1 Latar Belakang

Web dan Sistem Informasi Keanggotaan Serikat Pekerja Kampus (SPK) adalah portal terintegrasi untuk:
- Registrasi dan pengelolaan keanggotaan
- Penerbitan nomor dan kartu anggota
- Manajemen iuran bulanan
- Forum komunikasi anggota
- Survei anggota
- Publikasi informasi organisasi

**Data Eksisting:** 1.700+ anggota yang akan dimigrasikan ke sistem baru.

### 1.2 Tujuan Proyek

| No | Tujuan | Prioritas |
|----|--------|-----------|
| 1 | Portal registrasi dan manajemen keanggotaan modern | Critical |
| 2 | Sistem RBAC dengan 6 role berbeda | Critical |
| 3 | Manajemen iuran dengan verifikasi pembayaran | Critical |
| 4 | CMS untuk konten publik dan internal | High |
| 5 | Forum dan survei anggota | Medium |
| 6 | Statistik dan laporan | Medium |

### 1.3 Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework Backend | CodeIgniter 4 |
| CSS Framework | Tailwind CSS |
| Template | Edura |
| Database | MySQL 8.0+ |
| PHP Version | PHP 8.1+ |
| Web Server | Apache/Nginx |
| Email Service | SMTP/Mailgun |

---

## 2. SPRINT PLANNING & TIMELINE

### 2.1 Overview Timeline

**Total Durasi:** 16 minggu (8 Sprint × 2 minggu)

| Sprint | Minggu | Fokus | Story Points |
|--------|--------|-------|--------------|
| Sprint 1 | 1-2 | Foundation & Setup | 29 |
| Sprint 2 | 3-4 | RBAC & User Management | 39 |
| Sprint 3 | 5-6 | Registration & Onboarding | 45 |
| Sprint 4 | 7-8 | CMS & Public Pages | 55 |
| Sprint 5 | 9-10 | Dues & Financial Module | 55 |
| Sprint 6 | 11-12 | Communication & Survey | 52 |
| Sprint 7 | 13-14 | Admin Dashboard & Reports | 46 |
| Sprint 8 | 15-16 | Testing & Deployment | 60 |

**Total Story Points:** 381

---

### 2.2 Sprint 1: Foundation & Setup (Minggu 1-2)

**Tujuan:** Membangun fondasi teknis proyek

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S1-01 | Setup CI4 project dengan struktur MVC | Critical | 3 |
| S1-02 | Konfigurasi database MySQL dan migrations | Critical | 5 |
| S1-03 | Integrasi Tailwind CSS dengan template Edura | High | 3 |
| S1-04 | Implementasi autentikasi (login/logout) | Critical | 5 |
| S1-05 | Buat migration tabel sp_members | Critical | 5 |
| S1-06 | Password hashing & security | Critical | 3 |
| S1-07 | Setup environment variables | High | 2 |
| S1-08 | Basic session management | High | 3 |

**Deliverables:**
- Repository dengan struktur CI4
- Database schema tabel sp_members
- Halaman login/logout fungsional
- Template dasar dengan Tailwind CSS

---

### 2.3 Sprint 2: RBAC & User Management (Minggu 3-4)

**Tujuan:** Sistem Role-Based Access Control lengkap

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S2-01 | Migration tabel rbac_roles | Critical | 3 |
| S2-02 | Migration tabel rbac_permissions | Critical | 3 |
| S2-03 | Migration tabel rbac_role_permissions | Critical | 2 |
| S2-04 | Middleware role checking | Critical | 5 |
| S2-05 | Middleware permission checking | Critical | 5 |
| S2-06 | UI Role Management (Super Admin) | High | 5 |
| S2-07 | Regional scope untuk Pengurus Wilayah | High | 5 |
| S2-08 | Seeder default roles & permissions | High | 3 |
| S2-09 | Migration rbac_menus & submenus | High | 3 |
| S2-10 | Dynamic menu berdasarkan role | High | 5 |

**Deliverables:**
- 6 role dengan permission lengkap
- Middleware RBAC terintegrasi
- Menu dinamis per role

---

### 2.4 Sprint 3: Registration & Onboarding (Minggu 5-6)

**Tujuan:** Alur registrasi dengan verifikasi

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S3-01 | Halaman registrasi multi-step | Critical | 8 |
| S3-02 | Kalkulasi iuran (golongan/gaji) | Critical | 5 |
| S3-03 | QR Code & info pembayaran | High | 3 |
| S3-04 | Upload bukti pembayaran | Critical | 5 |
| S3-05 | Verifikasi email | Critical | 5 |
| S3-06 | Halaman calon anggota (limited) | High | 5 |
| S3-07 | Approval calon anggota (Admin) | Critical | 5 |
| S3-08 | Penolakan & pembatalan | High | 3 |
| S3-09 | Generate nomor anggota | High | 3 |
| S3-10 | Migration sp_dues_bills & payments | Critical | 3 |

**Deliverables:**
- Form registrasi lengkap
- Sistem verifikasi email
- Proses approval admin

---

### 2.5 Sprint 4: CMS & Public Pages (Minggu 7-8)

**Tujuan:** Content Management System dan halaman publik

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S4-01 | Migration tabel CMS | Critical | 5 |
| S4-02 | Landing Page dinamis | Critical | 8 |
| S4-03 | Halaman statis (sejarah, manifesto, dll) | High | 5 |
| S4-04 | Halaman struktur pengurus | High | 5 |
| S4-05 | Halaman publikasi (PDF download) | High | 5 |
| S4-06 | Halaman regulasi (PDF download) | High | 5 |
| S4-07 | News/Blog CRUD | High | 8 |
| S4-08 | Halaman kontak + form | Medium | 3 |
| S4-09 | Subscribe newsletter | Medium | 3 |
| S4-10 | Admin Panel CMS | Critical | 8 |

**Deliverables:**
- 10 halaman publik
- Admin panel CMS
- Landing page builder

---

### 2.6 Sprint 5: Dues & Financial Module (Minggu 9-10)

**Tujuan:** Modul keuangan dan iuran

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S5-01 | Migration sp_dues_rates | Critical | 3 |
| S5-02 | Master Tarif Iuran CRUD | Critical | 5 |
| S5-03 | Generate tagihan bulanan otomatis | Critical | 8 |
| S5-04 | UI verifikasi pembayaran (Bendahara) | Critical | 8 |
| S5-05 | Sistem klaim iuran | High | 5 |
| S5-06 | Auto-flag tunggakan (>3 bulan) | High | 5 |
| S5-07 | Notifikasi reminder pembayaran | High | 5 |
| S5-08 | Dashboard Iuran | High | 8 |
| S5-09 | Export laporan keuangan | Medium | 5 |
| S5-10 | Cron job billing otomatis | High | 3 |

**Deliverables:**
- Sistem billing otomatis
- Dashboard keuangan
- Reminder pembayaran

---

### 2.7 Sprint 6: Communication & Survey (Minggu 11-12)

**Tujuan:** Forum, pesan, dan survei

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S6-01 | Migration tabel forum | Critical | 3 |
| S6-02 | Forum Diskusi Anggota | Critical | 8 |
| S6-03 | Moderasi forum | High | 5 |
| S6-04 | Migration tabel messages | High | 3 |
| S6-05 | Sistem pesan ke pengurus | High | 5 |
| S6-06 | Migration tabel survei | Critical | 5 |
| S6-07 | CRUD Survei (Admin) | Critical | 8 |
| S6-08 | Pengisian survei (Anggota) | Critical | 5 |
| S6-09 | Hasil & rekap survei | High | 5 |
| S6-10 | Notifikasi broadcast | High | 5 |

**Deliverables:**
- Forum diskusi
- Sistem survei
- Notifikasi broadcast

---

### 2.8 Sprint 7: Admin Dashboard & Reports (Minggu 13-14)

**Tujuan:** Dashboard dan laporan

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S7-01 | Dashboard Super Admin | Critical | 8 |
| S7-02 | Grafik demografi anggota | High | 5 |
| S7-03 | Bulk upload anggota (Excel) | Critical | 8 |
| S7-04 | Bulk upload master data | High | 5 |
| S7-05 | Export data anggota | High | 5 |
| S7-06 | Audit Log viewer | High | 5 |
| S7-07 | Dashboard Pengurus Wilayah | High | 5 |
| S7-08 | Kartu Anggota digital | High | 5 |

**Deliverables:**
- Dashboard lengkap
- Bulk import/export
- Kartu anggota digital

---

### 2.9 Sprint 8: Testing & Deployment (Minggu 15-16)

**Tujuan:** Testing dan go-live

| ID | User Story | Priority | Points |
|----|------------|----------|--------|
| S8-01 | Unit testing semua Model | Critical | 8 |
| S8-02 | Integration testing | Critical | 8 |
| S8-03 | User Acceptance Testing | Critical | 8 |
| S8-04 | Security audit | Critical | 5 |
| S8-05 | Performance optimization | High | 5 |
| S8-06 | Bug fixing & polishing | Critical | 8 |
| S8-07 | Setup production server | Critical | 5 |
| S8-08 | Deployment & go-live | Critical | 5 |
| S8-09 | Dokumentasi | High | 5 |
| S8-10 | Training tim | High | 3 |

**Deliverables:**
- Aplikasi production-ready
- Dokumentasi lengkap
- Tim terlatih

---

## 3. STRUKTUR DATABASE

### 3.1 Overview Entity Relationship

Database terdiri dari modul-modul berikut:

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    MEMBER       │     │      RBAC       │     │      CMS        │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ sp_members      │     │ rbac_roles      │     │ cms_pages       │
│ sp_member_docs  │     │ rbac_permissions│     │ cms_home_sections│
│ sp_email_verify │     │ rbac_role_perms │     │ cms_documents   │
│ sp_audit_logs   │     │ rbac_menus      │     │ cms_news_posts  │
└─────────────────┘     │ rbac_submenus   │     │ cms_officers    │
                        └─────────────────┘     │ cms_subscribers │
┌─────────────────┐                             │ cms_contact_msgs│
│     DUES        │     ┌─────────────────┐     └─────────────────┘
├─────────────────┤     │  COMMUNICATION  │
│ sp_dues_rates   │     ├─────────────────┤
│ sp_dues_bills   │     │ forum_threads   │
│ sp_dues_payments│     │ forum_posts     │
│ sp_dues_claims  │     │ messages        │
└─────────────────┘     │ surveys         │
                        │ survey_responses│
                        └─────────────────┘
```

---

### 3.2 Tabel sp_members (Anggota)

```sql
CREATE TABLE sp_members (
    -- IDENTITAS SISTEM
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    member_number VARCHAR(50) NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    
    -- STATUS & ROLE
    role ENUM('super_admin','admin','coordinator','treasurer','member','candidate') 
        NOT NULL DEFAULT 'candidate',
    membership_status ENUM('candidate','active','inactive','disabled','rejected') 
        NOT NULL DEFAULT 'candidate',
    onboarding_state ENUM('registered','payment_submitted','email_verified','approved','rejected') 
        NOT NULL DEFAULT 'registered',
    account_status ENUM('pending','active','suspended','rejected') 
        NOT NULL DEFAULT 'pending',
    
    -- VERIFIKASI
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    failed_login_attempts TINYINT UNSIGNED DEFAULT 0,
    locked_until DATETIME NULL,
    password_changed_at DATETIME NULL,
    reset_token_hash VARCHAR(255) NULL,
    reset_token_expires_at DATETIME NULL,
    
    -- DATA DEMOGRAFIS
    full_name VARCHAR(150) NOT NULL,
    gender ENUM('L','P') NULL,
    birth_place VARCHAR(100) NULL,
    birth_date DATE NULL,
    identity_number VARCHAR(50) NULL,
    phone_number VARCHAR(20) NULL,
    alt_phone_number VARCHAR(20) NULL,
    address TEXT NULL,
    province VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    district VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    region_code VARCHAR(10) NULL,
    
    -- KONTAK DARURAT
    emergency_contact_name VARCHAR(150) NULL,
    emergency_contact_relation VARCHAR(50) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    
    -- DATA PROFESI
    university_name VARCHAR(150) NOT NULL,
    campus_location VARCHAR(150) NULL,
    faculty VARCHAR(150) NULL,
    department VARCHAR(100) NULL,
    work_unit VARCHAR(100) NULL,
    employee_id_number VARCHAR(50) NULL,
    lecturer_id_number VARCHAR(50) NULL,
    academic_rank ENUM('Tenaga Pengajar','Asisten Ahli','Lektor','Lektor Kepala',
                       'Guru Besar','Tendik/Staff','Lainnya') DEFAULT 'Lainnya',
    employment_status ENUM('PNS','PPPK','Tetap Non-PNS','Kontrak/PKWT',
                          'Dosen Luar Biasa','Honorer','Lainnya') DEFAULT 'Lainnya',
    employment_start_date DATE NULL,
    contract_end_date DATE NULL,
    
    -- DATA EKONOMI
    payroll_source VARCHAR(100) NULL,
    salary_range VARCHAR(50) NULL,
    base_salary DECIMAL(15,2) NULL,
    take_home_pay DECIMAL(15,2) NULL,
    consent_sensitive_data TINYINT(1) DEFAULT 0,
    
    -- IURAN
    dues_rate_id INT UNSIGNED NULL,
    dues_method ENUM('fixed','percentage','manual') DEFAULT 'manual',
    dues_amount DECIMAL(15,2) NULL,
    dues_status ENUM('unpaid','paid','overdue','waived') DEFAULT 'unpaid',
    dues_last_paid_at DATE NULL,
    
    -- ADVOKASI
    expertise TEXT NULL,
    motivation TEXT NULL,
    advocacy_interests TEXT NULL,
    is_volunteer TINYINT(1) DEFAULT 0,
    branch_name VARCHAR(150) NULL,
    union_position VARCHAR(100) NULL,
    membership_start_date DATE NULL,
    membership_end_date DATE NULL,
    
    -- DOKUMEN & VERIFIKASI
    id_proof_file VARCHAR(255) NULL,
    employee_card_file VARCHAR(255) NULL,
    profile_photo_file VARCHAR(255) NULL,
    verification_status ENUM('unverified','under_review','verified','rejected') 
        DEFAULT 'unverified',
    verified_by INT UNSIGNED NULL,
    verified_at DATETIME NULL,
    rejection_reason TEXT NULL,
    
    -- PERSETUJUAN
    agreed_to_terms_at DATETIME NULL,
    agreed_to_privacy_at DATETIME NULL,
    admin_notes TEXT NULL,
    
    -- TIMESTAMPS
    joined_at DATE NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    -- INDEXES
    INDEX idx_membership_status (membership_status),
    INDEX idx_role (role),
    INDEX idx_region_code (region_code),
    INDEX idx_university (university_name),
    INDEX idx_onboarding_state (onboarding_state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 3.3 Tabel RBAC

#### rbac_roles
```sql
CREATE TABLE rbac_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NULL,
    description TEXT NULL,
    level TINYINT UNSIGNED NOT NULL DEFAULT 5,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

#### rbac_permissions
```sql
CREATE TABLE rbac_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(150) NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_module (module)
) ENGINE=InnoDB;
```

#### rbac_role_permissions
```sql
CREATE TABLE rbac_role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES rbac_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES rbac_permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### rbac_menus
```sql
CREATE TABLE rbac_menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NULL,
    url VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_public TINYINT(1) DEFAULT 0,
    permission_logic ENUM('ANY','ALL') DEFAULT 'ANY',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

#### rbac_submenus
```sql
CREATE TABLE rbac_submenus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    permission_logic ENUM('ANY','ALL') DEFAULT 'ANY',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (menu_id) REFERENCES rbac_menus(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### rbac_menu_permissions
```sql
CREATE TABLE rbac_menu_permissions (
    menu_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (menu_id, permission_id),
    FOREIGN KEY (menu_id) REFERENCES rbac_menus(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES rbac_permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### 3.4 Tabel CMS

#### cms_pages
```sql
CREATE TABLE cms_pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    content_html LONGTEXT NULL,
    template ENUM('default','legal','contact') DEFAULT 'default',
    status ENUM('draft','published','archived') DEFAULT 'draft',
    visibility ENUM('public','member_only') DEFAULT 'public',
    primary_document_id INT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status_published (status, published_at)
) ENGINE=InnoDB;
```

#### cms_page_revisions
```sql
CREATE TABLE cms_page_revisions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    content_html LONGTEXT NOT NULL,
    note TEXT NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (page_id) REFERENCES cms_pages(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### cms_home_sections
```sql
CREATE TABLE cms_home_sections (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(200) NULL,
    body_html TEXT NULL,
    config_json JSON NULL,
    sort_order INT DEFAULT 0,
    is_enabled TINYINT(1) DEFAULT 1,
    updated_by INT UNSIGNED NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

#### cms_documents
```sql
CREATE TABLE cms_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doc_type ENUM('publikasi','regulasi') NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NULL UNIQUE,
    description TEXT NULL,
    category_id INT UNSIGNED NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL DEFAULT 'application/pdf',
    file_size INT UNSIGNED NOT NULL,
    checksum_sha256 CHAR(64) NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    published_at DATETIME NULL,
    download_count INT UNSIGNED DEFAULT 0,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_doc_type_status (doc_type, status, published_at)
) ENGINE=InnoDB;
```

#### cms_document_categories
```sql
CREATE TABLE cms_document_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doc_type ENUM('publikasi','regulasi') NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    
    UNIQUE KEY unique_type_slug (doc_type, slug)
) ENGINE=InnoDB;
```

#### cms_news_posts
```sql
CREATE TABLE cms_news_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content_html LONGTEXT NOT NULL,
    cover_image_id INT UNSIGNED NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    published_at DATETIME NULL,
    author_id INT UNSIGNED NOT NULL,
    view_count INT UNSIGNED DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status_published (status, published_at),
    INDEX idx_author (author_id)
) ENGINE=InnoDB;
```

#### cms_media
```sql
CREATE TABLE cms_media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_type ENUM('image') DEFAULT 'image',
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    checksum_sha256 CHAR(64) NULL,
    alt_text VARCHAR(255) NULL,
    uploaded_by INT UNSIGNED NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

#### cms_officers
```sql
CREATE TABLE cms_officers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NULL,
    full_name VARCHAR(150) NOT NULL,
    position_title VARCHAR(100) NOT NULL,
    level ENUM('pusat','wilayah') DEFAULT 'pusat',
    region_code VARCHAR(10) NULL,
    photo_media_id INT UNSIGNED NULL,
    bio_html TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    period_start DATE NULL,
    period_end DATE NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_level_active (level, is_active, sort_order)
) ENGINE=InnoDB;
```

#### cms_subscribers
```sql
CREATE TABLE cms_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    status ENUM('pending','active','unsubscribed') DEFAULT 'pending',
    token_hash CHAR(64) NULL,
    verified_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

#### cms_contact_messages
```sql
CREATE TABLE cms_contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    status ENUM('new','in_progress','closed') DEFAULT 'new',
    assigned_to INT UNSIGNED NULL,
    admin_reply TEXT NULL,
    replied_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

---

### 3.5 Tabel Keuangan (Dues)

#### sp_dues_rates
```sql
CREATE TABLE sp_dues_rates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    scheme_type ENUM('golongan','gaji') NOT NULL,
    label VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    amount DECIMAL(15,2) NOT NULL,
    min_salary DECIMAL(15,2) NULL,
    max_salary DECIMAL(15,2) NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_scheme_active (scheme_type, is_active, sort_order)
) ENGINE=InnoDB;
```

**Data Seeder sp_dues_rates:**
```sql
INSERT INTO sp_dues_rates (scheme_type, label, code, amount, sort_order) VALUES
('golongan', 'Golongan I (Ia, Ib, Ic, Id)', 'GOL1', 20000, 1),
('golongan', 'Golongan II (IIa, IIb, IIc, IId)', 'GOL2', 30000, 2),
('golongan', 'Golongan III (IIIa, IIIb, IIIc, IIId)', 'GOL3', 35000, 3),
('golongan', 'Golongan IV (IVa, IVb, IVc, IVd, IVe)', 'GOL4', 45000, 4),
('gaji', 'Rp 0 - Rp 1.500.000', 'GAJI1', 7500, 5),
('gaji', 'Rp 1.500.000 - Rp 3.000.000', 'GAJI2', 15000, 6),
('gaji', 'Rp 3.000.001 - Rp 6.000.000', 'GAJI3', 30000, 7),
('gaji', 'Diatas Rp 6.000.000', 'GAJI4', 60000, 8);
```

#### sp_dues_bills
```sql
CREATE TABLE sp_dues_bills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    bill_type ENUM('registration','monthly') NOT NULL,
    period_year SMALLINT NULL,
    period_month TINYINT NULL,
    rate_id INT UNSIGNED NULL,
    bill_amount DECIMAL(15,2) NOT NULL,
    bill_status ENUM('unpaid','paid','overdue','waived') DEFAULT 'unpaid',
    due_date DATE NULL,
    arrears_level TINYINT UNSIGNED DEFAULT 0,
    waived_reason TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_member_period (member_id, bill_type, period_year, period_month),
    INDEX idx_status (bill_status),
    INDEX idx_period (period_year, period_month),
    FOREIGN KEY (member_id) REFERENCES sp_members(id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES sp_dues_rates(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

#### sp_dues_payments
```sql
CREATE TABLE sp_dues_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bill_id INT UNSIGNED NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    paid_amount DECIMAL(15,2) NOT NULL,
    paid_at DATETIME NULL,
    payment_method VARCHAR(50) NULL,
    ref_no VARCHAR(100) NULL,
    proof_document_id INT UNSIGNED NULL,
    payment_status ENUM('submitted','verified','rejected') DEFAULT 'submitted',
    verified_by INT UNSIGNED NULL,
    verified_at DATETIME NULL,
    verification_note TEXT NULL,
    rejected_reason TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_payment_status (payment_status),
    INDEX idx_bill (bill_id),
    FOREIGN KEY (bill_id) REFERENCES sp_dues_bills(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES sp_members(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### sp_dues_claims
```sql
CREATE TABLE sp_dues_claims (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    bill_id INT UNSIGNED NULL,
    payment_id INT UNSIGNED NULL,
    claim_type ENUM('already_paid','wrong_amount','wrong_period',
                    'double_payment','waiver_request','other') NOT NULL,
    description TEXT NOT NULL,
    supporting_doc_id INT UNSIGNED NULL,
    status ENUM('submitted','in_review','approved','rejected') DEFAULT 'submitted',
    processed_by INT UNSIGNED NULL,
    processed_at DATETIME NULL,
    decision_note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    FOREIGN KEY (member_id) REFERENCES sp_members(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### 3.6 Tabel Pendukung

#### sp_member_documents
```sql
CREATE TABLE sp_member_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    doc_type ENUM('id_proof','employee_card','dues_payment_proof',
                  'profile_photo','membership_form','other') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    checksum_sha256 CHAR(64) NULL,
    review_status ENUM('not_reviewed','approved','rejected') DEFAULT 'not_reviewed',
    reviewer_id INT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    review_note TEXT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_member_type (member_id, doc_type),
    FOREIGN KEY (member_id) REFERENCES sp_members(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### sp_email_verifications
```sql
CREATE TABLE sp_email_verifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    email VARCHAR(150) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_token (token_hash),
    INDEX idx_member (member_id),
    FOREIGN KEY (member_id) REFERENCES sp_members(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

#### sp_audit_logs
```sql
CREATE TABLE sp_audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_id INT UNSIGNED NULL,
    actor_type ENUM('member','system','anonymous') DEFAULT 'member',
    target_type VARCHAR(50) NOT NULL,
    target_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_actor (actor_id),
    INDEX idx_target (target_type, target_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;
```

---

## 4. ARSITEKTUR SISTEM

### 4.1 Struktur Folder CodeIgniter 4

```
app/
├── Config/
│   ├── Routes.php
│   ├── Services.php
│   ├── Filters.php
│   └── Database.php
├── Controllers/
│   ├── Public/
│   │   ├── HomeController.php
│   │   ├── PageController.php
│   │   ├── NewsController.php
│   │   ├── DocumentController.php
│   │   └── ContactController.php
│   ├── Auth/
│   │   ├── LoginController.php
│   │   ├── RegisterController.php
│   │   ├── PasswordController.php
│   │   └── VerificationController.php
│   ├── Member/
│   │   ├── ProfileController.php
│   │   ├── DuesController.php
│   │   ├── ForumController.php
│   │   └── SurveyController.php
│   └── Admin/
│       ├── DashboardController.php
│       ├── MemberController.php
│       ├── CmsController.php
│       ├── DuesController.php
│       ├── RbacController.php
│       └── ReportController.php
├── Models/
│   ├── MemberModel.php
│   ├── RoleModel.php
│   ├── PermissionModel.php
│   ├── MenuModel.php
│   ├── CmsPageModel.php
│   ├── CmsNewsModel.php
│   ├── CmsDocumentModel.php
│   ├── DuesRateModel.php
│   ├── DuesBillModel.php
│   ├── DuesPaymentModel.php
│   └── AuditLogModel.php
├── Filters/
│   ├── AuthFilter.php
│   ├── RoleFilter.php
│   ├── PermissionFilter.php
│   ├── MembershipFilter.php
│   └── RegionScopeFilter.php
├── Services/
│   ├── MemberOnboardingService.php
│   ├── DuesService.php
│   ├── EmailService.php
│   └── AuditService.php
├── Libraries/
│   └── RBACLibrary.php
├── Views/
│   ├── layouts/
│   │   ├── public.php
│   │   ├── member.php
│   │   └── admin.php
│   ├── public/
│   ├── auth/
│   ├── member/
│   └── admin/
├── Database/
│   ├── Migrations/
│   └── Seeds/
└── Helpers/
    └── rbac_helper.php
```

### 4.2 Konfigurasi Filter (Middleware)

**app/Config/Filters.php:**
```php
public $aliases = [
    'auth'       => \App\Filters\AuthFilter::class,
    'role'       => \App\Filters\RoleFilter::class,
    'permission' => \App\Filters\PermissionFilter::class,
    'membership' => \App\Filters\MembershipFilter::class,
    'region'     => \App\Filters\RegionScopeFilter::class,
];
```

### 4.3 Konfigurasi Routes

**app/Config/Routes.php:**
```php
// Public Routes
$routes->get('/', 'Public\HomeController::index');
$routes->get('sejarah', 'Public\PageController::sejarah');
$routes->get('manifesto', 'Public\PageController::manifesto');
$routes->get('visimisi', 'Public\PageController::visimisi');
$routes->get('ad-art', 'Public\PageController::adart');
$routes->get('pengurus', 'Public\PageController::pengurus');
$routes->get('publikasi', 'Public\DocumentController::publikasi');
$routes->get('regulasi', 'Public\DocumentController::regulasi');
$routes->get('news', 'Public\NewsController::index');
$routes->get('news/(:segment)', 'Public\NewsController::show/$1');
$routes->get('contact', 'Public\ContactController::index');
$routes->post('contact', 'Public\ContactController::submit');
$routes->post('subscribe', 'Public\HomeController::subscribe');

// Auth Routes
$routes->get('login', 'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::authenticate');
$routes->get('logout', 'Auth\LoginController::logout');
$routes->get('register', 'Auth\RegisterController::index');
$routes->post('register', 'Auth\RegisterController::store');
$routes->get('verify-email', 'Auth\VerificationController::verify');
$routes->post('forgot-password', 'Auth\PasswordController::forgot');
$routes->post('reset-password', 'Auth\PasswordController::reset');

// Member Routes (requires auth + membership:active)
$routes->group('member', ['filter' => 'auth'], function($routes) {
    $routes->get('profile', 'Member\ProfileController::index');
    $routes->post('profile', 'Member\ProfileController::update');
    $routes->get('card', 'Member\ProfileController::card', ['filter' => 'membership:active']);
    $routes->get('dues', 'Member\DuesController::index');
    $routes->post('dues/upload', 'Member\DuesController::uploadProof');
});

// Member Routes (requires membership:active)
$routes->group('member', ['filter' => ['auth', 'membership:active']], function($routes) {
    $routes->get('forum', 'Member\ForumController::index');
    $routes->get('forum/(:num)', 'Member\ForumController::thread/$1');
    $routes->post('forum', 'Member\ForumController::createThread');
    $routes->post('forum/(:num)/reply', 'Member\ForumController::reply/$1');
    $routes->get('survey', 'Member\SurveyController::index');
    $routes->get('survey/(:num)', 'Member\SurveyController::show/$1');
    $routes->post('survey/(:num)', 'Member\SurveyController::submit/$1');
});

// Admin Routes
$routes->group('admin', ['filter' => ['auth', 'role:super_admin,admin,coordinator,treasurer']], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // Member Management
    $routes->get('members', 'Admin\MemberController::index');
    $routes->get('members/(:num)', 'Admin\MemberController::show/$1');
    $routes->get('candidates', 'Admin\MemberController::candidates');
    $routes->post('members/(:num)/approve', 'Admin\MemberController::approve/$1');
    $routes->post('members/(:num)/reject', 'Admin\MemberController::reject/$1');
    $routes->post('members/(:num)/disable', 'Admin\MemberController::disable/$1');
    
    // CMS
    $routes->resource('cms/pages', ['controller' => 'Admin\CmsPageController']);
    $routes->resource('cms/news', ['controller' => 'Admin\CmsNewsController']);
    $routes->resource('cms/documents', ['controller' => 'Admin\CmsDocumentController']);
    
    // Dues (Treasurer)
    $routes->get('dues', 'Admin\DuesController::index', ['filter' => 'role:treasurer,super_admin']);
    $routes->get('dues/payments', 'Admin\DuesController::payments');
    $routes->post('dues/payments/(:num)/verify', 'Admin\DuesController::verifyPayment/$1');
    $routes->post('dues/generate', 'Admin\DuesController::generateBills');
});

// Super Admin Only
$routes->group('admin', ['filter' => ['auth', 'role:super_admin']], function($routes) {
    $routes->resource('roles', ['controller' => 'Admin\RbacController']);
    $routes->get('audit-logs', 'Admin\AuditController::index');
    $routes->get('cms/landing', 'Admin\CmsLandingController::index');
    $routes->post('cms/landing', 'Admin\CmsLandingController::update');
    $routes->post('members/bulk-import', 'Admin\MemberController::bulkImport');
});
```

---

## 5. RBAC (ROLE-BASED ACCESS CONTROL)

### 5.1 Definisi Role

| No | Role | Level | Deskripsi |
|----|------|-------|-----------|
| 1 | Super Admin | 1 | Akses penuh + konfigurasi sistem |
| 2 | Admin (Pengurus) | 2 | Manajemen anggota, konten, survei |
| 3 | Coordinator | 3 | Admin terbatas pada wilayah |
| 4 | Treasurer | 3 | Fokus manajemen iuran |
| 5 | Member | 4 | Akses fitur anggota |
| 6 | Candidate | 5 | Akses terbatas (calon anggota) |

### 5.2 Daftar Permission

#### Modul: Profil & Akun
| Permission | Deskripsi |
|------------|-----------|
| profile.view_self | Melihat profil sendiri |
| profile.edit_self | Mengedit profil sendiri |
| password.change_self | Mengubah password sendiri |
| membercard.view_self | Melihat kartu anggota |

#### Modul: Anggota & Onboarding
| Permission | Deskripsi |
|------------|-----------|
| member.view_list | Melihat daftar anggota |
| member.view_detail | Melihat detail anggota |
| member.approve_candidate | Menyetujui calon anggota |
| member.reject_candidate | Menolak calon anggota |
| member.disable | Menonaktifkan anggota |
| member.enable | Mengaktifkan kembali anggota |
| member.delete | Menghapus anggota (soft delete) |
| member.change_role | Mengubah role (Super Admin) |

#### Modul: Konten
| Permission | Deskripsi |
|------------|-----------|
| content.view_public | Melihat konten publik |
| content.view_member | Melihat konten member |
| content.manage | CRUD konten |
| blog.create | Membuat blog/news |
| blog.manage | Mengelola semua blog |
| notification.send_all | Broadcast ke semua |
| notification.send_region | Broadcast ke wilayah |

#### Modul: Forum & Pesan
| Permission | Deskripsi |
|------------|-----------|
| forum.view | Melihat forum |
| forum.post | Posting di forum |
| forum.moderate | Moderasi forum |
| message.send_to_board | Kirim pesan ke pengurus |
| message.reply_to_member | Balas pesan anggota |
| complaint.view_inbox | Lihat inbox pengaduan |
| complaint.respond | Respon pengaduan |

#### Modul: Survei
| Permission | Deskripsi |
|------------|-----------|
| survey.fill | Mengisi survei |
| survey.create | Membuat survei |
| survey.view_results | Melihat hasil survei |

#### Modul: Keuangan
| Permission | Deskripsi |
|------------|-----------|
| dues.rate.manage | Kelola tarif iuran |
| dues.bill.generate | Generate tagihan |
| dues.bill.view_all | Lihat semua tagihan |
| dues.payment.view_all | Lihat semua pembayaran |
| dues.payment.verify | Verifikasi pembayaran |
| dues.payment.reject | Tolak pembayaran |
| dues.claim.view_all | Lihat semua klaim |
| dues.claim.process | Proses klaim |
| dues.arrears.enforce_pending | Set status pending |
| dues.report.export_all | Export laporan |

#### Modul: Wilayah (Scope)
| Permission | Deskripsi |
|------------|-----------|
| region.member.download | Download data wilayah |
| region.link.update | Update link WA wilayah |
| region.stats.view | Lihat statistik wilayah |
| region.dues.view | Lihat rekap iuran wilayah |

#### Modul: Sistem (Super Admin)
| Permission | Deskripsi |
|------------|-----------|
| role.manage | Kelola role |
| menu.manage | Kelola menu |
| submenu.manage | Kelola submenu |
| audit.view | Lihat audit log |
| masterdata.bulk_import | Bulk import master |
| members.bulk_import | Bulk import anggota |

### 5.3 Mapping Role → Permission

#### Candidate
- profile.view_self
- content.view_public
- membership.status.view

#### Member
Semua permission Candidate + :
- profile.edit_self, password.change_self, membercard.view_self
- content.view_member
- forum.view, forum.post
- survey.fill
- message.send_to_board

#### Admin
Semua permission Member + :
- member.view_list, member.view_detail
- member.approve_candidate, member.reject_candidate
- member.disable, member.enable, member.delete
- content.manage, blog.create, blog.manage
- notification.send_all
- complaint.view_inbox, complaint.respond, message.reply_to_member
- survey.create, survey.view_results
- forum.moderate

#### Coordinator
Semua permission Admin + (scope wilayah):
- notification.send_region
- region.member.download, region.link.update
- region.stats.view, region.dues.view

#### Treasurer
Semua permission Member + :
- dues.rate.manage
- dues.bill.generate, dues.bill.view_all
- dues.payment.view_all, dues.payment.verify, dues.payment.reject
- dues.claim.view_all, dues.claim.process
- dues.arrears.enforce_pending, dues.arrears.reactivate
- dues.report.export_all

#### Super Admin
SEMUA permission

---

## 6. ALUR REGISTRASI & ONBOARDING

### 6.1 State Machine

```
[registered] → [payment_submitted] → [email_verified] → [approved]
                                                    ↘ [rejected]
```

### 6.2 Alur Detail

#### Langkah 1: Akses Registrasi
- User klik "Bergabung" di landing page
- Redirect ke /register

#### Langkah 2: Input Kredensial
- Email (validasi unik, format valid)
- Password (min 8 karakter, huruf + angka)
- Konfirmasi password

#### Langkah 3: Data Registrasi
**Wajib:**
- Nama lengkap
- Nomor telepon
- Nama universitas
- Fakultas/jurusan
- Status kepegawaian
- Jabatan akademik
- Golongan ATAU range gaji

#### Langkah 4: Persetujuan AD/ART
- Tampilkan ringkasan AD/ART
- Checkbox persetujuan wajib
- Simpan timestamp di `agreed_to_terms_at`

#### Langkah 5: Kalkulasi Iuran

| Golongan/Range | Iuran |
|----------------|-------|
| Golongan I | Rp 20.000 |
| Golongan II | Rp 30.000 |
| Golongan III | Rp 35.000 |
| Golongan IV | Rp 45.000 |
| Rp 0 - 1.5jt | Rp 7.500 |
| Rp 1.5jt - 3jt | Rp 15.000 |
| Rp 3jt - 6jt | Rp 30.000 |
| > Rp 6jt | Rp 60.000 |

#### Langkah 6: Info Pembayaran
- Tampilkan nominal iuran
- QR Code pembayaran
- Nomor rekening + nama bank
- Instruksi pembayaran

#### Langkah 7: Upload Bukti Bayar
- Format: JPG, PNG, PDF
- Max size: 2MB
- Validasi mime type

#### Langkah 8: Verifikasi Email
- Kirim email verifikasi
- User klik link
- Update `onboarding_state` → `email_verified`

#### Langkah 9: Approval Admin
- Admin review di panel
- Verifikasi pembayaran
- Approve → `membership_status` = `active`
- Generate `member_number`

### 6.3 Akses Calon Anggota

Setelah registrasi, calon anggota dapat:
- Login ke sistem
- Melihat profil (tanpa nomor anggota)
- Membaca AD/ART, Manifesto, Sejarah
- Mengisi survei yang di-assign
- Kontak/chat ke admin
- Batalkan pendaftaran

**Tidak dapat:**
- Akses forum
- Akses kartu anggota
- Join grup WhatsApp

---

## 7. MODUL CMS

### 7.1 Halaman Publik

| URL | Nama | Deskripsi |
|-----|------|-----------|
| / | Landing Page | Homepage dinamis |
| /sejarah | Sejarah | Sejarah organisasi |
| /manifesto | Manifesto | Manifesto SPK |
| /visimisi | Visi Misi | Visi dan misi |
| /ad-art | AD/ART | Anggaran Dasar |
| /pengurus | Struktur | Daftar pengurus |
| /publikasi | Publikasi | Download PDF |
| /regulasi | Regulasi | Download PDF |
| /news | Berita | List artikel |
| /news/{slug} | Detail | Detail artikel |
| /contact | Kontak | Form kontak |

### 7.2 Landing Page Sections

| Key | Section | Konten |
|-----|---------|--------|
| about | Tentang SPK | HTML content |
| stats | Statistik | Auto dari data member |
| latest_publications | Publikasi | 6 dokumen terbaru |
| cta_join | Tombol Gabung | Config: text, url |
| cta_login | Tombol Login | Config: text, url |
| officers | Pengurus | Data cms_officers |
| subscribe | Subscribe | Form email |
| footer | Footer | Alamat, kontak, sosmed |

### 7.3 Admin CMS

**Super Admin:**
- Landing Page Builder (semua section)
- Pages (semua halaman)
- Documents (publikasi, regulasi)
- News (semua artikel)
- Officers (struktur pengurus)
- Subscribers
- Contact Inbox
- Media Library

**Admin:**
- Pages (terbatas)
- Documents
- News
- Contact Inbox
- Media Library

---

## 8. MODUL KEUANGAN (IURAN)

### 8.1 Fitur Bendahara

| Fitur | Deskripsi |
|-------|-----------|
| Master Tarif | CRUD tarif iuran |
| Generate Tagihan | Buat tagihan bulanan |
| Verifikasi Bayar | Review bukti pembayaran |
| Klaim Iuran | Proses klaim anggota |
| Tunggakan | Monitor dan enforce |
| Dashboard | Statistik keuangan |
| Laporan | Export data |

### 8.2 Alur Pembayaran

1. **Generate Tagihan** (bulanan/manual)
2. **Anggota bayar** + upload bukti
3. **Bendahara verifikasi**
   - Approved → status: paid
   - Rejected → anggota upload ulang
4. **Update status** tagihan

### 8.3 Aturan Tunggakan

| Bulan | Aksi |
|-------|------|
| 1 | Reminder email |
| 2 | Warning + notifikasi |
| 3 | Final notice + pending |

**Auto-pending:** Jika 3 bulan tidak bayar:
- `account_status` → suspended
- Akses fitur anggota dibatasi
- Reaktivasi setelah lunas

---

## 9. API ENDPOINTS

### 9.1 Public Endpoints

```
GET  /                      Landing page
GET  /sejarah               Halaman sejarah
GET  /manifesto             Halaman manifesto
GET  /visimisi              Halaman visi misi
GET  /ad-art                Halaman AD/ART
GET  /pengurus              Halaman struktur
GET  /publikasi             List publikasi
GET  /regulasi              List regulasi
GET  /documents/{id}/download  Download PDF
GET  /news                  List berita
GET  /news/{slug}           Detail berita
GET  /contact               Halaman kontak
POST /contact               Submit pesan
POST /subscribe             Subscribe newsletter
```

### 9.2 Auth Endpoints

```
GET  /login                 Halaman login
POST /login                 Proses login
GET  /logout                Logout
GET  /register              Halaman register
POST /register              Proses register
GET  /verify-email?token=   Verifikasi email
POST /forgot-password       Request reset
POST /reset-password        Reset password
```

### 9.3 Member Endpoints

```
GET  /member/profile        Lihat profil
POST /member/profile        Update profil
GET  /member/card           Kartu anggota
GET  /member/dues           Tagihan iuran
POST /member/dues/upload    Upload bukti bayar
GET  /member/forum          List thread
GET  /member/forum/{id}     Detail thread
POST /member/forum          Buat thread
POST /member/forum/{id}/reply  Balas thread
GET  /member/survey         List survei
GET  /member/survey/{id}    Detail survei
POST /member/survey/{id}    Submit jawaban
```

### 9.4 Admin Endpoints

```
GET  /admin/dashboard           Dashboard
GET  /admin/members             List anggota
GET  /admin/members/{id}        Detail anggota
GET  /admin/candidates          List calon
POST /admin/members/{id}/approve    Approve
POST /admin/members/{id}/reject     Reject
POST /admin/members/{id}/disable    Disable
POST /admin/members/{id}/enable     Enable

# CMS
GET/POST/PUT/DELETE /admin/cms/pages
GET/POST/PUT/DELETE /admin/cms/news
GET/POST/PUT/DELETE /admin/cms/documents
GET/POST/PUT/DELETE /admin/cms/officers

# Dues (Treasurer)
GET  /admin/dues                Dashboard iuran
GET  /admin/dues/bills          List tagihan
POST /admin/dues/generate       Generate tagihan
GET  /admin/dues/payments       List pembayaran
POST /admin/dues/payments/{id}/verify   Verifikasi
POST /admin/dues/payments/{id}/reject   Tolak
GET  /admin/dues/claims         List klaim
POST /admin/dues/claims/{id}/process    Proses klaim

# Super Admin Only
GET/POST/PUT/DELETE /admin/roles
GET  /admin/audit-logs
GET  /admin/cms/landing
POST /admin/cms/landing
POST /admin/members/bulk-import
POST /admin/masterdata/bulk-import
```

---

## 10. KEAMANAN & BEST PRACTICES

### 10.1 Password & Autentikasi

```php
// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Verify password
password_verify($input, $stored_hash);

// Brute force protection
if ($member->failed_login_attempts >= 5) {
    $member->locked_until = date('Y-m-d H:i:s', strtotime('+30 minutes'));
}
```

### 10.2 Token Email

```php
// Generate token
$token = bin2hex(random_bytes(32));
$hash = hash('sha256', $token);

// Store hash only
$db->table('sp_email_verifications')->insert([
    'member_id' => $memberId,
    'token_hash' => $hash,
    'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
]);

// Send plain token in email
// Verify by hashing input and comparing
```

### 10.3 Upload File

```php
// Validasi
$validationRule = [
    'file' => [
        'uploaded[file]',
        'mime_in[file,image/jpeg,image/png,application/pdf]',
        'max_size[file,2048]' // 2MB
    ]
];

// Simpan dengan random name
$newName = $file->getRandomName();
$file->move(WRITEPATH . 'uploads/dues_proofs/' . $memberUuid, $newName);

// Simpan checksum
$checksum = hash_file('sha256', $filePath);
```

### 10.4 CSRF Protection

```php
// Enable CSRF di Config/Filters.php
public $globals = [
    'before' => ['csrf']
];

// Di view
<?= csrf_field() ?>
```

### 10.5 SQL Injection Prevention

```php
// Selalu gunakan Query Builder atau prepared statements
$builder = $db->table('sp_members');
$builder->where('email', $email);
$result = $builder->get();

// Atau Model
$member = $memberModel->where('email', $email)->first();
```

### 10.6 XSS Prevention

```php
// Escape output
<?= esc($userInput) ?>

// Sanitize HTML (jika diperlukan)
$clean = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```

### 10.7 Audit Logging

```php
// Log setiap aksi penting
$auditLog->insert([
    'actor_id' => session('member_id'),
    'target_type' => 'member',
    'target_id' => $memberId,
    'action' => 'member.approved',
    'old_values' => json_encode(['status' => 'candidate']),
    'new_values' => json_encode(['status' => 'active']),
    'ip_address' => $request->getIPAddress(),
    'user_agent' => $request->getUserAgent()
]);
```

### 10.8 Transaksi Database

```php
$db = \Config\Database::connect();
$db->transStart();

try {
    // Multiple operations
    $memberModel->update($id, $data);
    $paymentModel->update($paymentId, ['status' => 'verified']);
    $billModel->update($billId, ['status' => 'paid']);
    $auditLog->insert([...]);
    
    $db->transComplete();
} catch (\Exception $e) {
    $db->transRollback();
    throw $e;
}
```

### 10.9 Rate Limiting

```php
// Di Filter
$cache = \Config\Services::cache();
$key = 'login_attempts_' . $request->getIPAddress();
$attempts = $cache->get($key) ?? 0;

if ($attempts >= 10) {
    return $this->response->setStatusCode(429, 'Too Many Requests');
}

$cache->save($key, $attempts + 1, 3600); // 1 hour
```

### 10.10 Environment Configuration

```env
# .env (JANGAN commit ke repo!)
CI_ENVIRONMENT = production

database.default.hostname = localhost
database.default.database = spk_production
database.default.username = spk_user
database.default.password = strong_password_here

app.baseURL = 'https://spk.domain.com'

email.SMTPHost = smtp.mailgun.org
email.SMTPUser = postmaster@domain.com
email.SMTPPass = mailgun_password

encryption.key = hex2bin:generated_key_here
```

---

## LAMPIRAN

### A. Seeder Default Roles

```php
// Database/Seeds/RoleSeeder.php
$roles = [
    ['name' => 'super_admin', 'display_name' => 'Super Admin', 'level' => 1],
    ['name' => 'admin', 'display_name' => 'Admin (Pengurus)', 'level' => 2],
    ['name' => 'coordinator', 'display_name' => 'Pengurus Wilayah', 'level' => 3],
    ['name' => 'treasurer', 'display_name' => 'Bendahara', 'level' => 3],
    ['name' => 'member', 'display_name' => 'Anggota', 'level' => 4],
    ['name' => 'candidate', 'display_name' => 'Calon Anggota', 'level' => 5],
];
```

### B. Contoh Filter RBAC

```php
// Filters/RoleFilter.php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $userRole = $session->get('role');
        $allowedRoles = $arguments ?? [];
        
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to('/dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

### C. Contoh Service Onboarding

```php
// Services/MemberOnboardingService.php
namespace App\Services;

class MemberOnboardingService
{
    protected $memberModel;
    protected $billModel;
    protected $paymentModel;
    protected $auditModel;
    
    public function register(array $data): int
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Create member
        $memberId = $this->memberModel->insert([
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name' => $data['full_name'],
            'role' => 'candidate',
            'membership_status' => 'candidate',
            'onboarding_state' => 'registered',
            'account_status' => 'pending',
            // ... other fields
        ]);
        
        // Create registration bill
        $this->billModel->insert([
            'member_id' => $memberId,
            'bill_type' => 'registration',
            'rate_id' => $data['rate_id'],
            'bill_amount' => $data['dues_amount'],
            'bill_status' => 'unpaid'
        ]);
        
        // Audit log
        $this->auditModel->insert([
            'actor_id' => $memberId,
            'target_type' => 'member',
            'target_id' => $memberId,
            'action' => 'member.registered'
        ]);
        
        $db->transComplete();
        
        return $memberId;
    }
    
    public function approve(int $memberId, int $adminId): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Update member
        $this->memberModel->update($memberId, [
            'membership_status' => 'active',
            'account_status' => 'active',
            'onboarding_state' => 'approved',
            'member_number' => $this->generateMemberNumber(),
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);
        
        // Update payment status
        // ... 
        
        // Audit log
        $this->auditModel->insert([
            'actor_id' => $adminId,
            'target_type' => 'member',
            'target_id' => $memberId,
            'action' => 'member.approved'
        ]);
        
        $db->transComplete();
        
        return $db->transStatus();
    }
    
    protected function generateMemberNumber(): string
    {
        $year = date('Y');
        $lastNumber = $this->memberModel
            ->like('member_number', "SPK-{$year}-", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber['member_number']);
            $sequence = (int)end($parts) + 1;
        }
        
        return sprintf("SPK-%s-%05d", $year, $sequence);
    }
}
```

---

**Dokumen ini adalah panduan referensi pengembangan. Pastikan untuk selalu mengikuti best practices keamanan dan melakukan code review sebelum deployment.**
