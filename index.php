<?php
session_start();
include 'includes/db.php';

// Pagination
$posts_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Get total posts count
$stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts with user info
$stmt = $pdo->prepare("
    SELECT posts.*, users.username, users.profile_picture, categories.name as category_name, categories.color as category_color
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.status = 'published' 
    ORDER BY posts.created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$posts_per_page, $offset]);
$posts = $stmt->fetchAll();

// Get recent posts for sidebar
$stmt = $pdo->query("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.status = 'published' 
    ORDER BY posts.created_at DESC 
    LIMIT 5
");
$recent_posts = $stmt->fetchAll();

// Get popular posts for sidebar
$stmt = $pdo->query("
    SELECT posts.*, users.username, COUNT(post_likes.id) as like_count 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    LEFT JOIN post_likes ON posts.id = post_likes.post_id 
    WHERE posts.status = 'published' 
    GROUP BY posts.id 
    ORDER BY like_count DESC, posts.created_at DESC 
    LIMIT 5
");
$popular_posts = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Welcome to Opnex Blog</h1>
                <p class="lead mb-4">Discover amazing stories, insights, and perspectives from our vibrant community of writers and thinkers.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/opnex_blog/posts/create.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-pen me-2"></i>Write Your Story
                    </a>
                <?php else: ?>
                    <a href="/opnex_blog/auth/register.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Join Our Community
                    </a>
                <?php endif; ?>
                <a href="#featured-posts" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-arrow-down me-2"></i>Explore Posts
                </a>
            </div>
            <div class="col-lg-4 text-center">
                <div class="hero-icon">
                    <i class="fas fa-blog fa-5x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="mb-4">
                <h2 id="featured-posts" class="text-primary mb-3">
                    <i class="fas fa-star me-2"></i>Featured Stories
                </h2>
                <div class="border-bottom border-2 border-primary" style="width: 60px;"></div>
            </div>
            
            <?php if (empty($posts)): ?>
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">No stories yet</h3>
                        <p class="text-muted mb-4">Be the first to share your amazing story with the world!</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/opnex_blog/posts/create.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-pen me-2"></i>Create Your First Post
                            </a>
                        <?php else: ?>
                            <a href="/opnex_blog/auth/register.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Join and Start Writing
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4 post-card shadow-lg border-0">
                        <div class="card-body p-4">
                            <!-- Category Badge -->
                            <?php if (isset($post['category_name'])): ?>
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
                                </div>
                            </div>
                            
                            <div class="post-content mb-3">
                                <?php
                                // Show first image if exists, then text excerpt
                                $content = $post['content'];
                                preg_match('/<img[^>]+src=[\'\"]([^\'\"]+)[\'\"]/', $content, $matches);
                                if (!empty($matches[1])) {
                                    $img_url = $matches[1];
                                    // If not absolute, prepend /opnex_blog/
                                    if (strpos($img_url, '/') !== 0 && strpos($img_url, 'http') !== 0) {
                                        $img_url = '/opnex_blog/' . ltrim($img_url, '/');
                                    }
                                    echo '<div class="post-image mb-3">';
                                    echo '<img src="' . htmlspecialchars($img_url) . '" class="img-fluid rounded" style="max-height:300px;object-fit:cover;width:100%;">';
                                    echo '</div>';
                                }
                                // Show text excerpt (without HTML tags)
                                $text_only = strip_tags($content);
                                echo '<p class="text-muted">' . htmlspecialchars(mb_strimwidth($text_only, 0, 200, '...')) . '</p>';
                                ?>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-2">
                                    <a href="/opnex_blog/posts/view.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Read More
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="position: relative; z-index: 99999;">
                                            <i class="fas fa-share-alt me-1"></i>Share
                                        </button>
                                        <ul class="dropdown-menu" style="z-index: 99999 !important; position: absolute !important;">
                                            <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/opnex_blog/posts/view.php?id=' . $post['id']); ?>" target="_blank">
                                                <i class="fab fa-facebook me-2"></i>Facebook
                                            </a></li>
                                            <li><a class="dropdown-item" href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/opnex_blog/posts/view.php?id=' . $post['id']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank">
                                                <i class="fab fa-twitter me-2"></i>Twitter
                                            </a></li>
                                            <li><a class="dropdown-item" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/opnex_blog/posts/view.php?id=' . $post['id']); ?>" target="_blank">
                                                <i class="fab fa-linkedin me-2"></i>LinkedIn
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="copyToClipboard('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/opnex_blog/posts/view.php?id=' . $post['id']; ?>')">
                                                <i class="fas fa-link me-2"></i>Copy Link
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-eye me-1"></i><?php echo $post['views'] ?? 0; ?> views
                                    </small>
                                </div>
                            </div>
                            
                            <?php 
                            // Get like count and status
                            $like_stmt = $pdo->prepare('SELECT COUNT(*) FROM post_likes WHERE post_id = ?');
                            $like_stmt->execute([$post['id']]);
                            $like_count = $like_stmt->fetchColumn();
                            
                            $liked = false;
                            if (isset($_SESSION['user_id'])) {
                                $liked_stmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
                                $liked_stmt->execute([$post['id'], $_SESSION['user_id']]);
                                $liked = $liked_stmt->fetch() ? true : false;
                            }
                            
                            // Get comment count
                            $comment_stmt = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ? AND is_approved = 1');
                            $comment_stmt->execute([$post['id']]);
                            $comment_count = $comment_stmt->fetchColumn();
                            ?>
                            
                            <?php 
                            // Get bookmark status
                            $is_bookmarked = false;
                            if (isset($_SESSION['user_id'])) {
                                $bookmark_stmt = $pdo->prepare('SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ?');
                                $bookmark_stmt->execute([$_SESSION['user_id'], $post['id']]);
                                $is_bookmarked = $bookmark_stmt->fetch() ? true : false;
                            }
                            
                            // Calculate reading time
                            $word_count = str_word_count(strip_tags($post['content']));
                            $reading_time = max(1, round($word_count / 200)); // 200 words per minute
                            ?>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="d-flex gap-2 mb-2">
                                    <button class="btn <?php echo $liked ? 'btn-primary' : 'btn-outline-primary'; ?> like-btn" data-post-id="<?php echo $post['id']; ?>" style="min-width: 90px;">
                                        <i class="fas fa-heart me-1"></i> 
                                        <span class="like-text"><?php echo $liked ? 'Liked' : 'Like'; ?></span>
                                    </button>
                                    <button class="btn btn-outline-success comment-toggle-btn" data-post-id="<?php echo $post['id']; ?>" style="min-width: 100px;">
                                        <i class="fas fa-comment me-1"></i> 
                                        <span class="comment-text">Comment</span>
                                    </button>
                                    <button class="btn <?php echo $is_bookmarked ? 'btn-warning' : 'btn-outline-warning'; ?> bookmark-btn" data-post-id="<?php echo $post['id']; ?>" style="min-width: 90px;">
                                        <i class="fas fa-bookmark me-1"></i> 
                                        <span class="bookmark-text"><?php echo $is_bookmarked ? 'Saved' : 'Save'; ?></span>
                                    </button>
                                </div>
                                
                                <!-- Show counts and reading time separately for logged-in users -->
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-heart me-1"></i><?php echo $like_count; ?> likes
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-comment me-1"></i><?php echo $comment_count; ?> comments
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i><?php echo $reading_time; ?> min read
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="d-flex gap-2 mb-3">
                                    <span class="text-muted">
                                        <i class="fas fa-heart me-1"></i><?php echo $like_count; ?> likes
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-comment me-1"></i><?php echo $comment_count; ?> comments
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i><?php echo $reading_time; ?> min read
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Comment Section -->
                            <div class="comment-section" id="comment-section-<?php echo $post['id']; ?>" style="display: none;">
                                <div class="border-top pt-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-comments me-2"></i>Comments
                                    </h6>
                                    
                                    <!-- Comments List -->
                                    <div class="comments-list mb-3" id="comments-list-<?php echo $post['id']; ?>">
                                        <!-- Comments will be loaded here -->
                                    </div>
                                    
                                    <!-- Comment Form -->
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Type your comment below and click "Post Comment" to submit
                                            </small>
                                        </div>
                                        <form class="comment-form" data-post-id="<?php echo $post['id']; ?>">
                                            <div class="input-group">
                                                <textarea class="form-control comment-input" 
                                                          placeholder="Write a comment..." 
                                                          rows="2" 
                                                          style="resize: none;"></textarea>
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-paper-plane me-1"></i>
                                                    <span class="submit-text">Post Comment</span>
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Please <a href="/opnex_blog/auth/login.php">log in</a> to comment on this post.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Posts pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Write New Post Card -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-pen-to-square fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title mb-3">Share Your Story</h5>
                        <p class="text-muted mb-3">Have something amazing to share? Write your next post!</p>
                        <a href="/opnex_blog/posts/create.php" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-pen me-2"></i>Write New Post
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Recent Stories Widget -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Stories
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_posts as $recent): ?>
                        <div class="d-flex align-items-center mb-3 recent-post-item">
                            <div class="flex-shrink-0">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:45px;height:45px;">
                                    <i class="fas fa-file-alt text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <a href="/opnex_blog/posts/view.php?id=<?php echo $recent['id']; ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars(mb_strimwidth($recent['title'], 0, 35, '...')); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($recent['username']); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Popular Posts Widget -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Trending Now
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($popular_posts as $popular): ?>
                        <div class="d-flex align-items-center mb-3 popular-post-item">
                            <div class="flex-shrink-0">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:45px;height:45px;">
                                    <i class="fas fa-heart text-danger"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <a href="/opnex_blog/posts/view.php?id=<?php echo $popular['id']; ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars(mb_strimwidth($popular['title'], 0, 35, '...')); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-heart me-1"></i><?php echo $popular['like_count']; ?> likes
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Community Stats -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Community Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-primary mb-1"><?php echo $total_posts; ?></h4>
                                <small class="text-muted">Total Posts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-success mb-1"><?php echo count($recent_posts); ?></h4>
                                <small class="text-muted">Active Writers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'post_created'): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed" 
         style="top:20px;right:20px;z-index:1050;min-width:300px;">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong> Your post has been created successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<style>
/* Custom Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.post-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    overflow: visible;
    position: relative;
    z-index: 1;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.post-image img {
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.post-card:hover .post-image img {
    transform: scale(1.02);
}

.recent-post-item, .popular-post-item {
    transition: transform 0.2s ease;
    padding: 8px;
    border-radius: 8px;
}

.recent-post-item:hover, .popular-post-item:hover {
    transform: translateX(5px);
    background-color: #f8f9fa;
}

.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: transform 0.2s ease;
}

.stat-item:hover {
    transform: scale(1.05);
}

.empty-state {
    padding: 40px 20px;
}

.empty-state i {
    opacity: 0.5;
}

/* Button improvements */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Card improvements */
.card {
    border-radius: 15px;
    border: none;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .hero-section {
        text-align: center;
    }
    
    .hero-section .col-lg-4 {
        margin-top: 2rem;
    }
    
    .post-card {
        margin-bottom: 1.5rem;
    }
}

/* Loading animations */
.like-btn, .comment-toggle-btn, .bookmark-btn {
    transition: all 0.3s ease;
}

.like-btn:disabled, .comment-toggle-btn:disabled, .bookmark-btn:disabled {
    opacity: 0.6;
}

/* Comment section improvements */
.comment-section {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-top: 15px;
}

.comment-input {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.comment-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>

<script>
// Enhanced Like functionality
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const icon = this.querySelector('i');
        const likeText = this.querySelector('.like-text');
        
        // Add loading state
        this.disabled = true;
        icon.classList.add('fa-spin');
        
        fetch('/opnex_blog/posts/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.classList.toggle('btn-outline-primary');
                this.classList.toggle('btn-primary');
                
                // Update like text
                if (likeText) {
                    likeText.textContent = this.classList.contains('btn-primary') ? 'Liked' : 'Like';
                }
                
                // Update like count display
                const countDisplay = this.closest('.post-card').querySelector('.text-muted');
                if (countDisplay) {
                    const likeCountText = countDisplay.textContent;
                    const newCount = data.like_count;
                    const updatedText = likeCountText.replace(/(\d+) likes/, newCount + ' likes');
                    countDisplay.textContent = updatedText;
                }
                
                // Show success message
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error liking post', 'danger');
        })
        .finally(() => {
            this.disabled = false;
            icon.classList.remove('fa-spin');
        });
    });
});

// Bookmark functionality
document.querySelectorAll('.bookmark-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const icon = this.querySelector('i');
        const bookmarkText = this.querySelector('.bookmark-text');
        
        // Add loading state
        this.disabled = true;
        icon.classList.add('fa-spin');
        
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
                this.classList.toggle('btn-outline-warning');
                this.classList.toggle('btn-warning');
                
                // Update bookmark text
                if (bookmarkText) {
                    bookmarkText.textContent = this.classList.contains('btn-warning') ? 'Saved' : 'Save';
                }
                
                // Show success message
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error bookmarking post', 'danger');
        })
        .finally(() => {
            this.disabled = false;
            icon.classList.remove('fa-spin');
        });
    });
});

// Comment toggle functionality
document.querySelectorAll('.comment-toggle-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const commentSection = document.getElementById('comment-section-' + postId);
        const commentText = this.querySelector('.comment-text');
        
        if (commentSection.style.display === 'none') {
            commentSection.style.display = 'block';
            commentText.textContent = 'Hide Comments';
            loadComments(postId);
        } else {
            commentSection.style.display = 'none';
            commentText.textContent = 'Comment';
        }
    });
});

// Load comments function
function loadComments(postId) {
    const commentsList = document.getElementById('comments-list-' + postId);
    
    fetch('/opnex_blog/posts/get_comments.php?post_id=' + postId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentsList.innerHTML = data.comments.map(comment => `
                    <div class="d-flex mb-3">
                        <img src="${comment.profile_picture ? '/opnex_blog/assets/profile_pics/' + comment.profile_picture : '/opnex_blog/assets/profile_pics/default.png'}" 
                             alt="Profile" class="rounded-circle me-2" 
                             style="width:35px;height:35px;object-fit:cover;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <strong class="me-2">${comment.username}</strong>
                                <small class="text-muted">${comment.created_at}</small>
                            </div>
                            <p class="mb-0">${comment.content}</p>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            commentsList.innerHTML = '<p class="text-muted">Error loading comments</p>';
        });
}

// Comment form submission
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const postId = this.dataset.postId;
        const textarea = this.querySelector('.comment-input');
        const submitBtn = this.querySelector('button[type="submit"]');
        const submitText = submitBtn.querySelector('.submit-text');
        const content = textarea.value.trim();
        
        if (!content) {
            showAlert('Please enter a comment', 'warning');
            return;
        }
        
        // Add loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Posting...';
        
        fetch('/opnex_blog/posts/process_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId + '&content=' + encodeURIComponent(content)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                textarea.value = '';
                loadComments(postId);
                
                // Update comment count
                const countDisplay = this.closest('.post-card').querySelector('.text-muted');
                if (countDisplay) {
                    const commentCountText = countDisplay.textContent;
                    const currentCount = parseInt(commentCountText.match(/(\d+) comments/)[1]);
                    const newCount = currentCount + 1;
                    const updatedText = commentCountText.replace(/(\d+) comments/, newCount + ' comments');
                    countDisplay.textContent = updatedText;
                }
                
                showAlert('Comment posted successfully!', 'success');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error posting comment', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.textContent = 'Post Comment';
        });
    });
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('Link copied to clipboard!', 'success');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showAlert('Failed to copy link', 'danger');
    });
}

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

// Auto-hide success messages
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.display = 'none';
    });
}, 3000);
</script>

<?php include 'includes/footer.php'; ?>
