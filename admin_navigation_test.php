<?php
// Admin Navigation Test Script
// This script tests all internal navigation links in the admin panel

echo "<h2>Admin Navigation Link Test</h2>";
echo "<p>Testing all internal links in the admin panel...</p>";

$admin_dir = __DIR__ . '/admin/';
$base_url = 'http://projectsdemo.link/mapproject/admin/'; // Update this to your actual URL

// List of all admin pages
$admin_pages = [
    'dashboard.php',
    'posts.php', 
    'add-post.php',
    'profile.php',
    'logout.php',
    'index.php',
    'auth.php'
];

// List of expected internal navigation links
$expected_links = [
    'dashboard.php' => 'Admin Dashboard',
    'posts.php' => 'My Posts',
    'add-post.php' => 'Add New Post', 
    'profile.php' => 'Profile',
    '../index.php' => 'Main Website'
];

echo "<h3>1. File Existence Check</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File</th><th>Status</th><th>Size</th><th>Last Modified</th></tr>";

$all_files_exist = true;
foreach ($admin_pages as $page) {
    $file_path = $admin_dir . $page;
    $exists = file_exists($file_path);
    
    echo "<tr>";
    echo "<td>" . $page . "</td>";
    echo "<td style='color: " . ($exists ? 'green' : 'red') . ";'>";
    echo $exists ? "✓ EXISTS" : "✗ MISSING";
    echo "</td>";
    
    if ($exists) {
        $size = filesize($file_path);
        $modified = date('Y-m-d H:i:s', filemtime($file_path));
        echo "<td>" . number_format($size) . " bytes</td>";
        echo "<td>" . $modified . "</td>";
    } else {
        echo "<td colspan='2'>N/A</td>";
        $all_files_exist = false;
    }
    echo "</tr>";
}
echo "</table>";

echo "<h3>2. Navigation Consistency Check</h3>";

$navigation_files = ['dashboard.php', 'posts.php', 'add-post.php', 'profile.php'];
$nav_issues = [];

foreach ($navigation_files as $file) {
    $file_path = $admin_dir . $file;
    if (!file_exists($file_path)) {
        $nav_issues[] = "$file: File does not exist";
        continue;
    }
    
    $content = file_get_contents($file_path);
    
    // Check for required navigation elements
    $required_elements = [
        'navbar' => 'Navigation bar',
        'dashboard.php' => 'Dashboard link',
        'posts.php' => 'Posts link', 
        'add-post.php' => 'Add Post link',
        'profile.php' => 'Profile link'
    ];
    
    echo "<h4>$file Navigation Elements:</h4>";
    echo "<ul>";
    
    foreach ($required_elements as $element => $description) {
        $found = strpos($content, $element) !== false;
        echo "<li style='color: " . ($found ? 'green' : 'red') . ";'>";
        echo ($found ? "✓" : "✗") . " $description";
        echo "</li>";
        
        if (!$found && $element !== $file) { // Don't flag missing self-reference
            $nav_issues[] = "$file: Missing $description";
        }
    }
    echo "</ul>";
}

echo "<h3>3. Link Test Summary</h3>";

if ($all_files_exist && empty($nav_issues)) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h4>✓ All Tests Passed!</h4>";
    echo "<p>All admin files exist and navigation elements are present.</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h4>Issues Found:</h4>";
    
    if (!$all_files_exist) {
        echo "<p><strong>Missing Files:</strong> Some admin files are missing.</p>";
    }
    
    if (!empty($nav_issues)) {
        echo "<p><strong>Navigation Issues:</strong></p>";
        echo "<ul>";
        foreach ($nav_issues as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
}

echo "<h3>4. Quick Test Links</h3>";
echo "<p>Use these links to manually test navigation (update URL if needed):</p>";
echo "<ul>";
foreach ($expected_links as $link => $title) {
    if (strpos($link, '../') === 0) {
        $test_url = str_replace('../', 'http://projectsdemo.link/mapproject/', $link);
    } else {
        $test_url = $base_url . $link;
    }
    echo "<li><a href='$test_url' target='_blank'>$title ($link)</a></li>";
}
echo "</ul>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Upload this diagnostic to your server</li>";
echo "<li>Run it in your browser</li>";
echo "<li>Test each link manually</li>";
echo "<li>Check for any authentication or permission issues</li>";
echo "</ol>";

echo "<p><small>Generated: " . date('Y-m-d H:i:s') . "</small></p>";
?>