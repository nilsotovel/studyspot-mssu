-- StudySpot MSSU database

CREATE DATABASE IF NOT EXISTS studyspot
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE studyspot;

-- Drop old tables so the script can be re-run (reviews first because it points to the others)
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS study_spots;
DROP TABLE IF EXISTS users;

-- One row per account. Passwords are stored as hashes, never plain text.
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- The campus places people can review (filled in below)
CREATE TABLE study_spots (
    spot_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- One row per review. Each review belongs to one user and one study spot.
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    spot_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (spot_id) REFERENCES study_spots(spot_id)
);

-- Study spots from the MSSU campus map
INSERT INTO study_spots (spot_id, name) VALUES
    (1, 'Spiva Library'),
    (2, 'Billingsly Student Center'),
    (3, 'Robert W. Plaster Free Enterprise Center'),
    (4, 'Coffee Shop'),
    (5, 'The Lion''s Den'),
    (6, 'Julio S. León Health Science Center'),
    (7, 'Ummel Technology Building'),
    (8, 'Kuhn Hall'),
    (9, 'Reynolds Hall'),
    (10, 'Jeremiah "Jay" Nixon Hall'),
    (11, 'Taylor Hall'),
    (12, 'Webster Hall'),
    (13, 'Dianne Mayes Student Life Center');

-- Sample accounts for testing and the demo. Both passwords are: password123
INSERT INTO users (user_id, username, password_hash) VALUES
    (1, 'daniel_m', '$2y$10$cRbs0a74GuFUV.RqPcN4OefHcowEIfvnb2.KXdoonlvzt4sA6mMZq'),
    (2, 'caden_r', '$2y$10$cRbs0a74GuFUV.RqPcN4OefHcowEIfvnb2.KXdoonlvzt4sA6mMZq');

-- Sample reviews so the dashboard is not empty
INSERT INTO reviews (user_id, spot_id, rating, review_text) VALUES
    (1, 1, 5, 'Dead quiet after 6pm. My go-to before exams.'),
    (2, 2, 3, 'Good for group work but too loud to focus alone.'),
    (2, 3, 4, 'Comfortable chairs and outlets, some foot traffic between classes.');
