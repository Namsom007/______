CREATE DATABASE IF NOT EXISTS greenleaf_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE greenleaf_shop;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `care_instruction` text DEFAULT NULL,
  `light_requirement` varchar(100) DEFAULT NULL,
  `water_requirement` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `province` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `payment_method` enum('Cash on Delivery','Bank Transfer') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Shipping','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Admin (Password: admin123, stored as MD5 for seed, will auto-upgrade to bcrypt on first login)
INSERT INTO `users` (`first_name`, `last_name`, `username`, `email`, `password`, `role`) VALUES
('Admin', 'GreenLeaf', 'admin', 'admin@greenleaf.com', '0192023a7bbd73250516f069df18b500', 'admin');

-- Insert Categories
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Indoor Plants'),
(2, 'Outdoor Plants'),
(3, 'Cactus'),
(4, 'Succulents'),
(5, 'Pots'),
(6, 'Gardening Tools');

-- Insert Products
INSERT INTO `products` (`category_id`, `name`, `description`, `care_instruction`, `light_requirement`, `water_requirement`, `price`, `stock`, `image`) VALUES
(1, 'Monstera Deliciosa', 'The classic Swiss Cheese Plant. Perfect for bringing a tropical vibe indoors.', 'Wipe leaves with a damp cloth to remove dust.', 'Bright, indirect light', 'Water when top 2 inches of soil are dry', 450.00, 15, 'monstera.jpg'),
(1, 'Snake Plant', 'Extremely hardy indoor plant. Known for its air-purifying qualities.', 'Do not overwater. Extremely drought tolerant.', 'Low to bright indirect light', 'Water every 2-3 weeks', 250.00, 30, 'snake_plant.jpg'),
(1, 'Peace Lily', 'Beautiful dark green foliage with striking white flowers. Excellent air purifier.', 'Keep away from direct sunlight to prevent leaf burn.', 'Low to medium indirect light', 'Keep soil consistently moist but not soggy', 300.00, 20, 'peace_lily.jpg'),
(1, 'Rubber Plant', 'Sturdy and striking with glossy, burgundy-green leaves.', 'Clean leaves regularly. Rotate the plant for even growth.', 'Bright, indirect light', 'Water when the top inch of soil is dry', 350.00, 10, 'rubber_plant.jpg'),
(4, 'Aloe Vera', 'A versatile succulent known for its healing properties.', 'Needs good drainage. Do not let it sit in water.', 'Bright, direct sunlight', 'Water deeply, but infrequently', 150.00, 40, 'aloe_vera.jpg'),
(1, 'Golden Pothos', 'One of the easiest houseplants to grow. Trails beautifully.', 'Prune occasionally to encourage fuller growth.', 'Low to bright indirect light', 'Water when soil is completely dry', 120.00, 50, 'golden_pothos.jpg'),
(3, 'Bunny Ears Cactus', 'A cute cactus with pads that look like bunny ears.', 'Handle with care; the small fuzz is actually tiny spines.', 'Bright, direct sunlight', 'Water sparingly, only when completely dry', 180.00, 25, 'bunny_ears.jpg'),
(4, 'Zebra Haworthia', 'Small succulent with attractive white stripes on dark green leaves.', 'Great for small spaces or desks.', 'Bright, indirect light', 'Water every 2-3 weeks', 160.00, 35, 'zebra_haworthia.jpg'),
(4, 'Jade Plant', 'A classic succulent often considered a symbol of good luck.', 'Ensure excellent drainage.', 'Bright light with some direct sun', 'Water when soil is fully dry', 200.00, 20, 'jade_plant.jpg'),
(5, 'Ceramic Minimalist Pot', 'A beautiful, clean white ceramic pot with drainage hole and saucer.', 'Handle with care.', 'N/A', 'N/A', 150.00, 60, 'ceramic_pot.jpg'),
(6, 'Organic Fertilizer', 'All-natural fertilizer for lush, green growth. Suitable for all indoor plants.', 'Dilute 1 capful in 1 liter of water.', 'N/A', 'N/A', 190.00, 100, 'fertilizer.jpg'),
(6, 'Gardening Tool Set', 'Set includes a trowel, transplanter, and cultivator. High-quality rust-resistant material.', 'Clean after use and store in a dry place.', 'N/A', 'N/A', 350.00, 15, 'tool_set.jpg');
