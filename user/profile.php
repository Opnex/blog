<?php
session_start();
include '../includes/db.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (!$user_id) {
    header('Location: ../index.php');
    exit;
}

// Get detailed user info
$stmt = $pdo->prepare("
    SELECT users.*, 
           COUNT(DISTINCT posts.id) as total_posts,
           COUNT(DISTINCT post_likes.id) as total_likes_received,
           COUNT(DISTINCT comments.id) as total_comments,
           COUNT(DISTINCT CASE WHEN posts.status = 'draft' THEN posts.id END) as draft_posts,
           COUNT(DISTINCT CASE WHEN posts.status = 'published' THEN posts.id END) as published_posts,
           MAX(posts.created_at) as last_post_date,
           COUNT(DISTINCT user_followers.follower_id) as followers_count,
           COUNT(DISTINCT following.following_id) as following_count,
           COUNT(DISTINCT bookmarks.post_id) as bookmarked_posts,
           COUNT(DISTINCT user_likes.post_id) as liked_posts
    FROM users 
    LEFT JOIN posts ON users.id = posts.user_id
    LEFT JOIN post_likes ON posts.id = post_likes.post_id
    LEFT JOIN comments ON posts.id = comments.post_id AND comments.is_approved = 1
    LEFT JOIN user_followers ON users.id = user_followers.following_id
    LEFT JOIN user_followers following ON users.id = following.follower_id
    LEFT JOIN bookmarks ON users.id = bookmarks.user_id
    LEFT JOIN post_likes user_likes ON users.id = user_likes.user_id
    WHERE users.id = ?
    GROUP BY users.id
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../index.php');
    exit;
}

// Get user's posts
$stmt = $pdo->prepare("
    SELECT posts.*, categories.name as category_name, categories.color as category_color,
           COUNT(post_likes.id) as like_count,
           COUNT(comments.id) as comment_count
    FROM posts 
    LEFT JOIN categories ON posts.category_id = categories.id
    LEFT JOIN post_likes ON posts.id = post_likes.post_id
    LEFT JOIN comments ON posts.id = comments.post_id AND comments.is_approved = 1
    WHERE posts.user_id = ? AND posts.status = 'published'
    GROUP BY posts.id
    ORDER BY posts.created_at DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();

// Check if current user is following this user
$is_following = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id) {
    $stmt = $pdo->prepare('SELECT id FROM user_followers WHERE follower_id = ? AND following_id = ?');
    $stmt->execute([$_SESSION['user_id'], $user_id]);
    $is_following = $stmt->fetch() ? true : false;
}

// Get follower/following counts
$stmt = $pdo->prepare('SELECT COUNT(*) FROM user_followers WHERE following_id = ?');
$stmt->execute([$user_id]);
$followers_count = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM user_followers WHERE follower_id = ?');
$stmt->execute([$user_id]);
$following_count = $stmt->fetchColumn();

include '../includes/header.php';
?>

<div class="container mt-4">
    <!-- User Profile Header -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <img src="<?php echo !empty($user['profile_picture']) ? '/opnex_blog/assets/profile_pics/' . htmlspecialchars($user['profile_picture']) : '/opnex_blog/assets/profile_pics/default.png'; ?>" 
                             alt="Profile" class="rounded-circle me-4" 
                             style="width:120px;height:120px;object-fit:cover;border:4px solid #e9ecef;">
                        <div style="flex:1;">
                            <h2 class="mb-2"><?php echo htmlspecialchars($user['username']); ?></h2>
                            
                            <?php if ($user['bio']): ?>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars($user['bio']); ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php if ($user['location']): ?>
                                    <span class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($user['location']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($user['website']): ?>
                                    <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-globe me-1"></i>Website
                                    </a>
                                <?php endif; ?>
                                
                                <span class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>Joined <?php echo date('M Y', strtotime($user['created_at'])); ?>
                                </span>
                            </div>
                            
                            <!-- Social Links -->
                            <?php if ($user['social_twitter'] || $user['social_facebook'] || $user['social_linkedin']): ?>
                                <div class="d-flex gap-2 mb-3">
                                    <?php if ($user['social_twitter']): ?>
                                        <a href="https://twitter.com/<?php echo htmlspecialchars($user['social_twitter']); ?>" 
                                           target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['social_facebook']): ?>
                                        <a href="https://facebook.com/<?php echo htmlspecialchars($user['social_facebook']); ?>" 
                                           target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['social_linkedin']): ?>
                                        <a href="https://linkedin.com/in/<?php echo htmlspecialchars($user['social_linkedin']); ?>" 
                                           target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mb-3">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id): ?>
                                    <button class="btn <?php echo $is_following ? 'btn-outline-primary' : 'btn-primary'; ?> follow-btn" 
                                            data-user-id="<?php echo $user_id; ?>">
                                        <i class="fas fa-<?php echo $is_following ? 'user-minus' : 'user-plus'; ?> me-1"></i>
                                        <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                                    </button>
                                    
                                    <a href="followers.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-users me-1"></i>Followers
                                    </a>
                                    
                                    <a href="following.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-user-plus me-1"></i>Following
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                                    <a href="../admin/profile.php" class="btn btn-outline-warning">
                                        <i class="fas fa-edit me-1"></i>Edit Profile
                                    </a>
                                    
                                    <a href="my_followers.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-users me-1"></i>My Followers
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Detailed User Information -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Personal Information</h6>
                                    <div class="user-details">
                                        <?php if ($user['email'] && (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id)): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['full_name']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-user text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['gender']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-venus-mars text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['gender'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['birth_date']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-birthday-cake text-muted me-2"></i>
                                                <span><?php echo date('F j, Y', strtotime($user['birth_date'])); ?> (<?php echo $user['age']; ?> years old)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['phone']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-phone text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['location']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['location']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['address']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-home text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['address']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['city'] || $user['state'] || $user['country']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-globe text-muted me-2"></i>
                                                <span>
                                                    <?php 
                                                    $location_parts = array_filter([$user['city'], $user['state'], $user['country']]);
                                                    echo htmlspecialchars(implode(', ', $location_parts));
                                                    ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['website']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-globe text-muted me-2"></i>
                                                <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($user['website']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="detail-item mb-2">
                                            <i class="fas fa-calendar text-muted me-2"></i>
                                            <span>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></span>
                                        </div>
                                        
                                        <?php if ($user['last_post_date']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <span>Last active <?php echo date('M j, Y', strtotime($user['last_post_date'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-success mb-3"><i class="fas fa-trophy me-2"></i>Achievements & Activity</h6>
                                    <div class="achievements">
                                        <?php if ($user['total_posts'] >= 10): ?>
                                            <div class="achievement-item mb-2">
                                                <i class="fas fa-medal text-warning me-2"></i>
                                                <span>Prolific Writer (<?php echo $user['total_posts']; ?> posts)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['total_likes_received'] >= 50): ?>
                                            <div class="achievement-item mb-2">
                                                <i class="fas fa-heart text-danger me-2"></i>
                                                <span>Popular Content (<?php echo $user['total_likes_received']; ?> likes)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['followers_count'] >= 20): ?>
                                            <div class="achievement-item mb-2">
                                                <i class="fas fa-users text-info me-2"></i>
                                                <span>Community Leader (<?php echo $user['followers_count']; ?> followers)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['total_comments'] >= 30): ?>
                                            <div class="achievement-item mb-2">
                                                <i class="fas fa-comments text-primary me-2"></i>
                                                <span>Engaged Member (<?php echo $user['total_comments']; ?> comments)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!$user['total_posts'] && !$user['total_likes_received'] && !$user['followers_count'] && !$user['total_comments']): ?>
                                            <div class="achievement-item mb-2">
                                                <i class="fas fa-seedling text-success me-2"></i>
                                                <span>New Member - Start your journey!</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Education & Career Information -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-info mb-3"><i class="fas fa-graduation-cap me-2"></i>Education & Career</h6>
                                    <div class="education-career">
                                        <?php if ($user['education_level']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-graduation-cap text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['education_level'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['education_institution']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-university text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['education_institution']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['graduation_year']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                                <span>Graduated <?php echo $user['graduation_year']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['job_status']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-briefcase text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['job_status'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['job_title']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-user-tie text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['job_title']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['company']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-building text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['company']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['industry']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-industry text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['industry']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['years_experience']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <span><?php echo $user['years_experience']; ?> years experience</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-warning mb-3"><i class="fas fa-heart me-2"></i>Personal Details</h6>
                                    <div class="personal-details">
                                        <?php if ($user['marital_status']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-heart text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['marital_status'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['nationality']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-flag text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['nationality']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['religion']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-pray text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['religion']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['languages']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-language text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['languages']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['interests']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-star text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['interests']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['skills']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-tools text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['skills']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['hobbies']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-gamepad text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['hobbies']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['favorite_topics']): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-bookmark text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['favorite_topics']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Stats Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Detailed Statistics</h5>
                </div>
                <div class="card-body">
                    <!-- Main Stats -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="stat-item">
                                <h4 class="text-primary mb-1"><?php echo $user['total_posts']; ?></h4>
                                <small class="text-muted">Posts</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <a href="followers.php?user_id=<?php echo $user_id; ?>" class="text-decoration-none">
                                    <h4 class="text-success mb-1"><?php echo $user['followers_count']; ?></h4>
                                    <small class="text-muted">Followers</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <a href="following.php?user_id=<?php echo $user_id; ?>" class="text-decoration-none">
                                    <h4 class="text-info mb-1"><?php echo $user['following_count']; ?></h4>
                                    <small class="text-muted">Following</small>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Stats -->
                    <div class="detailed-stats">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h5 class="text-warning mb-1"><?php echo $user['total_likes_received']; ?></h5>
                                    <small class="text-muted">Likes Received</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h5 class="text-info mb-1"><?php echo $user['total_comments']; ?></h5>
                                    <small class="text-muted">Comments</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Post Breakdown -->
                        <?php if ($user['total_posts'] > 0): ?>
                        <hr class="my-3">
                        <h6 class="text-muted mb-2"><i class="fas fa-file-alt me-2"></i>Post Breakdown</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-success mb-1"><?php echo $user['published_posts']; ?></h6>
                                    <small class="text-muted">Published</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-warning mb-1"><?php echo $user['draft_posts']; ?></h6>
                                    <small class="text-muted">Drafts</small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Engagement Stats -->
                        <?php if ($user['total_posts'] > 0): ?>
                        <hr class="my-3">
                        <h6 class="text-muted mb-2"><i class="fas fa-chart-line me-2"></i>Engagement</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-primary mb-1"><?php echo $user['total_posts'] > 0 ? round($user['total_likes_received'] / $user['total_posts'], 1) : 0; ?></h6>
                                    <small class="text-muted">Avg Likes/Post</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-info mb-1"><?php echo $user['total_posts'] > 0 ? round($user['total_comments'] / $user['total_posts'], 1) : 0; ?></h6>
                                    <small class="text-muted">Avg Comments/Post</small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Personal Activity -->
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                        <hr class="my-3">
                        <h6 class="text-muted mb-2"><i class="fas fa-user-clock me-2"></i>Your Activity</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-success mb-1"><?php echo $user['bookmarked_posts']; ?></h6>
                                    <small class="text-muted">Bookmarked</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h6 class="text-danger mb-1"><?php echo $user['liked_posts']; ?></h6>
                                    <small class="text-muted">Liked Posts</small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                            <div class="stat-item">
                                <h4 class="text-secondary mb-1"><?php echo $user['total_comments']; ?></h4>
                                <small class="text-muted">Comments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User's Posts -->
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-file-alt me-2"></i>Posts by <?php echo htmlspecialchars($user['username']); ?>
                <span class="badge bg-primary ms-2"><?php echo count($posts); ?> posts</span>
            </h3>
            
            <?php if (empty($posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No posts yet</h4>
                    <p class="text-muted">This user hasn't published any posts yet.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post): ?>
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
                                    
                                    <h5 class="card-title mb-2"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <div class="d-flex align-items-center text-muted mb-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
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
                                            echo '<img src="' . htmlspecialchars($img_url) . '" class="img-fluid rounded" style="max-height:150px;object-fit:cover;width:100%;">';
                                            echo '</div>';
                                        }
                                        
                                        // Show text excerpt
                                        $text_only = strip_tags($content);
                                        echo '<p class="text-muted">' . htmlspecialchars(mb_strimwidth($text_only, 0, 100, '...')) . '</p>';
                                        ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="/opnex_blog/posts/view.php?id=<?php echo $post['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Read More
                                        </a>
                                        <div class="text-muted">
                                            <small>
                                                <i class="fas fa-heart me-1"></i><?php echo $post['like_count']; ?>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-comment me-1"></i><?php echo $post['comment_count']; ?>
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
.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.post-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

/* Detailed Information Styles */
.user-details, .achievements, .education-career, .personal-details {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    margin-bottom: 1rem;
}

.detail-item, .achievement-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child, .achievement-item:last-child {
    border-bottom: none;
}

.detail-item i, .achievement-item i {
    width: 20px;
    text-align: center;
}

.achievements {
    border-left-color: #28a745;
}

.education-career {
    border-left-color: #17a2b8;
}

.personal-details {
    border-left-color: #ffc107;
}

.detailed-stats {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.detailed-stats hr {
    border-color: #dee2e6;
    margin: 1rem 0;
}

.detailed-stats h6 {
    font-size: 0.9rem;
    font-weight: 600;
}

.follow-btn {
    transition: all 0.3s ease;
}

.follow-btn:hover {
    transform: scale(1.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .user-details, .achievements {
        margin-bottom: 1rem;
    }
    
    .detailed-stats {
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Follow/Unfollow functionality
document.querySelectorAll('.follow-btn').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const icon = this.querySelector('i');
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
                this.classList.toggle('btn-outline-primary');
                this.classList.toggle('btn-primary');
                
                // Update button text and icon
                if (this.classList.contains('btn-primary')) {
                    this.innerHTML = '<i class="fas fa-user-minus me-1"></i>Unfollow';
                } else {
                    this.innerHTML = '<i class="fas fa-user-plus me-1"></i>Follow';
                }
                
                // Show success message
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