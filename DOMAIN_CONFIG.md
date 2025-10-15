# Configuration for projectsdemo.link/maproject

## Database Setup Instructions

### Step 1: Create Database in cPanel
1. Login to your hosting cPanel
2. Go to "MySQL Databases"
3. Create new database: `projectsdemo_maproject` (or similar)
4. Create database user: `projectsdemo_maproject` (or similar)
5. Set a strong password
6. Add user to database with ALL PRIVILEGES

### Step 2: Update Database Configuration

Edit `db.php` with your actual credentials:

```php
$config = [
    'host' => 'localhost',
    'dbname' => 'projectsdemo_maproject',     // Your actual database name
    'username' => 'projectsdemo_maproject',   // Your actual username
    'password' => 'YourStrongPassword',       // Your actual password
    'charset' => 'utf8mb4',
];
```

### Step 3: Import Database Schema
1. Go to phpMyAdmin in cPanel
2. Select your database
3. Import `database_schema_hosting.sql`

### Step 4: Access Your Application

**Live URLs:**
- **Homepage**: https://projectsdemo.link/maproject/
- **Admin Panel**: https://projectsdemo.link/maproject/admin/
- **Super Admin**: https://projectsdemo.link/maproject/suladmin/

### Default Login Credentials

**Super Admin Login:**
- URL: https://projectsdemo.link/maproject/suladmin/
- Username: `superadmin`
- Password: `password`

**Admin Login:**
- URL: https://projectsdemo.link/maproject/admin/
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT: Change these passwords immediately after first login!**

### File Upload Configuration

Ensure the uploads directory has proper permissions:
```
/maproject/uploads/        (755 or 777)
/maproject/uploads/images/ (755 or 777)
/maproject/uploads/videos/ (755 or 777)
```

### Security Notes

1. **Change default passwords immediately**
2. **Use strong database passwords**
3. **Set proper file permissions**
4. **Keep regular backups**

### Troubleshooting

**If you get database connection errors:**
1. Verify database name in cPanel matches `db.php`
2. Check username and password are correct
3. Ensure database user has all privileges

**If file uploads don't work:**
1. Check uploads folder permissions (755 or 777)
2. Verify PHP has write access to uploads directory

**If login doesn't work:**
1. Confirm database import was successful
2. Check users table has the default accounts
3. Verify passwords are correct

Your persecution tracking application is configured for https://projectsdemo.link/maproject/