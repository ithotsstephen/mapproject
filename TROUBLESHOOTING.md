# 🚨 Troubleshooting Guide for projectsdemo.link/maproject

## Common Issues & Solutions

### Issue 1: "Site Not Showing Up" 
**Possible causes:**
- Files not uploaded to correct location
- Wrong folder structure
- Server configuration issues
- Database not set up

**Solutions:**

#### Step 1: Check File Upload Location
Make sure files are uploaded to the correct directory:
```
/public_html/maproject/     ← Your files should be here
├── index.php
├── db.php
├── admin/
├── suladmin/
└── ...
```

**NOT in:**
- `/public_html/` (root)
- `/public_html/www/maproject/`
- `/maproject/` (without public_html)

#### Step 2: Test Basic Functionality
1. **Upload the `test.php` file** to `/public_html/maproject/`
2. **Visit**: `https://projectsdemo.link/maproject/test.php`
3. **This will show you**:
   - If PHP is working
   - File structure status
   - Server configuration
   - What's missing

#### Step 3: Check File Permissions
Set these permissions via cPanel File Manager:
- **Folders**: 755 (including uploads/)
- **Files**: 644
- **uploads/ folder**: 777 (if 755 doesn't work)

---

### Issue 2: "Database Connection Errors"

**Error messages you might see:**
- "Database connection failed"
- "Access denied for user"
- "Unknown database"

**Solutions:**

#### Step 1: Create Database in cPanel
1. Go to **MySQL Databases** in cPanel
2. **Create database**: `projectsdemo_maproject` (or similar)
3. **Create user**: `projectsdemo_user` (with strong password)
4. **Add user to database** with ALL PRIVILEGES

#### Step 2: Update db.php
Edit `/public_html/maproject/db.php`:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'YOUR_ACTUAL_DATABASE_NAME',    // From cPanel
    'username' => 'YOUR_ACTUAL_USERNAME',       // From cPanel
    'password' => 'YOUR_ACTUAL_PASSWORD',       // From cPanel
    'charset' => 'utf8mb4',
];
```

#### Step 3: Import Database Schema
1. Go to **phpMyAdmin** in cPanel
2. Select your database
3. **Import** `database_schema_hosting.sql`

---

### Issue 3: "404 Not Found" or "File Not Found"

**Check these URLs step by step:**

1. **Test file**: `https://projectsdemo.link/maproject/test.php`
   - Should show diagnostic information
   - If this doesn't work, files aren't uploaded correctly

2. **Homepage**: `https://projectsdemo.link/maproject/`
   - Should redirect to `index.php`
   - If not working, check if `index.php` exists

3. **Direct homepage**: `https://projectsdemo.link/maproject/index.php`
   - Should show the main website
   - If error, check database configuration

---

### Issue 4: "Internal Server Error" (Error 500)

**Common causes:**
- Syntax errors in PHP files
- Wrong file permissions
- .htaccess issues
- PHP version compatibility

**Quick fixes:**
1. **Rename .htaccess temporarily** to `.htaccess.bak`
2. **Check error logs** in cPanel
3. **Test with just index.php**

---

### Issue 5: "Blank Page" or "White Screen"

**Causes:**
- PHP fatal errors
- Database connection issues
- Missing files

**Solutions:**
1. **Enable error reporting** - Add to top of index.php:
   ```php
   <?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ?>
   ```

2. **Check error logs** in cPanel

---

## Quick Diagnostic Checklist

### ✅ Upload Checklist:
- [ ] Files uploaded to `/public_html/maproject/`
- [ ] All folders preserved (admin/, suladmin/, uploads/, etc.)
- [ ] File permissions set correctly
- [ ] .htaccess file uploaded

### ✅ Database Checklist:
- [ ] Database created in cPanel
- [ ] Database user created with strong password
- [ ] User added to database with ALL PRIVILEGES
- [ ] `database_schema_hosting.sql` imported successfully
- [ ] `db.php` updated with correct credentials

### ✅ Testing Checklist:
- [ ] `https://projectsdemo.link/maproject/test.php` works
- [ ] `https://projectsdemo.link/maproject/` shows homepage
- [ ] `https://projectsdemo.link/maproject/admin/` shows admin login
- [ ] Login works with default credentials

---

## Emergency Reset Steps

If nothing works, try this complete reset:

1. **Delete everything** in `/public_html/maproject/`
2. **Re-upload** the entire `projectsdemo-maproject-final.zip`
3. **Extract** directly in `/public_html/maproject/`
4. **Set permissions**: folders=755, files=644, uploads=777
5. **Create fresh database** in cPanel
6. **Import** `database_schema_hosting.sql`
7. **Update** `db.php` with new credentials
8. **Test** with `test.php` first

---

## Contact Information

If you're still having issues, check:
1. **cPanel Error Logs**
2. **PHP Error Logs** 
3. **Server Status** in cPanel
4. **PHP Version** (should be 7.4+)

The most common issue is files not being in the correct `/public_html/maproject/` directory!