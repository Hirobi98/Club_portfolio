-- Achievements table for dynamic management
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed with existing hardcoded achievements
INSERT INTO achievements (title, description, display_order) VALUES
('Hult Prize Success', 'Successfully hosted multiple on-campus rounds, leading to KUET teams participating in regional and global summits.', 1),
('JobSpecs Excellence', 'Facilitated direct recruitment of hundreds of students by partnering with top multinational and local companies.', 2),
('Empowering Startups', 'Mentored and guided numerous student-led startups to secure funding and recognition on national platforms.', 3)
ON DUPLICATE KEY UPDATE title=title;
