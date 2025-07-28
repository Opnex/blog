-- Add detailed profile fields to users table
-- Run this script to add new profile information fields

-- Add new profile fields to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL,
ADD COLUMN IF NOT EXISTS birth_date DATE NULL,
ADD COLUMN IF NOT EXISTS age INT NULL,
ADD COLUMN IF NOT EXISTS education_level ENUM('high_school', 'bachelor', 'master', 'phd', 'other') NULL,
ADD COLUMN IF NOT EXISTS education_institution VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS graduation_year INT NULL,
ADD COLUMN IF NOT EXISTS marital_status ENUM('single', 'married', 'divorced', 'widowed', 'prefer_not_to_say') NULL,
ADD COLUMN IF NOT EXISTS job_status ENUM('employed', 'unemployed', 'student', 'retired', 'self_employed', 'prefer_not_to_say') NULL,
ADD COLUMN IF NOT EXISTS job_title VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS company VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS industry VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS years_experience INT NULL,
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS address TEXT NULL,
ADD COLUMN IF NOT EXISTS city VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS state VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS country VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS zip_code VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS interests TEXT NULL,
ADD COLUMN IF NOT EXISTS skills TEXT NULL,
ADD COLUMN IF NOT EXISTS languages TEXT NULL,
ADD COLUMN IF NOT EXISTS hobbies TEXT NULL,
ADD COLUMN IF NOT EXISTS favorite_topics TEXT NULL,
ADD COLUMN IF NOT EXISTS personal_website VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS linkedin_profile VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS github_profile VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS twitter_handle VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS instagram_handle VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS facebook_profile VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS youtube_channel VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS twitch_channel VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS discord_username VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS telegram_username VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS whatsapp_number VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS emergency_contact VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS emergency_phone VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS emergency_relationship VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
ADD COLUMN IF NOT EXISTS height DECIMAL(5,2) NULL,
ADD COLUMN IF NOT EXISTS weight DECIMAL(5,2) NULL,
ADD COLUMN IF NOT EXISTS eye_color VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS hair_color VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS ethnicity VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS nationality VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS religion VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS political_views VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS dietary_restrictions TEXT NULL,
ADD COLUMN IF NOT EXISTS allergies TEXT NULL,
ADD COLUMN IF NOT EXISTS medical_conditions TEXT NULL,
ADD COLUMN IF NOT EXISTS medications TEXT NULL,
ADD COLUMN IF NOT EXISTS insurance_provider VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS insurance_policy_number VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS emergency_room_preference VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS timezone VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS date_format VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS currency VARCHAR(10) NULL,
ADD COLUMN IF NOT EXISTS measurement_system ENUM('metric', 'imperial') NULL,
ADD COLUMN IF NOT EXISTS privacy_level ENUM('public', 'friends', 'private') DEFAULT 'public',
ADD COLUMN IF NOT EXISTS profile_completion_percentage INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS last_profile_update TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS profile_views INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS profile_rating DECIMAL(3,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS profile_reviews_count INT DEFAULT 0;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_gender ON users(gender);
CREATE INDEX IF NOT EXISTS idx_users_education_level ON users(education_level);
CREATE INDEX IF NOT EXISTS idx_users_marital_status ON users(marital_status);
CREATE INDEX IF NOT EXISTS idx_users_job_status ON users(job_status);
CREATE INDEX IF NOT EXISTS idx_users_country ON users(country);
CREATE INDEX IF NOT EXISTS idx_users_city ON users(city);
CREATE INDEX IF NOT EXISTS idx_users_privacy_level ON users(privacy_level);
CREATE INDEX IF NOT EXISTS idx_users_profile_completion ON users(profile_completion_percentage);

-- Create a function to calculate profile completion percentage
DELIMITER //
CREATE FUNCTION IF NOT EXISTS CalculateProfileCompletion(user_id INT) 
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE completion_percentage INT DEFAULT 0;
    DECLARE total_fields INT DEFAULT 0;
    DECLARE filled_fields INT DEFAULT 0;
    
    -- Count total profile fields
    SELECT COUNT(*) INTO total_fields
    FROM information_schema.columns 
    WHERE table_name = 'users' 
    AND column_name IN (
        'username', 'email', 'full_name', 'bio', 'location', 'website',
        'gender', 'birth_date', 'education_level', 'education_institution',
        'marital_status', 'job_status', 'job_title', 'company',
        'phone', 'address', 'city', 'state', 'country',
        'interests', 'skills', 'languages', 'hobbies'
    );
    
    -- Count filled fields for the user
    SELECT COUNT(*) INTO filled_fields
    FROM users 
    WHERE id = user_id 
    AND (
        username IS NOT NULL AND username != '' OR
        email IS NOT NULL AND email != '' OR
        full_name IS NOT NULL AND full_name != '' OR
        bio IS NOT NULL AND bio != '' OR
        location IS NOT NULL AND location != '' OR
        website IS NOT NULL AND website != '' OR
        gender IS NOT NULL OR
        birth_date IS NOT NULL OR
        education_level IS NOT NULL OR
        education_institution IS NOT NULL AND education_institution != '' OR
        marital_status IS NOT NULL OR
        job_status IS NOT NULL OR
        job_title IS NOT NULL AND job_title != '' OR
        company IS NOT NULL AND company != '' OR
        phone IS NOT NULL AND phone != '' OR
        address IS NOT NULL AND address != '' OR
        city IS NOT NULL AND city != '' OR
        state IS NOT NULL AND state != '' OR
        country IS NOT NULL AND country != '' OR
        interests IS NOT NULL AND interests != '' OR
        skills IS NOT NULL AND skills != '' OR
        languages IS NOT NULL AND languages != '' OR
        hobbies IS NOT NULL AND hobbies != ''
    );
    
    -- Calculate percentage
    IF total_fields > 0 THEN
        SET completion_percentage = ROUND((filled_fields / total_fields) * 100);
    END IF;
    
    RETURN completion_percentage;
END //
DELIMITER ;

-- Create a trigger to update profile completion percentage
DELIMITER //
CREATE TRIGGER IF NOT EXISTS UpdateProfileCompletion
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.profile_completion_percentage != OLD.profile_completion_percentage THEN
        UPDATE users 
        SET profile_completion_percentage = CalculateProfileCompletion(NEW.id),
            last_profile_update = CURRENT_TIMESTAMP
        WHERE id = NEW.id;
    END IF;
END //
DELIMITER ;

-- Update existing users with default values
UPDATE users SET 
    privacy_level = 'public',
    profile_completion_percentage = CalculateProfileCompletion(id),
    last_profile_update = CURRENT_TIMESTAMP
WHERE privacy_level IS NULL;

-- Show the new structure
DESCRIBE users; 