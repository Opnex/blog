<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn(); // Redirect to login if not authenticated

include '../includes/db.php';
include '../includes/header.php';
?>

<!-- Add this near the top of the file (after auth checks) -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <!-- <div>
        <a href="/opnex_blog/admin/profile.php" class="btn btn-outline-primary me-2">Profile</a>
        <a href="/opnex_blog/auth/logout.php" class="btn btn-outline-danger">Logout</a>
    </div> -->
</div>

<div class="container mt-5">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <!-- Dashboard Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Posts</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Published</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'published'");
                        $stmt->execute([$_SESSION['user_id']]);
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Drafts</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'draft'");
                        $stmt->execute([$_SESSION['user_id']]);
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Comments</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- User's Posts Table -->
    <div class="mt-5">
        <h4>Your Posts</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Views</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT id, title, status, created_at, views FROM posts WHERE user_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$_SESSION['user_id']]);
                    while ($post = $stmt->fetch()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($post['status'])); ?></td>
                            <td><?php echo htmlspecialchars($post['created_at']); ?></td>
                            <td><?php echo (int)$post['views']; ?></td>
                            <td>
                                                               <a href="/opnex_blog/posts/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                               <a href="posts/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                               <a href="/opnex_blog/posts/view.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Most Viewed Posts & Most Active Commenters -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h5>Most Viewed Posts</h5>
            <ul class="list-group">
                <?php
                $stmt = $pdo->query("SELECT title, views FROM posts WHERE status = 'published' ORDER BY views DESC, created_at DESC LIMIT 5");
                foreach ($stmt as $row): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($row['title']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo (int)$row['views']; ?> views</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h5>Most Active Commenters</h5>
            <ul class="list-group">
                <?php
                $stmt = $pdo->query("SELECT users.username, COUNT(comments.id) AS ccount FROM users JOIN comments ON users.id = comments.user_id GROUP BY users.id ORDER BY ccount DESC LIMIT 5");
                foreach ($stmt as $row): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($row['username']); ?>
                        <span class="badge bg-success rounded-pill"><?php echo (int)$row['ccount']; ?> comments</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <a href="/opnex_blog/posts/create.php" class="btn btn-primary">Create New Post</a>
        <a href="/opnex_blog/admin/posts/" class="btn btn-secondary">Manage Posts</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>