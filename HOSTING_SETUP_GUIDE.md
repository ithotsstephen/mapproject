# 🔧 GoDaddy Hosting Setup Guide
## Fix for Database Import Error

### ⚠️ **The Issue You Encountered**
The error `#1044 - Access denied for user to database 'incident_tracker'` occurs because:
1. You're trying to CREATE DATABASE via SQL, but shared hosting doesn't allow this
2. You need to create the database through cPanel first
3. Then import only the table structure (not database creation commands)

---

## 📋 **Step-by-Step Solution**

### **Step 1: Create Database via cPanel** 
1. **Login to your GoDaddy cPanel**
2. **Find "MySQL Databases" section**
3. **Create a new database:**
   - Database name: `mapprojectoct` (or any name you prefer)
   - It will become something like: `u232365723_mapprojectoct`
4. **Create a database user:**
   - Username: Choose a username
   - Password: Create a strong password
   - **IMPORTANT**: Write down these credentials!
5. **Add user to database:**
   - Select the user and database
   - Grant "All Privileges"

### **Step 2: Use the Correct SQL File**
**Don't use** `database_schema.sql` ❌  
**Use** `database_schema_hosting.sql` ✅ (I just created this for you)

This new file:
- ✅ No database creation commands
- ✅ Compatible with shared hosting
- ✅ Includes sample data
- ✅ Has proper indexes for performance

### **Step 3: Import the Correct SQL**
1. **Go to phpMyAdmin in cPanel**
2. **Select your database** (e.g., u232365723_mapprojectoct)
3. **Click "Import" tab**
4. **Choose file**: `database_schema_hosting.sql`
5. **Click "Go"**
6. **Should import successfully!** ✅

### **Step 4: Configure Database Connection**
1. **Edit `db.php` file** OR use the new `db_hosting.php`
2. **Update these values:**
   ```php
   $host = 'localhost';
   $dbname = 'projectsdemo_maproject';  // Your actual database name from cPanel
   $username = 'projectsdemo_maproject'; // Your actual username from cPanel 
   $password = 'YourActualPassword';      // Your actual password from cPanel
   ```

---

## 🎯 **Quick Fix Instructions**

### **Option A: Use New Files (Recommended)**
1. **Delete old `database_schema.sql`**
2. **Rename `database_schema_hosting.sql` to `database_schema.sql`**
3. **Rename `db_hosting.php` to `db.php`** (backup the old one first)
4. **Follow steps 1-4 above**

### **Option B: Manual Fix**
1. **Create database via cPanel first**
2. **Edit existing `database_schema.sql`:**
   - Remove the line: `CREATE DATABASE IF NOT EXISTS incident_tracker;`
   - Remove the line: `USE incident_tracker;`
3. **Import the edited file**

---

## 🔍 **Verify Installation**

After importing, your database should have these tables:
- ✅ `users` (with 2 sample users)
- ✅ `categories` (with 8 categories)  
- ✅ `posts` (with 3 sample posts)
- ✅ `states` (with all Indian states)
- ✅ `districts` 
- ✅ `admin_activity_log`

**Test the application:**
1. **Visit your website**: `https://projectsdemo.link/maproject/`
2. **Should show homepage with map** ✅
3. **Login to admin**: `https://projectsdemo.link/maproject/admin/`
   - Username: `admin`
   - Password: `admin123`
4. **Login to super admin**: `https://projectsdemo.link/maproject/suladmin/`
   - Username: `superadmin`
   - Password: `password`

---

## 🚨 **Important Security Notes**

### **Change Default Passwords Immediately:**
```sql
-- Change super admin password
UPDATE users SET password = '$2y$10$NewHashedPassword' WHERE username = 'superadmin';

-- Change admin password  
UPDATE users SET password = '$2y$10$NewHashedPassword' WHERE username = 'admin';
```

### **Set Folder Permissions:**
- `uploads/` folder: **755** or **777** (so PHP can write files)
- All other files: **644**
- All other folders: **755**

---

## 🆘 **If You Still Have Issues**

### **Common Problems & Solutions:**

**Problem**: "Access denied" errors
**Solution**: Double-check database credentials in `db.php`

**Problem**: "Table doesn't exist" errors  
**Solution**: Make sure SQL import was successful

**Problem**: File upload errors
**Solution**: Check `uploads/` folder permissions (755/777)

**Problem**: Login doesn't work
**Solution**: Verify users table has data, check passwords

### **Check Your Environment:**
Add this to any PHP file temporarily to check your setup:
```php
<?php
// Check database connection
require_once 'db.php';
echo "Database connection: OK<br>";

// Check PHP version
echo "PHP Version: " . PHP_VERSION . "<br>";

// Check upload directory
echo "Uploads writable: " . (is_writable('uploads') ? 'YES' : 'NO') . "<br>";
?>
```

---

## 📞 **Need Help?**

If you're still having issues:
1. **Check cPanel error logs**
2. **Verify database name/credentials are correct**
3. **Make sure you're using `database_schema_hosting.sql`**
4. **Confirm folder permissions are set correctly**

The application should work perfectly once the database is properly imported! 🎉