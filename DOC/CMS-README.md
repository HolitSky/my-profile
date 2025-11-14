# 🎨 Portfolio CMS - Complete Guide

> Simple, powerful CMS untuk portfolio website. Compatible dengan Hostinger shared hosting + MySQL.

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Quick Start](#quick-start)
5. [Detailed Setup](#detailed-setup)
6. [Usage Guide](#usage-guide)
7. [API Documentation](#api-documentation)
8. [Frontend Integration](#frontend-integration)
9. [Security](#security)
10. [Troubleshooting](#troubleshooting)
11. [FAQ](#faq)

---

## 🎯 Overview

Portfolio CMS adalah sistem manajemen konten berbasis PHP yang dirancang khusus untuk portfolio website. Sistem ini memungkinkan Anda mengelola semua konten portfolio (About, Skills, Experience, Education, Projects, dll) melalui admin panel yang user-friendly.

### Why This CMS?

✅ **Simple** - Easy to setup dan use
✅ **Lightweight** - Hanya ~50KB PHP files
✅ **Hostinger Compatible** - Works perfect di shared hosting
✅ **No Framework** - Pure PHP, no dependencies
✅ **REST API** - JSON API untuk frontend
✅ **Secure** - Built-in authentication & CSRF protection
✅ **Responsive** - Mobile-friendly admin panel

---

## ✨ Features

### Admin Panel
- 📊 **Dashboard** - Statistics & quick actions
- 👤 **About Management** - Edit profile & bio
- ⭐ **Skills Management** - Add/edit skills dengan proficiency level
- 💼 **Experience Management** - Work history timeline
- 🎓 **Education Management** - Education history
- 📁 **Portfolio Management** - Project showcase
- ⚙️ **Services Management** - Services offered
- 📧 **Contact Management** - Contact information

### Technical Features
- 🔐 Secure authentication system
- 🔄 RESTful API endpoints
- 💾 MySQL database storage
- 📱 Responsive Bootstrap UI
- 🎨 Modern gradient design
- ⚡ Fast & optimized
- 🔒 CSRF protection
- 📝 Input sanitization

---

## 📋 Requirements

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: with mod_rewrite
- **Extensions**: PDO, PDO_MySQL

### Hostinger Compatibility
✅ Shared Hosting
✅ Cloud Hosting
✅ VPS Hosting

All Hostinger plans support PHP & MySQL by default.

---

## 🚀 Quick Start

### 1. Create Database
```sql
-- Via phpMyAdmin di cPanel
CREATE DATABASE portfolio_cms;
```

### 2. Import Schema
Upload `database/schema.sql` via phpMyAdmin Import.

### 3. Configure
Edit `config/database.php`:
```php
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('SITE_URL', 'https://yourdomain.com');
```

### 4. Upload Files
Upload semua file ke `public_html/` via FTP atau File Manager.

### 5. Login
Buka: `https://yourdomain.com/admin/login.php`
- Username: `admin`
- Password: `admin123`

**Done! 🎉**

---

## 🔧 Detailed Setup

### Step 1: Database Setup

#### Via cPanel (Hostinger)
1. Login ke cPanel
2. Buka **MySQL Databases**
3. Create new database:
   - Name: `u123456_portfolio`
4. Create new user:
   - Username: `u123456_admin`
   - Password: [strong password]
5. Add user to database
   - Select: ALL PRIVILEGES

#### Via phpMyAdmin
1. Open phpMyAdmin
2. Click **New** (create database)
3. Name: `portfolio_cms`
4. Collation: `utf8mb4_unicode_ci`
5. Click **Create**

### Step 2: Import Database Schema

1. Select your database
2. Click **Import** tab
3. Choose file: `database/schema.sql`
4. Click **Go**
5. Verify: 8 tables created

**Tables Created:**
- `admin_users` - Admin accounts
- `about` - About section
- `skills` - Skills list
- `experience` - Work experience
- `education` - Education history
- `portfolio` - Projects
- `services` - Services offered
- `contact_info` - Contact details
- `testimonials` - Client testimonials

### Step 3: Configuration

#### Database Configuration
Copy example config:
```bash
cp config/database.example.php config/database.php
```

Edit `config/database.php`:
```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456_portfolio');
define('DB_USER', 'u123456_admin');
define('DB_PASS', 'your_secure_password');

// Site URL
define('SITE_URL', 'https://khalidsaifullah.me');
```

#### File Permissions
Set correct permissions:
```bash
# Folders
chmod 755 admin/ api/ assets/ config/

# Files
chmod 644 index.html *.php .htaccess

# Uploads folder
chmod 755 uploads/
```

### Step 4: Upload to Hostinger

#### Via File Manager
1. Login cPanel
2. Open **File Manager**
3. Navigate to `public_html/`
4. Upload all files
5. Extract if ZIP

#### Via FTP
1. Use FileZilla or similar
2. Connect to Hostinger FTP
3. Upload to `public_html/`
4. Preserve folder structure

### Step 5: Testing

#### Test Frontend
```
https://yourdomain.com/
```
Should show your portfolio website.

#### Test API
```
https://yourdomain.com/api/index.php
```
Should return JSON response.

#### Test Admin
```
https://yourdomain.com/admin/login.php
```
Login with default credentials.

---

## 📚 Usage Guide

### Admin Panel Navigation

#### Dashboard
- View statistics (skills, experience, projects count)
- Quick actions untuk add content
- Overview semua sections

#### About Section
- Edit title & description
- Support HTML formatting
- Auto-save on submit

#### Skills Management
- Add new skills
- Set proficiency percentage (0-100%)
- Categorize (Frontend, Backend, Database, etc)
- Edit/delete existing skills

#### Experience Management
- Add work experience
- Set start/end dates
- Mark as "Currently working"
- Add job description
- Edit/delete entries

#### Education Management
- Add education history
- Set degree & institution
- Add dates & location
- Mark as "Currently studying"

#### Portfolio Management
- Add projects
- Upload project images
- Set category (Web, Mobile, Design)
- Add project URL & GitHub link
- Mark as featured
- Add technologies used

#### Services Management
- Add services offered
- Set icon & description
- Reorder services

#### Contact Info
- Update email & phone
- Set location & birthday
- Update social media links

---

## 🔌 API Documentation

### Base URL
```
https://yourdomain.com/api/
```

### Endpoints

#### Get All Data
```http
GET /api/index.php
```

**Response:**
```json
{
  "success": true,
  "data": {
    "about": {
      "id": 1,
      "title": "About me",
      "description": "...",
      "profile_image": null
    },
    "contact": {
      "email": "email@example.com",
      "phone": "+62 xxx",
      "birthday": "1998-07-15",
      "location": "Indonesia",
      "linkedin": "https://...",
      "instagram": "https://..."
    },
    "skills": [
      {
        "id": 1,
        "name": "PHP",
        "percentage": 85,
        "category": "Backend"
      }
    ],
    "experience": [...],
    "education": [...],
    "portfolio": [...],
    "services": [...],
    "testimonials": [...]
  },
  "timestamp": 1234567890
}
```

### Response Format

All API responses follow this structure:
```json
{
  "success": true|false,
  "data": {...},
  "error": "Error message (if failed)",
  "timestamp": 1234567890
}
```

### Error Handling

**500 Internal Server Error:**
```json
{
  "success": false,
  "error": "Internal server error",
  "message": "Database connection failed"
}
```

---

## 🎨 Frontend Integration

### Option 1: Automatic (Recommended)

Add script sebelum `</body>` di `index.html`:
```html
<script src="./assets/js/cms-integration.js"></script>
```

Script ini akan:
- Fetch data dari API
- Update semua sections automatically
- Cache data di localStorage (5 minutes)
- Auto-refresh on tab focus

### Option 2: Manual Integration

```javascript
// Fetch data
fetch('/api/index.php')
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      const data = result.data;
      
      // Update About
      document.querySelector('.about-text p').innerHTML = data.about.description;
      
      // Update Skills
      data.skills.forEach(skill => {
        // Create skill element
        const skillEl = `
          <div class="skill-item">
            <span>${skill.name}</span>
            <div class="progress">
              <div style="width: ${skill.percentage}%"></div>
            </div>
          </div>
        `;
        // Append to skills container
      });
      
      // Update other sections...
    }
  })
  .catch(error => console.error('Error:', error));
```

### Caching Strategy

```javascript
const CACHE_KEY = 'portfolio_data';
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

function getCachedData() {
  const cached = localStorage.getItem(CACHE_KEY);
  if (cached) {
    const { data, timestamp } = JSON.parse(cached);
    if (Date.now() - timestamp < CACHE_DURATION) {
      return data;
    }
  }
  return null;
}

function setCachedData(data) {
  localStorage.setItem(CACHE_KEY, JSON.stringify({
    data: data,
    timestamp: Date.now()
  }));
}
```

---

## 🔒 Security

### Change Default Password

#### Method 1: Via phpMyAdmin
```sql
UPDATE admin_users 
SET password = '$2y$10$YOUR_NEW_HASH' 
WHERE username = 'admin';
```

Generate hash:
```php
<?php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
?>
```

#### Method 2: Via PHP Script
Create `change-password.php`:
```php
<?php
$new_password = 'your_new_password';
$hash = password_hash($new_password, PASSWORD_DEFAULT);
echo "New hash: " . $hash;
// Copy hash and update database
?>
```

### Additional Security

#### Protect Admin Directory
Create `admin/.htaccess`:
```apache
AuthType Basic
AuthName "Admin Area"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

Generate `.htpasswd`:
```bash
htpasswd -c .htpasswd admin
```

#### Enable HTTPS
1. Login cPanel
2. SSL/TLS Status
3. Enable AutoSSL
4. Update `SITE_URL` to HTTPS

#### Hide PHP Version
Add to `.htaccess`:
```apache
ServerSignature Off
```

#### Prevent Directory Listing
```apache
Options -Indexes
```

---

## 🐛 Troubleshooting

### Database Connection Error

**Error:** "Connection failed"

**Solutions:**
1. Check credentials di `config/database.php`
2. Verify database exists
3. Check user has privileges
4. Test connection via phpMyAdmin

### 500 Internal Server Error

**Causes:**
- Wrong file permissions
- PHP syntax error
- Missing PHP extensions

**Solutions:**
1. Check error log: cPanel → Error Log
2. Set permissions: 755 (folders), 644 (files)
3. Verify PHP version: 7.4+
4. Enable error reporting (temporarily):
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Cannot Login

**Solutions:**
1. Clear browser cache & cookies
2. Check `admin_users` table exists
3. Verify password hash
4. Reset password via phpMyAdmin
5. Check session configuration

### API Returns Empty Data

**Solutions:**
1. Verify database has data
2. Check API endpoint: `/api/index.php`
3. View response in browser
4. Check browser console for errors
5. Verify CORS settings (if needed)

### Images Not Loading

**Solutions:**
1. Check file paths
2. Verify image URLs in database
3. Check file permissions
4. Ensure images uploaded to correct folder
5. Clear browser cache

---

## ❓ FAQ

### Q: Apakah bisa digunakan di shared hosting?
**A:** Ya! CMS ini dirancang khusus untuk shared hosting seperti Hostinger.

### Q: Apakah perlu Node.js?
**A:** Tidak. Pure PHP, tidak butuh Node.js.

### Q: Bagaimana cara backup?
**A:** 
1. Database: Export via phpMyAdmin
2. Files: Download via FTP
3. Simpan di cloud storage

### Q: Apakah bisa multi-user?
**A:** Saat ini single user. Bisa dikembangkan untuk multi-user.

### Q: Bagaimana cara update content?
**A:** Login admin panel, edit via UI, save. Changes langsung reflect di API.

### Q: Apakah support upload images?
**A:** Ya, folder `uploads/` tersedia. Bisa dikembangkan upload feature.

### Q: Bagaimana cara customize design?
**A:** Edit CSS di `admin/includes/header.php` atau buat custom CSS file.

### Q: Apakah ada limit data?
**A:** Tergantung database limit hosting Anda. Umumnya cukup untuk portfolio.

### Q: Bagaimana cara migrate ke hosting lain?
**A:**
1. Export database
2. Download all files
3. Upload ke hosting baru
4. Import database
5. Update config

### Q: Apakah SEO friendly?
**A:** Ya, frontend tetap static HTML. SEO tidak terpengaruh.

---

## 📞 Support

### Resources
- **Documentation**: README-CMS.md
- **Quick Start**: QUICK-START.md
- **Deployment**: DEPLOYMENT-CHECKLIST.md

### Hostinger Support
- Website: https://support.hostinger.com
- Live Chat: Available 24/7
- Knowledge Base: Extensive tutorials

### PHP & MySQL
- PHP Docs: https://php.net/docs
- MySQL Docs: https://dev.mysql.com/doc/

---

## 📄 License

This CMS is created for Khalid Saifullah's portfolio.
Free to use and modify for personal projects.

---

## 🎉 Credits

**Developed for:** Khalid Saifullah
**Tech Stack:** PHP, MySQL, Bootstrap, Vanilla JS
**Compatible with:** Hostinger Shared Hosting

---

**Version:** 1.0.0
**Last Updated:** January 2025

Made with ❤️ for portfolio management
