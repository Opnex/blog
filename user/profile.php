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

<div class="container-fluid mt-4">
    <!-- User Profile Header -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="profile-header-card mb-4">
                <div class="profile-header-content">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar-wrapper">
                            <img src="<?php echo !empty($user['profile_picture']) ? '/opnex_blog/assets/profile_pics/' . htmlspecialchars($user['profile_picture']) : '/opnex_blog/assets/profile_pics/default.png'; ?>" 
                                 alt="Profile" class="profile-avatar">
                            <div class="profile-status-indicator"></div>
                        </div>
                    </div>
                    
                    <div class="profile-info-section">
                        <div class="profile-name-section">
                            <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
                            <?php if (!empty($user['full_name'])): ?>
                                <p class="profile-full-name"><?php echo htmlspecialchars($user['full_name']); ?></p>
                            <?php endif; ?>
                        </div>
                            
                        <div class="profile-bio-section">
                            <?php if ($user['bio']): ?>
                                <p class="profile-bio"><?php echo htmlspecialchars($user['bio']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-metadata">
                            <div class="metadata-grid">
                                <?php if ($user['location']): ?>
                                    <div class="metadata-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($user['location']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($user['website']): ?>
                                    <div class="metadata-item">
                                        <i class="fas fa-globe"></i>
                                        <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank" class="metadata-link">
                                            Website
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="metadata-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                                </div>
                            </div>
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
                            
                        <div class="profile-actions">
                            <div class="action-buttons">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id): ?>
                                    <button class="btn <?php echo $is_following ? 'btn-outline-primary' : 'btn-primary'; ?> follow-btn" 
                                            data-user-id="<?php echo $user_id; ?>">
                                        <i class="fas fa-<?php echo $is_following ? 'user-minus' : 'user-plus'; ?>"></i>
                                        <span><?php echo $is_following ? 'Unfollow' : 'Follow'; ?></span>
                                    </button>
                                    
                                    <a href="followers.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-users"></i>
                                        <span>Followers</span>
                                    </a>
                                    
                                    <a href="following.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Following</span>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                                    <a href="../admin/profile_edit.php" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                    <a href="profile_guide.php" class="btn btn-outline-info">
                                        <i class="fas fa-question-circle"></i>
                                        <span>Profile Guide</span>
                                    </a>
                                    
                                    <a href="my_followers.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-users"></i>
                                        <span>My Followers</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="profile-content-area">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <div class="profile-sections-container">
                            <!-- Personal Information Section -->
                            <div class="profile-section-card">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Personal Information</span>
                                    </h6>
                                </div>
                                <div class="section-content user-details">
                                        <?php if ($user['email'] && (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id)): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['full_name'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-user text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['gender'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-venus-mars text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['gender'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['birth_date'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-birthday-cake text-muted me-2"></i>
                                                <span><?php echo date('F j, Y', strtotime($user['birth_date'])); ?> (<?php echo $user['age']; ?> years old)</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['phone'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-phone text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['location'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['location']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['address'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-home text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['address']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['city']) || !empty($user['state']) || !empty($user['country'])): ?>
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
                                        
                                        <?php if (!empty($user['website'])): ?>
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
                                        
                                        <?php if (!empty($user['last_post_date'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <span>Last active <?php echo date('M j, Y', strtotime($user['last_post_date'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        // Check if user has filled in basic personal information
                                        $has_basic_info = !empty($user['full_name']) || !empty($user['phone']) || !empty($user['location']) || 
                                                         !empty($user['address']) || !empty($user['city']) || !empty($user['state']) || 
                                                         !empty($user['country']) || !empty($user['website']);
                                        
                                        if (!$has_basic_info && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                                            <div class="detail-item mb-2">
                                                <a href="../admin/profile_edit.php" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-plus me-1"></i>Add Personal Information
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Education & Career Section -->
                            <div class="profile-section-card">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Education & Career</span>
                                    </h6>
                                </div>
                                <div class="section-content education-career">
                                        <?php if (!empty($user['education_level'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-graduation-cap text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['education_level'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['education_institution'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-university text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['education_institution']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['graduation_year'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                                <span>Graduated <?php echo $user['graduation_year']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['job_status'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-briefcase text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['job_status'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['job_title'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-user-tie text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['job_title']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['company'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-building text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['company']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['industry'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-industry text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['industry']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['years_experience'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <span><?php echo $user['years_experience']; ?> years experience</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        // Check if any education/career info exists
                                        $has_education_career = !empty($user['education_level']) || !empty($user['education_institution']) || 
                                                               !empty($user['graduation_year']) || !empty($user['job_status']) || 
                                                               !empty($user['job_title']) || !empty($user['company']) || 
                                                               !empty($user['industry']) || !empty($user['years_experience']);
                                        
                                        if (!$has_education_career): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-info-circle text-muted me-2"></i>
                                                <span class="text-muted">No education or career information available</span>
                                            </div>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                                                <div class="detail-item mb-2">
                                                    <a href="../admin/profile_edit.php" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-plus me-1"></i>Add Education & Career Info
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Personal Details Section -->
                            <div class="profile-section-card">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <i class="fas fa-heart"></i>
                                        <span>Personal Details</span>
                                    </h6>
                                </div>
                                <div class="section-content personal-details">
                                        <?php if (!empty($user['marital_status'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-heart text-muted me-2"></i>
                                                <span><?php echo ucfirst(str_replace('_', ' ', $user['marital_status'])); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['nationality'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-flag text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['nationality']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['religion'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-pray text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['religion']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['languages'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-language text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['languages']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['interests'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-star text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['interests']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['skills'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-tools text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['skills']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['hobbies'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-gamepad text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['hobbies']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($user['favorite_topics'])): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-bookmark text-muted me-2"></i>
                                                <span><?php echo htmlspecialchars($user['favorite_topics']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        // Check if any personal details exist
                                        $has_personal_details = !empty($user['marital_status']) || !empty($user['nationality']) || 
                                                               !empty($user['religion']) || !empty($user['languages']) || 
                                                               !empty($user['interests']) || !empty($user['skills']) || 
                                                               !empty($user['hobbies']) || !empty($user['favorite_topics']);
                                        
                                        if (!$has_personal_details): ?>
                                            <div class="detail-item mb-2">
                                                <i class="fas fa-info-circle text-muted me-2"></i>
                                                <span class="text-muted">No personal details available</span>
                                            </div>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                                                <div class="detail-item mb-2">
                                                    <a href="../admin/profile_edit.php" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-plus me-1"></i>Add Personal Details
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Sidebar -->
                    <div class="col-lg-4">
                        <div class="profile-sidebar">
                            <!-- Achievements & Activity Section -->
                            <div class="sidebar-section-card">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <i class="fas fa-trophy"></i>
                                        <span>Achievements & Activity</span>
                                    </h6>
                                </div>
                                <div class="section-content achievements">
                                    <?php if ($user['total_posts'] >= 10): ?>
                                        <div class="achievement-item">
                                            <i class="fas fa-medal"></i>
                                            <span>Prolific Writer (<?php echo $user['total_posts']; ?> posts)</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['total_likes_received'] >= 50): ?>
                                        <div class="achievement-item">
                                            <i class="fas fa-heart"></i>
                                            <span>Popular Content (<?php echo $user['total_likes_received']; ?> likes)</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['followers_count'] >= 20): ?>
                                        <div class="achievement-item">
                                            <i class="fas fa-users"></i>
                                            <span>Community Leader (<?php echo $user['followers_count']; ?> followers)</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['total_comments'] >= 30): ?>
                                        <div class="achievement-item">
                                            <i class="fas fa-comments"></i>
                                            <span>Engaged Member (<?php echo $user['total_comments']; ?> comments)</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!$user['total_posts'] && !$user['total_likes_received'] && !$user['followers_count'] && !$user['total_comments']): ?>
                                        <div class="achievement-item">
                                            <i class="fas fa-seedling"></i>
                                            <span>New Member - Start your journey!</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Statistics Section -->
                            <div class="sidebar-section-card">
                                <div class="section-header">
                                    <h6 class="section-title">
                                        <i class="fas fa-chart-bar"></i>
                                        <span>Statistics</span>
                                    </h6>
                                </div>
                                <div class="section-content">
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo $user['total_posts']; ?></div>
                                            <div class="stat-label">Posts</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo $user['followers_count']; ?></div>
                                            <div class="stat-label">Followers</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo $user['following_count']; ?></div>
                                            <div class="stat-label">Following</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

/* Enhanced Profile Layout */
.profile-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.profile-header-content {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-avatar-section {
    flex-shrink: 0;
}

.profile-avatar-wrapper {
    position: relative;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.5);
}

.profile-status-indicator {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    background: #28a745;
    border-radius: 50%;
    border: 3px solid white;
}

.profile-info-section {
    flex: 1;
}

.profile-name-section {
    margin-bottom: 1rem;
}

.profile-name {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.profile-full-name {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
}

.profile-bio-section {
    margin-bottom: 1.5rem;
}

.profile-bio {
    font-size: 1.1rem;
    line-height: 1.6;
    opacity: 0.95;
    margin: 0;
}

.profile-metadata {
    margin-bottom: 1.5rem;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.metadata-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.metadata-item i {
    font-size: 1.1rem;
    opacity: 0.8;
}

.metadata-link {
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.metadata-link:hover {
    color: #ffd700;
    text-decoration: underline;
}

.profile-actions {
    margin-top: 1.5rem;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.action-buttons .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

/* Main Content Area */
.profile-content-area {
    margin-top: 2rem;
}

.profile-sections-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.profile-section-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.profile-section-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #dee2e6;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    font-size: 1.2rem;
    animation: pulse 2s infinite;
}

.section-content {
    padding: 2rem;
}

/* Sidebar Styling */
.profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.sidebar-section-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-section-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Profile Layout Fixes */
.profile-section {
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.profile-section .user-details,
.profile-section .achievements,
.profile-section .education-career,
.profile-section .personal-details {
    flex: 1;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.profile-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3, #004085);
    border-radius: 0 0 10px 10px;
}

/* Detailed Information Styles */
.user-details, .achievements, .education-career, .personal-details {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    padding: 2rem;
    border-radius: 15px;
    border-left: 5px solid #007bff;
    margin-bottom: 1.5rem;
    flex: 1;
    min-height: 280px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.user-details::before, .achievements::before, .education-career::before, .personal-details::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 15px 15px 0 0;
}

.user-details:hover, .achievements:hover, .education-career:hover, .personal-details:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    transform: translateY(-5px) scale(1.02);
    border-left-width: 8px;
}

.user-details:hover::before, .achievements:hover::before, .education-career:hover::before, .personal-details:hover::before {
    height: 6px;
    transition: height 0.3s ease;
}

.detail-item, .achievement-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(233, 236, 239, 0.6);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    margin: 0.25rem 0;
    border-radius: 8px;
}

.detail-item:hover, .achievement-item:hover {
    background: linear-gradient(135deg, rgba(0,123,255,0.08) 0%, rgba(0,86,179,0.05) 100%);
    border-radius: 10px;
    padding: 1rem 1.5rem;
    margin: 0.5rem -0.5rem;
    box-shadow: 0 4px 15px rgba(0,123,255,0.15);
    transform: translateX(5px);
}

.detail-item:last-child, .achievement-item:last-child {
    border-bottom: none;
}

.detail-item i, .achievement-item i {
    width: 25px;
    text-align: center;
    font-size: 1.1rem;
    margin-right: 1rem;
    transition: all 0.3s ease;
}

.detail-item:hover i, .achievement-item:hover i {
    transform: scale(1.2);
    color: #007bff;
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
    background: linear-gradient(135deg, #ffffff 0%, #f8fff9 100%);
}

.achievements::before {
    background: linear-gradient(90deg, #28a745, #1e7e34, #155724);
}

.education-career {
    border-left-color: #17a2b8;
    background: linear-gradient(135deg, #ffffff 0%, #f8fcff 100%);
}

.education-career::before {
    background: linear-gradient(90deg, #17a2b8, #138496, #0f6674);
}

.personal-details {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #ffffff 0%, #fffef8 100%);
}

.personal-details::before {
    background: linear-gradient(90deg, #ffc107, #e0a800, #b8860b);
}

/* Section header improvements */
.profile-section h6 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 2rem;
    padding: 1rem 0;
    border-bottom: 3px solid rgba(233, 236, 239, 0.5);
    position: relative;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.profile-section h6::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3, #004085);
    border-radius: 0 0 10px 10px;
}

.profile-section h6::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 0 0 10px 10px;
}

.text-success.profile-section h6::after {
    background: linear-gradient(90deg, #28a745, #1e7e34, #155724);
}

.text-info.profile-section h6::after {
    background: linear-gradient(90deg, #17a2b8, #138496, #0f6674);
}

.text-warning.profile-section h6::after {
    background: linear-gradient(90deg, #ffc107, #e0a800, #b8860b);
}

.profile-section h6 i {
    margin-right: 0.75rem;
    font-size: 1.2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
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
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.follow-btn:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

/* Enhanced button styles */
.btn {
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
    background: linear-gradient(135deg, transparent 0%, rgba(0,123,255,0.1) 100%);
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-color: #007bff;
    color: white;
}

.btn-outline-warning {
    border: 2px solid #ffc107;
    color: #ffc107;
    background: linear-gradient(135deg, transparent 0%, rgba(255,193,7,0.1) 100%);
}

.btn-outline-warning:hover {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    border-color: #ffc107;
    color: white;
}

.btn-outline-info {
    border: 2px solid #17a2b8;
    color: #17a2b8;
    background: linear-gradient(135deg, transparent 0%, rgba(23,162,184,0.1) 100%);
}

.btn-outline-info:hover {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border-color: #17a2b8;
    color: white;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .profile-header-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .profile-name {
        font-size: 2rem;
    }
    
    .metadata-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0 1rem;
    }
    
    .profile-header-card {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
    }
    
    .profile-name {
        font-size: 1.8rem;
    }
    
    .profile-full-name {
        font-size: 1rem;
    }
    
    .profile-bio {
        font-size: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-buttons .btn {
        width: 100%;
        justify-content: center;
    }
    
    .profile-content-area {
        margin-top: 1.5rem;
    }
    
    .profile-sections-container {
        gap: 1.5rem;
    }
    
    .profile-section-card {
        margin-bottom: 1rem;
    }
    
    .section-header {
        padding: 1rem 1.5rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
    
    .section-content {
        padding: 1.5rem;
    }
    
    .profile-sidebar {
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        padding: 0.75rem;
    }
    
    .stat-item {
        padding: 1rem 0.75rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .detail-item, .achievement-item {
        padding: 0.75rem 0;
    }
    
    .user-details, .achievements, .education-career, .personal-details {
        min-height: auto;
        padding: 1.5rem;
    }
}

@media (max-width: 576px) {
    .profile-header-card {
        padding: 1rem;
        border-radius: 15px;
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
    }
    
    .profile-name {
        font-size: 1.5rem;
    }
    
    .metadata-item {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    
    .section-header {
        padding: 0.75rem 1rem;
    }
    
    .section-content {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-item {
        padding: 0.75rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
}

/* Enhanced card styling */
.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 1.5rem;
}

.card-body {
    padding: 2rem;
}

/* Profile header enhancements */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 30px 30px;
}

.profile-avatar {
    border: 5px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.5);
}

/* Additional layout improvements */
.row {
    align-items: stretch;
}

.col-md-6 {
    display: flex;
    flex-direction: column;
}

.profile-section {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.profile-section > div:last-child {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Enhanced animations and effects */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-section {
    animation: fadeInUp 0.6s ease-out;
}

.profile-section:nth-child(2) {
    animation-delay: 0.2s;
}

.profile-section:nth-child(3) {
    animation-delay: 0.4s;
}

.profile-section:nth-child(4) {
    animation-delay: 0.6s;
}

/* Glass morphism effect */
.user-details, .achievements, .education-career, .personal-details {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Floating animation for icons */
.detail-item i, .achievement-item i {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-3px); }
}

/* Gradient text effect */
.profile-section h6 {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Enhanced animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-header-card {
    animation: slideInUp 0.8s ease-out;
}

.profile-section-card {
    animation: slideInUp 0.8s ease-out;
}

.profile-section-card:nth-child(2) {
    animation-delay: 0.2s;
}

.profile-section-card:nth-child(3) {
    animation-delay: 0.4s;
}

.sidebar-section-card {
    animation: slideInUp 0.8s ease-out;
}

.sidebar-section-card:nth-child(2) {
    animation-delay: 0.3s;
}

/* Loading states */
.profile-section-card.loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Focus states for accessibility */
.profile-section-card:focus-within {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
}

/* Print styles */
@media print {
    .profile-header-card {
        background: white !important;
        color: black !important;
        box-shadow: none !important;
    }
    
    .profile-section-card,
    .sidebar-section-card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
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