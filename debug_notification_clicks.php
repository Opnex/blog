<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>🔍 Notification Click Debug</h2>
    
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
                        <h5>🧪 Test Notification Clicks</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Step 1:</strong> Create test notifications</p>
                        <button onclick="createTestNotifications()" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create 5 Test Notifications
                        </button>
                        
                        <hr>
                        
                        <p><strong>Step 2:</strong> Test notification clicks</p>
                        <button onclick="testNotificationClicks()" class="btn btn-success">
                            <i class="fas fa-mouse-pointer me-1"></i>Test Click Events
                        </button>
                        
                        <hr>
                        
                        <p><strong>Step 3:</strong> Check notification count</p>
                        <button onclick="checkNotificationCount()" class="btn btn-info">
                            <i class="fas fa-bell me-1"></i>Check Count
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <div id="debug-info">
                            <p>Click the buttons to see debug information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🔧 Manual Test</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Manual Test Steps:</strong></p>
                        <ol>
                            <li>Click "Create 5 Test Notifications" above</li>
                            <li>Click the bell icon in the header (top right)</li>
                            <li>Click on one of the notifications in the dropdown</li>
                            <li>Watch the console for debug messages (F12 → Console)</li>
                            <li>Check if the badge count decreases</li>
                        </ol>
                        
                        <p><strong>Expected Behavior:</strong></p>
                        <ul>
                            <li>Notification should fade out when clicked</li>
                            <li>Badge count should decrease by 1</li>
                            <li>Console should show "Notification clicked" messages</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<script>
function createTestNotifications() {
    fetch('/opnex_blog/debug_notifications.php?create_test=1')
    .then(response => response.text())
    .then(() => {
        location.reload();
    })
    .catch(error => {
        console.error('Error creating notifications:', error);
    });
}

function testNotificationClicks() {
    const notificationLinks = document.querySelectorAll('.dropdown-item[href*="notif_id"]');
    console.log('Found notification links:', notificationLinks.length);
    
    notificationLinks.forEach((link, index) => {
        console.log(`Notification ${index + 1}:`, link.href);
        console.log(`Has click listener:`, link.onclick !== null);
    });
    
    document.getElementById('debug-info').innerHTML = `
        <p><strong>Found ${notificationLinks.length} notification links</strong></p>
        <p>Check browser console (F12) for detailed information</p>
    `;
}

function checkNotificationCount() {
    fetch('/opnex_blog/user/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('debug-info').innerHTML = `
            <p><strong>Server Count:</strong> ${data.unread_count} unread notifications</p>
            <p><strong>Badge Count:</strong> <span id="badge-count">Checking...</span></p>
        `;
        
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            document.getElementById('badge-count').textContent = badge.textContent;
        } else {
            document.getElementById('badge-count').textContent = 'No badge found';
        }
    })
    .catch(error => {
        console.error('Error checking count:', error);
    });
}

// Override the notification click handler for debugging
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-item[href*="notif_id"]').forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('🔔 Notification clicked!');
            console.log('Link href:', this.href);
            console.log('Link element:', this);
            
            const url = new URL(this.href);
            const notifId = url.searchParams.get('notif_id');
            console.log('Notification ID:', notifId);
            
            if (notifId) {
                console.log('📝 Marking notification as read...');
                
                // Add visual feedback
                this.style.opacity = '0.6';
                this.style.pointerEvents = 'none';
                
                fetch('/opnex_blog/user/mark_notification_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `notification_id=${notifId}`
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📝 Mark as read response:', data);
                    
                    if (data.success) {
                        console.log('✅ Notification marked as read successfully');
                        
                        // Remove bold styling and "New" badge
                        this.classList.remove('fw-bold');
                        const newBadge = this.querySelector('.badge');
                        if (newBadge) {
                            newBadge.remove();
                        }
                        
                        // Update notification count
                        updateNotificationCount();
                        
                        // Remove the notification item from the dropdown with animation
                        setTimeout(() => {
                            this.closest('li').style.transition = 'all 0.3s ease';
                            this.closest('li').style.opacity = '0';
                            this.closest('li').style.transform = 'translateX(-100%)';
                            
                            setTimeout(() => {
                                this.closest('li').remove();
                                console.log('🗑️ Notification removed from dropdown');
                                
                                // If no more notifications, show "No notifications" message
                                const notificationItems = document.querySelectorAll('.dropdown-item[href*="notif_id"]');
                                if (notificationItems.length === 0) {
                                    const dropdownMenu = document.querySelector('.dropdown-menu');
                                    if (dropdownMenu) {
                                        const noNotifications = document.createElement('li');
                                        noNotifications.innerHTML = '<span class="dropdown-item text-muted">No notifications</span>';
                                        dropdownMenu.appendChild(noNotifications);
                                    }
                                }
                            }, 300);
                        }, 200);
                    } else {
                        console.log('❌ Failed to mark notification as read');
                        // Restore if failed
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                    }
                })
                .catch(error => {
                    console.error('❌ Error marking notification as read:', error);
                    // Restore if failed
                    this.style.opacity = '1';
                    this.style.pointerEvents = 'auto';
                });
            }
        });
    });
});

// Function to update notification count from server
function updateNotificationCount() {
    console.log('🔄 Updating notification count...');
    fetch('/opnex_blog/user/get_notification_count.php')
    .then(response => response.json())
    .then(data => {
        console.log('📊 Server count response:', data);
        if (data.success) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                console.log('📊 Old badge count:', badge.textContent);
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'flex';
                    console.log('📊 New badge count:', badge.textContent);
                } else {
                    badge.style.display = 'none';
                    console.log('📊 Badge hidden (no notifications)');
                }
            } else {
                console.log('❌ No badge found');
            }
            
            // Update the dropdown header count
            const dropdownHeader = document.querySelector('.dropdown-header');
            if (dropdownHeader) {
                const headerText = dropdownHeader.innerHTML;
                dropdownHeader.innerHTML = headerText.replace(/\(\d+ unread\)/, `(${data.unread_count} unread)`);
                console.log('📊 Updated dropdown header');
            }
        }
    })
    .catch(error => {
        console.error('❌ Error updating notification count:', error);
    });
}
</script>

<?php include 'includes/footer.php'; ?> 