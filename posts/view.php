<?php
include '../includes/db.php';
$post_id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT posts.*, users.username, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ? AND posts.status = 'published'");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if ($post) {
    $meta_title = htmlspecialchars($post['title']) . ' - Opnex Blog';
    $meta_desc = htmlspecialchars(mb_strimwidth(strip_tags($post['content']), 0, 150, '...'));
}
include '../includes/header.php';

if (!$post) {
    header("Location: ../index.php?error=post_not_found");
    exit();
}

if (
    $post && isset($_SESSION['user_id']) && $_SESSION['user_id'] != $post['user_id']
    || $post && !isset($_SESSION['user_id'])
) {
    $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?')->execute([$post['id']]);
}
?>

<div class="container mt-5">
    <article>
        <div class="d-flex align-items-center mb-3">
            <img src="<?php echo !empty($post['profile_picture']) ? '/opnex_blog/assets/profile_pics/' . htmlspecialchars($post['profile_picture']) : '/opnex_blog/assets/profile_pics/default.png'; ?>" alt="Profile" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
            <div>
                <h1 class="mb-0"><?php echo htmlspecialchars($post['title']); ?></h1>
                <p class="text-muted mb-0">By <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
            </div>
        </div>
        <div class="post-content">
            <?php echo $post['content']; ?>
        </div>
        <div class="mt-3">
            <?php
            // Post like count and status
            $stmt2 = $pdo->prepare('SELECT COUNT(*) FROM post_likes WHERE post_id = ?');
            $stmt2->execute([$post['id']]);
            $like_count = $stmt2->fetchColumn();
            $liked = false;
            if (isset($_SESSION['user_id'])) {
                $stmt3 = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
                $stmt3->execute([$post['id'], $_SESSION['user_id']]);
                $liked = $stmt3->fetch() ? true : false;
            }
            ?>
            <button class="btn btn-outline-danger btn-sm like-btn" data-post-id="<?php echo $post['id']; ?>" <?php if (!isset($_SESSION['user_id'])) echo 'disabled'; ?>>
                <span class="like-icon"><?php echo $liked ? '♥' : '♡'; ?></span>
                <span class="like-count"><?php echo $like_count; ?></span> Like
            </button>
        </div>
    </article>

    <!-- Comments Section -->
    <div class="mt-5">
        <h3>Comments</h3>
        <?php
        // Fetch all comments for this post
        $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->execute([$post_id]);
        $all_comments = $stmt->fetchAll();
        // Build comment tree
        function buildTree(array $comments, $parent_id = null) {
            $branch = [];
            foreach ($comments as $comment) {
                if ($comment['parent_id'] == $parent_id) {
                    $children = buildTree($comments, $comment['id']);
                    if ($children) {
                        $comment['children'] = $children;
                    }
                    $branch[] = $comment;
                }
            }
            return $branch;
        }
        // Pagination for top-level comments
        $commentsPerPage = 5;
        $commentPage = isset($_GET['cpage']) ? max(1, (int)$_GET['cpage']) : 1;
        // Separate top-level and replies
        $top_level = array_filter($all_comments, function($c) { return !$c['parent_id']; });
        $totalTop = count($top_level);
        $totalCommentPages = ceil($totalTop / $commentsPerPage);
        $top_level_ids = array_column($top_level, 'id');
        $start = ($commentPage - 1) * $commentsPerPage;
        $paginated_ids = array_slice($top_level_ids, $start, $commentsPerPage);
        // Only show paginated top-level comments and their replies
        function filterTree($comments, $paginated_ids) {
            $filtered = [];
            foreach ($comments as $c) {
                if (!$c['parent_id'] && in_array($c['id'], $paginated_ids)) {
                    $filtered[] = $c;
                }
            }
            return $filtered;
        }
        $paginated_tree = buildTree($all_comments);
        $paginated_tree = filterTree($paginated_tree, $paginated_ids);
        // Render comment tree
        function renderComments($comments, $pdo, $level = 0) {
            foreach ($comments as $comment) {
                // Show all comments like Facebook (no approval needed)
                $canModerate = false;
                if (isset($_SESSION['user_id'])) {
                    $stmtPost = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
                    $stmtPost->execute([$_GET['id']]);
                    $postOwner = $stmtPost->fetchColumn();
                    $stmtAdmin = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
                    $stmtAdmin->execute([$_SESSION['user_id']]);
                    $is_admin = $stmtAdmin->fetchColumn();
                    $canModerate = ($is_admin == 1 || $postOwner == $_SESSION['user_id']);
                }
                // Like count and status
                $stmt2 = $pdo->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?');
                $stmt2->execute([$comment['id']]);
                $clike_count = $stmt2->fetchColumn();
                $cliked = false;
                if (isset($_SESSION['user_id'])) {
                    $stmt3 = $pdo->prepare('SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?');
                    $stmt3->execute([$comment['id'], $_SESSION['user_id']]);
                    $cliked = $stmt3->fetch() ? true : false;
                }
                echo '<div class="card mb-3 ms-' . ($level * 4) . '"><div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($comment['username']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($comment['content']) . '</p>';
                echo '<p class="text-muted small">' . date('M j, Y g:i a', strtotime($comment['created_at'])) . '</p>';
                echo '<button class="btn btn-outline-danger btn-sm comment-like-btn" data-comment-id="' . $comment['id'] . '"' . (!isset($_SESSION['user_id']) ? ' disabled' : '') . '>';
                echo '<span class="comment-like-icon">' . ($cliked ? '♥' : '♡') . '</span> ';
                echo '<span class="comment-like-count">' . $clike_count . '</span> Like</button>';
                if ($canModerate && !$comment['is_approved']) {
                    echo '<a href="/opnex_blog/posts/view.php?id=' . htmlspecialchars($_GET['id']) . '&approve_comment=' . $comment['id'] . '" class="btn btn-success btn-sm ms-2">Approve</a>';
                }
                if ($canModerate) {
                    echo '<a href="/opnex_blog/posts/view.php?id=' . htmlspecialchars($_GET['id']) . '&delete_comment=' . $comment['id'] . '" class="btn btn-danger btn-sm ms-2">Delete</a>';
                }
                if (isset($_SESSION['user_id'])) {
                    echo ' <button class="btn btn-link btn-sm reply-btn" data-comment-id="' . $comment['id'] . '">Reply</button>';
                    echo '<form action="process_comment.php" method="POST" class="reply-form mt-2 d-none" data-parent-id="' . $comment['id'] . '">';
                    echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($_GET['id']) . '">';
                    echo '<input type="hidden" name="parent_id" value="' . $comment['id'] . '">';
                    echo '<textarea name="content" class="form-control mb-2" rows="2" required placeholder="Write a reply..."></textarea>';
                    echo '<button type="submit" class="btn btn-primary btn-sm">Submit</button>';
                    echo '</form>';
                }
                if (!empty($comment['children'])) {
                    renderComments($comment['children'], $pdo, $level + 1);
                }
                echo '</div></div>';
            }
        }
        if ($paginated_tree) {
            renderComments($paginated_tree, $pdo);
        } else {
            echo '<p>No comments yet.</p>';
        }
        // Pagination links
        if ($totalCommentPages > 1) {
            echo '<nav aria-label="Comment navigation"><ul class="pagination justify-content-center mt-4">';
            for ($i = 1; $i <= $totalCommentPages; $i++) {
                $active = $i == $commentPage ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?id=' . htmlspecialchars($_GET['id']) . '&cpage=' . $i . '">' . $i . '</a></li>';
            }
            echo '</ul></nav>';
        }
        ?>

        <!-- Add Comment Form (for logged-in users) -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="process_comment.php" method="POST" class="mt-4">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Add Comment</label>
                    <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php else: ?>
            <p><a href="/opnex_blog/auth/login.php">Login</a> to post a comment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
// Post like
if (document.querySelector('.like-btn')) {
    document.querySelector('.like-btn').addEventListener('click', function(e) {
        e.preventDefault();
        var postId = this.getAttribute('data-post-id');
        var btnRef = this;
        fetch('like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'post_id=' + encodeURIComponent(postId)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btnRef.querySelector('.like-count').textContent = data.count;
                btnRef.querySelector('.like-icon').textContent = data.liked ? '♥' : '♡';
            }
        });
    });
}
// Comment like
if (document.querySelectorAll('.comment-like-btn').length) {
    document.querySelectorAll('.comment-like-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var commentId = this.getAttribute('data-comment-id');
            var btnRef = this;
            fetch('comment_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'comment_id=' + encodeURIComponent(commentId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btnRef.querySelector('.comment-like-count').textContent = data.count;
                    btnRef.querySelector('.comment-like-icon').textContent = data.liked ? '♥' : '♡';
                }
            });
        });
    });
}
// Reply button toggle
if (document.querySelectorAll('.reply-btn').length) {
    document.querySelectorAll('.reply-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var commentId = this.getAttribute('data-comment-id');
            var form = document.querySelector('.reply-form[data-parent-id="' + commentId + '"]');
            if (form) {
                form.classList.toggle('d-none');
                form.querySelector('textarea').focus();
            }
        });
    });
}
</script>
<?php
// Approve or delete comment logic
if (isset($_GET['approve_comment'])) {
    $cid = (int)$_GET['approve_comment'];
    $stmt = $pdo->prepare('UPDATE comments SET is_approved = 1 WHERE id = ?');
    $stmt->execute([$cid]);
    header('Location: view.php?id=' . $_GET['id']);
    exit();
}
if (isset($_GET['delete_comment'])) {
    $cid = (int)$_GET['delete_comment'];
    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
    $stmt->execute([$cid]);
    header('Location: view.php?id=' . $_GET['id']);
    exit();
}
// Mark notification as read
if (isset($_GET['notif_id']) && isset($_SESSION['user_id'])) {
    $nid = (int)$_GET['notif_id'];
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$nid, $_SESSION['user_id']]);
}
?>