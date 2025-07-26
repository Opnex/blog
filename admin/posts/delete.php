<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();

include '../../includes/db.php';

$post_id = $_GET['id'] ?? null;

if ($post_id) {
    try {
        // Delete associated comments first (due to foreign key constraints)
        $stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->execute([$post_id]);

        // Delete the post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        header("Location: ../dashboard.php?success=post_deleted");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../dashboard.php?error=invalid_request");
}
?>