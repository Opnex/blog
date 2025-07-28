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
                        <i class="fas fa-check-circle me-2"></i>Navbar Responsive Test
                    </h4>
                </div>
                <div class="card-body">
                    <h5>✅ Navbar Fixes Applied:</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Sticky Navbar:</strong> Navbar now sticks to the top when scrolling
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Responsive Layout:</strong> Mobile-friendly navigation with proper spacing
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Content Wrapper:</strong> Main content properly wrapped to prevent floating
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Search Bar:</strong> Responsive search bar that adapts to screen size
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>User Actions:</strong> Notifications and profile dropdown properly positioned
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h5>📱 Test Responsive Behavior:</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Resize your browser window to test mobile view</li>
                            <li>Click the hamburger menu on mobile to see navigation</li>
                            <li>Scroll down to see the sticky navbar behavior</li>
                            <li>Test the search bar and dropdowns on different screen sizes</li>
                        </ul>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Desktop View</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">✅ Horizontal navigation</p>
                                    <p class="mb-2">✅ Search bar on the right</p>
                                    <p class="mb-2">✅ User actions visible</p>
                                    <p class="mb-0">✅ All menu items accessible</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Mobile View</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">✅ Collapsible hamburger menu</p>
                                    <p class="mb-2">✅ Full-width search bar</p>
                                    <p class="mb-2">✅ Stacked navigation items</p>
                                    <p class="mb-0">✅ Touch-friendly buttons</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                        <a href="user/discover.php" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Test Discover Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add some content to test scrolling -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Scroll Test Content</h5>
                    <p>This content is here to test the sticky navbar behavior. Try scrolling down to see the navbar stay at the top.</p>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="alert alert-light">
                            <strong>Section <?php echo $i; ?></strong><br>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 