<?php
/**
 * Database Connection Test Script
 * Use this to test and fix your database connection issues
 */

echo "<h2>🔧 Database Connection Troubleshooting</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .error{color:red;} .success{color:green;} .warning{color:orange;} .info{color:blue;}</style>";

// Step 1: Check if database configuration exists
echo "<h3>Step 1: Database Configuration Check</h3>";

$config_file = 'db.php';
if (file_exists($config_file)) {
    echo "<div class='success'>✅ db.php file exists</div>";
    
    // Read the configuration
    $content = file_get_contents($config_file);
    
    // Check for placeholder values
    $placeholders = [
        'YourDatabasePassword' => 'Database password needs to be updated',
        'projectsdemo_maproject' => 'Database name/username might be placeholder'
    ];
    
    $has_placeholders = false;
    foreach ($placeholders as $placeholder => $message) {
        if (strpos($content, $placeholder) !== false) {
            echo "<div class='error'>❌ $message</div>";
            $has_placeholders = true;
        }
    }
    
    if (!$has_placeholders) {
        echo "<div class='success'>✅ No obvious placeholders found</div>";
    }
} else {
    echo "<div class='error'>❌ db.php file not found!</div>";
    exit;
}

// Step 2: Test basic PHP-MySQL functionality
echo "<h3>Step 2: PHP MySQL Extension Check</h3>";

if (extension_loaded('pdo')) {
    echo "<div class='success'>✅ PDO extension is loaded</div>";
} else {
    echo "<div class='error'>❌ PDO extension not loaded</div>";
}

if (extension_loaded('pdo_mysql')) {
    echo "<div class='success'>✅ PDO MySQL driver is loaded</div>";
} else {
    echo "<div class='error'>❌ PDO MySQL driver not loaded</div>";
}

// Step 3: Get database configuration from cPanel info
echo "<h3>Step 3: Common Database Configuration Patterns</h3>";
echo "<div class='info'>📋 For GoDaddy/cPanel hosting, your database details are typically:</div>";
echo "<ul>";
echo "<li><strong>Host:</strong> localhost (usually correct)</li>";
echo "<li><strong>Database Name:</strong> cpanelusername_dbname</li>";
echo "<li><strong>Username:</strong> cpanelusername_dbuser</li>";
echo "<li><strong>Password:</strong> The password you set when creating the database</li>";
echo "</ul>";

// Step 4: Test connection with current settings
echo "<h3>Step 4: Connection Test with Current Settings</h3>";

// Temporarily include the config to test
ob_start();
$connection_error = null;
try {
    // Suppress the die() statement temporarily
    $db_content = file_get_contents('db.php');
    $db_content_modified = str_replace('die("Database connection failed. Please try again later.");', 'throw new Exception("Connection failed");', $db_content);
    eval('?>' . $db_content_modified);
    echo "<div class='success'>✅ Database connection successful!</div>";
} catch (Exception $e) {
    $connection_error = $e->getMessage();
    echo "<div class='error'>❌ Database connection failed: " . htmlspecialchars($connection_error) . "</div>";
}
ob_end_clean();

// Step 5: Provide specific fix instructions
echo "<h3>Step 5: How to Fix Database Connection</h3>";

if ($connection_error) {
    echo "<div class='warning'>📝 <strong>TO FIX THIS ISSUE:</strong></div>";
    echo "<ol>";
    echo "<li><strong>Log into your cPanel/hosting control panel</strong></li>";
    echo "<li><strong>Go to 'MySQL Databases' section</strong></li>";
    echo "<li><strong>Note down your exact database details:</strong>";
    echo "<ul>";
    echo "<li>Database name (usually: yourdomain_dbname)</li>";
    echo "<li>Username (usually: yourdomain_username)</li>";  
    echo "<li>Password (the one you set)</li>";
    echo "</ul></li>";
    echo "<li><strong>Update your db.php file with these exact details</strong></li>";
    echo "<li><strong>Make sure your database has been imported with the SQL file</strong></li>";
    echo "</ol>";
    
    echo "<h4>🔧 Template for your db.php:</h4>";
    echo "<pre style='background:#f5f5f5;padding:10px;border:1px solid #ddd;'>";
    echo htmlspecialchars("<?php
// Database configuration - UPDATE THESE VALUES
\$config = [
    'host' => 'localhost',
    'dbname' => 'YOUR_ACTUAL_DATABASE_NAME',     // From cPanel
    'username' => 'YOUR_ACTUAL_USERNAME',        // From cPanel  
    'password' => 'YOUR_ACTUAL_PASSWORD',        // From cPanel
    'charset' => 'utf8mb4',
    // ... rest of the config
];");
    echo "</pre>";
}

// Step 6: Test database tables
echo "<h3>Step 6: Database Structure Check</h3>";

if (!$connection_error && isset($pdo)) {
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            echo "<div class='success'>✅ Database has " . count($tables) . " tables</div>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
            
            // Check if users table has data
            if (in_array('users', $tables)) {
                $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                echo "<div class='success'>✅ Users table has $user_count users</div>";
            }
        } else {
            echo "<div class='warning'>⚠️ Database is empty - you need to import the SQL file</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Cannot check tables: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Cannot check database structure until connection is fixed</div>";
}

echo "<hr>";
echo "<div class='info'>💡 <strong>Next Steps:</strong></div>";
echo "<ol>";
echo "<li>Fix the database connection by updating db.php with correct credentials</li>";
echo "<li>Import the database_updated_credentials.sql file if not done yet</li>";
echo "<li>Test the login with: projectadmin / ProjectDemo2024!</li>";
echo "</ol>";

echo "<div class='info'>📞 <strong>Need Help?</strong> Check your hosting provider's documentation for MySQL database setup.</div>";
?>