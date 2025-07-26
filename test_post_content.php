<?php
include 'includes/db.php';

echo "<h2>Post Content Test</h2>";

// Get all posts with their content
$stmt = $pdo->query("SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

echo "<h3>All Posts:</h3>";
foreach ($posts as $post) {
    echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
    echo "<h4>Post ID: " . $post['id'] . " - " . htmlspecialchars($post['title']) . "</h4>";
    echo "<p><strong>Created:</strong> " . $post['created_at'] . "</p>";
    echo "<p><strong>Content Length:</strong> " . strlen($post['content']) . " characters</p>";
    
    // Check if content contains images
    if (strpos($post['content'], '<img') !== false) {
        echo "<p style='color:green;'>✅ Contains images</p>";
        
        // Extract image URLs
        preg_match_all('/<img[^>]+src="([^">]+)"/', $post['content'], $matches);
        if (!empty($matches[1])) {
            echo "<p><strong>Image URLs found:</strong></p>";
            echo "<ul>";
            foreach ($matches[1] as $url) {
                echo "<li>$url</li>";
                // Test if image is accessible
                $test_url = 'http://localhost' . $url;
                echo "<li>Full URL: $test_url</li>";
                echo "<li>Image preview: <img src='$url' style='max-width:100px;max-height:100px;' onerror='this.style.display=\"none\"; this.nextSibling.style.display=\"inline\";'><span style='color:red;display:none;'>❌ Image not found</span></li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:orange;'>❌ No images found</p>";
    }
    
    // Show first 200 characters of content
    echo "<p><strong>Content Preview:</strong></p>";
    echo "<div style='background:#f5f5f5; padding:10px; max-height:200px; overflow-y:auto;'>";
    echo htmlspecialchars(substr($post['content'], 0, 500));
    if (strlen($post['content']) > 500) {
        echo "... (truncated)";
    }
    echo "</div>";
    echo "</div>";
}
?> 