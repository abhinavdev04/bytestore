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
CREATE TABLE `category` (
  `category_id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert categories
INSERT INTO `category` (`category_id`, `category_name`) VALUES
(16, 'Accessories'),
(14, 'Audio (Headphones/Speakers)'),
(12, 'Cooling'),
(2, 'Desktops'),
(3, 'Gaming'),
(6, 'Graphics Cards'),
(13, 'Keyboards & Mice'),
(1, 'Laptops'),
(8, 'Memory (RAM)'),
(4, 'Monitors'),
(7, 'Motherboards'),
(20, 'Networking (Routers/WiFi)'),
(11, 'PC Cases'),
(17, 'Phones'),
(10, 'Power Supplies'),
(21, 'Printers & Scanners'),
(5, 'Processors'),
(22, 'Servers'),
(23, 'Software & Services'),
(9, 'Storage (SSD/HDD)'),
(18, 'Tablets'),
(19, 'Wearables (Watches)'),
(15, 'Webcams'),
(24, 'Cameras');

-- --------------------------------------------------------
-- Table structure for table `product`
-- --------------------------------------------------------
CREATE TABLE `product` (
  `product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(255) NOT NULL,
  `product_description` TEXT NOT NULL,
  `product_price` DECIMAL(10,2) NOT NULL,
  `product_image_path` VARCHAR(500) NOT NULL,
  `product_stock` INT(11) NOT NULL DEFAULT 0,
  `category_id` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Sample products
INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image_path`, `product_stock`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Dell XPS 15', 'Laptop with 16GB RAM, 512GB SSD,i7 processor', 230000.00, 'assets/uploads/laptop1.jpg', 10, 1, '2025-11-21 14:00:25', '2025-12-25 08:30:37'),
(2, 'iPhone 15 Pro', 'Latest iPhone with 128GB storage, Pro camera system', 190000.00, 'assets/uploads/iphone.jpg', 15, 17, '2025-10-11 11:27:53', '2025-12-16 20:55:01'),
(3, 'Samsung Galaxy S24', 'Android smartphone with 256GB storage, 5G support', 180000.00, 'assets/uploads/samsung.jpg', 12, 17, '2025-09-23 01:48:50', '2025-11-13 01:17:14'),
(4, 'Wireless Mouse', 'Ergonomic wireless mouse with long battery life', 2999.00, 'assets/uploads/mouse.jpg', 50, 13, '2025-10-30 13:13:37', '2025-11-10 13:52:48'),
(5, 'Mechanical Keyboard', 'RGB backlit mechanical keyboard with blue switches', 4990.00, 'assets/uploads/keyboard.jpg', 30, 13, '2025-12-25 23:34:55', '2025-12-29 09:50:54'),
(6, 'Apple MacBook Air M2', '13-inch Retina Display, 8GB RAM, 256GB SSD, M2 Chip', 175000.00, 'assets/uploads/macbook_air.jpg', 8, 1, '2025-08-31 18:45:26', '2025-11-21 20:01:38'),
(7, 'HP Gaming Laptop', 'Ryzen 5, 16GB RAM, RTX 3050, 512GB SSD', 155000.00, 'assets/uploads/hp_pavilion.jpg', 10, 1, '2025-09-02 04:44:11', '2025-10-28 23:13:18'),
(8, 'Lenovo ThinkPad E14', 'Business laptop with 16GB RAM, 512GB SSD, i5 12th Gen', 140000.00, 'assets/uploads/thinkpad.jpg', 12, 1, '2025-08-18 00:00:07', '2025-09-30 05:16:45'),
(9, 'Sony WH Headphones', 'Noise cancelling wireless over-ear headphones', 58000.00, 'assets/uploads/sony_headphone.jpg', 20, 14, '2025-09-24 14:29:37', '2025-11-05 21:27:45'),
(10, 'JBL Charge 5 Speaker', 'Portable Bluetooth speaker with deep bass', 24000.00, 'assets/uploads/jbl.jpg', 25, 14, '2025-11-23 01:35:45', '2025-12-24 17:43:16'),
(11, 'Apple AirPods Pro 2', 'Noise cancellation, wireless charging case', 42000.00, 'assets/uploads/airpods.jpg', 30, 14, '2025-09-04 07:26:14', '2025-11-14 15:08:54'),
(12, 'Samsung Galaxy Tab S9', '11-inch AMOLED Display, 8GB RAM, 256GB', 120000.00, 'assets/uploads/galaxy_tab.jpg', 10, 18, '2025-08-29 16:21:44', '2025-10-12 06:04:36'),
(13, 'iPad 10th Gen', '10.9-inch, 64GB storage, A14 chip', 95000.00, 'assets/uploads/ipad10.jpg', 12, 18, '2025-09-05 07:16:28', '2025-10-13 12:21:10'),
(14, 'Apple Watch Series 9', 'Fitness tracking smartwatch with GPS', 78000.00, 'assets/uploads/apple_watch.jpg', 18, 19, '2025-09-20 06:51:27', '2025-11-07 05:13:24'),
(15, 'Samsung Watch 6', 'Premium fitness smartwatch', 55000.00, 'assets/uploads/galaxy_watch.jpg', 22, 19, '2025-11-13 08:02:12', '2025-12-19 07:49:07'),
(16, 'Logitech G502 Mouse', 'Gaming mouse with precision sensor and RGB', 9500.00, 'assets/uploads/logitech_g502.jpg', 35, 13, '2025-09-08 07:01:21', '2025-10-28 10:23:50'),
(17, 'Razer BlackWidow V3', 'Mechanical gaming keyboard with RGB lighting', 21000.00, 'assets/uploads/razer_keyboard.jpg', 28, 13, '2025-09-18 21:57:09', '2025-11-26 17:06:26'),
(18, 'Asus ROG Strix Gaming Laptop', 'i7 13th Gen, RTX 4060, 16GB RAM, 1TB SSD', 245000.00, 'assets/uploads/asus_rog.jpg', 6, 1, '2025-08-02 23:12:09', '2025-12-26 06:56:53'),
(19, 'Dell Monitor 27 Inch', 'Full HD IPS LED Monitor 144Hz', 45000.00, 'assets/uploads/dell_monitor.jpg', 15, 4, '2025-10-17 01:08:34', '2025-11-04 12:47:20'),
(20, 'Canon EOS 250D DSLR', '24.1MP DSLR camera with lens kit', 95000.00, 'assets/uploads/canon_dslr.jpg', 7, 24, '2025-12-20 02:07:23', '2026-01-16 13:13:38'),
(21, 'GoPro Hero 12', 'Action camera 5K video, waterproof', 78000.00, 'assets/uploads/gopro12.jpg', 14, 24, '2025-10-22 21:06:59', '2026-01-16 13:13:38'),
(22, 'Portable SSD 1TB', 'High-speed external solid state drive USB-C', 18000.00, 'assets/uploads/ssd1tb.jpg', 40, 9, '2025-08-12 05:10:20', '2025-12-18 18:02:06'),
(23, 'Sandisk 128GB Pendrive', 'USB 3.2 high speed pendrive with micro usb   ', 2200.00, 'assets/uploads/pendrive.jpg', 60, 9, '2025-09-09 17:25:55', '2025-09-22 11:55:05'),
(24, 'Gaming Chair RGB Edition', 'Ergonomic gaming chair with RGB lighting', 38000.00, 'assets/uploads/gaming_chair.jpg', 9, 3, '2025-09-27 02:58:30', '2025-11-01 10:23:41'),
(25, 'TP-Link WiFi 6 Router', 'High-speed dual band gigabit WiFi router', 18000.00, 'assets/uploads/wifi_router.jpg', 20, 20, '2025-11-17 17:56:32', '2025-12-17 09:17:16'),
(26, 'Acer HD LED Backlit Computer Monitor', 'Brand: Acer, Item Height: 36.2 Centimeters, Item Width: 46.3 Centimeters, Standing screen display size: 19.5 Inches, Screen Resolution: 1366 x 768 Pixels', 182999.00, 'assets/uploads/products/acer-hd-led-backlit-computer-monitor.png', 14, 4, '2025-11-13 23:15:23', '2025-12-30 14:31:49'),
(27, 'Acer Aspire 5', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 64999.00, 'assets/uploads/products/acer-aspire-5.png', 5, 1, '2025-10-01 10:26:56', '2025-10-10 02:25:51'),
(28, 'Acer Aspire A515-57G Intel i5 1235U 8GB Memory 256GB Storage NVIDIA MX 550 2GB WINDOWS 10', 'Resolution: 1920 x 1080, Size: 15.6 inches, Type: IPS LCD, Refresh rate: 60 Hz', 89999.00, 'assets/uploads/products/acer-aspire-a515-57g-intel-i5-1235u-8gb-memory-256.jpg', 7, 1, '2025-11-13 09:27:04', '2025-12-22 01:33:32'),
(30, 'Acer Nitro VG2 27\" Gaming Monitor', 'when Standy: 400 mW, when Off: 310mW, when Max: 60W, when On: 26W', 51499.00, 'assets/uploads/products/acer-nitro-vg2-27-gaming-monitor.png', 13, 4, '2025-08-10 15:28:26', '2025-10-30 03:09:21'),
(33, 'Acer Predator Helios 300 i7 12700H 16GB DDR5 512GB NVME RTX 3060 6GB Windows 10 Home', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 182999.00, 'assets/uploads/products/acer-predator-helios-300-i7-12700h-16gb-ddr5-512gb.png', 11, 1, '2025-11-07 04:27:45', '2025-11-16 19:01:41'),
(34, 'Acer Swift 3', 'The Acer Swift 3 is a small and lightweight device with an 11th Gen Intel Core i5-1135G7 CPU that can handle a wide range of jobs and applications with ease. The laptop has a 14-inch IPS display with 100% sRGB coverage, so your visuals will be clear and colorful. Furthermore, the laptop comes with 8GB of DDR4 RAM and 512GB of SSD storage. At 1.2 kilograms, the laptop is also extremely light. A backlit keyboard, a fingerprint reader, a webcam, and a multitude of connectivity connectors are also i', 65999.00, 'assets/uploads/products/acer-swift-3.png', 14, 1, '2025-08-06 02:53:10', '2025-12-21 05:41:51'),
(35, 'Addlink AddGame A X70 SSD M.2 PCIe Gen3x4', 'The Addlink AddGame A X70 SSD is a PCIe Gen3 x4 NVMe M.2 SSD with speeds up to 3500/3000 MB/s, capacities from 256GB to 2TB, and features RGB cooling, LDPC ECC, and SSD Toolbox software.', 6999.00, 'assets/uploads/products/addlink-addgame-a-x70-ssd-m2-pcie-gen3x4.png', 10, 9, '2025-08-06 21:49:24', '2025-09-29 11:34:31'),
(36, 'Explore All Products', 'Your trusted source for tech in Nepal', 145499.00, 'assets/uploads/products/explore-all-products.png', 8, 16, '2025-09-15 22:07:55', '2025-09-17 04:29:55'),
(37, 'iPad (9th Gen)', 'Material: Aluminum Back and Frame with Glass Front, Weight: 487 g, Dimensions (inches): 9.87 x 6.85 x 0.30', 219999.00, 'assets/uploads/products/ipad-9th-gen.png', 14, 18, '2025-12-25 12:02:47', '2025-12-28 21:20:03'),
(38, 'iPad Air (4th Gen)', 'Material: Aluminum Back and Frame with Glass Front, Weight: 460 g, Dimensions (inches): 9.74 x 7.02 x 0.24', 104100.00, 'assets/uploads/products/ipad-air-4th-gen.png', 9, 18, '2025-10-22 14:54:43', '2025-12-06 16:13:08'),
(39, 'iPad Air 5th Gen (M1 Series)', 'Material: Aluminum Back and Frame with Glass Front, Weight: 462 g, Dimensions (inches): 9.74 x 7.02 x 0.24', 432499.00, 'assets/uploads/products/ipad-air-5th-gen-m1-series.png', 6, 18, '2025-08-30 11:06:15', '2025-11-25 04:36:37'),
(40, 'Apple Mac Mini M2 16GB Memory 256GB SSD', 'Simultaneous Display: Two, Video Playback: Supported formats include HEVC, H.264, and ProRes HDR with Dolby Vision, HDR10, and HLG', 222999.00, 'assets/uploads/products/apple-mac-mini-m2-16gb-memory-256gb-ssd.png', 6, 2, '2025-09-18 05:45:04', '2025-12-15 06:14:29'),
(41, 'Apple MacBook M2 Air  13\" 16GB Memory 256GB SSD', 'Size: 13.6\" (diagonal), Technology: LED-backlit display with IPS technology, Resolution: 2560-by-1664 native resolution @ 224 pixels per inch, Brightness: 500 nits', 223499.00, 'assets/uploads/products/apple-macbook-m2-air-13-16gb-memory-256gb-ssd.jpg', 13, 1, '2025-11-11 20:41:54', '2025-11-23 06:05:36'),
(42, 'Apple MacBook Air 13 inch M2 Chip 16GB Memory 512GB SSD', 'Size: 13.6\" (diagonal), Technology: LED-backlit display with IPS technology, Resolution: 2560-by-1664 native resolution @ 224 pixels per inch, Brightness: 500 nits', 264999.00, 'assets/uploads/products/apple-macbook-air-13-inch-m2-chip-16gb-memory-512g.jpg', 9, 1, '2025-09-23 09:23:46', '2025-11-09 11:13:48'),
(43, 'Apple MacBook Pro 16\" M2 Max Chip 32GB Memory 1TB SSD', 'Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 525499.00, 'assets/uploads/products/apple-macbook-pro-16-m2-max-chip-32gb-memory-1tb-s.jpg', 6, 1, '2025-11-08 23:13:18', '2025-12-09 22:26:28'),
(44, 'Apple MacBook Pro 14 inch M2 Pro Chip 16GB Memory 512GB SSD', 'Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 304999.00, 'assets/uploads/products/apple-macbook-pro-14-inch-m2-pro-chip-16gb-memory-.jpg', 14, 1, '2025-08-26 01:22:36', '2025-11-24 18:48:41'),
(45, 'Apple MacBook Pro 14  inch M2 Pro Chip 32GB Memory 1TB SSD', 'Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 509999.00, 'assets/uploads/products/apple-macbook-pro-14-inch-m2-pro-chip-32gb-memory-.jpg', 8, 1, '2025-08-23 05:02:35', '2025-11-12 13:50:33'),
(46, 'Apple MacBook Pro 16 inch M2 Pro Chip 16GB Memory 1TB SSD', 'Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 405499.00, 'assets/uploads/products/apple-macbook-pro-16-inch-m2-pro-chip-16gb-memory-.jpg', 10, 1, '2025-10-15 07:28:30', '2025-12-22 01:48:49'),
(47, 'Apple MacBook Pro 16 inch  M2 Pro chip 16GB Memory 512GB SSD', 'Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 379999.00, 'assets/uploads/products/apple-macbook-pro-16-inch-m2-pro-chip-16gb-memory-.jpg', 7, 1, '2025-10-30 18:33:29', '2025-12-04 01:10:52'),
(48, 'MacBook Pro 13\" M1', 'Material: Aluminium, Weight: 1.4 kg, Dimensions (inches): 11.97 x 8.36 x 0.61', 451499.00, 'assets/uploads/products/macbook-pro-13-m1.png', 7, 1, '2025-08-10 22:53:40', '2025-10-20 09:34:47'),
(49, 'Apple MacBook Pro 13\" M2 Chip', 'Technology: Retina display, Size: 13.3-inch (diagonal), Type: LED-backlit display with IPS technology, Resolution: 2560 x 1600 native resolution @ 227 pixels per inch', 244999.00, 'assets/uploads/products/apple-macbook-pro-13-m2-chip.png', 9, 1, '2025-10-25 04:43:06', '2025-10-30 05:52:39'),
(50, 'MacBook  Pro 14 inch M2 Pro Chip16GB Memory 1TB SSD', 'Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 379999.00, 'assets/uploads/products/macbook-pro-14-inch-m2-pro-chip16gb-memory-1tb-ssd.jpg', 5, 1, '2025-09-15 13:27:36', '2025-10-27 16:58:46'),
(51, 'Apple MacBook Pro 14\" M2 Pro Chip 32GB Memory 512GB SSD', 'Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 364999.00, 'assets/uploads/products/apple-macbook-pro-14-m2-pro-chip-32gb-memory-512gb.jpg', 6, 1, '2025-09-19 21:53:51', '2025-10-21 20:57:09'),
(52, 'Apple MacBook Pro 16\" M2 Max Chip 64GB Memory 1TB SSD', 'Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1', 619999.00, 'assets/uploads/products/apple-macbook-pro-16-m2-max-chip-64gb-memory-1tb-s.jpg', 10, 1, '2025-09-24 14:32:48', '2025-10-14 03:44:06'),
(53, 'Apple Studio Display', 'The Apple Studio Display is a 27-inch high-resolution screen with bright, true-to-life colors. It has a great built-in camera for video calls, powerful speakers for clear sound, and three microphones. It connects easily to your Mac with Thunderbolt and USB-C ports. The design is sleek and can reduce glare with a special glass option. It’s perfect for creative work and multimedia.', 320499.00, 'assets/uploads/products/apple-studio-display.png', 14, 4, '2025-12-20 12:05:00', '2025-12-27 04:58:19'),
(54, 'Apple USB-C Charge Cable 2 m', 'The Apple USB-C Charge Cable (2 m) is a durable, woven cable that supports fast charging up to 240W and data transfer. It’s compatible with USB-C Apple devices and pairs with USB-C power adapters for efficient charging.', 4499.00, 'assets/uploads/products/apple-usb-c-charge-cable-2-m.jpg', 8, 16, '2025-12-25 13:27:37', '2025-12-29 01:39:01'),
(55, 'ASRock  B365M PRO4 Motherboard Intel', 'The ASRock B365M PRO4 is a MicroATX motherboard for 8th and 9th Gen Intel Core CPUs, supporting up to 64GB DDR4 RAM. It includes 2 PCIe x16 slots, 6 SATA ports, 2 M.2 slots, Intel Gigabit LAN, and 7 USB ports. Suitable for mid-range gaming and productivity.', 12999.00, 'assets/uploads/products/asrock-b365m-pro4-motherboard-intel.png', 8, 7, '2025-10-06 16:40:16', '2025-10-14 08:34:53'),
(56, 'ASRock B860 Steel Legend WiFi', 'The ASRock B860 Steel Legend WiFi is a mid-range ATX motherboard for Intel Arrow Lake CPUs, supporting up to 256 GB DDR5, PCIe 5.0, Wi-Fi 6E, Bluetooth 5.3, 2.5 GbE LAN, and Thunderbolt 4.', 12999.00, 'assets/uploads/products/asrock-b860-steel-legend-wifi.png', 6, 7, '2025-10-05 00:11:59', '2025-10-15 14:53:36'),
(57, 'Asus ProArt Display', 'Panel Size: 27 inch, Pixels Per Inch: 109 PPI, Aspect Ratio: 16:9, Display Viewing Area (H x V): 596.74 x 335.66 mm, Display Surface: Non-Glare', 78499.00, 'assets/uploads/products/asus-proart-display.jpg', 13, 4, '2025-08-11 05:21:11', '2025-11-01 14:18:55'),
(58, 'ROG Zephyrus G14', 'Built-In Microphone: Yes, Front-Facing Camera: No, Capacity: 4 Cell Li-Ion 76 Wh', 157999.00, 'assets/uploads/products/rog-zephyrus-g14.png', 8, 1, '2025-12-09 19:58:25', '2025-12-31 09:51:06'),
(59, 'Asus ROG Zephyrus G14 Ryzen 7 4800HS 8GB Memory 512GB Storage Nvidia GTX 1650 4GB WINDOWS 10', 'Size: 14\", Resolution: 1920 x 1080 Full HD, Refresh Rate: 120Hz, Panel: Anti-glare, IPS 100% sRGB', 38499.00, 'assets/uploads/products/asus-rog-zephyrus-g14-ryzen-7-4800hs-8gb-memory-51.jpg', 14, 1, '2025-12-12 00:58:50', '2025-12-12 14:42:52'),
(60, 'Asus ROG Zephyrus G14 Ryzen 9 5900HS 16GB Memory 1TB Storage Nvidia RTX 3060 6GB WINDOWS 10', 'Resolution: 1920 x 1080 Full HD, Refresh Rate: 144Hz, Size: 14\", Panel: Anti-Glare, IPS 100% sRGB', 167999.00, 'assets/uploads/products/asus-rog-zephyrus-g14-ryzen-9-5900hs-16gb-memory-1.jpg', 5, 1, '2025-12-25 10:48:45', '2025-12-25 17:46:50'),
(61, 'TUF Dash F15', 'Device: DTS software Built-in array microphone 2-speaker system, Integrated: yes, Type: 4S1P, 4-cell Li-ion', 134999.00, 'assets/uploads/products/tuf-dash-f15.png', 10, 1, '2025-10-20 02:25:48', '2025-11-28 09:10:42'),
(62, 'Asus Tuf f15 i7 12th gen 16GB DDR5 512GB SSD RTX 3060 6GB', 'Display: 15.6 inches FHD 1080p IPS  Anit-glare 144Hz 250nits, camera: 1MP 720P Front Facing Camera with Dual-Array Microphone, Features: Dual-Array Microphones, Speaker Type: Dual 2W Stereo speakers, Touchpad Type: Precision Trackpad', 184499.00, 'assets/uploads/products/asus-tuf-f15-i7-12th-gen-16gb-ddr5-512gb-ssd-rtx-3.jpg', 10, 1, '2025-09-16 13:22:09', '2025-11-05 09:03:05'),
(63, 'Asus Tuf F15 Gaming Laptop 12th gen i7 12700H 16GB DDR5 1TB NVME SSD RTX 3060 6GB', 'Display: 15.6 inches FHD 1080p IPS  Anit-glare 144Hz 250nits, camera: 1MP 720P Front Facing Camera with Dual-Array Microphone, Features: Dual-Array Microphones, Speaker Type: Dual 2W Stereo speakers, Touchpad Type: Precision Trackpad', 197999.00, 'assets/uploads/products/asus-tuf-f15-gaming-laptop-12th-gen-i7-12700h-16gb.jpg', 6, 1, '2025-11-11 06:52:33', '2025-12-27 02:19:17'),
(64, 'ASUS TUF Gaming A15', 'LCD: 15.6\" Full HD, WV, VRAM: 4GB, WLAN: Wi-Fi 6 11AX2*2_WW + BT, USB: USB 3.2A * 2, USB 3.2C * 2, OS: Windows 10 Home', 131999.00, 'assets/uploads/products/asus-tuf-gaming-a15.jpg', 14, 1, '2025-09-28 10:27:04', '2025-12-02 15:33:56'),
(65, 'ASUS TUF Gaming F16 (2025)', 'The ASUS TUF Gaming F16 FX608LP-BS96 (32GB/1TB) is a powerful 16-inch gaming laptop with an Intel Core Ultra 9 CPU, NVIDIA RTX 5070 GPU, 2.5K 165Hz display, and fast DDR5 RAM—designed for high-performance gaming and creative work.', 25499.00, 'assets/uploads/products/asus-tuf-gaming-f16-2025.png', 8, 1, '2025-12-31 04:10:28', '2025-12-31 18:06:33'),
(66, 'Canon Cartridge 308', 'The Canon Cartridge 308 is a black toner for LBP3360 and LBP3300 printers, yielding about 2,500 pages with sharp text and graphics.', 9499.00, 'assets/uploads/products/canon-cartridge-308.jpg', 12, 21, '2025-11-04 14:34:07', '2025-12-10 07:10:03'),
(67, 'Canon Cartridge 326', 'The Canon Cartridge 308 is a toner for LBP3300 and LBP3360 printers, yielding 2,500 pages and ensuring sharp, professional prints.', 8599.00, 'assets/uploads/products/canon-cartridge-326.jpg', 6, 21, '2025-12-13 07:31:38', '2025-12-20 08:25:01'),
(68, 'Canon imageCLASS MF441dw', 'Device Memory: 1 GB, Display: WVGA Colour LCD 5-inch Touch Screen Display, Weight (approx.): 16.2 kg', 104999.00, 'assets/uploads/products/canon-imageclass-mf441dw.jpg', 14, 21, '2025-09-15 01:56:30', '2025-11-08 00:04:20'),
(69, 'Canon iR-2006N Digital Copier with Duplex and RADF', 'Color: White, Dimensions (W x D x H): 622 x 589 x 607mm (with ADF), Weight: Approximately 35.5 kg (with ADF)', 209999.00, 'assets/uploads/products/canon-ir-2006n-digital-copier-with-duplex-and-radf.png', 8, 21, '2025-12-13 14:50:23', '2025-12-18 14:01:26'),
(70, 'Canon Laser Shot LBP 2900 Printer', 'Type: Desktop Page Printer, Printing method: Electrophoto Method (On-demand fixing), Printing software: CAPT (Canon Advanced Printing Technology), Print speed: Plain paper(64 to 90 g/m2) When printing A4 continuously 12 pages/min.', 25499.00, 'assets/uploads/products/canon-laser-shot-lbp-2900-printer.jpg', 10, 21, '2025-11-10 23:09:57', '2025-11-17 16:22:51'),
(71, 'CAT-5 UTP 2M Cable', 'Type: CAT-5 Cable, Length: 2m, Connector: RJ-45', 449.00, 'assets/uploads/products/cat-5-utp-2m-cable.jpg', 14, 20, '2025-12-21 17:04:22', '2025-12-26 13:12:30'),
(72, 'Colorful B550M Gaming Frozen Motherboard AMD', 'The B550M Gaming Frozen motherboard supports AMD AM4 CPUs, offers 4 DDR4 slots, PCIe 4.0, M.2, SATA ports, USB 3.1, HDMI, and 8-channel audio. It features RGB lighting, efficient cooling, and a 3-year warranty.', 13499.00, 'assets/uploads/products/colorful-b550m-gaming-frozen-motherboard-amd.jpg', 11, 7, '2025-09-05 11:17:56', '2025-11-13 14:07:44'),
(73, 'Colorful Z490 Gaming Pro Motherboard Intel', 'The Colorful Z490 Gaming Pro is an ATX motherboard for 10th Gen Intel Core processors, featuring 4 DDR4 DIMM slots, multiple PCIe and M.2 slots, and 6 SATA ports. It offers USB 3.2, HDMI, DisplayPort, and Realtek Gigabit LAN, supporting up to 125W CPUs with a 128MB UEFI BIOS.', 19499.00, 'assets/uploads/products/colorful-z490-gaming-pro-motherboard-intel.jpg', 14, 7, '2025-10-19 23:44:48', '2025-11-30 10:08:31'),
(74, 'Cryorig M9 CPU air cooler', 'The Cryorig M9 is a compact tower CPU cooler with three 6mm copper heatpipes and a copper base for optimal heat conduction. It features a 92mm PWM fan operating at 600–2200 RPM, with a noise level of 26.4 dBA, max airflow of 48.4 CFM, and static pressure of 3.1 mmH2O.', 4199.00, 'assets/uploads/products/cryorig-m9-cpu-air-cooler.png', 15, 12, '2025-10-06 17:54:19', '2025-12-03 02:35:47'),
(75, 'Dell 24 Monitor – S2421HN', 'The Dell 2.1 Speaker System AE415 features 30W RMS power, clear sound, and a compact design. It includes dual 3.5mm inputs and a headphone jack, making it ideal for desktops and notebooks.', 26499.00, 'assets/uploads/products/dell-24-monitor-s2421hn.webp', 7, 4, '2025-09-13 17:20:31', '2025-10-16 16:03:19'),
(76, 'Dell 27\" FHD IPS Monitor', 'Display Type: LED-backlit LCD monitor / TFT active matrix, Adaptive-Sync Technology: AMD FreeSync, Native Resolution: Full HD (1080p) 1920 x 1080 at 75 Hz, Panel Type: IPS', 34299.00, 'assets/uploads/products/dell-27-fhd-ips-monitor.png', 6, 4, '2025-12-13 18:38:54', '2025-12-19 22:15:31'),
(77, 'Dell Chromebook 13 3380', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 31999.00, 'assets/uploads/products/dell-chromebook-13-3380.png', 11, 1, '2025-09-15 08:22:49', '2025-12-04 14:20:58'),
(78, 'Dell Inspiron 14 5425 Ryzen 5-5625U 16GB RAM 512GB SSD', 'The Dell Inspiron 14 5425 is powered by AMD Ryzen 5-5625 processor. It has AMD integrated Radeon graphics as a GPU which is RX Vega 7. This laptop comes with 16GB of DDR4 RAM. This comes with 512 GB of M.2 SSD storage, which can be increased.', 97999.00, 'assets/uploads/products/dell-inspiron-14-5425-ryzen-5-5625u-16gb-ram-512gb.webp', 14, 1, '2025-12-08 12:29:58', '2025-12-12 04:39:38'),
(79, 'Dell Latitude 7330', 'Size: 13.3\", Resolution: FHD (1920x1080), Others: AG, SLP, No-Touch, ComfView+, WVA, 400 nits, FHD IR Cam+IP, WLAN, CF', 4999.00, 'assets/uploads/products/dell-latitude-7330.jpg', 10, 1, '2025-12-13 02:29:03', '2025-12-23 20:19:01'),
(80, 'Dell Latitude 7430', 'Screen Size: 14 inch, Resolution: 1080 x 1920 Pixels, Screen Type: FHD AG, Non-Touch, WVA, 250 nits, HD RGB Cam, WLAN, Carbon Fiber', 154499.00, 'assets/uploads/products/dell-latitude-7430.jpg', 15, 1, '2025-09-29 19:26:43', '2025-10-04 21:43:56'),
(81, 'Dell OptiPlex 5400 AIO', 'OS: Windows 10 Professional, Monitor: OptiPlex All-in-One (Touch / Non-Touch), Size: 23.8\"', 134999.00, 'assets/uploads/products/dell-optiplex-5400-aio.png', 5, 2, '2025-09-01 03:31:12', '2026-01-16 13:13:38'),
(82, 'Dell PowerEdge R740', 'The Dell PowerEdge R740 is a 2U rack server with dual Intel Xeon Scalable processors (up to 28 cores each), supporting up to 3TB of DDR4 RAM and flexible storage options (up to 16 x 2.5” or 8 x 3.5” drives). It can support up to three 300W or six 150W GPUs for accelerated workloads, making it ideal for demanding applications like VDI and AI. The server provides advanced management tools, robust security features, and versatile networking options, balancing performance, scalability, and reliabili', 1106999.00, 'assets/uploads/products/dell-poweredge-r740.jpg', 8, 22, '2025-09-23 23:11:05', '2025-11-26 21:54:54'),
(83, 'Dell Precision 3470 Workstation', 'Display: 14\" Full HD IPS Display, Wireless: Intel AX211, 2x2 MIMO, 2400 Mbps, 2.4/5/6 GHz, Wi-Fi 6/6E (WiFi 802.11ax), Bluetooth 5.2, Keyboard: Single Pointing Non-Backlit English International Keyboard, Palm Rest: Single Pointing, No Security, Battery: 4 Cell, 64WHr, standard battery', 134999.00, 'assets/uploads/products/dell-precision-3470-workstation.png', 5, 1, '2025-12-23 00:42:32', '2026-01-16 13:13:38'),
(84, 'Dell 24\" UltraSharp Monitor', 'Display Type: LED-backlit LCD monitor / TFT active matrix, Diagonal Size: 23.8\", Viewable Size: 23.8\", Panel Type: IPS, Built-in Devices: USB 3.0 hub', 42999.00, 'assets/uploads/products/dell-24-ultrasharp-monitor.png', 9, 4, '2025-09-21 10:34:39', '2026-01-16 13:13:38'),
(85, 'Dell Vostro 3510', 'Type: Full HD / HD, Size: 15.6\", Type: Factory Installed / Preinstalled', 61999.00, 'assets/uploads/products/dell-vostro-3510.jpg', 5, 1, '2025-10-15 20:20:05', '2026-01-16 13:13:38'),
(86, 'Dell XPS 13 i7 12th gen', 'Size: 13.4\", Resolution: FHD+ 1920 x 1200, Refresh Rate: 60Hz, Others: Non-Touch, Anti-Glare, 500 nit, InfinityEdge', 224999.00, 'assets/uploads/products/dell-xps-13-i7-12th-gen.jpg', 12, 1, '2025-12-17 15:56:47', '2026-01-16 13:13:38'),
(87, 'Dell XPS 13 Plus 9320 i7 12th gen 13.4 OLED  32GB DDR5  1TB  SSD', 'Size: 13.4\", Resolution: 3.5K 3456 x 2160 with Infinity Edge, Refresh Rate: 60Hz, Type: OLED Touch, Anti-Glare', 303499.00, 'assets/uploads/products/dell-xps-13-plus-9320-i7-12th-gen-134-oled-32gb-dd.jpg', 12, 1, '2025-12-28 03:17:01', '2026-01-10 15:18:05'),
(88, 'Dell XPS 13 Plus 9320 i7 1260p 16GB RAM 512GB SSD Windows 11', 'Size: 13.4\", Resolution: 1920x1200, FHD+ with Infinity Edge, Refresh Rate: 60Hz, Type: Touch, Anti-Glare', 243499.00, 'assets/uploads/products/dell-xps-13-plus-9320-i7-1260p-16gb-ram-512gb-ssd-.jpg', 7, 1, '2025-11-10 13:11:18', '2026-01-16 13:13:38'),
(89, 'Dell XPS 13Plus 9320  i7 12th gen 13.4 OLED 16GB 512GB', 'Size: 13.4\", Resolution: 3.5K 3456 x 2160 with Infinity Edge, Refresh Rate: 60Hz, Type: OLED Touch, Anti-Glare', 263499.00, 'assets/uploads/products/dell-xps-13plus-9320-i7-12th-gen-134-oled-16gb-512.jpg', 7, 1, '2025-10-03 11:37:41', '2026-01-16 13:13:38'),
(90, 'Dell XPS 15 9520 i7 12700H 16GB RAM 512GB SSD Windows 11', 'Size: 15.6\", Resolution: 1920x1200, FHD+ with Infinity Edge, Refresh Rate: 60Hz, Type: Non-Touch, Anti-Glare', 255499.00, 'assets/uploads/products/dell-xps-15-9520-i7-12700h-16gb-ram-512gb-ssd-wind.jpg', 7, 1, '2025-11-25 20:26:40', '2026-01-16 13:13:38'),
(91, 'Dell XPS 17 9720', 'Material: Aluminum, Weight: 2.21 kg (non-touch)2.42 kg (touch), Dimensions (inches): 14.74 x 9.76 x 0.77', 91999.00, 'assets/uploads/products/dell-xps-17-9720.webp', 5, 1, '2025-11-07 01:49:57', '2026-01-16 13:13:38'),
(92, 'Portege X30-G', 'The Dynabook Portege X30-G is a lightweight 13.3\" laptop with an Intel i5, 8GB RAM, and 256GB SSD. It features a Full HD display, Wi-Fi 6, Bluetooth 5.1, security options, and meets military durability standards, making it ideal for business on the go.', 95499.00, 'assets/uploads/products/portege-x30-g.jpg', 11, 1, '2025-08-01 04:28:44', '2026-01-16 13:13:38'),
(93, 'E55BT WIreless', 'The JBL E55BT are wireless over-ear headphones with 50mm drivers delivering balanced JBL sound, Bluetooth 4.0 with multipoint support, and up to 20 hours of battery life. They feature comfortable foldable design, on-ear controls, a detachable wired cable, and hands-free calling, making them ideal for everyday wireless listening.', 9499.00, 'assets/uploads/products/e55bt-wireless.png', 5, 14, '2025-11-03 00:38:14', '2025-11-04 10:27:14'),
(94, 'GALAX GeForce RTX™ 4080', 'CUDA Cores: 9728, Boost Clock: 2565MHz, 1-Click OC Clock: 2580MHz', 216499.00, 'assets/uploads/products/galax-geforce-rtx-4080.png', 9, 6, '2025-10-29 14:51:24', '2025-11-13 12:42:57'),
(95, 'GALAX GeForce RTX™ 4090', 'CUDA Cores: 16384, Boost Clock: 2580MHz, 1-Click OC Clock: 2595MHz', 304499.00, 'assets/uploads/products/galax-geforce-rtx-4090.png', 6, 6, '2025-09-09 07:23:55', '2025-10-08 19:04:48'),
(96, 'Gigabyte Z890M Gaming X ( Micro-ATX )', 'The Gigabyte Z890M Gaming X is a compact micro-ATX motherboard for Intel Core Ultra Series 2 (Arrow Lake-S) processors. It supports up to 256 GB DDR5 RAM (up to DDR5-9066+ OC), PCIe 5.0 for the primary GPU slot, has 3 M.2 slots, 4 SATA ports, 2.5 GbE LAN but no Wi-Fi, and multiple display outputs (HDMI 2.1 and dual DisplayPort 2.1). It features good VRM cooling, supports CPU overclocking, and is designed for high-performance gaming and productivity in a smaller form factor.', 76499.00, 'assets/uploads/products/gigabyte-z890m-gaming-x-micro-atx-.png', 11, 7, '2025-10-10 08:56:38', '2026-01-16 13:13:38'),
(97, 'Google Chromecast 4K UHD', 'The Google Chromecast 4K UHD streams 4K HDR video with Dolby Vision and Dolby Atmos, features a voice remote with Google Assistant, runs Android TV, and supports thousands of apps for high-quality streaming.', 11999.00, 'assets/uploads/products/google-chromecast-4k-uhd.jpg', 12, 16, '2025-10-06 12:51:40', '2026-01-16 13:13:38'),
(98, 'Google Chromecast 1080p HD', 'The Google Chromecast 1080p HD is a compact streaming device supporting Full HD (1080p) HDR video with Dolby audio. It runs Android TV with Google TV, includes a voice remote, offers Wi-Fi and Bluetooth connectivity, and supports thousands of apps. It’s an affordable option for streaming HD content on any HDMI-equipped TV.', 6999.00, 'assets/uploads/products/google-chromecast-1080p-hd.jpg', 11, 16, '2025-12-20 17:05:26', '2026-01-16 13:13:38'),
(99, 'Google Pixel 7', 'Material: Glass Front & Back, with Aluminum Frame, Weight: 197 g, Dimensions (inches): 6.13 x 2.88 x 0.34', 11999.00, 'assets/uploads/products/google-pixel-7.png', 5, 17, '2025-09-28 06:41:41', '2026-01-16 13:13:38'),
(100, 'H510 FLOW', 'The NZXT H510 Flow is a compact mid-tower PC case with excellent airflow, featuring a perforated front panel and two 120mm fans. It supports various motherboards and liquid cooling up to 360mm in the front. Its design offers improved cooling and a stylish look, perfect for high-performance gaming builds.', 13999.00, 'assets/uploads/products/h510-flow.png', 10, 11, '2025-09-18 18:23:37', '2026-01-16 13:13:38'),
(101, 'H510i', 'The NZXT H510i is a compact mid-tower ATX case with a tempered glass side panel, supporting Mini-ITX to ATX motherboards. It features integrated RGB and fan control via the Smart Device V2, good cooling options with pre-installed fans, USB-C front port, and excellent cable management for clean builds.', 19499.00, 'assets/uploads/products/h510i.png', 6, 11, '2025-11-07 10:44:24', '2026-01-16 13:13:38'),
(102, 'H710', 'The NZXT H710 is a spacious mid-tower ATX case with a tempered glass side panel, supporting up to E-ATX motherboards, large GPUs, and tall CPU coolers. It includes four pre-installed fans, supports multiple radiator sizes, features USB-C front ports, and offers excellent cable management and airflow—perfect for high-end gaming and workstation builds.', 21999.00, 'assets/uploads/products/h710.png', 6, 11, '2025-08-27 14:57:59', '2026-01-16 13:13:38'),
(103, 'H710i', 'The NZXT H710i is a spacious, premium mid-tower PC case with a tempered glass side panel, supporting up to E-ATX motherboards and large GPUs. It features excellent cooling options, built-in RGB and fan control via Smart Device V2, USB-C front port, and clean cable management—ideal for high-end gaming and workstation builds.', 26999.00, 'assets/uploads/products/h710i.png', 13, 11, '2025-08-19 21:39:33', '2026-01-16 13:13:38'),
(104, 'HDMI Cable 5m', 'Speed: ULTRA HIGH SPEED, Length: 5m, Connector: Type A to Type A, Resolution Support: 720P;1080L;1080P', 1199.00, 'assets/uploads/products/hdmi-cable-5m.png', 14, 16, '2025-09-29 20:37:32', '2026-01-16 13:13:38'),
(105, 'HP 14\" EliteBook 840 G8', 'Type: Factory Installed, OS: Windows 10 Pro, Architecture: 64-bit (x86_64)', 169999.00, 'assets/uploads/products/hp-14-elitebook-840-g8.png', 5, 1, '2025-12-02 15:24:00', '2026-01-16 13:13:38'),
(106, 'HP 49A Black Original Toner Cartridge', 'Brand: HP, Model: HP 49A, Part No: Q5949A, Print Technology: Laser', 11499.00, 'assets/uploads/products/hp-49a-black-original-toner-cartridge.webp', 9, 21, '2025-10-03 08:55:35', '2026-01-16 13:15:49'),
(107, 'HP 80A Black Original Toner Cartridge', 'Brand: HP, Model: HP 80A, Part No: CF280A, Printing Technology: Laser', 11499.00, 'assets/uploads/products/hp-80a-black-original-toner-cartridge.webp', 7, 21, '2025-10-24 22:25:20', '2026-01-16 13:15:49'),
(108, 'HP 85A Black Original Toner Cartridge', 'Brand: HP, Model: HP 85A, Part No: CE285A, Printing Technology: Laser', 9499.00, 'assets/uploads/products/hp-85a-black-original-toner-cartridge.jpg', 11, 21, '2025-11-07 18:28:03', '2026-01-16 13:15:49'),
(109, 'HP 90X High Yield Black Original LaserJet Toner Cartridge, CE390X', 'Color(s) of print cartridges: Black, Print technology: Laser, Page yield (black and white): ~24,000 pages', 2899.00, 'assets/uploads/products/hp-90x-high-yield-black-original-laserjet-toner-ca.webp', 8, 21, '2025-11-02 12:19:01', '2026-01-16 13:15:49'),
(110, 'HP Chromebook x360 14 G1 (2 in 1)', 'Type: Preinstalled, OS: Chrome OS, Internal: 14.0\" FHD IPS eDP Brightview WLED-backlit slim-flat (3.0 mm) touch screen', 54999.00, 'assets/uploads/products/hp-chromebook-x360-14-g1-2-in-1.jpg', 14, 1, '2025-10-26 07:20:48', '2026-01-16 13:15:49'),
(111, 'HP EliteBook x360 1030 G8 Notebook PC', 'Type: Preinstalled, OS: Windows 10 Pro 64-bit, WLAN: Intel Wi-Fi 6 AX201 802.11a/b/g/n/ac/ax (2x2) and Bluetooth 5 combo, (vPro / non - vPro)', 114499.00, 'assets/uploads/products/hp-elitebook-x360-1030-g8-notebook-pc.png', 6, 1, '2025-08-23 20:24:27', '2026-01-16 13:15:49'),
(112, 'Hp Envy X360', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 98499.00, 'assets/uploads/products/hp-envy-x360.jpg', 7, 1, '2025-10-18 16:22:48', '2026-01-16 13:15:49'),
(113, 'HP Laptop 15s', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 78499.00, 'assets/uploads/products/hp-laptop-15s.png', 13, 1, '2025-10-30 02:06:16', '2026-01-16 13:15:49'),
(114, 'HP Omen Transcend 14 Intel', 'With an ultra-thin and light all-metal chassis that feels as luxurious as it looks, a longer and more efficient battery life thanks to USB-C fast charging, a new Intel Core Ultra processor with powerful NVIDIA GeForce RTX graphics, and a 2.8K 120Hz OLED display that is both bright and fast.', 83999.00, 'assets/uploads/products/hp-omen-transcend-14-intel.png', 5, 1, '2025-10-14 11:31:08', '2026-01-16 13:15:49'),
(115, 'HP ProBook 430 G5 8th Gen i5 8250U, 8GB RAM, 256GB SSD, 13.3″ HD AG, Windows 11 Pro', 'Size: 13.3\" Diagonal, Type: FHD UWVA, Backlit: LED Backlit, Touch: Yes', 44999.00, 'assets/uploads/products/hp-probook-430-g5-8th-gen-i5-8250u-8gb-ram-256gb-s.jpg', 7, 1, '2025-10-31 01:56:32', '2026-01-16 13:15:49'),
(116, 'HP Spectre X360', 'Screen Size: 13.5\", Screen Monitor: OLED, User Interface Type: Yes, Display Resolution: 3000 x 2000 pixels', 169999.00, 'assets/uploads/products/hp-spectre-x360.jpg', 8, 1, '2025-10-21 09:49:58', '2026-01-16 13:15:49'),
(117, 'Hp Victus 15 Intel i5 12450H 8GB RAM 512GB SSD Nvidia GTX 1650 4GB Windows 11', 'This HP laptop comes equipped with an Geforce Nvidia GeForce GTX 1650 4GB Graphics, an Intel Core i7-12450 H processor, and 8 GB of RAM. You get a 15.6-inch screen laptop, which is midsize for a gaming setup. The weight of the laptop is 2.39 kg(Weight varies by configuration).', 97999.00, 'assets/uploads/products/hp-victus-15-intel-i5-12450h-8gb-ram-512gb-ssd-nvi.png', 7, 1, '2025-09-04 18:37:49', '2026-01-16 13:15:49'),
(118, 'HP ZBook Firefly 14 G8 Mobile Workstation', 'Vendor: Intel, Model: Core™ i7 1185G7, Cores: 4, Threads: 8', 214499.00, 'assets/uploads/products/hp-zbook-firefly-14-g8-mobile-workstation.png', 5, 1, '2025-11-28 10:09:37', '2026-01-16 13:15:49'),
(119, 'Huawei Display 23.8\"', 'Display Size: 23.8 inches, Dusplay Type: IPS, Aspect Ratio: 16 :9, Resolution: 1920 x 1080 (FHD), Refresh Rate: 75 Hz', 24999.00, 'assets/uploads/products/huawei-display-238.png', 6, 4, '2025-10-11 12:37:37', '2026-01-16 13:15:49'),
(120, 'Ideapad 3', 'The Lenovo Ideapad 3 is powered by AMD Ryzen 5550U processor. This SoC has six cores and twelve threads with a maximum boost frequency of 4.0 GHz. In addition, it features 8MB of L3 cache memory. It has AMD integrated Radeon graphics as a GPU.\r\n\r\n\r\n\r\nThis laptop comes with 8GB of DDR4 RAM, which may be increased to 12GB later on. This comes with 256GB of M.2 SSD storage, which can be increased.', 77999.00, 'assets/uploads/products/ideapad-3.png', 5, 1, '2025-10-25 17:13:03', '2026-01-16 13:15:49'),
(121, 'IdeaPad 3', 'Built-In Microphone: Yes, Front-Facing Camera: No', 154499.00, 'assets/uploads/products/ideapad-3.png', 12, 1, '2025-12-25 13:31:52', '2026-01-16 13:15:49'),
(122, 'IdeaPad  Slim 3 15', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 234999.00, 'assets/uploads/products/ideapad-slim-3-15.jpg', 13, 1, '2025-08-03 21:50:32', '2026-01-16 13:15:49'),
(123, 'iMac 24 inch', 'Display: 24-inch 4.5K Retina display, Camera: 1080p FaceTime HD camera, Audio: High-fidelity six-speaker system with force-cancelling woofers, Connections and Expansion: Two Thunderbolt / USB 4 ports with support for DisplayPort, Thunderbolt Version: Thunderbolt 3 (up to 40Gb/s)', 221999.00, 'assets/uploads/products/imac-24-inch.jpg', 12, 2, '2025-12-16 07:23:02', '2026-01-16 13:15:49'),
(124, 'Inspiron 15 3511', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 91999.00, 'assets/uploads/products/inspiron-15-3511.png', 6, 1, '2025-11-13 19:34:21', '2026-01-16 13:15:49'),
(125, 'Inspiron 3511', 'The Dell Inspiron 15 3511 (2021) is a 15.6-inch Full HD display Windows laptop. It has an Intel Core i5-1165G67 processor, a GeForce MX350 GPU, 8GB of RAM, and 256GB of SSD storage. However, depending on the region, it may be available in a variety of memory configurations. The majority of the chassis is made of plastic, and the laptop has a matte covering to prevent fingerprints and smudges. It also features the majority of the required I/O ports, and wireless networking choices include WiFi 5 ', 71999.00, 'assets/uploads/products/inspiron-3511.png', 6, 1, '2025-12-11 13:38:34', '2026-01-16 13:07:21'),
(126, 'Intel i5 13600K', 'Total Cores: 14, No. of Performance-cores: 6, No. of Efficient-cores: 8, Total Threads: 20', 54499.00, 'assets/uploads/products/intel-i5-13600k.jpg', 9, 5, '2025-10-17 09:54:23', '2026-01-16 13:15:49'),
(127, 'Intel i7 13700K', 'Total Cores: 16, No. of Performance-cores: 8, No. of Efficient-cores: 8, Total Threads: 24', 99999.00, 'assets/uploads/products/intel-i7-13700k.jpg', 10, 5, '2025-08-14 13:57:08', '2026-01-16 13:15:49'),
(128, 'Intel i9 13900K', 'Total Cores: 24, No. of Performance-cores: 8, No. of Efficient-cores: 16, Total Threads: 32', 44799.00, 'assets/uploads/products/intel-i9-13900k.jpg', 10, 5, '2025-12-31 13:10:15', '2026-01-10 15:17:33'),
(129, 'KRAKEN X63 RGB', 'The NZXT Kraken X63 RGB is a 280mm all-in-one liquid CPU cooler with dual 140mm RGB fans, a quiet and efficient pump, and customizable RGB lighting featuring a 360° rotatable infinity mirror pump cap. It supports many Intel and AMD sockets, offers strong cooling performance for high-end CPUs, and is controlled via NZXT’s CAM software. It balances excellent cooling, low noise, and stylish RGB aesthetics.', 24499.00, 'assets/uploads/products/kraken-x63-rgb.png', 7, 12, '2025-12-09 17:35:45', '2026-01-16 13:15:49'),
(130, 'Latitude 14\" 5420', 'The Dell Latitude 5420 14\" A 3 GHz 11th Gen Intel Core i7 4-core vPro processor and 8GB of RAM enables you to speed through your task, while integrated Intel Iris Xe Graphics and dual Thunderbolt 4 connections allow you to connect up to two 4K displays or one 8K display.', 91999.00, 'assets/uploads/products/latitude-14-5420.png', 7, 1, '2025-11-27 15:06:47', '2026-01-16 13:15:49'),
(131, 'Legion 5', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 169499.00, 'assets/uploads/products/legion-5.png', 8, 1, '2025-10-23 02:28:02', '2026-01-16 13:15:49'),
(132, 'Legion 5', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 157999.00, 'assets/uploads/products/legion-5.png', 14, 1, '2025-11-21 14:19:48', '2026-01-16 13:15:49'),
(133, 'Legion 5 Pro', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 155449.00, 'assets/uploads/products/legion-5-pro.png', 6, 1, '2025-10-08 10:26:49', '2026-01-16 13:15:49'),
(134, 'Legion 5 Pro AMD Ryzen 7 5800H 16GB RAM 512GB SSD NVIDIA RTX 3050 4GB', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 169999.00, 'assets/uploads/products/legion-5-pro-amd-ryzen-7-5800h-16gb-ram-512gb-ssd-.png', 10, 1, '2025-11-10 23:08:55', '2026-01-16 13:15:49'),
(135, 'Legion 7', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 125499.00, 'assets/uploads/products/legion-7.jpg', 8, 1, '2025-09-27 09:22:39', '2026-01-16 13:15:49'),
(136, 'Lenovo IdeaPad 3 Chromebook', 'Vendor: Intel, Model: Celeron® N4020, Cores: 2, Threads: 2', 33999.00, 'assets/uploads/products/lenovo-ideapad-3-chromebook.png', 9, 1, '2025-08-10 05:09:17', '2026-01-16 13:15:49'),
(137, 'Lenovo IdeaPad 3 i5 1235U 8GB RAM 256GB SSD Windows 11', 'Size: 14\", Resolution: FHD(1920 x 1080), Type: IPS Panel, Anti-Glare, Brightness: 300 nits', 79999.00, 'assets/uploads/products/lenovo-ideapad-3-i5-1235u-8gb-ram-256gb-ssd-window.jpg', 12, 1, '2025-09-25 00:31:31', '2026-01-16 13:15:49'),
(138, 'Lenovo Legion 5 Pro Ryzen 9 6900HX, 16GB RAM, 1TB SSD,  RTX 3070Ti, 16inch QHD 165Hz', 'Vendor: AMD, Model: Ryzen™ 9 6900HX, Cores: 0, Threads: 0', 282999.00, 'assets/uploads/products/lenovo-legion-5-pro-ryzen-9-6900hx-16gb-ram-1tb-ss.jpg', 13, 1, '2025-11-03 18:34:29', '2026-01-16 13:15:49'),
(139, 'Lenovo Legion 5  Ryzen 5 5600H 8GB RAM 512GB SSD NVIDIA GeForce RTX 3050Ti 4GB', 'Operating System: Windows 11 Home, Screen: 1920x1080 pixels Full HD IPS Display, 15.6\" with Anti-glare coating, No TouchScreen, Refresh Rate: 120Hz, Input: Keyboard with dedicated number pad & Touchpad with click buttons, Wireless & Networking: Wi-Fi 6 802.11AX wireless, Ethernet LAN port, Bluetooth', 122499.00, 'assets/uploads/products/lenovo-legion-5-ryzen-5-5600h-8gb-ram-512gb-ssd-nv.jpg', 14, 1, '2025-11-03 10:49:49', '2026-01-16 13:15:49'),
(140, 'Lenovo Legion 5i i7 12700H 16GB DDR5 1TB NVME SSD RTX 3060 6GB Windows 10 Home', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 207999.00, 'assets/uploads/products/lenovo-legion-5i-i7-12700h-16gb-ddr5-1tb-nvme-ssd-.png', 5, 1, '2025-09-21 20:49:30', '2026-01-16 13:15:49'),
(141, 'Lenovo Legion 5 AMD Ryzen 7 6800H 16GB RAM 512GB SSD Nvidia RTX 3060 6GB', 'Graphics: NVIDIA® GeForce RTX™ 3060 Laptop GPU, 6 GB GDDR6, Boost Clock 1702 MHz, Maximum Graphics Power 130W, Audio: 2x2W speakers with Nahimic Audio for Gamers, webcam: HD 720p with E-Shutter, Connectivity: WIFI 6 802.11AX (2 x 2) and Bluetooth® 5.1', 204999.00, 'assets/uploads/products/lenovo-legion-5-amd-ryzen-7-6800h-16gb-ram-512gb-s.png', 7, 1, '2025-08-21 22:41:43', '2026-01-16 13:15:49'),
(142, 'Lenovo Thinkbook 13x Gen 4 Intel', 'The Lenovo ThinkBook 13x Gen 4 (13\" Intel) laptop delivers business-class performance with Intel® Core™ Ultra processors and the Lenovo LA3 AI chip. Intel® Core™ Ultra laptops provide great productivity and immersive AI experiences without latency or battery waste.', 115499.00, 'assets/uploads/products/lenovo-thinkbook-13x-gen-4-intel.webp', 11, 1, '2025-08-11 19:26:44', '2026-01-16 13:07:05'),
(143, 'Thinkpad T14 Gen 3', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 217499.00, 'assets/uploads/products/thinkpad-t14-gen-3.png', 6, 1, '2025-10-18 03:43:09', '2026-01-16 13:15:49'),
(144, 'LENOVO V14 Gen 4', 'Processor: 1x AMD Ryzen™ 5 7520U Processor(Ryzen™ 5 7520U), Memory: 1x 8GBLPDDR5-5500, Operating System: Windows 11 Pro 64(EN:English), Hard Drive: 1x 256 GB SSD PCIe, Wireless Network: 1x Wireless 802.11 2x2 AC; Bluetooth® 5.1 or above', 188499.00, 'assets/uploads/products/lenovo-v14-gen-4.webp', 11, 1, '2025-10-28 09:44:33', '2026-01-16 13:15:49'),
(145, 'Ideapad Pro 5 16IMH9 Ultra 9 32GB 1TB', 'Lenovo ideapad 5 Pro laptop is more powerful performance than its previous model laptop with intel brand New ultra 9 185H processor and it give more fast and efficient performance with 32GB DDR5 Memory .The 16 inch OLED Display gives you more vivid color with 120Hz refresh rate . \r\n\r\nThis laptop take your work to next level with AI Features and RTX 4050 Graphics help you for more powerful graphics performance in 3D Hybrid architecture.', 104999.00, 'assets/uploads/products/ideapad-pro-5-16imh9-ultra-9-32gb-1tb.jpg', 6, 1, '2025-09-12 07:04:30', '2026-01-16 13:15:49'),
(146, 'LG UltraGear Curved Gaming Monitor', 'Size: 34 inch, Display Type: IPS, Display Resolution: UW-FHD, Color Gamut: (Typ.), sRGB: 99% (CIE1931)', 97999.00, 'assets/uploads/products/lg-ultragear-curved-gaming-monitor.png', 15, 4, '2025-10-22 22:32:55', '2026-01-16 13:15:49'),
(147, 'Lightning Digital A/V Adapter', 'he Lightning Digital AV Adapter lets you mirror your iPhone or iPad screen to an HDMI TV or projector in up to 1080p HD. It supports video and audio output, connects via Lightning, and requires a separate HDMI cable. Ideal for presentations and media sharing.', 6499.00, 'assets/uploads/products/lightning-digital-av-adapter.png', 12, 16, '2025-11-14 04:16:52', '2026-01-16 13:15:49'),
(148, 'Lightning to USB Camera Adapter', 'The Lightning to USB Camera Adapter lets you transfer photos and videos from a camera to your iPhone or iPad, supports USB peripherals with power, and works with iOS 9.2 or later.', 4199.00, 'assets/uploads/products/lightning-to-usb-camera-adapter.jpg', 8, 16, '2025-09-24 18:18:11', '2026-01-16 13:15:49'),
(149, 'Logitech Group', 'Camera: Full HD 1080p @ 30 fps with autofocus and 10x ZOOM, Speakerphone: Full-duplex with acoustic echo cancellation, Microphone: Single omni-directional microphone supporting 20-foot diameter range, Speakers: – Frequency response => 120Hz – 14KHz, HUB/Cable: Central mountable hub for connection of all components', 168999.00, 'assets/uploads/products/logitech-group.webp', 15, 15, '2025-11-03 22:16:37', '2026-01-16 13:15:49'),
(150, 'Logitech K375s Wireless Keyboard', 'The Logitech K375s is a versatile wireless keyboard that connects to up to three devices via Bluetooth or a USB receiver. It features a full-size layout, Easy-Switch keys for device switching, and a stand for phones or tablets. With compatibility across multiple operating systems, it offers up to 18 months of battery life and an adjustable typing angle, making it ideal for multi-device use.', 4449.00, 'assets/uploads/products/logitech-k375s-wireless-keyboard.png', 8, 13, '2025-11-23 15:32:43', '2026-01-16 13:15:49'),
(151, 'Macbook Pro 14', 'Battery Type: Lithium-polymer, Power Supply Input: USB Type C, Battery Life (up to): 17 hours, Power Supply Maximum Wattage: 67 watts', 292799.00, 'assets/uploads/products/macbook-pro-14.png', 6, 1, '2025-10-18 04:49:36', '2026-01-16 13:15:49'),
(152, 'MacBook Pro 16', 'Battery Type: Lithium-polymer, Power Supply Input: USB Type C, Battery Life (up to): 21 hours, Power Supply Maximum Wattage: 140 watts', 294999.00, 'assets/uploads/products/macbook-pro-16.png', 15, 1, '2025-08-16 21:37:25', '2026-01-16 13:15:49'),
(153, 'Microsoft Surface Pro 12 2025', 'The Surface Pro 12 2025 is a lightweight, AI-powered 12-inch 2-in-1 Windows device featuring a high-resolution touchscreen, a Snapdragon X Plus chip, long-lasting battery life, and modern connectivity—ideal for portable productivity and creativity.', 162499.00, 'assets/uploads/products/microsoft-surface-pro-12-2025.png', 8, 1, '2025-12-29 01:47:48', '2026-01-10 15:17:48'),
(154, 'MSI Pro X670-P WIFI DDR5', 'The PRO Series is tailored to professionals from all walks of life. The lineup features impressive performance and high quality, while aiming to provide users incredible experience. Users who care about productivity and efficiency can definitely count on the MSI PRO Series to assist you with multitasking and increasing efficiency.', 20499.00, 'assets/uploads/products/msi-pro-x670-p-wifi-ddr5.png', 7, 7, '2025-10-01 01:41:12', '2026-01-16 13:15:49'),
(155, 'MSI Pro Z790-P WIFI DDR5', 'The PRO Series is tailored to professionals from all walks of life. The lineup features impressive performance and high quality, while aiming to provide users incredible experience. Users who care about productivity and efficiency can definitely count on the MSI PRO Series to assist you with multitasking and increasing efficiency.', 19499.00, 'assets/uploads/products/msi-pro-z790-p-wifi-ddr5.png', 6, 7, '2025-12-19 17:21:39', '2026-01-16 13:15:49'),
(156, 'AER RGB 2 (120mm)', 'The NZXT Aer RGB 2 (120mm) is a quiet, durable RGB case fan with PWM control, 8 customizable LEDs, and optimized airflow, perfect for gaming and custom PC builds.', 4122.00, 'assets/uploads/products/aer-rgb-2-120mm.png', 8, 12, '2025-09-17 18:27:56', '2026-01-16 13:15:49'),
(157, 'NZXT C750 Gold ATX PSU', 'The NZXT C750 Gold is a fully modular 750W power supply with 80 Plus Gold efficiency, quiet fluid dynamic bearing fan, and high-quality sleeved cables. It supports modern GPUs, offers stable and efficient power delivery, and includes a 10-year warranty—ideal for gaming and high-performance PCs.', 19499.00, 'assets/uploads/products/nzxt-c750-gold-atx-psu.jpg', 11, 10, '2025-10-06 17:50:00', '2026-01-16 13:15:49'),
(158, 'Rog Strix G15', 'Built-In Microphone: Yes, Front-Facing Camera: No', 234999.00, 'assets/uploads/products/rog-strix-g15.png', 13, 1, '2025-12-27 23:24:09', '2026-01-10 15:17:59'),
(159, 'Rog Zephyrus G14', 'Built-In Microphone: Yes, Front-Facing Camera: No', 167999.00, 'assets/uploads/products/rog-zephyrus-g14.png', 5, 1, '2025-10-11 20:17:53', '2026-01-16 13:15:49'),
(160, 'Rog Zephyrus G14', 'Built-In Microphone: Yes, Front-Facing Camera: No', 157999.00, 'assets/uploads/products/rog-zephyrus-g14.png', 10, 1, '2025-08-15 05:10:18', '2026-01-16 13:15:49'),
(161, 'SAMSUNG Portable SSD T7 Shield USB 3.2', 'The Samsung T7 Shield is a fast, rugged portable SSD with up to 1,050 MB/s speeds, water and dust resistance, AES 256-bit encryption, and comes in 1TB, 2TB, and 4TB sizes.', 24499.00, 'assets/uploads/products/samsung-portable-ssd-t7-shield-usb-32.png', 7, 9, '2025-10-23 16:03:23', '2026-01-16 13:15:49'),
(162, 'Sandisk Extreme Portable SSD', 'Read: 1050 MB/s, Write: 1000 MB/s', 17499.00, 'assets/uploads/products/sandisk-extreme-portable-ssd.png', 12, 9, '2025-12-13 22:36:08', '2026-01-16 13:15:49'),
(163, 'Studio 3 wireless', 'The Beats Studio 3 Wireless are high-end over-ear Bluetooth headphones with advanced noise cancellation, Apple’s W1 chip for simple pairing and device switching, up to 22 hours of battery life with ANC on, quick charging, and a comfortable fit. They deliver strong sound quality and smooth connectivity, especially for Apple users.', 34999.00, 'assets/uploads/products/studio-3-wireless.png', 15, 14, '2025-10-02 00:08:47', '2026-01-16 13:15:49'),
(164, 'Swift 3', 'portable Ultrabook laptop with nice balance performance and also durable battery life .This laptop for better for student and normal user for there daily work.', 269999.00, 'assets/uploads/products/swift-3.png', 9, 1, '2025-08-27 06:11:13', '2026-01-16 13:15:49');
INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image_path`, `product_stock`, `category_id`, `created_at`, `updated_at`) VALUES
(165, 'ThinkPad E14 Gen 2', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 545499.00, 'assets/uploads/products/thinkpad-e14-gen-2.png', 6, 1, '2025-09-25 22:59:11', '2026-01-16 13:15:49'),
(166, 'ThinkPad E14 Gen 3', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 124999.00, 'assets/uploads/products/thinkpad-e14-gen-3.png', 10, 1, '2025-09-25 08:17:20', '2026-01-16 13:15:49'),
(167, 'ThinkPad T14 Gen 3', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 244999.00, 'assets/uploads/products/thinkpad-t14-gen-3.png', 8, 1, '2025-08-23 05:04:44', '2026-01-16 13:15:49'),
(168, 'ThinkPad T14 Gen 6 (AMD)', 'Lenovo ThinkPad T14 Gen 6 features an AMD Ryzen AI 7 PRO 350, 14-inch WUXGA touchscreen, 32GB RAM, and 1TB SSD. It offers Wi-Fi 7, Bluetooth 5.4, and advanced security features. Weighing about 1.5 kg, it’s durable and great for on-the-go productivity.', 104999.00, 'assets/uploads/products/thinkpad-t14-gen-6-amd.jpg', 9, 1, '2025-09-29 05:29:47', '2026-01-16 13:15:49'),
(169, 'ThinkPad T14s Gen 6', 'The Lenovo ThinkPad T14s Gen 6 has an Intel Core Ultra 7 processor, 16GB RAM, and a 512GB SSD. It features a 14-inch 1920x1200 display, Thunderbolt 4, and strong security options. Weighing 1.24 kg, it’s ideal for professionals seeking portability and efficiency.', 207999.00, 'assets/uploads/products/thinkpad-t14s-gen-6.jpg', 14, 1, '2025-11-09 23:06:47', '2026-01-16 13:15:49'),
(170, 'Travel-Cube Adapter Type-A  Type-C', 'Brand: unitek, Rated Power Total: 60W Max, Input AC: 100-240V AC (50-60Hz) 1.5A Max, Product Dimension: 77x 68.87 x 28.97 (mm)', 6799.00, 'assets/uploads/products/travel-cube-adapter-type-a-type-c.jpg', 11, 16, '2025-12-05 23:54:35', '2026-01-16 13:15:49'),
(171, 'TUF Gaming F15', 'Built-In Microphone: Yes, Front-Facing Camera: Yes', 319999.00, 'assets/uploads/products/tuf-gaming-f15.png', 9, 1, '2025-09-13 05:51:31', '2026-01-10 15:12:35'),
(172, 'UGreen Portable Wireless Mouse (BlueTooth)', 'Mode: Wireless -2.4 GHz + Bluetooth, Operating Distance: 10 m/32.8ft, DPI: 1000/1600/2000/4000 DPI, Operating Voltage: 1.5V', 1799.00, 'assets/uploads/products/ugreen-portable-wireless-mouse-bluetooth.webp', 8, 13, '2025-11-12 10:45:27', '2026-01-10 15:12:21'),
(173, 'UGreen USB-C Multifunction Docking Station (5-in-1)', 'SKU: 10919, Input: 1 x USB-C Male, Output: 2 x USB 3.0 Female, 1 x HDMI Female , 1 x RJ45, USB Standard: USB 3.0 is capable of providing a data transfer speed up to 5Gbps', 6149.00, 'assets/uploads/products/ugreen-usb-c-multifunction-docking-station-5-in-1.webp', 7, 16, '2025-08-08 07:56:44', '2026-01-10 15:11:48'),
(174, 'WD_BLACK SN750', 'The WD_BLACK SN750 is a fast PCIe Gen3 NVMe M.2 SSD for gamers and enthusiasts, offering 250GB–4TB capacities, up to 3,470 MB/s read and 3,100 MB/s write speeds, with an optional heatsink for better cooling, and a 5-year warranty.', 2499.00, 'assets/uploads/products/wd_black-sn750.png', 13, 9, '2025-09-17 19:42:04', '2026-01-16 13:15:49'),
(175, 'WD Elements External USB HDD', 'Capacity: 1TB-5TB, Interface: USB 3.0, USB 2.0', 16624.00, 'assets/uploads/products/wd-elements-external-usb-hdd.png', 8, 22, '2025-12-04 07:41:26', '2026-01-10 15:11:26');


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


/*new data */;



COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
