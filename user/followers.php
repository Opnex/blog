<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

// Get user ID from URL parameter
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare('SELECT username, profile_picture FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: profile.php');
    exit;
}

// Pagination
$followers_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $followers_per_page;

// Get total followers count
$stmt = $pdo->prepare('SELECT COUNT(*) FROM user_followers WHERE following_id = ?');
$stmt->execute([$user_id]);
$total_followers = $stmt->fetchColumn();
$total_pages = ceil($total_followers / $followers_per_page);

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
    LIMIT ? OFFSET ?
");
$stmt->execute([$user_id, $followers_per_page, $offset]);
$followers = $stmt->fetchAll();

// Check if current user is following each follower
$current_user_id = $_SESSION['user_id'];
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
                <a href="profile.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left me-1"></i>Back to Profile
                </a>
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-users me-2"></i>Followers
                    </h2>
                    <p class="text-muted mb-0">
                        <?php echo $total_followers; ?> people following 
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                    </p>
                </div>
            </div>

            <?php if (empty($followers)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No followers yet</h4>
                    <p class="text-muted">When people follow <?php echo htmlspecialchars($user['username']); ?>, they'll appear here.</p>
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
                                        <?php if ($follower['id'] != $current_user_id): ?>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-sm <?php echo $following_status[$follower['id']] ? 'btn-outline-primary' : 'btn-primary'; ?> follow-btn" 
                                                        data-user-id="<?php echo $follower['id']; ?>">
                                                    <i class="fas fa-<?php echo $following_status[$follower['id']] ? 'user-minus' : 'user-plus'; ?>"></i>
                                                    <?php echo $following_status[$follower['id']] ? 'Unfollow' : 'Follow'; ?>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Followers pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?user_id=<?php echo $user_id; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?user_id=<?php echo $user_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?user_id=<?php echo $user_id; ?>&page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
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