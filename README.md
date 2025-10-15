# Persecution Tracker Web Application
## Complete Setup Guide for GoDaddy Hosting

### Overview
This is a complete web application built with PHP and MySQL for tracking persecution incidents across Indian states. It includes:

- **Public Website**: Interactive India map, state-wise reports, detailed post views
- **Admin Panel**: For regular admins to create and manage posts
- **Super Admin Panel**: For managing admins, categories, and system oversight

### System Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for clean URLs)

---

## Installation Instructions

### Step 1: Database Setup

1. **Create Database**
   - Login to your GoDaddy cPanel
   - Go to MySQL Databases
   - Create a new database named `incident_tracker`
   - Create a database user with full privileges

2. **Import Database Schema**
   - Go to phpMyAdmin
   - Select your database
   - Import the `database_schema.sql` file
   - This will create all necessary tables and sample data

### Step 2: File Upload

1. **Upload Files**
   - Extract the project files
   - Upload all files to your domain's public_html folder via File Manager or FTP
   - Ensure proper folder structure is maintained

2. **Set Permissions**
   - Set permissions for `uploads/` folder to 755 or 777
   - Ensure PHP can write to this directory

### Step 3: Configuration

1. **Database Configuration**
   - Edit `db.php` file
   - Update database credentials:
     ```php
     $host = 'localhost'; // Usually localhost on GoDaddy
     $dbname = 'your_database_name';
     $username = 'your_db_username'; 
     $password = 'your_db_password';
     ```

2. **Security Settings**
   - Change default admin passwords immediately after setup
   - Review security settings in `db.php`

### Step 4: Create Super Admin Account

Run this SQL query in phpMyAdmin to create your super admin account:

```sql
INSERT INTO users (name, username, email, password, role, status, created_at) 
VALUES (
    'Super Administrator',
    'superadmin',
    'admin@yourdomain.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'super_admin',
    'active',
    NOW()
);
```

**⚠️ IMPORTANT**: Change the password immediately after first login!

### Step 5: Access URLs

After installation, access your application:

- **Public Website**: `https://projectsdemo.link/maproject/`
- **Admin Panel**: `https://projectsdemo.link/maproject/admin/`
- **Super Admin Panel**: `https://projectsdemo.link/maproject/suladmin/`

---

## Features Overview

### Public Website Features

1. **Dynamic JSON Data Display**: Each state now shows:
   - State name
   - Number (custom numeric value)
   - Population (formatted with commas)
   - Area in km² (formatted with commas)

2. **Enhanced Tooltips**: Improved styling with:
   - Better readability
   - Rounded corners
   - Enhanced shadows
   - Proper spacing

### Sample Data Format:

When you hover over any state, you'll see data like:
```
West Bengal
Number: 26
Population: 91,347,736
Area: 88,752 km²
```

### How to Customize:

1. **Modify state-data.js** to update the numbers for each state
2. **Change data fields** by editing the `getStateDataString()` function
3. **Adjust styling** in map-style.css for the `#tryjstip` element

### Files Modified:

- `js/state-data.js` - Contains all the JSON data for states
- `js/map-config.js` - Updated hover configurations 
- `css/map-style.css` - Enhanced tooltip styling
- `index.html` - Added state-data.js reference

### Next Steps:

You can easily modify the numeric values in `state-data.js` to match your specific requirements. The system is designed to be flexible and easily maintainable.

The map is now running on http://localhost:8080 - hover over any state to see the JSON data in action!