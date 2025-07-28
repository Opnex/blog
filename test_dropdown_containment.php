<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Dropdown Containment Test
                    </h4>
                </div>
                <div class="card-body">
                    <h5>🔧 Containment Fix Applied:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Position: relative</strong> - Navbar and nav items properly positioned
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Overflow: visible</strong> - Dropdowns can extend outside navbar
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Z-index: 9999</strong> - Dropdowns appear above content
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>JavaScript Positioning</strong> - Dynamic positioning within bounds
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Viewport Check</strong> - Ensures dropdowns don't overflow screen
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h5>🧪 Test Instructions:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Test the containment:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>🔔 Notification Bell:</strong> Click and check console for positioning</li>
                            <li><strong>👤 User Profile:</strong> Click and verify dropdown stays within bounds</li>
                            <li><strong>📱 Mobile Test:</strong> Resize browser to test mobile behavior</li>
                            <li><strong>🔄 Scroll Test:</strong> Scroll down and test dropdown positioning</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Check browser console (F12) for positioning logs</strong>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">What Should NOT Happen</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">❌ Dropdowns floating outside navbar</li>
                                        <li class="mb-2">❌ Dropdowns overlapping other content</li>
                                        <li class="mb-2">❌ Dropdowns going off-screen</li>
                                        <li class="mb-2">❌ Dropdowns appearing in wrong position</li>
                                        <li class="mb-0">❌ Dropdowns not responding to clicks</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">What Should Happen</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">✅ Dropdowns appear below their parent</li>
                                        <li class="mb-2">✅ Dropdowns stay within navbar bounds</li>
                                        <li class="mb-2">✅ Dropdowns are properly positioned</li>
                                        <li class="mb-2">✅ Dropdowns respond to clicks</li>
                                        <li class="mb-0">✅ Console shows positioning logs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                        <button class="btn btn-outline-warning" onclick="testContainment()">
                            <i class="fas fa-test me-1"></i>Test Containment
                        </button>
                        <button class="btn btn-outline-info" onclick="checkDropdowns()">
                            <i class="fas fa-search me-1"></i>Check Dropdowns
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test content with different heights -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Scroll and Position Test</h5>
                    <p>This content tests that dropdowns remain properly positioned when scrolling and at different viewport sizes.</p>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="alert alert-light">
                            <strong>Content Section <?php echo $i; ?></strong><br>
                            Height: <?php echo rand(50, 200); ?>px - This tests dropdown positioning at different scroll positions.
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testContainment() {
    console.log('🧪 Testing dropdown containment...');
    
    // Check navbar structure
    const navbar = document.querySelector('.navbar');
    const navbarNav = document.querySelector('.navbar-nav');
    
    console.log('Navbar position:', navbar.style.position);
    console.log('Navbar overflow:', navbar.style.overflow);
    console.log('Navbar-nav position:', navbarNav.style.position);
    
    // Check dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index} (${dropdown.id}):`, {
                position: menu.style.position,
                top: menu.style.top,
                right: menu.style.right,
                left: menu.style.left,
                zIndex: menu.style.zIndex,
                display: menu.style.display
            });
        }
    });
    
    // Test notification dropdown
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifDropdown) {
        console.log('Testing notification dropdown containment...');
        notifDropdown.click();
        
        setTimeout(() => {
            const menu = notifDropdown.nextElementSibling;
            if (menu) {
                const rect = menu.getBoundingClientRect();
                console.log('Notification dropdown bounds:', rect);
                console.log('Viewport width:', window.innerWidth);
                console.log('Dropdown right edge:', rect.right);
                console.log('Within bounds:', rect.right <= window.innerWidth);
            }
        }, 100);
    }
}

function checkDropdowns() {
    console.log('🔍 Checking dropdown structure...');
    
    // Check all dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown');
    console.log('Dropdown containers found:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        console.log(`Dropdown ${index}:`, {
            hasToggle: !!toggle,
            hasMenu: !!menu,
            toggleId: toggle?.id,
            menuClasses: menu?.className,
            dropdownPosition: dropdown.style.position
        });
    });
    
    // Check navbar structure
    const navbar = document.querySelector('.navbar');
    const container = navbar.querySelector('.container');
    const collapse = navbar.querySelector('.navbar-collapse');
    
    console.log('Navbar structure:', {
        navbarPosition: navbar.style.position,
        containerPosition: container.style.position,
        collapsePosition: collapse.style.position
    });
}

// Add debugging on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Dropdown containment test page loaded');
    
    // Check initial structure
    setTimeout(() => {
        checkDropdowns();
    }, 500);
});
</script>

<?php include 'includes/footer.php'; ?> 