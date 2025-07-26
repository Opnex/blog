<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? 1; // Default to first category
    $status = $_POST['status'] ?? 'published'; // Default to published
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($title) || empty($content)) {
        header("Location: create.php?error=empty_fields");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, category_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $content, $category_id, $status]);
        header("Location: ../index.php?success=post_created");
    } catch (PDOException $e) {
        header("Location: create.php?error=creation_failed");
    }
}
?>