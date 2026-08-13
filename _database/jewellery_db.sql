-- Database Structure for Hybrid Blockchain Jewellery Management System

CREATE DATABASE IF NOT EXISTS jewellery_db;
USE jewellery_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('manufacturer', 'shop', 'customer') NOT NULL DEFAULT 'customer',
    wallet_address VARCHAR(42),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jewellery Table
CREATE TABLE IF NOT EXISTS jewellery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_id INT UNIQUE, -- Matches Blockchain Token ID
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    type VARCHAR(100),
    weight DECIMAL(10,3),
    purity VARCHAR(50),
    making_charge DECIMAL(10,2),
    hallmark_number VARCHAR(100),
    certificate_number VARCHAR(100),
    manufacturer_id INT,
    current_owner_id INT,
    selling_price DECIMAL(15,2),
    status ENUM('manufactured', 'in_shop', 'sold', 'stolen') DEFAULT 'manufactured',
    image_url VARCHAR(255),
    blockchain_hash VARCHAR(255),
    qr_code VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manufacturer_id) REFERENCES users(id),
    FOREIGN KEY (current_owner_id) REFERENCES users(id)
);

-- Transactions/History Table (Off-chain cache for quick lookup)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jewellery_id INT,
    from_user_id INT,
    to_user_id INT,
    tx_hash VARCHAR(255),
    tx_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jewellery_id) REFERENCES jewellery(id),
    FOREIGN KEY (from_user_id) REFERENCES users(id),
    FOREIGN KEY (to_user_id) REFERENCES users(id)
);.0

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    wallet_address VARCHAR(42),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin
INSERT INTO admins (name, email, password) VALUES 
('Super Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
