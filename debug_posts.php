<?php
include 'includes/db.php';

echo "<h2>Database Debug - Posts</h2>";

// Check all posts
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();

echo "<h3>All Posts:</h3>";
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

// Check published posts only
echo "<h3>Published Posts Only:</h3>";
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.status = 'published' ORDER BY posts.created_at DESC");
$stmt->execute();
$published_posts = $stmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Status</th><th>Created</th><th>Views</th></tr>";

foreach ($published_posts as $post) {
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

// Check users
echo "<h3>Users:</h3>";
$stmt = $pdo->query("SELECT * FROM users ORDER BY id");
$users = $stmt->fetchAll();

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Is Admin</th><th>Is Active</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . ($user['is_admin'] ? 'Yes' : 'No') . "</td>";
    echo "<td>" . (($user['is_active'] ?? 1) ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";
?> 