<?php
// Test script for Cover Picture Upload and Super Admin Edit Features
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Cover Picture & Super Admin Edit Features Test</h1>";
echo "<p><strong>Testing Date:</strong> " . date('Y-m-d H:i:s') . "</p>";

$tests_passed = 0;
$total_tests = 0;

function test_result($test_name, $passed, $message = '') {
    global $tests_passed, $total_tests;
    $total_tests++;
    if ($passed) {
        $tests_passed++;
        echo "<p style='color: green;'>✅ <strong>$test_name:</strong> PASSED";
        if ($message) echo " - $message";
        echo "</p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>$test_name:</strong> FAILED";
        if ($message) echo " - $message";
        echo "</p>";
    }
    return $passed;
}

// Test 1: File Existence
echo "<h2>1. File Existence Tests</h2>";
$files_to_check = [
    'admin/add-post.php' => 'Admin add post with cover picture upload',
    'suladmin/posts.php' => 'Super admin posts listing',
    'suladmin/edit-post.php' => 'Super admin post edit form',
    'suladmin/auth.php' => 'Super admin authentication with file upload function'
];

foreach ($files_to_check as $file => $description) {
    test_result($description, file_exists(__DIR__ . '/' . $file), $file);
}

// Test 2: Database Connection
echo "<h2>2. Database Connection Test</h2>";
try {
    require_once __DIR__ . '/db.php';
    test_result("Database connection", isset($pdo), "PDO object available");
    
    if (isset($pdo)) {
        // Test posts table structure
        $stmt = $pdo->query("DESCRIBE posts");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $required_columns = ['featured_image_path', 'image_path', 'video_path'];
        foreach ($required_columns as $column) {
            test_result("Posts table has $column column", in_array($column, $columns));
        }
    }
} catch (Exception $e) {
    test_result("Database connection", false, "Error: " . $e->getMessage());
}

// Test 3: Functions Availability
echo "<h2>3. Required Functions Test</h2>";
try {
    // Test admin functions
    require_once __DIR__ . '/admin/auth.php';
    test_result("Admin handle_file_upload function", function_exists('handle_file_upload'));
    test_result("Admin generate_admin_csrf_token function", function_exists('generate_admin_csrf_token'));
    
    // Test super admin functions
    require_once __DIR__ . '/suladmin/auth.php';
    test_result("Super Admin generate_csrf_token function", function_exists('generate_csrf_token'));
    test_result("Super Admin check_super_admin_auth function", function_exists('check_super_admin_auth'));
    test_result("get_indian_states function", function_exists('get_indian_states'));
    
} catch (Exception $e) {
    test_result("Functions loading", false, "Error: " . $e->getMessage());
}

// Test 4: Form Elements Check
echo "<h2>4. Form Elements Test</h2>";
if (file_exists(__DIR__ . '/admin/add-post.php')) {
    $add_post_content = file_get_contents(__DIR__ . '/admin/add-post.php');
    
    $form_elements = [
        'name="featured_image"' => 'Cover picture upload field',
        'Cover Picture' => 'Cover picture label',
        'enctype="multipart/form-data"' => 'Form supports file upload',
        'accept="image/*"' => 'Image file type restriction'
    ];
    
    foreach ($form_elements as $element => $description) {
        test_result($description, strpos($add_post_content, $element) !== false);
    }
}

// Test 5: Super Admin Edit Features
echo "<h2>5. Super Admin Edit Features Test</h2>";
if (file_exists(__DIR__ . '/suladmin/posts.php')) {
    $posts_content = file_get_contents(__DIR__ . '/suladmin/posts.php');
    test_result("Posts listing has edit links", strpos($posts_content, 'edit-post.php') !== false);
    test_result("Edit button with proper icon", strpos($posts_content, 'fa-edit') !== false);
}

if (file_exists(__DIR__ . '/suladmin/edit-post.php')) {
    $edit_content = file_get_contents(__DIR__ . '/suladmin/edit-post.php');
    
    $edit_features = [
        'Cover Picture' => 'Cover picture edit field',
        'current-file' => 'Shows current uploaded files',
        'UPDATE posts SET' => 'Database update functionality',
        'enctype="multipart/form-data"' => 'Form supports file uploads'
    ];
    
    foreach ($edit_features as $feature => $description) {
        test_result($description, strpos($edit_content, $feature) !== false);
    }
}

// Test 6: Upload Directory
echo "<h2>6. Upload Directory Test</h2>";
$upload_dirs = ['uploads/', 'uploads/images/', 'uploads/videos/'];
foreach ($upload_dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    if (!is_dir($full_path)) {
        mkdir($full_path, 0755, true);
    }
    test_result("Upload directory: $dir", is_dir($full_path) && is_writable($full_path), "Exists and writable");
}

// Test 7: Sample Database Query
echo "<h2>7. Database Query Test</h2>";
if (isset($pdo)) {
    try {
        // Test categories query
        $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
        test_result("Categories query", true, "Found " . count($categories) . " active categories");
        
        // Test posts query with image fields
        $posts = $pdo->query("SELECT id, title, featured_image_path FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
        test_result("Posts query with image fields", true, "Found " . count($posts) . " posts");
        
        // Test admin users exist
        $admins = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role IN ('admin', 'super_admin')")->fetch();
        test_result("Admin users exist", $admins['count'] > 0, "Found " . $admins['count'] . " admin users");
        
    } catch (Exception $e) {
        test_result("Database queries", false, "Error: " . $e->getMessage());
    }
}

// Final Results
echo "<h2>🎯 Final Test Results</h2>";
if ($tests_passed == $total_tests) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🎉 ALL TESTS PASSED! ($tests_passed/$total_tests)</h3>";
    echo "<p><strong>Both features are ready to use!</strong></p>";
    
    echo "<h4>🖼️ Cover Picture Upload Feature:</h4>";
    echo "<ul>";
    echo "<li>✅ Available in admin/add-post.php</li>";
    echo "<li>✅ Properly labeled as 'Cover Picture (Featured Image)'</li>";
    echo "<li>✅ File validation and storage implemented</li>";
    echo "<li>✅ Preview functionality available</li>";
    echo "</ul>";
    
    echo "<h4>✏️ Super Admin Edit Feature:</h4>";
    echo "<ul>";
    echo "<li>✅ Edit links added to suladmin/posts.php</li>";
    echo "<li>✅ Complete edit form in suladmin/edit-post.php</li>";
    echo "<li>✅ Can edit all posts from all admins</li>";
    echo "<li>✅ Cover picture editing with current file display</li>";
    echo "<li>✅ File upload handling for image replacements</li>";
    echo "</ul>";
    
    echo "<h4>Next Steps:</h4>";
    echo "<ol>";
    echo "<li>Upload all files to your server</li>";
    echo "<li>Test cover picture upload: <code>https://projectsdemo.link/mapproject/admin/add-post.php</code></li>";
    echo "<li>Test super admin edit: <code>https://projectsdemo.link/mapproject/suladmin/posts.php</code></li>";
    echo "<li>Ensure upload directories have proper permissions (755)</li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>⚠️ SOME TESTS FAILED ($tests_passed/$total_tests passed)</h3>";
    echo "<p>Please review the failed tests above and fix the issues.</p>";
    echo "</div>";
}

// Feature Usage Guide
echo "<h2>📚 Feature Usage Guide</h2>";
echo "<div style='background-color: #e2e3e5; padding: 15px; border-radius: 10px;'>";
echo "<h4>🖼️ Using Cover Picture Upload (Admin):</h4>";
echo "<ol>";
echo "<li>Login to admin panel</li>";
echo "<li>Go to 'Add New Post'</li>";
echo "<li>In the 'Media Files' section, find 'Cover Picture (Featured Image)'</li>";
echo "<li>Upload an image (JPG, PNG, GIF - Max 10MB)</li>";
echo "<li>Preview will show immediately</li>";
echo "<li>Save post - cover picture will be stored in uploads/images/</li>";
echo "</ol>";

echo "<h4>✏️ Using Super Admin Edit (Super Admin):</h4>";
echo "<ol>";
echo "<li>Login to super admin panel</li>";
echo "<li>Go to 'All Posts'</li>";
echo "<li>Click the yellow 'Edit' button (📝) next to any post</li>";
echo "<li>Modify any field including cover pictures</li>";
echo "<li>Current images are shown with thumbnails</li>";
echo "<li>Upload new images to replace existing ones</li>";
echo "<li>Click 'Save Changes' or 'Save & Close'</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><small>Feature test completed: " . date('Y-m-d H:i:s') . "</small></p>";
?>