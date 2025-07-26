<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check for duplicate username or email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $exists = $stmt->fetchColumn();
    if ($exists) {
        header("Location: register.php?error=exists");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php?success=1"); // Redirect to login
    } catch (PDOException $e) {
        header("Location: register.php?error=server");
        exit();
    }
}
?>