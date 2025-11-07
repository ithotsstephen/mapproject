<?php
session_start();
require_once 'db.php';

// Get post ID from URL
$post_id = intval($_GET['id'] ?? 0);

if ($post_id <= 0) {
    header('Location: index.php');
    exit();
}

// Get post details
$sql = "
    SELECT p.*, c.name as category_name, u.name as admin_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN users u ON p.admin_id = u.id 
    WHERE p.id = ? AND p.status = 'published'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit();
}

// Parse external links
$external_links = $post['external_links'] ? explode(',', $post['external_links']) : [];

// Parse tags
$tags = $post['tags'] ? explode(',', $post['tags']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> | Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="description" content="<?php echo htmlspecialchars(truncate_text($post['short_message'], 160)); ?>">
    
    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(truncate_text($post['short_message'], 160)); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <?php if ($post['featured_image_path']): ?>
        <meta property="og:image" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $post['featured_image_path']; ?>">
    <?php endif; ?>
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        .content-section {
            padding: 40px 0;
        }
        .post-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .post-video {
            width: 100%;
            max-height: 400px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .post-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .post-meta .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .post-meta .meta-item:last-child {
            margin-bottom: 0;
        }
        .post-meta .meta-icon {
            width: 20px;
            margin-right: 10px;
            color: #667eea;
        }
        .post-content {
            font-size: 1.1em;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        .map-container {
            height: 300px;
            border-radius: 8px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .external-links a {
            display: block;
            padding: 8px 0;
            color: #667eea;
            text-decoration: none;
        }
        .external-links a:hover {
            text-decoration: underline;
        }
        .tags .badge {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .back-button {
            margin-bottom: 20px;
        }
        .share-buttons {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .share-buttons .btn {
            margin-right: 10px;
            margin-bottom: 10px;
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
                <?php if ($post['state']): ?>
                    <a class="nav-link" href="state.php?state=<?php echo urlencode($post['state']); ?>">
                        <?php echo htmlspecialchars($post['state']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p class="lead"><?php echo htmlspecialchars($post['short_message']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <!-- Back Button -->
            <div class="back-button">
                <?php if ($post['state']): ?>
                    <a href="state.php?state=<?php echo urlencode($post['state']); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to <?php echo htmlspecialchars($post['state']); ?> Reports
                    </a>
                <?php else: ?>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                <?php endif; ?>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Featured Image -->
                    <?php if ($post['featured_image_path']): ?>
                        <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" 
                             alt="Report Image" class="post-image">
                    <?php endif; ?>

                    <!-- Video -->
                    <?php if ($post['video_path']): ?>
                        <video controls class="post-video">
                            <source src="<?php echo htmlspecialchars($post['video_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>

                    <!-- Additional Image -->
                    <?php if ($post['image_path'] && $post['image_path'] != $post['featured_image_path']): ?>
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" 
                             alt="Additional Image" class="post-image">
                    <?php endif; ?>

                    <!-- Post Content -->
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['detailed_message'])); ?>
                    </div>

                    <!-- External Links -->
                    <?php if (!empty($external_links)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-external-link-alt"></i> External Links & References</h5>
                            </div>
                            <div class="card-body external-links">
                                <?php foreach ($external_links as $link): ?>
                                    <?php $link = trim($link); ?>
                                    <?php if (!empty($link)): ?>
                                        <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener">
                                            <i class="fas fa-link"></i> <?php echo htmlspecialchars($link); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                        <div class="tags mb-4">
                            <h6><i class="fas fa-tags"></i> Tags:</h6>
                            <?php foreach ($tags as $tag): ?>
                                <?php $tag = trim($tag); ?>
                                <?php if (!empty($tag)): ?>
                                    <span class="badge bg-secondary">#<?php echo htmlspecialchars($tag); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share Buttons blocked by stephen can uncomment later if needed -->
                    <!-- <div class="share-buttons">
                        <h6><i class="fas fa-share-alt"></i> Share this report:</h6>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                           target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <button onclick="copyToClipboard()" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                    </div> -->
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Post Meta Information -->
                    <div class="post-meta">
                        <h5><i class="fas fa-info-circle"></i> Report Details</h5>
                        
                        <?php if ($post['incident_date']): ?>
                            <div class="meta-item">
                                <i class="fas fa-calendar meta-icon"></i>
                                <div>
                                    <strong>Incident Date:</strong><br>
                                    <?php echo format_date($post['incident_date']); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($post['state']): ?>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt meta-icon"></i>
                                <div>
                                    <strong>Location:</strong><br>
                                    <?php echo htmlspecialchars($post['state']); ?>
                                    <?php if ($post['district']): ?>
                                        , <?php echo htmlspecialchars($post['district']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($post['category_name']): ?>
                            <div class="meta-item">
                                <i class="fas fa-tag meta-icon"></i>
                                <div>
                                    <strong>Category:</strong><br>
                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <i class="fas fa-clock meta-icon"></i>
                            <div>
                                <strong>Reported On:</strong><br>
                                <?php echo format_date($post['created_at']); ?>
                            </div>
                        </div>

                        <?php if ($post['updated_at'] != $post['created_at']): ?>
                            <div class="meta-item">
                                <i class="fas fa-edit meta-icon"></i>
                                <div>
                                    <strong>Last Updated:</strong><br>
                                    <?php echo format_date($post['updated_at']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Location Map -->
                    <?php if ($post['latitude'] && $post['longitude']): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-map"></i> Location Map</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="map-container">
                                    <p class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        Coordinates: <?php echo htmlspecialchars($post['latitude']); ?>, <?php echo htmlspecialchars($post['longitude']); ?>
                                    </p>
                                    <!-- You can integrate Google Maps or OpenStreetMap here -->
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        <a href="https://www.google.com/maps?q=<?php echo $post['latitude']; ?>,<?php echo $post['longitude']; ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> View on Google Maps
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Related Reports -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list"></i> Related Reports</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get related posts from same state or category
                            $related_sql = "
                                SELECT id, title, state, incident_date 
                                FROM posts 
                                WHERE status = 'published' AND id != ? 
                                AND (state = ? OR category_id = ?) 
                                ORDER BY created_at DESC 
                                LIMIT 5
                            ";
                            $related_stmt = $pdo->prepare($related_sql);
                            $related_stmt->execute([$post['id'], $post['state'], $post['category_id']]);
                            $related_posts = $related_stmt->fetchAll();
                            
                            if ($related_posts):
                            ?>
                                <?php foreach ($related_posts as $related_post): ?>
                                    <div class="mb-3">
                                        <h6 class="mb-1">
                                            <a href="details.php?id=<?php echo $related_post['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars(truncate_text($related_post['title'], 60)); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($related_post['state']); ?>
                                            <?php if ($related_post['incident_date']): ?>
                                                • <?php echo format_date($related_post['incident_date']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">No related reports found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
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
    <script>
        function copyToClipboard() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Add smooth scrolling for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus on content when page loads
            if (window.location.hash) {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({behavior: 'smooth'});
                }
            }
        });
    </script>
</body>
</html>