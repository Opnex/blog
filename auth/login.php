<?php 
include '../includes/db.php';
include '../includes/header.php';

if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">Registration successful! Please login.</div>';

        if ($_GET['success'] === 'password_reset') {
        echo '<div class="alert alert-success">Password reset successful! Please login.</div>';
    }
}
if (isset($_GET['logout'])) {
    echo '<div class="alert alert-success">You have been logged out.</div>';
}
if (isset($_GET['error']) && $_GET['error'] === 'banned') {
    echo '<div class="alert alert-danger">Your account has been banned or deactivated. Please contact support.</div>';
}
?>


<div class="container mt-5">
    <h2>Login</h2>
    <form action="process_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Username/Email</label>
            <input type="text" name="credential" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="register.php">Register</a></p>
</div>

<?php include '../includes/footer.php'; ?>
<script>
  setTimeout(function() {
    var alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(function(alert) {
      alert.style.display = 'none';
    });
  }, 3000);
</script>