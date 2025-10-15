<?php
/**
 * Quick Admin Login Test
 * Use this to test admin authentication
 */

session_start();

echo "<h2>🚪 Admin Login Test</h2>";
echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .form-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    input[type='text'], input[type='password'] { margin: 5px; padding: 8px; width: 200px; }
    button { padding: 10px 20px; margin: 10px 5px; }
</style>";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        require_once 'db.php';
        
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($username) || empty($password)) {
            echo "<div class='error'>❌ Please enter both username and password</div>";
        } else {
            $stmt = $pdo->prepare("SELECT id, name, username, password, role, status FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                echo "<div class='success'>✅ <strong>Login Successful!</strong></div>";
                echo "<div class='info'>👤 Logged in as: {$user['name']} ({$user['username']})</div>";
                echo "<div class='info'>🔑 Role: {$user['role']}</div>";
                
                // Test admin access
                if (in_array($user['role'], ['admin', 'super_admin'])) {
                    echo "<div class='success'>✅ Admin access granted!</div>";
                    
                    $current_domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
                    $current_path = dirname($_SERVER['REQUEST_URI']);
                    $base_url = $current_domain . $current_path;
                    
                    echo "<div style='margin: 20px 0;'>";
                    echo "<h3>🎯 Test These Admin Pages:</h3>";
                    echo "<ul>";
                    echo "<li><a href='{$base_url}/admin/dashboard.php' target='_blank'>Admin Dashboard</a></li>";
                    echo "<li><a href='{$base_url}/admin/add-post.php' target='_blank'>Create Post (add-post.php)</a></li>";
                    echo "<li><a href='{$base_url}/admin/posts.php' target='_blank'>Manage Posts</a></li>";
                    echo "</ul>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>❌ Not an admin user</div>";
                }
            } else {
                echo "<div class='error'>❌ Invalid username or password</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo "<div class='success'>✅ Logged out successfully</div>";
    echo "<meta http-equiv='refresh' content='2;url=" . $_SERVER['PHP_SELF'] . "'>";
}

// Show current session status
echo "<div class='form-section'>";
echo "<h3>📋 Current Session Status</h3>";

if (isset($_SESSION['user_id'])) {
    echo "<div class='success'>✅ Currently logged in</div>";
    echo "<ul>";
    echo "<li><strong>User ID:</strong> " . $_SESSION['user_id'] . "</li>";
    echo "<li><strong>Username:</strong> " . ($_SESSION['username'] ?? 'Not set') . "</li>";
    echo "<li><strong>Name:</strong> " . ($_SESSION['name'] ?? 'Not set') . "</li>";
    echo "<li><strong>Role:</strong> " . ($_SESSION['role'] ?? 'Not set') . "</li>";
    echo "</ul>";
    
    if (in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'])) {
        echo "<div class='success'>✅ Has admin access</div>";
    } else {
        echo "<div class='warning'>⚠️ No admin access</div>";
    }
    
    echo "<a href='?logout=1'><button>Logout</button></a>";
} else {
    echo "<div class='warning'>⚠️ Not logged in</div>";
}
echo "</div>";

// Show login form if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='form-section'>";
    echo "<h3>🔐 Login Form</h3>";
    echo "<form method='post'>";
    
    echo "<div>";
    echo "<label>Username:</label><br>";
    echo "<input type='text' name='username' value='projectadmin' placeholder='Enter username'>";
    echo "<small style='display: block; color: #666;'>Default super admin: projectadmin</small>";
    echo "</div>";
    
    echo "<div>";
    echo "<label>Password:</label><br>";
    echo "<input type='password' name='password' value='ProjectDemo2024!' placeholder='Enter password'>";
    echo "<small style='display: block; color: #666;'>Default password: ProjectDemo2024!</small>";
    echo "</div>";
    
    echo "<button type='submit' name='login'>Test Login</button>";
    echo "</form>";
    
    echo "<div style='margin-top: 20px;'>";
    echo "<h4>📝 Available Credentials:</h4>";
    echo "<ul>";
    echo "<li><strong>Super Admin:</strong> projectadmin / ProjectDemo2024!</li>";
    echo "<li><strong>Regular Admin:</strong> demoAdmin / DemoAdmin2024!</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
}

// Show fix information
echo "<div class='form-section'>";
echo "<h3>🔧 Recent Fix Applied</h3>";
echo "<div class='info'>📋 <strong>Issue Found & Fixed:</strong></div>";
echo "<p>The admin authentication was only checking for role = 'admin', but your super admin user has role = 'super_admin'.</p>";
echo "<p><strong>Fix applied:</strong> Updated auth.php to accept both 'admin' and 'super_admin' roles.</p>";

echo "<div class='success'>✅ <strong>What was changed:</strong></div>";
echo "<ul>";
echo "<li>check_admin_auth() now accepts both admin and super_admin</li>";
echo "<li>validate_admin_session() now checks for both roles</li>";
echo "<li>This allows both regular admins and super admins to access admin pages</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<div class='info'>💡 <strong>Next Steps:</strong></div>";
echo "<ol>";
echo "<li>Use the login form above to test authentication</li>";
echo "<li>After successful login, click the 'Create Post' link</li>";
echo "<li>If still having issues, check browser console for JavaScript errors</li>";
echo "<li>Verify that all admin files are uploaded to your server</li>";
echo "</ol>";
?>