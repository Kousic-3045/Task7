CREATE DATABASE IF NOT EXISTS validation_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE validation_demo;

CREATE TABLE IF NOT EXISTS employees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO employees (name, email, phone)
SELECT 'Demo Employee', 'demo@example.com', '+919876543210'
WHERE NOT EXISTS (
    SELECT 1 FROM employees WHERE email = 'demo@example.com'
);
