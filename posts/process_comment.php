<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? $_POST['parent_id'] : null;

    try {
        $is_approved = 1; // Auto-approve all comments like Facebook
        // Get post owner for notifications
        $stmtPost = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
        $stmtPost->execute([$post_id]);
        $postOwner = $stmtPost->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_id, is_approved) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $content, $parent_id, $is_approved]);
        $comment_id = $pdo->lastInsertId();
        // Notify post owner if not self
        if ($postOwner && $postOwner != $user_id) {
            $msg = 'New comment on your post.';
            $url = 'posts/view.php?id=' . $post_id . '#comment-' . $comment_id;
            $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)')->execute([$postOwner, 'comment', $msg, $url]);
        }
        // Notify parent comment owner if reply and not self or post owner
        if ($parent_id) {
            $stmtParent = $pdo->prepare('SELECT user_id FROM comments WHERE id = ?');
            $stmtParent->execute([$parent_id]);
            $parentOwner = $stmtParent->fetchColumn();
            if ($parentOwner && $parentOwner != $user_id && $parentOwner != $postOwner) {
                $msg = 'New reply to your comment.';
                $url = 'posts/view.php?id=' . $post_id . '#comment-' . $comment_id;
                $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)')->execute([$parentOwner, 'reply', $msg, $url]);
            }
        }
        
        // Check if this is an AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Comment posted successfully']);
        } else {
            header("Location: view.php?id=$post_id&success=comment_added");
        }
    } catch (PDOException $e) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        } else {
            die("Error: " . $e->getMessage());
        }
    }
}
?>