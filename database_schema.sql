-- Database: incident_tracker
-- Create database and tables for persecution/incident reporting system

CREATE DATABASE IF NOT EXISTS incident_tracker;
USE incident_tracker;

-- Users table (Super Admin and Admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super', 'admin') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    district VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table (Types of persecution/incidents)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- States table (Indian states)
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Districts table (Based on states)
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE
);

-- Posts table (Incident reports)
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    short_message TEXT,
    detailed_message TEXT,
    incident_date DATE,
    image_path VARCHAR(255),
    video_path VARCHAR(255),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    state VARCHAR(100),
    district VARCHAR(100),
    category_id INT,
    external_links TEXT,
    featured_image_path VARCHAR(255),
    tags TEXT,
    status ENUM('draft', 'published', 'unpublished') DEFAULT 'draft',
    admin_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default Super Admin
INSERT INTO users (username, password, role, name, email) VALUES 
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super', 'Super Administrator', 'admin@example.com');
-- Default password is 'password' (hashed with bcrypt)

-- Insert Indian States
INSERT INTO states (name) VALUES 
('Andhra Pradesh'), ('Arunachal Pradesh'), ('Assam'), ('Bihar'), ('Chhattisgarh'),
('Goa'), ('Gujarat'), ('Haryana'), ('Himachal Pradesh'), ('Jharkhand'),
('Karnataka'), ('Kerala'), ('Madhya Pradesh'), ('Maharashtra'), ('Manipur'),
('Meghalaya'), ('Mizoram'), ('Nagaland'), ('Odisha'), ('Punjab'),
('Rajasthan'), ('Sikkim'), ('Tamil Nadu'), ('Telangana'), ('Tripura'),
('Uttar Pradesh'), ('Uttarakhand'), ('West Bengal'),
('Andaman and Nicobar Islands'), ('Chandigarh'), ('Dadra and Nagar Haveli and Daman and Diu'),
('Delhi'), ('Jammu and Kashmir'), ('Ladakh'), ('Lakshadweep'), ('Puducherry');

-- Insert sample districts for major states
-- Andhra Pradesh
INSERT INTO districts (state_id, name) VALUES 
(1, 'Anantapur'), (1, 'Chittoor'), (1, 'East Godavari'), (1, 'Guntur'), (1, 'Krishna'),
(1, 'Kurnool'), (1, 'Prakasam'), (1, 'Srikakulam'), (1, 'Visakhapatnam'), (1, 'Vizianagaram'),
(1, 'West Godavari'), (1, 'YSR Kadapa'), (1, 'Nellore');

-- Karnataka
INSERT INTO districts (state_id, name) VALUES 
(11, 'Bagalkot'), (11, 'Bangalore Rural'), (11, 'Bangalore Urban'), (11, 'Belgaum'),
(11, 'Bellary'), (11, 'Bidar'), (11, 'Chamarajanagar'), (11, 'Chikkaballapur'),
(11, 'Chikkamagaluru'), (11, 'Chitradurga'), (11, 'Dakshina Kannada'), (11, 'Davanagere'),
(11, 'Dharwad'), (11, 'Gadag'), (11, 'Gulbarga'), (11, 'Hassan'), (11, 'Haveri'),
(11, 'Kodagu'), (11, 'Kolar'), (11, 'Koppal'), (11, 'Mandya'), (11, 'Mysore'),
(11, 'Raichur'), (11, 'Ramanagara'), (11, 'Shimoga'), (11, 'Tumkur'), (11, 'Udupi'),
(11, 'Uttara Kannada'), (11, 'Yadgir');

-- Tamil Nadu
INSERT INTO districts (state_id, name) VALUES 
(23, 'Ariyalur'), (23, 'Chennai'), (23, 'Coimbatore'), (23, 'Cuddalore'), (23, 'Dharmapuri'),
(23, 'Dindigul'), (23, 'Erode'), (23, 'Kanchipuram'), (23, 'Kanyakumari'), (23, 'Karur'),
(23, 'Krishnagiri'), (23, 'Madurai'), (23, 'Nagapattinam'), (23, 'Namakkal'), (23, 'Nilgiris'),
(23, 'Perambalur'), (23, 'Pudukkottai'), (23, 'Ramanathapuram'), (23, 'Salem'), (23, 'Sivaganga'),
(23, 'Thanjavur'), (23, 'Theni'), (23, 'Thoothukudi'), (23, 'Tiruchirappalli'), (23, 'Tirunelveli'),
(23, 'Tiruppur'), (23, 'Tiruvallur'), (23, 'Tiruvannamalai'), (23, 'Tiruvottiyur'), (23, 'Vellore'),
(23, 'Viluppuram'), (23, 'Virudhunagar');

-- Maharashtra
INSERT INTO districts (state_id, name) VALUES 
(14, 'Ahmednagar'), (14, 'Akola'), (14, 'Amravati'), (14, 'Aurangabad'), (14, 'Beed'),
(14, 'Bhandara'), (14, 'Buldhana'), (14, 'Chandrapur'), (14, 'Dhule'), (14, 'Gadchiroli'),
(14, 'Gondia'), (14, 'Hingoli'), (14, 'Jalgaon'), (14, 'Jalna'), (14, 'Kolhapur'),
(14, 'Latur'), (14, 'Mumbai City'), (14, 'Mumbai Suburban'), (14, 'Nagpur'), (14, 'Nanded'),
(14, 'Nandurbar'), (14, 'Nashik'), (14, 'Osmanabad'), (14, 'Palghar'), (14, 'Parbhani'),
(14, 'Pune'), (14, 'Raigad'), (14, 'Ratnagiri'), (14, 'Sangli'), (14, 'Satara'),
(14, 'Sindhudurg'), (14, 'Solapur'), (14, 'Thane'), (14, 'Wardha'), (14, 'Washim'), (14, 'Yavatmal');

-- Insert sample categories
INSERT INTO categories (name, description) VALUES 
('Religious Persecution', 'Incidents related to religious discrimination or violence'),
('Caste-based Violence', 'Incidents involving caste discrimination and violence'),
('Gender-based Violence', 'Violence against women and gender minorities'),
('Political Violence', 'Political persecution and violence'),
('Social Discrimination', 'General social discrimination incidents'),
('Property Damage', 'Destruction of property due to persecution'),
('Forced Conversion', 'Cases involving forced religious conversion'),
('Economic Boycott', 'Economic persecution and boycotts');