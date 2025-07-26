<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function isAdmin() {
    if (!isset($_SESSION['user_id'])) return false;
    global $pdo;
    $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    return $row && $row['is_admin'] == 1;
}
?>