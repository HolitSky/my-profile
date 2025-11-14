# 🚀 Auto-Deploy Setup Guide

Complete guide untuk setup GitHub webhook auto-deploy di Hostinger.

---

## 📋 Prerequisites

- ✅ Git installed on server
- ✅ SSH key configured for GitHub
- ✅ Repository cloned in server
- ✅ Write permissions on project directory

---

## 🔧 Step 1: Configure deploy.php

### **1.1 Set Secret Token**

Edit `deploy.php` line 23:
```php
define('DEPLOY_SECRET', 'your-secret-token-here-change-this-12345');
```

**Generate strong secret:**
```bash
# On your local machine
php -r "echo bin2hex(random_bytes(32));"
```

**Example:**
```php
define('DEPLOY_SECRET', 'a3f8d9e2c1b4567890abcdef12345678901234567890abcdef1234567890abcd');
```

---

### **1.2 Verify Project Path**

Line 37:
```php
define('PROJECT_PATH', '/home/u734000704/domains/khalidsaifullah.me/public_html');
```

**Check your actual path on server:**
```bash
ssh u734000704@id-dci-web1785.hosting-data.io
pwd
# Should show: /home/u734000704/domains/khalidsaifullah.me/public_html
```

---

### **1.3 Set Git Branch**

Line 38:
```php
define('GIT_BRANCH', 'main'); // or 'master'
```

---

## 🔑 Step 2: Setup SSH Key on Server

### **2.1 Generate SSH Key**

```bash
# SSH to your server
ssh u734000704@id-dci-web1785.hosting-data.io

# Generate SSH key
ssh-keygen -t ed25519 -C "u734000704@hostinger"

# Press Enter for default location
# Press Enter twice for no passphrase

# Display public key
cat ~/.ssh/id_ed25519.pub
```

---

### **2.2 Add SSH Key to GitHub**

1. Copy the public key output
2. Go to GitHub: **Settings** → **SSH and GPG keys**
3. Click **New SSH key**
4. Paste the key
5. Click **Add SSH key**

---

### **2.3 Test SSH Connection**

```bash
ssh -T git@github.com
# Should see: Hi [username]! You've successfully authenticated...
```

---

### **2.4 Configure Git Remote**

```bash
cd /home/u734000704/domains/khalidsaifullah.me/public_html

# Check current remote
git remote -v

# If using HTTPS, change to SSH
git remote set-url origin git@github.com:HolitSky/my-profile.git

# Verify
git remote -v
# Should show: git@github.com:HolitSky/my-profile.git
```

---

## 🌐 Step 3: Setup GitHub Webhook

### **3.1 Go to Repository Settings**

1. Open your GitHub repository
2. Go to **Settings** → **Webhooks**
3. Click **Add webhook**

---

### **3.2 Configure Webhook**

**Payload URL:**
```
https://www.khalidsaifullah.me/deploy.php
```

**Content type:**
```
application/json
```

**Secret:**
```
[paste your DEPLOY_SECRET from deploy.php]
```

**Which events:**
```
☑ Just the push event
```

**Active:**
```
☑ Active
```

Click **Add webhook**

---

### **3.3 Test Webhook**

1. GitHub will send a test ping
2. Check **Recent Deliveries** tab
3. Should see green checkmark ✅
4. Click on delivery to see response

---

## 📝 Step 4: Set File Permissions

```bash
# SSH to server
ssh u734000704@id-dci-web1785.hosting-data.io

cd /home/u734000704/domains/khalidsaifullah.me/public_html

# Make deploy.php executable
chmod 755 deploy.php

# Create log file
touch deploy.log
chmod 666 deploy.log

# Ensure git can write
chmod -R 755 .git
```

---

## 🧪 Step 5: Test Deploy

### **5.1 Manual Test**

```bash
# On your local machine
curl -X POST https://www.khalidsaifullah.me/deploy.php \
  -H "Content-Type: application/json" \
  -d '{"ref":"refs/heads/main"}'
```

**Expected response:**
```json
{
    "success": true,
    "message": "Deploy successful",
    "timestamp": "2025-11-14 12:00:00",
    "branch": "main",
    "commit": {
        "commit": "abc123...",
        "author": "Your Name",
        "message": "Latest commit message"
    }
}
```

---

### **5.2 Test with Git Push**

```bash
# Make a small change
echo "# Test deploy" >> README.md
git add .
git commit -m "Test auto-deploy"
git push origin main

# Check deploy.log on server
ssh u734000704@id-dci-web1785.hosting-data.io
cd /home/u734000704/domains/khalidsaifullah.me/public_html
tail -f deploy.log
```

---

## 📊 Step 6: Monitor Deployments

### **6.1 View Deploy Log**

```bash
# SSH to server
ssh u734000704@id-dci-web1785.hosting-data.io

cd /home/u734000704/domains/khalidsaifullah.me/public_html

# View last 50 lines
tail -50 deploy.log

# Follow log in real-time
tail -f deploy.log

# Search for errors
grep ERROR deploy.log
```

---

### **6.2 Log Format**

```
[2025-11-14 12:00:00] [INFO] Deploy request received from 140.82.112.5
[2025-11-14 12:00:00] [INFO] IP check passed: 140.82.112.5
[2025-11-14 12:00:00] [INFO] Signature verified
[2025-11-14 12:00:00] [INFO] Push event detected for branch: main
[2025-11-14 12:00:00] [INFO] Starting git pull...
[2025-11-14 12:00:01] [SUCCESS] Git pull successful
[2025-11-14 12:00:01] [SUCCESS] Deploy completed successfully
[2025-11-14 12:00:01] [INFO] Deploy process finished
```

---

## 🔒 Security Features

### **IP Whitelist**
- ✅ Your IP: `180.252.241.181`
- ✅ GitHub webhook IPs (CIDR ranges)
- ✅ Localhost for testing

### **Secret Verification**
- ✅ GitHub signature validation (HMAC SHA-256)
- ✅ Prevents unauthorized deploys

### **Branch Protection**
- ✅ Only deploys specified branch
- ✅ Ignores other branches

### **Logging**
- ✅ All deploy attempts logged
- ✅ Success/failure tracking
- ✅ Error details captured

---

## 🛠️ Troubleshooting

### **Issue: "Permission denied (publickey)"**

**Solution:**
```bash
# Check SSH key
ssh -T git@github.com

# If fails, regenerate and add to GitHub
ssh-keygen -t ed25519 -C "u734000704@hostinger"
cat ~/.ssh/id_ed25519.pub
# Add to GitHub → Settings → SSH keys
```

---

### **Issue: "fatal: could not read Username"**

**Solution:**
```bash
# Change remote to SSH
cd /home/u734000704/domains/khalidsaifullah.me/public_html
git remote set-url origin git@github.com:HolitSky/my-profile.git
```

---

### **Issue: "Access denied" in deploy.log**

**Solution:**
```php
// Add GitHub webhook IPs to deploy.php
define('DEPLOY_ALLOWED_IPS', [
    '127.0.0.1',
    '180.252.241.181',
    '140.82.112.0/20',     // Add these
    '143.55.64.0/20',
    '192.30.252.0/22',
    '185.199.108.0/22',
]);
```

---

### **Issue: "Invalid signature"**

**Solution:**
```php
// Make sure secret matches in both places:
// 1. deploy.php
define('DEPLOY_SECRET', 'your-secret-here');

// 2. GitHub webhook settings
// Secret: your-secret-here (same value)
```

---

### **Issue: Deploy works but changes not visible**

**Solution:**
```bash
# Clear OPcache
# Create clear-cache.php:
<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared!";
}
?>

# Or restart PHP-FPM via Hostinger panel
```

---

## 📱 Step 7: Add Deploy Notification (Optional)

### **7.1 Telegram Notification**

Add to `deploy.php` after successful deploy:

```php
function sendTelegramNotification($message) {
    $botToken = 'YOUR_BOT_TOKEN';
    $chatId = 'YOUR_CHAT_ID';
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// After successful deploy:
$message = "✅ Deploy successful!\n";
$message .= "Branch: " . GIT_BRANCH . "\n";
$message .= "Commit: " . substr($status['commit'], 0, 7) . "\n";
$message .= "Author: " . $status['author'] . "\n";
$message .= "Message: " . $status['message'];
sendTelegramNotification($message);
```

---

## ✅ Deployment Workflow

```
1. Developer pushes code to GitHub
   ↓
2. GitHub sends webhook to deploy.php
   ↓
3. deploy.php verifies:
   - IP whitelist ✓
   - Secret signature ✓
   - Branch name ✓
   ↓
4. Execute git pull on server
   ↓
5. Log results to deploy.log
   ↓
6. Return JSON response
   ↓
7. Changes live on website! 🚀
```

---

## 🎯 Quick Commands

```bash
# SSH to server
ssh u734000704@id-dci-web1785.hosting-data.io

# Go to project
cd domains/khalidsaifullah.me/public_html

# View logs
tail -f deploy.log

# Manual pull
git pull origin main

# Check status
git status

# View last commit
git log -1

# Test webhook
curl -X POST https://www.khalidsaifullah.me/deploy.php
```

---

## 🔐 Security Checklist

- ✅ Strong secret token set
- ✅ IP whitelist configured
- ✅ SSH key passwordless (for automation)
- ✅ File permissions set correctly
- ✅ Deploy log protected (.gitignore)
- ✅ HTTPS enabled on webhook URL

---

## 📚 Additional Resources

- [GitHub Webhooks Documentation](https://docs.github.com/en/webhooks)
- [GitHub Webhook IPs](https://api.github.com/meta)
- [Hostinger SSH Guide](https://support.hostinger.com/en/articles/1583245-how-to-use-ssh)

---

**Auto-deploy setup complete! Push to GitHub and watch it deploy automatically!** 🚀✅
