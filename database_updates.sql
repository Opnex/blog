-- Database updates for new features

-- 1. Post Categories/Tags
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add category_id to posts table
ALTER TABLE posts ADD COLUMN category_id INT DEFAULT NULL;
ALTER TABLE posts ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

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

-- 3. User Profile enhancements
ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL;
ALTER TABLE users ADD COLUMN website VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN location VARCHAR(100) DEFAULT NULL;
ALTER TABLE users ADD COLUMN social_twitter VARCHAR(100) DEFAULT NULL;
ALTER TABLE users ADD COLUMN social_facebook VARCHAR(100) DEFAULT NULL;
ALTER TABLE users ADD COLUMN social_linkedin VARCHAR(100) DEFAULT NULL;
ALTER TABLE users ADD COLUMN posts_count INT DEFAULT 0;
ALTER TABLE users ADD COLUMN followers_count INT DEFAULT 0;
ALTER TABLE users ADD COLUMN following_count INT DEFAULT 0;

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

-- 5. Post Views tracking (if not exists)
ALTER TABLE posts ADD COLUMN views INT DEFAULT 0;

-- 6. Theme preference
ALTER TABLE users ADD COLUMN theme_preference ENUM('light', 'dark') DEFAULT 'light';

-- Insert some default categories
INSERT INTO categories (name, slug, description, color) VALUES
('Technology', 'technology', 'Tech news, tutorials, and insights', '#007bff'),
('Lifestyle', 'lifestyle', 'Life tips, personal stories, and lifestyle content', '#28a745'),
('Business', 'business', 'Business insights, entrepreneurship, and career advice', '#ffc107'),
('Travel', 'travel', 'Travel stories, tips, and destination guides', '#17a2b8'),
('Food', 'food', 'Recipes, cooking tips, and food experiences', '#dc3545'),
('Health', 'health', 'Health tips, fitness, and wellness content', '#6f42c1'),
('Education', 'education', 'Learning resources, tutorials, and educational content', '#fd7e14'),
('Entertainment', 'entertainment', 'Movies, music, games, and entertainment news', '#e83e8c');

-- Update existing posts to have a default category
UPDATE posts SET category_id = 1 WHERE category_id IS NULL; 