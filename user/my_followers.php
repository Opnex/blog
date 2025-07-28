<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

$current_user_id = $_SESSION['user_id'];

// Get current user info
$stmt = $pdo->prepare('SELECT username, profile_picture FROM users WHERE id = ?');
$stmt->execute([$current_user_id]);
$user = $stmt->fetch();

// Get followers with user info
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.profile_picture, u.bio, uf.created_at as followed_at,
           (SELECT COUNT(*) FROM posts WHERE user_id = u.id AND status = 'published') as post_count,
           (SELECT COUNT(*) FROM user_followers WHERE following_id = u.id) as followers_count,
           (SELECT COUNT(*) FROM user_followers WHERE follower_id = u.id) as following_count
    FROM user_followers uf
    JOIN users u ON uf.follower_id = u.id
    WHERE uf.following_id = ?
    ORDER BY uf.created_at DESC
");
$stmt->execute([$current_user_id]);
$followers = $stmt->fetchAll();

// Check if current user is following each follower back
$following_status = [];
foreach ($followers as $follower) {
    $stmt = $pdo->prepare('SELECT id FROM user_followers WHERE follower_id = ? AND following_id = ?');
    $stmt->execute([$current_user_id, $follower['id']]);
    $following_status[$follower['id']] = $stmt->fetch() ? true : false;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-users me-2"></i>People Following You
                    </h2>
                    <p class="text-muted mb-0">
                        <strong><?php echo count($followers); ?></strong> people are following 
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                    </p>
                </div>
            </div>

            <?php if (empty($followers)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No followers yet</h4>
                    <p class="text-muted">When people follow you, they'll appear here.</p>
                    <a href="suggestions.php" class="btn btn-primary">
                        <i class="fas fa-lightbulb me-1"></i>Find People to Follow
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($followers as $follower): ?>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="<?php echo $follower['profile_picture'] ? '../assets/profile_pics/' . $follower['profile_picture'] : '../assets/profile_pics/default.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($follower['username']); ?>" 
                                                 class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="profile.php?user_id=<?php echo $follower['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($follower['username']); ?>
                                                </a>
                                                <?php if ($following_status[$follower['id']]): ?>
                                                    <span class="badge bg-success ms-2">Following Back</span>
                                                <?php endif; ?>
                                            </h6>
                                            <?php if ($follower['bio']): ?>
                                                <p class="text-muted small mb-1"><?php echo htmlspecialchars(substr($follower['bio'], 0, 50)); ?><?php echo strlen($follower['bio']) > 50 ? '...' : ''; ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-3">
                                                    <i class="fas fa-file-alt me-1"></i><?php echo $follower['post_count']; ?> posts
                                                </span>
                                                <span class="me-3">
                                                    <i class="fas fa-users me-1"></i><?php echo $follower['followers_count']; ?> followers
                                                </span>
                                                <span>
                                                    <i class="fas fa-calendar me-1"></i><?php echo date('M j, Y', strtotime($follower['followed_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <?php if ($following_status[$follower['id']]): ?>
                                                <button class="btn btn-outline-primary btn-sm follow-btn" data-user-id="<?php echo $follower['id']; ?>">
                                                    <i class="fas fa-user-minus"></i>Unfollow
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-primary btn-sm follow-btn" data-user-id="<?php echo $follower['id']; ?>">
                                                    <i class="fas fa-user-plus"></i>Follow Back
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Quick Actions -->
                <div class="text-center mt-4">
                    <a href="suggestions.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-lightbulb me-1"></i>Find More People
                    </a>
                    <a href="following.php" class="btn btn-outline-secondary">
                        <i class="fas fa-user-plus me-1"></i>Who You're Following
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Follow/Unfollow functionality
document.querySelectorAll('.follow-btn').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const isFollowing = this.classList.contains('btn-outline-primary');
        
        fetch('/opnex_blog/user/follow.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}&action=${isFollowing ? 'unfollow' : 'follow'}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isFollowing) {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    this.innerHTML = '<i class="fas fa-user-plus"></i>Follow Back';
                    // Update the badge
                    const badge = this.closest('.card-body').querySelector('.badge');
                    if (badge) badge.remove();
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                    this.innerHTML = '<i class="fas fa-user-minus"></i>Unfollow';
                    // Add the badge
                    const usernameElement = this.closest('.card-body').querySelector('h6 a');
                    if (usernameElement) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success ms-2';
                        badge.textContent = 'Following Back';
                        usernameElement.parentNode.appendChild(badge);
                    }
                }
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error following user', 'danger');
        });
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script>

<?php include '../includes/footer.php'; ?> 