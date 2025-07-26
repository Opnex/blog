<?php
require_once '../includes/auth.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}
include '../includes/db.php';
$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'] ?? null;
if (!$comment_id) {
    echo json_encode(['success' => false, 'error' => 'No comment ID']);
    exit;
}
// Check if already liked
$stmt = $pdo->prepare('SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?');
$stmt->execute([$comment_id, $user_id]);
if ($stmt->fetch()) {
    // Unlike
    $pdo->prepare('DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?')->execute([$comment_id, $user_id]);
    $liked = false;
} else {
    // Like
    $pdo->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)')->execute([$comment_id, $user_id]);
    $liked = true;
    // Notify comment owner if not self
    $stmtOwner = $pdo->prepare('SELECT user_id, post_id FROM comments WHERE id = ?');
    $stmtOwner->execute([$comment_id]);
    $row = $stmtOwner->fetch();
    if ($row && $row['user_id'] != $user_id) {
        $msg = 'Someone liked your comment.';
        $url = 'posts/view.php?id=' . $row['post_id'] . '#comment-' . $comment_id;
        $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)')->execute([$row['user_id'], 'like', $msg, $url]);
    }
}
// Get new like count
$stmt = $pdo->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?');
$stmt->execute([$comment_id]);
$count = $stmt->fetchColumn();
echo json_encode(['success' => true, 'liked' => $liked, 'count' => $count]); 