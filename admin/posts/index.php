<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();

include '../../includes/db.php';
include '../../includes/header.php';

// Fetch all posts by the logged-in user
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>Your Posts</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Excerpt</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo htmlspecialchars(mb_strimwidth(strip_tags($post['content']), 0, 150, '...')); ?></td>
                <td>
                    <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($post['status']); ?>
                    </span>
                </td>
                <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                <td>
                                                <a href="/opnex_blog/posts/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                          <a href="/opnex_blog/admin/posts/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>