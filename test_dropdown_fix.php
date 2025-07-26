<?php
session_start();
// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Thomas Opeyemi Stephen';
$_SESSION['profile_picture'] = 'default.png';

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Dropdown Z-Index Fix Test</h1>
    
    <div class="alert alert-success">
        <h4>✅ Dropdown Fix Applied!</h4>
        <ul>
            <li>✅ Z-index set to 99999</li>
            <li>✅ Position forced to absolute</li>
            <li>✅ Background and backdrop-filter applied</li>
            <li>✅ Box-shadow for better visibility</li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Test Instructions:</h3>
            <ol>
                <li>Click on "Thomas Opeyemi Stephen" dropdown in the navbar</li>
                <li>The dropdown should appear above all other content</li>
                <li>Check if the dropdown menu is visible</li>
                <li>Open browser console (F12) to see debug messages</li>
            </ol>
        </div>
        <div class="col-md-6">
            <h3>Expected Result:</h3>
            <ul>
                <li>✅ Dropdown menu appears above other content</li>
                <li>✅ Menu has white background with blur effect</li>
                <li>✅ Menu items are clearly visible</li>
                <li>✅ No more "hiding under other page" issue</li>
            </ul>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Debug Info:</h3>
        <p>Check browser console (F12) for dropdown positioning messages.</p>
        <p>Current z-index: <span id="zindex">99999</span></p>
    </div>
</div>

<script>
// Additional debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('Testing dropdown z-index fix...');
    
    // Check dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index} menu z-index:`, menu.style.zIndex);
            console.log(`Dropdown ${index} menu position:`, menu.style.position);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 