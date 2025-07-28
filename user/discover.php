<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

$current_user_id = $_SESSION['user_id'];

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Pagination
$users_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $users_per_page;

// Build query
$where_conditions = ['u.id != ?', 'u.is_active = 1'];
$params = [$current_user_id];

if ($search) {
    $where_conditions[] = '(u.username LIKE ? OR u.bio LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter) {
    $where_conditions[] = 'EXISTS (SELECT 1 FROM posts p WHERE p.user_id = u.id AND p.category_id = ? AND p.status = "published")';
    $params[] = $category_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_query = "
    SELECT COUNT(DISTINCT u.id) 
    FROM users u 
    WHERE $where_clause
";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $users_per_page);

// Get users
$query = "
    SELECT u.id, u.username, u.profile_picture, u.bio, u.created_at,
           (SELECT COUNT(*) FROM posts WHERE user_id = u.id AND status = 'published') as post_count,
           (SELECT COUNT(*) FROM user_followers WHERE following_id = u.id) as followers_count,
           (SELECT COUNT(*) FROM user_followers WHERE follower_id = u.id) as following_count,
           (SELECT COUNT(*) FROM user_followers uf1 
            JOIN user_followers uf2 ON uf1.following_id = uf2.following_id 
            WHERE uf1.follower_id = ? AND uf2.follower_id = u.id) as mutual_followers
    FROM users u
    WHERE $where_clause
    ORDER BY mutual_followers DESC, followers_count DESC, post_count DESC
    LIMIT ? OFFSET ?
";

$params[] = $current_user_id;
$params[] = $users_per_page;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Check if current user is following each user
$following_status = [];
foreach ($users as $user) {
    $stmt = $pdo->prepare('SELECT id FROM user_followers WHERE follower_id = ? AND following_id = ?');
    $stmt->execute([$current_user_id, $user['id']]);
    $following_status[$user['id']] = $stmt->fetch() ? true : false;
}

// Get categories for filter
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-search me-2"></i>Discover People
                    </h2>
                    <p class="text-muted mb-0">Find amazing writers and thinkers in our community</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search Users</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by username or bio..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter by Category</label>
                            <select name="category" class="form-select">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div class="row">
                <?php if (empty($users)): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No users found</h4>
                            <p class="text-muted">
                                <?php if ($search || $category_filter): ?>
                                    Try adjusting your search criteria.
                                <?php else: ?>
                                    No other users found in the system.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4">
                                    <div class="text-center mb-3">
                                        <img src="<?php echo $user['profile_picture'] ? '../assets/profile_pics/' . $user['profile_picture'] : '../assets/profile_pics/default.png'; ?>" 
                                             alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                             class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                                        <h5 class="mb-1">
                                            <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </a>
                                        </h5>
                                        <?php if ($user['bio']): ?>
                                            <p class="text-muted small mb-2"><?php echo htmlspecialchars(substr($user['bio'], 0, 100)); ?><?php echo strlen($user['bio']) > 100 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <div class="text-primary fw-bold"><?php echo $user['post_count']; ?></div>
                                            <small class="text-muted">Posts</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-success fw-bold"><?php echo $user['followers_count']; ?></div>
                                            <small class="text-muted">Followers</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-info fw-bold"><?php echo $user['following_count']; ?></div>
                                            <small class="text-muted">Following</small>
                                        </div>
                                    </div>
                                    
                                    <?php if ($user['mutual_followers'] > 0): ?>
                                        <div class="text-center mb-3">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-handshake me-1"></i><?php echo $user['mutual_followers']; ?> mutual followers
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-user me-1"></i>View Profile
                                        </a>
                                        <button class="btn btn-sm <?php echo $following_status[$user['id']] ? 'btn-outline-primary' : 'btn-primary'; ?> follow-btn" 
                                                data-user-id="<?php echo $user['id']; ?>">
                                            <i class="fas fa-<?php echo $following_status[$user['id']] ? 'user-minus' : 'user-plus'; ?>"></i>
                                            <?php echo $following_status[$user['id']] ? 'Unfollow' : 'Follow'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="User discovery pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
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