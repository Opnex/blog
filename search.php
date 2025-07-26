<?php
session_start();
include 'includes/db.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$results = [];

if (!empty($search)) {
    $where_conditions = ["(posts.title LIKE ? OR posts.content LIKE ?)"];
    $params = ["%$search%", "%$search%"];
    
    if ($category > 0) {
        $where_conditions[] = "posts.category_id = ?";
        $params[] = $category;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $sql = "SELECT posts.*, users.username, users.profile_picture, categories.name as category_name, categories.color as category_color
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            LEFT JOIN categories ON posts.category_id = categories.id
            WHERE posts.status = 'published' AND $where_clause
            ORDER BY posts.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <!-- Search Form -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-search me-2"></i>Search Posts
                    </h2>
                    
                    <form method="GET" action="search.php" class="search-form">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           name="q" 
                                           class="form-control form-control-lg" 
                                           placeholder="Search posts by title or content..."
                                           value="<?php echo htmlspecialchars($search); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select name="category" class="form-select form-select-lg">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (!empty($search)): ?>
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-list me-2"></i>
                    Search Results for "<?php echo htmlspecialchars($search); ?>"
                    <span class="badge bg-primary ms-2"><?php echo count($results); ?> posts</span>
                </h3>
                
                <?php if (empty($results)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No posts found</h4>
                        <p class="text-muted">Try adjusting your search terms or browse all categories.</p>
                        <a href="index.php" class="btn btn-primary">Browse All Posts</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results as $post): ?>
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
                                            
                                            // Show text excerpt with highlighted search terms
                                            $text_only = strip_tags($content);
                                            $excerpt = mb_strimwidth($text_only, 0, 150, '...');
                                            if (!empty($search)) {
                                                $excerpt = preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', $excerpt);
                                            }
                                            echo '<p class="text-muted">' . $excerpt . '</p>';
                                            ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="/opnex_blog/posts/view.php?id=<?php echo $post['id']; ?>" 
                                               class="btn btn-primary">
                                                <i class="fas fa-eye me-1"></i>Read More
                                            </a>
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
    <?php endif; ?>
</div>

<style>
.search-form .form-control:focus,
.search-form .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

mark {
    background-color: #fff3cd;
    color: #856404;
    padding: 2px 4px;
    border-radius: 3px;
}

.post-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>

<?php include 'includes/footer.php'; ?> 