<?php
/**
 * Database Connection Configuration
 * This file provides a secure PDO connection to MySQL database
 * 
 * ⚠️ IMPORTANT: Update the database credentials below with your actual cPanel details
 */

// ==========================================
// 🔧 UPDATE THESE DATABASE CREDENTIALS
// ==========================================
// Get these details from your cPanel > MySQL Databases section

$config = [
    'host' => 'localhost',  // Usually 'localhost' for shared hosting
    
    // 🚨 REPLACE THESE WITH YOUR ACTUAL CPANEL DATABASE DETAILS:
    'dbname' => 'YOUR_CPANEL_DATABASE_NAME',     // Example: username_maproject  
    'username' => 'YOUR_CPANEL_DB_USERNAME',     // Example: username_dbuser
    'password' => 'YOUR_CPANEL_DB_PASSWORD',     // The password you set in cPanel
    
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

// ==========================================
// 📋 HOW TO GET YOUR DATABASE DETAILS:
// ==========================================
// 1. Login to your cPanel (hosting control panel)
// 2. Go to "MySQL Databases" section
// 3. Your database name will be: cpanelusername_databasename
// 4. Your username will be: cpanelusername_dbuser  
// 5. Use the password you created for the database user
// 
// Example:
// If your cPanel username is 'projectsdemo' and you created:
// - Database: maproject
// - User: maproject_user
// Then your details would be:
// - dbname: projectsdemo_maproject
// - username: projectsdemo_maproject_user
// - password: [your chosen password]

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    // Test the connection
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Enhanced error logging for debugging
    $error_message = $e->getMessage();
    
    // Log detailed error for debugging (remove in production)
    error_log("Database connection failed: " . $error_message);
    error_log("Attempted connection to: {$config['host']} with database: {$config['dbname']} and user: {$config['username']}");
    
    // Check for common error patterns and provide helpful messages
    if (strpos($error_message, 'Access denied') !== false) {
        $user_message = "Database connection failed: Access denied. Please check your username and password in cPanel.";
    } elseif (strpos($error_message, 'Unknown database') !== false) {
        $user_message = "Database connection failed: Database not found. Please check your database name in cPanel.";
    } elseif (strpos($error_message, 'YOUR_CPANEL') !== false) {
        $user_message = "Database connection failed: Please update the database credentials in db.php with your actual cPanel details.";
    } else {
        $user_message = "Database connection failed. Please check your database configuration.";
    }
    
    die($user_message);
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Session configuration for security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

/**
 * Utility functions
 */

// Sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if file is a valid image
function is_valid_image($file_path) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = getimagesize($file_path);
    
    if ($file_info === false) {
        return false;
    }
    
    return in_array($file_info['mime'], $allowed_types);
}

// Check if file is a valid video
function is_valid_video($file_path) {
    $allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_path);
    finfo_close($finfo);
    
    return in_array($mime_type, $allowed_types);
}

// Safe file upload
function safe_file_upload($file, $upload_dir, $allowed_extensions = []) {
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Invalid file upload'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!empty($allowed_extensions) && !in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . '/' . $new_filename;
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $upload_path];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
}
?>