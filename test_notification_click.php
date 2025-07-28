<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>🧪 Notification Click Test</h2>
    
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
                        <h5>📋 Test Instructions</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li><strong>Step 1:</strong> Click the bell icon in the header (top right)</li>
                            <li><strong>Step 2:</strong> Look for notifications in the dropdown</li>
                            <li><strong>Step 3:</strong> Click on one of the notifications</li>
                            <li><strong>Step 4:</strong> Watch the console (F12) for debug messages</li>
                            <li><strong>Step 5:</strong> Check if the badge count decreases</li>
                        </ol>
                        
                        <hr>
                        
                        <p><strong>Expected Console Messages:</strong></p>
                        <ul>
                            <li><code>🔔 Notification clicked!</code></li>
                            <li><code>📝 Marking notification as read...</code></li>
                            <li><code>✅ Notification marked as read successfully</code></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🔧 Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <button onclick="createTestNotification()" class="btn btn-primary mb-2">
                            <i class="fas fa-plus me-1"></i>Create Test Notification
                        </button>
                        
                        <br>
                        
                        <button onclick="testClickEvents()" class="btn btn-success mb-2">
                            <i class="fas fa-mouse-pointer me-1"></i>Test Click Events
                        </button>
                        
                        <br>
                        
                        <button onclick="checkNotificationCount()" class="btn btn-info">
                            <i class="fas fa-bell me-1"></i>Check Count
                        </button>
                        
                        <div id="test-results" class="mt-3">
                            <p>Click buttons to see results...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Current Status</h5>
                    </div>
                    <div class="card-body">
                        <div id="status-info">
                            <p>Loading status...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<script>
function createTestNotification() {
    fetch('/opnex_blog/debug_notifications.php?create_test=1')
    .then(response => response.text())
    .then(() => {
        document.getElementById('test-results').innerHTML = '<p class="text-success">✅ Test notification created! Refresh the page to see it.</p>';
        setTimeout(() => location.reload(), 1000);
    })
    .catch(error => {
        console.error('Error creating notification:', error);
        document.getElementById('test-results').innerHTML = '<p class="text-danger">❌ Error creating notification</p>';
    });
}

function testClickEvents() {
    const notificationLinks = document.querySelectorAll('.dropdown-item[href*="notif_id"]');
    document.getElementById('test-results').innerHTML = `
        <p><strong>Found ${notificationLinks.length} notification links</strong></p>
        <p>Click the bell icon to open dropdown and test clicking on notifications.</p>
    `;
    
    notificationLinks.forEach((link, index) => {
        console.log(`Notification ${index + 1}:`, link.href);
    });
}

function checkNotificationCount() {
    fetch('/opnex_blog/user/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.notification-badge');
        const badgeText = badge ? badge.textContent : 'No badge';
        
        document.getElementById('test-results').innerHTML = `
            <p><strong>Server Count:</strong> ${data.unread_count} unread</p>
            <p><strong>Badge Count:</strong> ${badgeText}</p>
        `;
    })
    .catch(error => {
        console.error('Error checking count:', error);
    });
}

// Update status on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('/opnex_blog/user/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.notification-badge');
        const badgeText = badge ? badge.textContent : 'No badge';
        
        document.getElementById('status-info').innerHTML = `
            <p><strong>Unread Notifications:</strong> ${data.unread_count}</p>
            <p><strong>Badge Display:</strong> ${badgeText}</p>
            <p><strong>Badge Visible:</strong> ${badge && badge.style.display !== 'none' ? 'Yes' : 'No'}</p>
        `;
    })
    .catch(error => {
        console.error('Error loading status:', error);
    });
});

// Add manual click test
document.addEventListener('click', function(e) {
    if (e.target.closest('.dropdown-item[href*="notif_id"]')) {
        console.log('🎯 Manual click detected on notification!');
        console.log('Target element:', e.target);
        console.log('Closest link:', e.target.closest('.dropdown-item[href*="notif_id"]'));
    }
});
</script>

<?php include 'includes/footer.php'; ?> 