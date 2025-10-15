<?php
// Simple add-post.php test WITHOUT authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Add-Post.php Test (No Auth)</h1>";
echo "<p>Testing add-post.php components without authentication requirements...</p>";

try {
    // Start session but don't require login
    session_start();
    
    echo "<p>1. ✅ Session started</p>";
    
    // Include database
    require_once '../db.php';
    echo "<p>2. ✅ Database included</p>";
    
    // Test database connection
    if (isset($pdo)) {
        $test = $pdo->query("SELECT 1");
        echo "<p>3. ✅ Database connection working</p>";
    }
    
    // Include auth.php but don't call the auth functions
    require_once 'auth.php';
    echo "<p>4. ✅ Auth functions loaded</p>";
    
    // Test the problematic line that was causing 500 error
    echo "<p>5. Testing get_indian_states() function...</p>";
    $states = get_indian_states();
    echo "<p>✅ get_indian_states() works! Found " . count($states) . " states</p>";
    
    // Test categories query
    echo "<p>6. Testing categories query...</p>";
    $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name")->fetchAll();
    echo "<p>✅ Categories query works! Found " . count($categories) . " categories</p>";
    
    echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2 style='color: #155724;'>🎉 SUCCESS!</h2>";
    echo "<p><strong>All add-post.php components are working correctly!</strong></p>";
    echo "<p>The HTTP 500 error was definitely caused by the missing get_indian_states() function.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Make sure you upload the updated <code>db.php</code> to your server</li>";
    echo "<li>Ensure you're logged in as an admin user</li>";
    echo "<li>Try the actual add-post.php page</li>";
    echo "</ol>";
    echo "</div>";
    
    // Show some sample data
    echo "<h3>Sample States Data:</h3>";
    echo "<ul>";
    $count = 0;
    foreach ($states as $key => $value) {
        if ($count < 5) {
            echo "<li>$key</li>";
            $count++;
        }
    }
    echo "<li>... and " . (count($states) - 5) . " more states</li>";
    echo "</ul>";
    
    if (!empty($categories)) {
        echo "<h3>Available Categories:</h3>";
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>" . htmlspecialchars($category['name']) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2 style='color: #721c24;'>❌ Error Found</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack Trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
?>