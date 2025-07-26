<?php
session_start();
include 'includes/db.php';

echo "<h2>Image Upload Test</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ Please log in first</p>";
    exit;
}

echo "<p>✅ User logged in: " . $_SESSION['username'] . "</p>";

// Check if assets/post_images directory exists
$upload_dir = 'assets/post_images/';
if (!is_dir($upload_dir)) {
    echo "<p>❌ Upload directory doesn't exist: $upload_dir</p>";
    mkdir($upload_dir, 0777, true);
    echo "<p>✅ Created upload directory</p>";
} else {
    echo "<p>✅ Upload directory exists: $upload_dir</p>";
}

// List existing images
echo "<h3>Existing Images:</h3>";
$images = glob($upload_dir . '*');
if ($images) {
    echo "<ul>";
    foreach ($images as $image) {
        echo "<li>$image</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No images found in upload directory</p>";
}

// Test form
echo "<h3>Test Image Upload:</h3>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_image" accept="image/*" required>
    <button type="submit">Upload Test Image</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h3>Upload Result:</h3>";
    $file = $_FILES['test_image'];
    echo "<p>File name: " . $file['name'] . "</p>";
    echo "<p>File size: " . $file['size'] . " bytes</p>";
    echo "<p>File type: " . $file['type'] . "</p>";
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($ext, $allowed) && $file['size'] <= 2*1024*1024) {
        $filename = 'test_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $url = '/opnex_blog/' . $upload_dir . $filename;
            echo "<p>✅ Upload successful!</p>";
            echo "<p>File saved to: $dest</p>";
            echo "<p>URL: $url</p>";
            echo "<p>Image preview:</p>";
            echo "<img src='$url' style='max-width:300px;max-height:200px;'>";
        } else {
            echo "<p>❌ Upload failed</p>";
        }
    } else {
        echo "<p>❌ Invalid file type or size too large</p>";
    }
}
?> 