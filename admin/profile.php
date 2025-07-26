<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

// Fetch current user data
$stmt = $pdo->prepare("SELECT username, email, profile_picture, bio FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container mt-5">
    <h2>Your Profile</h2>
    <?php if (!empty($user['profile_picture'])): ?>
        <img src="../assets/profile_pics/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;">
    <?php else: ?>
        <img src="../assets/profile_pics/default.png" alt="Profile Picture" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;">
    <?php endif; ?>
    <!-- Profile Update Form -->
    <form action="process_profile.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" 
                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control">
            <small class="form-text text-muted">Max size: 2MB. JPG, PNG only.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <!-- Password Update Form -->
    <form action="process_password.php" method="POST" class="mt-5">
        <h4>Change Password</h4>
        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Change Password</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>