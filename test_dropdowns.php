<?php
session_start();
include 'includes/header.php';
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-chevron-down fa-3x text-primary mb-3"></i>
                        <h2 class="mb-2">🔧 Dropdown Test Page</h2>
                        <p class="lead mb-0">Testing notification and profile dropdowns</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bell me-2"></i>Notification Dropdown
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Click Bell Icon:</strong> Should open notifications
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Shows Unread Count:</strong> Red badge with number
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>List Notifications:</strong> Recent notifications
                                        </li>
                                        <li class="mb-0">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Click Outside:</strong> Should close dropdown
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Profile Dropdown
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Click Profile Pic:</strong> Should open menu
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>My Profile:</strong> View own profile
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Dashboard:</strong> User dashboard
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Account Settings:</strong> Change password, profile
                                        </li>
                                        <li class="mb-0">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Logout:</strong> Sign out option
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Instructions -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Test Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-info">🔔 Notification Dropdown Test:</h6>
                            <ol>
                                <li>Look for the bell icon in the navbar</li>
                                <li>Click on the bell icon</li>
                                <li>Dropdown should open showing notifications</li>
                                <li>Click outside to close</li>
                                <li>Check console for dropdown status</li>
                            </ol>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">👤 Profile Dropdown Test:</h6>
                            <ol>
                                <li>Look for your profile picture in the navbar</li>
                                <li>Click on your profile picture</li>
                                <li>Dropdown should open with menu options</li>
                                <li>Click outside to close</li>
                                <li>Check console for dropdown status</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-info" onclick="testDropdowns()">
                            <i class="fas fa-test-tube me-2"></i>Test Dropdowns
                        </button>
                        <button class="btn btn-outline-info ms-2" onclick="checkConsole()">
                            <i class="fas fa-terminal me-2"></i>Check Console
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Info -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bug me-2"></i>Debug Information
                    </h5>
                </div>
                <div class="card-body">
                    <div id="debugInfo">
                        <p>Click "Test Dropdowns" to see debug information...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testDropdowns() {
    console.log('🧪 Testing dropdowns...');
    
    // Check for dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown');
    const notificationBtn = document.querySelector('.user-btn[title="Notifications"]');
    const profileBtn = document.querySelector('.user-btn[title="Profile"]');
    const notificationMenu = document.querySelector('.dropdown:first-child .dropdown-menu');
    const profileMenu = document.querySelector('.dropdown:last-child .dropdown-menu');
    
    const debugInfo = {
        dropdowns: dropdowns.length,
        notificationBtn: notificationBtn ? 'Found' : 'Missing',
        profileBtn: profileBtn ? 'Found' : 'Missing',
        notificationMenu: notificationMenu ? 'Found' : 'Missing',
        profileMenu: profileMenu ? 'Found' : 'Missing',
        bootstrap: typeof bootstrap !== 'undefined' ? 'Loaded' : 'Missing'
    };
    
    console.log('🔍 Debug Info:', debugInfo);
    
    // Update debug display
    document.getElementById('debugInfo').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Element Status:</h6>
                <ul>
                    <li>Dropdown containers: ${debugInfo.dropdowns}</li>
                    <li>Notification button: ${debugInfo.notificationBtn}</li>
                    <li>Profile button: ${debugInfo.profileBtn}</li>
                    <li>Notification menu: ${debugInfo.notificationMenu}</li>
                    <li>Profile menu: ${debugInfo.profileMenu}</li>
                    <li>Bootstrap: ${debugInfo.bootstrap}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Test Results:</h6>
                <div id="testResults">
                    <p>Click the buttons in the navbar to test...</p>
                </div>
            </div>
        </div>
    `;
    
    // Add click listeners for testing
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            setTimeout(() => {
                const isOpen = notificationMenu.classList.contains('show');
                console.log('🔔 Notification dropdown:', isOpen ? 'OPEN' : 'CLOSED');
                document.getElementById('testResults').innerHTML = `
                    <p class="text-${isOpen ? 'success' : 'info'}">
                        <i class="fas fa-${isOpen ? 'check' : 'info'}-circle"></i>
                        Notification dropdown: ${isOpen ? 'OPEN' : 'CLOSED'}
                    </p>
                `;
            }, 100);
        });
    }
    
    if (profileBtn) {
        profileBtn.addEventListener('click', function() {
            setTimeout(() => {
                const isOpen = profileMenu.classList.contains('show');
                console.log('👤 Profile dropdown:', isOpen ? 'OPEN' : 'CLOSED');
                document.getElementById('testResults').innerHTML = `
                    <p class="text-${isOpen ? 'success' : 'info'}">
                        <i class="fas fa-${isOpen ? 'check' : 'info'}-circle"></i>
                        Profile dropdown: ${isOpen ? 'OPEN' : 'CLOSED'}
                    </p>
                `;
            }, 100);
        });
    }
}

function checkConsole() {
    console.log('📋 Console check - Dropdown elements:');
    console.log('Dropdown containers:', document.querySelectorAll('.dropdown').length);
    console.log('User buttons:', document.querySelectorAll('.user-btn').length);
    console.log('Dropdown menus:', document.querySelectorAll('.dropdown-menu').length);
    console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
    
    alert('Check browser console (F12) for detailed information!');
}

// Auto-test on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🧪 Dropdown test page loaded!');
    setTimeout(testDropdowns, 1000);
});
</script>

<?php include 'includes/footer.php'; ?> 