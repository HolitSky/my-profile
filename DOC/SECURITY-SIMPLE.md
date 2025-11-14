# 🔒 Simple Security System

## Super Simple! Cuma Edit 1 File

Tidak perlu `.htaccess` yang kompleks. Cukup edit **1 baris** di `security-config.php`.

---

## 🎯 Cara Pakai:

### Method 1: Via Dashboard (Recommended)

**Akses:**
```
http://localhost/my-profile/security-dashboard.php
```

**Klik tombol:**
- 🟢 **Enable Development Mode** → Files accessible
- 🔴 **Enable Production Mode** → Files blocked

**Done!** Instant, no restart needed.

---

### Method 2: Edit Manual (Super Simple)

**Edit file:** `security-config.php`

**Cari baris ini:**
```php
define('ENABLE_UTILITY_FILES', true);  // ← GANTI ini!
```

**Development Mode (Local):**
```php
define('ENABLE_UTILITY_FILES', true);   // ✅ Files accessible
```

**Production Mode (Hostinger):**
```php
define('ENABLE_UTILITY_FILES', false);  // ❌ Files blocked
```

**Save file. Done!**

---

## 📊 What Gets Protected:

### Protected Files:
```
✓ test-db.php               → Database tester
✓ generate-password.php     → Password generator
✓ database/run-migrations.php → Migration runner
✓ security-dashboard.php    → This dashboard
```

### Always Accessible:
```
✓ index.html                → Your portfolio
✓ admin/login.php           → Admin panel
✓ api/index.php             → REST API
✓ assets/*                  → Images, CSS, JS
```

---

## 🚀 Quick Reference:

### Development (Local):
```php
// security-config.php
define('ENABLE_UTILITY_FILES', true);
```
**Result:**
- ✅ test-db.php works
- ✅ generate-password.php works
- ✅ run-migrations.php works
- ✅ Only from localhost

### Production (Hostinger):
```php
// security-config.php
define('ENABLE_UTILITY_FILES', false);
```
**Result:**
- ❌ test-db.php blocked
- ❌ generate-password.php blocked
- ❌ run-migrations.php blocked
- ✅ Admin panel still works
- ✅ API still works

---

## 🎯 Deployment Checklist:

### Before Upload to Hostinger:

1. **Open:** `security-config.php`

2. **Change:**
```php
define('ENABLE_UTILITY_FILES', false);  // ← Set to false
```

3. **Save & Upload**

4. **Verify:**
```
https://yourdomain.com/test-db.php → Should show 403 Forbidden
https://yourdomain.com/admin/login.php → Should work
```

**Done!** 🎉

---

## 💡 Benefits:

✅ **Super Simple** - Cuma 1 baris code
✅ **No .htaccess Issues** - Pure PHP protection
✅ **Instant Toggle** - No server restart
✅ **Visual Dashboard** - Easy to use
✅ **Auto Localhost Check** - Extra security layer

---

## 🔧 Troubleshooting:

### Can't Access Dashboard

**Error:** 403 Forbidden

**Solution:**
```php
// Edit security-config.php
define('ENABLE_UTILITY_FILES', true);
```

### Files Still Accessible in Production

**Problem:** Utility files work on Hostinger

**Solution:**
```php
// Make sure security-config.php has:
define('ENABLE_UTILITY_FILES', false);
```

---

## 📝 Summary:

**1 File to Rule Them All:**
```
security-config.php
```

**1 Line to Change:**
```php
define('ENABLE_UTILITY_FILES', true/false);
```

**2 Modes:**
- `true` = Development (files accessible)
- `false` = Production (files blocked)

**That's it!** 🚀

---

**No complex .htaccess, no server config, just simple PHP!**
