<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$message = '';
$error = '';
$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $validation_errors = [];

            if (empty($name)) {
                $validation_errors[] = 'Name is required.';
            }

            if (empty($email)) {
                $validation_errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validation_errors[] = 'Please enter a valid email address.';
            }

            $email_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? AND role IN ('admin', 'super_admin')");
            $email_check->execute([$email, $admin_id]);
            if ($email_check->fetch()) {
                $validation_errors[] = 'Email address is already in use by another admin.';
            }

            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $validation_errors[] = 'Current password is required to change password.';
                } else {
                    $user_check = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                    $user_check->execute([$admin_id]);
                    $user_data = $user_check->fetch();

                    if (!$user_data || !password_verify($current_password, $user_data['password'])) {
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
                if (!empty($new_password)) {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$name, $email, $password_hash, $admin_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$name, $email, $admin_id]);
                }

                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;

                $message = 'Profile updated successfully!';
                log_super_admin_activity('Updated Profile');
            }
        } catch (Exception $e) {
            $error = 'An error occurred while updating your profile. Please try again.';
            error_log('Super admin profile update error: ' . $e->getMessage());
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin_data = $stmt->fetch();

log_super_admin_activity('Accessed Profile Page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 600; }
        .profile-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .alert { border-radius: 10px; }
        .password-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
        .info-card {
            background: #fdf1ef;
            border: 1px solid #f1b0b7;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt"></i> Super Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admins.php"><i class="fas fa-users-cog"></i> Manage Admins</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="add-post.php"><i class="fas fa-plus"></i> Add Post</a></li>
                    <li class="nav-item"><a class="nav-link" href="posts.php"><i class="fas fa-file-alt"></i> All Posts</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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

                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> Account Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Account Type:</strong> <?php echo ucfirst($admin_data['role']); ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin_data['username']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <?php echo ucfirst($admin_data['status']); ?></p>
                            <p><strong>Member Since:</strong> <?php echo format_date($admin_data['created_at']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($admin_data['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="password-section" id="password">
                            <h5><i class="fas fa-lock"></i> Change Password</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password">
                                </div>
                            </div>
                            <small class="text-muted">Leave blank to keep your current password.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
