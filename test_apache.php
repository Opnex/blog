<?php
session_start();
// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Test User';
$_SESSION['profile_picture'] = 'default.png';

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Apache Test Page</h1>
    <p>This page is to test if the Apache/XAMPP setup is working properly.</p>
    
    <div class="alert alert-success">
        <h4>✅ Apache Setup Working!</h4>
        <ul>
            <li>✅ Base path: <?php echo $base; ?></li>
            <li>✅ Session working</li>
            <li>✅ Header included</li>
            <li>✅ Profile picture should be visible</li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Test Navigation</h3>
            <p>Try clicking these links:</p>
            <ul>
                <li><a href="<?php echo $base; ?>/index.php">Home</a></li>
                <li><a href="<?php echo $base; ?>/admin/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo $base; ?>/posts/create.php">Create Post</a></li>
                <li><a href="<?php echo $base; ?>/user/bookmarks.php">Bookmarks</a></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h3>Test Dropdowns</h3>
            <p>Click on the user profile dropdown in the navbar to test if it works.</p>
            <p>Click on the notifications dropdown to test if it works.</p>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Current Session Info:</h3>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 