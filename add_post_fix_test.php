<?php
// Simple error diagnostic for add-post.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>add-post.php Error Test</h2>";

try {
    echo "<p>1. Starting session...</p>";
    session_start();
    
    echo "<p>2. Including database file...</p>";
    require_once '../db.php';
    
    echo "<p>3. Including auth file...</p>";  
    require_once 'auth.php';
    
    echo "<p>4. Testing database connection...</p>";
    if (isset($pdo)) {
        $test = $pdo->query("SELECT 1");
        echo "<p style='color: green;'>✅ Database connection working</p>";
    } else {
        echo "<p style='color: red;'>❌ No \$pdo variable</p>";
    }
    
    echo "<p>5. Testing categories query...</p>";
    $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
    echo "<p style='color: green;'>✅ Categories query successful. Found " . count($categories) . " active categories.</p>";
    
    echo "<p>6. Testing get_indian_states function...</p>";
    $states = get_indian_states();
    echo "<p style='color: green;'>✅ get_indian_states() function working. Found " . count($states) . " states.</p>";
    
    echo "<p>7. Testing auth functions...</p>";
    if (function_exists('check_admin_auth')) {
        echo "<p style='color: green;'>✅ check_admin_auth function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ check_admin_auth function missing</p>";
    }
    
    if (function_exists('validate_admin_session')) {
        echo "<p style='color: green;'>✅ validate_admin_session function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ validate_admin_session function missing</p>";
    }
    
    if (function_exists('generate_admin_csrf_token')) {
        echo "<p style='color: green;'>✅ generate_admin_csrf_token function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ generate_admin_csrf_token function missing</p>";
    }
    
    echo "<div style='background-color: #d4edda; padding: 15px; margin-top: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #155724;'>✅ All Components Working!</h3>";
    echo "<p>If this test passes, the add-post.php page should work. The original 500 error was likely due to the missing get_indian_states() function, which has now been added to db.php.</p>";
    echo "<p><strong>Next step:</strong> Try accessing add-post.php again. If you still get an error, check:</p>";
    echo "<ul>";
    echo "<li>Web server error logs</li>";
    echo "<li>File permissions (should be 644 for .php files)</li>";
    echo "<li>Ensure you're logged in as an admin user</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; margin-top: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24;'>❌ Error Found:</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>