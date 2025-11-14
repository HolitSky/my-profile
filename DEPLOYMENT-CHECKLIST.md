# 📋 Deployment Checklist - Hostinger

## Pre-Deployment

- [ ] Backup existing website (jika ada)
- [ ] Siapkan kredensial cPanel Hostinger
- [ ] Test CMS di local environment dulu (optional)

---

## Database Setup

- [ ] Login ke cPanel Hostinger
- [ ] Buka **MySQL Databases**
- [ ] Create database baru
  - Nama: `u[user-id]_portfolio` (contoh)
- [ ] Create MySQL user
  - Username: `u[user-id]_admin` (contoh)
  - Password: [generate strong password]
- [ ] Add user to database
  - Privileges: **ALL PRIVILEGES**
- [ ] **CATAT KREDENSIAL:**
  ```
  DB Host: localhost
  DB Name: _________________
  DB User: _________________
  DB Pass: _________________
  ```

---

## Import Database

- [ ] Buka **phpMyAdmin** di cPanel
- [ ] Select database yang baru dibuat
- [ ] Klik tab **Import**
- [ ] Choose file: `database/schema.sql`
- [ ] Klik **Go**
- [ ] Verify: Cek apakah tables sudah terbuat (8 tables)

---

## Upload Files

### Via File Manager (Recommended)
- [ ] Login cPanel → **File Manager**
- [ ] Navigate ke `public_html/`
- [ ] Upload semua file & folder
- [ ] Extract jika upload dalam bentuk ZIP

### Via FTP
- [ ] Download FTP client (FileZilla)
- [ ] Connect dengan kredensial FTP Hostinger
- [ ] Upload ke folder `public_html/`

**Files yang harus di-upload:**
```
✓ admin/
✓ api/
✓ assets/
✓ config/
✓ database/
✓ index.html
✓ .htaccess
✓ README-CMS.md
✓ QUICK-START.md
```

---

## Configuration

- [ ] Edit `config/database.php`
  ```php
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'u123_portfolio');  // ← Your DB name
  define('DB_USER', 'u123_admin');      // ← Your DB user
  define('DB_PASS', 'your_password');   // ← Your DB password
  define('SITE_URL', 'https://khalidsaifullah.me'); // ← Your domain
  ```

- [ ] Save file

---

## File Permissions

Set correct permissions via File Manager:

- [ ] Folders: **755**
  - `admin/`
  - `api/`
  - `assets/`
  - `config/`
  
- [ ] Files: **644**
  - `index.html`
  - `*.php`
  - `.htaccess`

- [ ] Create `uploads/` folder: **755**

---

## Testing

### 1. Test Frontend
- [ ] Buka: `https://yourdomain.com/`
- [ ] Verify: Website loading dengan benar

### 2. Test API
- [ ] Buka: `https://yourdomain.com/api/index.php`
- [ ] Verify: JSON response muncul
- [ ] Check: `"success": true`

### 3. Test Admin Login
- [ ] Buka: `https://yourdomain.com/admin/login.php`
- [ ] Login dengan:
  - Username: `admin`
  - Password: `admin123`
- [ ] Verify: Redirect ke dashboard

### 4. Test Admin Panel
- [ ] Dashboard loading
- [ ] Statistics muncul
- [ ] Semua menu accessible
- [ ] Try add/edit/delete data

---

## Security

- [ ] **GANTI PASSWORD DEFAULT!**
  - Via phpMyAdmin atau admin panel
  
- [ ] Generate new password hash:
  ```php
  <?php echo password_hash('your_new_password', PASSWORD_DEFAULT); ?>
  ```

- [ ] Update di database:
  ```sql
  UPDATE admin_users 
  SET password = '$2y$10$YOUR_NEW_HASH' 
  WHERE username = 'admin';
  ```

- [ ] Test login dengan password baru

- [ ] Optional: Add `.htaccess` protection ke `/admin/`

- [ ] Optional: Enable HTTPS (Hostinger provides free SSL)

---

## SSL Certificate (HTTPS)

- [ ] Login cPanel
- [ ] Buka **SSL/TLS Status**
- [ ] Enable AutoSSL untuk domain
- [ ] Wait 5-10 minutes
- [ ] Test: `https://yourdomain.com`
- [ ] Update `SITE_URL` di `config/database.php` ke HTTPS

---

## Post-Deployment

### Content Setup
- [ ] Login admin panel
- [ ] Update **About** section
- [ ] Add **Skills** (minimal 5)
- [ ] Add **Experience** (work history)
- [ ] Add **Education**
- [ ] Upload **Portfolio** projects
- [ ] Update **Services**
- [ ] Verify **Contact Info**

### Frontend Integration
- [ ] Add CMS integration script ke `index.html`:
  ```html
  <script src="./assets/js/cms-integration.js"></script>
  ```
- [ ] Test: Data dari CMS muncul di frontend
- [ ] Clear browser cache
- [ ] Test di multiple browsers

### Final Checks
- [ ] Test semua links
- [ ] Test responsive design (mobile/tablet)
- [ ] Check images loading
- [ ] Verify SEO meta tags
- [ ] Test contact form (jika ada)
- [ ] Check page load speed

---

## Backup Strategy

### Initial Backup
- [ ] Backup database via phpMyAdmin
  - Export → SQL format
  - Save: `backup_YYYY-MM-DD.sql`

- [ ] Download all files via FTP
  - Save: `files_backup_YYYY-MM-DD.zip`

### Regular Backups
- [ ] Setup cPanel automatic backups (jika available)
- [ ] Manual backup setiap minggu/bulan
- [ ] Store backups di cloud (Google Drive, Dropbox)

---

## Monitoring

- [ ] Setup Google Analytics (optional)
- [ ] Monitor error logs di cPanel
- [ ] Check website uptime
- [ ] Monitor database size
- [ ] Check disk space usage

---

## Troubleshooting

### If website shows error:
1. Check error logs: cPanel → **Error Log**
2. Verify file permissions
3. Check database connection
4. Clear browser cache
5. Check PHP version (min 7.4)

### If admin login fails:
1. Clear cookies
2. Check database `admin_users` table
3. Reset password via phpMyAdmin
4. Check session configuration

### If API returns error:
1. Check database connection
2. Verify tables exist
3. Check file permissions
4. Test direct: `/api/index.php`

---

## Support Resources

- **Hostinger Support**: https://support.hostinger.com
- **PHP Documentation**: https://php.net
- **MySQL Documentation**: https://dev.mysql.com/doc/

---

## ✅ Deployment Complete!

Setelah semua checklist selesai:
- ✅ Website live di Hostinger
- ✅ Admin panel accessible
- ✅ API working
- ✅ Data manageable via CMS
- ✅ Security configured
- ✅ Backups ready

**Congratulations! 🎉**

Your portfolio CMS is now live and ready to use!

---

**Last Updated**: 2025-01-13
**Version**: 1.0.0
