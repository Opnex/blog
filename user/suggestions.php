<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

$current_user_id = $_SESSION['user_id'];

// Get users that the current user is not following
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.profile_picture, u.bio,
           (SELECT COUNT(*) FROM posts WHERE user_id = u.id AND status = 'published') as post_count,
           (SELECT COUNT(*) FROM user_followers WHERE following_id = u.id) as followers_count,
           (SELECT COUNT(*) FROM user_followers WHERE follower_id = u.id) as following_count,
           (SELECT COUNT(*) FROM user_followers uf1 
            JOIN user_followers uf2 ON uf1.following_id = uf2.following_id 
            WHERE uf1.follower_id = ? AND uf2.follower_id = u.id) as mutual_followers
    FROM users u
    WHERE u.id != ? 
    AND u.id NOT IN (SELECT following_id FROM user_followers WHERE follower_id = ?)
    AND u.is_active = 1
    ORDER BY mutual_followers DESC, followers_count DESC, post_count DESC
    LIMIT 20
");
$stmt->execute([$current_user_id, $current_user_id, $current_user_id]);
$suggestions = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-lightbulb me-2"></i>People You May Know
                    </h2>
                    <p class="text-muted mb-0">Discover amazing writers and thinkers in our community</p>
                </div>
            </div>

            <?php if (empty($suggestions)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No suggestions available</h4>
                    <p class="text-muted">You're already following everyone! Check back later for new members.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($suggestions as $user): ?>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="<?php echo $user['profile_picture'] ? '../assets/profile_pics/' . $user['profile_picture'] : '../assets/profile_pics/default.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                                 class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </a>
                                            </h6>
                                            <?php if ($user['bio']): ?>
                                                <p class="text-muted small mb-1"><?php echo htmlspecialchars(substr($user['bio'], 0, 50)); ?><?php echo strlen($user['bio']) > 50 ? '...' : ''; ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-3">
                                                    <i class="fas fa-file-alt me-1"></i><?php echo $user['post_count']; ?> posts
                                                </span>
                                                <span class="me-3">
                                                    <i class="fas fa-users me-1"></i><?php echo $user['followers_count']; ?> followers
                                                </span>
                                                <?php if ($user['mutual_followers'] > 0): ?>
                                                    <span class="text-primary">
                                                        <i class="fas fa-handshake me-1"></i><?php echo $user['mutual_followers']; ?> mutual
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-primary btn-sm follow-btn" data-user-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-user-plus"></i>Follow
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Refresh Button -->
                <div class="text-center mt-4">
                    <a href="suggestions.php" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt me-1"></i>Refresh Suggestions
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
                    this.innerHTML = '<i class="fas fa-user-plus"></i>Follow';
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                    this.innerHTML = '<i class="fas fa-user-minus"></i>Unfollow';
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