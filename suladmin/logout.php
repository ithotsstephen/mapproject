<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

// Log the logout activity before destroying session
if (isset($_SESSION['user_id'])) {
    log_super_admin_activity('Logged Out');
}

logout_super_admin();
?>