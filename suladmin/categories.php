<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$action = $_GET['action'] ?? 'list';
$category_id = intval($_GET['id'] ?? 0);
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
                $description = trim($_POST['description'] ?? '');
                $status = $_POST['status'] ?? 'active';
                
                if (empty($name)) {
                    $error = 'Category name is required.';
                } else {
                    // Check if category name already exists
                    $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                    $check_stmt->execute([$name]);
                    
                    if ($check_stmt->fetch()) {
                        $error = 'Category name already exists.';
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO categories (name, description, status, created_at) 
                            VALUES (?, ?, ?, NOW())
                        ");
                        
                        if ($stmt->execute([$name, $description, $status])) {
                            $message = 'Category created successfully.';
                            log_super_admin_activity('Created Category', "Name: $name");
                            $action = 'list';
                        } else {
                            $error = 'Failed to create category.';
                        }
                    }
                }
                break;
                
            case 'edit':
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $status = $_POST['status'] ?? 'active';
                
                if (empty($name)) {
                    $error = 'Category name is required.';
                } else {
                    // Check if category name already exists (excluding current category)
                    $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
                    $check_stmt->execute([$name, $category_id]);
                    
                    if ($check_stmt->fetch()) {
                        $error = 'Category name already exists.';
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE categories 
                            SET name = ?, description = ?, status = ?, updated_at = NOW() 
                            WHERE id = ?
                        ");
                        
                        if ($stmt->execute([$name, $description, $status, $category_id])) {
                            $message = 'Category updated successfully.';
                            log_super_admin_activity('Updated Category', "ID: $category_id, Name: $name");
                            $action = 'list';
                        } else {
                            $error = 'Failed to update category.';
                        }
                    }
                }
                break;
                
            case 'delete':
                if ($category_id > 0) {
                    // Check if category has posts
                    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ?");
                    $check_stmt->execute([$category_id]);
                    $post_count = $check_stmt->fetchColumn();
                    
                    if ($post_count > 0) {
                        $error = 'Cannot delete category with existing posts. Deactivate instead.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        if ($stmt->execute([$category_id])) {
                            $message = 'Category deleted successfully.';
                            log_super_admin_activity('Deleted Category', "ID: $category_id");
                        } else {
                            $error = 'Failed to delete category.';
                        }
                    }
                }
                $action = 'list';
                break;
        }
    }
}

// Get category data for edit
$category_data = null;
if ($action === 'edit' && $category_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category_data = $stmt->fetch();
    
    if (!$category_data) {
        $error = 'Category not found.';
        $action = 'list';
    }
}

// Get all categories for list view
$categories = [];
if ($action === 'list') {
    $stmt = $pdo->query("
        SELECT c.*, 
               (SELECT COUNT(*) FROM posts WHERE category_id = c.id) as post_count
        FROM categories c 
        ORDER BY created_at DESC
    ");
    $categories = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | Super Admin</title>
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
        .category-card {
            transition: transform 0.2s ease;
        }
        .category-card:hover {
            transform: translateY(-2px);
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
                <a class="nav-link" href="admins.php">
                    <i class="fas fa-users-cog"></i> Manage Admins
                </a>
                <a class="nav-link active" href="categories.php">
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
                        <i class="fas fa-tags"></i> 
                        <?php
                        switch($action) {
                            case 'add': echo 'Add New Category'; break;
                            case 'edit': echo 'Edit Category'; break;
                            default: echo 'Manage Categories'; break;
                        }
                        ?>
                    </h2>
                    <?php if ($action === 'list'): ?>
                        <p class="mb-0">Organize posts into categories</p>
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
            <!-- Category List -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>All Categories (<?php echo count($categories); ?>)</h4>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Category
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card category-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo htmlspecialchars($category['name']); ?></h6>
                                <span class="badge bg-<?php echo $category['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($category['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <?php if ($category['description']): ?>
                                    <p class="text-muted mb-3">
                                        <?php echo htmlspecialchars($category['description']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="text-muted mb-2">
                                    <i class="fas fa-file-alt"></i> <?php echo $category['post_count']; ?> posts
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock"></i> 
                                    Created: <?php echo format_date($category['created_at']); ?>
                                </p>
                                <?php if ($category['updated_at']): ?>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-edit"></i> 
                                        Updated: <?php echo format_date($category['updated_at']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="?action=edit&id=<?php echo $category['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($category['post_count'] == 0): ?>
                                    <a href="?action=delete&id=<?php echo $category['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($categories)): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                            <h4>No Categories Found</h4>
                            <p class="text-muted">Create categories to organize your posts.</p>
                            <a href="?action=add" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Category
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
                            <h4><?php echo $action === 'add' ? 'Add New Category' : 'Edit Category'; ?></h4>
                            <a href="categories.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($category_data['name'] ?? ''); ?>" 
                                       placeholder="e.g. Religious Violence" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Brief description of this category..."><?php echo htmlspecialchars($category_data['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($category_data['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>
                                        Active
                                    </option>
                                    <option value="inactive" <?php echo ($category_data['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>
                                        Inactive
                                    </option>
                                </select>
                                <small class="form-text text-muted">
                                    Inactive categories won't be available for new posts.
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="categories.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $action === 'add' ? 'Create Category' : 'Update Category'; ?>
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