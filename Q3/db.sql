USE student_admin;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL
);

-- password for this demo user is Manu@123 (stored as a BCrypt hash)
INSERT INTO users (username, password_hash, full_name) VALUES
('manu', '$2a$10$.2oBQAN7XuiUpuiKCPVG1ueNdPw1FrO/G9oZES0fTc5tGPJtj.VGm', 'Manunanditha');
