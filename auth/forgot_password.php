<?php
include '../includes/db.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Forgot Password</h2>
    <form action="process_forgot_password.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
        <a href="login.php" class="btn btn-link">Back to Login</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>