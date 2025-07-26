<?php
require_once '../includes/auth.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}
include '../includes/db.php';
$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
if (!$post_id) {
    echo json_encode(['success' => false, 'error' => 'No post ID']);
    exit;
}
// Check if already liked
$stmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
$stmt->execute([$post_id, $user_id]);
if ($stmt->fetch()) {
    // Unlike
    $pdo->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?')->execute([$post_id, $user_id]);
    $liked = false;
} else {
    // Like
    $pdo->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)')->execute([$post_id, $user_id]);
    $liked = true;
    // Notify post owner if not self
    $stmtOwner = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
    $stmtOwner->execute([$post_id]);
    $owner = $stmtOwner->fetchColumn();
    if ($owner && $owner != $user_id) {
        $msg = 'Someone liked your post.';
        $url = 'posts/view.php?id=' . $post_id;
        $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)')->execute([$owner, 'like', $msg, $url]);
    }
}
// Get new like count
$stmt = $pdo->prepare('SELECT COUNT(*) FROM post_likes WHERE post_id = ?');
$stmt->execute([$post_id]);
$count = $stmt->fetchColumn();
echo json_encode(['success' => true, 'liked' => $liked, 'count' => $count]); 