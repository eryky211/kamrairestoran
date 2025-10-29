-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 10:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kamrairestaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_key` varchar(50) NOT NULL,
  `announcement_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_key`, `announcement_text`) VALUES
('box1', 'Welcome to Kamrai Restaurant!'),
('box2', 'Don\'t forget to check out our daily specials.');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Appetizers'),
(2, 'Main Courses'),
(3, 'Desserts'),
(4, 'Drinks');

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`item_id`, `category_id`, `item_name`, `description`, `price`, `is_available`) VALUES
(1, 1, 'Spring Rolls', 'Crispy vegetable spring rolls.', 8.99, 1),
(2, 1, 'Garlic Bread', 'Toasted bread with garlic butter.', 7.00, 1),
(3, 2, 'Classic Burger', 'Beef patty with lettuce, tomato, and cheese.', 15.99, 1),
(4, 2, 'Grilled Chicken', 'Grilled chicken breast with side vegetables.', 18.50, 1),
(5, 3, 'Chocolate Cake', 'Rich dark chocolate cake.', 7.99, 1),
(6, 4, 'Cola', '330ml can.', 2.99, 1),
(7, 4, 'Mineral Water', '500ml bottle.', 1.99, 1),
(8, 2, 'Nasi Goreng Ayam', 'A fried rice served with 1/8 roasted chicken', 7.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_price_at_order` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_item_id`, `order_id`, `item_id`, `quantity`, `item_price_at_order`) VALUES
(1, 1, 3, 1, 15.99),
(2, 1, 7, 1, 1.99),
(3, 2, 1, 1, 8.99),
(4, 2, 6, 1, 2.99),
(5, 3, 1, 1, 8.99);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `waiter_id` int(11) NOT NULL,
  `order_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('Pending','Preparing','Served') DEFAULT 'Pending',
  `payment_status` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `total_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `table_id`, `waiter_id`, `order_time`, `order_status`, `payment_status`, `total_price`) VALUES
(1, 1, 1, '2025-10-28 18:53:49', 'Served', 'Paid', 17.98),
(2, 3, 1, '2025-10-29 06:36:03', 'Served', 'Paid', 11.98),
(3, 2, 1, '2025-10-29 06:43:27', 'Served', 'Paid', 8.99);

-- --------------------------------------------------------

--
-- Table structure for table `restauranttables`
--

CREATE TABLE `restauranttables` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(50) NOT NULL,
  `status` enum('Available','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restauranttables`
--

INSERT INTO `restauranttables` (`table_id`, `table_number`, `status`) VALUES
(1, 'Table 1', 'Available'),
(2, 'Table 2', 'Available'),
(3, 'Table 3', 'Available'),
(4, 'Table 4', 'Available'),
(5, 'Table 5', 'Available'),
(6, 'Patio 1', 'Available'),
(7, 'Table 6', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('waiter','kitchen','cashier','admin') NOT NULL,
  `full_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `full_name`) VALUES
(1, 'admin', '$2y$10$69XKBpqDkTrOFZOyAv2xMONJ.tT8Qaa/Mz2Rx1rfhLIAQmyAD3tOi', 'admin', 'Admin User'),
(3, 'waiter1', '$2y$10$onfPuy4ybWOMuOq7v6tOy.SPnWzcY35UA/MCYpx9Y/IRYrk/qxQ56', 'waiter', 'Burn Waiter'),
(4, 'kitchen1', '$2y$10$swfbw58Rrb.CB/I3F5Pbk.XOHOSMuXwZtTaUPgigY4N6IZZrNR06C', 'kitchen', 'Burn Kitchen'),
(5, 'cashier1', '$2y$10$NEuOnlFq8Nh.U1Ghj55dr.dQd2qNFGwI8b85PBdFo7TgXP94CtIyy', 'cashier', 'Burn Cashier');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `waiter_id` (`waiter_id`);

--
-- Indexes for table `restauranttables`
--
ALTER TABLE `restauranttables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `restauranttables`
--
ALTER TABLE `restauranttables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD CONSTRAINT `menuitems_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menuitems` (`item_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restauranttables` (`table_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
