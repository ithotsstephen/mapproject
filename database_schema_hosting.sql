-- Persecution Tracker Database Schema
-- For existing database (shared hosting compatible)
-- Run this after creating your database through cPanel

-- Drop tables if they exist (be careful in production!)
DROP TABLE IF EXISTS admin_activity_log;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS states;
DROP TABLE IF EXISTS users;

-- Create users table
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

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create states table
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create districts table
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE
);

-- Create posts table
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
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create admin activity log table (optional - for tracking)
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

-- Insert default super admin user
-- Password is 'password' (change immediately after login!)
INSERT INTO users (name, username, email, password, role, status, created_at) VALUES
('Super Administrator', 'superadmin', 'admin@projectsdemo.link', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active', NOW());

-- Insert sample admin user
-- Password is 'admin123' (change after login!)
INSERT INTO users (name, username, email, password, role, status, created_at) VALUES
('Sample Admin', 'admin', 'admin@projectsdemo.link', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyeBAuL8SHNyxaaAUy3zhbQWo3K/O', 'admin', 'active', NOW());

-- Insert default categories
INSERT INTO categories (name, description, status) VALUES
('Religious Violence', 'Incidents involving religious persecution and violence', 'active'),
('Hate Crimes', 'Criminal acts motivated by bias or hatred', 'active'),
('Discrimination', 'Cases of social, economic, or institutional discrimination', 'active'),
('Mob Violence', 'Incidents involving mob attacks and lynching', 'active'),
('Property Destruction', 'Destruction of religious or personal property', 'active'),
('Forced Conversion', 'Cases of forced religious conversion', 'active'),
('Legal Harassment', 'Misuse of legal system for persecution', 'active'),
('Social Boycott', 'Social and economic boycotts', 'active');

-- Insert Indian states
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

-- Insert some sample posts for demonstration
INSERT INTO posts (title, short_message, detailed_message, category_id, admin_id, state, district, incident_date, status, created_at) VALUES
('Sample Incident Report - Delhi', 
 'This is a sample incident report to demonstrate the system functionality.', 
 'This is a detailed sample report that shows how incident details are stored and displayed in the system. This is for demonstration purposes only and should be replaced with actual incident data.', 
 1, 2, 'Delhi', 'Central Delhi', '2024-01-15', 'published', NOW()),

('Sample Community Tensions - Mumbai', 
 'Sample report showing how community tension incidents are documented.', 
 'This sample demonstrates the detailed reporting capability of the system. In a real scenario, this would contain factual information about actual incidents, including dates, locations, parties involved, and resolution status.', 
 2, 2, 'Maharashtra', 'Mumbai', '2024-01-10', 'published', NOW()),

('Sample Property Damage Case - Bangalore', 
 'Demonstration of property damage incident reporting.', 
 'This sample entry illustrates how property damage incidents are recorded in the system, including location details, extent of damage, and follow-up actions taken. This is placeholder content for system demonstration.', 
 5, 2, 'Karnataka', 'Bangalore Urban', '2024-01-08', 'draft', NOW());

-- Create indexes for better performance
CREATE INDEX idx_posts_state ON posts(state);
CREATE INDEX idx_posts_status ON posts(status);
CREATE INDEX idx_posts_category ON posts(category_id);
CREATE INDEX idx_posts_admin ON posts(admin_id);
CREATE INDEX idx_posts_created ON posts(created_at);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Note: Default login credentials (CHANGE IMMEDIATELY!)
-- Super Admin: username = superadmin, password = password
-- Sample Admin: username = admin, password = admin123