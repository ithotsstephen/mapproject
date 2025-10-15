<?php
/**
 * URL and File Structure Diagnostic
 * This script helps identify URL and file structure issues
 */

echo "<h2>🔍 URL & File Structure Diagnostic</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .error{color:red;} .success{color:green;} .warning{color:orange;} .info{color:blue;}</style>";

// Get current URL information
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$base_url = dirname($current_url);
$document_root = $_SERVER['DOCUMENT_ROOT'];
$script_path = __FILE__;
$script_dir = dirname($script_path);

echo "<h3>📍 Current Environment Info</h3>";
echo "<ul>";
echo "<li><strong>Current URL:</strong> $current_url</li>";
echo "<li><strong>Base URL:</strong> $base_url</li>";
echo "<li><strong>Document Root:</strong> $document_root</li>";
echo "<li><strong>Script Directory:</strong> $script_dir</li>";
echo "</ul>";

// Determine project path
$project_folder = basename($script_dir);
echo "<div class='info'>📁 <strong>Project Folder:</strong> $project_folder</div>";

// Check for admin directory and files
echo "<h3>📂 File Structure Check</h3>";

$admin_dir = $script_dir . '/admin';
$suladmin_dir = $script_dir . '/suladmin';

if (is_dir($admin_dir)) {
    echo "<div class='success'>✅ Admin directory exists: $admin_dir</div>";
    
    // Check for key admin files
    $admin_files = [
        'index.php' => 'Admin login page',
        'dashboard.php' => 'Admin dashboard',
        'add-post.php' => 'Add post page',
        'posts.php' => 'Posts management',
        'auth.php' => 'Authentication handler',
        'logout.php' => 'Logout handler'
    ];
    
    echo "<ul>";
    foreach ($admin_files as $file => $description) {
        $file_path = $admin_dir . '/' . $file;
        if (file_exists($file_path)) {
            echo "<li class='success'>✅ $file ($description)</li>";
        } else {
            echo "<li class='error'>❌ Missing: $file ($description)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<div class='error'>❌ Admin directory not found: $admin_dir</div>";
}

if (is_dir($suladmin_dir)) {
    echo "<div class='success'>✅ Super Admin directory exists</div>";
} else {
    echo "<div class='error'>❌ Super Admin directory not found</div>";
}

// Check database connection
echo "<h3>🔌 Database Connection Test</h3>";

$db_files = ['db.php', 'db_hosting.php'];
$db_connected = false;

foreach ($db_files as $db_file) {
    $db_path = $script_dir . '/' . $db_file;
    if (file_exists($db_path)) {
        echo "<div class='success'>✅ Database file exists: $db_file</div>";
        
        try {
            // Test connection with this file
            ob_start();
            include $db_path;
            ob_end_clean();
            
            if (isset($pdo)) {
                // Test the connection
                $pdo->query("SELECT 1");
                echo "<div class='success'>✅ Database connection successful with $db_file</div>";
                $db_connected = true;
                
                // Check for users table
                try {
                    $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    echo "<div class='success'>✅ Users table has $user_count users</div>";
                } catch (Exception $e) {
                    echo "<div class='warning'>⚠️ Users table issue: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                break;
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Database connection failed with $db_file: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

if (!$db_connected) {
    echo "<div class='error'>❌ No working database connection found</div>";
}

// Generate correct URLs
echo "<h3>🔗 Correct URLs for Your Project</h3>";

$domain = $_SERVER['HTTP_HOST'];
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");

echo "<div class='info'>Based on your current setup, your URLs should be:</div>";
echo "<ul>";
echo "<li><strong>Homepage:</strong> <a href='$protocol://$domain/$project_folder/' target='_blank'>$protocol://$domain/$project_folder/</a></li>";
echo "<li><strong>Admin Login:</strong> <a href='$protocol://$domain/$project_folder/admin/' target='_blank'>$protocol://$domain/$project_folder/admin/</a></li>";
echo "<li><strong>Add Post:</strong> <a href='$protocol://$domain/$project_folder/admin/add-post.php' target='_blank'>$protocol://$domain/$project_folder/admin/add-post.php</a></li>";
echo "<li><strong>Super Admin:</strong> <a href='$protocol://$domain/$project_folder/suladmin/' target='_blank'>$protocol://$domain/$project_folder/suladmin/</a></li>";
echo "</ul>";

// Check uploads directory
echo "<h3>📁 Upload Directory Check</h3>";

$uploads_dir = $script_dir . '/uploads';
if (is_dir($uploads_dir)) {
    echo "<div class='success'>✅ Uploads directory exists</div>";
    if (is_writable($uploads_dir)) {
        echo "<div class='success'>✅ Uploads directory is writable</div>";
    } else {
        echo "<div class='error'>❌ Uploads directory is not writable - check permissions</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Uploads directory missing - creating it...</div>";
    if (mkdir($uploads_dir, 0755, true)) {
        echo "<div class='success'>✅ Uploads directory created successfully</div>";
    } else {
        echo "<div class='error'>❌ Failed to create uploads directory</div>";
    }
}

// Provide troubleshooting steps
echo "<h3>🛠️ Troubleshooting Steps</h3>";

echo "<div class='warning'>If the admin pages are not working:</div>";
echo "<ol>";
echo "<li><strong>Check file upload:</strong> Make sure all admin/* files are uploaded correctly</li>";
echo "<li><strong>Check permissions:</strong> Ensure PHP files have proper permissions (644)</li>";
echo "<li><strong>Check database:</strong> Make sure the database is imported and connected</li>";
echo "<li><strong>Clear browser cache:</strong> Try accessing in incognito/private mode</li>";
echo "<li><strong>Check error logs:</strong> Look at cPanel error logs for specific errors</li>";
echo "</ol>";

echo "<div class='info'>💡 <strong>Test the links above to see which ones work!</strong></div>";
?>