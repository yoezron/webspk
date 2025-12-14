# Panduan Pengembangan Web Serikat Pekerja Kampus (SPK)

Framework yang dipakai: **CodeIgniter 4**, template **Edura**, **Tailwind CSS**, database **MySQL**. Sistem mencakup portal registrasi anggota, pengelolaan keanggotaan, penerbitan nomor/kartu anggota, forum komunikasi, survei anggota, statistik demografi, dan publikasi informasi serikat. Data anggota lama (>1700) perlu **bulk upload** oleh Super Admin dan dapat berstatus **pending** sampai dilengkapi dan/atau upload bukti iuran terakhir.

---

## 1) Modul & Ruang Lingkup Fitur

### 1.1 Public Site (tanpa login)

- Landing page `/` (dinamis via CMS, dikelola Super Admin)
- Halaman statis/dinamis:

  - `/sejarah`, `/manifesto`, `/visimisi`, `/ad-art`, `/pengurus`, `/contact`

- Dokumen PDF:

  - `/publikasi` (policy brief, dsb)
  - `/regulasi` (UU/PP/Permen, dsb)

- `/news` (berita/blog SPK: teks + gambar)
- Tombol **Bergabung** dan **Login** di landing page

### 1.2 Member Area (login)

- Profil & edit profil
- Kartu anggota (aktif saja)
- AD/ART, Manifesto, Sejarah, Informasi Serikat
- Ubah password
- Forum diskusi anggota
- Survei anggota
- Pesan ke pengurus
- Tombol gabung WhatsApp (umum + wilayah sesuai provinsi domisili kerja)

### 1.3 Admin Console

- Manajemen calon anggota & anggota aktif (approve/reject/disable/enable)
- News/blog, dokumen PDF, link WhatsApp
- Survei & akses hasil survei
- Forum & moderasi
- Statistik & pelaporan
- Audit log

### 1.4 Modul Kebendaharaan (Treasury / Iuran)

- Master tarif iuran
- Generate tagihan (registration + bulanan)
- Upload bukti bayar, verifikasi pembayaran, koreksi, klaim iuran
- Monitoring progres iuran bulanan
- Penegakan tunggakan ≥ 3 bulan (auto-flag + notifikasi bertahap + pending/suspend sesuai kebijakan)

---

## 2) Model Identitas, Status, dan Alur Keanggotaan

### 2.1 Prinsip desain: Role vs Status Keanggotaan

Dokumen merekomendasikan membedakan:

- **Role** = jenis pengguna & hak akses sistem (RBAC)
- **membership_status** = status keanggotaan serikat (candidate/active/disabled/rejected, dll)
- **onboarding_state** = tahap proses pendaftaran (registered → payment_submitted → email_verified → approved)

Catatan penting: “Calon anggota” bisa dimodelkan sebagai _status_ (disarankan) atau sebagai _role_ (lebih sederhana untuk UI), namun tetap simpan `membership_status` untuk logika verifikasi.

### 2.2 Status & State (wajib dipakai)

**A. membership_status**

- `candidate` (calon anggota)
- `active` (anggota aktif)
- `inactive` (opsional; non-aktif periodik)
- `disabled` (dinonaktifkan/ditangguhkan)
- `rejected` (ditolak)

**B. onboarding_state**

- `registered` (baru daftar, belum upload bukti bayar)
- `payment_submitted` (sudah upload bukti bayar)
- `email_verified` (email diverifikasi setelah upload bukti)
- `approved` (disetujui admin → aktif)
- `rejected` (ditolak)

**C. account_status**

- `pending`, `active`, `suspended`, `rejected` + aturan rekomendasi pemetaan status

---

## 3) Alur Registrasi → Aktif (State Machine)

### 3.1 Ringkasan alur bisnis (sesuai requirement)

1. Daftar → status **calon anggota**
2. Wajib bayar iuran pendaftaran + upload bukti bayar
3. Setelah upload bukti → wajib verifikasi email
4. Setelah email verified → admin verifikasi menjadi anggota aktif
5. Admin dapat menolak/menonaktifkan anggota (calon/aktif)

### 3.2 Detail langkah implementasi (backend)

**A. Submit bukti pembayaran**

- Validasi user hanya upload untuk bill miliknya
- Simpan dokumen ke `sp_member_documents` (doc_type=dues_payment_proof)
- Upsert `sp_dues_payments` (status `submitted`)
- Update `sp_members.onboarding_state = payment_submitted`
- Catat audit event `payment_proof_submitted`

**B. Verifikasi email (hanya setelah bukti bayar diupload)**

- Hanya boleh membuat token jika `onboarding_state=payment_submitted`
- Simpan **hash token** di DB, token mentah hanya di link email
- Saat verify, set `email_verified_at`, naikkan `onboarding_state=email_verified`, set `used_at`, audit `email_verified`

**C. Approve admin → anggota aktif**
Prasyarat disarankan:

- `sp_members.onboarding_state = email_verified`
- Ada registration bill
- `sp_dues_payments.payment_status = submitted`

Langkah:

- Set payment `verified`
- Set bill `paid`
- Update member: `membership_status=active`, `account_status=active`, `onboarding_state=approved`, generate `member_number` jika kosong, simpan review note, audit `admin_approved_member`

**D. Reject / Disable**

- Reject: set `membership_status=rejected`, `account_status=rejected`, `onboarding_state=rejected`, isi alasan, audit `admin_rejected_member`
- Disable: set `membership_status=disabled`, `account_status=suspended`, simpan alasan, audit `admin_disabled_member`

### 3.3 Akses Calon Anggota setelah login

Setelah upload bukti + verifikasi email, calon anggota **dapat login** tapi halaman terbatas (profil, edit profil, edit password, baca manifesto/sejarah/AD-ART, survei yang di-assign, kontak ke admin). Nomor anggota belum tampil sebelum di-approve.

---

## 4) RBAC (Role-Based Access Control) + Scope Wilayah

### 4.1 Prinsip RBAC & pembatasan wilayah

- Akses fitur ditentukan oleh role (RBAC)
- Tindakan sensitif dibatasi tambahan dengan **scope wilayah** dan **status keanggotaan**
- Pengurus wilayah hanya boleh mengakses anggota sesuai provinsi/region code, tidak boleh ubah role dan tidak boleh akses manajemen sistem

### 4.2 Role final (yang dipakai)

- Super Admin
- Admin (Pengurus Pusat)
- Koordinator/Pengurus Wilayah
- Bendahara
- Anggota (Member)
- Calon Anggota (Candidate)

> Implementasi ideal: simpan role untuk “tipe user”, dan `membership_status` untuk “aktif/calon/ditolak/dinonaktifkan”. Calon anggota di UI bisa diperlakukan sebagai “role tampilan” berbasis `membership_status=candidate`.

---

## 5) Permission Keys (siap masuk RBAC)

### 5.1 Super Admin

- `system.config.manage`
- `rbac.role.create`
- `rbac.role.update`
- `rbac.role.assign`
- `menu.manage`
- `cms.manage_all`
- `masterdata.bulk_import`
- `members.bulk_import`
- `audit.view_all`

### 5.2 Admin (Pengurus Pusat)

- `members.approve`
- `members.reject`
- `members.view_all`
- `members.update_all`
- `members.suspend`
- `members.disable`
- `broadcast.send_all`
- `news.manage`
- `docs.manage` (opsional)
- `survey.manage_all`
- `forum.moderate_all`
- `whatsapp.manage_all`
- `region_admin.assign_coordinator`

### 5.3 Pengurus Wilayah

- `members.view_region`
- `members.update_region_limited`
- `broadcast.send_region`
- `survey.manage_region`
- `forum.moderate_region`
- `whatsapp.manage_region`
- `members.propose_action` (usulan suspend/disable)

### 5.4 Bendahara (Kebendaharaan / Treasurer)

**Master**

- `dues.rate.manage`
- `dues.policy.manage` (opsional)

**Billing**

- `dues.bill.generate`
- `dues.bill.view_all`
- `dues.bill.update`

**Payments**

- `dues.payment.view_all`
- `dues.payment.verify`
- `dues.payment.reject`
- `dues.payment.adjust` (sangat sensitif)

**Claims**

- `dues.claim.view_all`
- `dues.claim.process`

**Arrears/Enforcement**

- `dues.arrears.view_all`
- (tambahkan) `dues.arrears.enforce` untuk aksi pending/suspend berbasis tunggakan

---

## 6) Struktur Tabel RBAC + Menu/Submenu (auto visibility)

### 6.1 Tabel RBAC inti

Relasi yang dipakai: `rbac_roles` 1—N `rbac_role_permissions` N—1 `rbac_permissions`.

**DDL ringkas (rekomendasi)**

```sql
-- ROLES
CREATE TABLE rbac_roles (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  role_key VARCHAR(50) NOT NULL UNIQUE,   -- super_admin, admin, region_admin, treasurer, member
  role_name VARCHAR(100) NOT NULL,
  is_system TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL, updated_at DATETIME NULL
);

-- PERMISSIONS
CREATE TABLE rbac_permissions (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  perm_key VARCHAR(100) NOT NULL UNIQUE,  -- members.approve, cms.manage_all, dst
  perm_name VARCHAR(150) NOT NULL,
  perm_group VARCHAR(80) NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL
);

-- ROLE <-> PERMISSION (many-to-many)
CREATE TABLE rbac_role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES rbac_roles(id),
  FOREIGN KEY (permission_id) REFERENCES rbac_permissions(id)
);
```

### 6.2 Tabel Menu/Submenu (dengan mapping permission)

Dokumen mengusulkan:

- `rbac_menus` 1—N `rbac_submenus`
- `rbac_menus` N—N `rbac_permissions` via `rbac_menu_permissions`
- `rbac_submenus` N—N `rbac_permissions` via `rbac_submenu_permissions`

**DDL ringkas (rekomendasi)**

```sql
CREATE TABLE rbac_menus (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  menu_key VARCHAR(60) NOT NULL UNIQUE,
  label VARCHAR(80) NOT NULL,
  icon VARCHAR(50) NULL,
  url VARCHAR(150) NULL,                 -- kalau menu juga clickable
  sort_order INT NOT NULL DEFAULT 0,
  is_public TINYINT(1) NOT NULL DEFAULT 0,
  permission_logic ENUM('ANY','ALL') NOT NULL DEFAULT 'ANY',
  is_enabled TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE rbac_submenus (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  menu_id BIGINT UNSIGNED NOT NULL,
  submenu_key VARCHAR(60) NOT NULL UNIQUE,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(150) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_public TINYINT(1) NOT NULL DEFAULT 0,
  permission_logic ENUM('ANY','ALL') NOT NULL DEFAULT 'ANY',
  is_enabled TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (menu_id) REFERENCES rbac_menus(id)
);

CREATE TABLE rbac_menu_permissions (
  menu_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (menu_id, permission_id),
  FOREIGN KEY (menu_id) REFERENCES rbac_menus(id),
  FOREIGN KEY (permission_id) REFERENCES rbac_permissions(id)
);

CREATE TABLE rbac_submenu_permissions (
  submenu_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (submenu_id, permission_id),
  FOREIGN KEY (submenu_id) REFERENCES rbac_submenus(id),
  FOREIGN KEY (permission_id) REFERENCES rbac_permissions(id)
);
```

### 6.3 Logika visibilitas menu (otomatis)

Item menu/submenu **tampil jika**:

- `is_public=1`, **atau**
- user punya permission yang dipersyaratkan item tsb, sesuai `permission_logic`:

  - `ANY`: minimal punya salah satu permission
  - `ALL`: wajib punya semua permission

**Pseudocode (service layer)**

```php
function canSeeItem($item, $userPerms, $requiredPerms) {
  if ($item->is_public) return true;
  if (empty($requiredPerms)) return false;   // atau true jika Anda ingin default-visible
  if ($item->permission_logic === 'ANY') {
    return count(array_intersect($userPerms, $requiredPerms)) > 0;
  }
  // ALL
  return count(array_diff($requiredPerms, $userPerms)) === 0;
}
```

> Praktik ideal: build menu tree sekali per request (atau cache per `role_id`) dan filter per user (tambahkan scope wilayah pada URL tertentu lewat Filter/Policy).

---

## 7) CMS (Manajemen Konten Page)

### 7.1 Landing Page `/` berbasis section (dinamis)

Gunakan tabel `cms_home_sections` dengan `section_key` unik:
`about`, `stats`, `latest_publications`, `cta_join`, `cta_login`, `officers`, `subscribe`, `footer`, plus `config_json` untuk parameter section.

### 7.2 Dokumen PDF: `/publikasi` dan `/regulasi`

- `cms_documents` menyimpan metadata + file PDF (mime harus `application/pdf`) + status publish/archived + checksum.
- `cms_document_categories` untuk kategori dokumen (publikasi/regulasi).

### 7.3 News/Blog: `/news`

Gunakan `cms_news_posts` (title, slug unik, excerpt, content_html, cover image, status publish). Asset gambar di `cms_media`.

### 7.4 Struktur Pengurus: `/pengurus`

Gunakan `cms_officers` (bisa berdiri sendiri atau link ke member).

---

## 8) Data Iuran (Master Tarif)

Tarif iuran yang harus disediakan (seed master) mengikuti daftar berikut.

**Tabel: `dues_rates` (contoh desain)**

```sql
CREATE TABLE dues_rates (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  rate_code VARCHAR(30) NOT NULL UNIQUE,      -- GOL_I, SAL_0_1500, dst
  label VARCHAR(150) NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'IDR',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0
);
```

**Seed contoh (sesuai requirement)**

- Golongan I (Ia, Ib, Ic, Id) → 20000
- Golongan II (IIa, IIb, IIc, IId) → 30000
- Golongan III (IIIa, IIb, IIc, Id) → 35000
- Golongan IV (IVa…IVe) → 45000
- Rp0–Rp1.500.000 → 7500
- Rp1.500.000–Rp3.000.000 → 15000
- Rp3.000.001–Rp6.000.000 → 30000
- > Rp6.000.000 → 60000

---

## 9) Fitur Kebendaharaan (Treasury) yang Ideal

### 9.1 Entitas utama iuran

Minimal 4 komponen:

- **Bill** (tagihan): `sp_dues_bills`
- **Payment** (pembayaran): `sp_dues_payments`
- **Documents** (bukti): `sp_member_documents`
- **Claims** (klaim/dispute): `sp_dues_claims` (disarankan terpisah)

### 9.2 Progres iuran bulanan (monitoring)

Dashboard bendahara:

- anggota wajib iuran vs sudah bayar vs belum bayar
- progres per periode
- filter wilayah/kategori tarif/status
- export CSV/Excel

### 9.3 Klaim iuran (claim/dispute)

Use case: “sudah bayar tapi belum terverifikasi”, nominal salah, double payment, salah periode, waiver/dispensasi.
Flow:

1. anggota submit klaim
2. bendahara verifikasi
3. keputusan: approved (koreksi bill/payment) atau rejected (alasan)
4. simpan jejak koreksi (audit)

### 9.4 Penegakan tunggakan ≥ 3 bulan

- auto-flag tunggakan (berdasarkan periode unpaid)
- notifikasi bertahap: bulan 1 reminder, bulan 2 warning, bulan 3 final notice + status pending
- bendahara dapat mengubah status pending/suspended sesuai kebijakan (dan simpan alasan “Menunggak > 3 bulan”)
- re-aktivasi setelah tunggakan lunas (butuh verifikasi)

> Keputusan penting kebijakan: “pending” hanya mematikan akses fitur anggota (forum/survei/kartu) atau benar-benar suspend login.

### 9.5 Relasi dengan Admin & Pengurus Wilayah (anti redundant)

- Admin: lihat ringkasan iuran (read-only), tidak mengubah transaksi
- Pengurus wilayah: lihat rekap wilayah (read-only) + follow-up anggota wilayah
- Bendahara: verifikasi pembayaran/klaim + enforcement tunggakan (write)

---

## 10) Struktur Data Anggota (Registrasi) & Privasi

### 10.1 Data sensitif (gaji) wajib consent eksplisit

Jika menyimpan `base_salary`/`take_home_pay`, sertakan `consent_sensitive_data` (default 0) agar ada persetujuan eksplisit.

### 10.2 Dokumen anggota

Gunakan tabel dokumen generik `sp_member_documents` untuk:

- KTP/ID proof
- Bukti pembayaran iuran
- Dokumen lain yang perlu review

---

## 11) Security, Audit, dan Validasi (Wajib)

### 11.1 Security minimum

- Password: `password_hash(PASSWORD_DEFAULT)`, tidak pernah simpan plaintext
- Token email: simpan **hash token** (sha256) di DB
- Upload bukti bayar: batasi mime (jpg/png/pdf), size 2–5MB, simpan di `writable/uploads/...`, gunakan random filename, simpan checksum sha256
- Otorisasi: user hanya boleh akses bill & dokumen miliknya
- DB transaksi untuk operasi gabungan (submit bukti, verify email, approve/reject/disable)

### 11.2 Audit log

Wajib catat event minimal:

- register
- submit payment proof
- create token verifikasi email
- verify email
- approve/reject/disable/enable
- verifikasi pembayaran

---

## 12) Rekomendasi Struktur Controller/Routes (CI4)

Endpoint yang direkomendasikan:

- `POST /register` → buat member + buat registration bill + keluarkan instruksi bayar
- `POST /dues/{billId}/upload-proof` → upload bukti pembayaran
- `POST /email/verification/request` → hanya jika `payment_submitted`
- `GET /email/verification/verify?token=...`
- Admin:

  - `GET /admin/candidates` (filter `payment_submitted`, `email_verified`)
  - `POST /admin/members/{id}/approve|reject|disable|enable`
  - `GET /admin/payments/pending` (payment_status=submitted)

---

## 13) Data Migration: Bulk Upload Anggota Lama

Karena ada data anggota lama (>1700), sediakan:

- Import Excel template (Super Admin)
- Aturan: jika data belum lengkap mengikuti skema registrasi baru → set status **pending**
- Re-aktif jika anggota melengkapi data dan upload bukti pembayaran iuran terakhir

---

## 14) Checklist Implementasi (urutan kerja yang aman)

1. **Migrations**: `rbac_*`, `sp_members`, `sp_member_documents`, `sp_dues_*`, `cms_*`, `audit_logs`, masterdata wilayah/universitas
2. **Seeders**: roles, permissions, role_permissions, menu/submenu + permission mapping, tarif iuran (`dues_rates`)
3. **Filters/Policy**:

   - Auth filter
   - RBAC permission filter
   - Region-scope filter (untuk pengurus wilayah)
   - Membership-status gate (fitur anggota aktif)

4. **Services**: MenuBuilder (auto visibility), DuesService (billing/payment/arrears), CMSService
5. **Admin UI**: kandidat (state-based), payment queue, approval actions
6. **Treasury UI**: verifikasi pembayaran, progres bulanan, klaim, tunggakan ≥3 bulan
7. **QA**: test state machine (register → payment → email verify → approve), negative cases (reject/disable), akses menu sesuai permission_logic ANY/ALL

---

Jika Anda ingin, saya bisa lanjutkan dengan **contoh struktur menu/submenu (tree)** lengkap beserta mapping permission per item (mis. “Keanggotaan”, “CMS”, “Kebendaharaan”, “Wilayah”, “Forum”, “Survei”) sehingga UI sidebar benar-benar otomatis mengikuti RBAC.

# Struktur Menu/Submenu Ideal (RBAC + Auto Visibility) — Web Serikat Pekerja Kampus

Dokumen kebutuhan menegaskan: akses ditentukan oleh **RBAC**, lalu dipersempit oleh **status keanggotaan** (candidate/active/disabled/…) dan **scope wilayah** untuk Pengurus Wilayah.

Di bawah ini saya susun **menu tree (sidebar)** yang “ideal & lengkap”, siap di-seed ke tabel `rbac_menus` + `rbac_submenus` dan mapping permission. Struktur ini juga mengikuti rekomendasi “Menu berdasarkan role” di dokumen.

---

## 1) Aturan Tampilan Menu (wajib diterapkan)

### 1.1 Aturan visibilitas (menu builder)

Item menu/submenu tampil jika:

- `is_public=1`, **atau**
- user punya permission yang dipersyaratkan (logic `ANY/ALL`).

### 1.2 Gate “membership:active”

Jika `membership_status != active`, **blok fitur anggota**: forum, survei, kartu anggota, join WA, (opsional) pesan internal, dsb.

### 1.3 Gate “region_scope”

Untuk Pengurus Wilayah: semua aksi anggota (view/disable/enable/download) **hanya untuk wilayahnya** (match `member.region_code == coordinator.region_code`).

---

## 2) Permission “Core” (yang dipakai untuk menu)

Dokumen sudah menyediakan set permission yang rapi untuk implementasi (profil, konten, anggota, forum, pesan, pengaduan, survei, wilayah) dan mapping role→permission.
Tambahan role Bendahara/Treasurer fokus pada iuran & kepatuhan (bukan konten/forum/survei/RBAC).

> Untuk menu di bawah, saya gunakan permission inti dari dokumen + permission treasury + permission system (super admin) yang sudah Anda tetapkan.

---

## 3) Menu Tree Sidebar (YAML Seed-Friendly)

> Format ini mudah dikonversi ke tabel `rbac_menus`, `rbac_submenus`, `rbac_*_permissions`.
> `gates` adalah rule tambahan (bukan RBAC), diterapkan via CI4 Filters/policy.

```yaml
version: 1
sidebar:
  - menu_key: dashboard
    label: Beranda
    icon: "home"
    permission_logic: ANY
    required_permissions: [profile.view_self]
    submenus:
      - submenu_key: dashboard.home
        label: Ringkasan
        url: /dashboard
        permission_logic: ANY
        required_permissions: [profile.view_self]

  - menu_key: account
    label: Akun
    icon: "user"
    permission_logic: ANY
    required_permissions: [profile.view_self]
    submenus:
      - submenu_key: account.profile_view
        label: Profil Saya
        url: /me/profile
        required_permissions: [profile.view_self]
      - submenu_key: account.profile_edit
        label: Edit Profil
        url: /me/profile/edit
        required_permissions: [profile.edit_self]
      - submenu_key: account.password
        label: Ubah Password
        url: /me/security/password
        required_permissions: [password.change_self]

  - menu_key: membership
    label: Keanggotaan
    icon: "id-card"
    permission_logic: ANY
    required_permissions: [membership.status.view]
    submenus:
      - submenu_key: membership.status
        label: Status Keanggotaan
        url: /me/membership/status
        required_permissions: [membership.status.view]

      - submenu_key: membership.member_card
        label: Kartu Anggota
        url: /me/member-card
        required_permissions: [membercard.view_self]
        gates: [membership:active]

  - menu_key: union_info
    label: Informasi Serikat
    icon: "book"
    permission_logic: ANY
    required_permissions: [content.view_public]
    submenus:
      # Jika halaman-halaman ini PUBLIC, bisa set is_public=1 dan tanpa permission.
      - submenu_key: union_info.info
        label: Informasi Serikat
        url: /member/info
        required_permissions: [content.view_member] # atau content.view_public (kebijakan)
      - submenu_key: union_info.adart
        label: AD/ART
        url: /ad-art
        required_permissions: [content.view_public]
      - submenu_key: union_info.manifesto
        label: Manifesto
        url: /manifesto
        required_permissions: [content.view_public]
      - submenu_key: union_info.sejarah
        label: Sejarah SPK
        url: /sejarah
        required_permissions: [content.view_public]

  - menu_key: dues_self
    label: Iuran Saya
    icon: "wallet"
    permission_logic: ANY
    required_permissions: [profile.view_self]
    submenus:
      - submenu_key: dues_self.bills
        label: Tagihan & Riwayat
        url: /me/dues
        required_permissions: [profile.view_self]
      - submenu_key: dues_self.upload_proof
        label: Upload Bukti Pembayaran
        url: /me/dues/upload
        required_permissions: [profile.view_self]
      - submenu_key: dues_self.claims
        label: Klaim Iuran
        url: /me/dues/claims
        required_permissions: [profile.view_self]

  - menu_key: forum
    label: Forum Anggota
    icon: "messages"
    permission_logic: ANY
    required_permissions: [forum.view]
    submenus:
      - submenu_key: forum.index
        label: Forum
        url: /member/forum
        required_permissions: [forum.view]
        gates: [membership:active]
      - submenu_key: forum.new_thread
        label: Buat Thread
        url: /member/forum/new
        required_permissions: [forum.post]
        gates: [membership:active]

  - menu_key: surveys
    label: Survei Anggota
    icon: "clipboard"
    permission_logic: ANY
    required_permissions: [survey.fill]
    submenus:
      - submenu_key: surveys.list
        label: Daftar Survei
        url: /member/surveys
        required_permissions: [survey.fill]
        gates: [membership:active]

  - menu_key: messaging
    label: Komunikasi
    icon: "mail"
    permission_logic: ANY
    required_permissions: [message.send_to_board]
    submenus:
      - submenu_key: messaging.compose
        label: Pesan ke Pengurus
        url: /member/messages/compose
        required_permissions: [message.send_to_board]
        gates: [membership:active]
      - submenu_key: messaging.inbox
        label: Inbox
        url: /member/messages
        required_permissions: [message.send_to_board]
        gates: [membership:active]

  - menu_key: whatsapp
    label: Grup WhatsApp
    icon: "brand-whatsapp"
    permission_logic: ANY
    required_permissions: [whatsapp.join_all_members]
    submenus:
      - submenu_key: whatsapp.all
        label: Grup Seluruh Anggota
        url: /member/whatsapp/all
        required_permissions: [whatsapp.join_all_members]
        gates: [membership:active]
      - submenu_key: whatsapp.region
        label: Grup Wilayah
        url: /member/whatsapp/region
        required_permissions: [whatsapp.join_region]
        gates: [membership:active]

  # =========================
  # ADMIN (Pengurus Pusat)
  # =========================
  - menu_key: admin_members
    label: Manajemen Anggota
    icon: "users"
    permission_logic: ANY
    required_permissions: [member.view_list]
    submenus:
      - submenu_key: admin_members.candidates
        label: Calon Anggota (Konfirmasi)
        url: /admin/candidates
        required_permissions: [member.approve_candidate, member.reject_candidate]
        permission_logic: ANY
      - submenu_key: admin_members.list
        label: Daftar Anggota
        url: /admin/members
        required_permissions: [member.view_list]
      - submenu_key: admin_members.detail
        label: Detail Anggota
        url: /admin/members/{id}
        required_permissions: [member.view_detail]
      - submenu_key: admin_members.actions
        label: Aksi Status (Enable/Disable/Suspend)
        url: /admin/members/{id}/status
        required_permissions: [member.disable, member.enable]
        permission_logic: ANY

      - submenu_key: admin_members.assign_region_admin
        label: Assign Pengurus Wilayah
        url: /admin/region/assign
        required_permissions: [region_admin.assign_coordinator]

  - menu_key: admin_broadcast
    label: Broadcast & Notifikasi
    icon: "bell"
    permission_logic: ANY
    required_permissions: [notification.send_all]
    submenus:
      - submenu_key: admin_broadcast.send_all
        label: Kirim Notifikasi (Semua Anggota)
        url: /admin/broadcast
        required_permissions: [notification.send_all]

  - menu_key: admin_complaints
    label: Pengaduan & Kontak
    icon: "inbox"
    permission_logic: ANY
    required_permissions: [complaint.view_inbox]
    submenus:
      - submenu_key: admin_complaints.inbox
        label: Inbox Pengaduan/Kontak
        url: /admin/complaints
        required_permissions: [complaint.view_inbox]
      - submenu_key: admin_complaints.respond
        label: Tanggapi Pengaduan
        url: /admin/complaints/{id}
        required_permissions: [complaint.respond]

  - menu_key: admin_forum
    label: Moderasi Forum
    icon: "shield"
    permission_logic: ANY
    required_permissions: [forum.moderate]
    submenus:
      - submenu_key: admin_forum.moderation
        label: Moderasi Thread & Komentar
        url: /admin/forum/moderation
        required_permissions: [forum.moderate]

  - menu_key: admin_surveys
    label: Manajemen Survei
    icon: "list-check"
    permission_logic: ANY
    required_permissions: [survey.create]
    submenus:
      - submenu_key: admin_surveys.manage
        label: CRUD Survei
        url: /admin/surveys
        required_permissions: [survey.create]
      - submenu_key: admin_surveys.results
        label: Hasil & Rekap
        url: /admin/surveys/results
        required_permissions: [survey.view_results]

  - menu_key: admin_whatsapp
    label: Link WhatsApp
    icon: "link"
    permission_logic: ANY
    required_permissions: [whatsapp.manage_all]
    submenus:
      - submenu_key: admin_whatsapp.manage
        label: Kelola Link Grup (Umum/Wilayah/Kluster)
        url: /admin/whatsapp-links
        required_permissions: [whatsapp.manage_all]

  # =========================
  # CMS (Konten)
  # =========================
  - menu_key: cms
    label: CMS
    icon: "layout"
    permission_logic: ANY
    required_permissions: [content.manage, blog.manage, docs.manage]
    submenus:
      - submenu_key: cms.landing_builder
        label: Landing Page Builder
        url: /admin/cms/home
        required_permissions: [cms.manage_all]
        gates: [role:super_admin] # khusus super admin

      - submenu_key: cms.pages
        label: Pages (Sejarah/Manifesto/VisiMisi/AD-ART/Contact)
        url: /admin/cms/pages
        required_permissions: [content.manage]

      - submenu_key: cms.documents_publikasi
        label: Documents - Publikasi
        url: /admin/cms/documents?type=publikasi
        required_permissions: [docs.manage]

      - submenu_key: cms.documents_regulasi
        label: Documents - Regulasi
        url: /admin/cms/documents?type=regulasi
        required_permissions: [docs.manage]

      - submenu_key: cms.news
        label: News / Blog
        url: /admin/cms/news
        required_permissions: [blog.manage]

      - submenu_key: cms.officers
        label: Struktur Pengurus
        url: /admin/cms/officers
        required_permissions: [content.manage]

      - submenu_key: cms.subscribers
        label: Subscribers
        url: /admin/cms/subscribers
        required_permissions: [content.manage]

      - submenu_key: cms.contact_inbox
        label: Contact Inbox
        url: /admin/cms/contact-inbox
        required_permissions: [complaint.view_inbox]

      - submenu_key: cms.media
        label: Media Library
        url: /admin/cms/media
        required_permissions: [content.manage]

  # =========================
  # PENGURUS WILAYAH
  # =========================
  - menu_key: region
    label: Wilayah
    icon: "map"
    permission_logic: ANY
    required_permissions: [members.view_region]
    submenus:
      - submenu_key: region.members
        label: Anggota Wilayah
        url: /region/members
        required_permissions: [members.view_region]
        gates: [region_scope]
      - submenu_key: region.members_download
        label: Download Data Anggota Wilayah
        url: /region/members/export
        required_permissions: [region.member.download]
        gates: [region_scope]
      - submenu_key: region.broadcast
        label: Notifikasi Wilayah
        url: /region/broadcast
        required_permissions: [notification.send_region]
        gates: [region_scope]
      - submenu_key: region.whatsapp_link
        label: Link Grup Wilayah
        url: /region/whatsapp-link
        required_permissions: [region.link.update]
        gates: [region_scope]
      - submenu_key: region.stats
        label: Statistik Wilayah
        url: /region/stats
        required_permissions: [region.stats.view]
        gates: [region_scope]
      - submenu_key: region.dues
        label: Dana Iuran Wilayah
        url: /region/dues
        required_permissions: [region.dues.view]
        gates: [region_scope]
      - submenu_key: region.propose_action
        label: Usulkan Tindakan (Suspend/Disable)
        url: /region/proposals
        required_permissions: [members.propose_action]
        gates: [region_scope]

  # =========================
  # BENDAHARA / TREASURY
  # =========================
  - menu_key: treasury
    label: Kebendaharaan
    icon: "coins"
    permission_logic: ANY
    required_permissions: [dues.payment.view_all]
    submenus:
      - submenu_key: treasury.dashboard
        label: Dashboard Iuran
        url: /treasury/dues/dashboard
        required_permissions: [dues.dashboard.view_all]

      - submenu_key: treasury.rates
        label: Master Tarif Iuran
        url: /treasury/dues/rates
        required_permissions: [dues.rate.manage]

      - submenu_key: treasury.bills
        label: Tagihan (Semua Anggota)
        url: /treasury/dues/bills
        required_permissions: [dues.bill.view_all]

      - submenu_key: treasury.generate_bills
        label: Generate Tagihan Bulanan
        url: /treasury/dues/bills/generate
        required_permissions: [dues.bill.generate]

      - submenu_key: treasury.payments_in
        label: Pembayaran Masuk
        url: /treasury/dues/payments
        required_permissions: [dues.payment.view_all]

      - submenu_key: treasury.verify_payments
        label: Verifikasi Bukti Bayar
        url: /treasury/dues/payments/pending
        required_permissions: [dues.payment.verify]

      - submenu_key: treasury.claims
        label: Klaim Iuran
        url: /treasury/dues/claims
        required_permissions: [dues.claim.view_all, dues.claim.process]
        permission_logic: ANY

      - submenu_key: treasury.arrears
        label: Tunggakan & Penertiban
        url: /treasury/dues/arrears
        required_permissions: [dues.arrears.view_all]

      - submenu_key: treasury.enforce_pending
        label: Pending/Suspend karena Tunggakan
        url: /treasury/dues/arrears/enforce
        required_permissions: [dues.arrears.enforce]

      - submenu_key: treasury.reports
        label: Laporan & Export
        url: /treasury/dues/reports
        required_permissions: [dues.report.export_all]

  # =========================
  # SUPER ADMIN (SYSTEM)
  # =========================
  - menu_key: system
    label: Sistem
    icon: "settings"
    permission_logic: ANY
    required_permissions: [system.config.manage, menu.manage, audit.view_all]
    submenus:
      - submenu_key: system.config
        label: Konfigurasi Sistem
        url: /system/config
        required_permissions: [system.config.manage]

      - submenu_key: system.roles
        label: Role Management
        url: /system/rbac/roles
        required_permissions: [rbac.role.create, rbac.role.update, rbac.role.assign]
        permission_logic: ANY

      - submenu_key: system.menu
        label: Menu Management
        url: /system/menus
        required_permissions: [menu.manage]

      - submenu_key: system.submenu
        label: Sub Menu Management
        url: /system/submenus
        required_permissions: [menu.manage]

      - submenu_key: system.masterdata_import
        label: Bulk Import Master Data
        url: /system/masterdata/import
        required_permissions: [masterdata.bulk_import]

      - submenu_key: system.members_import
        label: Bulk Upload Anggota (Excel)
        url: /system/members/import
        required_permissions: [members.bulk_import]

      - submenu_key: system.audit
        label: Audit Log
        url: /system/audit
        required_permissions: [audit.view_all]
```

---

## 4) Preset Menu per Role (default ideal)

Berikut default yang sejalan dengan rekomendasi dokumen (candidate, member, pengurus, pengurus wilayah, super admin) dan ditambah Bendahara.

### 4.1 Calon Anggota (Candidate)

- Beranda, Akun (profil), Informasi Serikat, Status Keanggotaan
- (Disarankan) akses Upload bukti pembayaran + verifikasi email bila onboarding dibuat terpisah

### 4.2 Anggota (Member)

- Beranda, Akun, Keanggotaan (kartu), Informasi Serikat, Forum, Survei, Komunikasi, Join WA

### 4.3 Pengurus (Admin/Pusat)

- Semua menu Anggota + Manajemen Anggota, Calon Anggota (konfirmasi), Pengaduan, Kelola Informasi/Blog, Kelola Survei, Broadcast

### 4.4 Pengurus Wilayah

- Semua menu Pengurus + Anggota Wilayah (download), Notifikasi Wilayah, Link Grup Wilayah, Statistik Wilayah, Dana Iuran Wilayah

### 4.5 Bendahara

- Kebendaharaan: master tarif, billing bulanan, verifikasi pembayaran, monitoring progres, klaim, tunggakan ≥ 3 bulan (pending/suspend), laporan/export

### 4.6 Super Admin

- Semua menu + Role/Menu/Submenu management, Global member management, CMS full, Audit log

---

## 5) Catatan penting agar “tidak redundant”

1. **CMS vs Informasi Serikat**

   - “Informasi Serikat” (member/candidate) = **view content**
   - “CMS” (admin) = **CRUD pages/docs/news/officers/subscribers/contact/media**; struktur panel CMS di dokumen sudah jelas.

2. **Manajemen Anggota vs Kebendaharaan**

   - Pengurus: onboarding/approve/reject/disable
   - Bendahara: iuran & kepatuhan (pending/suspend karena tunggakan) dengan audit trail

3. **Pengurus Wilayah**
   Semua fitur yang menyentuh data anggota harus lewat gate `region_scope`, dan menu wilayah dibuat terpisah agar tidak “tercampur” dengan global admin.

---
