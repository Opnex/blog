<?php
session_start();
// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Thomas Opeyemi Stephen';
$_SESSION['profile_picture'] = 'default.png';

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>All Dropdowns Test</h1>
    
    <div class="alert alert-success">
        <h4>✅ Testing All Dropdown Types</h4>
        <ul>
            <li>✅ Navbar user profile dropdown</li>
            <li>✅ Navbar notifications dropdown</li>
            <li>✅ Post share button dropdowns</li>
            <li>✅ All dropdowns should appear above content</li>
        </ul>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Test Instructions:</h3>
            <ol>
                <li><strong>Navbar Dropdowns:</strong> Click on "Thomas Opeyemi Stephen" or the bell icon</li>
                <li><strong>Share Button:</strong> Click on any "Share" button in the posts below</li>
                <li>All dropdowns should appear above other content</li>
                <li>Check browser console (F12) for debug messages</li>
            </ol>
        </div>
        <div class="col-md-6">
            <h3>Expected Results:</h3>
            <ul>
                <li>✅ All dropdowns visible above content</li>
                <li>✅ White background with blur effect</li>
                <li>✅ Proper positioning for each context</li>
                <li>✅ Z-index 99999 applied to all</li>
            </ul>
        </div>
    </div>
    
    <!-- Test Post Cards with Share Buttons -->
    <div class="mt-4">
        <h3>Test Post Cards:</h3>
        
        <div class="card mb-4 post-card shadow-lg border-0">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Test Post 1</h4>
                <p class="text-muted">This is a test post to verify share button dropdowns work properly.</p>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>Read More
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="position: relative; z-index: 99999;">
                                <i class="fas fa-share-alt me-1"></i>Share
                            </button>
                            <ul class="dropdown-menu" style="z-index: 99999 !important; position: absolute !important;">
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-facebook me-2"></i>Facebook
                                </a></li>
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </a></li>
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-linkedin me-2"></i>LinkedIn
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-link me-2"></i>Copy Link
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4 post-card shadow-lg border-0">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Test Post 2</h4>
                <p class="text-muted">Another test post to verify multiple share button dropdowns work.</p>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>Read More
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="position: relative; z-index: 99999;">
                                <i class="fas fa-share-alt me-1"></i>Share
                            </button>
                            <ul class="dropdown-menu" style="z-index: 99999 !important; position: absolute !important;">
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-facebook me-2"></i>Facebook
                                </a></li>
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </a></li>
                                <li><a class="dropdown-item" href="#" target="_blank">
                                    <i class="fab fa-linkedin me-2"></i>LinkedIn
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-link me-2"></i>Copy Link
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Additional debugging for all dropdowns
document.addEventListener('DOMContentLoaded', function() {
    console.log('Testing all dropdowns...');
    
    // Check all dropdown elements
    const allDropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Total dropdowns found:', allDropdowns.length);
    
    allDropdowns.forEach((dropdown, index) => {
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index} z-index:`, menu.style.zIndex);
            console.log(`Dropdown ${index} position:`, menu.style.position);
            console.log(`Dropdown ${index} context:`, dropdown.closest('.navbar-nav') ? 'navbar' : 'post-card');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 