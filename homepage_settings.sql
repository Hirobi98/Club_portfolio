CREATE TABLE IF NOT EXISTS homepage_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL
);

-- Insert default placeholder values for your homepage sections
INSERT INTO homepage_settings (setting_key, setting_value) VALUES 
('home_hero_title', 'Welcome to Spectrum KUET'),
('home_hero_subtitle', 'Empowering technical skills and professional development.'),
('about_description', 'Spectrum is a premium skill development club at KUET dedicated to bridges the gap between academic theories and industry standards.'),
('achievements_count', '15+ National Wins'),
('members_count', '120+ Active Members')
ON DUPLICATE KEY UPDATE setting_key=setting_key;