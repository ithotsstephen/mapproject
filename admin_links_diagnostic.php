<?php
// Admin Links Diagnostic Tool
// This script checks all admin pages for broken internal links and missing files

$admin_dir = __DIR__ . '/admin/';
$missing_files = [];
$broken_links = [];

echo "<h2>Admin Links Diagnostic Report</h2>";
echo "<p>Checking admin directory: " . $admin_dir . "</p>";

// List of files that should exist based on navigation
$expected_files = [
    'dashboard.php',
    'posts.php', 
    'add-post.php',
    'profile.php', // This appears to be missing
    'logout.php',
    'index.php',
    'auth.php'
];

echo "<h3>1. File Existence Check</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File</th><th>Status</th></tr>";

foreach ($expected_files as $file) {
    $file_path = $admin_dir . $file;
    $exists = file_exists($file_path);
    
    echo "<tr>";
    echo "<td>" . $file . "</td>";
    echo "<td style='color: " . ($exists ? 'green' : 'red') . ";'>";
    echo $exists ? "✓ EXISTS" : "✗ MISSING";
    echo "</td>";
    echo "</tr>";
    
    if (!$exists) {
        $missing_files[] = $file;
    }
}
echo "</table>";

echo "<h3>2. Navigation Links Analysis</h3>";

// Check dashboard.php for navigation links
$dashboard_file = $admin_dir . 'dashboard.php';
if (file_exists($dashboard_file)) {
    $dashboard_content = file_get_contents($dashboard_file);
    
    // Extract all href links
    preg_match_all('/href=["\']([^"\']+)["\']/', $dashboard_content, $matches);
    $links = $matches[1];
    
    echo "<h4>Links found in dashboard.php:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Link</th><th>Type</th><th>Status</th></tr>";
    
    foreach ($links as $link) {
        $link_type = 'External';
        $status = 'Unknown';
        
        // Skip external links
        if (strpos($link, 'http') === 0 || strpos($link, '//') === 0) {
            $link_type = 'External';
            $status = 'External (not checked)';
        } 
        // Check internal admin links
        else if (strpos($link, '../') === 0) {
            $link_type = 'Parent Directory';
            $actual_path = realpath(dirname($dashboard_file) . '/' . $link);
            $status = ($actual_path && file_exists($actual_path)) ? '✓ Valid' : '✗ Broken';
        }
        // Check current directory links
        else if (!strpos($link, '/')) {
            $link_type = 'Admin Page';
            $actual_path = $admin_dir . $link;
            $status = file_exists($actual_path) ? '✓ Valid' : '✗ Broken';
            
            if (!file_exists($actual_path)) {
                $broken_links[] = $link;
            }
        }
        // Check anchor links
        else if (strpos($link, '#') === 0) {
            $link_type = 'Anchor';
            $status = 'Anchor (not checked)';
        }
        else {
            $link_type = 'Other';
            $status = 'Needs manual check';
        }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($link) . "</td>";
        echo "<td>" . $link_type . "</td>";
        echo "<td style='color: " . (strpos($status, '✗') !== false ? 'red' : (strpos($status, '✓') !== false ? 'green' : 'orange')) . ";'>";
        echo $status;
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>dashboard.php not found!</p>";
}

echo "<h3>3. Summary</h3>";

if (empty($missing_files) && empty($broken_links)) {
    echo "<p style='color: green; font-weight: bold;'>✓ All admin links appear to be working correctly!</p>";
} else {
    echo "<h4 style='color: red;'>Issues Found:</h4>";
    
    if (!empty($missing_files)) {
        echo "<p><strong>Missing Files:</strong></p>";
        echo "<ul>";
        foreach ($missing_files as $file) {
            echo "<li style='color: red;'>" . $file . "</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($broken_links)) {
        echo "<p><strong>Broken Links:</strong></p>";
        echo "<ul>";
        foreach ($broken_links as $link) {
            echo "<li style='color: red;'>" . $link . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h4>Recommended Actions:</h4>";
    echo "<ol>";
    if (in_array('profile.php', $missing_files)) {
        echo "<li>Create admin/profile.php page for admin profile management</li>";
    }
    echo "<li>Update navigation menus to remove references to missing pages</li>";
    echo "<li>Test all remaining links after fixes</li>";
    echo "</ol>";
}

// Check if we're running from browser or command line
if (isset($_SERVER['HTTP_HOST'])) {
    echo "<hr><p><small>Run this diagnostic at: " . $_SERVER['REQUEST_URI'] . "</small></p>";
} else {
    echo "\nDiagnostic complete.\n";
}
?>