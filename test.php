<?php
/**
 * Diagnostic Test File for projectsdemo.link/maproject
 * Upload this file to test your server configuration
 */

echo "<h1>🔍 Diagnostic Test for projectsdemo.link/maproject</h1>";
echo "<hr>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: PHP is working!</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "<hr>";

// Test 2: File paths
echo "<h2>📁 Test 2: File Paths</h2>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<hr>";

// Test 3: Check if files exist
echo "<h2>📄 Test 3: File Structure Check</h2>";
$files_to_check = [
    'index.php',
    'db.php', 
    'state.php',
    'details.php',
    'admin/index.php',
    'suladmin/index.php',
    'css/map-style.css',
    'js/jquery.min.js'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file - EXISTS<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}
echo "<hr>";

// Test 4: Database connection test
echo "<h2>🗄️ Test 4: Database Connection</h2>";
try {
    if (file_exists('db.php')) {
        // Don't include db.php yet, just check if it exists
        echo "✅ db.php file exists<br>";
        
        // Try to read the database config
        $db_content = file_get_contents('db.php');
        if (strpos($db_content, 'projectsdemo_maproject') !== false) {
            echo "✅ Database config appears to be updated for your domain<br>";
        } else {
            echo "⚠️ Database config may need updating for your domain<br>";
        }
        
        // Try database connection (comment out if causing issues)
        /*
        require_once 'db.php';
        echo "✅ Database connection successful!<br>";
        */
        
    } else {
        echo "❌ db.php file not found<br>";
    }
} catch (Exception $e) {
    echo "⚠️ Database connection issue: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 5: Server information
echo "<h2>🖥️ Test 5: Server Information</h2>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "<hr>";

// Test 6: Permissions check
echo "<h2>🔒 Test 6: Permissions Check</h2>";
$dirs_to_check = ['uploads', 'uploads/images', 'uploads/videos'];
foreach ($dirs_to_check as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $dir - EXISTS and WRITABLE<br>";
        } else {
            echo "⚠️ $dir - EXISTS but NOT WRITABLE<br>";
        }
    } else {
        echo "❌ $dir - DOES NOT EXIST<br>";
    }
}
echo "<hr>";

// Test 7: Next steps
echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>If you see this page</strong>: Your server is working! ✅</li>";
echo "<li><strong>Check file structure</strong>: Make sure all files from the zip are uploaded</li>";
echo "<li><strong>Database setup</strong>: Create database in cPanel and import SQL file</li>";
echo "<li><strong>Update db.php</strong>: Add your real database credentials</li>";
echo "<li><strong>Test homepage</strong>: Go to <a href='index.php'>index.php</a></li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>🔗 Quick Links:</strong></p>";
echo "<ul>";
echo "<li><a href='index.php'>Homepage (index.php)</a></li>";
echo "<li><a href='admin/'>Admin Panel</a></li>";
echo "<li><a href='suladmin/'>Super Admin Panel</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Delete this file (test.php) after troubleshooting is complete.</em></p>";
?>