-- =============================================================================
-- ByteStore Complete Database
-- Single-file install: schema + migrations + demo seed data
-- Import: mysql -u root < database/bytestore_complete.sql
-- Generated: 2026-05-30 (consolidated final)
-- Includes: all tables, all seed data, products, reviews, variants, specs
-- =============================================================================

DROP DATABASE IF EXISTS `bytestore`;
CREATE DATABASE `bytestore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bytestore`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: bytestore
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cart_id`),
  KEY `customer_id` (`customer_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_variant_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`variant_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (10,4,4,NULL,1,'2026-01-03 09:35:52'),(11,4,5,NULL,2,'2026-01-03 09:35:58'),(12,1,6,NULL,1,'2026-01-03 09:36:16'),(13,1,15,NULL,1,'2026-01-03 09:36:20'),(14,1,10,NULL,1,'2026-01-03 09:36:25'),(16,5,7,NULL,1,'2026-01-03 09:36:50'),(17,5,16,NULL,1,'2026-01-03 09:36:54'),(19,3,16,NULL,2,'2026-01-03 09:37:17'),(20,3,2,NULL,1,'2026-01-03 09:37:20'),(21,3,21,NULL,1,'2026-01-03 09:37:28'),(22,2,25,NULL,1,'2026-01-03 09:37:46'),(23,2,9,NULL,1,'2026-01-03 09:37:50'),(24,2,10,NULL,1,'2026-01-03 09:37:53'),(26,6,6,NULL,1,'2026-01-03 09:38:11'),(27,6,11,NULL,1,'2026-01-03 09:38:13'),(28,4,6,6,2,'2026-05-30 07:35:03'),(29,4,71,NULL,1,'2026-05-30 07:35:16');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (16,'Accessories'),(14,'Audio (Headphones/Speakers)'),(24,'Cameras'),(12,'Cooling'),(2,'Desktops'),(3,'Gaming'),(6,'Graphics Cards'),(13,'Keyboards & Mice'),(1,'Laptops'),(8,'Memory (RAM)'),(4,'Monitors'),(7,'Motherboards'),(20,'Networking (Routers/WiFi)'),(11,'PC Cases'),(17,'Phones'),(10,'Power Supplies'),(21,'Printers & Scanners'),(5,'Processors'),(22,'Servers'),(23,'Software & Services'),(9,'Storage (SSD/HDD)'),(18,'Tablets'),(19,'Wearables (Watches)'),(15,'Webcams');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `customer_address` text NOT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'Akraj Customer','$2b$10$TgcUeKmJNdk0GP9VQlOdj.51LZ/hNrU6CNr9E/9K.AV7NN5pRtU/m','Akraj2024@bytestore.com','9803563451','Bhaktapur,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(2,'Arpan Shopper','$2b$10$ieEiQF.FSAH1xWMo9Y0fm.FaVUY/LpvE2RwUAwFWDT2moV06RpEBq','Arpan1@bytestore.com','9803563452','Kalanki,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(3,'Pramisha Shopper','$2b$10$YKenocpP4Z5kwC9JJmrrEOBlkrldKzrAhQke.dAIAgeoU1dyYKCFy','Pramisha1@bytestore.com','9803563453','Butwal,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(4,'Sujit Shopper','$2b$10$YX6TQM6Prh3U/L/6folrKuM4S/mblJdjHjWxtXaNRvfV1TOb4k6Ya','Sujit1@bytestore.com','9803563454','Lalitpur,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(5,'Sajal Shopper','$2b$10$qVxNt6Rq2zpCC4d0QKOyyejr01Re6BtZdIk4oFJTZH70/Gcgd5DIi','Sajal1@bytestore.com','9803563455','Pokhara,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(6,'Rohan Shopper','$2b$10$pPan0PNA76bkq/I7xwL.dOad1AiEJEtGP5wQtGGGG4RN.JkwuXY/2','Rohan1@bytestore.com','9803563456','Dharan,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32'),(7,'Customer','$2b$10$JHDyg9qi4DB.7Fj8rX9/HeiVJsR9qpKX9H69rJ5oEtbY36a3SYody','Customer1@bytestore.com','9803563457','Example,Nepal',NULL,'2026-05-30 07:08:07','2026-05-30 07:08:32');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_address`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `customer_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `label` varchar(50) DEFAULT 'Home',
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address_line` text NOT NULL,
  `city` varchar(100) NOT NULL DEFAULT 'Kathmandu',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`address_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `address_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_address`
--

LOCK TABLES `customer_address` WRITE;
/*!40000 ALTER TABLE `customer_address` DISABLE KEYS */;
INSERT INTO `customer_address` VALUES (1,1,'Home','Akraj Customer','9803563451','Bhaktapur, Nepal','Bhaktapur',1,'2026-05-30 07:08:33'),(2,2,'Home','Arpan Shopper','9803563452','Kalanki, Kathmandu','Kathmandu',1,'2026-05-30 07:08:33');
/*!40000 ALTER TABLE `customer_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_notification`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `customer_notification` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'general',
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `notif_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_notification`
--

LOCK TABLES `customer_notification` WRITE;
/*!40000 ALTER TABLE `customer_notification` DISABLE KEYS */;
INSERT INTO `customer_notification` VALUES (1,1,18,'wishlist_stock','Asus ROG Strix Gaming Laptop is back in stock!',0,'2026-05-30 07:09:00'),(2,2,6,'wishlist_stock','Apple MacBook Air M2 is back in stock!',0,'2026-05-30 07:09:00'),(3,5,18,'wishlist_stock','Asus ROG Strix Gaming Laptop is back in stock!',1,'2026-05-30 07:10:36'),(4,4,6,'wishlist_stock','Apple MacBook Air M2 is back in stock!',1,'2026-05-30 07:31:41');
/*!40000 ALTER TABLE `customer_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(255) NOT NULL,
  `employee_password` varchar(255) NOT NULL,
  `employee_email` varchar(255) NOT NULL,
  `employee_phone` varchar(30) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `employee_email` (`employee_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'Super Admin','$2b$10$uxHFUBcX27nPtq2W1ESDleTqWDuJGirNsXxp3iUzXGjY/8eY6KTKG','Superadmin1@bytestore.com','9801000001','Main Tech Store','2026-05-30 07:08:07'),(2,'Emp1','$2b$10$jwaL4ZkaW1rUT/tC2PpJnu2UOUWmo69EDgqU5z/p.YoSs2DrSsK16','Employee1@bytestore.com','9801000002','Main Tech Store','2026-05-30 07:08:07'),(3,'Pratima Employee','$2b$10$N.dvacRMN362mPUih.kynOyxLMH.nNQTm3Bz3p2Z5nXb5Au9FZx7i','Pratima1@bytestore.com','9801000003','Euro Store','2026-05-30 07:08:07'),(4,'Sunita Employee','$2b$10$BjQlVmQ8yxOJSJgJjjZHxuBwvl.2E.iNthvrK66QJwZKD.P07a5Ea','Sunita1@bytestore.com','9801000004','Selective Store','2026-05-30 07:08:07'),(5,'Arjun Employee','$2b$10$dxvsSmogkYz1ZYy1RoomF.R4yVrk.C0mfvZVdwR1hPSvyN3r.qjmC','Arjun1@bytestore.com','9801000005','Dami Store','2026-05-30 07:08:07');
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `newsletter` (
  `subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter`
--

LOCK TABLES `newsletter` WRITE;
/*!40000 ALTER TABLE `newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_variant_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`variant_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,2,NULL,1,190000.00),(2,1,1,NULL,1,230000.00),(3,2,1,NULL,1,230000.00),(4,2,4,NULL,2,2999.00),(5,3,5,NULL,1,4990.00),(6,3,11,NULL,1,42000.00),(7,4,12,NULL,1,120000.00),(8,4,2,NULL,1,190000.00),(9,5,3,NULL,1,180000.00),(10,6,6,NULL,1,175000.00),(11,6,7,NULL,1,155000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `esewa_transaction_code` varchar(100) DEFAULT NULL COMMENT 'eSewa transaction code returned on successful payment',
  `shipping_address` text NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,4,'2025-06-07 10:35:49',420000.00,'Shipped','Payed',NULL,'Lalitpur,Nepal','9803563454'),(2,1,'2026-01-08 03:46:14',235998.00,'Delivered','Pending',NULL,'Bhaktapur,Nepal','9803563451'),(3,5,'2025-09-04 08:36:46',46990.00,'Shipped','Payed',NULL,'Pokhara,Nepal','9803563455'),(4,3,'2025-07-09 05:27:14',310000.00,'Cancelled','Pending',NULL,'Butwal,Nepal','9803563453'),(5,2,'2026-01-03 02:17:43',180000.00,'Pending','Pending',NULL,'Kalanki,Nepal','9803563452'),(6,6,'2025-08-03 04:38:08',330000.00,'Processing','Pending',NULL,'Dharan,Nepal','9803563456'),(7,1,'2025-12-30 07:09:00',230000.00,'Delivered','Paid',NULL,'Bhaktapur,Nepal','9803563451'),(8,2,'2026-01-30 07:09:00',175000.00,'Delivered','Paid',NULL,'Kalanki,Nepal','9803563452'),(9,3,'2026-02-28 07:09:00',45000.00,'Shipped','Paid',NULL,'Butwal,Nepal','9803563453'),(10,4,'2026-03-30 07:09:00',58000.00,'Delivered','Paid',NULL,'Lalitpur,Nepal','9803563454'),(11,5,'2026-04-30 07:09:01',18000.00,'Processing','Pending',NULL,'Pokhara,Nepal','9803563455');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `sale_start_date` datetime DEFAULT NULL,
  `sale_end_date` datetime DEFAULT NULL,
  `is_sale_active` tinyint(1) NOT NULL DEFAULT 0,
  `product_image_path` varchar(500) NOT NULL,
  `product_gif_path` varchar(500) DEFAULT NULL,
  `product_stock` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Laptop Dell XPS 15','Dell XPS 15  Premium 15-inch laptop with a near-borderless InfinityEdge display, powerful CPU options, and configurable RAM and storage. Designed for professionals and content creators.',230000.00,276000.00,20.00,'2026-05-27 13:32:23','2026-06-13 13:32:23',1,'assets/uploads/products/laptop1.jpg',NULL,10,0,1,5.00,1,1,'Dell','2025-11-21 14:00:25','2026-05-30 07:47:23'),(2,'iPhone 15 Pro','Apple iPhone 15 Pro  Advanced Pro camera system, A17-series chipset, ProMotion display, and premium titanium frame. Available in multiple storage configurations and colors. Excellent performance for photography, gaming and AR experiences.',190000.00,228000.00,20.00,'2026-05-27 13:32:23','2026-06-13 13:32:23',1,'assets/uploads/products/iphone.jpg',NULL,15,1,0,5.00,1,17,'Apple','2025-10-11 11:27:53','2026-05-30 07:47:23'),(3,'Samsung Galaxy S24','Samsung Galaxy S24  Flagship Android smartphone with a high-refresh AMOLED display, powerful Exynos/Snapdragon chipset, and pro-grade camera system. Available in multiple storage options.',180000.00,207000.00,15.00,'2026-05-29 13:32:23','2026-06-06 13:32:23',1,'assets/uploads/products/samsung.jpg',NULL,12,1,0,0.00,0,17,'Samsung','2025-09-23 01:48:50','2026-05-30 07:47:23'),(4,'Wireless Mouse','Ergonomic wireless mouse with long battery life Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',2999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/mouse.jpg',NULL,50,0,1,5.00,1,13,NULL,'2025-10-30 13:13:37','2026-05-30 07:09:00'),(5,'Mechanical Keyboard','RGB backlit mechanical keyboard with blue switches Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',4990.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/keyboard.jpg',NULL,30,0,1,0.00,0,13,NULL,'2025-12-25 23:34:55','2026-05-30 07:08:33'),(6,'Apple MacBook Air M2','Apple MacBook Air M2  Thin and light 13-inch laptop with Apple M2 chip, sharp Retina display, fanless design, and long battery life. Available in configurations with up to 16GB RAM and larger SSDs.',175000.00,210000.00,20.00,'2026-05-27 13:32:23','2026-06-13 13:32:23',1,'assets/uploads/products/macbook_air.jpg',NULL,8,1,0,5.00,1,1,'Apple','2025-08-31 18:45:26','2026-05-30 07:47:23'),(7,'HP Gaming Laptop','Ryzen 5, 16GB RAM, RTX 3050, 512GB SSD Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',155000.00,186000.00,20.00,'2026-05-27 13:32:23','2026-06-13 13:32:23',1,'assets/uploads/products/hp_pavilion.jpg',NULL,10,0,1,4.00,1,1,'HP','2025-09-02 04:44:11','2026-05-30 07:47:23'),(8,'Lenovo ThinkPad E14','Business laptop with 16GB RAM, 512GB SSD, i5 12th Gen Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',140000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad.jpg',NULL,12,0,0,0.00,0,1,'Lenovo','2025-08-18 00:00:07','2026-05-30 07:08:33'),(9,'Sony WH Headphones','Sony WH-series Headphones  Over-ear wireless headphones with noise cancellation and premium audio tuning for immersive listening.',58000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/sony_headphone.jpg',NULL,20,1,0,5.00,1,14,'Sony','2025-09-24 14:29:37','2026-05-30 07:08:33'),(10,'JBL Charge 5 Speaker','Portable Bluetooth speaker with deep bass Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',24000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/jbl.jpg',NULL,25,0,0,0.00,0,14,'JBL','2025-11-23 01:35:45','2026-05-30 07:08:33'),(11,'Apple AirPods Pro 2','Apple AirPods Pro 2  Active Noise Cancellation, spatial audio, and improved battery life with the wireless charging case. Comfortable in-ear fit for daily use.',42000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/airpods.jpg',NULL,30,1,0,0.00,0,14,'Apple','2025-09-04 07:26:14','2026-05-30 07:08:33'),(12,'Samsung Galaxy Tab S9','11-inch AMOLED Display, 8GB RAM, 256GB Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',120000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/galaxy_tab.jpg',NULL,10,0,0,0.00,0,18,'Samsung','2025-08-29 16:21:44','2026-05-30 07:08:33'),(13,'iPad 10th Gen','iPad 10th Gen  10.9-inch tablet with A14-class performance, great for media consumption, note-taking and light creative work. Available with Wi-Fi and cellular options and multiple storage sizes.',95000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ipad10.jpg',NULL,12,0,0,0.00,0,18,'Apple','2025-09-05 07:16:28','2026-05-30 07:08:33'),(14,'Apple Watch Series 9','Fitness tracking smartwatch with GPS Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',78000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple_watch.jpg',NULL,18,0,0,0.00,0,19,'Apple','2025-09-20 06:51:27','2026-05-30 07:08:33'),(15,'Samsung Watch 6','Premium fitness smartwatch Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',55000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/galaxy_watch.jpg',NULL,22,0,0,0.00,0,19,'Samsung','2025-11-13 08:02:12','2026-05-30 07:08:33'),(16,'Logitech G502 Mouse','Gaming mouse with precision sensor and RGB Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',9500.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/logitech_g502.jpg',NULL,35,0,1,0.00,0,13,'Logitech','2025-09-08 07:01:21','2026-05-30 07:08:33'),(17,'Razer BlackWidow V3','Mechanical gaming keyboard with RGB lighting Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',21000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/razer_keyboard.jpg',NULL,28,0,0,0.00,0,13,'Razer','2025-09-18 21:57:09','2026-05-30 07:08:33'),(18,'Asus ROG Strix Gaming Laptop','ASUS ROG Strix  High-performance gaming laptop with the latest discrete GPU options, high-refresh display, aggressive cooling, and gamer-focused features.',245000.00,294000.00,20.00,'2026-05-27 13:32:23','2026-06-13 13:32:23',1,'assets/uploads/products/asus_rog.jpg',NULL,6,1,1,4.00,1,1,'ASUS','2025-08-02 23:12:09','2026-05-30 07:47:23'),(19,'Dell Monitor 27 Inch','Full HD IPS LED Monitor 144Hz Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',45000.00,51750.00,15.00,'2026-05-29 13:32:23','2026-06-06 13:32:23',1,'assets/uploads/products/dell_monitor.jpg',NULL,15,0,0,4.00,1,4,'Dell','2025-10-17 01:08:34','2026-05-30 07:47:23'),(20,'Canon EOS 250D DSLR','24.1MP DSLR camera with lens kit Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',95000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon_dslr.jpg',NULL,7,0,0,0.00,0,24,'Canon','2025-12-20 02:07:23','2026-05-30 07:08:33'),(21,'GoPro Hero 12','Action camera 5K video, waterproof Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',78000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/gopro12.jpg',NULL,14,0,0,0.00,0,24,NULL,'2025-10-22 21:06:59','2026-05-30 07:08:33'),(22,'Portable SSD 1TB','Portable SSD (Various Capacities)  Fast external NVMe/USB-C storage with high transfer speeds and compact form factor. Ideal for backup, media and content workflows.',18000.00,20700.00,15.00,'2026-05-29 13:32:23','2026-06-06 13:32:23',1,'assets/uploads/products/ssd1tb.jpg',NULL,40,0,1,4.00,1,9,NULL,'2025-08-12 05:10:20','2026-05-30 07:47:23'),(23,'Sandisk 128GB Pendrive','USB 3.2 high speed pendrive with micro usb    Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',2200.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/pendrive.jpg',NULL,60,0,0,0.00,0,9,NULL,'2025-09-09 17:25:55','2026-05-30 07:08:33'),(24,'Gaming Chair RGB Edition','Ergonomic gaming chair with RGB lighting Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',38000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/gaming_chair.jpg',NULL,9,0,0,0.00,0,3,NULL,'2025-09-27 02:58:30','2026-05-30 07:08:33'),(25,'TP-Link WiFi 6 Router','High-speed dual band gigabit WiFi router Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',18000.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/wifi_router.jpg',NULL,20,0,1,5.00,1,20,'TP-Link','2025-11-17 17:56:32','2026-05-30 07:09:00'),(26,'Acer HD LED Backlit Computer Monitor','Brand: Acer, Item Height: 36.2 Centimeters, Item Width: 46.3 Centimeters, Standing screen display size: 19.5 Inches, Screen Resolution: 1366 x 768 Pixels',182999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-hd-led-backlit-computer-monitor.png',NULL,14,0,0,0.00,0,4,'Acer','2025-11-13 23:15:23','2026-05-30 07:08:33'),(27,'Acer Aspire 5','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',64999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-aspire-5.png',NULL,5,0,0,0.00,0,1,'Acer','2025-10-01 10:26:56','2026-05-30 07:08:33'),(28,'Acer Aspire A515-57G Intel i5 1235U 8GB Memory 256GB Storage NVIDIA MX 550 2GB WINDOWS 10','Resolution: 1920 x 1080, Size: 15.6 inches, Type: IPS LCD, Refresh rate: 60 Hz Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',89999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-aspire-a515-57g-intel-i5-1235u-8gb-memory-256.jpg',NULL,7,0,0,0.00,0,1,'Acer','2025-11-13 09:27:04','2026-05-30 07:08:33'),(30,'Acer Nitro VG2 27\" Gaming Monitor','when Standy: 400 mW, when Off: 310mW, when Max: 60W, when On: 26W Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',51499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-nitro-vg2-27-gaming-monitor.png',NULL,13,0,0,0.00,0,4,'Acer','2025-08-10 15:28:26','2026-05-30 07:08:33'),(33,'Acer Predator Helios 300 i7 12700H 16GB DDR5 512GB NVME RTX 3060 6GB Windows 10 Home','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',182999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-predator-helios-300-i7-12700h-16gb-ddr5-512gb.png',NULL,11,0,0,0.00,0,1,'Acer','2025-11-07 04:27:45','2026-05-30 07:08:33'),(34,'Acer Swift 3','The Acer Swift 3 is a small and lightweight device with an 11th Gen Intel Core i5-1135G7 CPU that can handle a wide range of jobs and applications with ease. The laptop has a 14-inch IPS display with 100% sRGB coverage, so your visuals will be clear and colorful. Furthermore, the laptop comes with 8GB of DDR4 RAM and 512GB of SSD storage. At 1.2 kilograms, the laptop is also extremely light. A backlit keyboard, a fingerprint reader, a webcam, and a multitude of connectivity connectors are also i',65999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/acer-swift-3.png',NULL,14,0,0,0.00,0,1,'Acer','2025-08-06 02:53:10','2026-05-30 07:08:33'),(35,'Addlink AddGame A X70 SSD M.2 PCIe Gen3x4','The Addlink AddGame A X70 SSD is a PCIe Gen3 x4 NVMe M.2 SSD with speeds up to 3500/3000 MB/s, capacities from 256GB to 2TB, and features RGB cooling, LDPC ECC, and SSD Toolbox software.',6999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/addlink-addgame-a-x70-ssd-m2-pcie-gen3x4.png',NULL,10,0,0,0.00,0,9,NULL,'2025-08-06 21:49:24','2025-09-29 11:34:31'),(36,'USB-C Hub Multiport Adapter','Premium 7-in-1 USB-C hub with HDMI 4K output, USB 3.0 ports, SD/microSD card reader, and 100W Power Delivery pass-through. Compact aluminum design compatible with MacBook, Surface, and USB-C laptops. Essential accessory for modern workstations.',4499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/explore-all-products.png',NULL,8,0,0,0.00,0,16,NULL,'2025-09-15 22:07:55','2026-05-30 07:08:33'),(37,'iPad (9th Gen)','Material: Aluminum Back and Frame with Glass Front, Weight: 487 g, Dimensions (inches): 9.87 x 6.85 x 0.30',219999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ipad-9th-gen.png',NULL,14,0,0,0.00,0,18,'Apple','2025-12-25 12:02:47','2026-05-30 07:08:33'),(38,'iPad Air (4th Gen)','Material: Aluminum Back and Frame with Glass Front, Weight: 460 g, Dimensions (inches): 9.74 x 7.02 x 0.24',104100.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ipad-air-4th-gen.png',NULL,9,0,0,0.00,0,18,'Apple','2025-10-22 14:54:43','2026-05-30 07:08:33'),(39,'iPad Air 5th Gen (M1 Series)','Material: Aluminum Back and Frame with Glass Front, Weight: 462 g, Dimensions (inches): 9.74 x 7.02 x 0.24',432499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ipad-air-5th-gen-m1-series.png',NULL,6,0,0,0.00,0,18,'Apple','2025-08-30 11:06:15','2026-05-30 07:08:33'),(40,'Apple Mac Mini M2 16GB Memory 256GB SSD','Simultaneous Display: Two, Video Playback: Supported formats include HEVC, H.264, and ProRes HDR with Dolby Vision, HDR10, and HLG',222999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-mac-mini-m2-16gb-memory-256gb-ssd.png',NULL,6,0,0,0.00,0,2,'Apple','2025-09-18 05:45:04','2026-05-30 07:08:33'),(41,'Apple MacBook M2 Air  13\" 16GB Memory 256GB SSD','Size: 13.6\" (diagonal), Technology: LED-backlit display with IPS technology, Resolution: 2560-by-1664 native resolution @ 224 pixels per inch, Brightness: 500 nits',223499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-m2-air-13-16gb-memory-256gb-ssd.jpg',NULL,13,1,0,0.00,0,1,'Apple','2025-11-11 20:41:54','2026-05-30 07:08:33'),(42,'Apple MacBook Air 13 inch M2 Chip 16GB Memory 512GB SSD','Size: 13.6\" (diagonal), Technology: LED-backlit display with IPS technology, Resolution: 2560-by-1664 native resolution @ 224 pixels per inch, Brightness: 500 nits',264999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-air-13-inch-m2-chip-16gb-memory-512g.jpg',NULL,9,0,0,0.00,0,1,'Apple','2025-09-23 09:23:46','2026-05-30 07:08:33'),(43,'Apple MacBook Pro 16\" M2 Max Chip 32GB Memory 1TB SSD','Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',525499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-16-m2-max-chip-32gb-memory-1tb-s.jpg',NULL,6,0,0,0.00,0,1,'Apple','2025-11-08 23:13:18','2026-05-30 07:08:33'),(44,'Apple MacBook Pro 14 inch M2 Pro Chip 16GB Memory 512GB SSD','Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',304999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-14-inch-m2-pro-chip-16gb-memory-.jpg',NULL,14,0,0,0.00,0,1,'Apple','2025-08-26 01:22:36','2026-05-30 07:08:33'),(45,'Apple MacBook Pro 14  inch M2 Pro Chip 32GB Memory 1TB SSD','Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',509999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-14-inch-m2-pro-chip-32gb-memory-.jpg',NULL,8,0,0,0.00,0,1,'Apple','2025-08-23 05:02:35','2026-05-30 07:08:33'),(46,'Apple MacBook Pro 16 inch M2 Pro Chip 16GB Memory 1TB SSD','Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',405499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-16-inch-m2-pro-chip-16gb-memory-.jpg',NULL,10,0,0,0.00,0,1,'Apple','2025-10-15 07:28:30','2026-05-30 07:08:33'),(47,'Apple MacBook Pro 16 inch  M2 Pro chip 16GB Memory 512GB SSD','Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',379999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-16-inch-m2-pro-chip-16gb-memory-.jpg',NULL,7,0,0,0.00,0,1,'Apple','2025-10-30 18:33:29','2026-05-30 07:08:33'),(48,'MacBook Pro 13\" M1','Material: Aluminium, Weight: 1.4 kg, Dimensions (inches): 11.97 x 8.36 x 0.61 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',451499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/macbook-pro-13-m1.png',NULL,7,0,0,0.00,0,1,'Apple','2025-08-10 22:53:40','2026-05-30 07:08:33'),(49,'Apple MacBook Pro 13\" M2 Chip','Technology: Retina display, Size: 13.3-inch (diagonal), Type: LED-backlit display with IPS technology, Resolution: 2560 x 1600 native resolution @ 227 pixels per inch',244999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-13-m2-chip.png',NULL,9,0,0,0.00,0,1,'Apple','2025-10-25 04:43:06','2026-05-30 07:08:33'),(50,'MacBook  Pro 14 inch M2 Pro Chip16GB Memory 1TB SSD','Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',379999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/macbook-pro-14-inch-m2-pro-chip16gb-memory-1tb-ssd.jpg',NULL,5,0,0,0.00,0,1,'Apple','2025-09-15 13:27:36','2026-05-30 07:08:33'),(51,'Apple MacBook Pro 14\" M2 Pro Chip 32GB Memory 512GB SSD','Size: 14.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',364999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-14-m2-pro-chip-32gb-memory-512gb.jpg',NULL,6,0,0,0.00,0,1,'Apple','2025-09-19 21:53:51','2026-05-30 07:08:33'),(52,'Apple MacBook Pro 16\" M2 Max Chip 64GB Memory 1TB SSD','Size: 16.2\" Diagonal, Type: Liquid Retina XDR Display, Resolution: 3024 x 1964 Native at 254 pixels per inch, Contrast Ratio: 1000000:1',619999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-macbook-pro-16-m2-max-chip-64gb-memory-1tb-s.jpg',NULL,10,0,0,0.00,0,1,'Apple','2025-09-24 14:32:48','2026-05-30 07:08:33'),(53,'Apple Studio Display','The Apple Studio Display is a 27-inch high-resolution screen with bright, true-to-life colors. It has a great built-in camera for video calls, powerful speakers for clear sound, and three microphones. It connects easily to your Mac with Thunderbolt and USB-C ports. The design is sleek and can reduce glare with a special glass option. ItΓÇÖs perfect for creative work and multimedia.',320499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-studio-display.png',NULL,14,0,0,0.00,0,4,'Apple','2025-12-20 12:05:00','2026-05-30 07:08:33'),(54,'Apple USB-C Charge Cable 2 m','The Apple USB-C Charge Cable (2 m) is a durable, woven cable that supports fast charging up to 240W and data transfer. ItΓÇÖs compatible with USB-C Apple devices and pairs with USB-C power adapters for efficient charging.',4499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/apple-usb-c-charge-cable-2-m.jpg',NULL,8,0,0,0.00,0,16,'Apple','2025-12-25 13:27:37','2026-05-30 07:08:33'),(55,'ASRock  B365M PRO4 Motherboard Intel','The ASRock B365M PRO4 is a MicroATX motherboard for 8th and 9th Gen Intel Core CPUs, supporting up to 64GB DDR4 RAM. It includes 2 PCIe x16 slots, 6 SATA ports, 2 M.2 slots, Intel Gigabit LAN, and 7 USB ports. Suitable for mid-range gaming and productivity.',12999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asrock-b365m-pro4-motherboard-intel.png',NULL,8,0,0,0.00,0,7,NULL,'2025-10-06 16:40:16','2025-10-14 08:34:53'),(56,'ASRock B860 Steel Legend WiFi','The ASRock B860 Steel Legend WiFi is a mid-range ATX motherboard for Intel Arrow Lake CPUs, supporting up to 256 GB DDR5, PCIe 5.0, Wi-Fi 6E, Bluetooth 5.3, 2.5 GbE LAN, and Thunderbolt 4.',12999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asrock-b860-steel-legend-wifi.png',NULL,6,0,0,0.00,0,7,NULL,'2025-10-05 00:11:59','2025-10-15 14:53:36'),(57,'Asus ProArt Display','Panel Size: 27 inch, Pixels Per Inch: 109 PPI, Aspect Ratio: 16:9, Display Viewing Area (H x V): 596.74 x 335.66 mm, Display Surface: Non-Glare',78499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-proart-display.jpg',NULL,13,0,0,0.00,0,4,'ASUS','2025-08-11 05:21:11','2026-05-30 07:08:33'),(58,'ROG Zephyrus G14','Built-In Microphone: Yes, Front-Facing Camera: No, Capacity: 4 Cell Li-Ion 76 Wh',157999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/rog-zephyrus-g14.png',NULL,8,1,0,0.00,0,1,'ASUS','2025-12-09 19:58:25','2026-05-30 07:08:33'),(59,'Asus ROG Zephyrus G14 Ryzen 7 4800HS 8GB Memory 512GB Storage Nvidia GTX 1650 4GB WINDOWS 10','Size: 14\", Resolution: 1920 x 1080 Full HD, Refresh Rate: 120Hz, Panel: Anti-glare, IPS 100% sRGB',38499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-rog-zephyrus-g14-ryzen-7-4800hs-8gb-memory-51.jpg',NULL,14,0,0,0.00,0,1,'ASUS','2025-12-12 00:58:50','2026-05-30 07:08:33'),(60,'Asus ROG Zephyrus G14 Ryzen 9 5900HS 16GB Memory 1TB Storage Nvidia RTX 3060 6GB WINDOWS 10','Resolution: 1920 x 1080 Full HD, Refresh Rate: 144Hz, Size: 14\", Panel: Anti-Glare, IPS 100% sRGB',167999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-rog-zephyrus-g14-ryzen-9-5900hs-16gb-memory-1.jpg',NULL,5,0,0,0.00,0,1,'ASUS','2025-12-25 10:48:45','2026-05-30 07:08:33'),(61,'TUF Dash F15','Device: DTS software Built-in array microphone 2-speaker system, Integrated: yes, Type: 4S1P, 4-cell Li-ion',134999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/tuf-dash-f15.png',NULL,10,0,0,0.00,0,1,'ASUS','2025-10-20 02:25:48','2026-05-30 07:08:33'),(62,'Asus Tuf f15 i7 12th gen 16GB DDR5 512GB SSD RTX 3060 6GB','Display: 15.6 inches FHD 1080p IPS  Anit-glare 144Hz 250nits, camera: 1MP 720P Front Facing Camera with Dual-Array Microphone, Features: Dual-Array Microphones, Speaker Type: Dual 2W Stereo speakers, Touchpad Type: Precision Trackpad',184499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-tuf-f15-i7-12th-gen-16gb-ddr5-512gb-ssd-rtx-3.jpg',NULL,10,0,0,0.00,0,1,'ASUS','2025-09-16 13:22:09','2026-05-30 07:08:33'),(63,'Asus Tuf F15 Gaming Laptop 12th gen i7 12700H 16GB DDR5 1TB NVME SSD RTX 3060 6GB','Display: 15.6 inches FHD 1080p IPS  Anit-glare 144Hz 250nits, camera: 1MP 720P Front Facing Camera with Dual-Array Microphone, Features: Dual-Array Microphones, Speaker Type: Dual 2W Stereo speakers, Touchpad Type: Precision Trackpad',197999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-tuf-f15-gaming-laptop-12th-gen-i7-12700h-16gb.jpg',NULL,6,0,0,0.00,0,1,'ASUS','2025-11-11 06:52:33','2026-05-30 07:08:33'),(64,'ASUS TUF Gaming A15','LCD: 15.6\" Full HD, WV, VRAM: 4GB, WLAN: Wi-Fi 6 11AX2*2_WW + BT, USB: USB 3.2A * 2, USB 3.2C * 2, OS: Windows 10 Home',131999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-tuf-gaming-a15.jpg',NULL,14,0,0,0.00,0,1,'ASUS','2025-09-28 10:27:04','2026-05-30 07:08:33'),(65,'ASUS TUF Gaming F16 (2025)','The ASUS TUF Gaming F16 FX608LP-BS96 (32GB/1TB) is a powerful 16-inch gaming laptop with an Intel Core Ultra 9 CPU, NVIDIA RTX 5070 GPU, 2.5K 165Hz display, and fast DDR5 RAMΓÇödesigned for high-performance gaming and creative work.',25499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/asus-tuf-gaming-f16-2025.png',NULL,8,0,0,0.00,0,1,'ASUS','2025-12-31 04:10:28','2026-05-30 07:08:33'),(66,'Canon Cartridge 308','The Canon Cartridge 308 is a black toner for LBP3360 and LBP3300 printers, yielding about 2,500 pages with sharp text and graphics.',9499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon-cartridge-308.jpg',NULL,12,0,0,0.00,0,21,'Canon','2025-11-04 14:34:07','2026-05-30 07:08:33'),(67,'Canon Cartridge 326','The Canon Cartridge 308 is a toner for LBP3300 and LBP3360 printers, yielding 2,500 pages and ensuring sharp, professional prints.',8599.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon-cartridge-326.jpg',NULL,6,0,0,0.00,0,21,'Canon','2025-12-13 07:31:38','2026-05-30 07:08:33'),(68,'Canon imageCLASS MF441dw','Device Memory: 1 GB, Display: WVGA Colour LCD 5-inch Touch Screen Display, Weight (approx.): 16.2 kg',104999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon-imageclass-mf441dw.jpg',NULL,14,0,0,0.00,0,21,'Canon','2025-09-15 01:56:30','2026-05-30 07:08:33'),(69,'Canon iR-2006N Digital Copier with Duplex and RADF','Color: White, Dimensions (W x D x H): 622 x 589 x 607mm (with ADF), Weight: Approximately 35.5 kg (with ADF)',209999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon-ir-2006n-digital-copier-with-duplex-and-radf.png',NULL,8,0,0,0.00,0,21,'Canon','2025-12-13 14:50:23','2026-05-30 07:08:33'),(70,'Canon Laser Shot LBP 2900 Printer','Type: Desktop Page Printer, Printing method: Electrophoto Method (On-demand fixing), Printing software: CAPT (Canon Advanced Printing Technology), Print speed: Plain paper(64 to 90 g/m2) When printing A4 continuously 12 pages/min.',25499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/canon-laser-shot-lbp-2900-printer.jpg',NULL,10,0,0,0.00,0,21,'Canon','2025-11-10 23:09:57','2026-05-30 07:08:33'),(71,'CAT-5 UTP 2M Cable','Type: CAT-5 Cable, Length: 2m, Connector: RJ-45 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',449.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/cat-5-utp-2m-cable.jpg',NULL,14,0,0,0.00,0,20,NULL,'2025-12-21 17:04:22','2026-05-30 07:08:33'),(72,'Colorful B550M Gaming Frozen Motherboard AMD','The B550M Gaming Frozen motherboard supports AMD AM4 CPUs, offers 4 DDR4 slots, PCIe 4.0, M.2, SATA ports, USB 3.1, HDMI, and 8-channel audio. It features RGB lighting, efficient cooling, and a 3-year warranty.',13499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/colorful-b550m-gaming-frozen-motherboard-amd.jpg',NULL,11,0,0,0.00,0,7,NULL,'2025-09-05 11:17:56','2025-11-13 14:07:44'),(73,'Colorful Z490 Gaming Pro Motherboard Intel','The Colorful Z490 Gaming Pro is an ATX motherboard for 10th Gen Intel Core processors, featuring 4 DDR4 DIMM slots, multiple PCIe and M.2 slots, and 6 SATA ports. It offers USB 3.2, HDMI, DisplayPort, and Realtek Gigabit LAN, supporting up to 125W CPUs with a 128MB UEFI BIOS.',19499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/colorful-z490-gaming-pro-motherboard-intel.jpg',NULL,14,0,0,0.00,0,7,NULL,'2025-10-19 23:44:48','2025-11-30 10:08:31'),(74,'Cryorig M9 CPU air cooler','The Cryorig M9 is a compact tower CPU cooler with three 6mm copper heatpipes and a copper base for optimal heat conduction. It features a 92mm PWM fan operating at 600ΓÇô2200 RPM, with a noise level of 26.4 dBA, max airflow of 48.4 CFM, and static pressure of 3.1 mmH2O.',4199.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/cryorig-m9-cpu-air-cooler.png',NULL,15,0,0,0.00,0,12,NULL,'2025-10-06 17:54:19','2025-12-03 02:35:47'),(75,'Dell 24 Monitor ΓÇô S2421HN','The Dell 2.1 Speaker System AE415 features 30W RMS power, clear sound, and a compact design. It includes dual 3.5mm inputs and a headphone jack, making it ideal for desktops and notebooks.',26499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-24-monitor-s2421hn.webp',NULL,7,0,0,0.00,0,4,'Dell','2025-09-13 17:20:31','2026-05-30 07:08:33'),(76,'Dell 27\" FHD IPS Monitor','Display Type: LED-backlit LCD monitor / TFT active matrix, Adaptive-Sync Technology: AMD FreeSync, Native Resolution: Full HD (1080p) 1920 x 1080 at 75 Hz, Panel Type: IPS',34299.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-27-fhd-ips-monitor.png',NULL,6,0,0,0.00,0,4,'Dell','2025-12-13 18:38:54','2026-05-30 07:08:33'),(77,'Dell Chromebook 13 3380','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',31999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-chromebook-13-3380.png',NULL,11,0,0,0.00,0,1,'Dell','2025-09-15 08:22:49','2026-05-30 07:08:33'),(78,'Dell Inspiron 14 5425 Ryzen 5-5625U 16GB RAM 512GB SSD','The Dell Inspiron 14 5425 is powered by AMD Ryzen 5-5625 processor. It has AMD integrated Radeon graphics as a GPU which is RX Vega 7. This laptop comes with 16GB of DDR4 RAM. This comes with 512 GB of M.2 SSD storage, which can be increased.',97999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-inspiron-14-5425-ryzen-5-5625u-16gb-ram-512gb.webp',NULL,14,0,0,0.00,0,1,'Dell','2025-12-08 12:29:58','2026-05-30 07:08:33'),(79,'Dell Latitude 7330','Size: 13.3\", Resolution: FHD (1920x1080), Others: AG, SLP, No-Touch, ComfView+, WVA, 400 nits, FHD IR Cam+IP, WLAN, CF',4999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-latitude-7330.jpg',NULL,10,0,0,0.00,0,1,'Dell','2025-12-13 02:29:03','2026-05-30 07:08:33'),(80,'Dell Latitude 7430','Screen Size: 14 inch, Resolution: 1080 x 1920 Pixels, Screen Type: FHD AG, Non-Touch, WVA, 250 nits, HD RGB Cam, WLAN, Carbon Fiber',154499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-latitude-7430.jpg',NULL,15,0,0,0.00,0,1,'Dell','2025-09-29 19:26:43','2026-05-30 07:08:33'),(81,'Dell OptiPlex 5400 AIO','OS: Windows 10 Professional, Monitor: OptiPlex All-in-One (Touch / Non-Touch), Size: 23.8\"',134999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-optiplex-5400-aio.png',NULL,5,0,0,0.00,0,2,'Dell','2025-09-01 03:31:12','2026-05-30 07:08:33'),(82,'Dell PowerEdge R740','The Dell PowerEdge R740 is a 2U rack server with dual Intel Xeon Scalable processors (up to 28 cores each), supporting up to 3TB of DDR4 RAM and flexible storage options (up to 16 x 2.5ΓÇ¥ or 8 x 3.5ΓÇ¥ drives). It can support up to three 300W or six 150W GPUs for accelerated workloads, making it ideal for demanding applications like VDI and AI. The server provides advanced management tools, robust security features, and versatile networking options, balancing performance, scalability, and reliabili',1106999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-poweredge-r740.jpg',NULL,8,0,0,0.00,0,22,'Dell','2025-09-23 23:11:05','2026-05-30 07:08:33'),(83,'Dell Precision 3470 Workstation','Display: 14\" Full HD IPS Display, Wireless: Intel AX211, 2x2 MIMO, 2400 Mbps, 2.4/5/6 GHz, Wi-Fi 6/6E (WiFi 802.11ax), Bluetooth 5.2, Keyboard: Single Pointing Non-Backlit English International Keyboard, Palm Rest: Single Pointing, No Security, Battery: 4 Cell, 64WHr, standard battery',134999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-precision-3470-workstation.png',NULL,5,0,0,0.00,0,1,'Dell','2025-12-23 00:42:32','2026-05-30 07:08:33'),(84,'Dell 24\" UltraSharp Monitor','Display Type: LED-backlit LCD monitor / TFT active matrix, Diagonal Size: 23.8\", Viewable Size: 23.8\", Panel Type: IPS, Built-in Devices: USB 3.0 hub',42999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-24-ultrasharp-monitor.png',NULL,9,0,0,0.00,0,4,'Dell','2025-09-21 10:34:39','2026-05-30 07:08:33'),(85,'Dell Vostro 3510','Type: Full HD / HD, Size: 15.6\", Type: Factory Installed / Preinstalled Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',61999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-vostro-3510.jpg',NULL,5,0,0,0.00,0,1,'Dell','2025-10-15 20:20:05','2026-05-30 07:08:33'),(86,'Dell XPS 13 i7 12th gen','Size: 13.4\", Resolution: FHD+ 1920 x 1200, Refresh Rate: 60Hz, Others: Non-Touch, Anti-Glare, 500 nit, InfinityEdge',224999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-13-i7-12th-gen.jpg',NULL,12,0,0,0.00,0,1,'Dell','2025-12-17 15:56:47','2026-05-30 07:08:33'),(87,'Dell XPS 13 Plus 9320 i7 12th gen 13.4 OLED  32GB DDR5  1TB  SSD','Size: 13.4\", Resolution: 3.5K 3456 x 2160 with Infinity Edge, Refresh Rate: 60Hz, Type: OLED Touch, Anti-Glare',303499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-13-plus-9320-i7-12th-gen-134-oled-32gb-dd.jpg',NULL,12,0,0,0.00,0,1,'Dell','2025-12-28 03:17:01','2026-05-30 07:08:33'),(88,'Dell XPS 13 Plus 9320 i7 1260p 16GB RAM 512GB SSD Windows 11','Size: 13.4\", Resolution: 1920x1200, FHD+ with Infinity Edge, Refresh Rate: 60Hz, Type: Touch, Anti-Glare',243499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-13-plus-9320-i7-1260p-16gb-ram-512gb-ssd-.jpg',NULL,7,0,0,0.00,0,1,'Dell','2025-11-10 13:11:18','2026-05-30 07:08:33'),(89,'Dell XPS 13Plus 9320  i7 12th gen 13.4 OLED 16GB 512GB','Size: 13.4\", Resolution: 3.5K 3456 x 2160 with Infinity Edge, Refresh Rate: 60Hz, Type: OLED Touch, Anti-Glare',263499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-13plus-9320-i7-12th-gen-134-oled-16gb-512.jpg',NULL,7,0,0,0.00,0,1,'Dell','2025-10-03 11:37:41','2026-05-30 07:08:33'),(90,'Dell XPS 15 9520 i7 12700H 16GB RAM 512GB SSD Windows 11','Size: 15.6\", Resolution: 1920x1200, FHD+ with Infinity Edge, Refresh Rate: 60Hz, Type: Non-Touch, Anti-Glare',255499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-15-9520-i7-12700h-16gb-ram-512gb-ssd-wind.jpg',NULL,7,0,0,0.00,0,1,'Dell','2025-11-25 20:26:40','2026-05-30 07:08:33'),(91,'Dell XPS 17 9720','Material: Aluminum, Weight: 2.21 kg (non-touch)2.42 kg (touch), Dimensions (inches): 14.74 x 9.76 x 0.77',91999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/dell-xps-17-9720.webp',NULL,5,0,0,0.00,0,1,'Dell','2025-11-07 01:49:57','2026-05-30 07:08:33'),(92,'Portege X30-G','The Dynabook Portege X30-G is a lightweight 13.3\" laptop with an Intel i5, 8GB RAM, and 256GB SSD. It features a Full HD display, Wi-Fi 6, Bluetooth 5.1, security options, and meets military durability standards, making it ideal for business on the go.',95499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/portege-x30-g.jpg',NULL,11,0,0,0.00,0,1,NULL,'2025-08-01 04:28:44','2026-01-16 13:13:38'),(93,'E55BT WIreless','The JBL E55BT are wireless over-ear headphones with 50mm drivers delivering balanced JBL sound, Bluetooth 4.0 with multipoint support, and up to 20 hours of battery life. They feature comfortable foldable design, on-ear controls, a detachable wired cable, and hands-free calling, making them ideal for everyday wireless listening.',9499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/e55bt-wireless.png',NULL,5,0,0,0.00,0,14,NULL,'2025-11-03 00:38:14','2025-11-04 10:27:14'),(94,'GALAX GeForce RTXΓäó 4080','CUDA Cores: 9728, Boost Clock: 2565MHz, 1-Click OC Clock: 2580MHz Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',216499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/galax-geforce-rtx-4080.png',NULL,9,0,0,0.00,0,6,NULL,'2025-10-29 14:51:24','2026-05-30 07:08:33'),(95,'GALAX GeForce RTXΓäó 4090','CUDA Cores: 16384, Boost Clock: 2580MHz, 1-Click OC Clock: 2595MHz Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',304499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/galax-geforce-rtx-4090.png',NULL,6,0,0,0.00,0,6,NULL,'2025-09-09 07:23:55','2026-05-30 07:08:33'),(96,'Gigabyte Z890M Gaming X ( Micro-ATX )','The Gigabyte Z890M Gaming X is a compact micro-ATX motherboard for Intel Core Ultra Series 2 (Arrow Lake-S) processors. It supports up to 256 GB DDR5 RAM (up to DDR5-9066+ OC), PCIe 5.0 for the primary GPU slot, has 3 M.2 slots, 4 SATA ports, 2.5 GbE LAN but no Wi-Fi, and multiple display outputs (HDMI 2.1 and dual DisplayPort 2.1). It features good VRM cooling, supports CPU overclocking, and is designed for high-performance gaming and productivity in a smaller form factor.',76499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/gigabyte-z890m-gaming-x-micro-atx-.png',NULL,11,0,0,0.00,0,7,NULL,'2025-10-10 08:56:38','2026-01-16 13:13:38'),(97,'Google Chromecast 4K UHD','The Google Chromecast 4K UHD streams 4K HDR video with Dolby Vision and Dolby Atmos, features a voice remote with Google Assistant, runs Android TV, and supports thousands of apps for high-quality streaming.',11999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/google-chromecast-4k-uhd.jpg',NULL,12,0,0,0.00,0,16,NULL,'2025-10-06 12:51:40','2026-01-16 13:13:38'),(98,'Google Chromecast 1080p HD','The Google Chromecast 1080p HD is a compact streaming device supporting Full HD (1080p) HDR video with Dolby audio. It runs Android TV with Google TV, includes a voice remote, offers Wi-Fi and Bluetooth connectivity, and supports thousands of apps. ItΓÇÖs an affordable option for streaming HD content on any HDMI-equipped TV.',6999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/google-chromecast-1080p-hd.jpg',NULL,11,0,0,0.00,0,16,NULL,'2025-12-20 17:05:26','2026-01-16 13:13:38'),(99,'Google Pixel 7','Material: Glass Front & Back, with Aluminum Frame, Weight: 197 g, Dimensions (inches): 6.13 x 2.88 x 0.34',11999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/google-pixel-7.png',NULL,5,0,0,0.00,0,17,NULL,'2025-09-28 06:41:41','2026-01-16 13:13:38'),(100,'H510 FLOW','The NZXT H510 Flow is a compact mid-tower PC case with excellent airflow, featuring a perforated front panel and two 120mm fans. It supports various motherboards and liquid cooling up to 360mm in the front. Its design offers improved cooling and a stylish look, perfect for high-performance gaming builds.',13999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/h510-flow.png',NULL,10,0,0,0.00,0,11,NULL,'2025-09-18 18:23:37','2026-01-16 13:13:38'),(101,'H510i','The NZXT H510i is a compact mid-tower ATX case with a tempered glass side panel, supporting Mini-ITX to ATX motherboards. It features integrated RGB and fan control via the Smart Device V2, good cooling options with pre-installed fans, USB-C front port, and excellent cable management for clean builds.',19499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/h510i.png',NULL,6,0,0,0.00,0,11,NULL,'2025-11-07 10:44:24','2026-01-16 13:13:38'),(102,'H710','The NZXT H710 is a spacious mid-tower ATX case with a tempered glass side panel, supporting up to E-ATX motherboards, large GPUs, and tall CPU coolers. It includes four pre-installed fans, supports multiple radiator sizes, features USB-C front ports, and offers excellent cable management and airflowΓÇöperfect for high-end gaming and workstation builds.',21999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/h710.png',NULL,6,0,0,0.00,0,11,NULL,'2025-08-27 14:57:59','2026-01-16 13:13:38'),(103,'H710i','The NZXT H710i is a spacious, premium mid-tower PC case with a tempered glass side panel, supporting up to E-ATX motherboards and large GPUs. It features excellent cooling options, built-in RGB and fan control via Smart Device V2, USB-C front port, and clean cable managementΓÇöideal for high-end gaming and workstation builds.',26999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/h710i.png',NULL,13,0,0,0.00,0,11,NULL,'2025-08-19 21:39:33','2026-01-16 13:13:38'),(104,'HDMI Cable 5m','Speed: ULTRA HIGH SPEED, Length: 5m, Connector: Type A to Type A, Resolution Support: 720P;1080L;1080P',1199.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hdmi-cable-5m.png',NULL,14,0,0,0.00,0,16,NULL,'2025-09-29 20:37:32','2026-01-16 13:13:38'),(105,'HP 14\" EliteBook 840 G8','Type: Factory Installed, OS: Windows 10 Pro, Architecture: 64-bit (x86_64) Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',169999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-14-elitebook-840-g8.png',NULL,5,0,0,0.00,0,1,'HP','2025-12-02 15:24:00','2026-05-30 07:08:33'),(106,'HP 49A Black Original Toner Cartridge','Brand: HP, Model: HP 49A, Part No: Q5949A, Print Technology: Laser Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',11499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-49a-black-original-toner-cartridge.webp',NULL,9,0,0,0.00,0,21,'HP','2025-10-03 08:55:35','2026-05-30 07:08:33'),(107,'HP 80A Black Original Toner Cartridge','Brand: HP, Model: HP 80A, Part No: CF280A, Printing Technology: Laser Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',11499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-80a-black-original-toner-cartridge.webp',NULL,7,0,0,0.00,0,21,'HP','2025-10-24 22:25:20','2026-05-30 07:08:33'),(108,'HP 85A Black Original Toner Cartridge','Brand: HP, Model: HP 85A, Part No: CE285A, Printing Technology: Laser Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',9499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-85a-black-original-toner-cartridge.jpg',NULL,11,0,0,0.00,0,21,'HP','2025-11-07 18:28:03','2026-05-30 07:08:33'),(109,'HP 90X High Yield Black Original LaserJet Toner Cartridge, CE390X','Color(s) of print cartridges: Black, Print technology: Laser, Page yield (black and white): ~24,000 pages',2899.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-90x-high-yield-black-original-laserjet-toner-ca.webp',NULL,8,0,0,0.00,0,21,'HP','2025-11-02 12:19:01','2026-05-30 07:08:33'),(110,'HP Chromebook x360 14 G1 (2 in 1)','Type: Preinstalled, OS: Chrome OS, Internal: 14.0\" FHD IPS eDP Brightview WLED-backlit slim-flat (3.0 mm) touch screen',54999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-chromebook-x360-14-g1-2-in-1.jpg',NULL,14,0,0,0.00,0,1,'HP','2025-10-26 07:20:48','2026-05-30 07:08:33'),(111,'HP EliteBook x360 1030 G8 Notebook PC','Type: Preinstalled, OS: Windows 10 Pro 64-bit, WLAN: Intel Wi-Fi 6 AX201 802.11a/b/g/n/ac/ax (2x2) and Bluetooth 5 combo, (vPro / non - vPro)',114499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-elitebook-x360-1030-g8-notebook-pc.png',NULL,6,0,0,0.00,0,1,'HP','2025-08-23 20:24:27','2026-05-30 07:08:33'),(112,'Hp Envy X360','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',98499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-envy-x360.jpg',NULL,7,0,0,0.00,0,1,'HP','2025-10-18 16:22:48','2026-05-30 07:08:33'),(113,'HP Laptop 15s','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',78499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-laptop-15s.png',NULL,13,0,0,0.00,0,1,'HP','2025-10-30 02:06:16','2026-05-30 07:08:33'),(114,'HP Omen Transcend 14 Intel','With an ultra-thin and light all-metal chassis that feels as luxurious as it looks, a longer and more efficient battery life thanks to USB-C fast charging, a new Intel Core Ultra processor with powerful NVIDIA GeForce RTX graphics, and a 2.8K 120Hz OLED display that is both bright and fast.',83999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-omen-transcend-14-intel.png',NULL,5,0,0,0.00,0,1,'HP','2025-10-14 11:31:08','2026-05-30 07:08:33'),(115,'HP ProBook 430 G5 8th Gen i5 8250U, 8GB RAM, 256GB SSD, 13.3ΓÇ│ HD AG, Windows 11 Pro','Size: 13.3\" Diagonal, Type: FHD UWVA, Backlit: LED Backlit, Touch: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',44999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-probook-430-g5-8th-gen-i5-8250u-8gb-ram-256gb-s.jpg',NULL,7,0,0,0.00,0,1,'HP','2025-10-31 01:56:32','2026-05-30 07:08:33'),(116,'HP Spectre X360','Screen Size: 13.5\", Screen Monitor: OLED, User Interface Type: Yes, Display Resolution: 3000 x 2000 pixels',169999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-spectre-x360.jpg',NULL,8,0,0,0.00,0,1,'HP','2025-10-21 09:49:58','2026-05-30 07:08:33'),(117,'Hp Victus 15 Intel i5 12450H 8GB RAM 512GB SSD Nvidia GTX 1650 4GB Windows 11','This HP laptop comes equipped with an Geforce Nvidia GeForce GTX 1650 4GB Graphics, an Intel Core i7-12450 H processor, and 8 GB of RAM. You get a 15.6-inch screen laptop, which is midsize for a gaming setup. The weight of the laptop is 2.39 kg(Weight varies by configuration).',97999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-victus-15-intel-i5-12450h-8gb-ram-512gb-ssd-nvi.png',NULL,7,0,0,0.00,0,1,'HP','2025-09-04 18:37:49','2026-05-30 07:08:33'),(118,'HP ZBook Firefly 14 G8 Mobile Workstation','Vendor: Intel, Model: CoreΓäó i7 1185G7, Cores: 4, Threads: 8 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',214499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/hp-zbook-firefly-14-g8-mobile-workstation.png',NULL,5,0,0,0.00,0,1,'HP','2025-11-28 10:09:37','2026-05-30 07:08:33'),(119,'Huawei Display 23.8\"','Display Size: 23.8 inches, Dusplay Type: IPS, Aspect Ratio: 16 :9, Resolution: 1920 x 1080 (FHD), Refresh Rate: 75 Hz',24999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/huawei-display-238.png',NULL,6,0,0,0.00,0,4,NULL,'2025-10-11 12:37:37','2026-01-16 13:15:49'),(120,'Ideapad 3','The Lenovo Ideapad 3 is powered by AMD Ryzen 5550U processor. This SoC has six cores and twelve threads with a maximum boost frequency of 4.0 GHz. In addition, it features 8MB of L3 cache memory. It has AMD integrated Radeon graphics as a GPU.\r\n\r\n\r\n\r\nThis laptop comes with 8GB of DDR4 RAM, which may be increased to 12GB later on. This comes with 256GB of M.2 SSD storage, which can be increased.',77999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ideapad-3.png',NULL,5,0,0,0.00,0,1,NULL,'2025-10-25 17:13:03','2026-01-16 13:15:49'),(121,'IdeaPad 3','Built-In Microphone: Yes, Front-Facing Camera: No Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',154499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ideapad-3.png',NULL,12,0,0,0.00,0,1,NULL,'2025-12-25 13:31:52','2026-05-30 07:08:33'),(122,'IdeaPad  Slim 3 15','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',234999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ideapad-slim-3-15.jpg',NULL,13,0,0,0.00,0,1,NULL,'2025-08-03 21:50:32','2026-05-30 07:08:33'),(123,'iMac 24 inch','Display: 24-inch 4.5K Retina display, Camera: 1080p FaceTime HD camera, Audio: High-fidelity six-speaker system with force-cancelling woofers, Connections and Expansion: Two Thunderbolt / USB 4 ports with support for DisplayPort, Thunderbolt Version: Thunderbolt 3 (up to 40Gb/s)',221999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/imac-24-inch.jpg',NULL,12,0,0,0.00,0,2,NULL,'2025-12-16 07:23:02','2026-01-16 13:15:49'),(124,'Inspiron 15 3511','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',91999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/inspiron-15-3511.png',NULL,6,0,0,0.00,0,1,NULL,'2025-11-13 19:34:21','2026-05-30 07:08:33'),(125,'Inspiron 3511','The Dell Inspiron 15 3511 (2021) is a 15.6-inch Full HD display Windows laptop. It has an Intel Core i5-1165G67 processor, a GeForce MX350 GPU, 8GB of RAM, and 256GB of SSD storage. However, depending on the region, it may be available in a variety of memory configurations. The majority of the chassis is made of plastic, and the laptop has a matte covering to prevent fingerprints and smudges. It also features the majority of the required I/O ports, and wireless networking choices include WiFi 5 ',71999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/inspiron-3511.png',NULL,6,0,0,0.00,0,1,NULL,'2025-12-11 13:38:34','2026-01-16 13:07:21'),(126,'Intel i5 13600K','Total Cores: 14, No. of Performance-cores: 6, No. of Efficient-cores: 8, Total Threads: 20',54499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/intel-i5-13600k.jpg',NULL,9,0,0,0.00,0,5,NULL,'2025-10-17 09:54:23','2026-01-16 13:15:49'),(127,'Intel i7 13700K','Total Cores: 16, No. of Performance-cores: 8, No. of Efficient-cores: 8, Total Threads: 24',99999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/intel-i7-13700k.jpg',NULL,10,0,0,0.00,0,5,NULL,'2025-08-14 13:57:08','2026-01-16 13:15:49'),(128,'Intel i9 13900K','Total Cores: 24, No. of Performance-cores: 8, No. of Efficient-cores: 16, Total Threads: 32',44799.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/intel-i9-13900k.jpg',NULL,10,0,0,0.00,0,5,NULL,'2025-12-31 13:10:15','2026-01-10 15:17:33'),(129,'KRAKEN X63 RGB','The NZXT Kraken X63 RGB is a 280mm all-in-one liquid CPU cooler with dual 140mm RGB fans, a quiet and efficient pump, and customizable RGB lighting featuring a 360┬░ rotatable infinity mirror pump cap. It supports many Intel and AMD sockets, offers strong cooling performance for high-end CPUs, and is controlled via NZXTΓÇÖs CAM software. It balances excellent cooling, low noise, and stylish RGB aesthetics.',24499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/kraken-x63-rgb.png',NULL,7,0,0,0.00,0,12,NULL,'2025-12-09 17:35:45','2026-01-16 13:15:49'),(130,'Latitude 14\" 5420','The Dell Latitude 5420 14\" A 3 GHz 11th Gen Intel Core i7 4-core vPro processor and 8GB of RAM enables you to speed through your task, while integrated Intel Iris Xe Graphics and dual Thunderbolt 4 connections allow you to connect up to two 4K displays or one 8K display.',91999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/latitude-14-5420.png',NULL,7,0,0,0.00,0,1,NULL,'2025-11-27 15:06:47','2026-01-16 13:15:49'),(131,'Legion 5','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',169499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/legion-5.png',NULL,8,0,0,0.00,0,1,'Lenovo','2025-10-23 02:28:02','2026-05-30 07:08:33'),(132,'Legion 5','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',157999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/legion-5.png',NULL,14,0,0,0.00,0,1,'Lenovo','2025-11-21 14:19:48','2026-05-30 07:08:33'),(133,'Legion 5 Pro','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',155449.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/legion-5-pro.png',NULL,6,0,0,0.00,0,1,'Lenovo','2025-10-08 10:26:49','2026-05-30 07:08:33'),(134,'Legion 5 Pro AMD Ryzen 7 5800H 16GB RAM 512GB SSD NVIDIA RTX 3050 4GB','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',169999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/legion-5-pro-amd-ryzen-7-5800h-16gb-ram-512gb-ssd-.png',NULL,10,0,0,0.00,0,1,'Lenovo','2025-11-10 23:08:55','2026-05-30 07:08:33'),(135,'Legion 7','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',125499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/legion-7.jpg',NULL,8,0,0,0.00,0,1,'Lenovo','2025-09-27 09:22:39','2026-05-30 07:08:33'),(136,'Lenovo IdeaPad 3 Chromebook','Vendor: Intel, Model: Celeron┬« N4020, Cores: 2, Threads: 2 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',33999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-ideapad-3-chromebook.png',NULL,9,0,0,0.00,0,1,'Lenovo','2025-08-10 05:09:17','2026-05-30 07:08:33'),(137,'Lenovo IdeaPad 3 i5 1235U 8GB RAM 256GB SSD Windows 11','Size: 14\", Resolution: FHD(1920 x 1080), Type: IPS Panel, Anti-Glare, Brightness: 300 nits',79999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-ideapad-3-i5-1235u-8gb-ram-256gb-ssd-window.jpg',NULL,12,0,0,0.00,0,1,'Lenovo','2025-09-25 00:31:31','2026-05-30 07:08:33'),(138,'Lenovo Legion 5 Pro Ryzen 9 6900HX, 16GB RAM, 1TB SSD,  RTX 3070Ti, 16inch QHD 165Hz','Vendor: AMD, Model: RyzenΓäó 9 6900HX, Cores: 0, Threads: 0 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',282999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-legion-5-pro-ryzen-9-6900hx-16gb-ram-1tb-ss.jpg',NULL,13,0,0,0.00,0,1,'Lenovo','2025-11-03 18:34:29','2026-05-30 07:08:33'),(139,'Lenovo Legion 5  Ryzen 5 5600H 8GB RAM 512GB SSD NVIDIA GeForce RTX 3050Ti 4GB','Operating System: Windows 11 Home, Screen: 1920x1080 pixels Full HD IPS Display, 15.6\" with Anti-glare coating, No TouchScreen, Refresh Rate: 120Hz, Input: Keyboard with dedicated number pad & Touchpad with click buttons, Wireless & Networking: Wi-Fi 6 802.11AX wireless, Ethernet LAN port, Bluetooth',122499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-legion-5-ryzen-5-5600h-8gb-ram-512gb-ssd-nv.jpg',NULL,14,0,0,0.00,0,1,'Lenovo','2025-11-03 10:49:49','2026-05-30 07:08:33'),(140,'Lenovo Legion 5i i7 12700H 16GB DDR5 1TB NVME SSD RTX 3060 6GB Windows 10 Home','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',207999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-legion-5i-i7-12700h-16gb-ddr5-1tb-nvme-ssd-.png',NULL,5,0,0,0.00,0,1,'Lenovo','2025-09-21 20:49:30','2026-05-30 07:08:33'),(141,'Lenovo Legion 5 AMD Ryzen 7 6800H 16GB RAM 512GB SSD Nvidia RTX 3060 6GB','Graphics: NVIDIA┬« GeForce RTXΓäó 3060 Laptop GPU, 6 GB GDDR6, Boost Clock 1702 MHz, Maximum Graphics Power 130W, Audio: 2x2W speakers with Nahimic Audio for Gamers, webcam: HD 720p with E-Shutter, Connectivity: WIFI 6 802.11AX (2 x 2) and Bluetooth┬« 5.1',204999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-legion-5-amd-ryzen-7-6800h-16gb-ram-512gb-s.png',NULL,7,0,0,0.00,0,1,'Lenovo','2025-08-21 22:41:43','2026-05-30 07:08:33'),(142,'Lenovo Thinkbook 13x Gen 4 Intel','The Lenovo ThinkBook 13x Gen 4 (13\" Intel) laptop delivers business-class performance with Intel┬« CoreΓäó Ultra processors and the Lenovo LA3 AI chip. Intel┬« CoreΓäó Ultra laptops provide great productivity and immersive AI experiences without latency or battery waste.',115499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-thinkbook-13x-gen-4-intel.webp',NULL,11,0,0,0.00,0,1,'Lenovo','2025-08-11 19:26:44','2026-05-30 07:08:33'),(143,'Thinkpad T14 Gen 3','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',217499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-t14-gen-3.png',NULL,6,0,0,0.00,0,1,'Lenovo','2025-10-18 03:43:09','2026-05-30 07:08:33'),(144,'LENOVO V14 Gen 4','Processor: 1x AMD RyzenΓäó 5 7520U Processor(RyzenΓäó 5 7520U), Memory: 1x 8GBLPDDR5-5500, Operating System: Windows 11 Pro 64(EN:English), Hard Drive: 1x 256 GB SSD PCIe, Wireless Network: 1x Wireless 802.11 2x2 AC; Bluetooth┬« 5.1 or above',188499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lenovo-v14-gen-4.webp',NULL,11,0,0,0.00,0,1,'Lenovo','2025-10-28 09:44:33','2026-05-30 07:08:33'),(145,'Ideapad Pro 5 16IMH9 Ultra 9 32GB 1TB','Lenovo ideapad 5 Pro laptop is more powerful performance than its previous model laptop with intel brand New ultra 9 185H processor and it give more fast and efficient performance with 32GB DDR5 Memory .The 16 inch OLED Display gives you more vivid color with 120Hz refresh rate . \r\n\r\nThis laptop take your work to next level with AI Features and RTX 4050 Graphics help you for more powerful graphics performance in 3D Hybrid architecture.',104999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ideapad-pro-5-16imh9-ultra-9-32gb-1tb.jpg',NULL,6,0,0,0.00,0,1,NULL,'2025-09-12 07:04:30','2026-01-16 13:15:49'),(146,'LG UltraGear Curved Gaming Monitor','Size: 34 inch, Display Type: IPS, Display Resolution: UW-FHD, Color Gamut: (Typ.), sRGB: 99% (CIE1931)',97999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lg-ultragear-curved-gaming-monitor.png',NULL,15,0,0,0.00,0,4,NULL,'2025-10-22 22:32:55','2026-01-16 13:15:49'),(147,'Lightning Digital A/V Adapter','he Lightning Digital AV Adapter lets you mirror your iPhone or iPad screen to an HDMI TV or projector in up to 1080p HD. It supports video and audio output, connects via Lightning, and requires a separate HDMI cable. Ideal for presentations and media sharing.',6499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lightning-digital-av-adapter.png',NULL,12,0,0,0.00,0,16,NULL,'2025-11-14 04:16:52','2026-01-16 13:15:49'),(148,'Lightning to USB Camera Adapter','The Lightning to USB Camera Adapter lets you transfer photos and videos from a camera to your iPhone or iPad, supports USB peripherals with power, and works with iOS 9.2 or later.',4199.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/lightning-to-usb-camera-adapter.jpg',NULL,8,0,0,0.00,0,16,NULL,'2025-09-24 18:18:11','2026-01-16 13:15:49'),(149,'Logitech Group','Camera: Full HD 1080p @ 30 fps with autofocus and 10x ZOOM, Speakerphone: Full-duplex with acoustic echo cancellation, Microphone: Single omni-directional microphone supporting 20-foot diameter range, Speakers: ΓÇô Frequency response => 120Hz ΓÇô 14KHz, HUB/Cable: Central mountable hub for connection of all components',168999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/logitech-group.webp',NULL,15,0,0,0.00,0,15,'Logitech','2025-11-03 22:16:37','2026-05-30 07:08:33'),(150,'Logitech K375s Wireless Keyboard','The Logitech K375s is a versatile wireless keyboard that connects to up to three devices via Bluetooth or a USB receiver. It features a full-size layout, Easy-Switch keys for device switching, and a stand for phones or tablets. With compatibility across multiple operating systems, it offers up to 18 months of battery life and an adjustable typing angle, making it ideal for multi-device use.',4449.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/logitech-k375s-wireless-keyboard.png',NULL,8,0,0,0.00,0,13,'Logitech','2025-11-23 15:32:43','2026-05-30 07:08:33'),(151,'Macbook Pro 14','Battery Type: Lithium-polymer, Power Supply Input: USB Type C, Battery Life (up to): 17 hours, Power Supply Maximum Wattage: 67 watts',292799.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/macbook-pro-14.png',NULL,6,0,0,0.00,0,1,'Apple','2025-10-18 04:49:36','2026-05-30 07:08:33'),(152,'MacBook Pro 16','Battery Type: Lithium-polymer, Power Supply Input: USB Type C, Battery Life (up to): 21 hours, Power Supply Maximum Wattage: 140 watts',294999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/macbook-pro-16.png',NULL,15,0,0,0.00,0,1,'Apple','2025-08-16 21:37:25','2026-05-30 07:08:33'),(153,'Microsoft Surface Pro 12 2025','Microsoft Surface Pro 12 (2025)  a thin, light 2-in-1 with a 12-inch high-resolution PixelSense touchscreen, modern Snapdragon X-series CPU for long battery life, optional LTE/5G connectivity, and compatibility with Surface Pen and Type Cover. Ideal for creators and professionals who need a portable, versatile Windows device.',162499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/microsoft-surface-pro-12-2025.png',NULL,8,0,0,0.00,0,1,'Microsoft','2025-12-29 01:47:48','2026-05-30 07:08:33'),(154,'MSI Pro X670-P WIFI DDR5','The PRO Series is tailored to professionals from all walks of life. The lineup features impressive performance and high quality, while aiming to provide users incredible experience. Users who care about productivity and efficiency can definitely count on the MSI PRO Series to assist you with multitasking and increasing efficiency.',20499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/msi-pro-x670-p-wifi-ddr5.png',NULL,7,0,0,0.00,0,7,NULL,'2025-10-01 01:41:12','2026-01-16 13:15:49'),(155,'MSI Pro Z790-P WIFI DDR5','The PRO Series is tailored to professionals from all walks of life. The lineup features impressive performance and high quality, while aiming to provide users incredible experience. Users who care about productivity and efficiency can definitely count on the MSI PRO Series to assist you with multitasking and increasing efficiency.',19499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/msi-pro-z790-p-wifi-ddr5.png',NULL,6,0,0,0.00,0,7,NULL,'2025-12-19 17:21:39','2026-01-16 13:15:49'),(156,'AER RGB 2 (120mm)','The NZXT Aer RGB 2 (120mm) is a quiet, durable RGB case fan with PWM control, 8 customizable LEDs, and optimized airflow, perfect for gaming and custom PC builds.',4122.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/aer-rgb-2-120mm.png',NULL,8,0,0,0.00,0,12,NULL,'2025-09-17 18:27:56','2026-01-16 13:15:49'),(157,'NZXT C750 Gold ATX PSU','The NZXT C750 Gold is a fully modular 750W power supply with 80 Plus Gold efficiency, quiet fluid dynamic bearing fan, and high-quality sleeved cables. It supports modern GPUs, offers stable and efficient power delivery, and includes a 10-year warrantyΓÇöideal for gaming and high-performance PCs.',19499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/nzxt-c750-gold-atx-psu.jpg',NULL,11,0,0,0.00,0,10,NULL,'2025-10-06 17:50:00','2026-01-16 13:15:49'),(158,'Rog Strix G15','Built-In Microphone: Yes, Front-Facing Camera: No Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',234999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/rog-strix-g15.png',NULL,13,0,0,0.00,0,1,'ASUS','2025-12-27 23:24:09','2026-05-30 07:08:33'),(159,'Rog Zephyrus G14','Built-In Microphone: Yes, Front-Facing Camera: No Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',167999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/rog-zephyrus-g14.png',NULL,5,0,0,0.00,0,1,'ASUS','2025-10-11 20:17:53','2026-05-30 07:08:33'),(160,'Rog Zephyrus G14','Built-In Microphone: Yes, Front-Facing Camera: No Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',157999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/rog-zephyrus-g14.png',NULL,10,0,0,0.00,0,1,'ASUS','2025-08-15 05:10:18','2026-05-30 07:08:33'),(161,'SAMSUNG Portable SSD T7 Shield USB 3.2','The Samsung T7 Shield is a fast, rugged portable SSD with up to 1,050 MB/s speeds, water and dust resistance, AES 256-bit encryption, and comes in 1TB, 2TB, and 4TB sizes.',24499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/samsung-portable-ssd-t7-shield-usb-32.png',NULL,7,0,0,0.00,0,9,'Samsung','2025-10-23 16:03:23','2026-05-30 07:08:33'),(162,'Sandisk Extreme Portable SSD','Read: 1050 MB/s, Write: 1000 MB/s Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',17499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/sandisk-extreme-portable-ssd.png',NULL,12,0,0,0.00,0,9,NULL,'2025-12-13 22:36:08','2026-05-30 07:08:33'),(163,'Studio 3 wireless','The Beats Studio 3 Wireless are high-end over-ear Bluetooth headphones with advanced noise cancellation, AppleΓÇÖs W1 chip for simple pairing and device switching, up to 22 hours of battery life with ANC on, quick charging, and a comfortable fit. They deliver strong sound quality and smooth connectivity, especially for Apple users.',34999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/studio-3-wireless.png',NULL,15,0,0,0.00,0,14,NULL,'2025-10-02 00:08:47','2026-01-16 13:15:49'),(164,'Swift 3','portable Ultrabook laptop with nice balance performance and also durable battery life .This laptop for better for student and normal user for there daily work.',269999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/swift-3.png',NULL,9,0,0,0.00,0,1,NULL,'2025-08-27 06:11:13','2026-01-16 13:15:49'),(165,'ThinkPad E14 Gen 2','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',545499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-e14-gen-2.png',NULL,6,0,0,0.00,0,1,'Lenovo','2025-09-25 22:59:11','2026-05-30 07:08:33'),(166,'ThinkPad E14 Gen 3','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',124999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-e14-gen-3.png',NULL,10,0,0,0.00,0,1,'Lenovo','2025-09-25 08:17:20','2026-05-30 07:08:33'),(167,'ThinkPad T14 Gen 3','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',244999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-t14-gen-3.png',NULL,8,0,0,0.00,0,1,'Lenovo','2025-08-23 05:04:44','2026-05-30 07:08:33'),(168,'ThinkPad T14 Gen 6 (AMD)','Lenovo ThinkPad T14 Gen 6 features an AMD Ryzen AI 7 PRO 350, 14-inch WUXGA touchscreen, 32GB RAM, and 1TB SSD. It offers Wi-Fi 7, Bluetooth 5.4, and advanced security features. Weighing about 1.5 kg, itΓÇÖs durable and great for on-the-go productivity.',104999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-t14-gen-6-amd.jpg',NULL,9,0,0,0.00,0,1,'Lenovo','2025-09-29 05:29:47','2026-05-30 07:08:33'),(169,'ThinkPad T14s Gen 6','The Lenovo ThinkPad T14s Gen 6 has an Intel Core Ultra 7 processor, 16GB RAM, and a 512GB SSD. It features a 14-inch 1920x1200 display, Thunderbolt 4, and strong security options. Weighing 1.24 kg, itΓÇÖs ideal for professionals seeking portability and efficiency.',207999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/thinkpad-t14s-gen-6.jpg',NULL,14,0,0,0.00,0,1,'Lenovo','2025-11-09 23:06:47','2026-05-30 07:08:33'),(170,'Travel-Cube Adapter Type-A  Type-C','Brand: unitek, Rated Power Total: 60W Max, Input AC: 100-240V AC (50-60Hz) 1.5A Max, Product Dimension: 77x 68.87 x 28.97 (mm)',6799.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/travel-cube-adapter-type-a-type-c.jpg',NULL,11,0,0,0.00,0,16,NULL,'2025-12-05 23:54:35','2026-01-16 13:15:49'),(171,'TUF Gaming F15','Built-In Microphone: Yes, Front-Facing Camera: Yes Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',319999.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/tuf-gaming-f15.png',NULL,9,0,0,0.00,0,1,'ASUS','2025-09-13 05:51:31','2026-05-30 07:08:33'),(172,'UGreen Portable Wireless Mouse (BlueTooth)','Mode: Wireless -2.4 GHz + Bluetooth, Operating Distance: 10 m/32.8ft, DPI: 1000/1600/2000/4000 DPI, Operating Voltage: 1.5V',1799.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ugreen-portable-wireless-mouse-bluetooth.webp',NULL,8,0,0,0.00,0,13,NULL,'2025-11-12 10:45:27','2026-01-10 15:12:21'),(173,'UGreen USB-C Multifunction Docking Station (5-in-1)','SKU: 10919, Input: 1 x USB-C Male, Output: 2 x USB 3.0 Female, 1 x HDMI Female , 1 x RJ45, USB Standard: USB 3.0 is capable of providing a data transfer speed up to 5Gbps',6149.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/ugreen-usb-c-multifunction-docking-station-5-in-1.webp',NULL,7,0,0,0.00,0,16,NULL,'2025-08-08 07:56:44','2026-01-10 15:11:48'),(174,'WD_BLACK SN750','The WD_BLACK SN750 is a fast PCIe Gen3 NVMe M.2 SSD for gamers and enthusiasts, offering 250GBΓÇô4TB capacities, up to 3,470 MB/s read and 3,100 MB/s write speeds, with an optional heatsink for better cooling, and a 5-year warranty.',2499.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/wd_black-sn750.png',NULL,13,0,0,0.00,0,9,NULL,'2025-09-17 19:42:04','2026-01-16 13:15:49'),(175,'WD Elements External USB HDD','Capacity: 1TB-5TB, Interface: USB 3.0, USB 2.0 Built for reliability and everyday performance with official warranty support available at ByteStore Nepal.',16624.00,NULL,NULL,NULL,NULL,0,'assets/uploads/products/wd-elements-external-usb-hdd.png',NULL,8,0,0,0.00,0,22,NULL,'2025-12-04 07:41:26','2026-05-30 07:08:33');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_image`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `product_image` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`image_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_image_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_image`
--

LOCK TABLES `product_image` WRITE;
/*!40000 ALTER TABLE `product_image` DISABLE KEYS */;
INSERT INTO `product_image` VALUES (1,2,'assets/uploads/products/iphone.jpg',0),(2,6,'assets/uploads/products/macbook_air.jpg',0),(3,18,'assets/uploads/products/asus_rog.jpg',0),(4,1,'assets/uploads/products/laptop1.jpg',0),(5,2,'assets/uploads/products/iphone.jpg',0),(6,6,'assets/uploads/products/macbook_air.jpg',0),(7,18,'assets/uploads/products/asus_rog.jpg',0),(8,19,'assets/uploads/products/dell_monitor.jpg',0);
/*!40000 ALTER TABLE `product_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_review`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `product_review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `review_title` varchar(200) DEFAULT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`review_id`),
  KEY `product_id` (`product_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `review_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `review_product_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_review`
--

LOCK TABLES `product_review` WRITE;
/*!40000 ALTER TABLE `product_review` DISABLE KEYS */;
INSERT INTO `product_review` VALUES (1,2,1,5,'Excellent flagship phone','The iPhone 15 Pro delivers outstanding camera quality and smooth performance. Battery lasts all day with normal use. Highly recommended for photography enthusiasts.','2026-05-30 07:08:33',1),(2,6,2,5,'Perfect for students','MacBook Air M2 is incredibly light and fast. Handles coding, design work, and streaming without any lag. Best laptop purchase I have made.','2026-05-30 07:08:33',1),(3,18,3,4,'Great gaming performance','ROG Strix handles AAA games at high settings. Cooling is effective but fans can get loud under heavy load. Overall excellent gaming laptop.','2026-05-30 07:08:33',1),(4,9,4,5,'Best noise cancellation','Sony WH headphones block out Kathmandu traffic completely. Sound quality is premium and comfort is great for long sessions.','2026-05-30 07:08:33',1),(5,22,5,4,'Fast and portable storage','Transfer speeds are excellent for video editing workflows. Compact enough to carry everywhere. Good value for 1TB capacity.','2026-05-30 07:08:33',1),(6,1,1,5,'Great business laptop','Dell XPS 15 is perfect for my development work. Build quality is excellent and the display is stunning.','2026-05-30 07:09:00',1),(7,7,2,4,'Solid gaming laptop','HP Pavilion gaming handles most titles well. Gets warm under load but performance is good for the price.','2026-05-30 07:09:00',1),(8,4,3,5,'Best mouse I have owned','Logitech quality as expected. Comfortable for long coding sessions.','2026-05-30 07:09:00',1),(9,19,4,4,'Good monitor for the price','144Hz refresh rate makes a noticeable difference in gaming. Colors are accurate enough for daily use.','2026-05-30 07:09:00',1),(10,25,5,5,'Reliable router','WiFi 6 coverage is excellent across my apartment. Setup was straightforward.','2026-05-30 07:09:00',1);
/*!40000 ALTER TABLE `product_review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_spec`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `product_spec` (
  `spec_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `spec_key` varchar(100) NOT NULL,
  `spec_value` varchar(500) NOT NULL,
  PRIMARY KEY (`spec_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_spec_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_spec`
--

LOCK TABLES `product_spec` WRITE;
/*!40000 ALTER TABLE `product_spec` DISABLE KEYS */;
INSERT INTO `product_spec` VALUES (1,2,'Display','6.1-inch Super Retina XDR OLED'),(2,2,'Chip','A17 Pro'),(3,2,'Camera','48MP Main + 12MP Ultra Wide + 12MP Telephoto'),(4,2,'Battery','Up to 23 hours video playback'),(5,2,'Connectivity','5G, Wi-Fi 6E, Bluetooth 5.3'),(6,6,'Display','13.6-inch Liquid Retina (2560 x 1664)'),(7,6,'Chip','Apple M2 (8-core CPU, 10-core GPU)'),(8,6,'Memory','8GB unified memory'),(9,6,'Storage','256GB SSD'),(10,6,'Weight','1.24 kg'),(11,18,'Display','15.6-inch FHD 144Hz IPS'),(12,18,'Processor','Intel Core i7 13th Gen'),(13,18,'Graphics','NVIDIA RTX 4060 8GB'),(14,18,'Memory','16GB DDR5'),(15,18,'Storage','1TB NVMe SSD');
/*!40000 ALTER TABLE `product_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variant`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `variant_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_type` varchar(50) DEFAULT 'Configuration',
  `variant_price` decimal(10,2) NOT NULL,
  `variant_stock` int(11) NOT NULL DEFAULT 0,
  `variant_sku` varchar(100) DEFAULT NULL,
  `variant_image_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`variant_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variant_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variant`
--

LOCK TABLES `product_variant` WRITE;
/*!40000 ALTER TABLE `product_variant` DISABLE KEYS */;
INSERT INTO `product_variant` VALUES (1,2,'iPhone 15 Pro  128GB','Configuration',190000.00,10,'IP15P-128','assets/uploads/products/iphone-128.jpg'),(2,2,'iPhone 15 Pro  256GB','Configuration',210000.00,8,'IP15P-256','assets/uploads/products/iphone-256.jpg'),(3,2,'iPhone 15 Pro  512GB','Configuration',240000.00,5,'IP15P-512','assets/uploads/products/iphone-512.jpg'),(4,3,'Samsung S24  128GB','Configuration',180000.00,12,'SGS24-128','assets/uploads/products/samsung-128.jpg'),(5,3,'Samsung S24  256GB','Configuration',195000.00,10,'SGS24-256','assets/uploads/products/samsung-256.jpg'),(6,6,'MacBook Air M2  8GB / 256GB','Configuration',175000.00,6,'MBA-M2-8-256','assets/uploads/products/macbook_air_8_256.jpg'),(7,6,'MacBook Air M2  16GB / 512GB','Configuration',215000.00,4,'MBA-M2-16-512','assets/uploads/products/macbook_air_16_512.jpg'),(8,13,'iPad 10th Gen  64GB WiΓÇæFi','Configuration',95000.00,10,'IPAD10-64','assets/uploads/products/ipad10-64.jpg'),(9,13,'iPad 10th Gen  256GB WiΓÇæFi','Configuration',125000.00,6,'IPAD10-256','assets/uploads/products/ipad10-256.jpg'),(10,22,'Portable SSD  512GB','Configuration',12000.00,20,'SSD-512','assets/uploads/products/ssd-512.jpg'),(11,22,'Portable SSD  1TB','Configuration',18000.00,40,'SSD-1TB','assets/uploads/products/ssd-1tb.jpg'),(12,22,'Portable SSD  2TB','Configuration',32000.00,12,'SSD-2TB','assets/uploads/products/ssd-2tb.jpg'),(13,153,'Surface Pro 12  8GB / 128GB (WiΓÇæFi)','Configuration',162499.00,5,'SP12-8-128','assets/uploads/products/microsoft-surface-pro-12-128.jpg'),(14,153,'Surface Pro 12  16GB / 256GB','Configuration',192999.00,3,'SP12-16-256','assets/uploads/products/microsoft-surface-pro-12-256.jpg'),(15,153,'Surface Pro 12  32GB / 512GB','Configuration',249999.00,2,'SP12-32-512','assets/uploads/products/microsoft-surface-pro-12-512.jpg'),(16,1,'Dell XPS 15  16GB / 512GB','Configuration',230000.00,4,'DXPS15-16-512','assets/uploads/products/laptop1-16-512.jpg'),(17,1,'Dell XPS 15  32GB / 1TB','Configuration',285000.00,2,'DXPS15-32-1TB','assets/uploads/products/laptop1-32-1tb.jpg'),(18,18,'ROG Strix  Base (16GB / 1TB)','Configuration',245000.00,5,'ROG-BASE-16-1TB','assets/uploads/products/asus_rog_base.jpg'),(19,18,'ROG Strix  Performance (32GB / 1TB)','Configuration',269999.00,3,'ROG-PERF-32-1TB','assets/uploads/products/asus_rog_perf.jpg'),(20,11,'AirPods Pro 2  Standard Case','Configuration',42000.00,25,'APRO2-ST','assets/uploads/products/airpods_standard.jpg'),(21,11,'AirPods Pro 2  MagSafe Case','Configuration',45000.00,15,'APRO2-MAG','assets/uploads/products/airpods_magsafe.jpg'),(22,9,'Sony WH  Black','Configuration',58000.00,12,'SONYWH-BLK','assets/uploads/products/sony_headphone_black.jpg'),(23,9,'Sony WH  Silver','Configuration',58000.00,8,'SONYWH-SLV','assets/uploads/products/sony_headphone_silver.jpg');
/*!40000 ALTER TABLE `product_variant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recently_viewed`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `recently_viewed` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`view_id`),
  UNIQUE KEY `customer_product_view` (`customer_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `recent_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `recent_product_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recently_viewed`
--

LOCK TABLES `recently_viewed` WRITE;
/*!40000 ALTER TABLE `recently_viewed` DISABLE KEYS */;
INSERT INTO `recently_viewed` VALUES (1,1,6,'2026-05-30 07:09:00'),(2,1,18,'2026-05-30 07:09:00'),(3,2,2,'2026-05-30 07:09:00'),(4,2,3,'2026-05-30 07:09:00'),(5,3,7,'2026-05-30 07:09:00'),(6,4,19,'2026-05-30 07:09:00'),(7,5,22,'2026-05-30 07:09:00'),(8,5,71,'2026-05-30 07:15:32'),(9,5,25,'2026-05-30 07:15:48'),(10,5,6,'2026-05-30 07:27:48'),(11,4,6,'2026-05-30 07:36:40'),(12,4,40,'2026-05-30 07:32:53'),(13,4,1,'2026-05-30 07:33:14'),(14,4,2,'2026-05-30 07:33:26'),(15,4,11,'2026-05-30 07:33:37'),(18,4,5,'2026-05-30 07:33:57'),(19,4,4,'2026-05-30 07:34:02'),(20,4,121,'2026-05-30 07:34:05'),(21,4,128,'2026-05-30 07:34:08'),(22,4,153,'2026-05-30 07:34:10'),(24,4,71,'2026-05-30 07:35:15');
/*!40000 ALTER TABLE `recently_viewed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `wishlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `customer_product` (`customer_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_product_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (1,1,18,'2026-05-30 07:08:33'),(2,1,2,'2026-05-30 07:08:33'),(3,2,6,'2026-05-30 07:08:33'),(4,3,9,'2026-05-30 07:08:33'),(5,4,6,'2026-05-30 07:09:00'),(6,5,18,'2026-05-30 07:09:00'),(7,2,19,'2026-05-30 07:09:00'),(8,3,25,'2026-05-30 07:09:00');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket`
--

CREATE TABLE IF NOT EXISTS `support_ticket` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Other',
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Open','Pending','Resolved','Closed') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ticket_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `ticket_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `support_reply`
--

CREATE TABLE IF NOT EXISTS `support_reply` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_type` enum('customer','employee') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reply_id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `reply_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `support_ticket` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- ByteStore Data Enrichment Patch
-- Adds: customers, orders, reviews, support tickets, specs, discounts, stock
-- =============================================================================

SET FOREIGN_KEY_CHECKS=0;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. NEW CUSTOMERS (IDs 8–15) — password = email
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `customer` (`customer_id`,`customer_name`,`customer_password`,`customer_email`,`customer_phone`,`customer_address`,`profile_photo`,`created_at`,`updated_at`) VALUES
(8,  'Bikash Buyer',    '$2b$10$NZOwjw9ul6.lkN8jGOsT0ubhEJ4H3StxnI6MgvMVJqMFtsJ8rUJJu', 'Bikash1@bytestore.com',  '9841100008','Thamel, Kathmandu',NULL,'2025-10-15 09:00:00','2025-10-15 09:00:00'),
(9,  'Deepak Shopper',  '$2b$10$hDyiOYsVMNL6LNNJ6pwp4O4YSFXPCk8oxN4ncu.uVwHs9iLJInQL2', 'Deepak1@bytestore.com',  '9841100009','Pulchowk, Lalitpur',NULL,'2025-11-02 10:30:00','2025-11-02 10:30:00'),
(10, 'Anita Buyer',     '$2b$10$zkW8ohTtSMzsoPh.vAleZuATC2DynHkqxeab/RXKvvRdQmz36VMHa', 'Anita1@bytestore.com',   '9841100010','New Road, Kathmandu',NULL,'2025-11-20 08:15:00','2025-11-20 08:15:00'),
(11, 'Rajan Customer',  '$2b$10$YOJGtVRG0c.axIhlYIDRV.YQLI/AOc1T/ro1asd71WyYZ/q5jOc2.', 'Rajan1@bytestore.com',   '9841100011','Birgunj, Parsa',   NULL,'2025-12-05 11:00:00','2025-12-05 11:00:00'),
(12, 'Priya Shopper',   '$2b$10$tqHILMXKGqzAxpHgcx5Uvuk1VvFhhNGMpxQ7CxW.34lknYcqcVXPi', 'Priya1@bytestore.com',   '9841100012','Chitwan, Bharatpur',NULL,'2025-12-18 14:00:00','2025-12-18 14:00:00'),
(13, 'Amit Buyer',      '$2b$10$Val4/NBD50Z62hknLscPLe.OhbxT20Arwtb4GsZgY6xPr52OfiLP2', 'Amit1@bytestore.com',    '9841100013','Biratnagar, Morang',NULL,'2026-01-10 09:30:00','2026-01-10 09:30:00'),
(14, 'Kabita Customer', '$2b$10$syVskm3Lr6W7OAPYElAY9OOCe62I3zMBxtDBAtkxxK02hohR1Fmcu', 'Kabita1@bytestore.com',  '9841100014','Bhairahawa, Rupandehi',NULL,'2026-02-14 13:00:00','2026-02-14 13:00:00'),
(15, 'Nabin Shopper',   '$2b$10$Qo8u//DWKZTZeEsy29vwk.uGSVgCpSDj85285Nkxpi7E6C7nV03wy', 'Nabin1@bytestore.com',   '9841100015','Hetauda, Makwanpur',NULL,'2026-03-01 10:00:00','2026-03-01 10:00:00');

-- Saved addresses for new customers
INSERT INTO `customer_address` (`customer_id`,`label`,`full_name`,`phone`,`address_line`,`city`,`is_default`) VALUES
(8,  'Home','Bikash Buyer',   '9841100008','Thamel, Kathmandu',   'Kathmandu',1),
(9,  'Home','Deepak Shopper', '9841100009','Pulchowk, Lalitpur',  'Lalitpur', 1),
(10, 'Home','Anita Buyer',    '9841100010','New Road, Kathmandu', 'Kathmandu',1),
(11, 'Home','Rajan Customer', '9841100011','Birgunj, Parsa',      'Birgunj',  1),
(12, 'Home','Priya Shopper',  '9841100012','Bharatpur, Chitwan',  'Bharatpur',1),
(13, 'Home','Amit Buyer',     '9841100013','Biratnagar, Morang',  'Biratnagar',1),
(14, 'Home','Kabita Customer','9841100014','Bhairahawa, Rupandehi','Bhairahawa',1),
(15, 'Home','Nabin Shopper',  '9841100015','Hetauda, Makwanpur',  'Hetauda',  1);

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. PRODUCTS — OUT OF STOCK
-- ─────────────────────────────────────────────────────────────────────────────
UPDATE `product` SET `product_stock`=0 WHERE `product_id` IN (35,55,56,72,73,74,92,93,97,99,100,101,119,128,129,147,148,156,172,174);

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. PRODUCTS — DISCOUNTS WITH SALE DATES
-- ─────────────────────────────────────────────────────────────────────────────
UPDATE `product` SET `original_price`=ROUND(`product_price`/0.85,2), `discount_percent`=15, `is_sale_active`=1,
  `sale_start_date`='2026-05-20 00:00:00', `sale_end_date`='2026-06-20 23:59:59'
WHERE `product_id` IN (4,5,10,11,16,17);

UPDATE `product` SET `original_price`=ROUND(`product_price`/0.80,2), `discount_percent`=20, `is_sale_active`=1,
  `sale_start_date`='2026-05-25 00:00:00', `sale_end_date`='2026-06-10 23:59:59'
WHERE `product_id` IN (9,14,15,20,21,23,24);

UPDATE `product` SET `original_price`=ROUND(`product_price`/0.75,2), `discount_percent`=25, `is_sale_active`=1,
  `sale_start_date`='2026-05-28 00:00:00', `sale_end_date`='2026-06-05 23:59:59'
WHERE `product_id` IN (8,13,27,30,57);

UPDATE `product` SET `original_price`=ROUND(`product_price`/0.70,2), `discount_percent`=30, `is_sale_active`=1,
  `sale_start_date`='2026-05-30 00:00:00', `sale_end_date`='2026-06-02 23:59:59'
WHERE `product_id` IN (28,34,59,77,85,91);

UPDATE `product` SET `original_price`=ROUND(`product_price`/0.90,2), `discount_percent`=10, `is_sale_active`=1,
  `sale_start_date`='2026-05-15 00:00:00', `sale_end_date`='2026-06-30 23:59:59'
WHERE `product_id` IN (33,40,41,42,48,49,50,51,53,54,58,60,61,62,63,64);

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. PRODUCT SPECIFICATIONS (comprehensive)
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `product_spec` (`product_id`,`spec_key`,`spec_value`) VALUES
-- Product 1: Dell XPS 15
(1,'Display','15.6-inch FHD+ InfinityEdge IPS, 500 nits'),(1,'Processor','Intel Core i7-12700H (14-core)'),(1,'RAM','16GB DDR5 4800MHz'),(1,'Storage','512GB M.2 PCIe NVMe SSD'),(1,'GPU','NVIDIA GeForce RTX 3050 Ti 4GB GDDR6'),(1,'Battery','86Wh, up to 13 hours'),(1,'Weight','1.86 kg'),(1,'OS','Windows 11 Home'),
-- Product 3: Samsung Galaxy S24
(3,'Display','6.2-inch Dynamic AMOLED 2X, 2340×1080, 120Hz'),(3,'Chip','Exynos 2400 / Snapdragon 8 Gen 3'),(3,'Camera','50MP Main + 10MP Telephoto + 12MP UltraWide'),(3,'RAM','8GB LPDDR5'),(3,'Storage','128GB / 256GB UFS 3.1'),(3,'Battery','4000mAh, 25W wired'),(3,'OS','Android 14, One UI 6.1'),
-- Product 4: Wireless Mouse
(4,'DPI','800 / 1200 / 1600 (switchable)'),(4,'Battery Life','Up to 18 months (AA battery)'),(4,'Connectivity','2.4GHz Wireless USB receiver'),(4,'Weight','101g'),(4,'Buttons','3 (Left, Right, Middle scroll)'),(4,'Range','Up to 10m'),
-- Product 5: Mechanical Keyboard
(5,'Switch Type','Blue (Clicky), 2mm actuation'),(5,'Backlight','RGB per-key, 16.8M colors'),(5,'Layout','Full Size — 104 keys'),(5,'Connectivity','USB-A Wired, braided cable'),(5,'Actuation Force','50g'),(5,'Anti-ghosting','Full N-Key rollover'),
-- Product 7: HP Gaming Laptop
(7,'Display','15.6-inch FHD IPS, 144Hz, 250 nits'),(7,'CPU','AMD Ryzen 5 5600H (6-core, 4.2GHz boost)'),(7,'GPU','NVIDIA RTX 3050 4GB GDDR6'),(7,'RAM','16GB DDR4 3200MHz'),(7,'Storage','512GB PCIe NVMe SSD'),(7,'Battery','52.5Wh, ~6 hours'),(7,'Weight','2.25 kg'),
-- Product 8: Lenovo ThinkPad E14
(8,'Display','14-inch FHD IPS, 300 nits, anti-glare'),(8,'CPU','Intel Core i5-1235U (12th Gen, 10-core)'),(8,'RAM','16GB DDR4 3200MHz'),(8,'Storage','512GB NVMe SSD'),(8,'Battery','45Wh, up to 11.5 hours'),(8,'Weight','1.69 kg'),(8,'Security','Fingerprint reader, TPM 2.0'),
-- Product 9: Sony WH Headphones
(9,'Driver Size','40mm dome'),(9,'Frequency Response','4Hz–40,000Hz'),(9,'Battery Life','30 hours ANC on, 38 hours ANC off'),(9,'Charging','USB-C, 10 min = 5 hours playback'),(9,'Bluetooth','5.2, LDAC / AAC / SBC'),(9,'NFC','Yes'),(9,'Weight','254g'),
-- Product 10: JBL Charge 5
(10,'Output Power','40W RMS'),(10,'Frequency Response','65Hz–20kHz'),(10,'Battery Life','20 hours'),(10,'Charging','USB-C, also charges devices (USB-A)'),(10,'Waterproofing','IP67 dust & waterproof'),(10,'Connectivity','Bluetooth 5.1, PartyBoost'),
-- Product 11: AirPods Pro 2
(11,'Chip','H2'),(11,'ANC','Adaptive Transparency + Personalised Spatial Audio'),(11,'Battery','6h (ANC on) + 24h case'),(11,'Connectivity','Bluetooth 5.3'),(11,'Charging','MagSafe / Lightning / USB-C case'),(11,'Water Resistance','IPX4'),
-- Product 13: iPad 10th Gen
(13,'Display','10.9-inch Liquid Retina, 2360×1640, 264ppi'),(13,'Chip','Apple A14 Bionic'),(13,'Camera','12MP Rear, 12MP Ultra-wide Front'),(13,'Storage','64GB / 256GB'),(13,'Battery','28.65Wh, up to 10 hours'),(13,'Connectivity','Wi-Fi 6, optional 5G'),(13,'Weight','477g'),
-- Product 14: Apple Watch Series 9
(14,'Display','Always-On Retina LTPO OLED, 41/45mm'),(14,'Chip','S9 SiP dual-core'),(14,'Battery','18 hours, 36 hours Low Power Mode'),(14,'Health','Blood oxygen, ECG, crash detection, temperature'),(14,'Water Resistance','WR50 swim-proof'),(14,'GPS','L1 GNSS + Dual-freq GPS'),
-- Product 15: Samsung Watch 6
(15,'Display','1.5-inch Super AMOLED, 480×480'),(15,'Chip','Exynos W930 dual-core 1.4GHz'),(15,'Battery','300mAh, ~40 hours'),(15,'Health','BioActive sensor, ECG, blood pressure'),(15,'OS','Wear OS 4 + One UI Watch 5'),(15,'Water Resistance','5ATM + IP68'),
-- Product 16: Logitech G502
(16,'DPI','100–25,600 adjustable'),(16,'Sensor','HERO 25K'),(16,'Buttons','11 programmable'),(16,'Weight','121g (adjustable with weights)'),(16,'Polling Rate','1000Hz'),(16,'Cable','2.1m braided'),
-- Product 17: Razer BlackWidow V3
(17,'Switch','Razer Green (Clicky), 50g actuation'),(17,'Backlight','Razer Chroma RGB per-key'),(17,'Layout','Full Size 104-key'),(17,'Connectivity','USB-A wired, braided cable'),(17,'Anti-ghosting','Full N-Key rollover'),(17,'Multimedia Keys','Dedicated, with volume dial'),
-- Product 19: Dell Monitor 27"
(19,'Resolution','1920×1080 FHD IPS'),(19,'Refresh Rate','144Hz'),(19,'Response Time','1ms GtG'),(19,'Panel','IPS, 99% sRGB'),(19,'HDR','DisplayHDR 400'),(19,'Ports','HDMI 2.0, DisplayPort 1.4, 4× USB-A 3.2'),(19,'VESA','100×100mm'),
-- Product 20: Canon EOS 250D
(20,'Sensor','24.1MP APS-C CMOS'),(20,'Processor','DIGIC 8'),(20,'ISO','100–25600 (expandable to 51200)'),(20,'Video','Full HD 1080p / 4K time-lapse'),(20,'AF Points','9-point all cross-type'),(20,'Battery','LP-E17, ~1070 shots'),(20,'Weight','449g with battery'),
-- Product 21: GoPro Hero 12
(21,'Video','5.3K60, 4K120, 2.7K240, 1080p'),(21,'Stabilization','HyperSmooth 6.0'),(21,'Battery','Enduro, 70 min 5.3K60'),(21,'Waterproofing','10m without housing'),(21,'Display','2.27-inch rear touch, 1.4-inch front'),(21,'HDR','Photo and video HDR'),
-- Product 22: Portable SSD 1TB
(22,'Interface','USB 3.2 Gen 2 / USB-C'),(22,'Read Speed','Up to 1050 MB/s'),(22,'Write Speed','Up to 1000 MB/s'),(22,'Dimensions','69.54 × 38.57 × 8.94 mm'),(22,'Weight','39g'),(22,'Encryption','256-bit AES hardware'),
-- Product 23: SanDisk 128GB Pendrive
(23,'Interface','USB 3.2 Gen 1 (USB-A) + Micro-USB'),(23,'Read Speed','Up to 150 MB/s'),(23,'Write Speed','Up to 60 MB/s'),(23,'Capacity','128GB'),(23,'Dimensions','58.3 × 21.9 × 8.6mm'),
-- Product 24: Gaming Chair
(24,'Material','PU Leather + Cold-foam padding'),(24,'Adjustment','Height, armrest 4D, recline 90–170°'),(24,'Max Load','150kg'),(24,'Lumbar','Adjustable lumbar + headrest pillow'),(24,'RGB','LED strip, USB-powered'),
-- Product 25: TP-Link WiFi 6 Router
(25,'Standard','Wi-Fi 6 (802.11ax) Dual-Band'),(25,'Speed','AX3000: 574Mbps (2.4GHz) + 2402Mbps (5GHz)'),(25,'Coverage','Up to 230 m²'),(25,'Ports','1× WAN + 4× LAN Gigabit'),(25,'Antennas','4 external high-gain'),(25,'Security','WPA3, VPN server, SPI Firewall'),
-- Product 26: Acer Monitor
(26,'Resolution','1366×768 HD'),(26,'Panel','IPS LED Backlit'),(26,'Size','19.5 inches'),(26,'Refresh Rate','60Hz'),(26,'Ports','VGA, HDMI'),
-- Product 57: Asus ProArt Display
(57,'Resolution','2560×1440 QHD'),(57,'Panel','IPS, 99% DCI-P3'),(57,'Size','27 inches'),(57,'Refresh Rate','75Hz'),(57,'Calibration','Factory pre-calibrated, ΔE<2'),(57,'Ports','USB-C (90W PD), HDMI, DisplayPort'),
-- Product 94: RTX 4080
(94,'CUDA Cores','9728'),(94,'VRAM','16GB GDDR6X'),(94,'Boost Clock','2565 MHz'),(94,'TDP','320W'),(94,'Outputs','3× DisplayPort 1.4, 1× HDMI 2.1'),(94,'Memory Bandwidth','716.8 GB/s'),
-- Product 95: RTX 4090
(95,'CUDA Cores','16384'),(95,'VRAM','24GB GDDR6X'),(95,'Boost Clock','2520 MHz'),(95,'TDP','450W'),(95,'Outputs','3× DisplayPort 1.4a, 1× HDMI 2.1a'),(95,'Memory Bandwidth','1008 GB/s'),
-- Product 126: Intel i5-13600K
(126,'Cores','14 (6P + 8E)'),(126,'Threads','20'),(126,'Base Clock','3.5GHz (P-core)'),(126,'Boost Clock','5.1GHz'),(126,'TDP','125W'),(126,'Cache','24MB L3'),(126,'Socket','LGA1700'),
-- Product 127: Intel i7-13700K
(127,'Cores','16 (8P + 8E)'),(127,'Threads','24'),(127,'Base Clock','3.4GHz (P-core)'),(127,'Boost Clock','5.4GHz'),(127,'TDP','125W'),(127,'Cache','30MB L3'),(127,'Socket','LGA1700'),
-- Product 153: Surface Pro 12
(153,'Display','12-inch PixelSense Flow, 2880×1920, 120Hz'),(153,'CPU','Snapdragon X Elite / Plus'),(153,'RAM','16GB / 32GB LPDDR5x'),(153,'Storage','128GB–512GB SSD'),(153,'Battery','Up to 14 hours'),(153,'Connectivity','Wi-Fi 7, Bluetooth 5.4, optional LTE/5G'),(153,'Weight','895g tablet only'),
-- Product 161: Samsung T7 Shield
(161,'Interface','USB 3.2 Gen 2'),(161,'Read Speed','Up to 1050 MB/s'),(161,'Write Speed','Up to 1000 MB/s'),(161,'Durability','MIL-STD-810G, IP65 water & dust'),(161,'Encryption','AES 256-bit'),(161,'Warranty','3 years'),
-- Product 162: SanDisk Extreme SSD
(162,'Interface','USB 3.2 Gen 2 (USB-C)'),(162,'Read Speed','Up to 1050 MB/s'),(162,'Write Speed','Up to 1000 MB/s'),(162,'Waterproofing','IP55'),(162,'Capacity','500GB / 1TB / 2TB'),
-- Product 41: MacBook Air 16GB 256GB
(41,'Display','13.6-inch Liquid Retina, 2560×1664, 500 nits'),(41,'Chip','Apple M2 (8-core CPU, 10-core GPU)'),(41,'RAM','16GB unified memory'),(41,'Storage','256GB SSD'),(41,'Battery','Up to 18 hours'),(41,'Weight','1.24 kg'),(41,'MagSafe','Yes, USB-C charging also supported'),
-- Product 43: MacBook Pro 16" M2 Max
(43,'Display','16.2-inch Liquid Retina XDR, 3456×2234, 1600 nits peak'),(43,'Chip','Apple M2 Max (12-core CPU, 38-core GPU)'),(43,'RAM','32GB unified memory'),(43,'Storage','1TB SSD'),(43,'Battery','Up to 22 hours'),(43,'Weight','2.15 kg'),(43,'Ports','3× Thunderbolt 4, HDMI, SD card, MagSafe 3'),
-- Product 58: ROG Zephyrus G14
(58,'Display','14-inch WQXGA 2560×1600 165Hz IPS'),(58,'CPU','AMD Ryzen 9 7940HS'),(58,'GPU','NVIDIA RTX 4060 8GB'),(58,'RAM','16GB DDR5'),(58,'Storage','1TB PCIe 4.0 NVMe'),(58,'Battery','73Wh, ~10 hours'),(58,'Weight','1.65 kg');

-- ─────────────────────────────────────────────────────────────────────────────
-- 5. ORDERS — spread across 12 months (IDs 12–55)
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `orders` (`order_id`,`customer_id`,`order_date`,`total_amount`,`order_status`,`payment_status`,`shipping_address`,`customer_phone`) VALUES
-- June 2025
(12,8, '2025-06-05 10:30:00',175000.00,'Delivered','Paid',  'Thamel, Kathmandu',       '9841100008'),
(13,3, '2025-06-12 14:00:00', 42000.00,'Delivered','Paid',  'Butwal, Nepal',            '9803563453'),
(14,2, '2025-06-20 09:15:00', 58000.00,'Delivered','Paid',  'Kalanki, Nepal',           '9803563452'),
-- July 2025
(15,1, '2025-07-03 11:00:00',245000.00,'Delivered','Paid',  'Bhaktapur, Nepal',         '9803563451'),
(16,4, '2025-07-14 16:30:00', 95000.00,'Delivered','Paid',  'Lalitpur, Nepal',          '9803563454'),
(17,5, '2025-07-22 08:45:00', 21000.00,'Delivered','Paid',  'Pokhara, Nepal',           '9803563455'),
(18,9, '2025-07-28 13:00:00', 18000.00,'Delivered','Paid',  'Pulchowk, Lalitpur',       '9841100009'),
-- August 2025
(19,6, '2025-08-04 10:00:00',190000.00,'Delivered','Paid',  'Dharan, Nepal',            '9803563456'),
(20,8, '2025-08-10 15:30:00', 78000.00,'Delivered','Paid',  'Thamel, Kathmandu',        '9841100008'),
(21,2, '2025-08-18 09:00:00', 24000.00,'Delivered','Paid',  'Kalanki, Nepal',           '9803563452'),
(22,10,'2025-08-25 12:00:00',120000.00,'Delivered','Paid',  'New Road, Kathmandu',      '9841100010'),
-- September 2025
(23,1, '2025-09-02 11:30:00', 38000.00,'Delivered','Paid',  'Bhaktapur, Nepal',         '9803563451'),
(24,3, '2025-09-09 14:00:00',155000.00,'Delivered','Paid',  'Butwal, Nepal',            '9803563453'),
(25,7, '2025-09-15 10:30:00', 55000.00,'Delivered','Paid',  'Example, Nepal',           '9803563457'),
(26,11,'2025-09-22 09:00:00', 45000.00,'Delivered','Paid',  'Birgunj, Parsa',           '9841100011'),
-- October 2025
(27,4, '2025-10-05 13:00:00',230000.00,'Delivered','Paid',  'Lalitpur, Nepal',          '9803563454'),
(28,9, '2025-10-12 15:00:00', 65000.00,'Delivered','Paid',  'Pulchowk, Lalitpur',       '9841100009'),
(29,5, '2025-10-20 10:00:00',175000.00,'Delivered','Paid',  'Pokhara, Nepal',           '9803563455'),
(30,12,'2025-10-27 11:30:00', 18000.00,'Delivered','Paid',  'Bharatpur, Chitwan',       '9841100012'),
-- November 2025
(31,6, '2025-11-03 09:00:00', 42000.00,'Delivered','Paid',  'Dharan, Nepal',            '9803563456'),
(32,1, '2025-11-10 14:30:00',190000.00,'Delivered','Paid',  'Bhaktapur, Nepal',         '9803563451'),
(33,10,'2025-11-18 11:00:00', 58000.00,'Delivered','Paid',  'New Road, Kathmandu',      '9841100010'),
(34,8, '2025-11-25 16:00:00', 95000.00,'Delivered','Paid',  'Thamel, Kathmandu',        '9841100008'),
-- December 2025
(35,2, '2025-12-02 10:00:00',245000.00,'Delivered','Paid',  'Kalanki, Nepal',           '9803563452'),
(36,11,'2025-12-08 13:30:00', 21000.00,'Delivered','Paid',  'Birgunj, Parsa',           '9841100011'),
(37,3, '2025-12-15 09:30:00', 78000.00,'Delivered','Paid',  'Butwal, Nepal',            '9803563453'),
(38,7, '2025-12-22 11:00:00',120000.00,'Delivered','Paid',  'Example, Nepal',           '9803563457'),
(39,12,'2025-12-28 14:00:00', 38000.00,'Delivered','Paid',  'Bharatpur, Chitwan',       '9841100012'),
-- January 2026
(40,4, '2026-01-05 10:30:00', 55000.00,'Delivered','Paid',  'Lalitpur, Nepal',          '9803563454'),
(41,13,'2026-01-12 09:00:00',175000.00,'Delivered','Paid',  'Biratnagar, Morang',       '9841100013'),
(42,5, '2026-01-20 15:00:00', 65000.00,'Delivered','Paid',  'Pokhara, Nepal',           '9803563455'),
(43,9, '2026-01-27 11:00:00', 24000.00,'Delivered','Paid',  'Pulchowk, Lalitpur',       '9841100009'),
-- February 2026
(44,6, '2026-02-03 10:00:00', 42000.00,'Delivered','Paid',  'Dharan, Nepal',            '9803563456'),
(45,14,'2026-02-10 14:00:00',120000.00,'Delivered','Paid',  'Bhairahawa, Rupandehi',    '9841100014'),
(46,1, '2026-02-17 09:30:00', 95000.00,'Delivered','Paid',  'Bhaktapur, Nepal',         '9803563451'),
(47,10,'2026-02-24 13:00:00',190000.00,'Shipped',  'Paid',  'New Road, Kathmandu',      '9841100010'),
-- March 2026
(48,2, '2026-03-04 11:00:00',230000.00,'Shipped',  'Paid',  'Kalanki, Nepal',           '9803563452'),
(49,15,'2026-03-11 15:30:00', 58000.00,'Shipped',  'Paid',  'Hetauda, Makwanpur',       '9841100015'),
(50,8, '2026-03-18 10:00:00', 78000.00,'Shipped',  'Paid',  'Thamel, Kathmandu',        '9841100008'),
(51,13,'2026-03-25 09:00:00',245000.00,'Shipped',  'Paid',  'Biratnagar, Morang',       '9841100013'),
-- April 2026
(52,3, '2026-04-02 13:00:00', 21000.00,'Processing','Pending','Butwal, Nepal',          '9803563453'),
(53,14,'2026-04-09 10:30:00',175000.00,'Processing','Pending','Bhairahawa, Rupandehi',  '9841100014'),
(54,5, '2026-04-18 14:00:00', 45000.00,'Processing','Pending','Pokhara, Nepal',         '9803563455'),
(55,11,'2026-04-25 11:00:00', 38000.00,'Pending',  'Pending','Birgunj, Parsa',          '9841100011'),
-- May 2026
(56,15,'2026-05-03 09:30:00', 65000.00,'Pending',  'Pending','Hetauda, Makwanpur',      '9841100015'),
(57,12,'2026-05-10 14:00:00',120000.00,'Pending',  'Pending','Bharatpur, Chitwan',      '9841100012'),
(58,7, '2026-05-18 10:00:00', 55000.00,'Pending',  'Pending','Example, Nepal',          '9803563457'),
(59,1, '2026-05-25 15:30:00',190000.00,'Pending',  'Pending','Bhaktapur, Nepal',        '9803563451'),
-- Cancelled orders
(60,6, '2025-08-30 10:00:00',245000.00,'Cancelled','Refunded','Dharan, Nepal',          '9803563456'),
(61,9, '2025-11-15 13:00:00', 78000.00,'Cancelled','Refunded','Pulchowk, Lalitpur',     '9841100009'),
(62,4, '2026-02-01 11:00:00',120000.00,'Cancelled','Pending', 'Lalitpur, Nepal',        '9803563454');

-- ─────────────────────────────────────────────────────────────────────────────
-- 6. ORDER ITEMS (2-3 per order, top-selling products repeated)
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `order_items` (`order_id`,`product_id`,`variant_id`,`quantity`,`price`) VALUES
(12,6,6,1,175000.00),
(13,11,20,1,42000.00),
(14,9,22,1,58000.00),
(15,18,18,1,245000.00),
(16,13,8,1,95000.00),
(17,17,NULL,1,21000.00),
(18,22,11,1,18000.00),
(19,2,1,1,190000.00),
(20,14,NULL,1,78000.00),
(21,10,NULL,1,24000.00),
(22,12,NULL,1,120000.00),
(23,16,NULL,2,19000.00),(23,23,NULL,1,2200.00),(23,23,NULL,2,4400.00),
(24,7,NULL,1,155000.00),
(25,15,NULL,1,55000.00),
(26,19,NULL,1,45000.00),
(27,1,16,1,230000.00),
(28,9,22,1,58000.00),(28,10,NULL,1,24000.00),
(29,6,7,1,175000.00),
(30,22,10,1,12000.00),(30,23,NULL,2,4400.00),
(31,11,21,1,42000.00),
(32,2,2,1,190000.00),
(33,9,23,1,58000.00),
(34,13,9,1,125000.00),
(35,18,19,1,245000.00),
(36,5,NULL,1,4990.00),(36,4,NULL,2,5998.00),(36,23,NULL,1,2200.00),
(37,14,NULL,1,78000.00),
(38,12,NULL,1,120000.00),
(39,16,NULL,1,9500.00),(39,17,NULL,1,21000.00),
(40,22,11,2,36000.00),
(41,6,6,1,175000.00),
(42,9,22,1,58000.00),(42,4,NULL,1,2999.00),
(43,10,NULL,1,24000.00),
(44,11,20,1,42000.00),
(45,12,NULL,1,120000.00),
(46,13,9,1,125000.00),
(47,2,3,1,190000.00),
(48,1,17,1,230000.00),
(49,9,23,1,58000.00),
(50,14,NULL,1,78000.00),
(51,18,18,1,245000.00),
(52,5,NULL,1,4990.00),(52,4,NULL,1,2999.00),(52,23,NULL,3,6600.00),
(53,6,7,1,175000.00),
(54,22,10,1,12000.00),(54,22,11,1,18000.00),
(55,16,NULL,1,9500.00),(55,19,NULL,1,45000.00),
(56,9,22,1,58000.00),(56,4,NULL,1,2999.00),
(57,12,NULL,1,120000.00),
(58,11,21,1,45000.00),(58,10,NULL,1,24000.00),
(59,2,1,1,190000.00),
(60,18,19,1,245000.00),
(61,14,NULL,1,78000.00),
(62,12,NULL,1,120000.00);

-- ─────────────────────────────────────────────────────────────────────────────
-- 7. PRODUCT REVIEWS (60 new reviews, diverse products and customers)
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `product_review` (`product_id`,`customer_id`,`rating`,`review_title`,`review_text`,`is_verified`) VALUES
(6, 8,5,'Incredibly lightweight','MacBook Air M2 is a dream for daily use. Battery lasts two full workdays. Best laptop I have ever owned.',1),
(18,9,5,'Gaming beast','ROG Strix handles everything I throw at it — GTA VI, Cyberpunk 2077 maxed out. Thermals stay manageable.',1),
(2,10,4,'Great phone, pricey','iPhone 15 Pro camera is stunning. ProRes video is a game-changer for content creation. Price is steep but worth it.',1),
(9,11,5,'Dead-silent commute','Sony WH-1000XM5 noise cancellation makes Kathmandu traffic disappear. Call quality is crystal clear.',1),
(1,12,5,'Developer dream laptop','Running multiple Docker containers with zero slowdown. InfinityEdge display is gorgeous for long coding sessions.',1),
(22,13,4,'Blazing fast SSD','Transferring 100GB of footage in under 2 minutes. Compact enough to fit in any bag.',1),
(11,14,5,'AirPods Pro worth every paisa','Personalized spatial audio is mind-blowing. Fit is comfortable for 4+ hour sessions.',1),
(7, 15,3,'Decent for the price','Good gaming performance but runs hot after 2 hours. Fan noise is noticeable. Acceptable for budget gaming.',1),
(13,8, 5,'Perfect student tablet','iPad 10th Gen handles Notability, Zoom, and streaming effortlessly. The landscape camera is a nice upgrade.',1),
(19,9, 5,'Monitor upgrade complete','144Hz makes such a difference. Colors are accurate right out of the box. Dell build quality as expected.',1),
(14,10,4,'Love the health features','ECG and crash detection give real peace of mind. Battery life could be better but overnight charging works.',1),
(25,11,5,'WiFi 6 is a revelation','Upgraded from an old AC router. Download speeds tripled. Coverage reaches every corner of my house.',1),
(6,13,5,'M2 chip is insane','Running Final Cut Pro on MacBook Air M2 with no thermal throttling. Fanless design is a huge plus.',1),
(18,14,4,'Excellent gaming laptop','ROG Strix thermal performance is impressive. Display refresh rate is buttery smooth for FPS games.',1),
(2,15,5,'Best Android alternative','Switched from Android. Camera system is leagues ahead. Face ID is reliable even in dim light.',1),
(16,8, 4,'Precision gaming mouse','HERO 25K sensor tracks perfectly on any surface. Weight adjustment system is a nice touch.',1),
(17,9, 5,'Keyboard of my dreams','Razer Green switches have perfect feedback. RGB Chroma syncs beautifully with other Razer gear.',1),
(4, 10,5,'Replaced my wired mouse','Battery has lasted 9 months without changing. Scroll wheel is smooth and click feedback is satisfying.',1),
(5, 11,4,'Solid mechanical keyboard','Blue switches are tactile and loud — perfect for my home office. Build quality feels premium.',1),
(12,12,5,'Samsung tablet is stunning','AMOLED display makes Netflix look like a cinema. S Pen integration is seamless for note-taking.',1),
(10,13,4,'JBL bass is real','Charge 5 fills my apartment with sound. IP67 means I can use it in the shower without worry.',1),
(15,14,5,'Samsung Watch 6 is sleek','Health tracking is accurate. Blood pressure monitoring is a feature I use daily.',1),
(20,15,4,'Great entry DSLR','Canon EOS 250D produces professional-looking photos. Kit lens is surprisingly capable.',1),
(21,8, 5,'GoPro Hero 12 exceeded expectations','HyperSmooth 6 makes handheld footage look gimbal-smooth. Waterproofing works great in rain.',1),
(24,9, 3,'Chair looks better than it feels','RGB lighting is cool but lumbar support could be firmer after long sessions. Assembly instructions were unclear.',0),
(23,10,5,'Fast USB drive','Transfers large files quickly. Micro USB is handy for phone connections. Great value.',1),
(19,11,4,'Dell monitor solid choice','IPS panel colors are accurate. Stand could use height adjustment but VESA mount works.',1),
(1, 13,4,'XPS 15 worth the investment','Build quality is exceptional. Keyboard feels premium. Gets a bit warm under heavy loads but nothing alarming.',1),
(6, 14,5,'MacBook Air M2 — flawless','Running Xcode, Figma, and Slack simultaneously with zero lag. Best purchase of 2025.',1),
(18,15,5,'ROG Strix — no compromises','Gaming at 144fps consistently. Build quality is solid. Keyboard RGB customization is excellent.',1),
(9,  8,4,'Sony WH comfort','Ear cups are plush for extended use. LDAC codec makes Spotify sound like lossless. Slight pressure on glasses.',1),
(2, 12,3,'iPhone 15 Pro — mixed feelings','Camera is outstanding but battery drains faster than my previous phone. Titanium frame does feel premium.',1),
(22,9, 5,'SSD changed my workflow','No more waiting for file transfers. Fits in my shirt pocket. Highly recommend for video editors.',1),
(13,11,4,'iPad great for kids too','My children use it for school. Parental controls are easy to set up. Durable build survived a few drops.',1),
(7, 12,4,'HP gaming laptop improved','Ryzen 5 performance is solid. 144Hz display is smooth. Thermal paste reapplication improved temps significantly.',1),
(11,13,5,'AirPods Pro 2 transparency mode','Transparency mode sounds more natural than wearing no headphones. Personalized spatial audio is a genuine innovation.',1),
(25,14,4,'TP-Link router easy setup','Set up in under 10 minutes. Tenda and Huawei routers in the same building now have no interference.',1),
(14,15,5,'Apple Watch Series 9 daily driver','Crash detection triggered once when I dropped my phone — smartwatch caught it. Reassuring peace of mind.',1),
(16,3, 5,'G502 is the best gaming mouse','HERO sensor is incredibly accurate for FPS games. The extra buttons are well-placed.',1),
(17,2, 4,'BlackWidow V3 clicky goodness','Green switches are satisfying but loud for open offices. RGB is stunning. Build is tank-like.',1),
(20,1, 5,'Canon 250D perfect for vlogging','Flip screen is essential for self-recording. 4K time-lapse is a hidden gem feature.',1),
(21,4, 4,'GoPro for action sports','Used it rafting in Trishuli River — footage is stunning. Battery drains fast at 5.3K.',1),
(12,5, 5,'Samsung Tab S9 best tablet','DeX mode turns it into a mini desktop. AMOLED colors are unreal for graphic design work.',1),
(10,6, 5,'JBL Charge 5 — always packed','Cannot travel without it now. PartyBoost with another JBL makes parties legendary.',1),
(15,7, 4,'Samsung Watch accurate health','Sleep tracking is detailed. SpO2 readings match my oximeter. Band feels comfortable all night.',1),
(23,1, 5,'Tiny but fast','SanDisk never disappoints. Used for OS installs and file sharing. USB 3.2 speeds are noticeably faster.',1),
(24,2, 4,'Gaming chair comfortable','Sat in it for 8-hour sessions without back pain. Assembly took 45 minutes but the manual was clear enough.',1),
(8, 3, 5,'ThinkPad reliability unmatched','Dropped it twice — still running perfectly. Keyboard is the best I have typed on in any laptop.',1),
(5, 4, 5,'Mechanical keyboard convert','Went from membrane to this. Cannot imagine going back. Typing speed increased noticeably.',1),
(4, 5, 4,'Simple reliable mouse','Does exactly what it should. No software needed. Battery life is outstanding.',1);

-- Update rating aggregates for all reviewed products
UPDATE `product` p SET
  `rating_avg`  = (SELECT ROUND(AVG(r.rating),2) FROM product_review r WHERE r.product_id = p.product_id),
  `rating_count`= (SELECT COUNT(*) FROM product_review r WHERE r.product_id = p.product_id)
WHERE product_id IN (SELECT DISTINCT product_id FROM product_review);

-- ─────────────────────────────────────────────────────────────────────────────
-- 8. SUPPORT TICKETS & REPLIES
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `support_ticket` (`ticket_id`,`customer_id`,`category`,`subject`,`message`,`status`) VALUES
(1, 1,'Order Issue',      'Order #2 not delivered yet',               'My order placed on 8th January has not arrived. Tracking shows it left the warehouse 5 days ago. Please update.',        'Resolved'),
(2, 2,'Returns & Refunds','Received damaged product',                 'The laptop screen has a dead pixel cluster. I would like to request a replacement under warranty.',                       'Resolved'),
(3, 3,'Technical Support','MacBook Air M2 not charging',              'My MacBook Air M2 stopped recognising the MagSafe cable after the latest macOS update. Tried two cables. Same issue.',   'Resolved'),
(4, 4,'Billing',          'Charged twice for order #1',               'My eSewa account was debited twice for NPR 420,000. Transaction IDs attached. Please refund the duplicate charge.',      'Resolved'),
(5, 5,'General',          'When will ROG Strix restock?',             'The ROG Strix Gaming Laptop shows out of stock. Do you have a restock date? I would like to be notified.',             'Closed'),
(6, 6,'Order Issue',      'Wrong item received in order #6',          'I ordered Apple MacBook Air M2 but received HP Gaming Laptop instead. Please arrange correct item delivery.',           'Resolved'),
(7, 7,'Technical Support','Wireless mouse not pairing',               'New wireless mouse won''t pair with my PC. USB receiver inserted, tried on multiple ports. Battery is fresh.',          'Resolved'),
(8, 8,'Returns & Refunds','iPhone 15 Pro battery draining fast',      'Battery drops 30% in 2 hours of light use. Purchased 3 weeks ago. Would like battery replacement or exchange.',        'Pending'),
(9, 9,'Order Issue',      'Shipment stuck in Kathmandu hub',          'Order #18 has been showing in transit for 8 days at Kathmandu sorting hub. No movement since.',                       'Resolved'),
(10,10,'General',         'Do you offer EMI payment options?',        'I want to buy the MacBook Pro 16. Does ByteStore offer EMI through any banks or fintech partners?',                    'Closed'),
(11,11,'Billing',         'GST invoice not received',                 'I need a formal VAT invoice for order #26 for company expense reimbursement. Please send to my email.',                 'Resolved'),
(12,12,'Technical Support','Samsung Galaxy Tab S9 touchscreen glitch','Screen registers phantom touches in the top-left corner. Factory reset did not fix it.',                              'Open'),
(13,13,'Order Issue',     'Estimated delivery date incorrect',        'App shows delivery by 15th Jan but courier says 20th Jan. Which is accurate? Need to arrange someone to receive it.', 'Resolved'),
(14,14,'Returns & Refunds','AirPods Pro 2 right earbud not working', 'Right earbud produces no sound. Left works fine. Tried cleaning mesh, resetting. Still broken.',                      'Pending'),
(15,15,'General',         'Do you ship to Hetauda?',                  'Your checkout page does not show Hetauda in the delivery city options. Can you deliver here?',                         'Resolved'),
(16,1, 'Technical Support','Dell XPS 15 overheating while gaming',   'CPU hits 95°C during gaming. Fans spin at max. Room temperature is normal. Should I re-paste the CPU?',              'Open'),
(17,2, 'Order Issue',     'Order #35 tracking not updating',         'Tracking number provided shows no movement for 3 days. Is the courier service experiencing delays?',                   'Pending'),
(18,3, 'General',         'Can I reserve a product before launch?',   'I heard there is a new Lenovo Legion coming. Can I pre-order or reserve one at ByteStore?',                          'Closed'),
(19,4, 'Returns & Refunds','Gaming Chair delivered with broken armrest','One armrest attachment point was cracked on arrival. Box appeared undamaged. Need replacement part.',              'Resolved'),
(20,5, 'Billing',         'Promo code not applied',                   'Used code BYTE20 at checkout but discount was not deducted. Paid full price. Order #29 total is wrong.',             'Resolved'),
(21,6, 'Technical Support','Router dropping WiFi connection',         'TP-Link WiFi 6 router disconnects all devices every few hours. Firmware is latest version. Reset does not help.',   'Open'),
(22,7, 'Order Issue',     'Delivery attempted but I was not home',   'Courier marked delivery failed but I was home all day. No notification received. Need reschedule.',                   'Resolved'),
(23,8, 'Returns & Refunds','MacBook Air M2 wrong color received',     'Ordered Midnight but received Starlight. Would like correct color replacement without paying restocking fee.',       'Pending'),
(24,9, 'Technical Support','Sony headphones Bluetooth range poor',    'Only works within 3 meters. Bluetooth specs say 10 meters. Obstacles are minimal in my apartment.',                  'Open'),
(25,10,'General',         'Is there a ByteStore loyalty program?',    'I have purchased over NPR 5,00,000 worth of products. Do you have a loyalty or rewards program I can enroll in?',  'Closed');

INSERT INTO `support_reply` (`ticket_id`,`sender_type`,`sender_id`,`message`) VALUES
-- Ticket 1
(1,'employee',1,'Hello Akraj, we apologise for the delay. We have escalated your order to our courier partner. Expected delivery is within 2 business days. You will receive an SMS update.'),
(1,'customer',1,'Thank you. Received the order this morning. Appreciate the quick resolution.'),
(1,'employee',1,'Glad to hear it! We have noted the delay for internal review. Thank you for your patience.'),
-- Ticket 2
(2,'employee',1,'Hi Arpan, we are very sorry about the damaged screen. Please share photos of the defect to admin@bytestore.com and we will arrange a replacement within 3-5 business days.'),
(2,'customer',2,'Photos sent. Thank you for the prompt response.'),
(2,'employee',2,'Replacement confirmed. New unit dispatched. Old unit pick-up scheduled for tomorrow.'),
-- Ticket 3
(3,'employee',1,'Hi Pramisha, this is a known issue with macOS Ventura 13.6. Please try resetting the SMC: shut down, hold Ctrl+Option+Shift+Power for 10 seconds. Let us know if this resolves it.'),
(3,'customer',3,'SMC reset worked! Charging again. Thank you so much.'),
-- Ticket 4
(4,'employee',2,'Hello Sujit, we have verified the double deduction with our payment gateway. A refund of NPR 420,000 has been initiated. It will reflect in your eSewa wallet within 3-5 working days.'),
(4,'customer',4,'Received the refund. Thank you for resolving this quickly.'),
-- Ticket 5
(5,'employee',1,'Hi Sajal, the ROG Strix is expected to be back in stock within 2-3 weeks. We have added you to the stock notification list. You will get an email the moment it is available.'),
-- Ticket 6
(6,'employee',2,'Hi Rohan, we sincerely apologise for the mix-up. Correct item dispatched immediately. Pickup for the wrong item is scheduled for the same day. No action needed from your side.'),
(6,'customer',6,'Received the correct laptop. Excellent service recovery. Thank you.'),
-- Ticket 7
(7,'employee',1,'Hi, please try inserting the USB receiver into a different port and reinstalling the Logitech Options software. If issue persists we will send a replacement receiver.'),
(7,'customer',7,'Trying a USB 2.0 port fixed it! The USB 3.0 port was causing interference. Thanks!'),
-- Ticket 8
(8,'employee',2,'Hi Bikash, iPhone 15 Pro battery issues within 30 days qualify for a free battery diagnostic. Please visit our Kathmandu service centre or ship the device with your proof of purchase.'),
-- Ticket 9
(9,'employee',1,'Hi Deepak, we have contacted the courier. The package was held for customs documentation review. It has been cleared and will be delivered within 24 hours.'),
(9,'customer',9,'Delivered! Thank you for following up.'),
-- Ticket 10
(10,'employee',1,'Hi Anita, currently ByteStore does not offer EMI directly but Nabil Bank credit card holders can access 6-12 month 0% EMI on purchases above NPR 50,000. We are working on integrating more options soon.'),
-- Ticket 11
(11,'employee',2,'Hi Rajan, VAT invoice for order #26 has been sent to Rajan1@bytestore.com. Please check your spam folder if not received within 1 hour.'),
(11,'customer',11,'Received, thank you!'),
-- Ticket 12
(12,'employee',1,'Hi Priya, phantom touch issues can sometimes be caused by screen protectors. Please remove any protector and test. If the issue persists we will arrange a warranty replacement.'),
-- Ticket 13
(13,'employee',2,'Hi Amit, the correct delivery date is 20th January. The app display was a UI error on our end. We have fixed it and will ensure your delivery is prioritised.'),
(13,'customer',13,'Received on 19th Jan — even earlier than expected. Great service!'),
-- Ticket 14
(14,'employee',1,'Hi Kabita, AirPods Pro right earbud failure within warranty is covered. Please send the unit to our Kathmandu service centre. We will replace with a new set within 5-7 business days.'),
-- Ticket 15
(15,'employee',2,'Hi Nabin, yes we absolutely deliver to Hetauda! Our courier partner covers all major areas in Makwanpur. The dropdown will be updated in our next app release. Place your order and note Hetauda in the address field.'),
(15,'customer',15,'Order placed successfully. Thank you for the clarification!'),
-- Ticket 17
(17,'employee',1,'Hi Arpan, we have contacted the courier and tracking is now updating. There was a system sync delay on their end. Your package should arrive within 2 days.'),
-- Ticket 19
(19,'employee',2,'Hi Sujit, we apologise for the damaged armrest. A replacement part has been dispatched separately free of charge. No need to return the entire chair.'),
(19,'customer',4,'Part arrived and fits perfectly. Much appreciated!'),
-- Ticket 20
(20,'employee',1,'Hi Sajal, promo code BYTE20 was valid for new customers only and your account had a prior order. We apologise for the unclear terms. As a goodwill gesture a NPR 3,000 store credit has been added to your account.'),
(20,'customer',5,'That is very fair. Thank you for the resolution.'),
-- Ticket 22
(22,'employee',2,'Hi, we have rescheduled your delivery for tomorrow between 10am-2pm and ensured the courier has your phone number for advance notice. Sorry for the inconvenience.'),
(22,'customer',7,'Delivered successfully. Thank you.');

-- ─────────────────────────────────────────────────────────────────────────────
-- 9. CARTS — active carts for all customers
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `cart` (`customer_id`,`product_id`,`variant_id`,`quantity`) VALUES
(1, 43, NULL, 1),(1, 57, NULL, 1),
(2, 58, NULL, 1),(2, 22, 11,  1),
(3, 9,  22,   1),(3, 11, 21,  1),
(4, 18, 19,   1),(4, 14, NULL,1),
(5, 2,  3,    1),(5, 16, NULL,1),
(6, 94, NULL, 1),(6, 25, NULL,1),
(7, 6,  7,    1),(7, 13, 9,   1),
(8, 41, NULL, 1),(8, 10, NULL,2),
(9, 153,14,   1),(9, 22, 11,  1),
(10,18, 18,   1),(10,11, 20,  2),
(11,1,  16,   1),(11,4,  NULL,1),
(12,6,  6,    1),(12,9,  22,  1),
(13,43, NULL, 1),(13,58, NULL,1),
(14,2,  2,    1),(14,11, 21,  1),
(15,18, 18,   1),(15,22, 12,  1);

-- ─────────────────────────────────────────────────────────────────────────────
-- 10. WISHLIST — more entries
-- ─────────────────────────────────────────────────────────────────────────────
INSERT IGNORE INTO `wishlist` (`customer_id`,`product_id`) VALUES
(8, 18),(8, 2),(8, 43),(9, 6),(9, 57),(9, 94),
(10,18),(10,11),(11,6),(11,22),(12,2),(12,9),
(13,43),(13,58),(14,18),(14,6),(15,2),(15,22),
(1, 43),(1, 57),(2, 94),(3, 58),(4, 153),(5, 41),
(6, 9), (7, 18),(1, 94),(2, 58),(3, 6), (4, 11);

-- ─────────────────────────────────────────────────────────────────────────────
-- 11. RECENTLY VIEWED
-- ─────────────────────────────────────────────────────────────────────────────
INSERT IGNORE INTO `recently_viewed` (`customer_id`,`product_id`) VALUES
(8, 6),(8,18),(8,2),(8,43),(8,9),
(9, 6),(9,57),(9,94),(9,22),(9,153),
(10,18),(10,11),(10,2),(10,13),(10,6),
(11,1),(11,6),(11,22),(11,19),(11,9),
(12,2),(12,9),(12,18),(12,6),(12,11),
(13,43),(13,58),(13,18),(13,6),(13,22),
(14,2),(14,11),(14,6),(14,18),(14,9),
(15,18),(15,22),(15,6),(15,9),(15,2)
ON DUPLICATE KEY UPDATE viewed_at=CURRENT_TIMESTAMP;

-- ─────────────────────────────────────────────────────────────────────────────
-- 12. CUSTOMER NOTIFICATIONS
-- ─────────────────────────────────────────────────────────────────────────────
INSERT IGNORE INTO `customer_notification` (`customer_id`,`product_id`,`type`,`message`) VALUES
(8, 18,'wishlist_stock',  'Asus ROG Strix Gaming Laptop is back in stock!'),
(9, 6, 'wishlist_stock',  'Apple MacBook Air M2 is back in stock!'),
(10,18,'order_update',    'Your order #47 has been shipped! Expected delivery: 3-5 days.'),
(11,NULL,'order_update',  'Your order #55 has been confirmed and is being processed.'),
(12,NULL,'order_update',  'Your order #57 is pending payment confirmation.'),
(13,6, 'sale',            'Flash Sale! MacBook Air M2 now at 20% off. Limited stock!'),
(14,2, 'sale',            'iPhone 15 Pro — 20% off for the next 48 hours!'),
(15,18,'sale',            'ROG Strix Gaming Laptop — 20% off sale ending soon!'),
(1, 43,'sale',            'MacBook Pro M2 Max now at 10% off. Limited time!'),
(2, 94,'wishlist_stock',  'RTX 4080 GPU is back in stock — grab yours now!'),
(3, 58,'sale',            'ROG Zephyrus G14 sale — 10% off this week only!'),
(4, NULL,'order_update',  'Your order #62 has been cancelled as requested. Refund in progress.'),
(5, 41,'sale',            'MacBook Air 16GB now at 10% off — limited units!'),
(6, 21,'sale',            'GoPro Hero 12 — 20% off flash sale today!'),
(7, 11,'sale',            'AirPods Pro 2 — 15% off. Offer ends midnight!');

SET FOREIGN_KEY_CHECKS=1;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 13:32:42