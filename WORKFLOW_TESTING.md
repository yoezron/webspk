# Complete Workflow Testing Guide

Panduan lengkap untuk testing sistem keanggotaan dan pembayaran iuran dari A sampai Z.

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Member Registration Workflow](#member-registration-workflow)
3. [Email Verification Workflow](#email-verification-workflow)
4. [Admin Approval Workflow](#admin-approval-workflow)
5. [Payment Submission Workflow](#payment-submission-workflow)
6. [Payment Verification Workflow](#payment-verification-workflow)
7. [Testing Checklist](#testing-checklist)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### 1. Database Setup
Pastikan database sudah ter-setup dengan benar:

```bash
# Run migrations
php spark migrate

# Run seeders
php spark db:seed DatabaseSeeder
```

### 2. Default Accounts
Setelah seeder, Anda akan memiliki akun super admin:

**Super Admin Account:**
- Email: `superadmin@spk.local`
- Password: `SuperAdmin123!`
- Role: super_admin
- Status: active

### 3. Email Configuration
Pastikan `.env` sudah di-configure untuk email:

```ini
email.protocol = smtp
email.SMTPHost = ssl://smtp.googlemail.com
email.SMTPPort = 465
email.SMTPUser = spkwebadm@gmail.com
email.SMTPPass = rtkfxoimecemrrkg
email.fromEmail = spkwebadm@gmail.com
email.fromName = 'Serikat Pekerja Kampus'
```

### 4. Upload Directory
Pastikan directory upload ada dan writable:

```bash
mkdir -p public/uploads/documents
mkdir -p public/uploads/payments
chmod -R 755 public/uploads
```

---

## Member Registration Workflow

### Step 1: Initial Registration
**URL:** `http://localhost/registrasi`

**Proses:**
1. User mengakses halaman registrasi
2. Form Step 1 - Account Information:
   - Full Name (required)
   - Email (required, unique)
   - Phone Number (required)
   - Password (required, min 8 chars)
   - Password Confirmation (required, must match)

**Expected Result:**
- Data tersimpan ke `sp_members` dengan:
  - `role` = 'candidate'
  - `membership_status` = 'candidate'
  - `account_status` = 'pending'
  - `onboarding_state` = 'registered'
  - `email_verified_at` = NULL
  - `reset_token_hash` = generated token untuk email verification
  - `reset_token_expires_at` = +24 hours

**Database Check:**
```sql
SELECT id, full_name, email, role, membership_status,
       account_status, onboarding_state, email_verified_at
FROM sp_members
WHERE email = 'test@example.com';
```

**Next:** Email verification dikirim ke user's email

---

## Email Verification Workflow

### Step 2: Email Verification
**Email Sent:** Email dengan subject "Verifikasi Email - Serikat Pekerja Kampus"

**Proses:**
1. User menerima email dengan link verifikasi
2. Link format: `http://localhost/verify-email/{TOKEN}`
3. User klik link verifikasi
4. System validates token:
   - Check token exists
   - Check token not expired (< 24 hours)
   - Update member data

**Expected Result:**
- `email_verified_at` = current timestamp
- `onboarding_state` = 'email_verified'
- Success message: "Email berhasil diverifikasi"

**Database Check:**
```sql
SELECT email_verified_at, onboarding_state
FROM sp_members
WHERE email = 'test@example.com';
```

**If Token Expired:**
- Error: "Token verifikasi sudah kadaluarsa"
- User dapat request resend di halaman login

**Next:** User melanjutkan ke Step 2 registration

---

### Step 3: Personal Information
**URL:** `http://localhost/registrasi/step-2`

**Requirements:**
- Email must be verified first
- Session must be active

**Form Fields:**
- Gender (L/P)
- Birth Place
- Birth Date
- Identity Number (NIK)
- Address
- Province (dropdown - from sp_region_codes)
- City
- District
- Postal Code
- Emergency Contact Info

**Expected Result:**
- Personal data updated in `sp_members`
- `onboarding_state` = 'profile_completed'
- Redirect to Step 3

**Database Check:**
```sql
SELECT gender, birth_place, birth_date, province,
       city, onboarding_state
FROM sp_members
WHERE email = 'test@example.com';
```

---

### Step 4: Employment Information
**URL:** `http://localhost/registrasi/step-3`

**Form Fields:**
- University Name
- Campus Location
- Faculty
- Department
- Work Unit
- Employee ID Number
- Lecturer ID Number (if applicable)
- Academic Rank
- Employment Status
- Work Start Date
- Gross Salary
- Allowances (Functional, Structural, Other)
- Dues Rate Type (golongan/gaji)
- Dues Rate ID (dropdown - from sp_dues_rates)

**Expected Result:**
- Employment data updated
- `monthly_dues_amount` calculated from dues_rate
- Redirect to Step 4

**Dues Calculation:**
Based on `sp_dues_rates`:
- **Golongan Type:**
  - GOL1: Rp 20,000
  - GOL2: Rp 25,000
  - GOL3: Rp 30,000
  - GOL4: Rp 35,000

- **Gaji Type:**
  - GAJI1 (< 5jt): Rp 7,500
  - GAJI2 (5-7jt): Rp 15,000
  - GAJI3 (7-10jt): Rp 22,500
  - GAJI4 (>10jt): Rp 30,000

**Database Check:**
```sql
SELECT university_name, faculty, employment_status,
       gross_salary, dues_rate_type, dues_rate_id,
       monthly_dues_amount
FROM sp_members
WHERE email = 'test@example.com';
```

---

### Step 5: Document Upload
**URL:** `http://localhost/registrasi/step-4`

**Form Fields:**
- ID Card Photo (KTP) - required
- Family Card Photo (KK) - optional
- SK Pengangkatan - optional
- Registration Payment Proof - required
- Bank Account Info (Name, Number, Bank)
- Agreement Checkbox - required
- Privacy Policy Checkbox - required

**File Requirements:**
- Format: JPG, JPEG, PNG, PDF
- Max Size: 2MB per file
- Stored in: `public/uploads/documents/`

**Expected Result:**
- Files uploaded and stored
- File paths saved in database
- `onboarding_state` = 'payment_submitted'
- `registration_payment_date` = current timestamp
- Redirect to completion page

**Database Check:**
```sql
SELECT id_card_photo, family_card_photo,
       sk_pengangkatan_photo, registration_payment_proof,
       registration_payment_date, onboarding_state
FROM sp_members
WHERE email = 'test@example.com';
```

**Next:** Member menunggu approval dari admin

---

## Admin Approval Workflow

### Step 6: Admin Reviews Application

**Login as Admin:**
```
URL: http://localhost/login
Email: superadmin@spk.local
Password: SuperAdmin123!
```

**Dashboard Access:**
- URL: `http://localhost/admin/dashboard`
- Navigate to: Members → Pending Approvals
- Or directly: `http://localhost/admin/members/pending`

**Pending Approvals Page Shows:**
- List of members with `onboarding_state` = 'payment_submitted'
- Member info: Name, Email, Phone, University
- Submitted date
- Quick actions: Approve / Reject

**View Member Detail:**
- URL: `http://localhost/admin/members/view/{member_id}`
- Shows all member information
- Shows uploaded documents (preview)
- Approval/Rejection forms

---

### Step 7a: Approve Member

**Process:**
1. Admin klik "Approve" di pending list atau detail page
2. System generates `member_number`:
   - Format: `SPK-{REGION_CODE}-{PADDED_ID}`
   - Example: `SPK-JABAR-00001`
3. Update member status:
   - `membership_status` = 'active'
   - `account_status` = 'active'
   - `role` = 'member'
   - `onboarding_state` = 'approved'
   - `member_number` = generated
   - `approval_date` = current timestamp
   - `approved_by` = admin user_id

**Email Sent:** "Keanggotaan Disetujui"
- Subject: "Selamat! Keanggotaan Anda Telah Disetujui"
- Contains: Member number, welcome message

**Expected Result:**
- Member dapat login
- Member dapat mengakses member dashboard
- Member dapat submit payment

**Database Check:**
```sql
SELECT member_number, membership_status, account_status,
       role, onboarding_state, approval_date, approved_by
FROM sp_members
WHERE email = 'test@example.com';
```

**Next:** Member dapat login dan submit pembayaran iuran

---

### Step 7b: Reject Member

**Process:**
1. Admin klik "Reject"
2. Admin input rejection reason (required)
3. Update member status:
   - `account_status` = 'rejected'
   - `onboarding_state` = 'rejected'
   - `rejection_reason` = admin input
   - `rejected_by` = admin user_id

**Email Sent:** "Keanggotaan Ditolak"
- Subject: "Informasi Pendaftaran Keanggotaan"
- Contains: Rejection reason

**Expected Result:**
- Member cannot login
- Member receives email with reason

**Database Check:**
```sql
SELECT account_status, onboarding_state,
       rejection_reason, rejected_by
FROM sp_members
WHERE email = 'test@example.com';
```

---

## Payment Submission Workflow

### Step 8: Member Login

**After Approval:**
```
URL: http://localhost/login
Email: test@example.com
Password: (password dari registrasi)
```

**Expected:**
- Redirect to: `http://localhost/dashboard`
- Dashboard shows:
  - Welcome message with member name
  - Member number
  - Membership status
  - Quick links (Profile, Payment, etc.)

---

### Step 9: Submit Payment

**Navigate to Payment:**
- Dashboard → Submit Pembayaran
- Or: `http://localhost/member/payment`
- Click "Submit Pembayaran Baru"

**Payment Submission Form:**
URL: `http://localhost/member/payment/submit`

**Auto-filled Info:**
- Member Name
- Member Number
- Monthly Dues Amount (from registration)
- Arrears (if any)

**Form Fields:**
- Payment Month (dropdown 1-12)
- Payment Year (dropdown current year to -2 years)
- Payment Date (date picker, max=today)
- Amount (pre-filled, editable)
- Payment Method (dropdown):
  - Transfer Bank
  - Cash
  - Deduction (Potong Gaji)
  - Other
- Payment Reference/Account Number (optional)
- Payment Proof (file upload - required)
- Notes (optional)

**Validation:**
- Check duplicate: Cannot pay same period twice
- File required: JPG, PNG, PDF max 2MB

**Expected Result:**
- Payment record created in `sp_dues_payments`:
  - `member_id` = current user
  - `payment_type` = 'monthly_dues'
  - `status` = 'pending'
  - `amount`, `payment_month`, `payment_year`, etc.
  - File saved in `public/uploads/payments/`

**Database Check:**
```sql
SELECT id, member_id, payment_type, amount,
       payment_month, payment_year, payment_date,
       payment_method, payment_proof, status
FROM sp_dues_payments
WHERE member_id = {member_id}
ORDER BY created_at DESC
LIMIT 1;
```

**Success Message:**
"Pembayaran berhasil disubmit. Menunggu verifikasi admin."

**Redirect to:** `http://localhost/member/payment` (history)

---

### Step 10: View Payment History

**URL:** `http://localhost/member/payment`

**Shows:**
- Member summary card:
  - Name, Member Number
  - Monthly Dues Amount
  - Last Payment Date
  - Total Arrears (if any)

- Payment history table:
  - Periode (month/year)
  - Payment Date
  - Method
  - Amount
  - Status (badge):
    - 🕐 Menunggu (yellow)
    - ✓ Terverifikasi (green)
    - ✗ Ditolak (red)
  - Verification Date
  - Action: View Detail

**Pagination:** 20 records per page

---

### Step 11: View Payment Detail (Member View)

**URL:** `http://localhost/member/payment/view/{payment_id}`

**Shows:**
- Status indicator (big icon + text)
- Payment Information:
  - Period, Date, Method, Amount
  - Reference Number
  - Type, Submitted date
  - Notes (if any)

- Verification Information:
  - If pending: "Sedang dalam proses verifikasi"
  - If verified: Verification date, notes
  - If rejected: Rejection reason, re-submit button

- Payment Proof:
  - Image preview (if JPG/PNG)
  - PDF download button (if PDF)
  - Click to open in new tab

---

## Payment Verification Workflow

### Step 12: Admin Views Pending Payments

**Login as Admin:**
```
URL: http://localhost/login
Email: superadmin@spk.local
Password: SuperAdmin123!
```

**Navigate to Payments:**
- Dashboard → Payments
- Or: `http://localhost/admin/payments`

**Payment Dashboard Shows:**
- **Statistics Cards:**
  - Pending count
  - Verified count
  - Rejected count
  - Total amount

- **Quick Actions:**
  - "Verifikasi Pending" button

- **Filters:**
  - Status (All/Pending/Verified/Rejected)
  - Search (Name/Member Number/Reference)

- **Payment List Table:**
  - Member info
  - Period
  - Payment date
  - Amount
  - Status badge
  - Action: View Detail

---

### Step 13: View Pending Verifications

**URL:** `http://localhost/admin/payments/pending`

**Shows Card List:**
Each pending payment shows:
- Member info (Name, Member Number, Email)
- Payment info (Period, Date, Method, Amount)
- Quick actions:
  - "Lihat Detail & Verifikasi" button
  - "Lihat Bukti" button (opens file)
- Notes (if any)
- Submitted timestamp

---

### Step 14: View Payment Detail (Admin View)

**URL:** `http://localhost/admin/payments/view/{payment_id}`

**Layout:**
- Status card (big indicator)
- Member information section
- Payment information section
- Payment proof preview
- Verification section with forms

**If Status = Pending:**
Shows 2 forms side by side:

**Verify Form (Green):**
- Optional notes field
- "Verifikasi Pembayaran" button
- Confirmation dialog

**Reject Form (Red):**
- Required rejection reason field
- "Tolak Pembayaran" button
- Confirmation dialog

---

### Step 15a: Verify Payment

**Process:**
1. Admin klik "Verifikasi Pembayaran"
2. Confirm dialog
3. System processes:
   - Update payment: `status` = 'verified'
   - Set `verified_by` = admin user_id
   - Set `verified_at` = current timestamp
   - Set `verification_notes` = admin input
   - Update member: `last_dues_payment_date` = payment_date
   - **Recalculate Arrears:**
     * Calculate months since approval
     * Count verified payments
     * Calculate: `arrears_months` = months_due - months_paid
     * Calculate: `total_arrears` = arrears_months * monthly_amount
   - **Send Email:** Payment confirmation to member

**Email Sent:** "Konfirmasi Pembayaran Iuran"
- Subject: "Pembayaran Iuran Terverifikasi"
- Contains: Amount, Period, Thank you message

**Expected Result:**
- Payment status changed to 'verified'
- Member arrears updated
- Email sent to member

**Database Check:**
```sql
-- Check payment status
SELECT status, verified_by, verified_at, verification_notes
FROM sp_dues_payments
WHERE id = {payment_id};

-- Check member arrears
SELECT last_dues_payment_date, arrears_months, total_arrears
FROM sp_members
WHERE id = {member_id};
```

**Success Message:**
"Pembayaran berhasil diverifikasi dan email konfirmasi telah dikirim"

---

### Step 15b: Reject Payment

**Process:**
1. Admin input rejection reason
2. Klik "Tolak Pembayaran"
3. Confirm dialog
4. System processes:
   - Update payment: `status` = 'rejected'
   - Set `verified_by` = admin user_id
   - Set `verified_at` = current timestamp
   - Set `verification_notes` = rejection reason
   - Log action

**Expected Result:**
- Payment status = 'rejected'
- Member can see rejection reason
- Member can re-submit payment

**Database Check:**
```sql
SELECT status, verified_by, verified_at, verification_notes
FROM sp_dues_payments
WHERE id = {payment_id};
```

**Success Message:**
"Pembayaran ditolak"

---

## Testing Checklist

### ✅ Registration Flow
- [ ] Step 1: Account creation berhasil
- [ ] Email verification token generated
- [ ] Verification email dikirim
- [ ] Token verification berhasil
- [ ] Step 2: Personal data tersimpan
- [ ] Step 3: Employment data tersimpan
- [ ] Dues amount calculated correctly
- [ ] Step 4: Files uploaded successfully
- [ ] Registration complete page shown

### ✅ Admin Approval Flow
- [ ] Admin dapat lihat pending applications
- [ ] Admin dapat view detail member
- [ ] Admin dapat approve member
- [ ] Member number generated correctly
- [ ] Approval email dikirim
- [ ] Admin dapat reject member
- [ ] Rejection email dikirim
- [ ] Status updated correctly

### ✅ Member Login & Dashboard
- [ ] Approved member dapat login
- [ ] Rejected member tidak dapat login
- [ ] Dashboard shows correct info
- [ ] Member number displayed
- [ ] Quick links working

### ✅ Payment Submission Flow
- [ ] Member dapat akses payment form
- [ ] Form pre-filled correctly
- [ ] Duplicate payment prevented
- [ ] File upload working
- [ ] Payment submitted successfully
- [ ] Payment appears in history
- [ ] Status shows "Menunggu"

### ✅ Payment Verification Flow
- [ ] Admin dapat view pending payments
- [ ] Statistics accurate
- [ ] Filters working
- [ ] Admin dapat view payment detail
- [ ] Payment proof preview working
- [ ] Admin dapat verify payment
- [ ] Arrears calculated correctly
- [ ] Confirmation email sent
- [ ] Admin dapat reject payment
- [ ] Rejection reason saved
- [ ] Member dapat view rejection

### ✅ Email System
- [ ] Email verification sent
- [ ] Approval email sent
- [ ] Rejection email sent
- [ ] Payment confirmation sent
- [ ] Email templates rendered correctly
- [ ] Links in emails working

### ✅ File Uploads
- [ ] Documents uploaded (registration)
- [ ] Payment proofs uploaded
- [ ] Files accessible via URL
- [ ] File size validation working
- [ ] File type validation working

### ✅ Database Integrity
- [ ] No duplicate emails
- [ ] No duplicate member numbers
- [ ] Foreign keys working
- [ ] Unique constraints enforced
- [ ] Timestamps accurate
- [ ] Status transitions correct

---

## Troubleshooting

### Issue: Email Tidak Terkirim

**Check:**
```php
// Check .env configuration
email.protocol = smtp
email.SMTPHost = ssl://smtp.googlemail.com
email.SMTPPort = 465
email.SMTPUser = spkwebadm@gmail.com
email.SMTPPass = rtkfxoimecemrrkg

// Check email log
tail -f writable/logs/log-*.log | grep -i email
```

**Solution:**
- Pastikan Gmail "Less secure app access" enabled
- Atau gunakan Gmail App Password
- Test dengan: `php spark emailtest` (buat command jika perlu)

---

### Issue: File Upload Gagal

**Check:**
```bash
# Check directory permissions
ls -la public/uploads/
ls -la public/uploads/documents/
ls -la public/uploads/payments/

# Should be 755 or 775
chmod -R 755 public/uploads/
```

**Check PHP Settings:**
```php
// Check upload limits
phpinfo() | grep upload_max_filesize
phpinfo() | grep post_max_size

// Should be at least 2MB
upload_max_filesize = 2M
post_max_size = 8M
```

---

### Issue: Duplicate Key Error (Region Codes)

**Solution:**
```bash
# Truncate and re-seed
mysql -u root -p db_serikat_pekerja

TRUNCATE TABLE sp_region_codes;
EXIT;

php spark db:seed RegionCodesSeeder
```

---

### Issue: Member Cannot Login After Approval

**Check:**
```sql
SELECT member_number, membership_status, account_status,
       role, onboarding_state
FROM sp_members
WHERE email = 'test@example.com';
```

**Expected Values:**
- `membership_status` = 'active'
- `account_status` = 'active'
- `role` = 'member'
- `onboarding_state` = 'approved'
- `member_number` NOT NULL

**Fix:**
```sql
UPDATE sp_members
SET membership_status = 'active',
    account_status = 'active',
    role = 'member'
WHERE email = 'test@example.com';
```

---

### Issue: Payment Form Shows Rp 0

**Check:**
```sql
SELECT id, full_name, dues_rate_type, dues_rate_id,
       monthly_dues_amount
FROM sp_members
WHERE email = 'test@example.com';
```

**If NULL:**
```sql
-- Get correct dues rate ID
SELECT id, rate_code, monthly_amount
FROM sp_dues_rates;

-- Update member
UPDATE sp_members
SET dues_rate_type = 'golongan',
    dues_rate_id = 1,
    monthly_dues_amount = 20000.00
WHERE email = 'test@example.com';
```

---

### Issue: Arrears Not Calculating

**Check Approval Date:**
```sql
SELECT id, full_name, approval_date,
       monthly_dues_amount, arrears_months, total_arrears
FROM sp_members
WHERE email = 'test@example.com';
```

**Manual Recalculation:**
```sql
-- Count months since approval
SELECT TIMESTAMPDIFF(MONTH, approval_date, NOW()) as months_due
FROM sp_members
WHERE id = 1;

-- Count verified payments
SELECT COUNT(*) as months_paid
FROM sp_dues_payments
WHERE member_id = 1
  AND status = 'verified'
  AND payment_type = 'monthly_dues';

-- Calculate arrears
-- arrears_months = months_due - months_paid
-- total_arrears = arrears_months * monthly_dues_amount
```

---

## API Endpoints Summary

### Public Routes
```
GET  /                              # Landing page
GET  /registrasi                    # Registration step 1
POST /registrasi/step-1             # Process step 1
GET  /registrasi/step-2             # Registration step 2
POST /registrasi/step-2             # Process step 2
GET  /registrasi/step-3             # Registration step 3
POST /registrasi/step-3             # Process step 3
GET  /registrasi/step-4             # Registration step 4
POST /registrasi/step-4             # Process step 4
GET  /registrasi/selesai            # Registration complete

GET  /verify-email/{token}          # Email verification
POST /email-verification/resend     # Resend verification

GET  /login                         # Login page
POST /auth/login                    # Process login
GET  /logout                        # Logout
```

### Member Routes (Requires auth + member role)
```
GET  /dashboard                     # General dashboard
GET  /member/dashboard              # Member dashboard
GET  /member/profile                # Profile page

GET  /member/payment                # Payment history
GET  /member/payment/submit         # Payment form
POST /member/payment/process        # Process payment
GET  /member/payment/view/{id}      # Payment detail
```

### Admin Routes (Requires auth + admin role)
```
GET  /admin/dashboard               # Admin dashboard

GET  /admin/members                 # Member list
GET  /admin/members/pending         # Pending approvals
GET  /admin/members/view/{id}       # Member detail
POST /admin/members/approve/{id}    # Approve member
POST /admin/members/reject/{id}     # Reject member
POST /admin/members/suspend/{id}    # Suspend member
POST /admin/members/activate/{id}   # Activate member

GET  /admin/payments                # Payment list
GET  /admin/payments/pending        # Pending verifications
GET  /admin/payments/view/{id}      # Payment detail
POST /admin/payments/verify/{id}    # Verify payment
POST /admin/payments/reject/{id}    # Reject payment
```

---

## Success Criteria

**Registration Flow:**
- ✅ Member dapat register tanpa error
- ✅ Email verification berfungsi
- ✅ All steps dapat diselesaikan
- ✅ Files uploaded successfully

**Admin Flow:**
- ✅ Admin dapat approve/reject members
- ✅ Member number generated correctly
- ✅ Emails sent successfully
- ✅ Admin dapat verify/reject payments

**Payment Flow:**
- ✅ Member dapat submit payment
- ✅ Duplicate prevention working
- ✅ Admin dapat verify payment
- ✅ Arrears calculated correctly
- ✅ Confirmation email sent

**System:**
- ✅ No SQL errors
- ✅ No file upload errors
- ✅ No email errors
- ✅ Routes working correctly
- ✅ RBAC enforced properly

---

## Next Steps After Testing

1. **Production Deployment:**
   - Update `.env` for production
   - Setup SSL certificate
   - Configure production email
   - Setup backup strategy

2. **Features to Add:**
   - Dashboard statistics & charts
   - Bulk payment import
   - Export reports (PDF/Excel)
   - Notification system
   - Search & advanced filters

3. **Monitoring:**
   - Setup error logging
   - Monitor email delivery
   - Track user activity
   - Database performance

---

**Last Updated:** December 2024
**Version:** 1.0
**Author:** Claude AI Assistant
