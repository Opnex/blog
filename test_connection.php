<?php
// Test database connection and includes
echo "<h2>Testing Database Connection</h2>";

try {
    include 'includes/db.php';
    echo "✅ Database connection successful<br>";
    
    // Test if posts table exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
    $count = $stmt->fetchColumn();
    echo "✅ Posts table accessible. Published posts: " . $count . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>Testing Includes</h2>";

// Test header include
try {
    ob_start();
    include 'includes/header.php';
    $header_output = ob_get_clean();
    echo "✅ Header include successful<br>";
} catch (Exception $e) {
    echo "❌ Header include error: " . $e->getMessage() . "<br>";
}

echo "<h2>Testing File Paths</h2>";
echo "Current directory: " . __DIR__ . "<br>";
echo "File exists check: " . (file_exists('includes/header.php') ? '✅' : '❌') . " header.php<br>";
echo "File exists check: " . (file_exists('includes/db.php') ? '✅' : '❌') . " db.php<br>";

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "<br>";
?> 