<?php
include 'includes/db.php';

echo "<h2>Publishing Draft Posts</h2>";

// Update all draft posts to published
$stmt = $pdo->prepare("UPDATE posts SET status = 'published' WHERE status = 'draft'");
$result = $stmt->execute();

if ($result) {
    $affected = $stmt->rowCount();
    echo "<p>✅ Successfully published $affected draft posts!</p>";
} else {
    echo "<p>❌ Error publishing posts</p>";
}

// Show all posts after update
echo "<h3>All Posts After Update:</h3>";
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Status</th><th>Created</th><th>Views</th></tr>";

foreach ($posts as $post) {
    echo "<tr>";
    echo "<td>" . $post['id'] . "</td>";
    echo "<td>" . htmlspecialchars($post['title']) . "</td>";
    echo "<td>" . htmlspecialchars($post['username']) . "</td>";
    echo "<td>" . htmlspecialchars($post['status']) . "</td>";
    echo "<td>" . $post['created_at'] . "</td>";
    echo "<td>" . ($post['views'] ?? 0) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='index.php'>← Go to Homepage</a></p>";
?> 