# 🚀 Quick Start Guide - Portfolio CMS

## ⚡ Setup dalam 5 Menit

### 1️⃣ Upload ke Hostinger
Upload semua file via FTP atau File Manager cPanel.

### 2️⃣ Buat Database
1. Login cPanel → **MySQL Databases**
2. Create database baru (contoh: `u123_portfolio`)
3. Create user dan assign ke database
4. **Catat kredensial!**

### 3️⃣ Import Database
1. cPanel → **phpMyAdmin**
2. Select database Anda
3. Tab **Import** → Upload `database/schema.sql`
4. Klik **Go**

### 4️⃣ Configure
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123_portfolio');     // ← Database name
define('DB_USER', 'u123_user');          // ← Username
define('DB_PASS', 'your_password');      // ← Password
define('SITE_URL', 'https://yourdomain.com'); // ← Your URL
```

### 5️⃣ Login
Buka: `https://yourdomain.com/admin/login.php`
- Username: `admin`
- Password: `admin123`

**⚠️ PENTING: Ganti password setelah login!**

---

## 📱 Cara Pakai

### Admin Panel
URL: `https://yourdomain.com/admin/`

**Menu:**
- 📊 **Dashboard** - Overview
- 👤 **About** - Edit profil
- ⭐ **Skills** - Manage skills
- 💼 **Experience** - Work history
- 🎓 **Education** - Education history
- 📁 **Portfolio** - Projects
- ⚙️ **Services** - Services offered
- 📧 **Contact** - Contact info

### API Endpoint
```
GET https://yourdomain.com/api/index.php
```

Returns JSON dengan semua data portfolio.

---

## 🔗 Integrasi ke Frontend

### Option 1: Otomatis (Recommended)
Tambahkan di `index.html` sebelum `</body>`:
```html
<script src="./assets/js/cms-integration.js"></script>
```

### Option 2: Manual
```javascript
fetch('/api/index.php')
  .then(res => res.json())
  .then(result => {
    const data = result.data;
    // Update your HTML with data
  });
```

---

## 🔒 Keamanan

### Ganti Password
Via phpMyAdmin:
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_NEW_HASH' 
WHERE username = 'admin';
```

Generate hash:
```php
<?php echo password_hash('new_password', PASSWORD_DEFAULT); ?>
```

### Protect Admin (Optional)
Tambah `.htaccess` di folder `admin/`:
```apache
AuthType Basic
AuthName "Admin Area"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

---

## 🐛 Troubleshooting

### Database Connection Error
✅ Cek kredensial di `config/database.php`
✅ Pastikan database sudah di-import
✅ Cek user punya akses ke database

### 500 Error
✅ File permissions: 755 (folder), 644 (file)
✅ Cek error log di cPanel
✅ PHP version minimal 7.4

### Cannot Login
✅ Clear browser cache
✅ Cek table `admin_users` ada
✅ Reset password via phpMyAdmin

### API Empty
✅ Database ada data
✅ Config database benar
✅ Test: `yourdomain.com/api/index.php`

---

## 📂 File Structure
```
my-profile/
├── admin/          # Admin panel
├── api/            # REST API
├── config/         # Configuration
├── database/       # SQL schema
├── assets/         # Frontend assets
└── index.html      # Frontend
```

---

## 🎯 Next Steps

1. ✅ Login ke admin panel
2. ✅ Update About section
3. ✅ Add Skills
4. ✅ Add Experience & Education
5. ✅ Upload Portfolio projects
6. ✅ Test API endpoint
7. ✅ Integrate ke frontend
8. ✅ Ganti password default
9. ✅ Backup database

---

## 💡 Tips

- **Backup** database secara berkala
- Gunakan **HTTPS** untuk keamanan
- Test di **local** dulu sebelum production
- Optimize **images** sebelum upload
- Cache API response di frontend

---

## 📞 Need Help?

1. Cek error log di cPanel
2. Browser console untuk JS errors
3. Test database connection di phpMyAdmin
4. Baca README-CMS.md untuk detail lengkap

---

**Happy Coding! 🎉**

Made with ❤️ for Khalid Saifullah Portfolio
