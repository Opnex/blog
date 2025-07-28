<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/db.php';
$base = '/opnex_blog'; // Base path for Apache/XAMPP
$current_page = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($meta_title) ? htmlspecialchars($meta_title) : 'Opnex Blog'; ?></title>
    <meta name="description" content="<?php echo isset($meta_desc) ? htmlspecialchars($meta_desc) : 'A modern blog platform built with PHP and MySQL.'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Dark theme styles */
        [data-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --border-color: #404040;
        }
        
        [data-theme="dark"] body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        [data-theme="dark"] .card {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }
        
        [data-theme="dark"] .navbar-dark {
            background-color: var(--bg-secondary) !important;
        }
        
        [data-theme="dark"] .form-control {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        [data-theme="dark"] .form-control:focus {
            background-color: var(--bg-secondary);
            border-color: #007bff;
            color: var(--text-primary);
        }

        /* SIMPLE ROLL-BASED NAVBAR */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            overflow: visible;
        }

        .navbar .container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Brand Roll */
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
            color: #fff !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand:hover {
            color: #fff !important;
            opacity: 0.9;
        }

        .navbar-brand i {
            font-size: 1.5rem;
            color: #fff;
        }

        /* Navigation Roll */
        .nav-roll {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.4rem 0.8rem !important;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: #fff !important;
        }

        .nav-link i {
            font-size: 0.8rem;
            width: 1rem;
        }

        /* Search Roll */
        .search-roll {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-input {
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.3);
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.4rem 1rem;
            width: 200px;
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-input:focus {
            background-color: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.5);
            outline: none;
            color: #fff;
        }

        .search-btn {
            border-radius: 50%;
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255,255,255,0.3);
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .search-btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        /* User Roll */
        .user-roll {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .user-btn {
            border-radius: 50%;
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255,255,255,0.3);
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            position: relative;
        }

        .user-btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            font-size: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: #fff;
            margin-top: 0;
            min-width: 160px;
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            display: none;
            transform: translateY(0);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 0.4rem 0.8rem;
            transition: all 0.2s ease;
            border-radius: 4px;
            margin: 0.1rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            color: #333;
        }

        .dropdown-item:hover {
            background-color: #667eea;
            color: #fff;
        }

        .dropdown-item i {
            width: 0.8rem;
            font-size: 0.7rem;
        }

        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid rgba(0,0,0,.15);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.3rem 0;
            }
            
            .nav-roll {
                flex-direction: column;
                gap: 0.3rem;
                margin-top: 0.5rem;
            }
            
            .search-roll {
                margin: 0.5rem 0;
            }
            
            .search-input {
                width: 100%;
                max-width: 250px;
            }
            
            .user-roll {
                justify-content: center;
                margin-top: 0.5rem;
            }
        }

        /* Simple Layout */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Container Structure */
        .page-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Content Areas */
        .content-section {
            width: 100%;
            margin: 0;
            padding: 20px 0;
        }

        /* Fix floating content */
        .row {
            margin: 0;
            width: 100%;
        }

        .col-12, .col-md-6, .col-md-4 {
            padding: 0 15px;
        }

        /* Card containment */
        .card {
            width: 100%;
            margin-bottom: 20px;
        }

        /* Hero section fix */
        .hero-section {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            margin: 0;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <!-- Brand Roll -->
    <a class="navbar-brand" href="<?php echo $base; ?>/index.php">
      <i class="fas fa-blog"></i>
      <span>Opnex Blog</span>
    </a>
    
    <!-- Navigation Roll -->
    <div class="nav-roll">
      <?php if (!preg_match('#/auth/(login|register)\.php$#', $current_page)): ?>
      <a class="nav-link <?php echo $current_page === $base . '/index.php' ? 'active' : ''; ?>" href="<?php echo $base; ?>/index.php">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['user_id'])): ?>
      <a class="nav-link <?php echo strpos($current_page, '/admin/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/admin/dashboard.php">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
      <a class="nav-link <?php echo strpos($current_page, '/user/suggestions.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/user/suggestions.php">
        <i class="fas fa-lightbulb"></i>
        <span>Suggestions</span>
      </a>
      <a class="nav-link <?php echo strpos($current_page, '/user/bookmarks.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/user/bookmarks.php">
        <i class="fas fa-bookmark"></i>
        <span>Bookmarks</span>
      </a>
      <a class="nav-link <?php echo strpos($current_page, '/user/my_followers.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/user/my_followers.php">
        <i class="fas fa-users"></i>
        <span>My Followers</span>
      </a>
      <a class="nav-link <?php echo strpos($current_page, '/user/discover.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/user/discover.php">
        <i class="fas fa-search"></i>
        <span>Discover</span>
      </a>
      <?php
      require_once __DIR__ . '/auth.php';
      if (function_exists('isAdmin') && isAdmin()): ?>
      <a class="nav-link <?php echo strpos($current_page, '/admin/admin_panel.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/admin/admin_panel.php">
        <i class="fas fa-cog"></i>
        <span>Admin</span>
      </a>
      <?php endif; ?>
      <?php endif; ?>
    </div>
    
    <!-- User Roll -->
    <div class="user-roll">
      <?php if (isset($_SESSION['user_id'])): ?>
      <?php
      // Notifications
      include_once __DIR__ . '/db.php';
      $stmtNotif = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
      $stmtNotif->execute([$_SESSION['user_id']]);
      $notifs = $stmtNotif->fetchAll();
      $unreadCount = 0;
      foreach ($notifs as $n) if (!$n['is_read']) $unreadCount++;
      ?>
      
      <!-- Notifications -->
      <div class="dropdown">
        <button class="user-btn" data-bs-toggle="dropdown" title="Notifications">
          <i class="fas fa-bell"></i>
          <?php if ($unreadCount > 0): ?>
            <span class="notification-badge"><?php echo $unreadCount; ?></span>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><span class="dropdown-item">Notifications (<?php echo $unreadCount; ?> unread)</span></li>
          <?php if ($notifs): foreach ($notifs as $notif): ?>
            <li>
              <a class="dropdown-item" href="<?php echo $base . '/' . htmlspecialchars($notif['url']); ?>">
                <?php echo htmlspecialchars($notif['message']); ?>
              </a>
            </li>
          <?php endforeach; else: ?>
            <li><span class="dropdown-item">No notifications</span></li>
          <?php endif; ?>
        </ul>
      </div>
      
      <!-- Theme Toggle -->
      <button class="user-btn" id="themeToggle" title="Toggle Theme">
        <i class="fas fa-moon" id="themeIcon"></i>
      </button>
      
      <!-- User Profile -->
      <div class="dropdown">
        <button class="user-btn" data-bs-toggle="dropdown" title="Profile">
          <?php 
          $profile_pic = '';
          if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
              $profile_pic = $base . '/assets/profile_pics/' . htmlspecialchars($_SESSION['profile_picture']);
          } else {
              $profile_pic = $base . '/assets/profile_pics/default.png';
          }
          ?>
          <img src="<?php echo $profile_pic; ?>" 
               alt="Profile" class="user-avatar" 
               onerror="this.src='<?php echo $base; ?>/assets/profile_pics/default.png'">
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="<?php echo $base; ?>/user/profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">
              <i class="fas fa-user"></i>My Profile
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="<?php echo $base; ?>/admin/dashboard.php">
              <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="<?php echo $base; ?>/admin/profile.php">
              <i class="fas fa-cog"></i>Account Settings
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="<?php echo $base; ?>/auth/logout.php">
              <i class="fas fa-sign-out-alt"></i>Logout
            </a>
          </li>
        </ul>
      </div>
      
      <?php else: ?>
      <!-- Guest Actions -->
      <a class="nav-link" href="<?php echo $base; ?>/auth/login.php">
        <i class="fas fa-sign-in-alt"></i>
        <span>Login</span>
      </a>
      <a class="nav-link" href="<?php echo $base; ?>/auth/register.php">
        <i class="fas fa-user-plus"></i>
        <span>Register</span>
      </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Main Content Wrapper -->
<div class="main-content">
  <div class="page-container">

<script>
// Fix dropdown issues
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing dropdowns...');
    
    // Fix search button split
    const searchButtons = document.querySelectorAll('.navbar .btn-outline-light');
    searchButtons.forEach(btn => {
        btn.style.borderLeft = 'none';
        btn.style.marginLeft = '-1px';
    });

    // Ensure dropdowns work with proper z-index and positioning
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.zIndex = '99999';
        menu.style.position = 'absolute';
        menu.style.background = 'rgba(255,255,255,0.95)';
        menu.style.backdropFilter = 'blur(10px)';
        menu.style.borderRadius = '12px';
        menu.style.border = 'none';
        menu.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
        menu.style.minWidth = '200px';
        
        // Set positioning based on context
        if (menu.closest('.navbar-nav')) {
            menu.style.top = '100%';
            menu.style.right = '0';
            menu.style.left = 'auto';
        } else if (menu.closest('.post-card')) {
            menu.style.top = '100%';
            menu.style.left = '0';
            menu.style.right = 'auto';
        } else {
            menu.style.top = '100%';
            menu.style.left = '0';
            menu.style.right = 'auto';
        }
    });

    // Simple dropdown toggle function
    function toggleDropdown(toggle) {
        const dropdownMenu = toggle.nextElementSibling;
        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
            const isShown = dropdownMenu.classList.contains('show');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            dropdownMenu.classList.toggle('show');
            
            // Force positioning and z-index
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.style.zIndex = '99999';
                dropdownMenu.style.position = 'absolute';
                dropdownMenu.style.background = 'rgba(255,255,255,0.95)';
                dropdownMenu.style.backdropFilter = 'blur(10px)';
                dropdownMenu.style.borderRadius = '12px';
                dropdownMenu.style.border = 'none';
                dropdownMenu.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
                dropdownMenu.style.minWidth = '200px';
                dropdownMenu.style.display = 'block';
                
                // Set positioning based on context
                if (dropdownMenu.closest('.navbar-nav')) {
                    dropdownMenu.style.top = '100%';
                    dropdownMenu.style.right = '0';
                    dropdownMenu.style.left = 'auto';
                } else if (dropdownMenu.closest('.post-card')) {
                    dropdownMenu.style.top = '100%';
                    dropdownMenu.style.left = '0';
                    dropdownMenu.style.right = 'auto';
                } else {
                    dropdownMenu.style.top = '100%';
                    dropdownMenu.style.left = '0';
                    dropdownMenu.style.right = 'auto';
                }
                
                console.log('Dropdown shown with z-index:', dropdownMenu.style.zIndex);
            }
            
            console.log('Dropdown toggled:', !isShown);
        }
    }

    // Add click event listeners for dropdowns
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown(this);
        });
    });
    
    // Special handling for notification dropdown
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifDropdown) {
        notifDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Notification dropdown clicked');
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');
                console.log('Notification dropdown toggled:', dropdownMenu.classList.contains('show'));
                
                // Position the dropdown properly
                if (dropdownMenu.classList.contains('show')) {
                    const rect = this.getBoundingClientRect();
                    dropdownMenu.style.right = (window.innerWidth - rect.right) + 'px';
                    dropdownMenu.style.top = (rect.bottom + 10) + 'px';
                    
                    setTimeout(() => {
                        attachNotificationClickEvents();
                    }, 100);
                }
            }
        });
    }

    // Handle user dropdown positioning
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('User dropdown clicked');
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');
                console.log('User dropdown toggled:', dropdownMenu.classList.contains('show'));
                
                // Position the dropdown properly
                if (dropdownMenu.classList.contains('show')) {
                    const rect = this.getBoundingClientRect();
                    dropdownMenu.style.right = (window.innerWidth - rect.right) + 'px';
                    dropdownMenu.style.top = (rect.bottom + 10) + 'px';
                }
            }
        });
    }

    // Function to attach click events to notifications
    function attachNotificationClickEvents() {
        console.log('🔗 Attaching notification click events...');
        document.querySelectorAll('.dropdown-item[href*="notif_id"]').forEach(link => {
            // Remove existing listeners to prevent duplicates
            link.removeEventListener('click', handleNotificationClick);
            link.addEventListener('click', handleNotificationClick);
        });
        console.log('✅ Notification click events attached');
    }
    
    // Function to handle notification clicks
    function handleNotificationClick(e) {
        console.log('🔔 Notification clicked!');
        console.log('Link href:', this.href);
        
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
                    
                    // Update notification count immediately
                    updateNotificationCount();
                    
                    // Also force a page refresh after a short delay to ensure everything is updated
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
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
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    console.log('Dropdown initialization complete');
    

    
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
                        badge.style.visibility = 'visible';
                        console.log('📊 New badge count:', badge.textContent);
                    } else {
                        // Completely hide the badge when no notifications
                        badge.style.display = 'none';
                        badge.style.visibility = 'hidden';
                        badge.textContent = '0';
                        console.log('📊 Badge completely hidden (no notifications)');
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
                
                // If no notifications, update dropdown content
                if (data.unread_count === 0) {
                    const dropdownMenu = document.querySelector('.dropdown-menu');
                    if (dropdownMenu) {
                        // Remove all notification items
                        const notificationItems = dropdownMenu.querySelectorAll('.dropdown-item[href*="notif_id"]');
                        notificationItems.forEach(item => {
                            item.closest('li').remove();
                        });
                        
                        // Add "No notifications" message if not already present
                        if (!dropdownMenu.querySelector('.text-muted')) {
                            const noNotifications = document.createElement('li');
                            noNotifications.innerHTML = '<span class="dropdown-item text-muted">No notifications</span>';
                            dropdownMenu.appendChild(noNotifications);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('❌ Error updating notification count:', error);
        });
    }
    
    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        const notifDropdown = document.getElementById('notifDropdown');
        if (notifDropdown && notifDropdown.classList.contains('show')) {
            // Refresh notification content
            location.reload();
        }
    }, 30000);
});

        // Fix dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔧 Fixing dropdown functionality...');
            
            // Initialize Bootstrap dropdowns
            if (typeof bootstrap !== 'undefined') {
                // Initialize all dropdowns
                const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));
                console.log('✅ Bootstrap dropdowns initialized:', dropdownList.length);
            }
            
            // Manual dropdown toggle for user buttons
            document.querySelectorAll('.user-btn[data-bs-toggle="dropdown"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdown = this.closest('.dropdown');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    
                    // Toggle dropdown
                    if (menu.classList.contains('show')) {
                        menu.classList.remove('show');
                    } else {
                        // Close other dropdowns first
                        document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                            openMenu.classList.remove('show');
                        });
                        menu.classList.add('show');
                        
                        // Fix positioning
                        const buttonRect = this.getBoundingClientRect();
                        const navbarRect = document.querySelector('.navbar').getBoundingClientRect();
                        
                        // Position dropdown relative to button
                        menu.style.position = 'absolute';
                        menu.style.top = '100%';
                        menu.style.right = '0';
                        menu.style.left = 'auto';
                        menu.style.zIndex = '9999';
                        menu.style.marginTop = '0';
                        
                        // Ensure dropdown doesn't go outside viewport
                        const menuRect = menu.getBoundingClientRect();
                        if (menuRect.right > window.innerWidth) {
                            menu.style.right = '0';
                            menu.style.left = 'auto';
                        }
                        
                        console.log('🎯 Dropdown positioned and toggled');
                    }
                    
                    console.log('🎯 Dropdown toggled:', menu.classList.contains('show'));
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
            
            console.log('✅ Dropdown functionality fixed!');
        });
</script>
