<?php
// Super Admin Authentication Check
function check_super_admin_auth() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
        header('Location: index.php');
        exit();
    }
}

// Check if session is valid (user still exists and is active)
function validate_super_admin_session() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'super_admin' AND status = 'active'");
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
function logout_super_admin() {
    session_start();
    session_destroy();
    header('Location: index.php');
    exit();
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

// Activity logging function
function log_super_admin_activity($action, $details = '') {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
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
    }
}
?>