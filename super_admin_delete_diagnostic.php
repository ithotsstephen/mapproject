<?php
/**
 * Super Admin Delete Functionality Diagnostic
 * Use this to test and fix admin deletion issues
 */

session_start();

echo "<h2>🔧 Super Admin Delete Diagnostic</h2>";
echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-form { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style>";

// Test 1: Check session and permissions
echo "<div class='section'>";
echo "<h3>👤 Session & Permissions Check</h3>";

if (isset($_SESSION['user_id'])) {
    echo "<div class='info'>📝 User ID: " . $_SESSION['user_id'] . "</div>";
    echo "<div class='info'>📝 Role: " . ($_SESSION['role'] ?? 'Not set') . "</div>";
    
    if ($_SESSION['role'] === 'super_admin') {
        echo "<div class='success'>✅ Logged in as super admin</div>";
    } else {
        echo "<div class='error'>❌ Not logged in as super admin</div>";
        echo "<div class='warning'>⚠️ You need to be logged in as super admin to delete admins</div>";
    }
} else {
    echo "<div class='error'>❌ Not logged in</div>";
}
echo "</div>";

// Test 2: Check database connection and admin data
echo "<div class='section'>";
echo "<h3>🔌 Database & Admin Data Check</h3>";

try {
    require_once 'db.php';
    echo "<div class='success'>✅ Database connection successful</div>";
    
    // Get all admins
    $stmt = $pdo->query("
        SELECT u.id, u.name, u.username, u.role, u.status, 
               (SELECT COUNT(*) FROM posts WHERE admin_id = u.id) as post_count
        FROM users u 
        WHERE role IN ('admin', 'super_admin') 
        ORDER BY role DESC, created_at DESC
    ");
    $all_users = $stmt->fetchAll();
    
    echo "<div class='success'>✅ Found " . count($all_users) . " admin users</div>";
    
    if (!empty($all_users)) {
        echo "<table style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>ID</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Name</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Username</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Role</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Status</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Posts</th>";
        echo "<th style='border: 1px solid #ccc; padding: 8px;'>Can Delete?</th>";
        echo "</tr>";
        
        foreach ($all_users as $user) {
            $can_delete = ($user['role'] === 'admin' && $user['post_count'] == 0);
            echo "<tr>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['id']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['name']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['username']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['role']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['status']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>{$user['post_count']}</td>";
            echo "<td style='border: 1px solid #ccc; padding: 8px;'>";
            if ($user['role'] === 'super_admin') {
                echo "<span class='warning'>Super Admin (Protected)</span>";
            } elseif ($user['post_count'] > 0) {
                echo "<span class='error'>No (Has {$user['post_count']} posts)</span>";
            } elseif ($can_delete) {
                echo "<span class='success'>Yes</span>";
            } else {
                echo "<span class='error'>No</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
echo "</div>";

// Test 3: Test delete functionality
echo "<div class='section'>";
echo "<h3>🗑️ Delete Functionality Test</h3>";

if (isset($_POST['test_delete'])) {
    $admin_id = intval($_POST['admin_id']);
    
    if ($admin_id <= 0) {
        echo "<div class='error'>❌ Invalid admin ID</div>";
    } else {
        try {
            // Check if admin exists and get info
            $stmt = $pdo->prepare("
                SELECT id, name, username, role,
                       (SELECT COUNT(*) FROM posts WHERE admin_id = ?) as post_count
                FROM users WHERE id = ?
            ");
            $stmt->execute([$admin_id, $admin_id]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                echo "<div class='error'>❌ Admin with ID $admin_id not found</div>";
            } elseif ($admin['role'] === 'super_admin') {
                echo "<div class='error'>❌ Cannot delete super admin users</div>";
            } elseif ($admin['post_count'] > 0) {
                echo "<div class='error'>❌ Cannot delete admin '{$admin['username']}' - has {$admin['post_count']} posts</div>";
            } else {
                // Perform test delete (you can uncomment this to actually delete)
                /*
                $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
                if ($delete_stmt->execute([$admin_id])) {
                    echo "<div class='success'>✅ Admin '{$admin['username']}' deleted successfully</div>";
                } else {
                    echo "<div class='error'>❌ Failed to delete admin</div>";
                }
                */
                
                // For testing, just show what would happen
                echo "<div class='success'>✅ Admin '{$admin['username']}' (ID: {$admin_id}) can be deleted</div>";
                echo "<div class='warning'>⚠️ Delete test not executed (safety check)</div>";
                echo "<div class='info'>💡 To actually delete, uncomment the delete code in this diagnostic</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Delete test error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// Show test form
echo "<div class='test-form'>";
echo "<h4>🧪 Test Delete Function</h4>";
echo "<form method='post'>";

try {
    $stmt = $pdo->query("SELECT id, name, username, role FROM users WHERE role = 'admin' ORDER BY username");
    $admins = $stmt->fetchAll();
    
    if (!empty($admins)) {
        echo "<label>Select Admin to Test Delete:</label><br>";
        echo "<select name='admin_id' style='margin: 10px 0; padding: 5px;'>";
        echo "<option value=''>-- Select Admin --</option>";
        foreach ($admins as $admin) {
            echo "<option value='{$admin['id']}'>{$admin['name']} ({$admin['username']})</option>";
        }
        echo "</select><br>";
        
        echo "<button type='submit' name='test_delete' style='padding: 8px 15px; margin: 10px 0;'>Test Delete</button>";
        echo "<p style='color: #666; font-size: 12px;'>* This will only test the deletion logic, not actually delete</p>";
    } else {
        echo "<p>No admin users found to test with.</p>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error loading admins: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</form>";
echo "</div>";
echo "</div>";

// Test 4: Common issues and solutions
echo "<div class='section'>";
echo "<h3>🔧 Common Issues & Solutions</h3>";

echo "<div class='warning'>📋 <strong>Why Admin Delete Might Not Work:</strong></div>";
echo "<ol>";
echo "<li><strong>Admin has posts:</strong> Can't delete admins who have created posts</li>";
echo "<li><strong>Wrong permissions:</strong> Must be logged in as super_admin</li>";
echo "<li><strong>GET vs POST issue:</strong> Delete links use GET instead of secure POST</li>";
echo "<li><strong>JavaScript confirmation:</strong> User might cancel the confirmation dialog</li>";
echo "<li><strong>Database error:</strong> Connection or query issues</li>";
echo "</ol>";

echo "<div class='success'>✅ <strong>Solutions:</strong></div>";
echo "<ol>";
echo "<li><strong>Check post count:</strong> Only admins with 0 posts can be deleted</li>";
echo "<li><strong>Use deactivate instead:</strong> Set status to 'inactive' for admins with posts</li>";
echo "<li><strong>Implement secure delete:</strong> Use POST request with CSRF token</li>";
echo "<li><strong>Add proper error handling:</strong> Show clear error messages</li>";
echo "</ol>";
echo "</div>";

// Test 5: Improved delete implementation
echo "<div class='section'>";
echo "<h3>🛠️ Recommended Fix</h3>";

echo "<div class='info'>📝 <strong>Current Implementation Issue:</strong></div>";
echo "<p>The delete functionality uses GET requests which can be:</p>";
echo "<ul>";
echo "<li>Less secure (no CSRF protection)</li>";
echo "<li>Accidentally triggered by crawlers or prefetching</li>";
echo "<li>Not following REST conventions</li>";
echo "</ul>";

echo "<div class='success'>✅ <strong>Recommended Solution:</strong></div>";
echo "<p>Use POST requests with proper CSRF tokens and confirmation:</p>";

echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>HTML Form:</strong><br>";
echo "<code>";
echo htmlspecialchars('<form method="POST" style="display: inline;" onsubmit="return confirm(\'Are you sure?\')">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="admin_id" value="<?php echo $admin[\'id\']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <button type="submit" class="btn btn-sm btn-outline-danger">
        <i class="fas fa-trash"></i> Delete
    </button>
</form>');
echo "</code>";
echo "</div>";

echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>PHP Handler:</strong><br>";
echo "<code>";
echo htmlspecialchars('if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "delete") {
    $admin_id = intval($_POST["admin_id"]);
    if (verify_csrf_token($_POST["csrf_token"])) {
        // Perform delete with proper checks
        // ... deletion logic
    }
}');
echo "</code>";
echo "</div>";
echo "</div>";

echo "<hr>";
echo "<div class='info'>💡 <strong>Next Steps:</strong></div>";
echo "<ol>";
echo "<li>Check if you're logged in as super admin</li>";
echo "<li>Verify the admin you're trying to delete has 0 posts</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Test with the diagnostic form above</li>";
echo "<li>Consider implementing the improved POST-based solution</li>";
echo "</ol>";
?>