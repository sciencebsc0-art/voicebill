CREATE DATABASE IF NOT EXISTS Pay_APP
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE Pay_APP;

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(190) NULL,
    customer_phone VARCHAR(30) NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'INR',
    razorpay_order_id VARCHAR(80) NOT NULL UNIQUE,
    razorpay_payment_id VARCHAR(80) NULL,
    razorpay_signature VARCHAR(255) NULL,
    status ENUM('created','paid','failed') NOT NULL DEFAULT 'created',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;