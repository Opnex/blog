<?php
header('Content-Type: application/xml; charset=utf-8');
include 'includes/db.php';
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'])); ?>/</loc>
        <priority>1.0</priority>
    </url>
    <?php
    $stmt = $pdo->query("SELECT id, title, created_at FROM posts WHERE status = 'published'");
    while ($row = $stmt->fetch()) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $row['title']));
        $slug = trim($slug, '-');
        $url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . "/post/{$row['id']}/$slug";
        echo "<url>\n";
        echo "  <loc>" . htmlspecialchars($url) . "</loc>\n";
        echo "  <lastmod>" . date('Y-m-d', strtotime($row['created_at'])) . "</lastmod>\n";
        echo "  <priority>0.8</priority>\n";
        echo "</url>\n";
    }
    ?>
</urlset> 