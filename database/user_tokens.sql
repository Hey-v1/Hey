-- User tokens table for remember me and password reset functionality
CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires DATETIME NOT NULL,
    type ENUM('remember_me', 'password_reset') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (selector),
    INDEX (expires),
    INDEX (type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
