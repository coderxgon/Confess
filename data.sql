CREATE TABLE confessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    heart_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
