<?php
// PHP Syntax and Error Diagnostic for add-post.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>add-post.php Error Diagnostic</h2>";
echo "<p>Checking for PHP errors in add-post.php...</p>";

$file_path = __DIR__ . '/admin/add-post.php';

// Check if file exists
if (!file_exists($file_path)) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "<h3>❌ File Not Found</h3>";
    echo "<p>The file admin/add-post.php does not exist at: " . $file_path . "</p>";
    echo "</div>";
    exit;
}

echo "<h3>1. File Existence Check</h3>";
echo "<p style='color: green;'>✅ File exists: " . $file_path . "</p>";
echo "<p>File size: " . number_format(filesize($file_path)) . " bytes</p>";
echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($file_path)) . "</p>";

// Check PHP syntax
echo "<h3>2. PHP Syntax Check</h3>";
$syntax_check = shell_exec("php -l " . escapeshellarg($file_path) . " 2>&1");
if (strpos($syntax_check, 'No syntax errors') !== false) {
    echo "<p style='color: green;'>✅ PHP Syntax: " . htmlspecialchars($syntax_check) . "</p>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "<h4>❌ PHP Syntax Errors Found:</h4>";
    echo "<pre>" . htmlspecialchars($syntax_check) . "</pre>";
    echo "</div>";
}

// Check for required includes/dependencies
echo "<h3>3. Dependencies Check</h3>";
$content = file_get_contents($file_path);

$required_files = [
    '../db.php' => __DIR__ . '/db.php',
    'auth.php' => __DIR__ . '/admin/auth.php'
];

foreach ($required_files as $include_path => $actual_path) {
    if (strpos($content, $include_path) !== false) {
        if (file_exists($actual_path)) {
            echo "<p style='color: green;'>✅ Required file exists: $include_path</p>";
        } else {
            echo "<p style='color: red;'>❌ Required file missing: $include_path (looking for: $actual_path)</p>";
        }
    }
}

// Check for common PHP issues in the content
echo "<h3>4. Common Issues Check</h3>";

$issues = [];

// Check for missing PHP opening tags
if (!preg_match('/^\s*<\?php/', $content)) {
    $issues[] = "File doesn't start with proper PHP opening tag";
}

// Check for potential variable issues
if (preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*\$_[A-Z]+\[[\'"][^\'"]*[\'"]\]\s*\?\?\s*[\'"][^\'"]/', $content)) {
    // This is actually okay - null coalescing operator
} else {
    // Check for uninitialized variables
    preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*\$_POST\[/', $content, $post_vars);
    if (!empty($post_vars[1])) {
        echo "<p style='color: orange;'>⚠️ Found $_POST variables, check for proper null coalescing</p>";
    }
}

// Check for session_start
if (strpos($content, 'session_start()') === false) {
    $issues[] = "Missing session_start() call";
}

// Check for database connection
if (strpos($content, '$pdo') === false) {
    $issues[] = "No database connection variable (\$pdo) found";
}

if (empty($issues)) {
    echo "<p style='color: green;'>✅ No obvious issues found in code structure</p>";
} else {
    echo "<div style='color: orange; padding: 10px; border: 1px solid orange;'>";
    echo "<h4>⚠️ Potential Issues:</h4>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>" . htmlspecialchars($issue) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Check for specific function calls that might cause issues
echo "<h3>5. Function and Method Check</h3>";

$problematic_patterns = [
    'verify_admin_csrf_token' => 'CSRF token verification function',
    'generate_admin_csrf_token' => 'CSRF token generation function', 
    'check_admin_auth' => 'Admin authentication check',
    'validate_admin_session' => 'Session validation function',
    'log_admin_activity' => 'Activity logging function'
];

foreach ($problematic_patterns as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "<p style='color: orange;'>⚠️ Found call to $description - ensure this function exists in auth.php</p>";
    }
}

// Test database connection if possible
echo "<h3>6. Database Connection Test</h3>";
try {
    if (file_exists(__DIR__ . '/db.php')) {
        include_once __DIR__ . '/db.php';
        if (isset($pdo)) {
            $test_query = $pdo->query("SELECT 1");
            if ($test_query) {
                echo "<p style='color: green;'>✅ Database connection working</p>";
            } else {
                echo "<p style='color: red;'>❌ Database connection failed</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ \$pdo variable not defined in db.php</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ db.php file not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>7. Suggested Actions</h3>";
echo "<ol>";
echo "<li>Check your web server's error logs for specific PHP errors</li>";
echo "<li>Ensure all required files (db.php, auth.php) exist and are properly configured</li>";
echo "<li>Verify database connection credentials are correct</li>";
echo "<li>Check file permissions (should be readable by web server)</li>";
echo "<li>Try accessing the page with error reporting enabled</li>";
echo "</ol>";

echo "<h3>8. Quick Test</h3>";
echo "<p>Try this minimal test to isolate the issue:</p>";
echo "<pre>";
echo htmlspecialchars('<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
echo "PHP is working";

// Test includes one by one
try {
    require_once "../db.php";
    echo " - db.php loaded";
} catch (Exception $e) {
    echo " - db.php ERROR: " . $e->getMessage();
}

try {
    require_once "auth.php";
    echo " - auth.php loaded";
} catch (Exception $e) {
    echo " - auth.php ERROR: " . $e->getMessage();
}
?>');
echo "</pre>";

echo "<hr>";
echo "<p><small>Diagnostic completed: " . date('Y-m-d H:i:s') . "</small></p>";
?>