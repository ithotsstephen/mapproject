<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$action = $_GET['action'] ?? 'list';
$post_id = intval($_GET['id'] ?? 0);
$page = intval($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        switch ($_POST['action']) {
            case 'bulk_delete':
                $selected_posts = $_POST['selected_posts'] ?? [];
                if (!empty($selected_posts)) {
                    $placeholders = str_repeat('?,', count($selected_posts) - 1) . '?';
                    $stmt = $pdo->prepare("DELETE FROM posts WHERE id IN ($placeholders)");
                    $deleted_count = $stmt->execute($selected_posts) ? $stmt->rowCount() : 0;
                    
                    if ($deleted_count > 0) {
                        $message = "Deleted $deleted_count posts successfully.";
                        log_super_admin_activity('Bulk Deleted Posts', "Count: $deleted_count");
                    } else {
                        $error = 'Failed to delete posts.';
                    }
                }
                break;
                
            case 'bulk_status':
                $selected_posts = $_POST['selected_posts'] ?? [];
                $new_status = $_POST['bulk_status'] ?? '';
                
                if (!empty($selected_posts) && in_array($new_status, ['published', 'draft'])) {
                    $placeholders = str_repeat('?,', count($selected_posts) - 1) . '?';
                    $params = array_merge([$new_status], $selected_posts);
                    $stmt = $pdo->prepare("UPDATE posts SET status = ? WHERE id IN ($placeholders)");
                    $updated_count = $stmt->execute($params) ? $stmt->rowCount() : 0;
                    
                    if ($updated_count > 0) {
                        $message = "Updated $updated_count posts to $new_status status.";
                        log_super_admin_activity('Bulk Updated Post Status', "Status: $new_status, Count: $updated_count");
                    } else {
                        $error = 'Failed to update posts.';
                    }
                }
                break;
        }
    }
}

// Handle individual actions
if ($action === 'delete' && $post_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    if ($stmt->execute([$post_id])) {
        $message = 'Post deleted successfully.';
        log_super_admin_activity('Deleted Post', "ID: $post_id");
    } else {
        $error = 'Failed to delete post.';
    }
    $action = 'list';
}

// Get filters
$filter_status = $_GET['status'] ?? '';
$filter_state = $_GET['state'] ?? '';
$filter_category = $_GET['category'] ?? '';
$search_term = $_GET['search'] ?? '';

// Build query conditions
$conditions = [];
$params = [];

if ($filter_status) {
    $conditions[] = "p.status = ?";
    $params[] = $filter_status;
}

if ($filter_state) {
    $conditions[] = "p.state = ?";
    $params[] = $filter_state;
}

if ($filter_category) {
    $conditions[] = "p.category_id = ?";
    $params[] = $filter_category;
}

if ($search_term) {
    $conditions[] = "(p.title LIKE ? OR p.short_message LIKE ? OR p.detailed_message LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Get total count for pagination
$count_sql = "
    SELECT COUNT(*) 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN users u ON p.admin_id = u.id 
    $where_clause
";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Get posts
$posts_sql = "
    SELECT p.*, c.name as category_name, u.name as admin_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN users u ON p.admin_id = u.id 
    $where_clause
    ORDER BY p.created_at DESC 
    LIMIT $per_page OFFSET $offset
";
$posts_stmt = $pdo->prepare($posts_sql);
$posts_stmt->execute($params);
$posts = $posts_stmt->fetchAll();

// Get filter options
$states = $pdo->query("SELECT DISTINCT state FROM posts WHERE state IS NOT NULL ORDER BY state")->fetchAll(PDO::FETCH_COLUMN);
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts | Super Admin</title>
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
        .filters-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .post-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .bulk-actions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table-actions .btn {
            margin-right: 5px;
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
                <a class="nav-link" href="categories.php">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a class="nav-link active" href="posts.php">
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
                    <h2><i class="fas fa-file-alt"></i> All Posts</h2>
                    <p class="mb-0">Manage all posts across the system</p>
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

        <!-- Filters -->
        <div class="filters-card">
            <div class="card-header">
                <h5><i class="fas fa-filter"></i> Filters & Search</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" <?php echo $filter_status === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $filter_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select">
                            <option value="">All States</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?php echo htmlspecialchars($state); ?>" 
                                        <?php echo $filter_state === $state ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $filter_category == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search posts..." 
                                   value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="posts.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                        <span class="text-muted ms-3">
                            Showing <?php echo count($posts); ?> of <?php echo $total_posts; ?> posts
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Form -->
        <form method="POST" id="bulkForm">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <!-- Bulk Actions Bar -->
            <div class="bulk-actions" style="display: none;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span id="selectedCount">0</span> posts selected
                    </div>
                    <div class="col-md-6 text-end">
                        <select name="bulk_status" class="form-select d-inline-block w-auto me-2">
                            <option value="">Change Status...</option>
                            <option value="published">Publish</option>
                            <option value="draft">Set as Draft</option>
                        </select>
                        <button type="submit" name="action" value="bulk_status" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Update Status
                        </button>
                        <button type="submit" name="action" value="bulk_delete" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete the selected posts?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Posts Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Post</th>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Admin</th>
                                <th>Created</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_posts[]" value="<?php echo $post['id']; ?>" 
                                               class="form-check-input post-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($post['featured_image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($post['featured_image_path']); ?>" 
                                                     class="post-thumbnail me-3" alt="Thumbnail">
                                            <?php else: ?>
                                                <div class="post-thumbnail me-3 bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars(truncate_text($post['title'], 50)); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(truncate_text($post['short_message'], 80)); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($post['state'] ?? 'N/A'); ?>
                                        <?php if ($post['district']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($post['district']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['category_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['admin_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo format_date($post['created_at']); ?>
                                    </td>
                                    <td class="table-actions">
                                        <a href="../details.php?id=<?php echo $post['id']; ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo $post['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this post?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h5>No Posts Found</h5>
                                        <p class="text-muted">No posts match your current filters.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="card-footer">
                        <nav aria-label="Posts pagination">
                            <ul class="pagination justify-content-center mb-0">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($_GET); ?>">
                                            Previous
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($_GET); ?>">
                                            Next
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bulk selection functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const postCheckboxes = document.querySelectorAll('.post-checkbox');
        const bulkActions = document.querySelector('.bulk-actions');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
            const count = checkedBoxes.length;
            
            selectedCount.textContent = count;
            bulkActions.style.display = count > 0 ? 'block' : 'none';
            
            // Update select all checkbox state
            if (count === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (count === postCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        postCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        // Initialize
        updateBulkActions();
    </script>
</body>
</html>