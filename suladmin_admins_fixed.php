<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$admin_id = intval($_GET['id'] ?? $_POST['admin_id'] ?? 0);
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        switch ($action) {
            case 'add':
                $name = trim($_POST['name'] ?? '');
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                $status = $_POST['status'] ?? 'active';
                
                // Validation
                if (empty($name) || empty($username) || empty($email) || empty($password)) {
                    $error = 'All fields are required.';
                } elseif ($password !== $confirm_password) {
                    $error = 'Passwords do not match.';
                } elseif (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters long.';
                } else {
                    // Check if username/email already exists
                    $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $check_stmt->execute([$username, $email]);
                    
                    if ($check_stmt->fetch()) {
                        $error = 'Username or email already exists.';
                    } else {
                        // Create admin
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            INSERT INTO users (name, username, email, password, role, status, created_at) 
                            VALUES (?, ?, ?, ?, 'admin', ?, NOW())
                        ");
                        
                        if ($stmt->execute([$name, $username, $email, $hashed_password, $status])) {
                            $message = 'Admin created successfully.';
                            log_super_admin_activity('Created Admin', "Username: $username");
                            $action = 'list'; // Redirect to list view
                        } else {
                            $error = 'Failed to create admin.';
                        }
                    }
                }
                break;
                
            case 'edit':
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $status = $_POST['status'] ?? 'active';
                $password = $_POST['password'] ?? '';
                
                if (empty($name) || empty($email)) {
                    $error = 'Name and email are required.';
                } else {
                    $update_fields = "name = ?, email = ?, status = ?";
                    $params = [$name, $email, $status];
                    
                    // Update password if provided
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $error = 'Password must be at least 6 characters long.';
                        } else {
                            $update_fields .= ", password = ?";
                            $params[] = password_hash($password, PASSWORD_DEFAULT);
                        }
                    }
                    
                    if (empty($error)) {
                        $params[] = $admin_id;
                        $stmt = $pdo->prepare("UPDATE users SET $update_fields WHERE id = ? AND role = 'admin'");
                        
                        if ($stmt->execute($params)) {
                            $message = 'Admin updated successfully.';
                            log_super_admin_activity('Updated Admin', "ID: $admin_id");
                            $action = 'list';
                        } else {
                            $error = 'Failed to update admin.';
                        }
                    }
                }
                break;
                
            case 'delete':
                if ($admin_id > 0) {
                    // Get admin details first
                    $admin_stmt = $pdo->prepare("SELECT id, name, username, role FROM users WHERE id = ?");
                    $admin_stmt->execute([$admin_id]);
                    $admin_info = $admin_stmt->fetch();
                    
                    if (!$admin_info) {
                        $error = 'Admin not found.';
                    } elseif ($admin_info['role'] !== 'admin') {
                        $error = 'Cannot delete non-admin users or super admins.';
                    } else {
                        // Check if admin has posts
                        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE admin_id = ?");
                        $check_stmt->execute([$admin_id]);
                        $post_count = $check_stmt->fetchColumn();
                        
                        if ($post_count > 0) {
                            $error = "Cannot delete admin '{$admin_info['username']}' - they have {$post_count} existing posts. You can deactivate them instead.";
                        } else {
                            // Proceed with deletion
                            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
                            if ($stmt->execute([$admin_id])) {
                                $message = "Admin '{$admin_info['username']}' deleted successfully.";
                                log_super_admin_activity('Deleted Admin', "Username: {$admin_info['username']}, ID: $admin_id");
                            } else {
                                $error = 'Failed to delete admin. Please try again.';
                            }
                        }
                    }
                } else {
                    $error = 'Invalid admin ID.';
                }
                $action = 'list';
                break;
                
            case 'deactivate':
                if ($admin_id > 0) {
                    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ? AND role = 'admin'");
                    if ($stmt->execute([$admin_id])) {
                        $message = 'Admin deactivated successfully.';
                        log_super_admin_activity('Deactivated Admin', "ID: $admin_id");
                    } else {
                        $error = 'Failed to deactivate admin.';
                    }
                } else {
                    $error = 'Invalid admin ID.';
                }
                $action = 'list';
                break;
                
            case 'activate':
                if ($admin_id > 0) {
                    $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'admin'");
                    if ($stmt->execute([$admin_id])) {
                        $message = 'Admin activated successfully.';
                        log_super_admin_activity('Activated Admin', "ID: $admin_id");
                    } else {
                        $error = 'Failed to activate admin.';
                    }
                } else {
                    $error = 'Invalid admin ID.';
                }
                $action = 'list';
                break;
        }
    }
}

// Get admin data for edit
$admin_data = null;
if ($action === 'edit' && $admin_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$admin_id]);
    $admin_data = $stmt->fetch();
    
    if (!$admin_data) {
        $error = 'Admin not found.';
        $action = 'list';
    }
}

// Get all admins for list view
$admins = [];
if ($action === 'list') {
    $stmt = $pdo->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM posts WHERE admin_id = u.id) as post_count
        FROM users u 
        WHERE role = 'admin' 
        ORDER BY created_at DESC
    ");
    $admins = $stmt->fetchAll();
}

// Helper function to format dates
function format_date($date) {
    if (!$date) return 'Never';
    return date('M j, Y g:i A', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .admin-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .admin-card:hover {
            transform: translateY(-2px);
        }
        .admin-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .action-form {
            display: inline-block;
        }
        .btn-group-sm .btn {
            margin: 2px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt"></i> Super Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link active" href="admins.php">Admins</a>
                <a class="nav-link" href="posts.php">Posts</a>
                <a class="nav-link" href="categories.php">Categories</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Admin List -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Admin Management</h2>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Admin
                </a>
            </div>

            <div class="row">
                <?php foreach ($admins as $admin): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="admin-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="admin-avatar me-3">
                                        <?php echo strtoupper(substr($admin['name'], 0, 2)); ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($admin['name']); ?></h5>
                                        <p class="card-text text-muted mb-2">
                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($admin['username']); ?>
                                        </p>
                                        <p class="card-text text-muted mb-2">
                                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?>
                                        </p>
                                        <span class="badge status-badge <?php echo $admin['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($admin['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col">
                                        <small class="text-muted d-block">Posts Created</small>
                                        <strong class="text-primary"><?php echo $admin['post_count']; ?></strong>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">Joined</small>
                                        <strong><?php echo date('M Y', strtotime($admin['created_at'])); ?></strong>
                                    </div>
                                </div>
                                
                                <?php if ($admin['last_login']): ?>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-sign-in-alt"></i> 
                                            Last login: <?php echo format_date($admin['last_login']); ?>
                                        </small>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group-sm d-flex flex-wrap">
                                    <!-- Edit Button -->
                                    <a href="?action=edit&id=<?php echo $admin['id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <?php if ($admin['status'] === 'active'): ?>
                                        <!-- Deactivate Button -->
                                        <form method="POST" class="action-form">
                                            <input type="hidden" name="action" value="deactivate">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                                    onclick="return confirm('Deactivate this admin?')">
                                                <i class="fas fa-pause"></i> Deactivate
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Activate Button -->
                                        <form method="POST" class="action-form">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <button type="submit" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-play"></i> Activate
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($admin['post_count'] == 0): ?>
                                        <!-- Delete Button (only if no posts) -->
                                        <form method="POST" class="action-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('⚠️ PERMANENTLY DELETE this admin?\\n\\nAdmin: <?php echo addslashes($admin['name']); ?>\\nUsername: <?php echo addslashes($admin['username']); ?>\\n\\nThis action cannot be undone!')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Cannot Delete Message -->
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                                                title="Cannot delete admin with <?php echo $admin['post_count']; ?> posts. Deactivate instead.">
                                            <i class="fas fa-lock"></i> Protected
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($admin['post_count'] > 0): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i> 
                                        Cannot delete: Admin has <?php echo $admin['post_count']; ?> posts
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($admins)): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h4>No Admins Found</h4>
                            <p class="text-muted">Get started by adding your first admin.</p>
                            <a href="?action=add" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Admin
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4><?php echo $action === 'add' ? 'Add New Admin' : 'Edit Admin'; ?></h4>
                            <a href="admins.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($admin_data['name'] ?? ''); ?>" required>
                                </div>
                                
                                <?php if ($action === 'add'): ?>
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username *</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin_data['username']); ?>" readonly>
                                        <small class="text-muted">Username cannot be changed</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?php echo ($admin_data['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($admin_data['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Password <?php echo $action === 'add' ? '*' : '(leave blank to keep current)'; ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?php echo $action === 'add' ? 'required' : ''; ?>>
                                </div>
                                
                                <?php if ($action === 'add'): ?>
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="admins.php" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 
                                    <?php echo $action === 'add' ? 'Create Admin' : 'Update Admin'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>