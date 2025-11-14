# 🔐 Environment Setup Guide

This project uses `.env` file for sensitive configuration. **Never commit `.env` to git!**

---

## 🚀 Quick Start

### **Step 1: Copy Example File**

```bash
# Copy .env.example to .env
cp .env.example .env
```

### **Step 2: Edit .env File**

Open `.env` and fill in your values:

```bash
# Use your favorite editor
nano .env
# or
vim .env
# or open in IDE
```

---

## 📋 Configuration Guide

### **1. Application Settings**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.khalidsaifullah.me
```

**Values:**
- `APP_ENV`: `local` or `production`
- `APP_DEBUG`: `true` or `false` (always `false` in production!)
- `APP_URL`: Your website URL

---

### **2. Database Settings**

```env
DB_HOST=localhost
DB_NAME=u734000704_khalid_profile
DB_USER=u734000704_root
DB_PASS=YourSecurePassword123
```

**How to get values:**
- **Hostinger:** cPanel → MySQL Databases
- **Local:** Usually `root` with empty password

---

### **3. Security Settings**

```env
YOUR_IP=180.252.241.181
ALLOWED_IPS=127.0.0.1,::1,180.252.241.181
ENABLE_UTILITY_FILES=true
```

**Find your IP:**
```bash
# Visit in browser:
https://api.ipify.org

# Or use command:
curl https://api.ipify.org
```

**Production:**
- Set `ENABLE_UTILITY_FILES=false` after setup
- Only add trusted IPs to `ALLOWED_IPS`

---

### **4. Deployment Settings**

```env
DEPLOY_ENABLED=true
ALLOW_MANUAL_TRIGGER=true
MANUAL_TRIGGER_IP=180.252.241.181
DEPLOY_SECRET=ed93417c86d1eee139dd20a6afce675c9a74a49737d7124e327bdf0defd9a516
PROJECT_PATH=/home/u734000704/domains/khalidsaifullah.me/public_html
GIT_BRANCH=main
```

**Generate DEPLOY_SECRET:**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

**Get PROJECT_PATH:**
```bash
# SSH to server
ssh user@server
pwd
# Copy the output
```

---

### **5. GitHub Webhook IPs**

```env
GITHUB_WEBHOOK_IPS=140.82.112.0/20,143.55.64.0/20,192.30.252.0/22,185.199.108.0/22
```

**Note:** These are official GitHub webhook IP ranges. Update if GitHub changes them.

**Check latest:** https://api.github.com/meta

---

## 🔧 Environment-Specific Setup

### **Local Development (.env)**

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/my-profile

DB_HOST=localhost
DB_NAME=khalid_portfolio
DB_USER=root
DB_PASS=

YOUR_IP=127.0.0.1
ALLOWED_IPS=127.0.0.1,::1
ENABLE_UTILITY_FILES=true

DEPLOY_ENABLED=false
```

---

### **Production (.env)**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.khalidsaifullah.me

DB_HOST=localhost
DB_NAME=u734000704_khalid_profile
DB_USER=u734000704_root
DB_PASS=YourProductionPassword

YOUR_IP=180.252.241.181
ALLOWED_IPS=127.0.0.1,::1,180.252.241.181
ENABLE_UTILITY_FILES=false

DEPLOY_ENABLED=true
ALLOW_MANUAL_TRIGGER=false
MANUAL_TRIGGER_IP=180.252.241.181
DEPLOY_SECRET=your-generated-secret-here
PROJECT_PATH=/home/u734000704/domains/khalidsaifullah.me/public_html
GIT_BRANCH=main
GITHUB_WEBHOOK_IPS=140.82.112.0/20,143.55.64.0/20,192.30.252.0/22,185.199.108.0/22
```

---

## 📤 Deployment to Server

### **Method 1: Manual Upload**

```bash
# 1. Create .env on server
ssh user@server
cd /home/u734000704/domains/khalidsaifullah.me/public_html
nano .env

# 2. Paste your production config
# 3. Save and exit (Ctrl+X, Y, Enter)

# 4. Set permissions
chmod 600 .env
```

---

### **Method 2: SCP Upload**

```bash
# From local machine
scp .env.production user@server:/home/u734000704/domains/khalidsaifullah.me/public_html/.env

# Set permissions
ssh user@server
chmod 600 /home/u734000704/domains/khalidsaifullah.me/public_html/.env
```

---

## 🔒 Security Best Practices

### **1. File Permissions**

```bash
# .env should be readable only by owner
chmod 600 .env

# Verify
ls -la .env
# Should show: -rw------- (600)
```

---

### **2. Never Commit .env**

```bash
# Check .gitignore includes .env
cat .gitignore | grep .env

# Should show:
# .env
# .env.local
# .env.production
```

**Verify:**
```bash
git status
# .env should NOT appear in untracked files
```

---

### **3. Use Strong Secrets**

```bash
# Generate strong DEPLOY_SECRET
php -r "echo bin2hex(random_bytes(32));"

# Generate strong DB_PASS
php -r "echo bin2hex(random_bytes(16));"
```

---

### **4. Rotate Secrets Regularly**

```bash
# Every 3-6 months:
# 1. Generate new DEPLOY_SECRET
# 2. Update .env
# 3. Update GitHub webhook secret
# 4. Test deployment
```

---

## 🧪 Testing

### **Test 1: Check .env Loading**

```bash
# Visit (from allowed IP):
https://www.khalidsaifullah.me/force-refresh.php

# Should show:
# ✅ API endpoint working
# About: Khalid Saifullah
# Contact Email: (your email)
```

---

### **Test 2: Test Database Connection**

```bash
# Visit (from allowed IP):
https://www.khalidsaifullah.me/test-db.php

# Should show:
# ✅ Database connection successful
```

---

### **Test 3: Test Deploy**

```bash
# Manual trigger (from allowed IP):
curl https://www.khalidsaifullah.me/deploy.php

# Should return JSON:
# {"success": true, "message": "Deploy successful"}
```

---

## 🐛 Troubleshooting

### **Issue: "Database connection failed"**

**Solution:**
```bash
# 1. Check .env exists
ls -la .env

# 2. Check .env values
cat .env | grep DB_

# 3. Test connection
php -r "
require 'includes/env.php';
echo 'DB_NAME: ' . env('DB_NAME') . PHP_EOL;
echo 'DB_USER: ' . env('DB_USER') . PHP_EOL;
"
```

---

### **Issue: ".env not loading"**

**Solution:**
```bash
# 1. Check file location
pwd
ls -la .env

# 2. Check permissions
chmod 600 .env

# 3. Check includes/env.php exists
ls -la includes/env.php

# 4. Clear OPcache
curl https://www.khalidsaifullah.me/clear-cache.php
```

---

### **Issue: "Access denied" on utility files**

**Solution:**
```bash
# 1. Check your IP
curl https://api.ipify.org

# 2. Update .env
YOUR_IP=YOUR_ACTUAL_IP
ALLOWED_IPS=127.0.0.1,::1,YOUR_ACTUAL_IP

# 3. Clear cache
curl https://www.khalidsaifullah.me/clear-cache.php
```

---

## 📚 Files Using .env

| File | Purpose | Required Variables |
|------|---------|-------------------|
| `config/config.php` | Database & app config | `DB_*`, `APP_*` |
| `security-config.php` | IP whitelist | `YOUR_IP`, `ALLOWED_IPS` |
| `deploy.php` | Auto-deploy | `DEPLOY_*`, `PROJECT_PATH` |
| `includes/env.php` | .env loader | (loads all) |

---

## ✅ Checklist

**Before Committing:**
- [ ] `.env` is in `.gitignore`
- [ ] `.env.example` is up to date
- [ ] No sensitive data in code
- [ ] All secrets use `env()` function

**Before Deploying:**
- [ ] `.env` created on server
- [ ] All values filled correctly
- [ ] File permissions set (600)
- [ ] Database connection tested
- [ ] Deploy webhook tested

**Production Security:**
- [ ] `APP_DEBUG=false`
- [ ] `ENABLE_UTILITY_FILES=false`
- [ ] `ALLOW_MANUAL_TRIGGER=false`
- [ ] Strong `DEPLOY_SECRET`
- [ ] Only trusted IPs in `ALLOWED_IPS`

---

## 🎯 Summary

**What is .env?**
- Configuration file for sensitive data
- Never committed to git
- Different values for local/production

**Why use .env?**
- ✅ Keep secrets out of code
- ✅ Easy to change config
- ✅ Safe to open-source project
- ✅ Environment-specific settings

**How to use?**
```php
// In your code:
$dbName = env('DB_NAME');
$isDebug = env_bool('APP_DEBUG', false);
$allowedIPs = env_array('ALLOWED_IPS');
```

**Remember:**
- 🔒 `.env` = NEVER commit
- 📝 `.env.example` = Always commit
- 🔐 Use strong secrets
- 🧪 Test after changes

---

**Your project is now ready to be open-sourced! 🎉**
