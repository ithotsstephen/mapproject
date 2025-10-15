# 🚨 Database Connection Error - Fix Guide

## The Problem
You're seeing "Database connection failed. Please try again later." because the database credentials in `db.php` are not correctly configured for your hosting environment.

## 🔧 Step-by-Step Solution

### Step 1: Get Your Database Details from cPanel
1. **Login to your hosting cPanel** (usually at yourdomain.com/cpanel or through your hosting provider)
2. **Find "MySQL Databases"** in the cPanel dashboard
3. **Note down these details:**
   - Database name: `username_databasename` (e.g., `projectsdemo_maproject`)
   - Database user: `username_dbuser` (e.g., `projectsdemo_dbuser`)  
   - Database password: The password you set when creating the database

### Step 2: Update Your db.php File
Replace the placeholder values in `/public_html/maproject/db.php`:

```php
$config = [
    'host' => 'localhost',
    'dbname' => 'YOUR_ACTUAL_DATABASE_NAME',     // Replace with actual name from cPanel
    'username' => 'YOUR_ACTUAL_DB_USERNAME',     // Replace with actual username from cPanel
    'password' => 'YOUR_ACTUAL_DB_PASSWORD',     // Replace with actual password from cPanel
    'charset' => 'utf8mb4',
    // ... rest stays the same
];
```

### Step 3: Import the Database (if not done yet)
1. In cPanel, go to **phpMyAdmin**
2. Select your database from the left sidebar
3. Click **Import** tab
4. Choose the `database_updated_credentials.sql` file
5. Click **Go** to import

### Step 4: Test the Connection
1. Upload the `database_test.php` file to your server
2. Visit: `https://projectsdemo.link/maproject/database_test.php`
3. This will show you exactly what's wrong and how to fix it

## 📋 Common Database Names by Hosting Provider

### GoDaddy/cPanel Hosting:
- **Database name:** `cpanelusername_dbname`
- **Username:** `cpanelusername_dbuser`
- **Host:** `localhost`

### Example for projectsdemo.link:
If your cPanel username is `projectsdemo`:
- **Database name:** `projectsdemo_maproject`
- **Username:** `projectsdemo_mauser`
- **Password:** [whatever you set]

## 🔍 How to Find Your cPanel Username

1. **Check your hosting welcome email** - usually contains cPanel details
2. **Login to your hosting provider's dashboard** - look for cPanel access
3. **Contact your hosting support** if you can't find the details

## ⚠️ Security Note

After fixing the database connection, **immediately change the default passwords**:
- Super Admin: `projectadmin` / `ProjectDemo2024!`
- Regular Admin: `demoAdmin` / `DemoAdmin2024!`

## 🆘 Still Having Issues?

1. **Check the error logs** in cPanel > Error Logs
2. **Run the database test script** for detailed diagnostics
3. **Contact your hosting provider** - they can help with database setup
4. **Verify your domain is pointing to the correct directory** (`/public_html/maproject/`)

## 📞 Quick Checklist

- [ ] Database created in cPanel
- [ ] Database user created and assigned to database  
- [ ] SQL file imported successfully
- [ ] db.php updated with correct credentials
- [ ] Files uploaded to `/public_html/maproject/` directory
- [ ] Database test script shows connection success

Once you complete these steps, your application should work properly!