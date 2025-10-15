<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

$message = '';
$error = '';
$admin_id = $_SESSION['user_id'];

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_admin_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validation
            $validation_errors = [];
            
            if (empty($name)) {
                $validation_errors[] = 'Name is required.';
            }
            
            if (empty($email)) {
                $validation_errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validation_errors[] = 'Please enter a valid email address.';
            }
            
            // Check if email is already taken by another admin
            $email_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? AND role IN ('admin', 'super_admin')");
            $email_check->execute([$email, $admin_id]);
            if ($email_check->fetch()) {
                $validation_errors[] = 'Email address is already in use by another admin.';
            }
            
            // Password validation (if changing password)
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $validation_errors[] = 'Current password is required to change password.';
                } else {
                    // Verify current password
                    $user_check = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
                    $user_check->execute([$admin_id]);
                    $user_data = $user_check->fetch();
                    
                    if (!password_verify($current_password, $user_data['password_hash'])) {
                        $validation_errors[] = 'Current password is incorrect.';
                    }
                }
                
                if (strlen($new_password) < 6) {
                    $validation_errors[] = 'New password must be at least 6 characters long.';
                }
                
                if ($new_password !== $confirm_password) {
                    $validation_errors[] = 'New password and confirmation do not match.';
                }
            }
            
            if (!empty($validation_errors)) {
                $error = implode('<br>', $validation_errors);
            } else {
                // Update profile
                if (!empty($new_password)) {
                    // Update with new password
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password_hash = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$name, $email, $password_hash, $admin_id]);
                } else {
                    // Update without password change
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$name, $email, $admin_id]);
                }
                
                // Update session data
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                
                $message = 'Profile updated successfully!';
                log_admin_activity('Updated Profile');
            }
        } catch (Exception $e) {
            $error = 'An error occurred while updating your profile. Please try again.';
            error_log("Profile update error: " . $e->getMessage());
        }
    }
}

// Get current admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin_data = $stmt->fetch();

log_admin_activity('Accessed Profile Page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .profile-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-success {
            background: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background: #218838;
            border-color: #1e7e34;
        }
        .alert {
            border-radius: 10px;
        }
        .nav-admin {
            background: #28a745;
        }
        .password-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
        .info-card {
            background: #e9f7ef;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-user-shield"></i> Admin Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="posts.php">
                            <i class="fas fa-file-alt"></i> My Posts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add-post.php">
                            <i class="fas fa-plus"></i> Add New Post
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="fas fa-user-edit"></i> Profile
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-edit"></i> My Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt"></i> View Website
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2><i class="fas fa-user-edit"></i> My Profile</h2>
                    <p class="mb-0">Manage your account information and settings</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                
                <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Account Information -->
                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> Account Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Account Type:</strong> <?php echo ucfirst($admin_data['role']); ?></p>
                            <p><strong>Account Status:</strong> 
                                <span class="badge bg-<?php echo $admin_data['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($admin_data['status']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($admin_data['created_at'])); ?></p>
                            <p><strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($admin_data['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-card">
                    <h4 class="mb-4"><i class="fas fa-user-edit"></i> Update Profile</h4>
                    
                    <form method="POST" action="">
                        <?php echo generate_admin_csrf_token(); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($admin_data['name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Section -->
                        <div class="password-section">
                            <h5><i class="fas fa-key"></i> Change Password</h5>
                            <p class="text-muted mb-3">Leave password fields empty if you don't want to change your password.</p>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        <div class="form-text">Minimum 6 characters</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>