# 🔄 Frontend CMS Integration

## Data Sekarang Dinamis!

Frontend Anda sekarang sudah terintegrasi dengan CMS. Data akan otomatis load dari database via API.

---

## ✅ Yang Sudah Dilakukan:

### 1. **Script Integration**
Added to `index.html`:
```html
<script src="./assets/js/cms-integration.js"></script>
```

### 2. **CMS Integration Script**
File: `assets/js/cms-integration.js`
- ✅ Fetch data dari API
- ✅ LocalStorage caching (5 minutes)
- ✅ Auto-update frontend
- ✅ Error handling

---

## 🎯 How It Works:

### **Flow:**
```
1. Page loads (index.html)
   ↓
2. cms-integration.js runs
   ↓
3. Check LocalStorage cache
   ↓
4. If cache expired or empty:
   → Fetch from API (./api/index.php)
   ↓
5. Update HTML elements dynamically
   ↓
6. Cache data for 5 minutes
```

### **What Gets Updated:**

✅ **About Section**
- Title
- Description (paragraphs)

✅ **Contact Info**
- Email
- Phone
- Birthday
- Location
- Social links (LinkedIn, Instagram)

✅ **Services**
- Service title
- Service description
- Service icon

✅ **Skills**
- Skill name
- Proficiency percentage
- Category

✅ **Experience**
- Job title
- Company
- Location
- Dates
- Description

✅ **Education**
- Degree
- Institution
- Location
- Dates
- Description

✅ **Portfolio Projects**
- Project title
- Category
- Description
- Image
- Links
- Technologies

---

## 🧪 Testing:

### **Step 1: Run Migration (If Not Done)**
```
http://localhost/my-profile/database/run-migrations.php
```
Click "Run Migration & Seeder"

### **Step 2: Verify API Works**
```
http://localhost/my-profile/api/index.php
```

**Expected:**
```json
{
  "success": true,
  "data": {
    "about": {...},
    "contact": {...},
    "skills": [...],
    "experience": [...],
    ...
  }
}
```

### **Step 3: Open Frontend**
```
http://localhost/my-profile/index.html
```

### **Step 4: Check Browser Console**
Press `F12` → Console tab

**Expected logs:**
```
Fetching portfolio data from API...
Using cached data (or) Data fetched successfully
Updating about section...
Updating contact info...
Updating services...
...
```

### **Step 5: Verify Data Updated**
Check if data from database appears on page:
- About section text
- Contact email/phone
- Services list
- Skills with percentages
- Experience timeline
- Education history
- Portfolio projects

---

## 🎛️ Configuration:

### **Enable/Disable CMS**

Edit `assets/js/cms-integration.js`:

```javascript
// Enable CMS (use database data)
const USE_CMS = true;

// Disable CMS (use static HTML)
const USE_CMS = false;
```

### **Change Cache Duration**

```javascript
// 5 minutes (default)
const CACHE_DURATION = 5 * 60 * 1000;

// 10 minutes
const CACHE_DURATION = 10 * 60 * 1000;

// 1 hour
const CACHE_DURATION = 60 * 60 * 1000;

// No cache (always fetch)
const CACHE_DURATION = 0;
```

### **Change API URL**

```javascript
// Relative path (default)
const API_URL = './api/index.php';

// Absolute path
const API_URL = 'http://localhost/my-profile/api/index.php';

// Production
const API_URL = 'https://yourdomain.com/api/index.php';
```

---

## 🔄 Update Workflow:

### **Admin Updates Content:**

1. **Login to Admin Panel**
   ```
   http://localhost/my-profile/admin/login.php
   ```

2. **Update Content**
   - Edit About section
   - Add/Edit Skills
   - Add/Edit Experience
   - Add/Edit Portfolio projects
   - etc.

3. **Save Changes**
   - Data saved to database

### **Frontend Auto-Updates:**

1. **User visits website**
   ```
   http://localhost/my-profile/
   ```

2. **CMS Integration runs**
   - Checks cache (5 min)
   - If expired: fetch from API
   - Updates HTML dynamically

3. **User sees latest content**
   - No page reload needed (after cache expires)
   - Fresh data from database

---

## 🐛 Troubleshooting:

### **Data Not Updating**

**Problem:** Frontend still shows old/static data

**Solutions:**

1. **Clear Cache:**
```javascript
// Open browser console (F12)
localStorage.removeItem('portfolio_data');
// Reload page
```

2. **Check API:**
```
http://localhost/my-profile/api/index.php
```
Should return JSON with data

3. **Check Console:**
Press F12 → Console
Look for errors

4. **Verify Database:**
```
http://localhost/my-profile/test-db.php
```
Check tables have data

### **API Returns Empty**

**Problem:** API returns success but no data

**Solution:**
```
1. Run migrations: database/run-migrations.php
2. Check database has data
3. Verify API query in api/index.php
```

### **Console Errors**

**Error:** "Failed to fetch"
**Solution:** Check API URL is correct

**Error:** "Unexpected token"
**Solution:** API not returning valid JSON

**Error:** "CORS error"
**Solution:** API should have CORS headers (already included)

---

## 📊 Cache Management:

### **Clear Cache Manually:**

```javascript
// Browser console (F12)
localStorage.removeItem('portfolio_data');
```

### **Clear All Cache:**

```javascript
// Browser console
localStorage.clear();
```

### **View Cached Data:**

```javascript
// Browser console
const cached = localStorage.getItem('portfolio_data');
console.log(JSON.parse(cached));
```

---

## 🎯 Best Practices:

### **Development:**
```javascript
// Short cache for testing
const CACHE_DURATION = 1 * 60 * 1000; // 1 minute

// Or no cache
const CACHE_DURATION = 0;
```

### **Production:**
```javascript
// Longer cache for performance
const CACHE_DURATION = 10 * 60 * 1000; // 10 minutes
```

### **After Content Update:**
```
Tell users to:
1. Hard refresh (Ctrl + Shift + R)
2. Or wait for cache to expire (5 min)
```

---

## ✅ Summary:

### **What Changed:**

**Before:**
- ❌ Static HTML data
- ❌ Need to edit HTML manually
- ❌ No admin panel

**After:**
- ✅ Dynamic data from database
- ✅ Update via admin panel
- ✅ Auto-sync with frontend
- ✅ Caching for performance
- ✅ No HTML editing needed

### **How to Update Content:**

1. Login admin panel
2. Edit content
3. Save
4. Frontend auto-updates (after cache expires)

**That's it!** 🎉

---

## 🚀 Next Steps:

1. ✅ Test frontend integration
2. ✅ Update content via admin panel
3. ✅ Verify data appears on frontend
4. ✅ Deploy to Hostinger
5. ✅ Update API_URL for production

---

**Your portfolio is now fully dynamic! 🎉**

Update content anytime via admin panel, no coding needed!

---

**Version:** 1.0.0
**Integration:** Complete
**Status:** ✅ Working
