<?php
session_start();
require_once 'db.php';

// Get state name from URL
$state = $_GET['state'] ?? '';

if (empty($state)) {
    header('Location: index.php');
    exit();
}

// Get filters from URL
$category_filter = $_GET['category'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$posts_per_page = 10;

// Build query
$where_conditions = ["state = ? AND status = 'published'"];
$params = [$state];

if (!empty($category_filter)) {
    $where_conditions[] = "category_id = ?";
    $params[] = $category_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "incident_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "incident_date <= ?";
    $params[] = $date_to;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM posts p WHERE $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for current page
$offset = ($page - 1) * $posts_per_page;
$sql = "
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE $where_clause 
    ORDER BY p.incident_date DESC, p.created_at DESC 
    LIMIT $posts_per_page OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Get categories for filter dropdown
$categories = get_categories($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($state); ?> - Persecution Reports | Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .post-card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .post-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .post-meta {
            font-size: 0.9em;
            color: #666;
        }
        .pagination-wrapper {
            margin-top: 40px;
        }
        .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <strong>Persecution Tracker</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 mb-3">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($state); ?>
                    </h1>
                    <p class="lead">Persecution incidents reported in <?php echo htmlspecialchars($state); ?></p>
                    <p class="mb-0">
                        <i class="fas fa-file-alt"></i> <?php echo $total_posts; ?> total reports found
                        <?php if (!empty($category_filter) || !empty($date_from) || !empty($date_to)): ?>
                            with current filters
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-4">
        <!-- Back Button -->
        <div class="back-button">
            <a href="index.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Map
            </a>
        </div>

        <!-- Filter Section -->
        <section class="filter-section rounded">
            <div class="container">
                <h4 class="mb-4"><i class="fas fa-filter"></i> Filter Reports</h4>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="state" value="<?php echo htmlspecialchars($state); ?>">
                    
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" 
                               value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" 
                               value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="state.php?state=<?php echo urlencode($state); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Posts List -->
        <div class="row">
            <?php if (empty($posts)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <h4><i class="fas fa-info-circle"></i> No Reports Found</h4>
                        <p>No persecution reports found for <?php echo htmlspecialchars($state); ?> with the current filters.</p>
                        <a href="state.php?state=<?php echo urlencode($state); ?>" class="btn btn-primary">View All Reports</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-6 col-md-12">
                        <div class="card post-card h-100">
                            <?php if ($post['featured_image_path']): ?>
                                <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" 
                                     class="post-image" alt="Report Image">
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text"><?php echo truncate_text(htmlspecialchars($post['short_message']), 150); ?></p>
                                
                                <div class="post-meta mb-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo $post['incident_date'] ? format_date($post['incident_date']) : 'Date not specified'; ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?php echo htmlspecialchars($post['district'] ?: 'District not specified'); ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($post['category_name']): ?>
                                        <div class="mt-2">
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['category_name']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($post['tags']): ?>
                                        <div class="mt-2">
                                            <?php 
                                            $tags = explode(',', $post['tags']);
                                            foreach (array_slice($tags, 0, 3) as $tag): 
                                            ?>
                                                <span class="badge bg-light text-dark me-1">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <a href="details.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <small class="text-muted float-end">
                                    Reported: <?php echo format_date($post['created_at']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper d-flex justify-content-center">
                <nav aria-label="Reports pagination">
                    <ul class="pagination">
                        <!-- Previous Page -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?state=<?php echo urlencode($state); ?>&category=<?php echo urlencode($category_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&page=<?php echo ($page - 1); ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?state=<?php echo urlencode($state); ?>&category=<?php echo urlencode($category_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Page -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?state=<?php echo urlencode($state); ?>&category=<?php echo urlencode($category_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&page=<?php echo ($page + 1); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Persecution Tracker</h5>
                    <p>Monitoring and documenting persecution incidents across India</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Persecution Tracker. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>