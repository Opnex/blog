<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Get user's bookmarks
$stmt = $pdo->prepare("
    SELECT posts.*, users.username, users.profile_picture, categories.name as category_name, categories.color as category_color,
           bookmarks.created_at as bookmarked_at
    FROM bookmarks 
    JOIN posts ON bookmarks.post_id = posts.id 
    JOIN users ON posts.user_id = users.id
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE bookmarks.user_id = ? AND posts.status = 'published'
    ORDER BY bookmarks.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookmarks = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-bookmark me-2"></i>My Bookmarks
                    <span class="badge bg-primary ms-2"><?php echo count($bookmarks); ?> posts</span>
                </h2>
            </div>
            
            <?php if (empty($bookmarks)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-bookmark fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">No bookmarks yet</h3>
                    <p class="text-muted mb-4">Start bookmarking posts you want to read later!</p>
                    <a href="../index.php" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Browse Posts
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($bookmarks as $post): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 post-card shadow-sm border-0">
                                <div class="card-body p-4">
                                    <!-- Category Badge -->
                                    <?php if ($post['category_name']): ?>
                                        <div class="mb-3">
                                            <span class="badge" style="background-color: <?php echo $post['category_color']; ?>">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex align-items-start mb-3">
                                        <img src="<?php echo !empty($post['profile_picture']) ? '/opnex_blog/assets/profile_pics/' . htmlspecialchars($post['profile_picture']) : '/opnex_blog/assets/profile_pics/default.png'; ?>" 
                                             alt="Profile" class="rounded-circle me-3" 
                                             style="width:50px;height:50px;object-fit:cover;border:2px solid #e9ecef;">
                                        <div style="flex:1;">
                                            <h4 class="card-title mb-1"><?php echo htmlspecialchars($post['title']); ?></h4>
                                            <div class="d-flex align-items-center text-muted mb-2">
                                                <i class="fas fa-user me-1"></i>
                                                <span class="me-3"><?php echo htmlspecialchars($post['username']); ?></span>
                                                <i class="fas fa-calendar me-1"></i>
                                                <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-bookmark me-1"></i>Bookmarked <?php echo date('M j, Y', strtotime($post['bookmarked_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="post-content mb-3">
                                        <?php
                                        // Show first image if exists
                                        $content = $post['content'];
                                        preg_match('/<img[^>]+src=[\'\"]([^\'\"]+)[\'\"]/', $content, $matches);
                                        if (!empty($matches[1])) {
                                            $img_url = $matches[1];
                                            if (strpos($img_url, '/') !== 0 && strpos($img_url, 'http') !== 0) {
                                                $img_url = '/opnex_blog/' . ltrim($img_url, '/');
                                            }
                                            echo '<div class="post-image mb-3">';
                                            echo '<img src="' . htmlspecialchars($img_url) . '" class="img-fluid rounded" style="max-height:200px;object-fit:cover;width:100%;">';
                                            echo '</div>';
                                        }
                                        
                                        // Show text excerpt
                                        $text_only = strip_tags($content);
                                        echo '<p class="text-muted">' . htmlspecialchars(mb_strimwidth($text_only, 0, 150, '...')) . '</p>';
                                        ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex gap-2">
                                            <a href="/opnex_blog/posts/view.php?id=<?php echo $post['id']; ?>" 
                                               class="btn btn-primary">
                                                <i class="fas fa-eye me-1"></i>Read More
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm remove-bookmark" data-post-id="<?php echo $post['id']; ?>">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                        <div class="text-muted">
                                            <small>
                                                <i class="fas fa-eye me-1"></i><?php echo $post['views'] ?? 0; ?> views
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.post-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>

<script>
// Remove bookmark functionality
document.querySelectorAll('.remove-bookmark').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const card = this.closest('.col-lg-6');
        
        if (confirm('Remove this post from bookmarks?')) {
            fetch('/opnex_blog/posts/bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card with animation
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'scale(0.8)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Update bookmark count
                        const countBadge = document.querySelector('.badge');
                        const currentCount = parseInt(countBadge.textContent.match(/\d+/)[0]);
                        countBadge.textContent = `${currentCount - 1} posts`;
                        
                        // Show empty state if no bookmarks left
                        if (currentCount - 1 === 0) {
                            location.reload();
                        }
                    }, 300);
                    
                    showAlert('Post removed from bookmarks!', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error removing bookmark', 'danger');
            });
        }
    });
});

// Show alert function
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top:20px;right:20px;z-index:1050;min-width:300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.transition = 'all 0.5s ease';
        alertDiv.style.transform = 'translateX(100%)';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
    }, 3000);
}
</script>

<?php include '../includes/footer.php'; ?> 