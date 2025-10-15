<?php
// Password hash generator for updated credentials
// Run this script to get the correct password hashes

echo "=== UPDATED CREDENTIAL HASHES ===\n\n";

// Super Admin Credentials
$superadmin_password = 'ProjectDemo2024!';
$superadmin_hash = password_hash($superadmin_password, PASSWORD_DEFAULT);

echo "Super Admin:\n";
echo "Username: projectadmin\n";
echo "Password: $superadmin_password\n";
echo "Hash: $superadmin_hash\n\n";

// Regular Admin Credentials  
$admin_password = 'DemoAdmin2024!';
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);

echo "Regular Admin:\n";
echo "Username: demoAdmin\n";
echo "Password: $admin_password\n";
echo "Hash: $admin_hash\n\n";

echo "=== SQL INSERT STATEMENTS ===\n\n";

echo "-- Super Admin User\n";
echo "INSERT INTO users (name, username, email, password, role, status, created_at) VALUES\n";
echo "('Project Administrator', 'projectadmin', 'admin@projectsdemo.link', '$superadmin_hash', 'super_admin', 'active', NOW());\n\n";

echo "-- Regular Admin User\n";  
echo "INSERT INTO users (name, username, email, password, role, status, created_at) VALUES\n";
echo "('Demo Admin User', 'demoAdmin', 'demoadmin@projectsdemo.link', '$admin_hash', 'admin', 'active', NOW());\n\n";

echo "=== LOGIN URLS ===\n\n";
echo "Super Admin Panel: https://projectsdemo.link/maproject/suladmin/\n";
echo "Admin Panel: https://projectsdemo.link/maproject/admin/\n";
echo "Public Site: https://projectsdemo.link/maproject/\n\n";

echo "⚠️ IMPORTANT: Change these passwords immediately after first login!\n";
?>