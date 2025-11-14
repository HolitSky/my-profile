# 🌍 Environment Setup Guide

## Auto Environment Detection (Recommended)

CMS ini menggunakan **auto-detection** untuk membedakan Local vs Production environment.

---

## 📁 File Configuration

### Main Config File: `config/config.php`

File ini **otomatis detect** environment berdasarkan hostname:

```php
// Auto-detect environment
function getEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return 'local';
    }
    
    return 'production';
}
```

---

## 🏠 Local Environment Setup

### 1. Edit `config/config.php` - Section LOCAL

```php
// ============================================
// LOCAL ENVIRONMENT CONFIGURATION
// ============================================
if ($environment === 'local') {
    
    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'portfolio_cms');    // ← Database local Anda
    define('DB_USER', 'root');             // ← Username (root untuk XAMPP/WAMP)
    define('DB_PASS', '');                 // ← Password (kosong untuk XAMPP/WAMP)
    
    // Site Configuration
    define('SITE_URL', 'http://localhost/my-profile');  // ← URL local Anda
    
    // Debug Mode - ENABLED
    define('DEBUG_MODE', true);
}
```

### 2. Create Database Local

#### Via phpMyAdmin (XAMPP/WAMP):
```sql
CREATE DATABASE portfolio_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Via Command Line:
```bash
mysql -u root -p
CREATE DATABASE portfolio_cms;
exit;
```

### 3. Import Schema

```bash
# Via phpMyAdmin
1. Select database: portfolio_cms
2. Import → Choose file: database/schema.sql
3. Click Go

# Via Command Line
mysql -u root -p portfolio_cms < database/schema.sql
```

### 4. Test Connection

```
http://localhost/my-profile/test-db.php
```

**Expected Result:**
- ✅ Green success message
- Environment badge: **LOCAL**
- All 9 tables listed
- Debug mode: ON

---

## 🌐 Production Environment Setup (Hostinger)

### 1. Edit `config/config.php` - Section PRODUCTION

```php
// ============================================
// PRODUCTION ENVIRONMENT CONFIGURATION
// ============================================
else {
    
    // Database Configuration - HOSTINGER
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u123456_portfolio');    // ← GANTI dengan DB name Hostinger
    define('DB_USER', 'u123456_admin');        // ← GANTI dengan DB user Hostinger
    define('DB_PASS', 'your_secure_password'); // ← GANTI dengan DB password Hostinger
    
    // Site Configuration
    define('SITE_URL', 'https://khalidsaifullah.me'); // ← GANTI dengan domain Anda
    
    // Debug Mode - DISABLED
    define('DEBUG_MODE', false);
}
```

### 2. Create Database di Hostinger

#### Via cPanel:
```
1. Login cPanel Hostinger
2. MySQL Databases
3. Create New Database
   - Name: portfolio (akan jadi: u123456_portfolio)
4. Create New User
   - Username: admin (akan jadi: u123456_admin)
   - Password: [generate strong password]
5. Add User To Database
   - Select: ALL PRIVILEGES
6. CATAT KREDENSIAL!
```

### 3. Import Schema

```
1. cPanel → phpMyAdmin
2. Select database: u123456_portfolio
3. Import tab
4. Choose file: database/schema.sql
5. Click Go
```

### 4. Upload Files

```
Upload semua file ke: public_html/
```

### 5. Test Connection

```
https://yourdomain.com/test-db.php
```

**Expected Result:**
- ✅ Green success message
- Environment badge: **PRODUCTION**
- All 9 tables listed
- Debug mode: OFF

### 6. Delete Test File

```bash
# Via File Manager atau FTP
Delete: test-db.php
```

---

## 🔄 How Auto-Detection Works

### Detection Logic:

```php
function getEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Local indicators
    $localHosts = ['localhost', '127.0.0.1', '::1', 'localhost:8080'];
    
    if (in_array($host, $localHosts) || strpos($host, '.local') !== false) {
        return 'local';
    }
    
    return 'production';
}
```

### Triggers:

**Local Environment** jika:
- `localhost`
- `127.0.0.1`
- `::1` (IPv6 localhost)
- `localhost:8080`
- `*.local` (e.g., `mysite.local`)

**Production Environment** jika:
- Domain name (e.g., `khalidsaifullah.me`)
- IP address (e.g., `192.168.1.100`)
- Any other hostname

---

## 🎯 Configuration Differences

| Feature | Local | Production |
|---------|-------|------------|
| **Debug Mode** | ✅ ON | ❌ OFF |
| **Error Display** | ✅ Show | ❌ Hide |
| **Error Reporting** | E_ALL | 0 (none) |
| **Session Lifetime** | 2 hours | 1 hour |
| **Database** | Local MySQL | Hostinger MySQL |
| **URL** | http://localhost | https://domain.com |

---

## 🧪 Testing Connection

### Method 1: Via Browser

```
Local:      http://localhost/my-profile/test-db.php
Production: https://yourdomain.com/test-db.php
```

### Method 2: Via PHP CLI

```bash
php test-db.php
```

### Method 3: Via Code

```php
<?php
require_once 'config/config.php';

try {
    $db = getDB();
    echo "✅ Connected to: " . DB_NAME . "\n";
    echo "Environment: " . ENVIRONMENT . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
```

---

## 🔒 Security Best Practices

### 1. Never Commit Credentials

```bash
# Add to .gitignore
config/config.php
config/database.php
.env
```

### 2. Use Strong Passwords (Production)

```php
// ❌ BAD
define('DB_PASS', 'admin123');

// ✅ GOOD
define('DB_PASS', 'xK9$mP2#vL8@nQ5');
```

### 3. Disable Debug in Production

```php
// Production
define('DEBUG_MODE', false);
ini_set('display_errors', 0);
error_reporting(0);
```

### 4. Use HTTPS in Production

```php
define('SITE_URL', 'https://yourdomain.com'); // Not http://
```

---

## 🐛 Troubleshooting

### Connection Failed in Local

**Problem:** Can't connect to local database

**Solutions:**
1. Check XAMPP/WAMP is running
2. Verify MySQL service started
3. Check username: `root`
4. Check password: empty or `root`
5. Verify database exists: `portfolio_cms`

```bash
# Test MySQL
mysql -u root -p
SHOW DATABASES;
```

### Connection Failed in Production

**Problem:** Can't connect to Hostinger database

**Solutions:**
1. Verify credentials in `config/config.php`
2. Check database name format: `u123456_portfolio`
3. Check user format: `u123456_admin`
4. Verify user has privileges
5. Test in phpMyAdmin first

### Wrong Environment Detected

**Problem:** Shows "Production" when testing locally

**Solutions:**
1. Check URL: must be `localhost` or `127.0.0.1`
2. Clear browser cache
3. Check `$_SERVER['HTTP_HOST']` value:
   ```php
   echo $_SERVER['HTTP_HOST'];
   ```

### Tables Not Found

**Problem:** Database connected but no tables

**Solution:**
```sql
-- Import schema
SOURCE /path/to/database/schema.sql;

-- Or via phpMyAdmin
Import → database/schema.sql
```

---

## 📝 Quick Reference

### Local Development

```bash
# 1. Start XAMPP/WAMP
# 2. Create database
mysql -u root -p
CREATE DATABASE portfolio_cms;

# 3. Import schema
mysql -u root -p portfolio_cms < database/schema.sql

# 4. Edit config/config.php (LOCAL section)
DB_NAME: portfolio_cms
DB_USER: root
DB_PASS: (empty)
SITE_URL: http://localhost/my-profile

# 5. Test
http://localhost/my-profile/test-db.php
```

### Production Deployment

```bash
# 1. Create database in cPanel
# 2. Import schema via phpMyAdmin
# 3. Edit config/config.php (PRODUCTION section)
DB_NAME: u123456_portfolio
DB_USER: u123456_admin
DB_PASS: your_password
SITE_URL: https://yourdomain.com

# 4. Upload files to public_html/
# 5. Test
https://yourdomain.com/test-db.php

# 6. Delete test file
rm test-db.php
```

---

## ✅ Checklist

### Local Setup
- [ ] XAMPP/WAMP installed
- [ ] MySQL service running
- [ ] Database created: `portfolio_cms`
- [ ] Schema imported
- [ ] `config/config.php` edited (LOCAL section)
- [ ] Test connection: `test-db.php`
- [ ] Admin login works

### Production Setup
- [ ] Hostinger account ready
- [ ] Database created in cPanel
- [ ] User created with privileges
- [ ] Schema imported via phpMyAdmin
- [ ] `config/config.php` edited (PRODUCTION section)
- [ ] Files uploaded to `public_html/`
- [ ] Test connection: `test-db.php`
- [ ] Test file deleted
- [ ] HTTPS enabled
- [ ] Admin password changed

---

## 🎉 Done!

Your environment is now configured for both Local and Production!

**Key Points:**
- ✅ Auto-detection works automatically
- ✅ No need to change code when deploying
- ✅ Debug mode auto-adjusts
- ✅ Secure by default
- ✅ Easy to maintain

**Next Steps:**
1. Test locally first
2. Deploy to production
3. Delete test files
4. Start managing content!

---

**Version:** 1.0.0
**Last Updated:** January 2025
