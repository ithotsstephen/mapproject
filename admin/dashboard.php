<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

// Get dashboard statistics for this admin
$admin_id = $_SESSION['user_id'];

// Admin's posts statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$my_total_posts = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as published FROM posts WHERE admin_id = ? AND status = 'published'");
$stmt->execute([$admin_id]);
$my_published_posts = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as draft FROM posts WHERE admin_id = ? AND status = 'draft'");
$stmt->execute([$admin_id]);
$my_draft_posts = $stmt->fetchColumn();

// Recent posts by this admin
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.admin_id = ? 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
$stmt->execute([$admin_id]);
$my_recent_posts = $stmt->fetchAll();

// Posts by month for this admin (last 6 months)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM posts 
    WHERE admin_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month 
    ORDER BY month
");
$stmt->execute([$admin_id]);
$monthly_posts = $stmt->fetchAll();

// Posts by status for this admin
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count 
    FROM posts 
    WHERE admin_id = ? 
    GROUP BY status
");
$stmt->execute([$admin_id]);
$posts_by_status = $stmt->fetchAll();

// Get available categories
$categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();

log_admin_activity('Accessed Dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
        .activity-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .quick-actions .btn {
            margin-bottom: 10px;
        }
        .nav-admin {
            background: #28a745;
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
                        <a class="nav-link active" href="dashboard.php">
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
                        <a class="nav-link" href="profile.php">
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

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                    <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! Manage your persecution reports and content.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">My Total Posts</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($my_total_posts); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt stat-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-success border-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($my_published_posts); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle stat-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Drafts</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($my_draft_posts); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-edit stat-icon text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-info border-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo count($categories); ?></div>
                                <small class="text-muted">Available</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tags stat-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Charts Column -->
            <div class="col-xl-8 mb-4">
                <!-- Posts by Month Chart -->
                <div class="chart-container mb-4">
                    <h5><i class="fas fa-chart-line"></i> My Posts by Month</h5>
                    <canvas id="monthlyPostsChart" height="100"></canvas>
                </div>

                <!-- Recent Posts -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock"></i> My Recent Posts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($my_recent_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars(truncate_text($post['title'], 40)); ?></strong>
                                                <?php if ($post['state']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($post['state']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($post['category_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($post['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo format_date($post['created_at']); ?></td>
                                            <td>
                                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="../details.php?id=<?php echo $post['id']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($my_recent_posts)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                No posts yet. <a href="add-post.php">Create your first post</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($my_recent_posts)): ?>
                            <div class="text-center mt-3">
                                <a href="posts.php" class="btn btn-primary">
                                    <i class="fas fa-list"></i> View All My Posts
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-xl-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body quick-actions">
                        <a href="add-post.php" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-plus"></i> Create New Post
                        </a>
                        <a href="posts.php?status=draft" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-edit"></i> Edit Draft Posts
                        </a>
                        <a href="posts.php" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-list"></i> View All My Posts
                        </a>
                        <a href="profile.php" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                        <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt"></i> View Website
                        </a>
                    </div>
                </div>

                <!-- Post Status Chart -->
                <div class="chart-container">
                    <h5><i class="fas fa-chart-pie"></i> My Posts by Status</h5>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Posts Chart
        const monthlyCtx = document.getElementById('monthlyPostsChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . date('M Y', strtotime($item['month'] . '-01')) . '"'; }, $monthly_posts)); ?>],
                datasets: [{
                    label: 'Posts Created',
                    data: [<?php echo implode(',', array_column($monthly_posts, 'count')); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . ucfirst($item['status']) . '"'; }, $posts_by_status)); ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_column($posts_by_status, 'count')); ?>],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>