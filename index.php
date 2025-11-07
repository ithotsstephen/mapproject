<?php
session_start();
require_once 'db.php';

// Get post counts for map display
$post_counts = get_post_counts_by_state($pdo);
$counts_by_state = [];
foreach ($post_counts as $count) {
    $counts_by_state[$count['state']] = $count['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persecution Report Tracker - India</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/map-style.css" rel="stylesheet" type="text/css"/>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .map-container {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .state-count {
            position: absolute;
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            pointer-events: none;
            z-index: 10;
        }
        .map-svg {
            width: 100%;
            max-width: 900px;
            height: auto;
            display: block; /* avoid inline-gap and better scaling */
            min-height: 520px; /* ensure visible area for the map */
        }
        .state-path {
            cursor: pointer;
            transition: all 0.3s ease;
            stroke-width: 1;
            pointer-events: auto; /* ensure paths receive pointer events */
        }
        .state-path:hover {
            opacity: 0.8;
            stroke-width: 2;
            filter: brightness(1.1);
        }
        .stats-section {
            padding: 60px 0;
        }
        .stat-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        #state-counts {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        /* allow the svg to overflow its container if paths extend beyond */
        #india-map {
            overflow: visible;
            z-index: 1;
            position: relative;
        }

        /* focus styles for accessibility when paths are keyboard-focusable */
        .state-path:focus {
            outline: 2px dashed rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <strong>India's Cry for Justice</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#map">Interactive Map</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stats">Statistics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="incidents.php">Incidents</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin/">Admin Login</a></li>
                            <li><a class="dropdown-item" href="suadmin/">Super Admin</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">India's Cry for Justice </h1>
            <p class="lead mb-4">Track and monitor persecution incidents across Indian states</p>
            <a href="#map" class="btn btn-light btn-lg">Explore Interactive Map</a>
        </div>
    </section>

    <!-- Map removed per user request -->
    <section id="map" class="map-container">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Explore Reports</h2>
                    <p class="lead">Browse reported incidents by using the <a href="incidents.php">Incidents</a> page or use the filters to find reports for a specific state.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="stats-section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>National Statistics</h2>
                    <p class="lead">Overview of reported incidents across India</p>
                </div>
            </div>
            <div class="row">
                <?php
                // Get total statistics
                $total_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
                $total_states = $pdo->query("SELECT COUNT(DISTINCT state) FROM posts WHERE status = 'published' AND state IS NOT NULL AND state != ''")->fetchColumn();
                $total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
                $recent_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published' AND DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
                ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_posts; ?></div>
                        <div>Total Incidents</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_states; ?></div>
                        <div>States Covered</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_categories; ?></div>
                        <div>Active Categories</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $recent_posts; ?></div>
                        <div>Incidents this Month</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Reports Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Recent Reports</h2>
                    <p class="lead">Latest persecution incidents reported</p>
                </div>
            </div>
            <div class="row">
                <?php
                // Get recent published posts
                $stmt = $pdo->query("
                    SELECT p.*, c.name as category_name 
                    FROM posts p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'published' 
                    ORDER BY p.created_at DESC 
                    LIMIT 6
                ");
                $recent_posts_data = $stmt->fetchAll();
                
                foreach ($recent_posts_data as $post):
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if ($post['featured_image_path']): ?>
                            <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="card-img-top" alt="Report Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo truncate_text(htmlspecialchars($post['short_message']), 100); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($post['state']); ?>
                                <?php if ($post['category_name']): ?>
                                    | <i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['category_name']); ?>
                                <?php endif; ?>
                                <br>
                                <i class="fas fa-calendar"></i> <?php echo format_date($post['created_at']); ?>
                            </small>
                        </div>
                        <div class="card-footer">
                            <a href="details.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($recent_posts_data)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No reports available at the moment.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>India's Cry for Justice</h5>
                    <p>Monitoring and documenting persecution incidents across India</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 India's Cry for Justice. All rights reserved.</p>
                    <p>
                        <a href="#" class="text-light me-3">Privacy Policy</a>
                        <a href="#" class="text-light">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
                // Map functionality removed — no SVG handlers to bind.
            
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 70
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>