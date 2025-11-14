# 🗄️ Database Setup Guide

## Migration & Seeder System

CMS ini menggunakan SQL migration dan seeder untuk setup database dengan data dari HTML existing Anda.

---

## 📦 Files

### 1. `database/migrations.sql`
- Complete database schema
- 9 tables dengan proper indexes
- Field validation (CHECK constraints)
- Auto timestamps

### 2. `database/seeders.sql`
- Initial data dari HTML existing
- Admin user dengan credentials custom
- Contact info, skills, experience, education
- Sample portfolio projects
- Services dan testimonials

### 3. `database/run-migrations.php`
- Web-based migration runner
- Visual interface
- Error handling
- Verification

---

## 🚀 Quick Setup

### Method 1: Web Interface (Recommended)

1. **Access Migration Runner:**
```
http://localhost/my-profile/database/run-migrations.php
```

2. **Click "Run Migration & Seeder"**

3. **Wait for completion**

4. **Login with credentials:**
   - Username: `admin_khalid`
   - Password: `Khalidprofile321.`

5. **Delete the file:**
```
Delete: database/run-migrations.php
```

### Method 2: Manual via phpMyAdmin

1. **Open phpMyAdmin**

2. **Select your database**

3. **Import migrations:**
   - Import → Choose `database/migrations.sql`
   - Click Go

4. **Import seeders:**
   - Import → Choose `database/seeders.sql`
   - Click Go

5. **Verify:**
   - Check 9 tables created
   - Check data populated

---

## 📊 Database Schema

### Tables Created:

1. **admin_users** - Admin authentication
   - Custom username: `admin_khalid`
   - Custom password: `Khalidprofile321.`
   - Email: `holitsky98@gmail.com`

2. **about** - About section
   - Extracted from HTML
   - Full description with HTML formatting

3. **contact_info** - Contact details
   - Email: holitsky98@gmail.com
   - Phone: +62 821-4768-8858
   - Birthday: 15 July 1998
   - Location: South Tangerang
   - Social links (LinkedIn, Instagram)

4. **skills** - Technical skills
   - 14 skills extracted
   - Categories: Frontend, Backend, Database, Design, GIS, Tools
   - Proficiency percentages

5. **experience** - Work history
   - 4 positions extracted from HTML
   - Current: Ministry of Forestry (May 2025 - Present)
   - Previous: BSILHK, Arculus Indonesia, K3I Korlantas

6. **education** - Education history
   - Universitas Ibn Khaldun Bogor
   - Informatics Engineering
   - GPA: 3.53
   - 2019 - 2024

7. **portfolio** - Projects
   - 5 sample projects
   - SIJAB, LiNE BSILHK, SINADI DKI, etc.
   - Categories, technologies, featured flag

8. **services** - Services offered
   - 3 services from HTML
   - Web Development, UI/UX Design, Mobile Development

9. **testimonials** - Client testimonials
   - 2 sample testimonials
   - Can be managed via admin panel

---

## 🔐 Default Credentials

### Admin Login
```
URL:      http://localhost/my-profile/admin/login.php
Username: admin_khalid
Password: Khalidprofile321.
Email:    holitsky98@gmail.com
```

**⚠️ IMPORTANT:** Change password after first login!

---

## 📝 Data Extracted from HTML

### ✅ Contact Information
- ✓ Email
- ✓ Phone
- ✓ Birthday
- ✓ Location
- ✓ LinkedIn URL
- ✓ Instagram URL

### ✅ About Section
- ✓ Full description (2 paragraphs)
- ✓ Video background path

### ✅ Services (3 items)
- ✓ Web Development (CodeIgniter & Laravel)
- ✓ UI/UX Design (Figma)
- ✓ Mobile Development (React Native)

### ✅ Skills (14 items)
- ✓ HTML (95%)
- ✓ CSS (90%)
- ✓ JavaScript (85%)
- ✓ React (80%)
- ✓ React Native (75%)
- ✓ PHP (90%)
- ✓ Laravel (85%)
- ✓ CodeIgniter (85%)
- ✓ MySQL (80%)
- ✓ PostgreSQL (75%)
- ✓ Figma (85%)
- ✓ Git (80%)
- ✓ Leaflet.js (75%)
- ✓ ArcGIS (70%)

### ✅ Education (1 item)
- ✓ Universitas Ibn Khaldun Bogor
- ✓ Informatics Engineering
- ✓ 2019 - 2024
- ✓ GPA: 3.53
- ✓ Final project description
- ✓ MBKM program details

### ✅ Experience (4 items)
1. **Ministry of Forestry** (May 2025 - Present)
   - Technical Assistant – GIS Programming
   - Full job description extracted

2. **BSILHK** (Aug - Dec 2024)
   - Web Developer & IT Consultant
   - Technologies: Laravel, CodeIgniter, Leaflet.js

3. **Arculus Indonesia** (Aug 2022 - Oct 2024)
   - Web Developer & UI/UX Designer
   - Multiple government projects

4. **K3I Korlantas Polri** (Oct - Nov 2022)
   - Data Entry Operator
   - G20 Summit support

### ✅ Portfolio Projects (5 items)
1. SIJAB Mobile Application (Featured)
2. LiNE BSILHK Information System (Featured)
3. SINADI DKI Dashboard (Featured)
4. Korlantas Polri Website
5. VOCA Platform

---

## 🧪 Testing

### 1. Test Database Connection
```
http://localhost/my-profile/test-db.php
```

**Expected:**
- ✅ Environment: LOCAL
- ✅ Connection successful
- ✅ 9 tables listed
- ✅ Record counts shown

### 2. Test Admin Login
```
http://localhost/my-profile/admin/login.php
```

**Login with:**
- Username: `admin_khalid`
- Password: `Khalidprofile321.`

**Expected:**
- ✅ Login successful
- ✅ Redirect to dashboard
- ✅ Statistics showing correct counts

### 3. Test API
```
http://localhost/my-profile/api/index.php
```

**Expected:**
- ✅ JSON response
- ✅ All data populated
- ✅ About, skills, experience, etc.

---

## 🔄 Re-running Migrations

If you need to reset database:

### Option 1: Via Web Interface
```
http://localhost/my-profile/database/run-migrations.php
```
Click "Run Migration & Seeder" again.

### Option 2: Manual
```sql
-- Drop all tables
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS contact_info;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS portfolio;
DROP TABLE IF EXISTS education;
DROP TABLE IF EXISTS experience;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS about;
DROP TABLE IF EXISTS admin_users;

-- Then import migrations.sql and seeders.sql again
```

---

## 🐛 Troubleshooting

### Migration Failed

**Error:** "Could not read migrations.sql"
**Solution:** Check file exists in `database/` folder

**Error:** "Table already exists"
**Solution:** Drop tables first or use run-migrations.php

**Error:** "Access denied"
**Solution:** Check database credentials in `config/config.php`

### Seeder Failed

**Error:** "Duplicate entry for key 'PRIMARY'"
**Solution:** Tables not empty. Drop and recreate.

**Error:** "Unknown column"
**Solution:** Run migrations first before seeders.

### Login Failed

**Error:** "Invalid username or password"
**Solution:** 
1. Check username: `admin_khalid` (not admin)
2. Check password: `Khalidprofile321.` (with dot at end)
3. Verify admin_users table has data

### No Data in Tables

**Solution:**
1. Run seeders: `database/seeders.sql`
2. Check seeder file executed successfully
3. Verify via phpMyAdmin

---

## 📋 Checklist

### Initial Setup
- [ ] Database created
- [ ] `config/config.php` configured
- [ ] Migrations executed
- [ ] Seeders executed
- [ ] 9 tables created
- [ ] Data populated
- [ ] Admin user created

### Verification
- [ ] Test connection: `test-db.php`
- [ ] Login works: `admin/login.php`
- [ ] Dashboard shows stats
- [ ] API returns data: `api/index.php`
- [ ] All sections have data

### Security
- [ ] Change admin password
- [ ] Delete `run-migrations.php`
- [ ] Delete `test-db.php`
- [ ] Backup database

---

## 🎯 Next Steps

After successful setup:

1. **Login to Admin Panel**
   ```
   http://localhost/my-profile/admin/login.php
   ```

2. **Change Password**
   - Via phpMyAdmin or create change password feature

3. **Update Content**
   - About section
   - Add more skills
   - Add more projects
   - Update contact info

4. **Test Frontend Integration**
   - Add CMS integration script to `index.html`
   - Test data displays correctly

5. **Deploy to Production**
   - Follow `DEPLOYMENT-CHECKLIST.md`
   - Update production config
   - Run migrations on production database

---

## 📊 Database Statistics

After seeding, you should have:

| Table | Records |
|-------|---------|
| admin_users | 1 |
| about | 1 |
| contact_info | 1 |
| services | 3 |
| skills | 14 |
| education | 1 |
| experience | 4 |
| portfolio | 5 |
| testimonials | 2 |
| **TOTAL** | **32** |

---

## 🔒 Security Notes

### Password Hash
```php
// Password: Khalidprofile321.
// Hash: $2y$10$vQx5mK9nP7LZYwX8fH.jOeYGzJ3kR4tN6pW2sA1bC5dE7fG8hI9jK
```

### Change Password
```sql
-- Generate new hash
<?php echo password_hash('new_password', PASSWORD_DEFAULT); ?>

-- Update in database
UPDATE admin_users 
SET password = '$2y$10$NEW_HASH_HERE' 
WHERE username = 'admin_khalid';
```

---

## ✅ Success!

Your database is now setup with:
- ✅ Complete schema (9 tables)
- ✅ All data from HTML
- ✅ Custom admin credentials
- ✅ Ready to use!

**Happy coding! 🎉**

---

**Version:** 1.0.0
**Last Updated:** January 2025
**Data Source:** index.html (Khalid Saifullah Portfolio)
