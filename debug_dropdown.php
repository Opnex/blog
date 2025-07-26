<?php
session_start();
// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Test User';
$_SESSION['profile_picture'] = 'default.png';

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Dropdown Debug Test</h1>
    
    <div class="alert alert-info">
        <h4>Testing Dropdown Functionality</h4>
        <p>Click on the user profile dropdown in the navbar to test if it works.</p>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Manual Dropdown Test</h3>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Test Dropdown
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Console Debug</h3>
            <p>Open browser console (F12) to see any JavaScript errors.</p>
            <button onclick="testDropdown()" class="btn btn-secondary">Test Dropdown Function</button>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Current Session:</h3>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
</div>

<script>
function testDropdown() {
    console.log('Testing dropdown functionality...');
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        console.log('✅ Bootstrap is loaded');
    } else {
        console.log('❌ Bootstrap is not loaded');
    }
    
    // Check dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        console.log(`Dropdown ${index}:`, dropdown);
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index} menu:`, menu);
        }
    });
}

// Add event listener to test dropdown clicks
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, testing dropdowns...');
    
    document.querySelectorAll('.dropdown-toggle').forEach((toggle, index) => {
        toggle.addEventListener('click', function(e) {
            console.log(`Dropdown ${index} clicked`);
            e.preventDefault();
            
            const menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                console.log('Toggling dropdown menu');
                menu.classList.toggle('show');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 