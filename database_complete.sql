-- Complete Database Schema for Persecution Tracker (shared hosting compatible)
-- Run after creating your database in cPanel/hosting
-- WARNING: This will drop existing tables

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS admin_activity_log;
DROP TABLE IF EXISTS post_images;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS states;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- States table
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Districts table
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE
);

-- Posts table
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    short_message TEXT NOT NULL,
    detailed_message LONGTEXT NOT NULL,
    category_id INT,
    admin_id INT,
    state VARCHAR(100),
    district VARCHAR(100),
    incident_date DATE,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    featured_image_path VARCHAR(500),
    image_path VARCHAR(500),
    video_path VARCHAR(500),
    external_links TEXT,
    tags TEXT,
    status ENUM('draft', 'admin_approval', 'published', 'unpublished') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Optional: multiple images per post (gallery)
CREATE TABLE post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Admin activity log
CREATE TABLE admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default users
-- Super Admin (password: ProjectDemo2024!)
INSERT INTO users (name, username, email, password, role, status, created_at) VALUES
('Project Administrator', 'projectadmin', 'admin@projectsdemo.link', '$2y$10$DlLv9J5ew9IoPXvigE43N.w1CYWPupP3jQXVeXOnwhZO2.vfnN74.', 'super_admin', 'active', NOW());

-- Admin (password: DemoAdmin2024!)
INSERT INTO users (name, username, email, password, role, status, created_at) VALUES
('Demo Admin User', 'demoAdmin', 'demoadmin@projectsdemo.link', '$2y$10$ZbwlBuGqkyRK0RMMlUcMXeCRfkT2FprGBiXYGs7tqzVZRMH733Z/6', 'admin', 'active', NOW());

-- Categories
INSERT INTO categories (name, description, status) VALUES
('Religious Violence', 'Incidents involving religious persecution and violence', 'active'),
('Hate Crimes', 'Criminal acts motivated by bias or hatred', 'active'),
('Discrimination', 'Cases of social, economic, or institutional discrimination', 'active'),
('Mob Violence', 'Incidents involving mob attacks and lynching', 'active'),
('Property Destruction', 'Destruction of religious or personal property', 'active'),
('Forced Conversion', 'Cases of forced religious conversion', 'active'),
('Legal Harassment', 'Misuse of legal system for persecution', 'active'),
('Social Boycott', 'Social and economic boycotts', 'active');

-- Indian states
INSERT INTO states (name, code) VALUES
('Andhra Pradesh', 'AP'),
('Arunachal Pradesh', 'AR'),
('Assam', 'AS'),
('Bihar', 'BR'),
('Chhattisgarh', 'CG'),
('Goa', 'GA'),
('Gujarat', 'GJ'),
('Haryana', 'HR'),
('Himachal Pradesh', 'HP'),
('Jharkhand', 'JH'),
('Karnataka', 'KA'),
('Kerala', 'KL'),
('Madhya Pradesh', 'MP'),
('Maharashtra', 'MH'),
('Manipur', 'MN'),
('Meghalaya', 'ML'),
('Mizoram', 'MZ'),
('Nagaland', 'NL'),
('Odisha', 'OR'),
('Punjab', 'PB'),
('Rajasthan', 'RJ'),
('Sikkim', 'SK'),
('Tamil Nadu', 'TN'),
('Telangana', 'TG'),
('Tripura', 'TR'),
('Uttar Pradesh', 'UP'),
('Uttarakhand', 'UK'),
('West Bengal', 'WB'),
('Delhi', 'DL'),
('Jammu and Kashmir', 'JK'),
('Ladakh', 'LA'),
('Chandigarh', 'CH'),
('Dadra and Nagar Haveli and Daman and Diu', 'DN'),
('Lakshadweep', 'LD'),
('Puducherry', 'PY'),
('Andaman and Nicobar Islands', 'AN');

-- Sample posts (published)
INSERT INTO posts (title, short_message, detailed_message, category_id, admin_id, state, district, incident_date, status, created_at) VALUES
('Sample Incident Report - Delhi',
 'This is a sample incident report to demonstrate the system functionality.',
 'This is a detailed sample report for demonstration purposes. Replace with actual incident data.',
 1, 2, 'Delhi', 'Central Delhi', '2024-01-15', 'published', NOW()),
('Sample Community Tensions - Mumbai',
 'Sample report showing how community tension incidents are documented.',
 'This sample demonstrates the detailed reporting capability of the system.',
 2, 2, 'Maharashtra', 'Mumbai', '2024-01-10', 'published', NOW()),
('Sample Property Damage Case - Bangalore',
 'Demonstration of property damage incident reporting.',
 'This sample entry illustrates how property damage incidents are recorded in the system.',
 5, 2, 'Karnataka', 'Bangalore Urban', '2024-01-08', 'published', NOW());

-- Indexes
CREATE INDEX idx_posts_state ON posts(state);
CREATE INDEX idx_posts_status ON posts(status);
CREATE INDEX idx_posts_category ON posts(category_id);
CREATE INDEX idx_posts_admin ON posts(admin_id);
CREATE INDEX idx_posts_created ON posts(created_at);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
