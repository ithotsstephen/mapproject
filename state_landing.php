<?php
session_start();
require_once 'db.php';

// Get state name from URL and normalize it
$raw_state = $_GET['state'] ?? '';
$state = trim((string)$raw_state);

if (empty($state)) {
    header('Location: index.php');
    exit();
}

$state = urldecode($state);
$state = html_entity_decode($state, ENT_QUOTES | ENT_HTML5);
$state = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $state);
$state = trim(preg_replace('/\s+/u', ' ', $state));

// We'll attempt exact match, then LIKE, then fuzzy (similar to state.php)
$query_state = $state;

// helper to fetch posts for a state (exact match)
$get_posts_for_state = function($pdo, $state_val, $limit = 6) {
    // Use integer interpolation for LIMIT to avoid drivers that don't allow
    // binding LIMIT as a parameter when native prepares are in use.
    $limit = (int)$limit;
    $sql = "SELECT p.*, c.name as category_name
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE LOWER(TRIM(state)) = LOWER(TRIM(?)) AND status = 'published'
            ORDER BY p.incident_date DESC, p.created_at DESC
            LIMIT " . $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$state_val]);
    return $stmt->fetchAll();
};

// total count for exact match
// Perform all DB lookups inside a try/catch to avoid fatal errors
$error_message = '';
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE LOWER(TRIM(state)) = LOWER(TRIM(?)) AND status = 'published'");
    $count_stmt->execute([$query_state]);
    $total_posts = (int)$count_stmt->fetchColumn();

    $recent_posts = [];
    if ($total_posts > 0) {
        $recent_posts = $get_posts_for_state($pdo, $query_state, 6);
    }

    // fallback: LIKE
    if ($total_posts === 0) {
        $like_param = '%' . $state . '%';
        $fallback_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE LOWER(state) LIKE LOWER(?) AND status = 'published'");
        $fallback_count_stmt->execute([$like_param]);
        $fallback_total = (int)$fallback_count_stmt->fetchColumn();
        if ($fallback_total > 0) {
            $total_posts = $fallback_total;
            // grab recent
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE LOWER(state) LIKE LOWER(?) AND status = 'published'
                ORDER BY p.incident_date DESC, p.created_at DESC
                LIMIT 6");
            $stmt->execute([$like_param]);
            $recent_posts = $stmt->fetchAll();
            $query_state = $state; // still display requested
        }
    }

    // final fallback: fuzzy match against distinct state values
    if ($total_posts === 0) {
        $distinct_stmt = $pdo->query("SELECT DISTINCT state FROM posts");
        $candidates = $distinct_stmt->fetchAll(PDO::FETCH_COLUMN);

        // helper: use mb_strtolower if available, otherwise fallback to strtolower
        $mb_to_lower = function($str) {
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($str, 'UTF-8');
            }
            return strtolower($str);
        };

        $normalize_for_compare = function($s) use ($mb_to_lower) {
            $s = (string)$s;
            $s = urldecode($s);
            $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5);
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);
            $s = preg_replace('/[^a-z0-9]+/u', ' ', $mb_to_lower($s));
            $s = trim(preg_replace('/\s+/u', ' ', $s));
            return $s;
        };

        $norm_req = $normalize_for_compare($state);
        $best = null;
        $bestScore = PHP_INT_MAX;
        foreach ($candidates as $cand) {
            $norm_c = $normalize_for_compare($cand);
            if ($norm_c === '') continue;
            $dist = levenshtein($norm_req, $norm_c);
            if ($dist < $bestScore) {
                $bestScore = $dist;
                $best = $cand;
            }
        }

        if ($best !== null) {
            $len = max(1, max(strlen($norm_req), strlen($normalize_for_compare($best))));
            if ($bestScore <= 3 || $bestScore <= max(1, intval($len * 0.25))) {
                $query_state = $best;
                // recompute counts & recent
                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE LOWER(TRIM(state)) = LOWER(TRIM(?)) AND status = 'published'");
                $count_stmt->execute([$query_state]);
                $total_posts = (int)$count_stmt->fetchColumn();
                $recent_posts = $get_posts_for_state($pdo, $query_state, 6);
            }
        }
    }

    // summary: top categories in this state
    $cat_stmt = $pdo->prepare("SELECT c.name, COUNT(*) as ccount FROM posts p LEFT JOIN categories c ON p.category_id = c.id WHERE LOWER(TRIM(p.state)) = LOWER(TRIM(?)) AND p.status = 'published' GROUP BY c.id ORDER BY ccount DESC LIMIT 6");
    $cat_stmt->execute([$query_state]);
    $top_categories = $cat_stmt->fetchAll();

} catch (Exception $e) {
    // Log full exception to server logs for later inspection
    error_log('state_landing error: ' . $e->getMessage());
    $total_posts = 0;
    $recent_posts = [];
    $top_categories = [];
    $error_message = 'An internal error occurred while loading state data. Please try again later.';

    // If debug flag provided, append the real exception message to the alert (temporary, for troubleshooting only)
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        $error_message .= ' Details: ' . $e->getMessage();
    }
}

$display_state = ($query_state !== $state) ? $query_state : $state;

// summary: top categories in this state
$cat_stmt = $pdo->prepare("SELECT c.name, COUNT(*) as ccount FROM posts p LEFT JOIN categories c ON p.category_id = c.id WHERE LOWER(TRIM(p.state)) = LOWER(TRIM(?)) AND p.status = 'published' GROUP BY c.id ORDER BY ccount DESC LIMIT 6");
$cat_stmt->execute([$query_state]);
$top_categories = $cat_stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars($display_state); ?> — State Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color: white; padding: 48px 0; margin-bottom: 24px;
        }
        .stat-card { background: #fff; border-radius:8px; padding:16px; box-shadow:0 6px 20px rgba(0,0,0,0.05); }
        .recent-thumb { width:100%; height:160px; object-fit:cover; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Persecution Tracker</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-light" href="incidents.php">Incidents</a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5"><?php echo htmlspecialchars($display_state); ?></h1>
                    <p class="lead">Overview of reported incidents in <?php echo htmlspecialchars($display_state); ?>.</p>
                    <?php if ($display_state !== $state): ?>
                        <div class="alert alert-info">Showing results for <strong><?php echo htmlspecialchars($display_state); ?></strong> (requested: <em><?php echo htmlspecialchars($state); ?></em>)</div>
                    <?php endif; ?>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger mt-2"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a class="btn btn-light me-2" href="incidents.php">View All Incidents</a>
                        <a class="btn btn-outline-light" href="details.php">View Map</a>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <div class="stat-card d-inline-block text-center">
                        <div style="font-size:32px;font-weight:700"><?php echo $total_posts; ?></div>
                        <div class="text-muted">Published Incidents</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <h4>Recent Reports</h4>
                <div class="row">
                    <?php if (empty($recent_posts)): ?>
                        <div class="col-12">
                            <p class="text-muted">No recent reports available for this state.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_posts as $post): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <?php if (!empty($post['featured_image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="recent-thumb" alt="">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                        <p class="card-text"><?php echo truncate_text(htmlspecialchars($post['short_message']), 120); ?></p>
                                        <a href="details.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">Read more</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="col-lg-4">
                <h5>Top Categories</h5>
                <ul class="list-group mb-4">
                    <?php if (empty($top_categories)): ?>
                        <li class="list-group-item">No category data</li>
                    <?php else: ?>
                        <?php foreach ($top_categories as $tc): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($tc['name'] ?: 'Uncategorized'); ?>
                                <span class="badge bg-secondary rounded-pill"><?php echo $tc['ccount']; ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <h5>Actions</h5>
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-primary" href="./incidents.php">See all incidents</a>
                    <a class="btn btn-outline-success" href="incidents.php?state=<?php echo urlencode($query_state); ?>">Filter incidents</a>
                </div>
            </aside>
        </div>
    </main>

    <footer class="bg-light py-4">
        <div class="container text-center text-muted">&copy; <?php echo date('Y'); ?> Persecution Tracker</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
