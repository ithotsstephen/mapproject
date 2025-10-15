<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$action = $_GET['action'] ?? 'list';
$admin_id = intval($_GET['id'] ?? 0);
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
                    // Don't allow deletion if admin has posts
                    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE admin_id = ?");
                    $check_stmt->execute([$admin_id]);
                    $post_count = $check_stmt->fetchColumn();
                    
                    if ($post_count > 0) {
                        $error = 'Cannot delete admin with existing posts. Deactivate instead.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
                        if ($stmt->execute([$admin_id])) {
                            $message = 'Admin deleted successfully.';
                            log_super_admin_activity('Deleted Admin', "ID: $admin_id");
                        } else {
                            $error = 'Failed to delete admin.';
                        }
                    }
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins | Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .admin-card {
            transition: transform 0.2s ease;
        }
        .admin-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8em;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt"></i> Super Admin
            </a>
            
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link active" href="admins.php">
                    <i class="fas fa-users-cog"></i> Manage Admins
                </a>
                <a class="nav-link" href="categories.php">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a class="nav-link" href="posts.php">
                    <i class="fas fa-file-alt"></i> All Posts
                </a>
            </div>
            
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2>
                        <i class="fas fa-users-cog"></i> 
                        <?php
                        switch($action) {
                            case 'add': echo 'Add New Admin'; break;
                            case 'edit': echo 'Edit Admin'; break;
                            default: echo 'Manage Admins'; break;
                        }
                        ?>
                    </h2>
                    <?php if ($action === 'list'): ?>
                        <p class="mb-0">Manage admin accounts and permissions</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Admin List -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>All Admins (<?php echo count($admins); ?>)</h4>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Admin
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach ($admins as $admin): ?>
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card admin-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo htmlspecialchars($admin['name']); ?></h6>
                                <span class="badge status-badge bg-<?php echo $admin['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($admin['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($admin['username']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-file-alt"></i> <?php echo $admin['post_count']; ?> posts
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock"></i> 
                                    Joined: <?php echo format_date($admin['created_at']); ?>
                                </p>
                                <?php if ($admin['last_login']): ?>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-sign-in-alt"></i> 
                                        Last login: <?php echo format_date($admin['last_login']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="?action=edit&id=<?php echo $admin['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($admin['post_count'] == 0): ?>
                                    <a href="?action=delete&id=<?php echo $admin['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this admin?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
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
                                <?php endif; ?>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?php echo ($admin_data['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>
                                            Active
                                        </option>
                                        <option value="inactive" <?php echo ($admin_data['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Password <?php echo $action === 'add' ? '*' : '(leave empty to keep current)'; ?>
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
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="admins.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $action === 'add' ? 'Create Admin' : 'Update Admin'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation for password confirmation
        <?php if ($action === 'add'): ?>
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>