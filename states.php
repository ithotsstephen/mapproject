<?php
session_start();
require_once 'db.php';

// Get counts of published posts grouped by state
$stmt = $pdo->query("SELECT state, COUNT(*) as cnt FROM posts WHERE status = 'published' AND state IS NOT NULL AND TRIM(state) != '' GROUP BY state ORDER BY cnt DESC, state ASC");
$states = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>States — Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Persecution Tracker</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="index.php">Home</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-3"><i class="fas fa-map"></i> States with Reports</h1>
                <p class="text-muted">Click a state to view published reports for that state.</p>
            </div>
        </div>

        <?php if (empty($states)): ?>
            <div class="alert alert-info">No published reports found yet.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($states as $s):
                    $state_name = trim($s['state']);
                    // Normalize display
                    $display = $state_name ?: 'Unknown';
                ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                       href="state.php?state=<?php echo urlencode($state_name); ?>">
                        <span><?php echo htmlspecialchars($display); ?></span>
                        <span class="badge bg-secondary rounded-pill"><?php echo (int)$s['cnt']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <footer class="text-center py-4">
        <small>&copy; <?php echo date('Y'); ?> Persecution Tracker</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
