<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($new_password !== $confirm_password) {
        header("Location: reset_password.php?token=$token&error=mismatch");
        exit();
    }

    try {
        // 1. Verify token validity
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            die("<div class='container mt-5 alert alert-danger'>Invalid or expired token.</div>");
        }

        // 2. Update password and clear token
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        
        // 3. Redirect to login with success message
        header("Location: login.php?success=password_reset");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>