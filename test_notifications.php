<?php
include 'includes/db.php';

echo "<h2>Notifications System Test</h2>";

// Check if notifications table exists
$stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
if ($stmt->rowCount() > 0) {
    echo "<p style='color: green;'>✅ Notifications table exists</p>";
} else {
    echo "<p style='color: red;'>❌ Notifications table missing</p>";
}

// Check if user_followers table exists
$stmt = $pdo->query("SHOW TABLES LIKE 'user_followers'");
if ($stmt->rowCount() > 0) {
    echo "<p style='color: green;'>✅ User followers table exists</p>";
} else {
    echo "<p style='color: red;'>❌ User followers table missing</p>";
}

// Check current notifications
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll();
    
    echo "<h3>Your Notifications (" . count($notifications) . ")</h3>";
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
} else {
    echo "<p>Please log in to see notifications</p>";
}

// Test creating a notification
if (isset($_GET['test']) && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $_SESSION['user_id'],
        'test',
        'This is a test notification',
        'test_notifications.php'
    ]);
    echo "<p style='color: green;'>✅ Test notification created</p>";
    echo "<a href='test_notifications.php'>Refresh to see it</a>";
}
?>

<p><a href="?test=1">Create Test Notification</a></p>
<p><a href="index.php">Back to Home</a></p> 