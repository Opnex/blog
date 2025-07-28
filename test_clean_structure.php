<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Clean Structure Test
                    </h4>
                </div>
                <div class="card-body">
                    <h5>✨ Clean Structure Applied:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Simplified CSS:</strong> Removed all complex dropdown positioning
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Bootstrap Native:</strong> Using Bootstrap's built-in dropdown functionality
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Clean JavaScript:</strong> Minimal custom code, let Bootstrap handle it
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Better Organization:</strong> Cleaner, more maintainable code structure
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>No Floating Issues:</strong> Standard Bootstrap positioning
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h5>🧪 Test Instructions:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Test the clean dropdowns:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>🔔 Notification Bell:</strong> Click to test notification dropdown</li>
                            <li><strong>👤 User Profile:</strong> Click to test user dropdown</li>
                            <li><strong>📱 Mobile Test:</strong> Resize browser to test mobile behavior</li>
                            <li><strong>🔄 Refresh:</strong> Try refreshing the page to ensure stability</li>
                        </ul>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">What's Fixed</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">✅ No more complex CSS overrides</li>
                                        <li class="mb-2">✅ Standard Bootstrap dropdown behavior</li>
                                        <li class="mb-2">✅ Clean, maintainable code</li>
                                        <li class="mb-2">✅ Proper positioning without floating</li>
                                        <li class="mb-0">✅ Mobile responsive by default</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Benefits</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">✅ Easier to maintain</li>
                                        <li class="mb-2">✅ Better performance</li>
                                        <li class="mb-2">✅ Standard behavior</li>
                                        <li class="mb-2">✅ Less code to debug</li>
                                        <li class="mb-0">✅ Future-proof structure</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                        <a href="user/discover.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Test Discover
                        </a>
                        <button class="btn btn-outline-success" onclick="testDropdowns()">
                            <i class="fas fa-test me-1"></i>Test Dropdowns
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test content -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Scroll Test</h5>
                    <p>Scroll down to test that the clean structure works properly when scrolling.</p>
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="alert alert-light">
                            <strong>Content Section <?php echo $i; ?></strong><br>
                            This tests that the clean navbar structure remains stable when scrolling.
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testDropdowns() {
    console.log('Testing clean dropdown structure...');
    
    // Check Bootstrap availability
    if (typeof bootstrap !== 'undefined') {
        console.log('✅ Bootstrap is available');
        
        // Check dropdown elements
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        console.log('Found dropdowns:', dropdowns.length);
        
        dropdowns.forEach((dropdown, index) => {
            const menu = dropdown.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                console.log(`Dropdown ${index}:`, {
                    id: dropdown.id,
                    hasBootstrapData: dropdown.hasAttribute('data-bs-toggle'),
                    menuClasses: menu.className
                });
            }
        });
        
        // Test notification dropdown
        const notifDropdown = document.getElementById('notifDropdown');
        if (notifDropdown) {
            console.log('Testing notification dropdown...');
            notifDropdown.click();
        }
    } else {
        console.log('❌ Bootstrap not available');
    }
}

// Add some debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Clean structure test page loaded');
    
    // Check if dropdowns are properly structured
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Dropdown elements found:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        console.log(`Dropdown ${index}:`, {
            id: dropdown.id,
            classes: dropdown.className,
            hasDataToggle: dropdown.hasAttribute('data-bs-toggle')
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 