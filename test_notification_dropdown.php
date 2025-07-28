<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>🔔 Notification Dropdown Test</h2>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-warning">
            <strong>Not logged in!</strong> Please <a href="auth/login.php">login</a> to test notifications.
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <strong>Logged in as:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🧪 Test Actions</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Step 1:</strong> Create a test notification</p>
                        <a href="debug_notifications.php?create_test=1" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Test Notification
                        </a>
                        
                        <hr>
                        
                        <p><strong>Step 2:</strong> Test follow notifications</p>
                        <a href="test_follow_notification.php" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i>Test Follow Notifications
                        </a>
                        
                        <hr>
                        
                        <p><strong>Step 3:</strong> Check your notifications</p>
                        <a href="debug_notifications.php" class="btn btn-info">
                            <i class="fas fa-bell me-1"></i>Debug Notifications
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Current Status</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        include 'includes/db.php';
                        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ?');
                        $stmt->execute([$_SESSION['user_id']]);
                        $total_notifications = $stmt->fetchColumn();
                        
                        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
                        $stmt->execute([$_SESSION['user_id']]);
                        $unread_notifications = $stmt->fetchColumn();
                        ?>
                        
                        <p><strong>Total notifications:</strong> <?php echo $total_notifications; ?></p>
                        <p><strong>Unread notifications:</strong> <?php echo $unread_notifications; ?></p>
                        
                        <?php if ($unread_notifications > 0): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-1"></i>
                                You have <?php echo $unread_notifications; ?> unread notification(s). 
                                Check the bell icon in the header!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No unread notifications. Create some test notifications first.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🔧 Troubleshooting</h5>
                    </div>
                    <div class="card-body">
                        <h6>If notifications aren't working:</h6>
                        <ol>
                            <li>Click "Create Test Notification" above</li>
                            <li>Refresh this page to see if the count updates</li>
                            <li>Check the bell icon in the header (top right)</li>
                            <li>Click the bell icon to see the dropdown</li>
                            <li>If the dropdown doesn't work, check browser console for errors</li>
                        </ol>
                        
                        <h6>Browser Console Check:</h6>
                        <p>Press F12 to open developer tools, then check the Console tab for any JavaScript errors.</p>
                        
                        <button class="btn btn-secondary" onclick="testDropdown()">
                            <i class="fas fa-bell me-1"></i>Test Dropdown JavaScript
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<script>
function testDropdown() {
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifDropdown) {
        console.log('Notification dropdown found:', notifDropdown);
        const dropdownMenu = notifDropdown.nextElementSibling;
        if (dropdownMenu) {
            console.log('Dropdown menu found:', dropdownMenu);
            console.log('Dropdown classes:', dropdownMenu.className);
            console.log('Dropdown is shown:', dropdownMenu.classList.contains('show'));
        } else {
            console.log('No dropdown menu found');
        }
    } else {
        console.log('Notification dropdown not found');
    }
}

// Auto-refresh page every 10 seconds to check for new notifications
setTimeout(() => {
    location.reload();
}, 10000);
</script>

<?php include 'includes/footer.php'; ?> 