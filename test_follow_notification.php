<?php
session_start();
include 'includes/db.php';

echo "<h2>Follow Notification Test</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in first</p>";
    echo "<a href='auth/login.php'>Login</a>";
    exit;
}

// Get all users except current user
$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id != ? AND is_active = 1');
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

echo "<h3>Test Follow Notifications</h3>";
echo "<p>Current user: " . htmlspecialchars($_SESSION['username']) . " (ID: " . $_SESSION['user_id'] . ")</p>";

if (isset($_GET['test_follow'])) {
    $target_user_id = (int)$_GET['test_follow'];
    
    // Check if already following
    $stmt = $pdo->prepare('SELECT id FROM user_followers WHERE follower_id = ? AND following_id = ?');
    $stmt->execute([$_SESSION['user_id'], $target_user_id]);
    $already_following = $stmt->fetch();
    
    if ($already_following) {
        echo "<p style='color: orange;'>Already following this user</p>";
    } else {
        // Create follow relationship
        $stmt = $pdo->prepare('INSERT INTO user_followers (follower_id, following_id) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $target_user_id]);
        
        // Get follower username for notification
        $follower_username = $_SESSION['username'];
        
        // Create notification for the user being followed
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $target_user_id,
            'follow',
            $follower_username . ' started following you',
            'user/profile.php?user_id=' . $_SESSION['user_id']
        ]);
        
        echo "<p style='color: green;'>✅ Follow relationship created and notification sent!</p>";
    }
}

echo "<h3>Available Users to Follow:</h3>";
foreach ($users as $user) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
    echo "<strong>" . htmlspecialchars($user['username']) . "</strong> (ID: " . $user['id'] . ")";
    echo " <a href='?test_follow=" . $user['id'] . "' style='color: blue;'>[Test Follow]</a>";
    echo "</div>";
}

echo "<h3>Current Notifications:</h3>";
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

if ($notifications) {
    foreach ($notifications as $notif) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<strong>" . htmlspecialchars($notif['message']) . "</strong><br>";
        echo "<small>Type: " . $notif['type'] . " | Read: " . ($notif['is_read'] ? 'Yes' : 'No') . "</small><br>";
        echo "<small>Created: " . $notif['created_at'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>No notifications found</p>";
}

echo "<br><a href='index.php'>Back to Home</a>";
?> 