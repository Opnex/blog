<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-check me-2"></i>Simple Dropdown Test
                    </h4>
                </div>
                <div class="card-body">
                    <h5>🔧 Simple Dropdown Fix Applied:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Position: absolute</strong> - Dropdowns use absolute positioning
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Top: 100%</strong> - Dropdowns appear below their parent
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Right: 0</strong> - Dropdowns align to the right
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Z-index: 9999</strong> - Dropdowns appear above content
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Display: none/block</strong> - Simple show/hide logic
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h5>🧪 Test Instructions:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Click these elements in the navbar:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>🔔 Notification Bell:</strong> Should show dropdown below the bell</li>
                            <li><strong>👤 User Profile:</strong> Should show dropdown below the profile</li>
                            <li><strong>📱 Mobile Test:</strong> Resize browser to test mobile behavior</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>If dropdowns still don't work:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Check browser console (F12) for errors</li>
                            <li>Try refreshing the page (Ctrl+F5)</li>
                            <li>Clear browser cache</li>
                            <li>Check if Bootstrap JS is loading properly</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                        <button class="btn btn-outline-primary" onclick="testDropdown()">
                            <i class="fas fa-test me-1"></i>Test Dropdown
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
                    <p>Scroll down to test that dropdowns work properly when scrolling.</p>
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="alert alert-light">
                            <strong>Content Section <?php echo $i; ?></strong><br>
                            This tests that dropdowns remain functional when scrolling.
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testDropdown() {
    console.log('Testing dropdown functionality...');
    
    // Check if dropdowns exist
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index}:`, {
                position: menu.style.position,
                top: menu.style.top,
                right: menu.style.right,
                zIndex: menu.style.zIndex,
                display: menu.style.display
            });
        }
    });
    
    // Try to open notification dropdown
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifDropdown) {
        console.log('Clicking notification dropdown...');
        notifDropdown.click();
    }
}

// Add some debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('Simple dropdown test page loaded');
    
    // Check Bootstrap
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap is loaded');
    } else {
        console.log('Bootstrap not found');
    }
    
    // Check dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Dropdown elements found:', dropdowns.length);
});
</script>

<?php include 'includes/footer.php'; ?> 