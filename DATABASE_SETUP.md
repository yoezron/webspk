# Database Setup Guide - SPK Membership System

## 📋 Prerequisites

Pastikan Anda sudah menginstall:
- PHP 8.1 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.3 atau lebih tinggi
- Composer

## 🔧 Installation Steps

### 1. Install MySQL/MariaDB

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

**macOS (with Homebrew):**
```bash
brew install mysql
brew services start mysql
```

**Windows:**
Download dan install dari https://dev.mysql.com/downloads/installer/

### 2. Secure MySQL Installation (Recommended)

```bash
sudo mysql_secure_installation
```

### 3. Create Database

Login ke MySQL:
```bash
mysql -u root -p
```

Jalankan perintah berikut:
```sql
CREATE DATABASE db_serikat_pekerja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user khusus (opsional, lebih aman)
CREATE USER 'spk_user'@'localhost' IDENTIFIED BY 'password_anda';
GRANT ALL PRIVILEGES ON db_serikat_pekerja.* TO 'spk_user'@'localhost';
FLUSH PRIVILEGES;

EXIT;
```

### 4. Update .env Configuration

File `.env` sudah dikonfigurasi dengan:

```ini
database.default.hostname = localhost
database.default.database = db_serikat_pekerja
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

**Jika menggunakan user khusus**, update credentials:
```ini
database.default.username = spk_user
database.default.password = password_anda
```

### 5. Run Migrations

Migrations akan membuat semua tabel yang diperlukan:

```bash
php spark migrate
```

**Output yang diharapkan:**
```
Running all new migrations...
Running: 2024-12-14-000001_CreateSpMembersTable
Migrated: 2024-12-14-000001_CreateSpMembersTable
Running: 2024-12-14-000002_CreateSpDuesRatesTable
Migrated: 2024-12-14-000002_CreateSpDuesRatesTable
Running: 2024-12-14-000003_CreateSpDuesPaymentsTable
Migrated: 2024-12-14-000003_CreateSpDuesPaymentsTable
Running: 2024-12-14-000004_CreateSpSessionsTable
Migrated: 2024-12-14-000004_CreateSpSessionsTable
Running: 2024-12-14-000005_CreateSpRegionCodesTable
Migrated: 2024-12-14-000005_CreateSpRegionCodesTable
```

### 6. Run Seeders

Seeders akan mengisi data awal:

```bash
php spark db:seed DatabaseSeeder
```

**Output yang diharapkan:**
```
=== Starting Database Seeding ===

1. Seeding Region Codes...
Region codes seeded successfully! Total: 34 provinces

2. Seeding Dues Rates...
Dues rates seeded successfully! Total: 8 rates

3. Seeding Super Admin...
Super Admin created successfully!
Email: superadmin@spk.local
Password: SuperAdmin123!

=== Database Seeding Completed! ===

You can now login with:
Email: superadmin@spk.local
Password: SuperAdmin123!
```

## 📊 Database Structure

### Tables Created:

1. **`sp_members`** - Tabel utama anggota
   - Authentication data (email, password, tokens)
   - Personal data (nama, alamat, KTP, dll)
   - Work data (universitas, fakultas, gaji, dll)
   - Documents (foto, KTP, KK, SK)
   - Dues tracking (tunggakan, pembayaran terakhir)

2. **`sp_dues_rates`** - Tarif iuran
   - Tarif golongan (GOL1-GOL4)
   - Tarif gaji (GAJI1-GAJI4)

3. **`sp_dues_payments`** - Pembayaran iuran
   - Payment records
   - Verification status
   - Payment proofs

4. **`ci_sessions`** - Database sessions
   - Session management untuk security

5. **`sp_region_codes`** - Kode wilayah
   - 34 provinsi Indonesia
   - Kode untuk nomor anggota

## 🔑 Default Login Credentials

Setelah seeding berhasil, Anda dapat login dengan:

**Super Admin Account:**
- Email: `superadmin@spk.local`
- Password: `SuperAdmin123!`
- Role: Super Administrator
- Access: Full system access

## 📝 Migration Commands

### Run migrations:
```bash
php spark migrate
```

### Rollback last migration:
```bash
php spark migrate:rollback
```

### Rollback all migrations:
```bash
php spark migrate:rollback --all
```

### Refresh (rollback all + migrate):
```bash
php spark migrate:refresh
```

### Check migration status:
```bash
php spark migrate:status
```

## 🌱 Seeder Commands

### Run all seeders:
```bash
php spark db:seed DatabaseSeeder
```

### Run specific seeder:
```bash
php spark db:seed SuperAdminSeeder
php spark db:seed DuesRatesSeeder
php spark db:seed RegionCodesSeeder
```

## 🧪 Testing Database Connection

Test koneksi database dengan:

```bash
php spark db:table sp_members
```

Atau check di MySQL:
```bash
mysql -u root -p db_serikat_pekerja -e "SHOW TABLES;"
```

## 🔧 Troubleshooting

### Error: "Unable to connect to the database"

**Solusi:**
1. Pastikan MySQL service running:
   ```bash
   sudo systemctl status mysql
   ```

2. Check credentials di `.env`

3. Test koneksi manual:
   ```bash
   mysql -u root -p
   ```

### Error: "Access denied for user"

**Solusi:**
1. Update password di `.env`
2. Reset MySQL password jika lupa
3. Check user privileges

### Error: "Database does not exist"

**Solusi:**
```bash
mysql -u root -p -e "CREATE DATABASE db_serikat_pekerja;"
```

### Error: Foreign key constraint fails

**Solusi:**
Rollback dan migrate ulang:
```bash
php spark migrate:rollback --all
php spark migrate
```

## 📦 Backup & Restore

### Backup database:
```bash
mysqldump -u root -p db_serikat_pekerja > backup_$(date +%Y%m%d).sql
```

### Restore database:
```bash
mysql -u root -p db_serikat_pekerja < backup_20241214.sql
```

## 🚀 Next Steps

Setelah database setup berhasil:

1. ✅ Start development server:
   ```bash
   php spark serve
   ```

2. ✅ Akses aplikasi:
   ```
   http://localhost:8080
   ```

3. ✅ Login sebagai Super Admin:
   - URL: http://localhost:8080/login
   - Email: superadmin@spk.local
   - Password: SuperAdmin123!

4. ✅ Test fitur-fitur:
   - Member registration
   - Email verification
   - Admin approval workflow
   - Payment submission & verification

## 📞 Support

Jika mengalami kesulitan, check:
- CodeIgniter 4 Database Documentation: https://codeigniter.com/user_guide/database/
- MySQL Documentation: https://dev.mysql.com/doc/
- Project Issues: (add your repository URL)

---

**Created:** 2024-12-14
**Version:** 1.0.0
**System:** Serikat Pekerja Kampus Membership System
