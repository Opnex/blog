<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic information
        $full_name = $_POST['full_name'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $location = $_POST['location'] ?? '';
        $website = $_POST['website'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Personal information
        $gender = $_POST['gender'] ?? null;
        $birth_date = $_POST['birth_date'] ?? null;
        $marital_status = $_POST['marital_status'] ?? null;
        
        // Address information
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $country = $_POST['country'] ?? '';
        $zip_code = $_POST['zip_code'] ?? '';
        
        // Education information
        $education_level = $_POST['education_level'] ?? null;
        $education_institution = $_POST['education_institution'] ?? '';
        $graduation_year = $_POST['graduation_year'] ?? null;
        
        // Career information
        $job_status = $_POST['job_status'] ?? null;
        $job_title = $_POST['job_title'] ?? '';
        $company = $_POST['company'] ?? '';
        $industry = $_POST['industry'] ?? '';
        $years_experience = $_POST['years_experience'] ?? null;
        
        // Personal details
        $nationality = $_POST['nationality'] ?? '';
        $religion = $_POST['religion'] ?? '';
        $languages = $_POST['languages'] ?? '';
        $interests = $_POST['interests'] ?? '';
        $skills = $_POST['skills'] ?? '';
        $hobbies = $_POST['hobbies'] ?? '';
        $favorite_topics = $_POST['favorite_topics'] ?? '';
        
        // Social media
        $personal_website = $_POST['personal_website'] ?? '';
        $linkedin_profile = $_POST['linkedin_profile'] ?? '';
        $github_profile = $_POST['github_profile'] ?? '';
        $twitter_handle = $_POST['twitter_handle'] ?? '';
        $instagram_handle = $_POST['instagram_handle'] ?? '';
        $facebook_profile = $_POST['facebook_profile'] ?? '';
        
        // Calculate age from birth date
        $age = null;
        if ($birth_date) {
            $birth = new DateTime($birth_date);
            $now = new DateTime();
            $age = $now->diff($birth)->y;
        }
        
        // Update user profile
        $stmt = $pdo->prepare("
            UPDATE users SET 
                full_name = ?, bio = ?, location = ?, website = ?, phone = ?,
                gender = ?, birth_date = ?, age = ?, marital_status = ?,
                address = ?, city = ?, state = ?, country = ?, zip_code = ?,
                education_level = ?, education_institution = ?, graduation_year = ?,
                job_status = ?, job_title = ?, company = ?, industry = ?, years_experience = ?,
                nationality = ?, religion = ?, languages = ?, interests = ?, skills = ?, 
                hobbies = ?, favorite_topics = ?, personal_website = ?, linkedin_profile = ?,
                github_profile = ?, twitter_handle = ?, instagram_handle = ?, facebook_profile = ?,
                last_profile_update = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $stmt->execute([
            $full_name, $bio, $location, $website, $phone,
            $gender, $birth_date, $age, $marital_status,
            $address, $city, $state, $country, $zip_code,
            $education_level, $education_institution, $graduation_year,
            $job_status, $job_title, $company, $industry, $years_experience,
            $nationality, $religion, $languages, $interests, $skills,
            $hobbies, $favorite_topics, $personal_website, $linkedin_profile,
            $github_profile, $twitter_handle, $instagram_handle, $facebook_profile,
            $user_id
        ]);
        
        $success_message = "Profile updated successfully!";
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
    } catch (Exception $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-success mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                        <option value="prefer_not_to_say" <?php echo ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                           value="<?php echo $user['birth_date'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-select" id="marital_status" name="marital_status">
                                        <option value="">Select Status</option>
                                        <option value="single" <?php echo ($user['marital_status'] ?? '') === 'single' ? 'selected' : ''; ?>>Single</option>
                                        <option value="married" <?php echo ($user['marital_status'] ?? '') === 'married' ? 'selected' : ''; ?>>Married</option>
                                        <option value="divorced" <?php echo ($user['marital_status'] ?? '') === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                                        <option value="widowed" <?php echo ($user['marital_status'] ?? '') === 'widowed' ? 'selected' : ''; ?>>Widowed</option>
                                        <option value="prefer_not_to_say" <?php echo ($user['marital_status'] ?? '') === 'prefer_not_to_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-info mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                           value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Education Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-warning mb-3"><i class="fas fa-graduation-cap me-2"></i>Education Information</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="education_level" class="form-label">Education Level</label>
                                    <select class="form-select" id="education_level" name="education_level">
                                        <option value="">Select Level</option>
                                        <option value="high_school" <?php echo ($user['education_level'] ?? '') === 'high_school' ? 'selected' : ''; ?>>High School</option>
                                        <option value="bachelor" <?php echo ($user['education_level'] ?? '') === 'bachelor' ? 'selected' : ''; ?>>Bachelor's Degree</option>
                                        <option value="master" <?php echo ($user['education_level'] ?? '') === 'master' ? 'selected' : ''; ?>>Master's Degree</option>
                                        <option value="phd" <?php echo ($user['education_level'] ?? '') === 'phd' ? 'selected' : ''; ?>>PhD</option>
                                        <option value="other" <?php echo ($user['education_level'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="education_institution" class="form-label">Institution</label>
                                    <input type="text" class="form-control" id="education_institution" name="education_institution" 
                                           value="<?php echo htmlspecialchars($user['education_institution'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="graduation_year" class="form-label">Graduation Year</label>
                                    <input type="number" class="form-control" id="graduation_year" name="graduation_year" 
                                           min="1950" max="<?php echo date('Y') + 10; ?>" 
                                           value="<?php echo $user['graduation_year'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Career Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-danger mb-3"><i class="fas fa-briefcase me-2"></i>Career Information</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="job_status" class="form-label">Job Status</label>
                                    <select class="form-select" id="job_status" name="job_status">
                                        <option value="">Select Status</option>
                                        <option value="employed" <?php echo ($user['job_status'] ?? '') === 'employed' ? 'selected' : ''; ?>>Employed</option>
                                        <option value="unemployed" <?php echo ($user['job_status'] ?? '') === 'unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                                        <option value="student" <?php echo ($user['job_status'] ?? '') === 'student' ? 'selected' : ''; ?>>Student</option>
                                        <option value="retired" <?php echo ($user['job_status'] ?? '') === 'retired' ? 'selected' : ''; ?>>Retired</option>
                                        <option value="self_employed" <?php echo ($user['job_status'] ?? '') === 'self_employed' ? 'selected' : ''; ?>>Self Employed</option>
                                        <option value="prefer_not_to_say" <?php echo ($user['job_status'] ?? '') === 'prefer_not_to_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="job_title" class="form-label">Job Title</label>
                                    <input type="text" class="form-control" id="job_title" name="job_title" 
                                           value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company</label>
                                    <input type="text" class="form-control" id="company" name="company" 
                                           value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="industry" class="form-label">Industry</label>
                                    <input type="text" class="form-control" id="industry" name="industry" 
                                           value="<?php echo htmlspecialchars($user['industry'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="years_experience" class="form-label">Years of Experience</label>
                                    <input type="number" class="form-control" id="years_experience" name="years_experience" 
                                           min="0" max="50" value="<?php echo $user['years_experience'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3"><i class="fas fa-heart me-2"></i>Personal Details</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" 
                                           value="<?php echo htmlspecialchars($user['nationality'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="religion" class="form-label">Religion</label>
                                    <input type="text" class="form-control" id="religion" name="religion" 
                                           value="<?php echo htmlspecialchars($user['religion'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="languages" class="form-label">Languages Spoken</label>
                                    <input type="text" class="form-control" id="languages" name="languages" 
                                           placeholder="e.g., English, Spanish, French" 
                                           value="<?php echo htmlspecialchars($user['languages'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interests" class="form-label">Interests</label>
                                    <input type="text" class="form-control" id="interests" name="interests" 
                                           placeholder="e.g., Technology, Travel, Music" 
                                           value="<?php echo htmlspecialchars($user['interests'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="skills" class="form-label">Skills</label>
                                    <input type="text" class="form-control" id="skills" name="skills" 
                                           placeholder="e.g., Programming, Design, Marketing" 
                                           value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hobbies" class="form-label">Hobbies</label>
                                    <input type="text" class="form-control" id="hobbies" name="hobbies" 
                                           placeholder="e.g., Reading, Gaming, Cooking" 
                                           value="<?php echo htmlspecialchars($user['hobbies'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="favorite_topics" class="form-label">Favorite Topics</label>
                                    <textarea class="form-control" id="favorite_topics" name="favorite_topics" rows="2" 
                                              placeholder="Topics you love to discuss or write about"><?php echo htmlspecialchars($user['favorite_topics'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-info mb-3"><i class="fas fa-share-alt me-2"></i>Social Media & Links</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="personal_website" class="form-label">Personal Website</label>
                                    <input type="url" class="form-control" id="personal_website" name="personal_website" 
                                           value="<?php echo htmlspecialchars($user['personal_website'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="linkedin_profile" class="form-label">LinkedIn Profile</label>
                                    <input type="url" class="form-control" id="linkedin_profile" name="linkedin_profile" 
                                           value="<?php echo htmlspecialchars($user['linkedin_profile'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="github_profile" class="form-label">GitHub Profile</label>
                                    <input type="url" class="form-control" id="github_profile" name="github_profile" 
                                           value="<?php echo htmlspecialchars($user['github_profile'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="twitter_handle" class="form-label">Twitter Handle</label>
                                    <input type="text" class="form-control" id="twitter_handle" name="twitter_handle" 
                                           placeholder="@username" value="<?php echo htmlspecialchars($user['twitter_handle'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instagram_handle" class="form-label">Instagram Handle</label>
                                    <input type="text" class="form-control" id="instagram_handle" name="instagram_handle" 
                                           placeholder="@username" value="<?php echo htmlspecialchars($user['instagram_handle'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facebook_profile" class="form-label">Facebook Profile</label>
                                    <input type="url" class="form-control" id="facebook_profile" name="facebook_profile" 
                                           value="<?php echo htmlspecialchars($user['facebook_profile'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="../user/profile.php?user_id=<?php echo $user_id; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card-header h4 {
    font-weight: 600;
}

.alert {
    border-radius: 8px;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-1px);
}
</style>

<?php include '../includes/footer.php'; ?> 