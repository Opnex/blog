<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Create New Post (Simple Version)</h2>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> 
            <?php if ($_GET['error'] === 'creation_failed'): ?>
                Failed to create post. Please try again.
            <?php elseif ($_GET['error'] === 'empty_fields'): ?>
                Please fill in both title and content.
            <?php else: ?>
                An error occurred. Please try again.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <form action="process_create.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="10" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="published" selected>Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Post</button>
        <a href="/opnex_blog/index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Auto-hide error messages after 3 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.display = 'none';
    });
}, 3000);
</script> 