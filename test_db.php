<?php
include 'includes/db.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    $pdo->query("SELECT 1");
    echo "<p>✅ Database connection successful</p>";
    
    // Test posts table
    $stmt = $pdo->query("DESCRIBE posts");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✅ Posts table exists with columns: " . implode(', ', $columns) . "</p>";
    
    // Test inserting a post
    $test_title = "Test Post " . date('Y-m-d H:i:s');
    $test_content = "This is a test post content.";
    $test_user_id = 1; // Assuming user ID 1 exists
    
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, status) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$test_user_id, $test_title, $test_content, 'published']);
    
    if ($result) {
        $post_id = $pdo->lastInsertId();
        echo "<p>✅ Successfully inserted test post with ID: $post_id</p>";
        
        // Clean up - delete the test post
        $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$post_id]);
        echo "<p>✅ Test post deleted</p>";
    } else {
        echo "<p>❌ Failed to insert test post</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='posts/create.php'>← Back to Create Post</a></p>";
?> 