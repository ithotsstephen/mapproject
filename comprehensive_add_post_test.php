<?php
// Comprehensive add-post.php Fix Verification
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Add-Post.php Fix Verification Test</h1>";
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

// Test 1: Check if files exist
echo "<h2>1. File Existence Tests</h2>";
test_result("admin/add-post.php exists", file_exists(__DIR__ . '/admin/add-post.php'));
test_result("db.php exists", file_exists(__DIR__ . '/db.php'));
test_result("admin/auth.php exists", file_exists(__DIR__ . '/admin/auth.php'));

// Test 2: PHP Syntax Check
echo "<h2>2. PHP Syntax Tests</h2>";
$syntax_check = shell_exec("php -l " . escapeshellarg(__DIR__ . '/admin/add-post.php') . " 2>&1");
test_result("add-post.php syntax", strpos($syntax_check, 'No syntax errors') !== false, "Syntax validation");

// Test 3: Database Connection
echo "<h2>3. Database Connection Test</h2>";
try {
    require_once __DIR__ . '/db.php';
    test_result("Database connection", isset($pdo), "PDO object created");
    
    if (isset($pdo)) {
        $test_query = $pdo->query("SELECT 1 as test");
        $result = $test_query->fetch();
        test_result("Database query", $result['test'] == 1, "Basic SELECT query works");
    }
} catch (Exception $e) {
    test_result("Database connection", false, "Error: " . $e->getMessage());
}

// Test 4: Required Functions
echo "<h2>4. Required Functions Test</h2>";
try {
    require_once __DIR__ . '/admin/auth.php';
    
    test_result("check_admin_auth function", function_exists('check_admin_auth'));
    test_result("validate_admin_session function", function_exists('validate_admin_session'));
    test_result("generate_admin_csrf_token function", function_exists('generate_admin_csrf_token'));
    test_result("verify_admin_csrf_token function", function_exists('verify_admin_csrf_token'));
    test_result("log_admin_activity function", function_exists('log_admin_activity'));
    test_result("get_indian_states function", function_exists('get_indian_states'), "This was the missing function causing 500 error");
    
    // Test the get_indian_states function
    if (function_exists('get_indian_states')) {
        $states = get_indian_states();
        test_result("get_indian_states returns data", is_array($states) && count($states) > 0, "Found " . count($states) . " states");
        test_result("get_indian_states has required states", 
            isset($states['Maharashtra']) && isset($states['Delhi']) && isset($states['Tamil Nadu']), 
            "Key states present");
    }
    
} catch (Exception $e) {
    test_result("Function loading", false, "Error: " . $e->getMessage());
}

// Test 5: Database Tables
echo "<h2>5. Database Tables Test</h2>";
if (isset($pdo)) {
    try {
        // Test categories table
        $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
        test_result("Categories table query", true, "Found " . count($categories) . " active categories");
        
        // Test users table
        $users = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role IN ('admin', 'super_admin')")->fetch();
        test_result("Admin users exist", $users['count'] > 0, "Found " . $users['count'] . " admin users");
        
        // Test posts table structure
        $pdo->query("DESCRIBE posts");
        test_result("Posts table accessible", true, "Posts table structure OK");
        
    } catch (Exception $e) {
        test_result("Database tables", false, "Error: " . $e->getMessage());
    }
}

// Test 6: File Permissions (if on Unix-like system)
echo "<h2>6. File Permissions Test</h2>";
$add_post_file = __DIR__ . '/admin/add-post.php';
if (function_exists('fileperms')) {
    $perms = fileperms($add_post_file);
    $readable = is_readable($add_post_file);
    test_result("add-post.php readable", $readable, "File permissions: " . substr(sprintf('%o', $perms), -4));
}

// Test 7: Mock add-post.php execution
echo "<h2>7. Simulated add-post.php Execution</h2>";
try {
    // Start output buffering to catch any output
    ob_start();
    
    // Simulate the key parts of add-post.php without authentication
    session_start();
    
    // Skip auth for testing - just test the main components
    $_SESSION['user_id'] = 999; // Mock user ID
    $_SESSION['name'] = 'Test User';
    $_SESSION['role'] = 'admin';
    
    // Test the main queries that add-post.php runs
    $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
    $states = get_indian_states();
    
    $output = ob_get_clean();
    
    test_result("add-post.php key components", true, "Categories and states loaded successfully");
    
} catch (Exception $e) {
    ob_end_clean();
    test_result("add-post.php simulation", false, "Error: " . $e->getMessage());
}

// Test 8: Create minimal working test
echo "<h2>8. Minimal add-post Test Page</h2>";
$test_file_content = '<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
require_once "../db.php";
require_once "auth.php";

echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>Add-Post Test Page</h1>";

try {
    $categories = $pdo->query("SELECT id, name FROM categories WHERE status = \'active\' ORDER BY name")->fetchAll();
    echo "<p>Categories loaded: " . count($categories) . "</p>";
    
    $states = get_indian_states();
    echo "<p>States loaded: " . count($states) . "</p>";
    
    echo "<p style=\'color: green;\'>SUCCESS: All components working!</p>";
    echo "<a href=\'add-post.php\'>Try Real add-post.php</a>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>';

file_put_contents(__DIR__ . '/admin/add-post-test.php', $test_file_content);
test_result("Test file created", file_exists(__DIR__ . '/admin/add-post-test.php'), "Created admin/add-post-test.php");

// Final Results
echo "<h2>🎯 Final Test Results</h2>";
if ($tests_passed == $total_tests) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🎉 ALL TESTS PASSED! ($tests_passed/$total_tests)</h3>";
    echo "<p><strong>The add-post.php fix should be working!</strong></p>";
    echo "<h4>Next Steps:</h4>";
    echo "<ol>";
    echo "<li>Upload the updated <code>db.php</code> file to your server</li>";
    echo "<li>Visit: <code>https://projectsdemo.link/mapproject/admin/add-post-test.php</code></li>";
    echo "<li>If test passes, try: <code>https://projectsdemo.link/mapproject/admin/add-post.php</code></li>";
    echo "<li>Make sure you're logged in as an admin user first</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>⚠️ SOME TESTS FAILED ($tests_passed/$total_tests passed)</h3>";
    echo "<p>There are still issues that need to be resolved.</p>";
    echo "<h4>Troubleshooting Steps:</h4>";
    echo "<ol>";
    echo "<li>Check the specific failed tests above</li>";
    echo "<li>Verify database credentials in db.php</li>";
    echo "<li>Ensure all files are uploaded to the server</li>";
    echo "<li>Check web server error logs</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔧 Quick Debug Links</h3>";
echo "<ul>";
echo "<li><a href='admin/add-post-test.php' target='_blank'>Test add-post components</a></li>";
echo "<li><a href='admin/add-post.php' target='_blank'>Try actual add-post page</a></li>";
echo "<li><a href='admin/dashboard.php' target='_blank'>Admin dashboard</a></li>";
echo "</ul>";

echo "<p><small>Diagnostic completed: " . date('Y-m-d H:i:s') . "</small></p>";
?>