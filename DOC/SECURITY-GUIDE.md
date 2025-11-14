# 🔒 Security Protection Guide

## File Protection System

Daripada delete files, kita protect dengan `.htaccess` supaya bisa dipakai lagi kalau butuh.

---

## 🛡️ Protected Files

### Utility Files (Localhost Only)
- ✅ `test-db.php` - Database connection tester
- ✅ `generate-password.php` - Password hash generator
- ✅ `database/run-migrations.php` - Migration runner

### Always Protected
- ✅ `config/` directory - Configuration files
- ✅ `database/*.sql` - SQL migration & seeder files
- ✅ `.git/` directory - Git repository
- ✅ `.env` files - Environment variables

---

## 🎯 Protection Modes

### 1. Development Mode (Local)
```
✓ Utility files accessible from localhost only
✓ test-db.php works
✓ generate-password.php works
✓ run-migrations.php works
✗ Blocked from internet
```

**Use when:** Developing locally

### 2. Production Mode (Hostinger)
```
✗ All utility files completely blocked
✗ test-db.php blocked
✗ generate-password.php blocked
✗ run-migrations.php blocked
✓ Maximum security
```

**Use when:** Deploying to production

---

## 🚀 How to Use

### Method 1: Web Interface (Easy)

1. **Access Toggle Page:**
```
http://localhost/my-profile/toggle-protection.php
```

2. **Select Mode:**
   - Click "Enable Development Mode" for local
   - Click "Enable Production Mode" for deployment

3. **Verify:**
   - Check status table
   - Test access to utility files

### Method 2: Manual .htaccess Edit

#### Enable Development Mode:
Edit `.htaccess`, ensure these lines are **active**:
```apache
<Files "test-db.php">
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.1
    Allow from ::1
    Allow from localhost
</Files>
```

#### Enable Production Mode:
Edit `.htaccess`, **uncomment** these lines:
```apache
<Files "test-db.php">
    Order Deny,Allow
    Deny from all
</Files>
```

---

## 📁 File Structure

```
my-profile/
├── .htaccess                    ← Main protection rules
├── test-db.php                  ← Protected (localhost only)
├── generate-password.php        ← Protected (localhost only)
├── toggle-protection.php        ← Protected (localhost only)
│
├── config/
│   ├── .htaccess               ← Deny all access
│   ├── config.php              ← Protected
│   └── auth.php                ← Protected
│
├── database/
│   ├── .htaccess               ← Deny all access
│   ├── migrations.sql          ← Protected
│   ├── seeders.sql             ← Protected
│   └── run-migrations.php      ← Protected (localhost only)
│
└── admin/                       ← Accessible (with login)
```

---

## 🔐 Protection Rules

### 1. Localhost-Only Access

Files accessible only from `127.0.0.1`, `::1`, or `localhost`:
```apache
<Files "test-db.php">
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.1
    Allow from ::1
    Allow from localhost
</Files>
```

**Result:**
- ✅ Works: `http://localhost/my-profile/test-db.php`
- ❌ Blocked: `http://yourdomain.com/test-db.php`

### 2. Complete Block

Block all access (production):
```apache
<Files "test-db.php">
    Order Deny,Allow
    Deny from all
</Files>
```

**Result:**
- ❌ Blocked from everywhere
- Returns: 403 Forbidden

### 3. Directory Protection

Block entire directories:
```apache
<DirectoryMatch "^.*/config">
    Order Deny,Allow
    Deny from all
</DirectoryMatch>
```

**Result:**
- ❌ Cannot access any file in `config/` directory
- ❌ Cannot list directory contents

---

## 🧪 Testing Protection

### Test Localhost Access (Development Mode)

1. **From Browser (localhost):**
```
http://localhost/my-profile/test-db.php
```
**Expected:** ✅ Page loads

2. **From External IP:**
```
http://192.168.1.100/my-profile/test-db.php
```
**Expected:** ❌ 403 Forbidden

### Test Production Block

1. **Enable Production Mode**

2. **Try accessing:**
```
http://localhost/my-profile/test-db.php
```
**Expected:** ❌ 403 Forbidden

---

## 🎯 Deployment Checklist

### Before Deploying to Hostinger:

- [ ] Enable Production Mode
- [ ] Test all utility files are blocked
- [ ] Verify admin panel still works
- [ ] Verify API still works
- [ ] Check config files protected
- [ ] Upload to Hostinger
- [ ] Test on production URL

### After Deployment:

- [ ] Verify utility files return 403
- [ ] Test admin login works
- [ ] Test API endpoint works
- [ ] Change admin password
- [ ] Enable HTTPS
- [ ] Backup database

---

## 🔧 Troubleshooting

### "403 Forbidden" on Localhost

**Problem:** Can't access test-db.php on localhost

**Solutions:**
1. Check Development Mode enabled
2. Verify `.htaccess` has localhost rules
3. Check Apache allows `.htaccess` overrides
4. Restart Apache

### Files Still Accessible in Production

**Problem:** Utility files accessible from internet

**Solutions:**
1. Enable Production Mode
2. Check `.htaccess` uploaded correctly
3. Verify Apache mod_rewrite enabled
4. Clear browser cache

### Admin Panel Blocked

**Problem:** Can't access admin panel

**Solutions:**
1. Admin panel should NOT be blocked
2. Check `.htaccess` doesn't block `admin/` folder
3. Verify only utility files are protected
4. Check file permissions (755/644)

---

## 📊 Protection Comparison

| Feature | No Protection | Development Mode | Production Mode |
|---------|---------------|------------------|-----------------|
| test-db.php | ⚠️ Public | ✅ Localhost Only | ✅ Blocked |
| generate-password.php | ⚠️ Public | ✅ Localhost Only | ✅ Blocked |
| run-migrations.php | ⚠️ Public | ✅ Localhost Only | ✅ Blocked |
| config/ files | ⚠️ Public | ✅ Blocked | ✅ Blocked |
| database/ files | ⚠️ Public | ✅ Blocked | ✅ Blocked |
| Admin Panel | ✅ Public | ✅ Public | ✅ Public |
| API | ✅ Public | ✅ Public | ✅ Public |
| Frontend | ✅ Public | ✅ Public | ✅ Public |

---

## 🎓 Best Practices

### Development (Local)

```bash
✓ Use Development Mode
✓ Keep utility files accessible
✓ Test features freely
✓ Debug with test-db.php
✓ Generate passwords as needed
```

### Production (Hostinger)

```bash
✓ Use Production Mode
✓ Block all utility files
✓ Enable HTTPS
✓ Change default passwords
✓ Regular backups
✓ Monitor error logs
```

---

## 🔄 Quick Commands

### Enable Development Mode
```
http://localhost/my-profile/toggle-protection.php
→ Click "Enable Development Mode"
```

### Enable Production Mode
```
http://localhost/my-profile/toggle-protection.php
→ Click "Enable Production Mode"
```

### Check Status
```
http://localhost/my-profile/toggle-protection.php
→ View status table
```

### Test Protection
```bash
# Should work in dev mode
http://localhost/my-profile/test-db.php

# Should be blocked in prod mode
http://yourdomain.com/test-db.php
```

---

## ⚠️ Important Notes

### DO NOT Delete These Files:
- ✅ Keep `test-db.php` - useful for troubleshooting
- ✅ Keep `generate-password.php` - needed for password reset
- ✅ Keep `run-migrations.php` - needed for database reset
- ✅ Keep `toggle-protection.php` - manage security

### Instead:
- ✅ Protect with `.htaccess`
- ✅ Use localhost-only access
- ✅ Enable production mode when deploying
- ✅ Keep files for future use

### Files You CAN Delete:
- ❌ `.htaccess-security` (example only)
- ❌ `README-*.md` (documentation)
- ❌ `test.html` (if exists)

---

## 🎉 Summary

### What We Did:
1. ✅ Created `.htaccess` protection rules
2. ✅ Protected utility files (localhost only)
3. ✅ Protected config & database directories
4. ✅ Created toggle system for easy switching
5. ✅ Documented everything

### Benefits:
- ✅ No need to delete files
- ✅ Easy to switch between dev/prod
- ✅ Secure by default
- ✅ Files available when needed
- ✅ Professional security setup

### Result:
- ✅ Development: Full access from localhost
- ✅ Production: Everything blocked
- ✅ Admin panel: Always accessible (with login)
- ✅ API: Always accessible
- ✅ Config: Always protected

---

**Your CMS is now professionally secured! 🔒**

Use Development Mode locally, Production Mode on Hostinger.

---

**Version:** 1.0.0
**Last Updated:** January 2025
**Security Level:** High
