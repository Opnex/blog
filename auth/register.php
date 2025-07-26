<?php 
include '../includes/db.php';
include '../includes/header.php'; 
?>

<div class="container mt-5">
    <h2>Register</h2>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php if ($_GET['error'] === 'exists'): ?>
                Username or email already exists.
            <?php elseif ($_GET['error'] === 'server'): ?>
                Server error. Please try again later.
            <?php else: ?>
                Registration failed. Please try again.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form action="process_register.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include '../includes/footer.php'; ?>