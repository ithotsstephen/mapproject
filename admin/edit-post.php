<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

$post_id = intval($_GET['id'] ?? 0);
$message = '';
$error = '';

if ($post_id <= 0) {
    header('Location: posts.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_admin_csrf_token($_POST['csrf_token'] ?? '')) {
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

            // Admins cannot publish directly. If an admin tries to set 'published', convert to 'admin_approval'.
            if ($status === 'published') {
                $status = 'admin_approval';
            }
            
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
            
            if (!empty($validation_errors)) {
                $error = implode('<br>', $validation_errors);
            } else {
                // Handle file uploads (only if new files are uploaded)
                $featured_image_path = null;
                $image_path = null;
                $video_path = null;
                
                // Get current file paths
                $stmt = $pdo->prepare("SELECT featured_image_path, image_path, video_path FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $current_post = $stmt->fetch();
                
                $featured_image_path = $current_post['featured_image_path'];
                $image_path = $current_post['image_path'];
                $video_path = $current_post['video_path'];
                
                // Handle new file uploads
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $new_featured_image = handle_file_upload($_FILES['featured_image'], 'uploads/images/', ['jpg', 'jpeg', 'png', 'gif']);
                    if ($new_featured_image) {
                        // Delete old file if exists
                        if ($featured_image_path && file_exists('../' . $featured_image_path)) {
                            unlink('../' . $featured_image_path);
                        }
                        $featured_image_path = $new_featured_image;
                    }
                }

                // Handle multiple additional images (append)
                $new_additional_images = [];
                if (isset($_FILES['additional_images'])) {
                    $files = $_FILES['additional_images'];
                    for ($i = 0; $i < count($files['name']); $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                        $fileArray = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                        $path = handle_file_upload($fileArray, 'uploads/images/', ['jpg', 'jpeg', 'png', 'gif']);
                        if ($path) $new_additional_images[] = $path;
                    }
                }
                
                if (isset($_FILES['video']) && $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $new_video = handle_file_upload($_FILES['video'], 'uploads/videos/', ['mp4', 'avi', 'mov', 'wmv']);
                    if ($new_video) {
                        // Delete old file if exists
                        if ($video_path && file_exists('../' . $video_path)) {
                            unlink('../' . $video_path);
                        }
                        $video_path = $new_video;
                    }
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
                
                // Update post
                $stmt = $pdo->prepare(""
                    . "UPDATE posts SET \n"
                    . "    title = ?, short_message = ?, detailed_message = ?, category_id = ?,\n"
                    . "    state = ?, district = ?, incident_date = ?, latitude = ?, longitude = ?,\n"
                    . "    featured_image_path = ?, image_path = ?, video_path = ?,\n"
                    . "    external_links = ?, tags = ?, status = ?, updated_at = NOW()\n"
                    . "WHERE id = ?"
                );
                
                $result = $stmt->execute([
                    $title, $short_message, $detailed_message, $category_id,
                    $state, $district, $incident_date, $latitude, $longitude,
                    $featured_image_path, $image_path, $video_path,
                    $external_links, $tags, $status, $post_id
                ]);
                
                if ($result) {
                    // Insert any newly uploaded additional images into post_images
                    if (!empty($new_additional_images)) {
                        $insert_img = $pdo->prepare("INSERT INTO post_images (post_id, image_path, sort_order, created_at) VALUES (?, ?, ?, NOW())");
                        // Determine starting order based on existing images
                        $order = 0;
                        try {
                            $oStmt = $pdo->prepare("SELECT MAX(sort_order) as maxo FROM post_images WHERE post_id = ?");
                            $oStmt->execute([$post_id]);
                            $maxo = $oStmt->fetchColumn();
                            $order = ($maxo !== null) ? intval($maxo) + 1 : 0;
                        } catch (Exception $e) {
                            $order = 0;
                        }
                        foreach ($new_additional_images as $imgPath) {
                            $insert_img->execute([$post_id, $imgPath, $order]);
                            $order++;
                        }
                    }
                    // Custom messages depending on status
                    if ($status === 'admin_approval') {
                        $message = 'Post updated and sent for Admin Approval. Only a Super Admin can publish this post.';
                    } elseif ($status === 'draft') {
                        $message = 'Post updated and saved as Draft.';
                    } elseif ($status === 'published') {
                        // This path should not be reachable for admins, but keep a safe message
                        $message = 'Post updated and marked Published.';
                    } else {
                        $message = 'Post updated successfully!';
                    }

                    log_admin_activity('Updated Post', "ID: $post_id, Title: $title, Status: $status");

                    // Redirect back to posts list or stay on edit page
                    if (isset($_POST['save_and_close'])) {
                        header("Location: posts.php?updated=1");
                        exit();
                    }
                } else {
                    throw new Exception('Failed to update post.');
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get post data
$stmt = $pdo->prepare(""
    . "SELECT p.*, c.name as category_name, u.name as admin_name \n"
    . "FROM posts p \n"
    . "LEFT JOIN categories c ON p.category_id = c.id \n"
    . "LEFT JOIN users u ON p.admin_id = u.id \n"
    . "WHERE p.id = ?"
);
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: posts.php?error=post_not_found');
    exit();
}

// Get categories and states
$categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
$states = get_indian_states();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post: <?php echo htmlspecialchars($post['title']); ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .page-header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; }
        .form-control:focus { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25); }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .file-preview { max-width: 200px; max-height: 200px; margin-top: 10px; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-user-shield"></i> Admin Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="posts.php">My Posts</a></li>
                    <li class="nav-item"><a class="nav-link" href="add-post.php">Add New Post</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../index.php" target="_blank">View Website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h2><i class="fas fa-edit"></i> Edit Post</h2>
            <p class="mb-0">Modify your persecution incident report</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <!-- Post Info -->
                <div class="post-info mb-3 p-3 bg-white rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Post ID:</strong> #<?php echo $post['id']; ?></p>
                            <p><strong>Created on:</strong> <?php echo date('F j, Y g:i A', strtotime($post['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Current Status:</strong> <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>"><?php echo ucfirst($post['status']); ?></span></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($post['category_name'] ?? 'None'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_admin_csrf_token(); ?>">

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="short_message" class="form-label">Short Message *</label>
                                    <textarea class="form-control" id="short_message" name="short_message" rows="3" required><?php echo htmlspecialchars($post['short_message']); ?></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="detailed_message" class="form-label">Detailed Message *</label>
                                    <textarea class="form-control" id="detailed_message" name="detailed_message" rows="6" required><?php echo htmlspecialchars($post['detailed_message']); ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $post['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            <?php if ($post['status'] === 'published'): ?>
                                                <!-- Show published as disabled so admin sees current published state but cannot set it -->
                                                <option value="published" selected disabled>Published (super-admin only)</option>
                                                <option value="admin_approval">Send for Admin Approval</option>
                                            <?php else: ?>
                                                <option value="admin_approval" <?php echo $post['status'] === 'admin_approval' ? 'selected' : ''; ?>>Admin Approval</option>
                                            <?php endif; ?>
                                        </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <select class="form-control" id="state" name="state">
                                        <option value="">Select State</option>
                                        <?php foreach ($states as $state_code => $state_name): ?>
                                        <option value="<?php echo htmlspecialchars($state_name); ?>" <?php echo $state_name === $post['state'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($state_name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="district" class="form-label">District</label>
                                    <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($post['district'] ?? ''); ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="incident_date" class="form-label">Incident Date</label>
                                    <input type="date" class="form-control" id="incident_date" name="incident_date" value="<?php echo $post['incident_date']; ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="<?php echo $post['latitude']; ?>" placeholder="e.g., 28.6139">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="<?php echo $post['longitude']; ?>" placeholder="e.g., 77.2090">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="featured_image" class="form-label">Cover Picture</label>
                                    <?php if ($post['featured_image_path']): ?>
                                    <div class="current-file">
                                        <small class="text-muted">Current:</small><br>
                                        <img src="../<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="img-thumbnail" style="max-width: 100px;"><br>
                                        <small><?php echo basename($post['featured_image_path']); ?></small>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                    <div class="form-text">Upload new image to replace current (JPG, PNG, GIF)</div>
                                    <div id="featured_image_preview"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="additional_images" class="form-label">Additional Images</label>
                                    <?php
                                    // Fetch existing images for this post
                                    $imgs_stmt = $pdo->prepare("SELECT id, image_path FROM post_images WHERE post_id = ? ORDER BY sort_order ASC, id ASC");
                                    $imgs_stmt->execute([$post_id]);
                                    $post_images = $imgs_stmt->fetchAll();
                                    ?>
                                    <?php if (!empty($post_images)): ?>
                                        <div class="mb-2" id="existing_images_container">
                                            <p class="small text-muted">Drag to reorder images. Edit caption and click <strong>Save Images</strong>.</p>
                                            <ul id="post-images-list" class="list-unstyled d-flex flex-wrap gap-2">
                                            <?php foreach ($post_images as $pi): ?>
                                                <li class="post-image-item p-2 bg-light rounded" data-image-id="<?php echo $pi['id']; ?>" style="max-width:160px;">
                                                    <div class="image-thumb text-center">
                                                        <img src="../<?php echo htmlspecialchars($pi['image_path']); ?>" class="img-thumbnail" style="max-width:140px; height:auto; display:block; margin-bottom:6px;">
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm image-caption" placeholder="Caption (optional)" value="<?php echo htmlspecialchars($pi['caption'] ?? ''); ?>">
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <small><a href="delete-post-image.php?id=<?php echo $pi['id']; ?>&post=<?php echo $post_id; ?>" class="text-danger small" onclick="return confirm('Delete this image?');">Delete</a></small>
                                                        <span class="drag-handle" style="cursor:grab;">☰</span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                            </ul>
                                            <div class="mt-2">
                                                <button id="save-images-btn" type="button" class="btn btn-sm btn-primary">Save Images</button>
                                                <span id="save-images-status" class="ms-2 text-muted small"></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                    <div class="form-text">Upload one or more new supporting images to add (they will be appended)</div>
                                    <div id="additional_images_preview"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="video" class="form-label">Video</label>
                                    <?php if ($post['video_path']): ?>
                                    <div class="current-file">
                                        <small class="text-muted">Current:</small><br>
                                        <i class="fas fa-video fa-2x text-muted"></i><br>
                                        <small><?php echo basename($post['video_path']); ?></small>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="video" name="video" accept="video/*">
                                    <div class="form-text">Supporting video (MP4, AVI, MOV)</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="external_links" class="form-label">External Links</label>
                                    <textarea class="form-control" id="external_links" name="external_links" rows="3" placeholder="One link per line"><?php echo htmlspecialchars($post['external_links'] ?? ''); ?></textarea>
                                    <div class="form-text">News articles, social media posts, etc.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars($post['tags'] ?? ''); ?>" placeholder="persecution, violence, discrimination">
                                    <div class="form-text">Comma-separated tags for categorization</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div><a href="posts.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Posts</a></div>
                                    <div>
                                        <button type="submit" name="save_and_continue" class="btn btn-outline-success me-2"><i class="fas fa-save"></i> Save Changes</button>
                                        <button type="submit" name="save_and_close" class="btn btn-success">Save & Close</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupFilePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (!input) return;

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (file.type.startsWith('image/')) {
                            preview.innerHTML = `<img src="${e.target.result}" class="file-preview img-thumbnail mt-2">`;
                        } else if (file.type.startsWith('video/')) {
                            preview.innerHTML = `<video src="${e.target.result}" class="file-preview mt-2" controls></video>`;
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
        }

        setupFilePreview('featured_image', 'featured_image_preview');
        setupFilePreview('additional_image', 'additional_image_preview');

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) { const bsAlert = new bootstrap.Alert(alert); bsAlert.close(); });
        }, 5000);
    </script>
</body>
</html>
<script>
    // Multi-file preview for additional_images[] input in edit form
    (function(){
        const input = document.getElementById('additional_images');
        const preview = document.getElementById('additional_images_preview');
        if (!input || !preview) return;

        input.addEventListener('change', function() {
            preview.innerHTML = '';
            const files = Array.from(input.files || []);
            files.forEach(function(file) {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '120px';
                    img.style.height = 'auto';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    })();
</script>
<script>
// Drag & drop reordering and Save Images handler
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('post-images-list');
    if (!list) return;

    let dragSrcEl = null;

    function handleDragStart(e) {
        dragSrcEl = this;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
        this.classList.add('dragging');
    }

    function handleDragOver(e) {
        if (e.preventDefault) e.preventDefault();
        this.classList.add('over');
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDragLeave(e) {
        this.classList.remove('over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) e.stopPropagation();
        if (dragSrcEl !== this) {
            // Swap elements
            const srcHtml = e.dataTransfer.getData('text/html');
            this.insertAdjacentHTML('beforebegin', srcHtml);
            const dropped = this.previousSibling;
            addDnDHandlers(dropped);
            dragSrcEl.parentNode.removeChild(dragSrcEl);
        }
        this.classList.remove('over');
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        const items = list.querySelectorAll('.post-image-item');
        items.forEach(function(it) { it.classList.remove('over'); });
    }

    function addDnDHandlers(item) {
        item.setAttribute('draggable', 'true');
        item.addEventListener('dragstart', handleDragStart, false);
        item.addEventListener('dragover', handleDragOver, false);
        item.addEventListener('dragleave', handleDragLeave, false);
        item.addEventListener('drop', handleDrop, false);
        item.addEventListener('dragend', handleDragEnd, false);
    }

    // Initialize handlers
    const items = list.querySelectorAll('.post-image-item');
    items.forEach(function(it) { addDnDHandlers(it); });

    // Save Images button
    const saveBtn = document.getElementById('save-images-btn');
    const statusSpan = document.getElementById('save-images-status');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const rows = Array.from(list.querySelectorAll('.post-image-item'));
            const imagesPayload = rows.map(function(row, idx) {
                return {
                    id: parseInt(row.getAttribute('data-image-id'), 10),
                    caption: row.querySelector('.image-caption').value || '',
                    sort_order: idx
                };
            });

            const csrf = document.querySelector('input[name="csrf_token"]').value;
            const postId = <?php echo json_encode($post_id); ?>;

            statusSpan.textContent = 'Saving...';
            fetch('update-post-images.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + encodeURIComponent(postId) +
                      '&csrf_token=' + encodeURIComponent(csrf) +
                      '&images=' + encodeURIComponent(JSON.stringify(imagesPayload))
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    statusSpan.textContent = 'Saved';
                    setTimeout(function(){ statusSpan.textContent = ''; }, 1500);
                } else {
                    statusSpan.textContent = (data && data.message) ? data.message : 'Error saving';
                }
            }).catch(function(err) {
                statusSpan.textContent = 'Network error';
            });
        });
    }
});
</script>
