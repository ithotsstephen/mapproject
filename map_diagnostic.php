<?php
session_start();
require_once 'db.php';

echo "<h2>🗺️ SVG Map Diagnostic</h2>";
echo "<style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .code-block { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin: 10px 0; }
    .test-map { border: 2px solid #333; margin: 10px 0; padding: 10px; }
</style>";

// Test 1: Check if CSS file exists and is accessible
echo "<h3>📋 Step 1: CSS File Check</h3>";
$css_file = 'css/map-style.css';
if (file_exists($css_file)) {
    echo "<div class='success'>✅ CSS file exists: $css_file</div>";
} else {
    echo "<div class='error'>❌ CSS file missing: $css_file</div>";
}

// Test 2: Check database connection and function
echo "<h3>🔌 Step 2: Database & Function Check</h3>";
try {
    if (function_exists('get_post_counts_by_state')) {
        echo "<div class='success'>✅ get_post_counts_by_state function exists</div>";
        
        $post_counts = get_post_counts_by_state($pdo);
        echo "<div class='success'>✅ Function executed successfully</div>";
        echo "<div class='info'>📊 Found " . count($post_counts) . " states with data</div>";
        
        if (!empty($post_counts)) {
            echo "<ul>";
            foreach ($post_counts as $count) {
                echo "<li>{$count['state']}: {$count['count']} posts</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<div class='error'>❌ get_post_counts_by_state function not found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Test 3: Create a simple test SVG
echo "<h3>🎨 Step 3: SVG Test</h3>";
echo "<div class='test-map'>";
echo "<h4>Simple SVG Test (Should show a blue rectangle):</h4>";
echo '<svg width="200" height="100" style="border: 1px solid black;">
    <rect width="190" height="90" x="5" y="5" fill="lightblue" stroke="blue"/>
    <text x="100" y="55" text-anchor="middle" fill="darkblue">SVG Working</text>
</svg>';
echo "</div>";

// Test 4: Check if JavaScript files exist
echo "<h3>📱 Step 4: JavaScript Files Check</h3>";
$js_files = [
    'js/jquery.min.js' => 'jQuery library',
    'js/map-config.js' => 'Map configuration',
    'js/map-interact.js' => 'Map interactions'
];

foreach ($js_files as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $file ($description) exists</div>";
    } else {
        echo "<div class='error'>❌ $file ($description) missing</div>";
    }
}

// Test 5: Create a working India map SVG
echo "<h3>🇮🇳 Step 5: India Map SVG Test</h3>";
echo "<div class='test-map'>";
echo "<h4>Complete India Map SVG:</h4>";
?>

<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000" style="width: 100%; max-width: 600px; height: auto; border: 1px solid #ccc;">
    <style>
        .state-test { 
            fill: #e3f2fd; 
            stroke: #1976d2; 
            stroke-width: 1; 
            cursor: pointer;
            transition: fill 0.3s ease;
        }
        .state-test:hover { 
            fill: #bbdefb; 
        }
    </style>
    
    <!-- Simplified India Map for Testing -->
    <g>
        <!-- Jammu & Kashmir -->
        <path class="state-test" data-state="Jammu and Kashmir" 
              d="M223,101L221,102L219,100L217,101L215,101L213,103L210,105L207,106L203,109L201,108L199,108L199,109L202,111L202,112L202,114L202,115L201,117L199,119L198,120L197,121L192,123L191,125L190,124L190,124L189,124L187,124L184,122L183,121L182,121L180,120L179,119L178,120L174,119L170,119L169,116L170,115L169,113L170,110L169,111L168,112L166,113L164,113L161,113L159,112L156,111L154,109L151,109L149,107L145,107L144,105L143,104L141,103L142,100L140,99L140,95L141,94L140,91L141,89L141,88L139,86L139,85L141,84L140,80L138,79L138,76L138,73L136,71L137,68L137,67L138,65L141,65L143,64L143,62L145,59L147,59L151,59L152,58L153,55L155,53L155,52L156,52L157,53L158,53L159,53L159,54L160,54L160,54L161,54L162,54L163,53L164,53L165,52L165,51L166,52L167,52L167,53L167,54L168,55L168,56L168,57L169,57L169,57L170,57L171,57L171,58L172,58L173,59L174,60L176,60L177,61L177,61L177,60L178,59L179,59L179,59L180,59L180,58L181,58L181,58L182,59L183,60L184,60L184,61L184,62L184,64L184,65L185,65L187,66L187,67L187,68L186,68L186,69L187,69L188,69L188,70L188,71L188,72L188,72L189,73L190,74L191,74L194,75L195,75L195,76L195,77L196,77L197,78L198,79L198,79L198,80L199,81L200,82L200,82L201,82L202,82L204,82L205,82L206,83L207,84L208,84L208,85L209,86L209,87L210,87L211,88L211,89L210,89L210,90L209,91L209,91L209,92L210,92L211,92L212,92L212,91L212,91L212,90L213,91L214,92L215,93L216,93L218,94L219,95L219,95L220,96L220,97L221,98L222,98L223,99L224,100Z"/>
        
        <!-- Delhi -->
        <circle class="state-test" data-state="Delhi" cx="235" cy="214" r="8" />
        
        <!-- Maharashtra -->
        <path class="state-test" data-state="Maharashtra" 
              d="M120,350 L200,350 L220,400 L200,500 L150,480 L100,450 Z"/>
        
        <!-- Gujarat -->
        <path class="state-test" data-state="Gujarat" 
              d="M50,280 L120,280 L140,350 L100,400 L60,380 L30,340 Z"/>
        
        <!-- Rajasthan -->
        <path class="state-test" data-state="Rajasthan" 
              d="M100,180 L200,180 L220,280 L180,320 L120,300 L80,250 Z"/>
        
        <!-- Uttar Pradesh -->
        <path class="state-test" data-state="Uttar Pradesh" 
              d="M200,200 L350,200 L380,280 L350,320 L250,300 L220,250 Z"/>
        
        <!-- Karnataka -->
        <path class="state-test" data-state="Karnataka" 
              d="M180,550 L280,550 L300,650 L250,700 L200,680 L150,620 Z"/>
        
        <!-- Tamil Nadu -->
        <path class="state-test" data-state="Tamil Nadu" 
              d="M220,650 L320,650 L340,780 L280,820 L240,800 L200,720 Z"/>
        
        <!-- Kerala -->
        <path class="state-test" data-state="Kerala" 
              d="M180,700 L220,700 L230,820 L200,850 L170,830 L160,750 Z"/>
        
        <!-- West Bengal -->
        <path class="state-test" data-state="West Bengal" 
              d="M400,350 L480,350 L500,450 L470,480 L420,460 L380,400 Z"/>
        
        <!-- Bihar -->
        <path class="state-test" data-state="Bihar" 
              d="M380,280 L450,280 L470,330 L440,360 L400,340 L360,300 Z"/>
    </g>
    
    <!-- Add a title -->
    <text x="400" y="30" text-anchor="middle" font-size="20" fill="#333">India Map Test</text>
</svg>

<?php
echo "</div>";

// Test 6: Check for JavaScript errors
echo "<h3>💻 Step 6: JavaScript Console Test</h3>";
echo "<div class='info'>📝 Check browser console for JavaScript errors:</div>";
echo "<ol>";
echo "<li>Open browser Developer Tools (F12)</li>";
echo "<li>Go to Console tab</li>";
echo "<li>Look for any red error messages</li>";
echo "<li>Check if jQuery is loaded</li>";
echo "</ol>";

echo "<script>
console.log('=== MAP DIAGNOSTIC TESTS ===');
console.log('jQuery loaded:', typeof jQuery !== 'undefined');
console.log('Document ready:', document.readyState);

// Test SVG interaction
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    var svgElements = document.querySelectorAll('.state-test');
    console.log('Found', svgElements.length, 'SVG state elements');
    
    svgElements.forEach(function(element, index) {
        element.addEventListener('click', function() {
            var state = this.getAttribute('data-state');
            console.log('Clicked state:', state);
            alert('Clicked: ' + state);
        });
    });
});
</script>";

// Test 7: Provide fixes
echo "<h3>🔧 Step 7: Possible Fixes</h3>";
echo "<div class='info'>📋 <strong>Common Issues & Solutions:</strong></div>";
echo "<ol>";
echo "<li><strong>SVG paths incomplete:</strong> The SVG paths in index.php appear to be truncated</li>";
echo "<li><strong>CSS not loading:</strong> Check if css/map-style.css is uploaded and accessible</li>";
echo "<li><strong>JavaScript errors:</strong> Check browser console for errors</li>";
echo "<li><strong>Database issues:</strong> Ensure database is properly connected</li>";
echo "</ol>";

echo "<div class='warning'>⚠️ <strong>Next Steps:</strong></div>";
echo "<ul>";
echo "<li>If the test SVG above shows properly, the issue is with the India map SVG paths</li>";
echo "<li>If nothing shows, check CSS and JavaScript files</li>";
echo "<li>If database connection failed, fix database credentials first</li>";
echo "</ul>";
?>