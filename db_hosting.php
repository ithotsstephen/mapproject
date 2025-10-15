<?php
/**
 * Database Configuration for GoDaddy Shared Hosting
 * Update the credentials below with your actual database information
 */

// Database Configuration for projectsdemo.link/maproject
// IMPORTANT: Update these values with your actual database credentials from cPanel
$host = 'localhost';  // Usually 'localhost' for shared hosting
$dbname = 'u232365723_mapproject01';  // Replace with your actual database name from cPanel
$username = 'u232365723_mapproject01';  // Replace with your actual database username
$password = 'Ithots*123!@#';  // Replace with your actual database password

// Alternative configuration for different hosting providers
// Uncomment and modify if not using GoDaddy:
/*
$host = 'your-host.com';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';
*/

try {
    // Create PDO connection with error mode and charset
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration or contact support.");
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

/**
 * Utility Functions
 */

// Sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Format date for display
function format_date($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

// Truncate text with ellipsis
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Validate file upload
function validate_file($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 10485760) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return 'Invalid file parameters';
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'File upload error';
    }
    
    if ($file['size'] > $max_size) {
        return 'File too large (max 10MB)';
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    
    $allowed_mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg', 
        'png' => 'image/png',
        'gif' => 'image/gif',
        'mp4' => 'video/mp4'
    ];
    
    $ext = array_search($mime, $allowed_mimes, true);
    
    if ($ext === false || !in_array($ext, $allowed_types)) {
        return 'Invalid file type';
    }
    
    return true; // File is valid
}

// Get categories for dropdown
function get_categories($status = 'active') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE status = ? ORDER BY name");
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

// Get Indian states list
function get_indian_states() {
    return [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 
        'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 
        'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 
        'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 
        'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Delhi', 
        'Jammu and Kashmir', 'Ladakh', 'Chandigarh', 
        'Dadra and Nagar Haveli and Daman and Diu', 'Lakshadweep', 
        'Puducherry', 'Andaman and Nicobar Islands'
    ];
}

// Get posts count by state for map
function get_posts_by_state() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT state, COUNT(*) as count 
        FROM posts 
        WHERE status = 'published' AND state IS NOT NULL 
        GROUP BY state
    ");
    
    $result = [];
    while ($row = $stmt->fetch()) {
        $result[$row['state']] = $row['count'];
    }
    return $result;
}

// Get statistics for dashboard
function get_dashboard_stats() {
    global $pdo;
    
    $stats = [];
    
    // Total published posts
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
    $stats['total_posts'] = $stmt->fetchColumn();
    
    // Total categories
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'");
    $stats['total_categories'] = $stmt->fetchColumn();
    
    // Total states with posts
    $stmt = $pdo->query("SELECT COUNT(DISTINCT state) FROM posts WHERE status = 'published' AND state IS NOT NULL");
    $stats['states_covered'] = $stmt->fetchColumn();
    
    // Recent posts (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['recent_posts'] = $stmt->fetchColumn();
    
    return $stats;
}

// Environment check for hosting compatibility
function check_hosting_environment() {
    $issues = [];
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $issues[] = 'PHP version 7.4+ required. Current: ' . PHP_VERSION;
    }
    
    // Check required extensions
    $required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'gd', 'fileinfo'];
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $issues[] = "Required PHP extension missing: $ext";
        }
    }
    
    // Check upload directory
    if (!is_writable('uploads')) {
        $issues[] = 'Uploads directory is not writable. Set permissions to 755 or 777.';
    }
    
    return $issues;
}

?>