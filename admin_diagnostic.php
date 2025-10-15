<?php
/**
 * Admin Pages Diagnostic Tool
 * Use this to troubleshoot admin page issues
 */

session_start();

echo "<h2>🔧 Admin Pages Diagnostic</h2>";
echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    .test-link { margin: 5px; padding: 5px 10px; background: #f0f0f0; border-radius: 5px; display: inline-block; }
</style>";

// Test 1: Check if admin files exist
echo "<div class='section'>";
echo "<h3>📂 Admin Files Check</h3>";

$admin_files = [
    'admin/index.php' => 'Admin login page',
    'admin/auth.php' => 'Authentication functions',
    'admin/dashboard.php' => 'Admin dashboard',
    'admin/add-post.php' => 'Create post page',
    'admin/posts.php' => 'Posts management',
    'admin/logout.php' => 'Logout handler'
];

foreach ($admin_files as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $file ($description)</div>";
    } else {
        echo "<div class='error'>❌ Missing: $file ($description)</div>";
    }
}
echo "</div>";

// Test 2: Check database connection
echo "<div class='section'>";
echo "<h3>🔌 Database Connection Test</h3>";

try {
    require_once 'db.php';
    echo "<div class='success'>✅ Database connection successful</div>";
    
    // Test categories table
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'");
    $category_count = $stmt->fetchColumn();
    echo "<div class='success'>✅ Found $category_count active categories</div>";
    
    // Test states
    $stmt = $pdo->query("SELECT COUNT(*) FROM states");
    $state_count = $stmt->fetchColumn();
    echo "<div class='success'>✅ Found $state_count states in database</div>";
    
    // Test users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $admin_count = $stmt->fetchColumn();
    echo "<div class='success'>✅ Found $admin_count admin users</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='warning'>⚠️ Fix database connection before testing admin pages</div>";
}
echo "</div>";

// Test 3: Check session status
echo "<div class='section'>";
echo "<h3>👤 Session Status</h3>";

if (isset($_SESSION['user_id'])) {
    echo "<div class='info'>📝 User ID: " . $_SESSION['user_id'] . "</div>";
    echo "<div class='info'>📝 Role: " . ($_SESSION['role'] ?? 'Not set') . "</div>";
    echo "<div class='info'>📝 Username: " . ($_SESSION['username'] ?? 'Not set') . "</div>";
    
    if ($_SESSION['role'] === 'admin') {
        echo "<div class='success'>✅ Logged in as admin</div>";
    } else {
        echo "<div class='warning'>⚠️ Not logged in as admin</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Not logged in</div>";
}
echo "</div>";

// Test 4: Direct file access tests
echo "<div class='section'>";
echo "<h3>🔗 Direct Page Access Tests</h3>";

$current_domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$current_path = dirname($_SERVER['REQUEST_URI']);
$base_url = $current_domain . $current_path;

echo "<div class='info'>📍 <strong>Test these URLs:</strong></div>";

$test_urls = [
    'Admin Login' => $base_url . '/admin/',
    'Admin Dashboard' => $base_url . '/admin/dashboard.php',
    'Create Post' => $base_url . '/admin/add-post.php',
    'View Posts' => $base_url . '/admin/posts.php'
];

foreach ($test_urls as $name => $url) {
    echo "<div class='test-link'>";
    echo "<strong>$name:</strong> <a href='$url' target='_blank'>$url</a>";
    echo "</div>";
}
echo "</div>";

// Test 5: Error checking
echo "<div class='section'>";
echo "<h3>🐛 Common Issues & Solutions</h3>";

echo "<div class='warning'>📋 <strong>If add-post.php is not opening, check:</strong></div>";
echo "<ol>";
echo "<li><strong>Authentication:</strong> Are you logged in as admin?</li>";
echo "<li><strong>File permissions:</strong> Can the web server read the PHP files?</li>";
echo "<li><strong>PHP errors:</strong> Check error logs for PHP syntax errors</li>";
echo "<li><strong>Database connection:</strong> Is the database working properly?</li>";
echo "<li><strong>Session issues:</strong> Try clearing cookies and logging in again</li>";
echo "</ol>";

echo "<div class='info'>💡 <strong>Troubleshooting steps:</strong></div>";
echo "<ol>";
echo "<li>First try: <a href='" . $base_url . "/admin/' target='_blank'>Login to admin panel</a></li>";
echo "<li>Use credentials: <code>projectadmin / ProjectDemo2024!</code> or <code>demoAdmin / DemoAdmin2024!</code></li>";
echo "<li>After login, try: <a href='" . $base_url . "/admin/add-post.php' target='_blank'>Create Post page</a></li>";
echo "<li>If still not working, check browser console for JavaScript errors</li>";
echo "</ol>";
echo "</div>";

// Test 6: Quick login form (if not logged in)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='section'>";
    echo "<h3>🚪 Quick Login Test</h3>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            $stmt = $pdo->prepare("SELECT id, name, username, password, role FROM users WHERE username = ? AND role = 'admin' AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                echo "<div class='success'>✅ Login successful! <a href='" . $base_url . "/admin/add-post.php'>Go to Create Post</a></div>";
            } else {
                echo "<div class='error'>❌ Invalid credentials</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Login error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<form method='post' style='margin-top: 15px;'>";
    echo "<div>Username: <input type='text' name='username' value='projectadmin' style='margin-left: 10px;'></div>";
    echo "<div style='margin-top: 10px;'>Password: <input type='password' name='password' value='ProjectDemo2024!' style='margin-left: 10px;'></div>";
    echo "<div style='margin-top: 10px;'><button type='submit' name='quick_login' style='padding: 5px 15px;'>Test Login</button></div>";
    echo "</form>";
    echo "</div>";
}

// Test 7: PHP Error Detection
echo "<div class='section'>";
echo "<h3>⚠️ PHP Error Detection</h3>";

echo "<div class='info'>🔍 Testing admin file syntax...</div>";

$test_files = ['admin/add-post.php', 'admin/auth.php', 'admin/dashboard.php'];

foreach ($test_files as $file) {
    if (file_exists($file)) {
        $output = [];
        $return_var = 0;
        exec("php -l $file 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "<div class='success'>✅ $file syntax OK</div>";
        } else {
            echo "<div class='error'>❌ $file has syntax errors:</div>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    }
}
echo "</div>";

echo "<hr>";
echo "<div class='info'>💡 <strong>Next Steps:</strong></div>";
echo "<ul>";
echo "<li>If database connection failed, fix db.php credentials first</li>";
echo "<li>If not logged in, use the login form above or go to admin panel</li>";  
echo "<li>If logged in but pages don't work, check file permissions</li>";
echo "<li>If syntax errors found, fix the PHP files</li>";
echo "</ul>";
?>