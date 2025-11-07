<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

// Get dashboard statistics
$stats = [];

// Total posts
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$stats['total_posts'] = $stmt->fetchColumn();

// Published posts
$stmt = $pdo->query("SELECT COUNT(*) as published FROM posts WHERE status = 'published'");
$stats['published_posts'] = $stmt->fetchColumn();

// Draft posts
$stmt = $pdo->query("SELECT COUNT(*) as draft FROM posts WHERE status = 'draft'");
$stats['draft_posts'] = $stmt->fetchColumn();

// Total admins
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$stats['total_admins'] = $stmt->fetchColumn();

// Active admins (logged in within last 30 days)
$stmt = $pdo->query("SELECT COUNT(*) as active FROM users WHERE role = 'admin' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['active_admins'] = $stmt->fetchColumn();

// Posts by month (last 12 months)
$monthly_posts = [];
$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM posts 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY month 
    ORDER BY month
");
$monthly_posts = $stmt->fetchAll();

// Recent posts
$stmt = $pdo->query("
    SELECT p.id, p.title, p.state, p.status, p.created_at, u.name as admin_name 
    FROM posts p 
    LEFT JOIN users u ON p.admin_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
$recent_posts = $stmt->fetchAll();

// Recent admin activity
$stmt = $pdo->query("
    SELECT u.name, u.last_login, u.status 
    FROM users u 
    WHERE role = 'admin' 
    ORDER BY last_login DESC 
    LIMIT 10
");
$recent_admin_activity = $stmt->fetchAll();

log_super_admin_activity('Accessed Dashboard');
?>
    <title>Super Admin Dashboard | India's Cry for Justice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    </style>
</head>
<body>
    <!-- Navigation -->
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admins.php">
                            <i class="fas fa-users-cog"></i> Manage Admins
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="posts.php">
                            <i class="fas fa-file-alt"></i> All Posts
                        </a>
                    </li>
                    <!-- Settings menu removed per request -->
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-edit"></i> Profile
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
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                    <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! Here's what's happening in your system.</p>
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
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Posts</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['total_posts']); ?></div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published Posts</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['published_posts']); ?></div>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Draft Posts</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['draft_posts']); ?></div>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Admins</div>
                                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['active_admins']); ?></div>
                                <small class="text-muted">of <?php echo $stats['total_admins']; ?> total</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users stat-icon text-info"></i>
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
                    <h5><i class="fas fa-chart-line"></i> Posts by Month</h5>
                    <canvas id="monthlyPostsChart" height="100"></canvas>
                </div>

                <!-- Posts by State Chart removed -->
            </div>

            <!-- Sidebar Column -->
            <div class="col-xl-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body quick-actions">
                        <a href="admins.php?action=add" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-user-plus"></i> Add New Admin
                        </a>
                        <a href="categories.php?action=add" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-tag"></i> Add Category
                        </a>
                        <a href="posts.php" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-eye"></i> Review Posts
                        </a>
                        <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt"></i> View Website
                        </a>
                    </div>
                </div>

                <!-- Recent Admin Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Recent Admin Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="recent-activity">
                            <?php foreach ($recent_admin_activity as $activity): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($activity['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php if ($activity['last_login']): ?>
                                                    Last login: <?php echo format_date($activity['last_login']); ?>
                                                <?php else: ?>
                                                    Never logged in
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?php echo $activity['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($activity['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($recent_admin_activity)): ?>
                                <div class="activity-item text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No admin activity found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock"></i> Recent Posts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>State</th>
                                        <th>Status</th>
                                        <th>Admin</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars(truncate_text($post['title'], 50)); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($post['state'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($post['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($post['admin_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo format_date($post['created_at']); ?></td>
                                            <td>
                                                <a href="posts.php?action=edit&id=<?php echo $post['id']; ?>" 
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
                                    
                                    <?php if (empty($recent_posts)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No posts found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Monthly Posts Chart
        (function() {
            const monthlyEl = document.getElementById('monthlyPostsChart');
            try {
                if (monthlyEl && monthlyEl.getContext) {
                    const monthlyCtx = monthlyEl.getContext('2d');
                    new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . date('M Y', strtotime($item['month'] . '-01')) . '"'; }, $monthly_posts)); ?>],
                datasets: [{
                    label: 'Posts Created',
                    data: [<?php echo implode(',', array_column($monthly_posts, 'count')); ?>],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
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
                } else {
                    console.warn('monthlyPostsChart canvas not found or unsupported');
                }
            } catch (e) {
                console.error('Error initializing monthlyPostsChart', e);
            }
        })();

        // Posts by State Chart (controlled sizing + truncated labels)
        (function() {
            const stateEl = document.getElementById('statePostsChart');
            try {
                if (stateEl && stateEl.getContext) {
                    const stateCtx = stateEl.getContext('2d');
                    const stateLabels = [<?php echo implode(',', array_map(function($item) { return '"' . $item['state'] . '"'; }, $posts_by_state)); ?>];
                    const stateData = [<?php echo implode(',', array_column($posts_by_state, 'count')); ?>];

                    new Chart(stateCtx, {
                        type: 'bar',
                        data: {
                            labels: stateLabels,
                            datasets: [{
                                label: 'Number of Posts',
                                data: stateData,
                                backgroundColor: '#764ba2',
                                borderColor: '#667eea',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            // keep aspect ratio to avoid uncontrolled vertical stretching
                            maintainAspectRatio: true,
                            aspectRatio: 2,
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxRotation: 45,
                                        minRotation: 0,
                                        callback: function(value, index, ticks) {
                                            const label = this.getLabelForValue ? this.getLabelForValue(value) : (stateLabels[index] || '');
                                            if (label && label.length > 15) return label.substr(0, 15) + '...';
                                            return label;
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true
                                }
                            },
                            elements: {
                                bar: {
                                    // limit bar thickness so chart remains compact
                                    maxBarThickness: 40,
                                    barPercentage: 0.8,
                                    categoryPercentage: 0.7
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: function(context) {
                                            // show full label in tooltip
                                            return stateLabels[context[0].dataIndex];
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.warn('statePostsChart canvas not found or unsupported');
                }
            } catch (e) {
                console.error('Error initializing statePostsChart', e);
            }
        })();
    </script>
</body>
</html>