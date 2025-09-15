-- Blog Database Setup
-- Run this SQL script to set up the database for the blog

-- Create database
CREATE DATABASE IF NOT EXISTS blog_db;
USE blog_db;

-- Create page_views table
CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_slug VARCHAR(255) NOT NULL UNIQUE,
    views INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create view_logs table for unique tracking
CREATE TABLE IF NOT EXISTS view_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_slug VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_post_ip (post_slug, ip_address),
    INDEX idx_viewed_at (viewed_at)
);

-- Insert sample data (optional)
INSERT INTO page_views (post_slug, views) VALUES
('sample-post', 0)
ON DUPLICATE KEY UPDATE views = views;



-- Grant permissions (adjust user as needed)
-- GRANT ALL PRIVILEGES ON blog_db.* TO 'your_db_user'@'localhost';
-- FLUSH PRIVILEGES;