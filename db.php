<?php
/**
 * Database Connection Configuration
 * This file provides a secure PDO connection to MySQL database
 */

// Database configuration for projectsdemo.link/maproject
$config = [
    'host' => 'localhost',
    'dbname' => 'u232365723_mapproject01', // Update with your actual database name from cPanel
    'username' => 'u232365723_mapproject01', // Update with your actual database username
    'password' => 'Ithots*123!@#', // Update with your actual database password
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

$pdo = null;
$lastException = null;
$primaryDsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$fallbackDsn = "mysql:host={$config['host']};dbname=incident_tracker;charset={$config['charset']}";

try {
    $pdo = new PDO($primaryDsn, $config['username'], $config['password'], $config['options']);
} catch (PDOException $e) {
    $lastException = $e;
    error_log("Primary DB connection failed: " . $e->getMessage());

    // Attempt fallback DB name if primary fails
    try {
        $pdo = new PDO($fallbackDsn, $config['username'], $config['password'], $config['options']);
        error_log("Connected to fallback DB 'incident_tracker' successfully.");
    } catch (PDOException $e2) {
        $lastException = $e2;
        error_log("Fallback DB connection failed: " . $e2->getMessage());

        // If debug flag present in HTTP request, show the error message (temporary troubleshooting aid)
        if (php_sapi_name() !== 'cli' && isset($_GET['debug']) && $_GET['debug'] == '1') {
            // Escaping message to avoid XSS when echoed into browser
            die("Database connection failed: " . htmlspecialchars($lastException->getMessage(), ENT_QUOTES, 'UTF-8'));
        }

        // Generic failure message for production
        die("Database connection failed. Please try again later.");
    }
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

// Check if user is logged in
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}

// Check if user has specific role
function check_role($required_role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
        header('Location: ../index.php');
        exit();
    }
}

// Generate secure file name
function generate_filename($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

// Validate uploaded file
function validate_file_upload($file, $allowed_types, $max_size = 5242880) { // 5MB default
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Maximum size: ' . ($max_size / 1024 / 1024) . 'MB'];
    }
    
    $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    return ['success' => true];
}

// Get states for dropdown
function get_states($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM states ORDER BY name");
    return $stmt->fetchAll();
}

// Get districts by state ID
function get_districts_by_state($pdo, $state_id) {
    $stmt = $pdo->prepare("SELECT id, name FROM districts WHERE state_id = ? ORDER BY name");
    $stmt->execute([$state_id]);
    return $stmt->fetchAll();
}

// Get categories for dropdown
function get_categories($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// Get post counts by state for map
function get_post_counts_by_state($pdo) {
    $stmt = $pdo->query("
        SELECT state, COUNT(*) as count 
        FROM posts 
        WHERE status = 'published' 
        GROUP BY state
    ");
    return $stmt->fetchAll();
}

// Format date for display
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Truncate text for previews
function truncate_text($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Get Indian states for dropdown
function get_indian_states() {
    return [
        'Andhra Pradesh' => 'Andhra Pradesh',
        'Arunachal Pradesh' => 'Arunachal Pradesh', 
        'Assam' => 'Assam',
        'Bihar' => 'Bihar',
        'Chhattisgarh' => 'Chhattisgarh',
        'Goa' => 'Goa',
        'Gujarat' => 'Gujarat',
        'Haryana' => 'Haryana',
        'Himachal Pradesh' => 'Himachal Pradesh',
        'Jharkhand' => 'Jharkhand',
        'Karnataka' => 'Karnataka',
        'Kerala' => 'Kerala',
        'Madhya Pradesh' => 'Madhya Pradesh',
        'Maharashtra' => 'Maharashtra',
        'Manipur' => 'Manipur',
        'Meghalaya' => 'Meghalaya',
        'Mizoram' => 'Mizoram',
        'Nagaland' => 'Nagaland',
        'Odisha' => 'Odisha',
        'Punjab' => 'Punjab',
        'Rajasthan' => 'Rajasthan',
        'Sikkim' => 'Sikkim',
        'Tamil Nadu' => 'Tamil Nadu',
        'Telangana' => 'Telangana',
        'Tripura' => 'Tripura',
        'Uttar Pradesh' => 'Uttar Pradesh',
        'Uttarakhand' => 'Uttarakhand',
        'West Bengal' => 'West Bengal',
        'Andaman and Nicobar Islands' => 'Andaman and Nicobar Islands',
        'Chandigarh' => 'Chandigarh',
        'Dadra and Nagar Haveli and Daman and Diu' => 'Dadra and Nagar Haveli and Daman and Diu',
        'Delhi' => 'Delhi',
        'Jammu and Kashmir' => 'Jammu and Kashmir',
        'Ladakh' => 'Ladakh',
        'Lakshadweep' => 'Lakshadweep',
        'Puducherry' => 'Puducherry'
    ];
}
?>