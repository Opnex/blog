<?php
session_start();
include 'includes/db.php';

echo "<h2>Form Debug</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ User not logged in</p>";
    exit;
}

echo "<p>✅ User logged in: " . $_SESSION['username'] . " (ID: " . $_SESSION['user_id'] . ")</p>";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Form Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'published';
    
    echo "<h3>Processed Data:</h3>";
    echo "<p>Title: '" . htmlspecialchars($title) . "'</p>";
    echo "<p>Content: '" . htmlspecialchars($content) . "'</p>";
    echo "<p>Status: '" . htmlspecialchars($status) . "'</p>";
    
    if (empty($title) || empty($content)) {
        echo "<p>❌ Error: Title or content is empty</p>";
    } else {
        echo "<p>✅ Data looks good, would insert into database</p>";
    }
} else {
    echo "<p>No form data received</p>";
}

echo "<hr>";
echo "<h3>Test Form:</h3>";
?>

<form method="POST" action="debug_form.php">
    <div class="mb-3">
        <label>Title:</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Content:</label>
        <textarea name="content" class="form-control" rows="5" required></textarea>
    </div>
    <div class="mb-3">
        <label>Status:</label>
        <select name="status" class="form-control">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Test Submit</button>
</form>

<p><a href="posts/create.php">← Back to Create Post</a></p> 