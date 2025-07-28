<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit;
}

if (isset($_GET['clear_all'])) {
    // Mark all notifications as read
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    
    $affected = $stmt->rowCount();
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;'>";
    echo "✅ Cleared $affected notifications!";
    echo "</div>";
}

// Get current notification count
$stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
$stmt->execute([$_SESSION['user_id']]);
$unread_count = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$total_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clear All Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #c82333; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔔 Clear All Notifications</h2>
        
        <div class="info">
            <p><strong>Current Status:</strong></p>
            <p>• Total notifications: <?php echo $total_count; ?></p>
            <p>• Unread notifications: <?php echo $unread_count; ?></p>
        </div>
        
        <?php if ($unread_count > 0): ?>
            <p>Click the button below to mark all notifications as read:</p>
            <a href="?clear_all=1" class="btn">🗑️ Clear All Notifications</a>
        <?php else: ?>
            <p style="color: #28a745;">✅ All notifications are already read!</p>
        <?php endif; ?>
        
        <p style="margin-top: 20px;">
            <a href="index.php">← Back to Home</a> | 
            <a href="test_notification_click.php">Test Notifications</a>
        </p>
    </div>
</body>
</html> 