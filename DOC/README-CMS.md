# Portfolio CMS - Setup Guide

## 📋 Overview
Simple PHP-based CMS untuk mengelola portfolio website Anda. Compatible dengan Hostinger shared hosting dan MySQL database.

## 🚀 Features
- ✅ Admin Dashboard dengan statistik
- ✅ Manage About section
- ✅ Manage Skills dengan proficiency level
- ✅ Manage Experience & Education
- ✅ Manage Portfolio projects
- ✅ Manage Services & Contact info
- ✅ REST API untuk frontend
- ✅ Secure authentication
- ✅ Responsive admin panel

## 📦 Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache dengan mod_rewrite (sudah ada di Hostinger)

## 🔧 Installation

### Step 1: Upload Files
Upload semua file ke hosting Hostinger Anda via FTP atau File Manager.

### Step 2: Create Database
1. Login ke cPanel Hostinger
2. Buka **MySQL Databases**
3. Create database baru (contoh: `u123456_portfolio`)
4. Create user baru dan assign ke database
5. Catat: **Database Name**, **Username**, **Password**

### Step 3: Import Database Schema
1. Buka **phpMyAdmin** di cPanel
2. Select database yang baru dibuat
3. Klik tab **Import**
4. Upload file `database/schema.sql`
5. Klik **Go**

### Step 4: Configure Database
Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456_portfolio');  // Ganti dengan nama database Anda
define('DB_USER', 'u123456_user');       // Ganti dengan username database
define('DB_PASS', 'your_password');      // Ganti dengan password database
define('SITE_URL', 'https://yourdomain.com'); // Ganti dengan URL website Anda
```

### Step 5: Test Installation
1. Buka `https://yourdomain.com/admin/login.php`
2. Login dengan:
   - **Username**: `admin`
   - **Password**: `admin123`
3. **PENTING**: Ganti password setelah login pertama!

## 🎯 Usage

### Admin Panel
Access: `https://yourdomain.com/admin/`

**Menu yang tersedia:**
- **Dashboard** - Overview dan statistik
- **About** - Edit about section
- **Skills** - Manage skills dan proficiency
- **Experience** - Manage work experience
- **Education** - Manage education history
- **Portfolio** - Manage project portfolio
- **Services** - Manage services yang ditawarkan
- **Contact Info** - Update contact information

### API Endpoints
Frontend dapat fetch data dari:
```
GET https://yourdomain.com/api/index.php
```

Response format:
```json
{
  "success": true,
  "data": {
    "about": {...},
    "skills": [...],
    "experience": [...],
    "education": [...],
    "portfolio": [...],
    "services": [...],
    "contact": {...}
  },
  "timestamp": 1234567890
}
```

## 🔒 Security

### Change Default Password
1. Login ke admin panel
2. Buka phpMyAdmin
3. Jalankan query:
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_NEW_HASH' 
WHERE username = 'admin';
```

Atau generate hash di PHP:
```php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

### Protect Admin Directory (Optional)
Tambahkan `.htaccess` di folder `admin/`:
```apache
AuthType Basic
AuthName "Admin Area"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

## 📁 File Structure
```
my-profile/
├── admin/                  # Admin panel
│   ├── includes/          # Header & footer templates
│   ├── login.php          # Login page
│   ├── dashboard.php      # Main dashboard
│   ├── about.php          # About management
│   ├── skills.php         # Skills management
│   ├── experience.php     # Experience management
│   ├── education.php      # Education management
│   ├── portfolio.php      # Portfolio management
│   ├── services.php       # Services management
│   ├── contact.php        # Contact info management
│   └── logout.php         # Logout handler
├── api/                   # REST API
│   └── index.php          # Main API endpoint
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── auth.php           # Authentication helpers
├── database/              # Database files
│   └── schema.sql         # Database schema
├── uploads/               # Upload directory (create this)
├── .htaccess             # Apache configuration
└── index.html            # Frontend (existing)
```

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan kredensial database benar
- Cek apakah database sudah di-import
- Pastikan user memiliki akses ke database

### 500 Internal Server Error
- Cek file permissions (755 untuk folder, 644 untuk file)
- Cek error log di cPanel
- Pastikan PHP version minimal 7.4

### Cannot Login
- Clear browser cache dan cookies
- Pastikan table `admin_users` ada dan terisi
- Reset password via phpMyAdmin

### API Returns Empty Data
- Pastikan database terisi dengan data
- Cek `config/database.php` sudah benar
- Test API endpoint langsung di browser

## 🔄 Update Frontend

Untuk mengintegrasikan API ke `index.html`, tambahkan JavaScript:

```javascript
// Fetch data from API
fetch('/api/index.php')
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      const data = result.data;
      
      // Update about section
      document.querySelector('.about-text p').textContent = data.about.description;
      
      // Update skills
      // ... dst
    }
  });
```

## 📞 Support
Jika ada masalah, cek:
1. Error log di cPanel
2. Browser console untuk JavaScript errors
3. Database connection di phpMyAdmin

## 📝 Notes
- Backup database secara berkala
- Ganti password default setelah install
- Gunakan HTTPS untuk keamanan
- Test di local dulu sebelum upload ke production

## 🎉 Happy Coding!
CMS ini dibuat khusus untuk portfolio Khalid Saifullah dengan fokus pada kesederhanaan dan kompatibilitas dengan Hostinger shared hosting.
