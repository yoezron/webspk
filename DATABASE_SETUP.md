# Database Setup Instructions

## Prerequisites
- MySQL 8.0+ atau MariaDB 10.5+
- MySQL client terinstall

## Setup Database

### 1. Buat Database
```bash
mysql -u root -p -e "CREATE DATABASE db_serikat_pekerja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2. Import Schema
```bash
mysql -u root -p db_serikat_pekerja < database_schema_spk.sql
```

### 3. Verifikasi Import
```bash
mysql -u root -p db_serikat_pekerja -e "SHOW TABLES;"
```

Seharusnya menampilkan 40+ tabel termasuk:
- `sp_members` - Tabel anggota
- `rbac_roles`, `rbac_permissions` - RBAC system
- `sp_dues_*` - Modul iuran
- `cms_*` - Modul CMS
- `sp_audit_logs` - Audit trail

### 4. Cek Data Awal (Seed Data)

Schema sudah include data awal untuk:
- **Super Admin** default account:
  - Email: `superadmin@spk.local`
  - Password: `SuperAdmin123!` (hash sudah di-generate)

- **Tarif Iuran** (8 rates):
  - Golongan I-IV (Rp 20.000 - Rp 45.000)
  - Gaji Tier 1-4 (Rp 7.500 - Rp 60.000)

- **RBAC Roles** (6 roles):
  - Super Admin
  - Admin (Pengurus Pusat)
  - Coordinator (Pengurus Wilayah)
  - Treasurer (Bendahara)
  - Member
  - Candidate

- **Permissions & Menus**: Complete RBAC structure

### 5. Update .env (Jika Berbeda)

Jika menggunakan username/password berbeda, update file `.env`:
```
database.default.username = your_username
database.default.password = your_password
```

## Troubleshooting

### Error: Access denied
```bash
# Ubah password MySQL user atau update .env
mysql -u root -p
```

### Error: Unknown database
```bash
# Pastikan database sudah dibuat
mysql -u root -p -e "SHOW DATABASES;"
```

### Foreign Key Errors
```bash
# Schema sudah handle ini dengan SET FOREIGN_KEY_CHECKS
# Tapi pastikan import dalam satu transaction
```

## Database Schema Overview

### Member Management (sp_*)
- `sp_members` - Core member data (80+ fields)
- `sp_member_documents` - Uploaded documents
- `sp_email_verifications` - Email tokens
- `sp_audit_logs` - Audit trail

### RBAC System (rbac_*)
- `rbac_roles` - Role definitions
- `rbac_permissions` - Permission definitions
- `rbac_role_permissions` - Role-Permission mapping
- `rbac_menus` - Menu structure
- `rbac_submenus` - Submenu structure

### Dues Management (sp_dues_*)
- `sp_dues_rates` - Tarif iuran
- `sp_dues_bills` - Tagihan bulanan
- `sp_dues_payments` - Pembayaran
- `sp_dues_claims` - Klaim keringanan

### CMS Module (cms_*)
- `cms_pages` - Dynamic pages
- `cms_documents` - Dokumen library
- `cms_news_posts` - Berita & artikel
- `cms_officers` - Data pengurus
- `cms_media` - Media uploads
- `cms_home_sections` - Landing page sections

## Next Steps After Import

1. **Test Login**: Gunakan super admin credentials
2. **Run Seeders**: Jika ada additional seed data
3. **Test RBAC**: Verifikasi permissions
4. **Start Development**: Aplikasi siap untuk development
