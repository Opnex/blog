<?php
include '../includes/db.php';
include '../includes/header.php';

$token = $_GET['token'] ?? null;

if ($token) {
    try {
        // Check if token is valid and not expired
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            die("<div class='container mt-5 alert alert-danger'>Invalid or expired token. <a href='forgot_password.php'>Try again</a>.</div>");
        }
        ?>
        
        <div class="container mt-5">
            <h2>Reset Password</h2>
            <form action="process_reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>
        
        <?php
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: forgot_password.php");
}
include '../includes/footer.php';
?>