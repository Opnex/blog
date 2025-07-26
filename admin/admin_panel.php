<?php
require_once '../includes/auth.php';
include '../includes/db.php';
if (!isAdmin()) {
    header('Location: dashboard.php');
    exit();
}
include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Admin Panel</h2>
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">Posts</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab">Comments</button>
        </li>
    </ul>
    <div class="tab-content mt-3" id="adminTabContent">
        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <h4>Users</h4>
            <table class="table table-bordered table-sm">
                <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th><th>Role</th><th>Actions</th></tr></thead>
                <tbody>
                <?php
                $users = $pdo->query('SELECT * FROM users')->fetchAll();
                foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['is_active'] ?? 1 ? 'Active' : 'Banned'; ?></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <?php if (!$user['is_admin']): ?>
                                <a href="admin_panel.php?action=promote&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Promote to Admin</a>
                            <?php else: ?>
                                <a href="admin_panel.php?action=demote&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Demote</a>
                            <?php endif; ?>
                            <?php if (($user['is_active'] ?? 1) == 1): ?>
                                <a href="admin_panel.php?action=ban&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger">Ban</a>
                            <?php else: ?>
                                <a href="admin_panel.php?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Posts Tab -->
        <div class="tab-pane fade" id="posts" role="tabpanel">
            <h4>Posts</h4>
            <table class="table table-bordered table-sm">
                <thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php
                $posts = $pdo->query('SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id')->fetchAll();
                foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo htmlspecialchars($post['status']); ?></td>
                        <td><?php echo htmlspecialchars($post['created_at']); ?></td>
                        <td><a href="admin_panel.php?action=delete_post&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Comments Tab -->
        <div class="tab-pane fade" id="comments" role="tabpanel">
            <h4>Comments</h4>
            <table class="table table-bordered table-sm">
                <thead><tr><th>ID</th><th>Content</th><th>Author</th><th>Post</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php
                $comments = $pdo->query('SELECT comments.*, users.username, posts.title AS post_title FROM comments JOIN users ON comments.user_id = users.id JOIN posts ON comments.post_id = posts.id')->fetchAll();
                foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo $comment['id']; ?></td>
                        <td><?php echo htmlspecialchars($comment['content']); ?></td>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['post_title']); ?></td>
                        <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                        <td><a href="admin_panel.php?action=delete_comment&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
// Handle actions (promote, demote, ban, activate, delete post/comment)
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    switch ($_GET['action']) {
        case 'promote':
            $pdo->prepare('UPDATE users SET is_admin = 1 WHERE id = ?')->execute([$id]);
            break;
        case 'demote':
            $pdo->prepare('UPDATE users SET is_admin = 0 WHERE id = ?')->execute([$id]);
            break;
        case 'ban':
            $pdo->prepare('UPDATE users SET is_active = 0 WHERE id = ?')->execute([$id]);
            break;
        case 'activate':
            $pdo->prepare('UPDATE users SET is_active = 1 WHERE id = ?')->execute([$id]);
            break;
        case 'delete_post':
            $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
            break;
        case 'delete_comment':
            $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
            break;
    }
    header('Location: admin_panel.php');
    exit();
}
include '../includes/footer.php'; 