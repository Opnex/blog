<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();

include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $status, $post_id, $_SESSION['user_id']]);
        header("Location: ../dashboard.php?success=post_updated");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>