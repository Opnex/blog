<?php
session_start();
include 'includes/db.php';

echo "<h2>Image Upload Debug Test</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ Please log in first</p>";
    exit;
}

echo "<p>✅ User logged in: " . $_SESSION['username'] . "</p>";

// Check upload directory
$upload_dir = 'assets/post_images/';
if (!is_dir($upload_dir)) {
    echo "<p>❌ Upload directory doesn't exist</p>";
    mkdir($upload_dir, 0777, true);
    echo "<p>✅ Created upload directory</p>";
} else {
    echo "<p>✅ Upload directory exists</p>";
}

// Test form
echo "<h3>Test Upload:</h3>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" accept="image/*" required>
    <button type="submit">Upload Test</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    echo "<h3>Upload Result:</h3>";
    
    $file = $_FILES['file'];
    echo "<p>File name: " . $file['name'] . "</p>";
    echo "<p>File size: " . $file['size'] . " bytes</p>";
    echo "<p>File type: " . $file['type'] . "</p>";
    echo "<p>Upload error: " . $file['error'] . "</p>";
    
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
            
            // Test JSON response like TinyMCE expects
            echo "<h4>JSON Response (like TinyMCE expects):</h4>";
            echo "<pre>" . json_encode(['location' => $url]) . "</pre>";
        } else {
            echo "<p>❌ Upload failed - move_uploaded_file returned false</p>";
            echo "<p>PHP error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p>❌ Invalid file type or size too large</p>";
    }
}
?> 