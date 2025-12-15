# Security Notice

## ⚠️ IMPORTANT: Credential Exposure in Git History

### Issue
The `.env` file containing sensitive credentials was previously committed to the git repository. This includes:
- Database password
- Email SMTP credentials
- Other sensitive configuration

### Impact
Anyone with access to the repository history can view these credentials.

### Immediate Actions Required

1. **Change All Exposed Credentials:**
   - ✅ Change database password immediately
   - ✅ Regenerate Gmail App Password (revoke old one)
   - ✅ Review access logs for unauthorized access

2. **For New Setup:**
   - Copy `.env.example` to `.env`
   - Fill in your own credentials
   - NEVER commit `.env` to version control

3. **Optional - Clean Git History:**
   If you want to remove sensitive data from git history:
   ```bash
   # WARNING: This rewrites history and requires force push
   git filter-branch --force --index-filter \
     "git rm --cached --ignore-unmatch .env" \
     --prune-empty --tag-name-filter cat -- --all

   # Force push to remote (coordinate with team first!)
   git push origin --force --all
   ```

## Security Best Practices

### 1. Environment Variables
- ✅ Use `.env.example` as template
- ✅ Never commit `.env` with real values
- ✅ Use different credentials for dev/staging/production
- ✅ Rotate credentials regularly

### 2. Application Security
- ✅ Enable CSRF protection (now enabled)
- ✅ Enable security filters
- ✅ Use HTTPS in production
- ✅ Set `CI_ENVIRONMENT = production` in production
- ✅ Disable debug mode in production

### 3. Database Security
- ✅ Use strong passwords
- ✅ Limit database user privileges
- ✅ Enable MySQL SSL connections
- ✅ Regular backups with encryption

### 4. Email Security
- ✅ Use Gmail App Passwords (not account password)
- ✅ Enable 2-factor authentication
- ✅ Monitor email sending logs
- ✅ Implement rate limiting for email sending

### 5. Session Security
- ✅ Use database session handler
- ✅ Enable secure cookies in production
- ✅ Set appropriate session timeout
- ✅ Implement session regeneration

### 6. File Upload Security
- ✅ Validate file types and sizes
- ✅ Store uploads outside webroot when possible
- ✅ Scan uploads for malware
- ✅ Implement access controls

### 7. Audit & Monitoring
- ✅ Enable audit logging (implemented)
- ✅ Monitor failed login attempts
- ✅ Review security logs regularly
- ✅ Set up alerts for suspicious activity

## Reporting Security Issues

If you discover a security vulnerability, please email: security@spk-kampus.id

**Do not** open public GitHub issues for security vulnerabilities.

## Security Updates

- 2025-12-15: Initial security audit completed
  - Removed .env from version control
  - Created .env.example template
  - Enabled CSRF protection
  - Enabled security filters
  - Documented credential exposure issue

## Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [CodeIgniter 4 Security](https://codeigniter.com/user_guide/concepts/security.html)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
