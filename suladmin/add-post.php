<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_super_admin_auth();
validate_super_admin_session();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            // Collect form data
            $title = trim($_POST['title'] ?? '');
            $short_message = trim($_POST['short_message'] ?? '');
            $detailed_message = trim($_POST['detailed_message'] ?? '');
            $category_id = intval($_POST['category_id'] ?? 0);
            $state = trim($_POST['state'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $incident_date = $_POST['incident_date'] ?? null;
            $latitude = trim($_POST['latitude'] ?? '');
            $longitude = trim($_POST['longitude'] ?? '');
            $external_links = trim($_POST['external_links'] ?? '');
            $tags = trim($_POST['tags'] ?? '');
            $status = $_POST['status'] ?? 'draft';

            // Validation
            $validation_errors = [];

            if (empty($title)) {
                $validation_errors[] = 'Title is required.';
            }

            if (empty($short_message)) {
                $validation_errors[] = 'Short message is required.';
            }

            if (empty($detailed_message)) {
                $validation_errors[] = 'Detailed message is required.';
            }

            if ($category_id <= 0) {
                $validation_errors[] = 'Please select a category.';
            }

            if (empty($state)) {
                $validation_errors[] = 'State is required.';
            }

            if (!in_array($status, ['draft', 'published', 'unpublished', 'admin_approval'], true)) {
                $status = 'draft';
            }

            if (!empty($validation_errors)) {
                $error = implode('<br>', $validation_errors);
            } else {
                // Handle file uploads
                $featured_image_path = null;
                $image_path = null;
                $video_path = null;

                try {
                    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $featured_image_path = handle_file_upload($_FILES['featured_image'], 'uploads/images/', ['jpg', 'jpeg', 'png', 'gif']);
                    }

                    if (isset($_FILES['additional_image']) && $_FILES['additional_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $image_path = handle_file_upload($_FILES['additional_image'], 'uploads/images/', ['jpg', 'jpeg', 'png', 'gif']);
                    }

                    if (isset($_FILES['video']) && $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $video_path = handle_file_upload($_FILES['video'], 'uploads/videos/', ['mp4']);
                    }
                } catch (Exception $e) {
                    throw new Exception('File upload error: ' . $e->getMessage());
                }

                // Clean coordinates
                $latitude = !empty($latitude) && is_numeric($latitude) ? floatval($latitude) : null;
                $longitude = !empty($longitude) && is_numeric($longitude) ? floatval($longitude) : null;

                // Clean incident date
                if (!empty($incident_date)) {
                    $incident_date = date('Y-m-d', strtotime($incident_date));
                } else {
                    $incident_date = null;
                }

                // Insert post
                $stmt = $pdo->prepare("
                    INSERT INTO posts (
                        title, short_message, detailed_message, category_id, admin_id,
                        state, district, incident_date, latitude, longitude,
                        featured_image_path, image_path, video_path,
                        external_links, tags, status, created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                    )
                ");

                $result = $stmt->execute([
                    $title, $short_message, $detailed_message, $category_id, $_SESSION['user_id'],
                    $state, $district, $incident_date, $latitude, $longitude,
                    $featured_image_path, $image_path, $video_path,
                    $external_links, $tags, $status
                ]);

                if ($result) {
                    $post_id = $pdo->lastInsertId();
                    $message = 'Post created successfully!';
                    log_super_admin_activity('Created Post', "ID: $post_id, Title: $title, Status: $status");

                    if ($status === 'draft') {
                        header("Location: edit-post.php?id=$post_id&created=1");
                    } else {
                        header("Location: posts.php?created=1");
                    }
                    exit();
                } else {
                    throw new Exception('Failed to create post.');
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get categories
$categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();

// Get states for dropdown
$states = get_indian_states();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post | Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .page-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-section h5 {
            color: #dc3545;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-crown"></i> Super Admin Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add-post.php"><i class="fas fa-plus"></i> Add Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="posts.php"><i class="fas fa-file-alt"></i> All Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admins.php"><i class="fas fa-users-cog"></i> Manage Admins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php"><i class="fas fa-tags"></i> Categories</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container-fluid">
            <h2><i class="fas fa-plus"></i> Add New Post</h2>
            <p class="mb-0">Create a new incident report</p>
        </div>
    </div>

    <div class="container-fluid">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-section">
                    <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Message *</label>
                            <textarea name="short_message" class="form-control" rows="3" required><?php echo htmlspecialchars($_POST['short_message'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Detailed Message *</label>
                            <textarea name="detailed_message" class="form-control" rows="6" required><?php echo htmlspecialchars($_POST['detailed_message'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fas fa-map-marker-alt"></i> Location & Date</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <select name="state" class="form-select" required>
                                <option value="">Select State</option>
                                <?php foreach ($states as $state_name): ?>
                                    <option value="<?php echo htmlspecialchars($state_name); ?>" <?php echo (($_POST['state'] ?? '') === $state_name) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($state_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District</label>
                            <input type="text" name="district" class="form-control" value="<?php echo htmlspecialchars($_POST['district'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Incident Date</label>
                            <input type="date" name="incident_date" class="form-control" value="<?php echo htmlspecialchars($_POST['incident_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fas fa-image"></i> Media</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="featured_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Additional Image</label>
                            <input type="file" name="additional_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Video (MP4)</label>
                            <input type="file" name="video" class="form-control" accept="video/mp4">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fas fa-link"></i> Links & Tags</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">External Links (comma separated)</label>
                            <input type="text" name="external_links" class="form-control" value="<?php echo htmlspecialchars($_POST['external_links'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fas fa-tag"></i> Status</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Publication Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?php echo (($_POST['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo (($_POST['status'] ?? '') === 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="unpublished" <?php echo (($_POST['status'] ?? '') === 'unpublished') ? 'selected' : ''; ?>>Unpublished</option>
                                <option value="admin_approval" <?php echo (($_POST['status'] ?? '') === 'admin_approval') ? 'selected' : ''; ?>>Admin Approval</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Save Post</button>
                    <a href="posts.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
