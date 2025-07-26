<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Create New Post</h2>
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
            <textarea name="content" class="form-control" id="content" rows="10"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <?php
                $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
                foreach ($categories as $category):
                ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="published" selected>Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Post</button>
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
          
          // Ensure TinyMCE content is submitted with the form and validate
          document.querySelector('form').addEventListener('submit', function(e) {
            tinymce.triggerSave();
            var text_content = tinymce.get('content').getContent({format: 'text'}).trim();
            if (!text_content) {
              alert('Please enter some content for your post.');
              tinymce.get('content').focus();
              e.preventDefault();
              return false;
            }
          });
        </script>
        
        <script>
        // Auto-hide error messages after 3 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 3000);
        </script>