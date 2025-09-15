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

-- Insert sample data (optional)
INSERT INTO page_views (post_slug, views) VALUES
('sample-post', 0)
ON DUPLICATE KEY UPDATE views = views;

-- Create comments table (optional, for future comments system)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_slug VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Grant permissions (adjust user as needed)
-- GRANT ALL PRIVILEGES ON blog_db.* TO 'your_db_user'@'localhost';
-- FLUSH PRIVILEGES;