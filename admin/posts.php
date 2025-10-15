<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

$page = intval($_GET['page'] ?? 1);
$per_page = 15;
$offset = ($page - 1) * $per_page;
$filter_status = $_GET['status'] ?? '';
$search_term = $_GET['search'] ?? '';

// Build query conditions
$conditions = ["admin_id = ?"];
$params = [$_SESSION['user_id']];

if ($filter_status) {
    $conditions[] = "status = ?";
    $params[] = $filter_status;
}

if ($search_term) {
    $conditions[] = "(title LIKE ? OR short_message LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = 'WHERE ' . implode(' AND ', $conditions);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM posts $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Get posts
$posts_sql = "
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $where_clause
    ORDER BY p.updated_at DESC 
    LIMIT $per_page OFFSET $offset
";
$posts_stmt = $pdo->prepare($posts_sql);
$posts_stmt->execute($params);
$posts = $posts_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white; padding: 2rem 0; margin-bottom: 2rem;
        }
        .post-thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-user-shield"></i> Admin Portal</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link active" href="posts.php"><i class="fas fa-file-alt"></i> My Posts</a>
                <a class="nav-link" href="add-post.php"><i class="fas fa-plus"></i> Add New Post</a>
            </div>
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container-fluid">
            <h2><i class="fas fa-file-alt"></i> My Posts</h2>
            <p class="mb-0">Manage your persecution incident reports</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" <?php echo $filter_status === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $filter_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Showing <?php echo count($posts); ?> of <?php echo $total_posts; ?> posts</span>
            <a href="add-post.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Post</a>
        </div>

        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if ($post['featured_image_path']): ?>
                            <img src="../<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars(truncate_text($post['title'], 50)); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(truncate_text($post['short_message'], 100)); ?></p>
                            <div class="mb-2">
                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                                <?php if ($post['category_name']): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($post['state'] ?? 'No location'); ?> • 
                                <?php echo format_date($post['created_at']); ?>
                            </small>
                        </div>
                        <div class="card-footer">
                            <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                            <a href="../details.php?id=<?php echo $post['id']; ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h4>No Posts Found</h4>
                <p class="text-muted">Start by creating your first post.</p>
                <a href="add-post.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create First Post</a>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav><ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul></nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>