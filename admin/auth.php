<?php
// Admin Authentication Check
function check_admin_auth() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
        header('Location: index.php');
        exit();
    }
}

// Check if session is valid (user still exists and is active)
function validate_admin_session() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role IN ('admin', 'super_admin') AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        
        if (!$stmt->fetch()) {
            // User no longer exists or is inactive, destroy session
            session_destroy();
            header('Location: index.php');
            exit();
        }
    }
}

// Logout function
function logout_admin() {
    session_start();
    session_destroy();
    header('Location: index.php');
    exit();
}

// Generate CSRF token
function generate_admin_csrf_token() {
    if (!isset($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

// Verify CSRF token
function verify_admin_csrf_token($token) {
    return isset($_SESSION['admin_csrf_token']) && hash_equals($_SESSION['admin_csrf_token'], $token);
}

// Activity logging function
function log_admin_activity($action, $details = '') {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        // Check if admin_activity_log table exists, if not create a simple log
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admin_activity_log (admin_id, action, details, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            // If table doesn't exist, we'll skip logging for now
            error_log("Admin activity logging failed: " . $e->getMessage());
        }
    }
}

// File upload function
function handle_file_upload($file, $upload_dir = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp4']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return null; // No file uploaded
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    if ($file['size'] > 10000000) { // 10MB limit
        throw new RuntimeException('Exceeded filesize limit.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    $ext = array_search(
        $mime,
        [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4'
        ],
        true
    );

    if ($ext === false || !in_array($ext, $allowed_types)) {
        throw new RuntimeException('Invalid file format.');
    }

    // Create upload directory if it doesn't exist
    $full_upload_dir = '../' . $upload_dir;
    if (!is_dir($full_upload_dir)) {
        mkdir($full_upload_dir, 0755, true);
    }

    // Generate unique filename
    $filename = sprintf('%s_%s.%s',
        uniqid(),
        bin2hex(random_bytes(8)),
        $ext
    );

    $filepath = $full_upload_dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    return $upload_dir . $filename;
}
?>