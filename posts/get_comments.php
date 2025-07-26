<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit;
}

$post_id = (int)$_GET['post_id'];

try {
    // Get comments for the post
    $stmt = $pdo->prepare("
        SELECT comments.*, users.username, users.profile_picture 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? AND comments.is_approved = 1 
        ORDER BY comments.created_at ASC
    ");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll();
    
    // Format comments for JSON response
    $formatted_comments = [];
    foreach ($comments as $comment) {
        $formatted_comments[] = [
            'id' => $comment['id'],
            'content' => htmlspecialchars($comment['content']),
            'username' => htmlspecialchars($comment['username']),
            'profile_picture' => $comment['profile_picture'],
            'created_at' => date('M j, Y g:i A', strtotime($comment['created_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'comments' => $formatted_comments
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching comments: ' . $e->getMessage()
    ]);
}
?> 