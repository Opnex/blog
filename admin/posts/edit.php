<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();

include '../../includes/db.php';
include '../../includes/header.php';

// Fetch the post to edit
$post_id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: ../dashboard.php?error=post_not_found");
    exit();
}
?>

<div class="container mt-5">
    <h2>Edit Post</h2>
    <form action="process_edit.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="/opnex_blog/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>