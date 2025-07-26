<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to bookmark posts']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$post_id = (int)$_POST['post_id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if already bookmarked
    $stmt = $pdo->prepare('SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ?');
    $stmt->execute([$user_id, $post_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove bookmark
        $stmt = $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND post_id = ?');
        $stmt->execute([$user_id, $post_id]);
        $action = 'removed';
    } else {
        // Add bookmark
        $stmt = $pdo->prepare('INSERT INTO bookmarks (user_id, post_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $post_id]);
        $action = 'added';
    }
    
    // Get updated bookmark count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookmarks WHERE post_id = ?');
    $stmt->execute([$post_id]);
    $bookmark_count = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'action' => $action,
        'bookmark_count' => $bookmark_count,
        'message' => $action === 'added' ? 'Post bookmarked!' : 'Bookmark removed!'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 