-- Database updates for new features (Fixed version)

-- 1. Post Categories/Tags
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add category_id to posts table (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'posts' 
     AND COLUMN_NAME = 'category_id') = 0,
    'ALTER TABLE posts ADD COLUMN category_id INT DEFAULT NULL',
    'SELECT "category_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'posts' 
     AND COLUMN_NAME = 'category_id' 
     AND REFERENCED_TABLE_NAME = 'categories') = 0,
    'ALTER TABLE posts ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL',
    'SELECT "foreign key already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Bookmarks table
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- 3. User Profile enhancements (only add if they don't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'bio') = 0,
    'ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL',
    'SELECT "bio column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'website') = 0,
    'ALTER TABLE users ADD COLUMN website VARCHAR(255) DEFAULT NULL',
    'SELECT "website column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'location') = 0,
    'ALTER TABLE users ADD COLUMN location VARCHAR(100) DEFAULT NULL',
    'SELECT "location column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'social_twitter') = 0,
    'ALTER TABLE users ADD COLUMN social_twitter VARCHAR(100) DEFAULT NULL',
    'SELECT "social_twitter column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'social_facebook') = 0,
    'ALTER TABLE users ADD COLUMN social_facebook VARCHAR(100) DEFAULT NULL',
    'SELECT "social_facebook column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'social_linkedin') = 0,
    'ALTER TABLE users ADD COLUMN social_linkedin VARCHAR(100) DEFAULT NULL',
    'SELECT "social_linkedin column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'posts_count') = 0,
    'ALTER TABLE users ADD COLUMN posts_count INT DEFAULT 0',
    'SELECT "posts_count column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'followers_count') = 0,
    'ALTER TABLE users ADD COLUMN followers_count INT DEFAULT 0',
    'SELECT "followers_count column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'following_count') = 0,
    'ALTER TABLE users ADD COLUMN following_count INT DEFAULT 0',
    'SELECT "following_count column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. User Followers table
CREATE TABLE IF NOT EXISTS user_followers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Post Views tracking (only if not exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'posts' 
     AND COLUMN_NAME = 'views') = 0,
    'ALTER TABLE posts ADD COLUMN views INT DEFAULT 0',
    'SELECT "views column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. Theme preference (only if not exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'theme_preference') = 0,
    'ALTER TABLE users ADD COLUMN theme_preference ENUM(\'light\', \'dark\') DEFAULT \'light\'',
    'SELECT "theme_preference column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Insert some default categories (only if they don't exist)
INSERT IGNORE INTO categories (name, slug, description, color) VALUES
('Technology', 'technology', 'Tech news, tutorials, and insights', '#007bff'),
('Lifestyle', 'lifestyle', 'Life tips, personal stories, and lifestyle content', '#28a745'),
('Business', 'business', 'Business insights, entrepreneurship, and career advice', '#ffc107'),
('Travel', 'travel', 'Travel stories, tips, and destination guides', '#17a2b8'),
('Food', 'food', 'Recipes, cooking tips, and food experiences', '#dc3545'),
('Health', 'health', 'Health tips, fitness, and wellness content', '#6f42c1'),
('Education', 'education', 'Learning resources, tutorials, and educational content', '#fd7e14'),
('Entertainment', 'entertainment', 'Movies, music, games, and entertainment news', '#e83e8c');

-- Update existing posts to have a default category (only if category_id is NULL)
UPDATE posts SET category_id = 1 WHERE category_id IS NULL; 