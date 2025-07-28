<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to follow users']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = (int)$_POST['user_id'];
$follower_id = $_SESSION['user_id'];
$action = $_POST['action'];

if ($user_id === $follower_id) {
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself']);
    exit;
}

try {
    if ($action === 'follow') {
        // Add follow relationship
        $stmt = $pdo->prepare('INSERT INTO user_followers (follower_id, following_id) VALUES (?, ?)');
        $stmt->execute([$follower_id, $user_id]);
        
        // Get follower username for notification
        $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
        $stmt->execute([$follower_id]);
        $follower_username = $stmt->fetchColumn();
        
        // Create notification for the user being followed
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $user_id,
            'follow',
            $follower_username . ' started following you',
            'user/profile.php?user_id=' . $follower_id
        ]);
        
        $message = 'User followed successfully!';
    } else {
        // Remove follow relationship
        $stmt = $pdo->prepare('DELETE FROM user_followers WHERE follower_id = ? AND following_id = ?');
        $stmt->execute([$follower_id, $user_id]);
        
        // Remove follow notification
        $stmt = $pdo->prepare('DELETE FROM notifications WHERE user_id = ? AND type = ? AND url LIKE ?');
        $stmt->execute([$user_id, 'follow', '%user_id=' . $follower_id]);
        
        $message = 'User unfollowed successfully!';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 