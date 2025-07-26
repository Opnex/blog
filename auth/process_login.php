<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credential = $_POST['credential'];
    $password = $_POST['password'];

    // Check if input is email or username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    
    $stmt->execute([$credential, $credential]);
    $user = $stmt->fetch();

    if ($user && isset($user['is_active']) && $user['is_active'] == 0) {
        header("Location: login.php?error=banned");
        exit();
    }

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php"); // Redirect to homepage after login
    } else {
        header("Location: login.php?error=1"); // Invalid credentials
    }
}
?>