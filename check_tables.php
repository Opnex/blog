<?php
include 'includes/db.php';

echo "<h2>Database Tables Check</h2>";

// Check if tables exist
$tables = ['users', 'posts', 'comments', 'post_likes', 'comment_likes', 'notifications'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    $exists = $stmt->fetch();
    echo "<p>Table '$table': " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</p>";
}

// Check posts table structure
echo "<h3>Posts Table Structure:</h3>";
$stmt = $pdo->query("DESCRIBE posts");
$columns = $stmt->fetchAll();
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td>" . $col['Field'] . "</td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . $col['Key'] . "</td>";
    echo "<td>" . $col['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check users table structure
echo "<h3>Users Table Structure:</h3>";
$stmt = $pdo->query("DESCRIBE users");
$columns = $stmt->fetchAll();
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td>" . $col['Field'] . "</td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . $col['Key'] . "</td>";
    echo "<td>" . $col['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if required columns exist
$required_columns = [
    'posts' => ['id', 'title', 'content', 'user_id', 'status', 'created_at', 'views'],
    'users' => ['id', 'username', 'email', 'password', 'is_admin', 'is_active', 'profile_picture', 'bio'],
    'comments' => ['id', 'post_id', 'user_id', 'content', 'parent_id', 'is_approved', 'created_at'],
    'post_likes' => ['id', 'post_id', 'user_id'],
    'comment_likes' => ['id', 'comment_id', 'user_id'],
    'notifications' => ['id', 'user_id', 'type', 'message', 'url', 'is_read', 'created_at']
];

foreach ($required_columns as $table => $columns) {
    echo "<h3>Required columns for $table:</h3>";
    $stmt = $pdo->query("DESCRIBE $table");
    $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($columns as $col) {
        $exists = in_array($col, $existing_columns);
        echo "<p>Column '$col': " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</p>";
    }
}
?> 