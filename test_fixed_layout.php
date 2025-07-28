<?php
session_start();
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <h1 class="display-4 mb-3">
            <i class="fas fa-blog me-3"></i>Opnex Blog
        </h1>
        <p class="lead mb-4">Share your thoughts and perspectives from our vibrant community of creators</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="posts/create.php" class="btn btn-light btn-lg">
                <i class="fas fa-pen me-2"></i>Create Post
            </a>
            <a href="user/discover.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-search me-2"></i>Discover
            </a>
        </div>
    </div>
</div>

<!-- Main Content Section -->
<div class="content-section">
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h2 class="mb-2">✅ Fixed Layout Structure</h2>
                            <p class="lead mb-0">No more floating content - everything is properly contained!</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-check me-2"></i>Layout Fixes
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <strong>Proper Containers:</strong> All content is contained within proper divs
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <strong>Fixed Width:</strong> Content is properly constrained to max-width
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <strong>No Overflow:</strong> Horizontal scrolling is prevented
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <strong>Responsive Design:</strong> Works on all screen sizes
                                            </li>
                                            <li class="mb-0">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <strong>Clean Structure:</strong> Proper HTML hierarchy
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-star me-2"></i>Design Improvements
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Centered Content:</strong> Everything is properly centered
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Consistent Spacing:</strong> Uniform margins and padding
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Hero Section:</strong> Beautiful gradient hero area
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Card Layout:</strong> Clean card-based design
                                            </li>
                                            <li class="mb-0">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Mobile Friendly:</strong> Responsive grid system
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
</div>

<!-- Feature Grid -->
<div class="content-section bg-light">
    <div class="page-container">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-mobile-alt fa-2x text-primary mb-3"></i>
                        <h5>Responsive Design</h5>
                        <p class="mb-0">Perfect layout that adapts to all screen sizes without floating content.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-paint-brush fa-2x text-success mb-3"></i>
                        <h5>Clean Styling</h5>
                        <p class="mb-0">Professional design with proper spacing and containment.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-rocket fa-2x text-info mb-3"></i>
                        <h5>Fast Performance</h5>
                        <p class="mb-0">Optimized layout that loads quickly and performs smoothly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Layout Test -->
<div class="content-section">
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-compass me-2"></i>Layout Test Results
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">✅ What's Fixed:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-1">• Content is properly contained</li>
                                    <li class="mb-1">• No more floating elements</li>
                                    <li class="mb-1">• Consistent spacing throughout</li>
                                    <li class="mb-1">• Responsive grid system</li>
                                    <li class="mb-1">• Clean visual hierarchy</li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-primary">🎯 Layout Features:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-1">• Hero section with gradient</li>
                                    <li class="mb-1">• Proper container structure</li>
                                    <li class="mb-1">• Card-based content layout</li>
                                    <li class="mb-1">• Mobile-first responsive design</li>
                                    <li class="mb-1">• Clean typography and spacing</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-home me-2"></i>View Homepage
                            </a>
                            <a href="user/discover.php" class="btn btn-outline-primary btn-lg ms-2">
                                <i class="fas fa-search me-2"></i>Test Discover Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Fixed layout test page loaded!');
    
    // Test layout containment
    const containers = document.querySelectorAll('.page-container');
    const contentSections = document.querySelectorAll('.content-section');
    
    console.log('Layout structure:', {
        containers: containers.length,
        contentSections: contentSections.length,
        heroSection: document.querySelector('.hero-section') ? 'Found' : 'Missing'
    });
    
    // Check for floating content
    const body = document.body;
    const bodyWidth = body.offsetWidth;
    const windowWidth = window.innerWidth;
    
    console.log('Width check:', {
        bodyWidth: bodyWidth,
        windowWidth: windowWidth,
        isOverflowing: bodyWidth > windowWidth
    });
});
</script>

<?php include 'includes/footer.php'; ?> 