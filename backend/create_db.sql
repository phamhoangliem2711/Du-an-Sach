-- SQL script to create database and books table
-- Run in MySQL / MariaDB client (adjust user/password as needed)

CREATE DATABASE IF NOT EXISTS testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE testdb;

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock INT NOT NULL DEFAULT 0
);

-- Example seed data
INSERT INTO books (title, author, price, stock) VALUES
('Lập trình PHP căn bản', 'Nguyễn A', 120000.00, 10),
('Thiết kế web hiện đại', 'Trần B', 150000.00, 5);
