INSERT INTO category(category_id,category_name) VALUES 
(1,'Laptops'),
(2,'Desktops'),
(3,'Gaming'),
(4,'Monitors'),
(5,'Processors'),
(6,'Graphics Cards'),
(7,'Motherboards'),
(8,'Memory (RAM)'),
(9,'Storage (SSD/HDD)'),
(10,'Power Supplies'),
(11,'PC Cases'),
(12,'Cooling'),
(13,'Keyboards & Mice'),
(14,'Audio (Headphones/Speakers)'),
(15,'Webcams'),
(16,'Accessories'),
(17,'Phones'),
(18,'Tablets'),
(19,'Wearables (Watches)'),
(20,'Networking (Routers/WiFi)'),
(21,'Printers & Scanners'),
(22,'Servers'),
(23,'Software & Services');


INSERT INTO category (category_name) VALUES ('Laptops');
INSERT INTO category (category_name) VALUES ('Desktops');
INSERT INTO category (category_name) VALUES ('Gaming');
INSERT INTO category (category_name) VALUES ('Monitors');

-- Computer Components
INSERT INTO category (category_name) VALUES ('Processors');
INSERT INTO category (category_name) VALUES ('Graphics Cards');
INSERT INTO category (category_name) VALUES ('Motherboards');
INSERT INTO category (category_name) VALUES ('Memory (RAM)');
INSERT INTO category (category_name) VALUES ('Storage (SSD/HDD)');
INSERT INTO category (category_name) VALUES ('Power Supplies');
INSERT INTO category (category_name) VALUES ('PC Cases');
INSERT INTO category (category_name) VALUES ('Cooling');

-- Peripherals & Accessories
INSERT INTO category (category_name) VALUES ('Keyboards & Mice');
INSERT INTO category (category_name) VALUES ('Audio (Headphones/Speakers)');
INSERT INTO category (category_name) VALUES ('Webcams');
INSERT INTO category (category_name) VALUES ('Accessories');

-- Mobile & Wearables
INSERT INTO category (category_name) VALUES ('Phones');
INSERT INTO category (category_name) VALUES ('Tablets');
INSERT INTO category (category_name) VALUES ('Wearables (Watches)');

-- Networking & Office
INSERT INTO category (category_name) VALUES ('Networking (Routers/WiFi)');
INSERT INTO category (category_name) VALUES ('Printers & Scanners');
INSERT INTO category (category_name) VALUES ('Servers');

-- Software & Services
INSERT INTO category (category_name) VALUES ('Software & Services');

-- Verify insertion
SELECT * FROM category ORDER BY category_name;



UPDATE product SET category_id = 17 WHERE product_id IN (2,3);


UPDATE product SET category_id = 1 WHERE product_id IN
(1,6,7,8,18,27,28,33,34,41,42,43,44,45,46,47,48,49,50,51,52,
58,59,60,61,62,63,64,65,77,78,79,80);


UPDATE product SET category_id = 18 WHERE product_id IN
(12,13,37,38,39);


UPDATE product SET category_id = 19 WHERE product_id IN (14,15);


UPDATE product SET category_id = 13 WHERE product_id IN
(4,5,16,17);


UPDATE product SET category_id = 14 WHERE product_id IN
(9,10,11,93);


UPDATE product SET category_id = 3 WHERE product_id IN
(24);


UPDATE product SET category_id = 4 WHERE product_id IN
(19,26,30,53,57,75,76);


UPDATE product SET category_id = 9 WHERE product_id IN
(22,23,35);


UPDATE product SET category_id = 20 WHERE product_id IN
(25,71);


UPDATE product SET category_id = 7 WHERE product_id IN
(55,56,72,73);


UPDATE product SET category_id = 12 WHERE product_id IN
(74);


UPDATE product SET category_id = 21 WHERE product_id IN
(66,67,68,69,70);


UPDATE product SET category_id = 2 WHERE product_id IN
(40);


UPDATE product SET category_id = 22 WHERE product_id IN
(82);


UPDATE product SET category_id = 6 WHERE product_id IN
(94,95);


UPDATE product SET category_id = 16 WHERE product_id IN
(36,54);