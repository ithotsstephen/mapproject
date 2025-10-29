<?php
session_start();
require_once 'db.php';

// Inputs
$from_date = sanitize_input($_GET['from_date'] ?? '');
$to_date = sanitize_input($_GET['to_date'] ?? '');
$state_filter = sanitize_input($_GET['state'] ?? '');

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build WHERE clauses
$where = "WHERE p.status = 'published'";
$params = [];

if (!empty($state_filter)) {
    $where .= " AND p.state = ?";
    $params[] = $state_filter;
}

if (!empty($from_date)) {
    // Filter by created_at (date portion)
    $where .= " AND DATE(p.created_at) >= ?";
    $params[] = $from_date;
}

if (!empty($to_date)) {
    // Filter by created_at (date portion)
    $where .= " AND DATE(p.created_at) <= ?";
    $params[] = $to_date;
}

// Count total
$count_sql = "SELECT COUNT(*) FROM posts p " . $where;
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = (int) $count_stmt->fetchColumn();
$total_pages = (int) ceil($total / $per_page);

// Fetch paginated posts
$sql = "
    SELECT p.*, c.name as category_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    " . $where . "
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
";

// Append limit/offset to params
$fetch_params = $params;
$fetch_params[] = $per_page;
$fetch_params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($fetch_params);
$posts = $stmt->fetchAll();

// Get states for dropdown
$states = get_indian_states();

function build_query_params($overrides = []) {
    $q = [];
    $keys = ['from_date','to_date','state','page'];
    foreach ($keys as $k) {
        if (isset($_GET[$k]) && $k !== 'page') {
            $q[$k] = $_GET[$k];
        }
    }
    foreach ($overrides as $k => $v) {
        $q[$k] = $v;
    }
    return http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents — Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 180px; object-fit: cover; }
        .filter-row .form-control { min-width: 180px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><strong>Persecution Tracker</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="incidents.php">Incidents</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h3>Incidents</h3>
                    <p class="text-muted">Browse published reports. Use filters to narrow results.</p>
                </div>
            </div>

            <form method="get" class="row g-2 align-items-end filter-row mb-4">
                <div class="col-auto">
                    <label class="form-label">From (Created On)</label>
                    <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                </div>
                <div class="col-auto">
                    <label class="form-label">To (Created On)</label>
                    <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                </div>
                <div class="col-auto">
                    <label class="form-label">State</label>
                    <select name="state" class="form-select">
                        <option value="">All States</option>
                        <?php foreach ($states as $s): ?>
                            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $s === $state_filter ? 'selected' : ''; ?>><?php echo htmlspecialchars($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Filter</button>
                    <a href="incidents.php" class="btn btn-outline-secondary ms-2">Reset</a>
                </div>
            </form>

            <div class="row">
                <?php if (empty($posts)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">No incidents found for the selected filters.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($post['featured_image_path'])): ?>
                                    <a href="details.php?id=<?php echo $post['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="card-img-top" alt="">
                                    </a>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="details.php?id=<?php echo $post['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($post['title']); ?></a>
                                    </h5>
                                    <p class="card-text"><?php echo htmlspecialchars(truncate_text($post['short_message'], 200)); ?></p>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($post['state']); ?>
                                        <?php if ($post['category_name']): ?> | <i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['category_name']); ?><?php endif; ?>
                                    </p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?php echo format_date($post['created_at']); ?></small>
                                    <a href="details.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php
                        $start = max(1, $page - 3);
                        $end = min($total_pages, $page + 3);
                        if ($page > 1):
                        ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo build_query_params(['page' => $page - 1]); ?>">&laquo; Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo build_query_params(['page' => $i]); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo build_query_params(['page' => $page + 1]); ?>">Next &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Persecution Tracker</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
