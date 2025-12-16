# 🔐 Security Credentials Rotation Guide

## ⚠️ CRITICAL: Immediate Action Required

**Your credentials have been exposed in Git history.** Follow this guide immediately to secure your system.

---

## 📋 Table of Contents

1. [Exposed Credentials List](#exposed-credentials-list)
2. [Immediate Actions (Within 24 Hours)](#immediate-actions-within-24-hours)
3. [Step-by-Step Rotation Guide](#step-by-step-rotation-guide)
4. [Prevention Measures](#prevention-measures)
5. [Security Checklist](#security-checklist)

---

## 🚨 Exposed Credentials List

The following credentials were found in Git history and **MUST** be changed immediately:

### Database Credentials
- ✅ Database Password: `change_this_password_123`
- ✅ Database Host: `localhost`
- ✅ Database Name: `db_webspk`
- ✅ Database User: `root`

### Email/SMTP Credentials
- ✅ SMTP Host: `smtp.gmail.com`
- ✅ SMTP User: (your Gmail address)
- ✅ SMTP Password: (app-specific password)

### Encryption Keys
- ✅ Encryption Key: `hex:...` (32-byte key)

### Session Security
- ✅ Cookie Name: `ci_session`
- ✅ Session Driver: `CodeIgniter\Session\Handlers\DatabaseHandler`

---

## ⏱️ Immediate Actions (Within 24 Hours)

### Priority 1: Database (CRITICAL - Do First)

```bash
# 1. Connect to MySQL
mysql -u root -p

# 2. Change root password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'NEW_STRONG_PASSWORD_HERE';

# 3. Create dedicated database user (recommended)
CREATE USER 'webspk_user'@'localhost' IDENTIFIED BY 'ANOTHER_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON db_webspk.* TO 'webspk_user'@'localhost';
FLUSH PRIVILEGES;

# 4. Exit MySQL
EXIT;
```

### Priority 2: Email/SMTP Credentials

**For Gmail:**
1. Go to https://myaccount.google.com/security
2. Enable 2-Factor Authentication if not enabled
3. Generate new App-Specific Password:
   - Go to "App passwords"
   - Select "Mail" and "Other (Custom name)"
   - Name it "WebSPK Production"
   - Copy the 16-character password

**Update `.env` with new credentials**

### Priority 3: Encryption Key

```bash
# Generate new encryption key
php spark key:generate

# This will automatically update your .env file
# Or manually generate:
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

---

## 📝 Step-by-Step Rotation Guide

### Step 1: Backup Current System

```bash
# 1. Backup database
mysqldump -u root -p db_webspk > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Backup .env file
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# 3. Test backup restoration (optional but recommended)
```

### Step 2: Update Database Credentials

**Edit `.env` file:**

```ini
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = db_webspk
database.default.username = webspk_user        # NEW dedicated user
database.default.password = YOUR_NEW_DB_PASS   # NEW strong password
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

**Test database connection:**

```bash
php spark migrate:status
```

If successful, you should see migration status. If error, revert to backup and check credentials.

### Step 3: Update Email Credentials

**Edit `.env` file:**

```ini
#--------------------------------------------------------------------
# EMAIL
#--------------------------------------------------------------------

email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = your-email@gmail.com          # Your Gmail
email.SMTPPass = xxxx xxxx xxxx xxxx          # NEW App Password (16 chars)
email.SMTPCrypto = tls
email.fromEmail = your-email@gmail.com
email.fromName = "Serikat Pekerja Kampus"
```

**Test email functionality:**

```bash
# Send test email via your application
# Or use this test script:
php -r "
require 'vendor/autoload.php';
\$email = \Config\Services::email();
\$email->setTo('test@example.com');
\$email->setSubject('Test Email');
\$email->setMessage('Testing email configuration');
if (\$email->send()) {
    echo 'Email sent successfully';
} else {
    echo \$email->printDebugger();
}
"
```

### Step 4: Rotate Encryption Key

```bash
# Generate new key
php spark key:generate

# Verify in .env
grep "encryption.key" .env
```

**⚠️ WARNING:** Changing encryption key will invalidate existing encrypted data (sessions, cookies, encrypted database fields). Schedule during maintenance window.

**Migration plan for encryption key change:**
1. Announce maintenance window to users
2. Force all users to logout
3. Clear all sessions: `TRUNCATE TABLE ci_sessions;`
4. Generate new key
5. Users will need to login again

### Step 5: Update Session Configuration (Optional but Recommended)

**Edit `.env` file:**

```ini
#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------

session.driver = CodeIgniter\Session\Handlers\DatabaseHandler
session.cookieName = spk_session              # NEW unique name
session.expiration = 7200
session.savePath = ci_sessions
session.matchIP = true                        # ENABLE for security
session.timeToUpdate = 300
session.regenerateDestroy = true              # ENABLE for security
```

### Step 6: Verify All Changes

```bash
# 1. Check database connection
php spark migrate:status

# 2. Test login functionality
# - Visit /login
# - Try logging in with test account
# - Verify session works

# 3. Test registration
# - Try registering new account
# - Check if email sent successfully

# 4. Check logs for errors
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

---

## 🛡️ Prevention Measures

### 1. Update .gitignore

Ensure `.env` is properly ignored:

```bash
# Check if .env is in .gitignore
grep -n "^\.env$" .gitignore

# If not found, add it:
echo ".env" >> .gitignore
echo ".env.backup.*" >> .gitignore
```

### 2. Install Pre-commit Hook

Create `.git/hooks/pre-commit`:

```bash
#!/bin/bash

# Pre-commit hook to prevent committing sensitive files

# List of files that should NEVER be committed
FORBIDDEN_FILES=(
    ".env"
    ".env.local"
    ".env.production"
    "config/database.php"
    "*.pem"
    "*.key"
    "*credentials*"
)

# Check if any forbidden files are being committed
for file in "${FORBIDDEN_FILES[@]}"; do
    if git diff --cached --name-only | grep -qE "$file"; then
        echo "❌ ERROR: Attempt to commit forbidden file: $file"
        echo "This file contains sensitive information and should NOT be committed."
        echo ""
        echo "If you need to commit this file, remove it from the forbidden list in .git/hooks/pre-commit"
        echo "But make sure you understand the security implications!"
        exit 1
    fi
done

# Check for common secrets in code
if git diff --cached | grep -qE "(password|api_key|secret|token)\s*=\s*['\"][^'\"]+['\"]"; then
    echo "⚠️  WARNING: Possible hardcoded secrets detected in your changes!"
    echo "Please review your code for hardcoded passwords, API keys, or secrets."
    echo ""
    read -p "Do you want to continue anyway? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        echo "Commit aborted."
        exit 1
    fi
fi

echo "✅ Pre-commit checks passed"
exit 0
```

**Make it executable:**

```bash
chmod +x .git/hooks/pre-commit
```

### 3. Remove Credentials from Git History

**⚠️ WARNING:** This will rewrite Git history. Coordinate with all team members first.

```bash
# Method 1: Using git filter-branch (for small repos)
git filter-branch --force --index-filter \
  'git rm --cached --ignore-unmatch .env' \
  --prune-empty --tag-name-filter cat -- --all

# Method 2: Using BFG Repo-Cleaner (recommended for large repos)
# 1. Download BFG
wget https://repo1.maven.org/maven2/com/madgag/bfg/1.14.0/bfg-1.14.0.jar

# 2. Run BFG to remove .env
java -jar bfg-1.14.0.jar --delete-files .env

# 3. Clean up
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# 4. Force push (⚠️  DANGER: Coordinate with team)
git push origin --force --all
git push origin --force --tags
```

**Alternative (if you can't rewrite history):**

1. Consider repository as compromised
2. Create new repository
3. Copy code (excluding .git directory)
4. Initialize new Git repo with proper .gitignore from start

### 4. Environment Variable Management

**Use environment-specific files:**

```bash
# Development
.env.development

# Staging
.env.staging

# Production
.env.production
```

**Never commit any of these files.** Use environment variables or secret management tools instead.

**For production, consider:**
- AWS Secrets Manager
- HashiCorp Vault
- Environment variables set at server level
- Docker secrets (if using containers)

---

## ✅ Security Checklist

After completing rotation, verify:

- [ ] Database password changed
- [ ] New database user created (not using root)
- [ ] Email SMTP password updated (new app password generated)
- [ ] Encryption key rotated
- [ ] All users forced to logout and re-login
- [ ] Session cookie name changed
- [ ] `matchIP` enabled in session config
- [ ] `regenerateDestroy` enabled in session config
- [ ] `.env` in .gitignore
- [ ] Pre-commit hook installed
- [ ] Git history cleaned (or new repo created)
- [ ] All team members notified of changes
- [ ] Production credentials different from development
- [ ] Backup of database taken before changes
- [ ] Tested login functionality
- [ ] Tested registration functionality
- [ ] Tested email sending
- [ ] Checked application logs for errors
- [ ] Documented new credentials in secure password manager

---

## 📞 Emergency Contacts

If you suspect active exploitation:

1. **Immediately:** Disable database access:
   ```sql
   REVOKE ALL PRIVILEGES ON db_webspk.* FROM 'compromised_user'@'localhost';
   ```

2. **Check for unauthorized access:**
   ```sql
   SELECT * FROM sp_audit_logs ORDER BY created_at DESC LIMIT 100;
   ```

3. **Review recent member changes:**
   ```sql
   SELECT * FROM sp_members WHERE updated_at > DATE_SUB(NOW(), INTERVAL 7 DAY);
   ```

4. **Check for suspicious payments:**
   ```sql
   SELECT * FROM sp_dues_payments WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY);
   ```

---

## 🔗 Additional Resources

- [OWASP Credential Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
- [CodeIgniter Security Documentation](https://codeigniter.com/user_guide/general/security.html)
- [Git Credential Management](https://git-scm.com/book/en/v2/Git-Tools-Credential-Storage)

---

## 📝 Maintenance Schedule

**Monthly:**
- Review audit logs for suspicious activity
- Rotate database backup encryption keys

**Quarterly:**
- Rotate SMTP app passwords
- Review and update firewall rules
- Security audit of codebase

**Annually:**
- Full security audit
- Penetration testing
- Update all dependencies
- Review and rotate all service credentials

---

**Last Updated:** December 16, 2025
**Next Review:** March 16, 2026

---

## 📧 Questions?

If you have questions about this guide or need assistance with credential rotation, please:

1. Check the SECURITY.md file for reporting procedures
2. Contact the system administrator
3. DO NOT post credentials or security issues in public channels

**Remember: Security is everyone's responsibility! 🔒**
