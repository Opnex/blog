<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    echo '<div class="alert alert-danger">Invalid post ID.</div>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();
if (!$post) {
    echo '<div class="alert alert-danger">Post not found or you do not have permission to edit this post.</div>';
    exit;
}
?>
<div class="container mt-5">
    <h2>Edit Post</h2>
    <form action="../admin/posts/process_edit.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" id="content" rows="8" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="published" <?php if ($post['status'] === 'published') echo 'selected'; ?>>Published</option>
                <option value="draft" <?php if ($post['status'] === 'draft') echo 'selected'; ?>>Draft</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
                       <a href="/opnex_blog/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
        <script>
          tinymce.init({
            selector: '#content',
            menubar: false,
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
            height: 300,
            images_upload_url: '/opnex_blog/posts/upload_image.php',
            automatic_uploads: true,
            images_upload_credentials: true,
            relative_urls: false,
            remove_script_host: true,
            document_base_url: 'http://localhost/opnex_blog/',
            convert_urls: false,
            verify_html: false

          });
        </script>
