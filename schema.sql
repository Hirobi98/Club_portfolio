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

INSERT INTO admins (username, password) 
VALUES ('admin', 'spectrum22');