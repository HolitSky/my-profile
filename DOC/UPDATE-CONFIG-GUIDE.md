# 🔄 Config Update Guide

## ⚠️ IMPORTANT: Update Required Files

Karena kita sudah membuat `config/config.php` yang baru dengan auto environment detection, Anda perlu update beberapa file.

---

## 📝 Files Yang Perlu Di-Update

### Option 1: Update Manual (Recommended)

Ganti semua baris ini:
```php
require_once __DIR__ . '/../config/database.php';
```

Menjadi:
```php
require_once __DIR__ . '/../config/config.php';
```

**Files yang perlu di-update:**

#### Admin Files (9 files):
1. `admin/login.php`
2. `admin/dashboard.php`
3. `admin/about.php`
4. `admin/skills.php`
5. `admin/experience.php`
6. `admin/education.php`
7. `admin/portfolio.php`
8. `admin/services.php`
9. `admin/contact.php`

#### API File (1 file):
10. `api/index.php`

#### Test File (1 file):
11. `test-connection.php` (optional, bisa pakai `test-db.php` yang baru)

---

### Option 2: Keep Old database.php (Backward Compatible)

Jika Anda ingin tetap menggunakan `config/database.php` yang lama, buat file ini:

**File: `config/database.php`**
```php
<?php
/**
 * Database Configuration (Backward Compatible)
 * This file loads the new config.php for backward compatibility
 */

// Load new config system
require_once __DIR__ . '/config.php';

// All constants are already defined in config.php
// This file exists only for backward compatibility
```

Dengan cara ini, semua file lama tetap berfungsi tanpa perlu update!

---

## 🎯 Recommended Approach

### Step 1: Create Backward Compatible database.php

```bash
# Create file: config/database.php
```

```php
<?php
// Backward compatibility wrapper
require_once __DIR__ . '/config.php';
```

### Step 2: Test

```bash
# Test local
http://localhost/my-profile/test-db.php

# Test admin
http://localhost/my-profile/admin/login.php
```

### Step 3: Deploy

```bash
# Upload files to Hostinger
# Test production
https://yourdomain.com/test-db.php
```

---

## 🔧 Quick Fix Script

Buat file `update-config.php` di root folder:

```php
<?php
/**
 * Quick Update Script
 * Updates all require statements to use new config.php
 */

$files = [
    'admin/login.php',
    'admin/dashboard.php',
    'admin/about.php',
    'admin/skills.php',
    'admin/experience.php',
    'admin/education.php',
    'admin/portfolio.php',
    'admin/services.php',
    'admin/contact.php',
    'api/index.php',
];

$search = "require_once __DIR__ . '/../config/database.php';";
$replace = "require_once __DIR__ . '/../config/config.php';";

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $updated = str_replace($search, $replace, $content);
        file_put_contents($file, $updated);
        echo "✅ Updated: $file\n";
    } else {
        echo "❌ Not found: $file\n";
    }
}

echo "\n🎉 Update complete!\n";
echo "Delete this file after running: update-config.php\n";
?>
```

**Run:**
```bash
php update-config.php
```

---

## ✅ Verification

After update, verify:

### 1. Test Database Connection
```
http://localhost/my-profile/test-db.php
```

Should show:
- ✅ Environment: LOCAL
- ✅ Connection successful
- ✅ All tables listed

### 2. Test Admin Login
```
http://localhost/my-profile/admin/login.php
```

Should:
- ✅ Load without errors
- ✅ Accept login (admin/admin123)
- ✅ Redirect to dashboard

### 3. Test API
```
http://localhost/my-profile/api/index.php
```

Should return:
- ✅ JSON response
- ✅ "success": true
- ✅ Data populated

---

## 🎯 Summary

**Easiest Way:**
1. Create `config/database.php` as wrapper
2. Point to `config/config.php`
3. No other changes needed!

**Best Way:**
1. Update all require statements
2. Use `config/config.php` directly
3. Remove old `config/database.php`

**Quick Way:**
1. Run `update-config.php` script
2. Auto-update all files
3. Test and done!

---

Choose the method that works best for you! 🚀
