<?php
include 'includes/db.php';

echo "<h2>Homepage Image Debug</h2>";

// Get all posts
$stmt = $pdo->query("SELECT id, title, content FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

foreach ($posts as $post) {
    echo "<div style='border:2px solid #ccc; margin:20px; padding:15px;'>";
    echo "<h3>Post ID: {$post['id']} - " . htmlspecialchars($post['title']) . "</h3>";
    
    $content = $post['content'];
    
    // Check if content contains img tags
    if (strpos($content, '<img') !== false) {
        echo "<p style='color:green;'>✅ Contains &lt;img&gt; tags</p>";
        
        // Try the old regex
        preg_match('/<img[^>]+src="([^">]+)"/', $content, $matches_old);
        echo "<p><strong>Old regex result:</strong> " . (empty($matches_old) ? 'No match' : $matches_old[1]) . "</p>";
        
        // Try the new regex
        preg_match('/<img[^>]+src=[\'\"]([^\'\"]+)[\'\"]/', $content, $matches_new);
        echo "<p><strong>New regex result:</strong> " . (empty($matches_new) ? 'No match' : $matches_new[1]) . "</p>";
        
        // Show all img tags
        preg_match_all('/<img[^>]+>/', $content, $all_imgs);
        echo "<p><strong>All img tags found:</strong></p>";
        echo "<ul>";
        foreach ($all_imgs[0] as $img_tag) {
            echo "<li>" . htmlspecialchars($img_tag) . "</li>";
        }
        echo "</ul>";
        
        // Test the URL processing
        if (!empty($matches_new[1])) {
            $img_url = $matches_new[1];
            echo "<p><strong>Original URL:</strong> $img_url</p>";
            
            // Process URL
            if (strpos($img_url, '/') !== 0 && strpos($img_url, 'http') !== 0) {
                $img_url = '/opnex_blog/' . ltrim($img_url, '/');
            }
            echo "<p><strong>Processed URL:</strong> $img_url</p>";
            
            // Test if image exists
            $file_path = str_replace('/opnex_blog/', '', $img_url);
            $file_path = ltrim($file_path, '/');
            echo "<p><strong>File path:</strong> $file_path</p>";
            echo "<p><strong>File exists:</strong> " . (file_exists($file_path) ? '✅ Yes' : '❌ No') . "</p>";
            
            // Show image preview
            echo "<p><strong>Image preview:</strong></p>";
            echo "<img src='$img_url' style='max-width:200px;max-height:150px;border:1px solid #ccc;' onerror='this.style.display=\"none\"; this.nextSibling.style.display=\"inline\";'><span style='color:red;display:none;'>❌ Image not found</span>";
        }
        
    } else {
        echo "<p style='color:orange;'>❌ No &lt;img&gt; tags found</p>";
    }
    
    // Show content preview
    echo "<p><strong>Content preview (first 300 chars):</strong></p>";
    echo "<div style='background:#f5f5f5; padding:10px; max-height:200px; overflow-y:auto; font-family:monospace;'>";
    echo htmlspecialchars(substr($content, 0, 300));
    if (strlen($content) > 300) {
        echo "... (truncated)";
    }
    echo "</div>";
    
    echo "</div>";
}
?> 