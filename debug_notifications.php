<?php
session_start();
include 'includes/db.php';

echo "<h2>🔍 Notification System Debug</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ Not logged in. Please log in first.</p>";
    echo "<a href='auth/login.php'>Login</a>";
    exit;
}

echo "<p style='color: green;'>✅ Logged in as: " . htmlspecialchars($_SESSION['username']) . " (ID: " . $_SESSION['user_id'] . ")</p>";

// Check notifications table structure
echo "<h3>📋 Database Structure</h3>";
try {
    $stmt = $pdo->query("DESCRIBE notifications");
    $columns = $stmt->fetchAll();
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking table structure: " . $e->getMessage() . "</p>";
}

// Check current notifications
echo "<h3>🔔 Current Notifications</h3>";
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

if ($notifications) {
    echo "<p>Found " . count($notifications) . " notifications:</p>";
    foreach ($notifications as $notif) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background: " . ($notif['is_read'] ? '#f0f0f0' : '#fff3cd') . ";'>";
        echo "<strong>ID: " . $notif['id'] . "</strong><br>";
        echo "<strong>Message: " . htmlspecialchars($notif['message']) . "</strong><br>";
        echo "Type: " . $notif['type'] . " | Read: " . ($notif['is_read'] ? 'Yes' : 'No') . "<br>";
        echo "URL: " . $notif['url'] . "<br>";
        echo "Created: " . $notif['created_at'] . "<br>";
        echo "</div>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No notifications found for your user</p>";
}

// Test creating a notification
if (isset($_GET['create_test'])) {
    echo "<h3>🧪 Creating Test Notification</h3>";
    try {
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            'test',
            'This is a test notification created at ' . date('Y-m-d H:i:s'),
            'debug_notifications.php'
        ]);
        echo "<p style='color: green;'>✅ Test notification created successfully!</p>";
        echo "<a href='debug_notifications.php'>Refresh to see it</a>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating notification: " . $e->getMessage() . "</p>";
    }
}

// Test follow notification
if (isset($_GET['test_follow'])) {
    $target_user_id = (int)$_GET['test_follow'];
    echo "<h3>🧪 Testing Follow Notification</h3>";
    
    try {
        // Create follow relationship
        $stmt = $pdo->prepare('INSERT INTO user_followers (follower_id, following_id) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $target_user_id]);
        
        // Create notification
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, message, url) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $target_user_id,
            'follow',
            $_SESSION['username'] . ' started following you',
            'user/profile.php?user_id=' . $_SESSION['user_id']
        ]);
        
        echo "<p style='color: green;'>✅ Follow notification created for user ID: " . $target_user_id . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating follow notification: " . $e->getMessage() . "</p>";
    }
}

// Get all users for testing
$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id != ? AND is_active = 1');
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

echo "<h3>🧪 Test Actions</h3>";
echo "<p><a href='?create_test=1' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Create Test Notification</a></p>";

echo "<h4>Test Follow Notifications:</h4>";
foreach ($users as $user) {
    echo "<a href='?test_follow=" . $user['id'] . "' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin: 2px; display: inline-block;'>Follow " . htmlspecialchars($user['username']) . "</a> ";
}

echo "<h3>🔗 Quick Links</h3>";
echo "<p><a href='index.php'>Home</a> | <a href='user/my_followers.php'>My Followers</a> | <a href='test_notifications.php'>Simple Test</a></p>";

// Check notification count in header
echo "<h3>📊 Header Notification Count</h3>";
$stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
$stmt->execute([$_SESSION['user_id']]);
$unread_count = $stmt->fetchColumn();
echo "<p>Unread notifications: <strong>" . $unread_count . "</strong></p>";
echo "<p>This should match the number in the bell icon badge.</p>";
?> 