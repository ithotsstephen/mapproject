<?php
session_start();
require_once '../db.php';
require_once 'auth.php';

check_admin_auth();
validate_admin_session();

$message = '';
$error = '';

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
                    log_admin_activity('Created Post', "ID: $post_id, Title: $title, Status: $status");
                    
                    // Redirect to edit page or posts list
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
    <title>Add New Post | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .file-upload-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-section h5 {
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
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
            
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="posts.php">
                    <i class="fas fa-file-alt"></i> My Posts
                </a>
                <a class="nav-link active" href="add-post.php">
                    <i class="fas fa-plus"></i> Add New Post
                </a>
            </div>
            
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2><i class="fas fa-plus"></i> Add New Post</h2>
                    <p class="mb-0">Create a new persecution incident report</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_admin_csrf_token(); ?>">
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                   placeholder="Enter a descriptive title for the incident" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_message" class="form-label">Short Summary *</label>
                            <textarea class="form-control" id="short_message" name="short_message" 
                                      rows="3" placeholder="Brief summary of the incident (will be shown in listings)"
                                      required><?php echo htmlspecialchars($_POST['short_message'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="detailed_message" class="form-label">Detailed Report *</label>
                            <textarea class="form-control" id="detailed_message" name="detailed_message" 
                                      rows="8" placeholder="Provide a detailed account of the incident"
                                      required><?php echo htmlspecialchars($_POST['detailed_message'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Location & Date -->
                    <div class="form-section">
                        <h5><i class="fas fa-map-marker-alt"></i> Location & Date</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State *</label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="">Select State</option>
                                    <?php foreach ($states as $state_name): ?>
                                        <option value="<?php echo htmlspecialchars($state_name); ?>"
                                                <?php echo ($state_name === ($_POST['state'] ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($state_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">District</label>
                                <input type="text" class="form-control" id="district" name="district" 
                                       value="<?php echo htmlspecialchars($_POST['district'] ?? ''); ?>" 
                                       placeholder="District name (optional)">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="incident_date" class="form-label">Incident Date</label>
                                <input type="date" class="form-control" id="incident_date" name="incident_date" 
                                       value="<?php echo $_POST['incident_date'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control" id="latitude" name="latitude" 
                                       value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>" 
                                       placeholder="e.g., 28.6139">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control" id="longitude" name="longitude" 
                                       value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>" 
                                       placeholder="e.g., 77.2090">
                            </div>
                        </div>
                    </div>

                    <!-- Media Files -->
                    <div class="form-section">
                        <h5><i class="fas fa-camera"></i> Media Files</h5>
                        
                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="featured_image" name="featured_image" 
                                   accept="image/*">
                            <small class="form-text text-muted">Main image for the post (JPG, PNG, GIF - Max 10MB)</small>
                            <div id="featured_image_preview"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_image" class="form-label">Additional Image</label>
                            <input type="file" class="form-control" id="additional_image" name="additional_image" 
                                   accept="image/*">
                            <small class="form-text text-muted">Additional supporting image (JPG, PNG, GIF - Max 10MB)</small>
                            <div id="additional_image_preview"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="video" class="form-label">Video</label>
                            <input type="file" class="form-control" id="video" name="video" 
                                   accept="video/mp4">
                            <small class="form-text text-muted">Supporting video (MP4 only - Max 10MB)</small>
                            <div id="video_preview"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Category & Status -->
                    <div class="form-section">
                        <h5><i class="fas fa-tag"></i> Category & Status</h5>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($category['id'] == ($_POST['category_id'] ?? '')) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Publication Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo (($_POST['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>
                                    Save as Draft
                                </option>
                                <option value="published" <?php echo (($_POST['status'] ?? '') === 'published') ? 'selected' : ''; ?>>
                                    Publish Now
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Drafts are not visible to the public
                            </small>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-link"></i> Additional Information</h5>
                        
                        <div class="mb-3">
                            <label for="external_links" class="form-label">External Links</label>
                            <textarea class="form-control" id="external_links" name="external_links" 
                                      rows="3" placeholder="One link per line (news articles, reports, etc.)"><?php echo htmlspecialchars($_POST['external_links'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags" 
                                   value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>" 
                                   placeholder="Comma-separated tags">
                            <small class="form-text text-muted">
                                e.g., violence, discrimination, harassment
                            </small>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="form-section">
                        <h5><i class="fas fa-save"></i> Actions</h5>
                        
                        <button type="submit" name="status" value="draft" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        
                        <button type="submit" name="status" value="published" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check"></i> Publish Post
                        </button>
                        
                        <a href="posts.php" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File preview functionality
        function setupFilePreview(inputId, previewId) {
            document.getElementById(inputId).addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById(previewId);
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (file.type.startsWith('image/')) {
                            preview.innerHTML = `<img src="${e.target.result}" class="file-upload-preview" alt="Preview">`;
                        } else if (file.type.startsWith('video/')) {
                            preview.innerHTML = `<video controls class="file-upload-preview"><source src="${e.target.result}" type="${file.type}"></video>`;
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
        }

        // Setup previews
        setupFilePreview('featured_image', 'featured_image_preview');
        setupFilePreview('additional_image', 'additional_image_preview');
        setupFilePreview('video', 'video_preview');

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        function autoSaveDraft() {
            // This could be implemented to auto-save drafts periodically
            console.log('Auto-save feature can be added here');
        }
    </script>
</body>
</html>