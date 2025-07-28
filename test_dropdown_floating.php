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
                        <i class="fas fa-exclamation-triangle me-2"></i>Dropdown Floating Test
                    </h4>
                </div>
                <div class="card-body">
                    <h5>🔧 Floating Fix Applied:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Position Fixed:</strong> Dropdowns now use absolute positioning
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Proper Containment:</strong> Dropdowns stay within navbar bounds
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Z-Index Fixed:</strong> Dropdowns appear above other content
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>No Floating:</strong> Dropdowns are anchored to their parent elements
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h5>🧪 Test Instructions:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Click these elements to test:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>🔔 Notification Bell:</strong> Should show dropdown properly positioned</li>
                            <li><strong>👤 User Profile:</strong> Should show dropdown without floating</li>
                            <li><strong>📱 Mobile View:</strong> Resize browser to test mobile responsiveness</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>If dropdowns still float:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Check browser console for errors</li>
                            <li>Try refreshing the page</li>
                            <li>Clear browser cache</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                        <a href="user/discover.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Test Discover
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add content to test scrolling -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Scroll Test</h5>
                    <p>Scroll down to test that dropdowns don't interfere with content.</p>
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="alert alert-light">
                            <strong>Content Section <?php echo $i; ?></strong><br>
                            This tests that dropdowns remain properly positioned when scrolling.
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Debug dropdown positioning
document.addEventListener('DOMContentLoaded', function() {
    console.log('Testing dropdown positioning...');
    
    // Check dropdown elements
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);
    
    dropdowns.forEach((dropdown, index) => {
        const menu = dropdown.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            console.log(`Dropdown ${index} position:`, menu.style.position);
            console.log(`Dropdown ${index} z-index:`, menu.style.zIndex);
            console.log(`Dropdown ${index} top:`, menu.style.top);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 