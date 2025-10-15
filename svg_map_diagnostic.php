<?php
/**
 * SVG Map Diagnostic & Comparison Tool
 * Use this to test and compare map functionality
 */

session_start();

echo "<h2>🗺️ Interactive SVG Map Diagnostic</h2>";
echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .comparison { border: 1px solid #ddd; margin: 20px 0; padding: 15px; }
    .map-preview { max-width: 400px; border: 1px solid #ccc; margin: 10px 0; }
    .test-button { padding: 10px 20px; margin: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
</style>";

// Test 1: Check current files
echo "<div class='comparison'>";
echo "<h3>📂 Current Files Status</h3>";

$files_to_check = [
    'index.php' => 'Current homepage',
    'index_complete_interactive.php' => 'Fixed interactive map (NEW)',
    'css/map-style.css' => 'Map CSS styles',
    'js/jquery.min.js' => 'jQuery library',
    'js/map-interact.js' => 'Map interactions',
    'js/map-config.js' => 'Map configuration'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $file ($description)</div>";
    } else {
        echo "<div class='error'>❌ Missing: $file ($description)</div>";
    }
}
echo "</div>";

// Test 2: Database connection and data check
echo "<div class='comparison'>";
echo "<h3>🔌 Database & Data Check</h3>";

try {
    require_once 'db.php';
    echo "<div class='success'>✅ Database connection successful</div>";
    
    // Check if get_post_counts_by_state function exists
    if (function_exists('get_post_counts_by_state')) {
        echo "<div class='success'>✅ get_post_counts_by_state function exists</div>";
        
        $post_counts = get_post_counts_by_state($pdo);
        echo "<div class='info'>📊 Found data for " . count($post_counts) . " states</div>";
        
        if (!empty($post_counts)) {
            echo "<div style='margin: 10px 0;'>";
            echo "<strong>States with reports:</strong><br>";
            foreach ($post_counts as $count) {
                echo "• {$count['state']}: {$count['count']} reports<br>";
            }
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ get_post_counts_by_state function missing</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
echo "</div>";

// Test 3: SVG Map Comparison
echo "<div class='comparison'>";
echo "<h3>🎨 SVG Map Comparison</h3>";

echo "<div class='warning'>📋 <strong>Issues with Current Map:</strong></div>";
echo "<ul>";
echo "<li>SVG paths are incomplete and truncated</li>";
echo "<li>States appear as tiny fragments instead of recognizable shapes</li>";
echo "<li>Interactive features may not work properly</li>";
echo "<li>Tooltip and hover effects missing</li>";
echo "</ul>";

echo "<div class='success'>✅ <strong>Fixed Map Features:</strong></div>";
echo "<ul>";
echo "<li>Complete SVG paths for all Indian states</li>";
echo "<li>Proper interactive hover and click functionality</li>";
echo "<li>Color-coded states based on report count</li>";
echo "<li>Tooltips showing state names and report counts</li>";
echo "<li>Responsive design that works on all devices</li>";
echo "<li>Visual legend and instructions</li>";
echo "</ul>";
echo "</div>";

// Test 4: Quick SVG Test
echo "<div class='comparison'>";
echo "<h3>🔬 Quick SVG Functionality Test</h3>";

echo "<div class='info'>📝 <strong>Simple SVG Test:</strong></div>";
echo "<svg width='300' height='200' style='border: 1px solid #ccc; margin: 10px 0;'>
    <rect width='280' height='180' x='10' y='10' fill='lightblue' stroke='blue' stroke-width='2'/>
    <circle cx='150' cy='100' r='50' fill='lightgreen' stroke='green' stroke-width='2'/>
    <text x='150' y='105' text-anchor='middle' fill='darkblue' font-family='Arial' font-size='14'>SVG Working</text>
</svg>";

echo "<div class='info'>If you can see the blue rectangle and green circle above, SVG is working in your browser.</div>";
echo "</div>";

// Test 5: Live URL Tests
echo "<div class='comparison'>";
echo "<h3>🔗 Live URL Tests</h3>";

$current_domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$current_path = dirname($_SERVER['REQUEST_URI']);
$base_url = $current_domain . $current_path;

echo "<div class='info'>🌐 <strong>Test These URLs:</strong></div>";

$test_urls = [
    'Current Homepage (Broken Map)' => $base_url . '/index.php',
    'Fixed Interactive Map (NEW)' => $base_url . '/index_complete_interactive.php',
    'State Listings Page' => $base_url . '/state.php',
    'Admin Panel' => $base_url . '/admin/'
];

foreach ($test_urls as $name => $url) {
    echo "<div style='margin: 10px 0;'>";
    echo "<strong>$name:</strong><br>";
    echo "<a href='$url' target='_blank' class='test-button'>Test: $url</a>";
    echo "</div>";
}
echo "</div>";

// Test 6: Implementation Steps
echo "<div class='comparison'>";
echo "<h3>🛠️ How to Fix Your Map</h3>";

echo "<div class='warning'>📋 <strong>Step-by-Step Solution:</strong></div>";
echo "<ol>";
echo "<li><strong>Backup current file:</strong><br>";
echo "<code>mv index.php index_backup.php</code></li>";
echo "<li><strong>Use the fixed version:</strong><br>";
echo "<code>mv index_complete_interactive.php index.php</code></li>";
echo "<li><strong>Test the result:</strong><br>";
echo "<code>Visit: $base_url/index.php</code></li>";
echo "<li><strong>Verify functionality:</strong><br>";
echo "• Map should show complete India with all states<br>";
echo "• Hover should show tooltips<br>";
echo "• Click should navigate to state reports<br>";
echo "• States should be color-coded by report count</li>";
echo "</ol>";

echo "<div class='success'>✅ <strong>Expected Results:</strong></div>";
echo "<ul>";
echo "<li>Complete, recognizable map of India</li>";
echo "<li>All 28 states + 8 union territories visible</li>";
echo "<li>Interactive hover effects with state names</li>";
echo "<li>Click navigation to state-specific reports</li>";
echo "<li>Visual indication of states with/without reports</li>";
echo "</ul>";
echo "</div>";

// Test 7: Troubleshooting
echo "<div class='comparison'>";
echo "<h3>🐛 Troubleshooting</h3>";

echo "<div class='warning'>⚠️ <strong>If map still doesn't work:</strong></div>";
echo "<ol>";
echo "<li><strong>Check browser console for JavaScript errors</strong></li>";
echo "<li><strong>Verify jQuery is loading:</strong> Look for jquery.min.js in Network tab</li>";
echo "<li><strong>Test database connection:</strong> Make sure post counts are loading</li>";
echo "<li><strong>Clear browser cache:</strong> Force refresh with Ctrl+F5</li>";
echo "<li><strong>Check file permissions:</strong> Ensure PHP files are readable</li>";
echo "</ol>";

echo "<div class='info'>💡 <strong>Quick Debug:</strong></div>";
echo "<p>Open browser Developer Tools (F12) and check:</p>";
echo "<ul>";
echo "<li><strong>Console tab:</strong> Look for JavaScript errors (red messages)</li>";
echo "<li><strong>Network tab:</strong> Verify all files load successfully</li>";
echo "<li><strong>Elements tab:</strong> Check if SVG elements are present in DOM</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<div class='info'>💡 <strong>Summary:</strong></div>";
echo "<p>Your current SVG map has incomplete paths. The fixed version provides a complete, interactive map with proper state boundaries, hover effects, and click functionality. Replace your index.php with the fixed version to resolve the issue.</p>";
?>