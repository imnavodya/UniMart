SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `unimart`;
USE `unimart`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@unimart.com', '$2y$10$B/1CegTXdRcMdd8lQeeDI.Nd8186qj9lsqHjGVMU0UEWdheL8ysg.', 'admin'),
('John Student', 'john@unimart.com', '$2y$10$B/1CegTXdRcMdd8lQeeDI.Nd8186qj9lsqHjGVMU0UEWdheL8ysg.', 'customer');

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-box',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`name`, `icon`) VALUES
('Electronics', 'fas fa-laptop'),
('Accessories', 'fas fa-headphones'),
('Stationery', 'fas fa-pen-nib'),
('Digital Products', 'fas fa-code'),
('Snacks', 'fas fa-hamburger'),
('Art & Crafts', 'fas fa-palette');

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `image`, `stock`) VALUES
(1, 'MacBook Pro M3 Max', 'Premium laptop for heavy development and design work. Features incredible battery life and unmatched performance.', 3999.99, 'macbook.jpg', 15),
(2, 'Sony WH-1000XM5', 'Industry-leading noise canceling headphones with premium sound quality and comfortable fit.', 349.99, 'sony_headphones.jpg', 30),
(1, 'iPad Pro 12.9\"', 'The ultimate tablet experience with M2 chip and gorgeous Liquid Retina XDR display.', 1099.00, 'ipad.jpg', 20),
(3, 'Moleskine Smart Notebook', 'Seamlessly digitize your hand-written notes with this premium smart notebook system.', 149.99, 'notebook.jpg', 50),
(4, 'Framer Template Bundle', 'A collection of 10 high-converting Framer templates for modern startups.', 99.00, 'framer.jpg', 999),
(5, 'Artisan Coffee Beans', 'Locally roasted premium coffee beans to keep you coding all night.', 24.99, 'coffee.jpg', 100);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`user_id`, `total_amount`, `status`, `created_at`) VALUES
(2, 349.99, 'completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 1099.00, 'pending', NOW());

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 1, 349.99),
(2, 3, 1, 1099.00);

COMMIT;
