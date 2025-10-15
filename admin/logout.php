<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

log_admin_activity('Logged Out');
logout_admin();
?>