-- Database Schema: spectrum_db

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    tag VARCHAR(50) DEFAULT 'EVENT',
    cover_image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    timeline TEXT NOT NULL,
    is_flagship TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    tag VARCHAR(50) DEFAULT 'OPPORTUNITY',
    image VARCHAR(255) NOT NULL,
    date_info VARCHAR(100) DEFAULT 'Ongoing',
    description TEXT NOT NULL,
    timeline TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS member_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS homepage_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL
);

INSERT INTO admins (username, password) 
VALUES ('admin', 'spectrum22')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO homepage_settings (setting_key, setting_value) VALUES 
('home_hero_title', 'Welcome to Spectrum KUET'),
('home_hero_subtitle', 'Empowering technical skills and professional development.'),
('about_description', 'Spectrum is a premium skill development club at KUET dedicated to bridges the gap between academic theories and industry standards.'),
('achievements_count', '15+ National Wins'),
('members_count', '120+ Active Members')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

INSERT INTO achievements (title, description, display_order) VALUES
('Hult Prize Success', 'Successfully hosted multiple on-campus rounds, leading to KUET teams participating in regional and global summits.', 1),
('JobSpecs Excellence', 'Facilitated direct recruitment of hundreds of students by partnering with top multinational and local companies.', 2),
('Empowering Startups', 'Mentored and guided numerous student-led startups to secure funding and recognition on national platforms.', 3)
ON DUPLICATE KEY UPDATE title=title;