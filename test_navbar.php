<?php
session_start();
// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Test User';
$_SESSION['profile_picture'] = 'default.png';

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Navbar Test Page</h1>
    <p>This page is to test if the navbar fixes are working properly.</p>
    
    <div class="alert alert-info">
        <h4>Test Results:</h4>
        <ul>
            <li>✅ Default profile image should be visible</li>
            <li>✅ Dropdown menus should work</li>
            <li>✅ Search button should not be split</li>
            <li>✅ Profile picture should not be floating</li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Test Dropdowns</h3>
            <p>Click on the user profile dropdown in the navbar to test if it works.</p>
        </div>
        <div class="col-md-6">
            <h3>Test Search</h3>
            <p>The search button in the navbar should appear seamless without splitting.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 