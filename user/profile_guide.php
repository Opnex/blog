<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>How to Update Your Profile</h4>
                </div>
                <div class="card-body p-4">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Complete your profile</strong> to help others learn more about you and connect with like-minded people!
                    </div>
                    
                    <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Step 1: Access Profile Editor</h5>
                    <ol class="mb-4">
                        <li>Go to your profile page by clicking on your username in the navigation</li>
                        <li>Click the <strong>"Edit Profile"</strong> button</li>
                        <li>You'll be taken to a comprehensive profile editing form</li>
                    </ol>
                    
                    <h5 class="text-success mb-3"><i class="fas fa-list me-2"></i>Step 2: Fill in Your Information</h5>
                    <p class="mb-3">The profile editor is organized into several sections:</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-user me-2"></i>Full Name</li>
                                        <li><i class="fas fa-phone me-2"></i>Phone Number</li>
                                        <li><i class="fas fa-quote-left me-2"></i>Bio</li>
                                        <li><i class="fas fa-map-marker-alt me-2"></i>Location</li>
                                        <li><i class="fas fa-globe me-2"></i>Website</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-venus-mars me-2"></i>Gender</li>
                                        <li><i class="fas fa-birthday-cake me-2"></i>Birth Date</li>
                                        <li><i class="fas fa-heart me-2"></i>Marital Status</li>
                                        <li><i class="fas fa-home me-2"></i>Address Details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Education & Career</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-graduation-cap me-2"></i>Education Level</li>
                                        <li><i class="fas fa-university me-2"></i>Institution</li>
                                        <li><i class="fas fa-calendar me-2"></i>Graduation Year</li>
                                        <li><i class="fas fa-briefcase me-2"></i>Job Status</li>
                                        <li><i class="fas fa-user-tie me-2"></i>Job Title</li>
                                        <li><i class="fas fa-building me-2"></i>Company</li>
                                        <li><i class="fas fa-industry me-2"></i>Industry</li>
                                        <li><i class="fas fa-clock me-2"></i>Years Experience</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="fas fa-heart me-2"></i>Personal Details</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-flag me-2"></i>Nationality</li>
                                        <li><i class="fas fa-pray me-2"></i>Religion</li>
                                        <li><i class="fas fa-language me-2"></i>Languages</li>
                                        <li><i class="fas fa-star me-2"></i>Interests</li>
                                        <li><i class="fas fa-tools me-2"></i>Skills</li>
                                        <li><i class="fas fa-gamepad me-2"></i>Hobbies</li>
                                        <li><i class="fas fa-bookmark me-2"></i>Favorite Topics</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="text-info mb-3"><i class="fas fa-share-alt me-2"></i>Step 3: Social Media & Links</h5>
                    <p class="mb-3">Connect your social media profiles and professional links:</p>
                    <ul class="mb-4">
                        <li><strong>Personal Website:</strong> Your blog or portfolio</li>
                        <li><strong>LinkedIn:</strong> Professional networking</li>
                        <li><strong>GitHub:</strong> For developers and tech enthusiasts</li>
                        <li><strong>Twitter/Instagram:</strong> Social media handles</li>
                        <li><strong>Facebook:</strong> Personal social media</li>
                    </ul>
                    
                    <h5 class="text-success mb-3"><i class="fas fa-save me-2"></i>Step 4: Save Your Changes</h5>
                    <ol class="mb-4">
                        <li>Fill in as much information as you're comfortable sharing</li>
                        <li>Click the <strong>"Update Profile"</strong> button at the bottom</li>
                        <li>Your profile will be updated immediately</li>
                        <li>You can always come back and edit your information later</li>
                    </ol>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>Privacy Note:</strong> You control what information is visible on your public profile. Some fields are only visible to you when you're logged in.
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="../admin/profile_edit.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit me-2"></i>Start Editing Your Profile
                        </a>
                        <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-user me-2"></i>View My Profile
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 