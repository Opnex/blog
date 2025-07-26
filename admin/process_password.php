<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    // Validate new password match
    if ($new_password !== $confirm_password) {
        header("Location: profile.php?error=password_mismatch");
        exit();
    }

    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            header("Location: profile.php?error=invalid_current_password");
            exit();
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        
        header("Location: profile.php?success=password_updated");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>