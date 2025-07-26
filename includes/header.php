<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/db.php';
$base = '/opnex_blog'; // Change this if your project root is different
$current_page = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($meta_title) ? htmlspecialchars($meta_title) : 'Opnex Blog'; ?></title>
    <meta name="description" content="<?php echo isset($meta_desc) ? htmlspecialchars($meta_desc) : 'A modern blog platform built with PHP and MySQL.'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        /* Navbar Custom Styles */
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: #667eea !important;
            transform: scale(1.05);
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(102, 126, 234, 0.2);
            color: #fff !important;
        }

        /* Search Bar Styling */
        .navbar .input-group {
            border-radius: 25px;
            overflow: hidden;
        }
        
        .navbar .form-control {
            border-radius: 25px 0 0 25px;
            border: 1px solid rgba(255,255,255,0.2);
            border-right: none;
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .navbar .form-control:focus {
            background-color: rgba(255,255,255,0.15);
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            color: #fff;
        }

        .navbar .btn-outline-light {
            border-radius: 0 25px 25px 0;
            border: 1px solid rgba(255,255,255,0.3);
            border-left: none;
            color: #fff;
            transition: all 0.3s ease;
            background-color: rgba(255,255,255,0.1);
        }

        .navbar .btn-outline-light:hover {
            background-color: #667eea;
            border-color: #667eea;
            transform: translateY(-1px);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .theme-toggle:hover {
            background-color: rgba(255,255,255,0.1);
            transform: scale(1.1);
        }

        /* Notifications Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Dropdown Styling */
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.2rem;
        }

        .dropdown-item:hover {
            background-color: #667eea;
            color: #fff;
            transform: translateX(5px);
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(44, 62, 80, 0.95);
                border-radius: 12px;
                margin-top: 1rem;
                padding: 1rem;
                backdrop-filter: blur(10px);
            }
            
            .navbar-nav .nav-link {
                margin: 0.2rem 0;
                text-align: center;
            }
            
            .navbar .form-control {
                margin: 1rem 0;
            }
        }

        /* User Profile Section */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            border-color: #667eea;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand" href="<?php echo $base; ?>/index.php">
      <i class="fas fa-blog me-2"></i>Opnex Blog
    </a>
    
    <!-- Mobile Toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Main Navigation -->
      <ul class="navbar-nav me-auto">
        <?php if (!preg_match('#/auth/(login|register)\.php$#', $current_page)): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo $current_page === $base . '/index.php' ? 'active' : ''; ?>" href="<?php echo $base; ?>/index.php">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo strpos($current_page, '/admin/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/admin/dashboard.php">
            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo strpos($current_page, '/posts/create.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/posts/create.php">
            <i class="fas fa-pen me-1"></i>Create Post
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo strpos($current_page, '/user/bookmarks.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/user/bookmarks.php">
            <i class="fas fa-bookmark me-1"></i>Bookmarks
          </a>
        </li>
        <?php
        require_once __DIR__ . '/auth.php';
        if (function_exists('isAdmin') && isAdmin()): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo strpos($current_page, '/admin/admin_panel.php') !== false ? 'active' : ''; ?>" href="<?php echo $base; ?>/admin/admin_panel.php">
            <i class="fas fa-cog me-1"></i>Admin Panel
          </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>
      </ul>
      
              <!-- Search Bar -->
        <form class="d-flex me-3" action="<?php echo $base; ?>/search.php" method="GET">
          <div class="input-group" style="max-width: 300px;">
            <input class="form-control" type="search" name="q" placeholder="Search posts..." 
                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
            <button class="btn btn-outline-light" type="submit" style="border-left: none;">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      
      <!-- User Actions -->
      <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
        <?php
        // Notifications dropdown
        include_once __DIR__ . '/db.php';
        $stmtNotif = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
        $stmtNotif->execute([$_SESSION['user_id']]);
        $notifs = $stmtNotif->fetchAll();
        $unreadCount = 0;
        foreach ($notifs as $n) if (!$n['is_read']) $unreadCount++;
        ?>
        
        <!-- Notifications -->
        <li class="nav-item dropdown">
          <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            <?php if ($unreadCount > 0): ?>
              <span class="notification-badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width:300px;">
            <li class="dropdown-header">
              <i class="fas fa-bell me-2"></i>Notifications
            </li>
            <?php if ($notifs): foreach ($notifs as $notif): ?>
              <li>
                <a class="dropdown-item<?php if (!$notif['is_read']) echo ' fw-bold'; ?>" href="<?php echo $base . '/' . htmlspecialchars($notif['url']); ?>?notif_id=<?php echo $notif['id']; ?>">
                  <?php echo htmlspecialchars($notif['message']); ?>
                  <br><small class="text-muted"><?php echo date('M j, Y H:i', strtotime($notif['created_at'])); ?></small>
                </a>
              </li>
            <?php endforeach; else: ?>
              <li><span class="dropdown-item text-muted">No notifications</span></li>
            <?php endif; ?>
          </ul>
        </li>
        
        <!-- Theme Toggle -->
        <li class="nav-item">
          <button class="btn btn-outline-light theme-toggle" id="themeToggle">
            <i class="fas fa-moon" id="themeIcon"></i>
          </button>
        </li>
        
        <!-- User Profile -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php 
            $profile_pic = '';
            if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                $profile_pic = $base . '/assets/profile_pics/' . htmlspecialchars($_SESSION['profile_picture']);
            } else {
                $profile_pic = $base . '/assets/profile_pics/default.png';
            }
            ?>
            <img src="<?php echo $profile_pic; ?>" 
                 alt="Profile" class="user-avatar me-2" 
                 onerror="this.src='<?php echo $base; ?>/assets/profile_pics/default.png'">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
              <a class="dropdown-item" href="<?php echo $base; ?>/admin/profile.php">
                <i class="fas fa-user me-2"></i>Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="<?php echo $base; ?>/auth/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
              </a>
            </li>
          </ul>
        </li>
        
        <?php else: ?>
        <!-- Guest Actions -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base; ?>/auth/login.php">
            <i class="fas fa-sign-in-alt me-1"></i>Login
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base; ?>/auth/register.php">
            <i class="fas fa-user-plus me-1"></i>Register
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
