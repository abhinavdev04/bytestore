-- ByteStore Database Schema (Plain Text, No Admin/Logging)
-- BCS 4th Semester Project
-- Database: bytestore

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- delete if exist
DROP DATABASE IF EXISTS `bytestore`;
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `bytestore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bytestore`;

-- --------------------------------------------------------
-- Table structure for table `customer`
-- --------------------------------------------------------
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample customers
INSERT INTO `customer` (`customer_name`, `customer_password`, `customer_email`, `customer_address`) VALUES
('Akraj Customer', '$2y$10$d3FtmP5TpBKD8p/tmhtMoOtYfXnPiOTv1yf6oje6upUAKhgCPxPge', 'Akraj2024@bytestore.com', 'Bhaktapur,Nepal'),
('Arpan Shopper', '$2y$10$MdqYBjr5XbwjhrAWq8BPs.DSC0pMx.GNqwnnClniRdpKEUfZh..gW', 'Arpan1@bytestore.com', 'Kalanki,Nepal'),
('Pramisha Shopper', '$2y$10$JtVseoOkft5wvmhUwA1Hh.80LCz8CfKbN..ckMB9WFLmUvw/rmKmG', 'Pramisha1@bytestore.com', 'Butwal,Nepal'),
('Sujit Shopper', '$2y$10$7ahVfFMdtdBguKxE9mrXGOME1YTX/M.eWpb0zv3ULkYuJQElIBD1a', 'Sujit1@bytestore.com', 'Lalitpur,Nepal'),
('Sajal Shopper', '$2y$10$I3xtQG6TgrATvMQNUY3CVuTVpWo95FITDwau8Wj6tprRMsdDqSAZC', 'Sajal1@bytestore.com', 'Pokhara,Nepal'),
('Rohan Shopper', '$2y$10$nUA74/qDP1NDXj9IRllAsOQL/i9jhUfiJUv8znuiQ7NYNyf9Ox17W', 'Rohan1@bytestore.com', 'Dharan,Nepal'),
('Customer', '$2y$10$udpuzqhUnCkNS0NqcLLKD.SuN7wsJKFPVbmYgLkIBpMhLwweS2xd.', 'Customer1@bytestore.com', 'Example,Nepal')
;

-- --------------------------------------------------------
-- Table structure for table `employee`
-- --------------------------------------------------------
CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(255) NOT NULL,
  `employee_password` varchar(255) NOT NULL,
  `employee_email` varchar(255) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `employee_email` (`employee_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Super admin (ID 1)
INSERT INTO `employee` (`employee_name`, `employee_password`, `employee_email`, `shop_name`) VALUES
('Super Admin', '$2y$10$fctJAfHmyWeB18bIaHb7b.hz2E7GqsC9/TYDhLbE3pODp67dxnRHi', 'Superadmin1@bytestore.com', 'Main Tech Store');

-- Normal employee
INSERT INTO `employee` (`employee_name`, `employee_password`, `employee_email`, `shop_name`) VALUES
('Emp1', '$2y$10$3O5KGA9nxIc9fecmvXgZQ.6FHnxY9yB94nAWbU8LCneuUiDYTH9oO', 'Employee1@bytestore.com', 'Main Tech Store'),
('Pratima Employee', '$2y$10$TVlh4ta/78ss8s99Ot.BAu29mv2wdvyoqa2F0lP9zBIQvxEKuPjwq', 'Pratima1@bytestore.com', 'Euro Store'),
('Sunita Employee', '$2y$10$lsx6tQO6nnCdx8N24gHnQ.72dd1/wC3R05pO8hGXXghPV.fLoW.Fy', 'Sunita1@bytestore.com', 'Selective Store'),
('Arjun Employee', '$2y$10$hf6qF80FXdHdJT1lfL/P/.lj2X8sztxLuoa73dzS.LCJcVeTQSkJW', 'Arjun1@bytestore.com', 'Dami Store');

-- --------------------------------------------------------
-- Table structure for table `product`
-- --------------------------------------------------------
CREATE TABLE `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image_path` varchar(500) NOT NULL,
  `product_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample products
INSERT INTO `product` (`product_name`, `product_description`, `product_price`, `product_image_path`, `product_stock`) VALUES
('Laptop Dell XPS 15', 'Laptop with 16GB RAM, 512GB SSD,i7 processor', 230000, 'assets/uploads/laptop1.jpg', 10),
('iPhone 15 Pro', 'Latest iPhone with 128GB storage, Pro camera system', 190000, 'assets/uploads/iphone.jpg', 15),
('Samsung Galaxy S24', 'Android smartphone with 256GB storage, 5G support', 180000, 'assets/uploads/samsung.jpg', 12),
('Wireless Mouse', 'Ergonomic wireless mouse with long battery life', 2999, 'assets/uploads/mouse.jpg', 50),
('Mechanical Keyboard', 'RGB backlit mechanical keyboard with blue switches', 4990, 'assets/uploads/keyboard.jpg', 30),
('Apple MacBook Air M2', '13-inch Retina Display, 8GB RAM, 256GB SSD, M2 Chip', 175000, 'assets/uploads/macbook_air.jpg', 8),
('HP Gaming Laptop', 'Ryzen 5, 16GB RAM, RTX 3050, 512GB SSD', 155000, 'assets/uploads/hp_pavilion.jpg', 10),
('Lenovo ThinkPad E14', 'Business laptop with 16GB RAM, 512GB SSD, i5 12th Gen', 140000, 'assets/uploads/thinkpad.jpg', 12),
('Sony WH Headphones', 'Noise cancelling wireless over-ear headphones', 58000, 'assets/uploads/sony_headphone.jpg', 20),
('JBL Charge 5 Speaker', 'Portable Bluetooth speaker with deep bass', 24000, 'assets/uploads/jbl.jpg', 25),
('Apple AirPods Pro 2', 'Noise cancellation, wireless charging case', 42000, 'assets/uploads/airpods.jpg', 30),
('Samsung Galaxy Tab S9', '11-inch AMOLED Display, 8GB RAM, 256GB', 120000, 'assets/uploads/galaxy_tab.jpg', 10),
('iPad 10th Gen', '10.9-inch, 64GB storage, A14 chip', 95000, 'assets/uploads/ipad10.jpg', 12),
('Apple Watch Series 9', 'Fitness tracking smartwatch with GPS', 78000, 'assets/uploads/apple_watch.jpg', 18),
('Samsung Watch 6', 'Premium fitness smartwatch', 55000, 'assets/uploads/galaxy_watch.jpg', 22),
('Logitech G502 Mouse', 'Gaming mouse with precision sensor and RGB', 9500, 'assets/uploads/logitech_g502.jpg', 35),
('Razer BlackWidow V3', 'Mechanical gaming keyboard with RGB lighting', 21000, 'assets/uploads/razer_keyboard.jpg', 28),
('Asus ROG Strix Gaming Laptop', 'i7 13th Gen, RTX 4060, 16GB RAM, 1TB SSD', 245000, 'assets/uploads/asus_rog.jpg', 6),
('Dell Monitor 27 Inch', 'Full HD IPS LED Monitor 144Hz', 45000, 'assets/uploads/dell_monitor.jpg', 15),
('Canon EOS 250D DSLR', '24.1MP DSLR camera with lens kit', 95000, 'assets/uploads/canon_dslr.jpg', 7),
('GoPro Hero 12', 'Action camera 5K video, waterproof', 78000, 'assets/uploads/gopro12.jpg', 14),
('Portable SSD 1TB', 'High-speed external solid state drive USB-C', 18000, 'assets/uploads/ssd1tb.jpg', 40),
('Sandisk 128GB Pendrive', 'USB 3.2 high speed pendrive with micro usb   ', 2200, 'assets/uploads/pendrive.jpg', 60),
('Gaming Chair RGB Edition', 'Ergonomic gaming chair with RGB lighting', 38000, 'assets/uploads/gaming_chair.jpg', 9),
('TP-Link WiFi 6 Router', 'High-speed dual band gigabit WiFi router', 18000, 'assets/uploads/wifi_router.jpg', 20);


-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample orders
INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `order_status`, `payment_status`, `shipping_address`) VALUES
(1, 4, '2025-06-07 10:35:49', 420000.00, 'Shipped', 'Payed', 'Lalitpur,Nepal'),
(2, 1, '2026-01-08 03:46:14', 235998.00, 'Delivered', 'Pending', 'Bhaktapur,Nepal'),
(3, 5, '2025-09-04 08:36:46', 46990.00, 'Shipped', 'Payed', 'Pokhara,Nepal'),
(4, 3, '2025-07-09 05:27:14', 310000.00, 'Cancelled', 'Pending', 'Butwal,Nepal'),
(5, 2, '2026-01-03 02:17:43', 180000.00, 'Pending', 'Pending', 'Kalanki,Nepal'),
(6, 6, '2025-08-03 04:38:08', 330000.00, 'Processing', 'Pending', 'Dharan,Nepal');


-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample order items
INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 190000.00),
(2, 1, 1, 1, 230000.00),
(3, 2, 1, 1, 230000.00),
(4, 2, 4, 2, 2999.00),
(5, 3, 5, 1, 4990.00),
(6, 3, 11, 1, 42000.00),
(7, 4, 12, 1, 120000.00),
(8, 4, 2, 1, 190000.00),
(9, 5, 3, 1, 180000.00),
(10, 6, 6, 1, 175000.00),
(11, 6, 7, 1, 155000.00);


-- --------------------------------------------------------
-- Table structure for table `cart`
-- --------------------------------------------------------
CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `customer_id` (`customer_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample carts
INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `quantity`, `created_at`) VALUES
(10, 4, 4, 1, '2026-01-03 09:35:52'),
(11, 4, 5, 2, '2026-01-03 09:35:58'),
(12, 1, 6, 1, '2026-01-03 09:36:16'),
(13, 1, 15, 1, '2026-01-03 09:36:20'),
(14, 1, 10, 1, '2026-01-03 09:36:25'),
(16, 5, 7, 1, '2026-01-03 09:36:50'),
(17, 5, 16, 1, '2026-01-03 09:36:54'),
(19, 3, 16, 2, '2026-01-03 09:37:17'),
(20, 3, 2, 1, '2026-01-03 09:37:20'),
(21, 3, 21, 1, '2026-01-03 09:37:28'),
(22, 2, 25, 1, '2026-01-03 09:37:46'),
(23, 2, 9, 1, '2026-01-03 09:37:50'),
(24, 2, 10, 1, '2026-01-03 09:37:53'),
(26, 6, 6, 1, '2026-01-03 09:38:11'),
(27, 6, 11, 1, '2026-01-03 09:38:13');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
