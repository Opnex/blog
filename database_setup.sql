-- Database setup for Opnex Blog
-- Run these commands in your MySQL database

-- 1. Update users table to add missing columns
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS bio TEXT NULL;

-- 2. Update posts table to add missing columns
ALTER TABLE posts 
ADD COLUMN IF NOT EXISTS status ENUM('draft', 'published') DEFAULT 'published',
ADD COLUMN IF NOT EXISTS views INT DEFAULT 0;

-- 3. Update comments table to add missing columns
ALTER TABLE comments 
ADD COLUMN IF NOT EXISTS parent_id INT NULL,
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1;

-- 4. Create post_likes table if it doesn't exist
CREATE TABLE IF NOT EXISTS post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Create comment_likes table if it doesn't exist
CREATE TABLE IF NOT EXISTS comment_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_comment_like (comment_id, user_id),
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. Create notifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    url VARCHAR(255) NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Make sure at least one user is admin
UPDATE users SET is_admin = 1 WHERE id = 1;

-- 8. Make sure all existing posts are published
UPDATE posts SET status = 'published' WHERE status IS NULL OR status = '';

-- 9. Make sure all existing comments are approved
UPDATE comments SET is_approved = 1 WHERE is_approved IS NULL;

-- 10. Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status);
CREATE INDEX IF NOT EXISTS idx_posts_user_id ON posts(user_id);
CREATE INDEX IF NOT EXISTS idx_comments_post_id ON comments(post_id);
CREATE INDEX IF NOT EXISTS idx_comments_user_id ON comments(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications(is_read); 