<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    try {
        // 1. Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // 2. Generate unique token (32 characters)
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour
            
            // 3. Store token in database
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
            
            // 4. Send email (simulated here - replace with real mail() or PHPMailer)
            $reset_link = "http://yourdomain.com/opnex_blog/auth/reset_password.php?token=$token";
            // mail($email, "Password Reset", "Click here to reset: $reset_link");
            
            // For testing, display the link:
            echo "<div class='container mt-5 alert alert-info'>";
            echo "Password reset link (simulated): <a href='$reset_link'>$reset_link</a>";
            echo "</div>";
            exit();
        }
        
        // Always show success (even if email doesn't exist) for security
        header("Location: forgot_password.php?success=1");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>