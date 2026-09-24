CREATE DATABASE IF NOT EXISTS nlc_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nlc_portal;

-- Admin Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site Configuration (About text, contact details, organization info)
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL
);

-- Navigation Menu Links
CREATE TABLE IF NOT EXISTS nav_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('divisions', 'media', 'quick_links', 'other_links') NOT NULL,
    title VARCHAR(150) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target VARCHAR(20) DEFAULT '_self',
    display_order INT DEFAULT 0
);

-- Hero Portrait Slider (Left)
CREATE TABLE IF NOT EXISTS portrait_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Hero 16:9 Banner Slider (Center)
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) DEFAULT '',
    image_path VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Activities / Carousel Cards
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Seed Default Admin (Username: admin | Password: Password@123)
INSERT INTO users (username, password_hash, full_name)
VALUES ('admin', '$2y$10$w8T0MlhqfO3B812vYyVz3OGUo6oYgG1c3fGkK40u7MhE1G6M2HqCe', 'System Administrator')
ON DUPLICATE KEY UPDATE id=id;

-- Seed Settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('org_name', 'NLC India Limited'),
('org_subtitle', '(\'Navratna\' - Government of India Enterprise)'),
('org_dept', 'CORPORATE COMMUNICATIONS DEPARTMENT'),
('about_title', 'About Corporate Communications'),
('about_text', 'The Corporate Communications Department manages internal and external communication activities of NLC India Limited. It handles media relations, public relations, corporate branding, and communication with stakeholders and the public.'),
('contact_bsnl', '04142 – 252257'),
('contact_intercom', '60278'),
('contact_email', 'nlcil.ccdept@nlcindia.in'),
('contact_address', 'Corporate Communications Department, Museum Road, Block-2, Neyveli Township, Neyveli, Cuddalore Dt., Tamil Nadu - 607801'),
('contact_map_url', 'https://maps.app.goo.gl/rkcXg87iD3Fo8mxA7')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- Seed Portrait Slides
INSERT INTO portrait_slides (title, image_path, display_order) VALUES
('CMD', 'images/cmd.png', 1),
('Director HR', 'images/dhr.png', 2),
('General Manager', 'images/gm.png', 3);

-- Seed 16:9 Banner Slides
INSERT INTO hero_slides (image_path, display_order) VALUES
('images/ccd.jpg', 1),
('images/nh.jpg', 2),
('images/ghb25.jpg', 3),
('images/ghb13.jpg', 4),
('images/pcp.jpg', 5),
('images/pnm.jpg', 6);

-- Seed Activities Cards
INSERT INTO activities (title, description, display_order) VALUES
('Brand Enhancement', 'The Corporate Communications Department plays a vital role in strengthening brand visibility by strategically communicating the organization\'s values, achievements, and initiatives to internal and external stakeholders.', 1),
('Public Relations', 'Maintaining relationships with stakeholders and society.', 2),
('Media Excellence & Reputation Management', 'Shaping a strong public image through strategic press communication, media engagement, and build credibility.', 3),
('Corporate Events & Exhibitions', 'Organizing conferences and official programs.', 4);

-- Seed Navigation
INSERT INTO nav_items (category, title, url, target, display_order) VALUES
('divisions', 'Advertisements & Sponsorships', 'advertisement.html', '_self', 1),
('divisions', 'Electronic Media', 'electronic-media.html', '_self', 2),
('divisions', 'Events & Exhibitions', '#', '_self', 3),
('divisions', 'Guest Houses', 'guest-house.html', '_self', 4),
('divisions', 'Holiday Homes', 'http://hrapps.nlcindia.com/holidayhomes/index.cfm', '_blank', 5),
('divisions', 'Industrial Visits', 'industrial-visit.html', '_self', 6),
('divisions', 'Photography and Videography', 'photo-videography.html', '_self', 7),
('divisions', 'Print Media', 'print-media.html', '_self', 8),
('divisions', 'Tenders', 'tender-contracts.html', '_self', 9),
('media', 'Photos Gallery', 'photo-gallery.html', '_self', 1),
('media', 'E-News Papers', 'enews-papers.html', '_self', 2),
('media', 'PASM', 'pasm.html', '_self', 3),
('quick_links', 'NLCIL LOGO', 'images/logo.png', '_blank', 1),
('quick_links', 'NLCIL 70th YEAR LOGO', 'images/NLCIL_Special_logo 70 Years.png', '_blank', 2),
('quick_links', 'NLCIL NEYON MASCOT', 'images/Lion_Mascot.png', '_blank', 3),
('quick_links', 'NLCIL CORPORATE IDENTITY MANUAL', 'images/NLCIL-CorporateIdentity Manual.pdf', '_blank', 4),
('quick_links', 'NLCIL SOCIAL MEDIA QR', 'images/Social media QR.png', '_blank', 5),
('quick_links', 'NLCIL VISION MISSION', 'images/Vision Mission.jpeg', '_blank', 6),
('quick_links', 'NLCIL CORE VALUES ENGLISH', 'images/Core Values English.jpeg', '_blank', 7),
('quick_links', 'NLCIL THEME SONG', 'https://nlcintra.nlcindia.com/new_intra/themesongs.html', '_blank', 8),
('other_links', 'NLCIL Website', 'https://www.nlcindia.in/', '_blank', 1),
('other_links', 'TPS 1', 'http://172.16.128.53/', '_blank', 2),
('other_links', 'TPS I EXP', 'http://172.16.140.54/TPS1EXP/', '_blank', 3),
('other_links', 'TPS II', 'http://172.16.164.68/', '_blank', 4),
('other_links', 'TPS II EXP', 'http://172.16.170.234/', '_blank', 5),
('other_links', 'MINE 1 & 1A', 'http://172.16.92.52:8080/web/', '_blank', 6),
('other_links', 'MINE 2', 'http://172.18.6.154/', '_blank', 7),
('other_links', 'TA OFFICE', 'http://172.16.78.105:9090/ta/mainpage.asp', '_blank', 8),
('other_links', 'MM COMPLEX', 'http://172.16.104.51/', '_blank', 9),
('other_links', 'GENERAL HOSPITAL', 'http://172.16.120.39/hospital/', '_blank', 10),
('other_links', 'CSR', 'http://nlcintra.nlcindia.com/csr/csr.php', '_blank', 11),
('other_links', 'CORPORATE OFFICE', 'http://nlcintra.nlcindia.com/index.php?skip=cmdmsgrep', '_blank', 12),
('other_links', 'TOWNSHIP ADMINISTRATION', 'http://172.16.78.105:9090/ta/mainpage.asp', '_blank', 13),
('other_links', 'L&DC Library', 'http://ldc.nlcindia.com/tccoordination/libtd/DEFAULT.asp', '_blank', 14);