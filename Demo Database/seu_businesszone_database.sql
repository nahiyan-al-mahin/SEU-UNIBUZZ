-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2026 at 08:47 PM
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
-- Database: `seu_businesszone_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_to_cart`
--

CREATE TABLE `add_to_cart` (
  `Product_add_id` int(11) NOT NULL,
  `Cart_Id` int(11) NOT NULL,
  `Product_Id` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_Id` int(11) NOT NULL,
  `Admin_Name` varchar(100) NOT NULL,
  `Admin_mail` varchar(150) NOT NULL,
  `Admin_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_Id`, `Admin_Name`, `Admin_mail`, `Admin_Password`) VALUES
(1, 'Mahin', 'mahin@admin.com', 'admin123'),
(2, 'Nusrat', 'nusrat@admin.com', 'admin123'),
(3, 'Aorpon', 'aorpon@admin.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `business_idea`
--

CREATE TABLE `business_idea` (
  `Idea_Id` int(11) NOT NULL,
  `Seller_Id` int(11) NOT NULL,
  `Idea` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Funding_Goal` decimal(12,2) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_idea`
--

INSERT INTO `business_idea` (`Idea_Id`, `Seller_Id`, `Idea`, `Description`, `Funding_Goal`, `Status`) VALUES
(1, 1, 'Campus Delivery App', 'An app for delivering food and items within SEU campus', 50000.00, 'Pending'),
(2, 2, 'Handmade Crafts Store', 'Online store for SEU students handmade crafts', 30000.00, 'Approved'),
(3, 1, 'SEU Ride Share', 'A carpooling app for SEU students commuting daily', 45000.00, 'Pending'),
(4, 2, 'Campus Canteen App', 'Digital menu and pre-order system for SEU canteen', 35000.00, 'Approved'),
(5, 1, 'Online Tutor Platform', 'Connect SEU senior students with juniors for paid tutoring', 40000.00, 'Pending'),
(6, 2, 'Secondhand Book Market', 'Buy and sell used textbooks among SEU students', 20000.00, 'Approved'),
(7, 1, 'Student Freelance Hub', 'Freelancing marketplace exclusively for SEU students', 60000.00, 'Pending'),
(8, 2, 'SEU Event Planner', 'Platform to organize and promote student events at SEU', 25000.00, 'Approved'),
(9, 1, 'Campus Lost & Found', 'Digital lost and found board for SEU campus items', 15000.00, 'Pending'),
(10, 1, 'FC 26 IN SHARE GAMEPLAY', 'WE WILL BUY FC26 THEN PLAY TOGETHER WHILE SAVING MONEY', 5000.00, 'Pending'),
(11, 1, 'Valorant Gameplay', 'Stream Valorant ', 20000.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `buyer`
--

CREATE TABLE `buyer` (
  `Buyer_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Cart_Id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyer`
--

INSERT INTO `buyer` (`Buyer_Id`, `User_Id`, `Cart_Id`) VALUES
(1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_Id` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 0,
  `Buyer_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_Id`, `Quantity`, `Buyer_Id`) VALUES
(1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `global_chat`
--

CREATE TABLE `global_chat` (
  `Message_id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Message_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `global_chat`
--

INSERT INTO `global_chat` (`Message_id`, `User_Id`, `Message_Date`, `Message`) VALUES
(1, 4, '2026-05-24 09:00:00', 'Hello everyone!'),
(2, 6, '2026-05-24 09:05:00', 'Hi! Check out my new products.'),
(4, 1, '2026-05-24 09:12:00', 'Welcome to SEU BusinessZone! Please follow community guidelines.'),
(5, 7, '2026-05-24 09:15:00', 'Just listed my SEU Hoodies, check them out!'),
(6, 4, '2026-05-24 09:18:00', 'Wow the hoodie looks amazing, will order soon!'),
(7, 2, '2026-05-24 09:20:00', 'Reminder: All sellers must verify their university email.'),
(9, 6, '2026-05-24 09:27:00', 'Yes! All my products support Cash on Delivery.'),
(10, 3, '2026-05-24 09:30:00', 'Exciting ideas in the funding section, go check them out!'),
(11, 4, '2026-05-24 09:33:00', 'I just funded the Campus Delivery App idea, looks promising!'),
(12, 7, '2026-05-24 09:35:00', 'New cookies batch is available, limited stock!'),
(14, 1, '2026-05-24 09:40:00', 'Yes, just add them to your cart and place a single order!'),
(15, 6, '2026-05-24 09:43:00', 'Just uploaded C Programming Book, great for 1st year students.'),
(16, 4, '2026-05-24 09:46:00', 'That book is super helpful, already bought it last semester!'),
(17, 2, '2026-05-24 09:50:00', 'Any issues with orders? Contact admin directly.'),
(18, 7, '2026-05-24 09:53:00', 'Restocked the Hijab collection, new colors available!'),
(20, 3, '2026-05-24 09:58:00', 'Thank you all for being part of SEU BusinessZone! Keep trading!'),
(21, 6, '2026-05-24 10:01:00', 'USB-C Hub now in stock, perfect for laptop users!'),
(22, 4, '2026-05-24 10:05:00', 'Just placed my first order, excited!'),
(23, 1, '2026-05-24 10:08:00', 'New business ideas are being reviewed, stay tuned!'),
(24, 1, '2026-05-30 03:09:43', 'This is mahin uddin the main admin and today is 30 may'),
(25, 6, '2026-05-30 03:10:06', 'This is also mahin but a seller \r\n');

-- --------------------------------------------------------

--
-- Table structure for table `idea_fund`
--

CREATE TABLE `idea_fund` (
  `Fund_id` int(11) NOT NULL,
  `Idea_Id` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_Status` varchar(50) NOT NULL DEFAULT 'Pending',
  `Amount` decimal(12,2) NOT NULL,
  `Buyer_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idea_fund`
--

INSERT INTO `idea_fund` (`Fund_id`, `Idea_Id`, `Payment_Method`, `Payment_Status`, `Amount`, `Buyer_Id`) VALUES
(1, 0, 'Cash on Delivery', 'Completed', 5000.00, 1),
(3, 0, 'Cash on Delivery', 'Completed', 4000.00, 1),
(5, 0, 'Cash on Delivery', 'Completed', 6000.00, 1),
(7, 0, 'Cash on Delivery', 'Completed', 8000.00, 1),
(9, 0, 'Cash on Delivery', 'Completed', 5500.00, 1),
(11, 0, 'Online', 'Completed', 5000.00, 1),
(12, 11, 'Online', 'Completed', 2500.00, 1),
(13, 11, 'Online', 'Completed', 2500.00, 1),
(14, 11, 'Online', 'Completed', 5000.00, 1),
(15, 11, 'Online', 'Completed', 10000.00, 1),
(16, 11, 'Online', 'Completed', 1000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `Order_Id` int(11) NOT NULL,
  `Buyer_Id` int(11) NOT NULL,
  `Order_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL DEFAULT 'Pending',
  `Total_amount` decimal(10,2) NOT NULL,
  `Payment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`Order_Id`, `Buyer_Id`, `Order_Date`, `Status`, `Total_amount`, `Payment_id`) VALUES
(1, 1, '2026-05-24 10:00:00', 'Pending', 1500.00, NULL),
(3, 1, '2026-05-30 23:17:00', 'Pending', 1830.00, 2),
(4, 1, '2026-05-30 23:41:17', 'Pending', 280.00, 3),
(5, 1, '2026-05-30 23:42:04', 'Pending', 280.00, 4),
(6, 1, '2026-05-30 23:43:20', 'Pending', 280.00, 5),
(7, 1, '2026-05-30 23:44:13', 'Pending', 280.00, 6);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_id` int(11) NOT NULL,
  `Order_Id` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_Status` varchar(50) NOT NULL DEFAULT 'Pending',
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_id`, `Order_Id`, `Payment_Method`, `Payment_Status`, `Amount`) VALUES
(2, 3, 'Cash on Delivery', 'Pending', 1830.00),
(3, 4, 'Cash on Delivery', 'Pending', 280.00),
(4, 5, 'Cash on Delivery', 'Pending', 280.00),
(5, 6, 'Cash on Delivery', 'Pending', 280.00),
(6, 7, 'Cash on Delivery', 'Pending', 280.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_Id` int(11) NOT NULL,
  `Seller_Id` int(11) NOT NULL,
  `Product_Name` varchar(150) NOT NULL,
  `Product_Type` varchar(100) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Stock_Quantity` int(11) NOT NULL DEFAULT 0,
  `Product_Image` varchar(255) DEFAULT NULL,
  `Product_Category_Id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Product_Id`, `Seller_Id`, `Product_Name`, `Product_Type`, `Price`, `Stock_Quantity`, `Product_Image`, `Product_Category_Id`) VALUES
(2, 1, 'Data Structures Book', 'Digital', 300.00, 50, 'productImages/ds_book.jpg', 2),
(3, 2, 'SEU Hoodie', 'Physical', 650.00, 20, 'productImages/hoodie.jpg', 3),
(4, 2, 'Homemade Cake', 'Food', 450.00, 5, 'productImages/cake.jpg', 4),
(5, 1, 'Notebook Set', 'Physical', 150.00, 100, 'productImages/notebook.jpg', 5),
(6, 1, 'USB-C Hub', 'Physical', 1200.00, 15, 'productImages/usb_hub.jpg', 1),
(7, 2, 'Hijab (Cotton)', 'Physical', 250.00, 30, 'productImages/hijab.jpg', 3),
(8, 1, 'C Programming Book', 'Digital', 280.00, 40, 'productImages/c_book.jpg', 2),
(9, 2, 'Homemade Cookies Box', 'Food', 350.00, 8, 'productImages/cookies.jpg', 4),
(10, 1, 'Ballpen Set (10pcs)', 'Physical', 120.00, 200, 'productImages/ballpen.jpg', 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `Product_Category_Id` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`Product_Category_Id`, `Category_Name`) VALUES
(1, 'Electronics'),
(2, 'Books'),
(3, 'Clothing'),
(4, 'Food'),
(5, 'Stationery');

-- --------------------------------------------------------

--
-- Table structure for table `product_orders`
--

CREATE TABLE `product_orders` (
  `Product_Order_Id` int(11) NOT NULL,
  `Product_Id` int(11) NOT NULL,
  `Order_Id` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_orders`
--

INSERT INTO `product_orders` (`Product_Order_Id`, `Product_Id`, `Order_Id`, `Quantity`) VALUES
(2, 3, 1, 1),
(4, 9, 3, 1),
(5, 8, 3, 1),
(6, 6, 3, 1),
(7, 8, 6, 1),
(8, 8, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `Seller_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `University_Email` varchar(150) NOT NULL,
  `Rating` decimal(3,2) DEFAULT 0.00,
  `Department` varchar(100) DEFAULT NULL,
  `Semester` varchar(20) DEFAULT NULL,
  `Student_Id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller`
--

INSERT INTO `seller` (`Seller_Id`, `User_Id`, `University_Email`, `Rating`, `Department`, `Semester`, `Student_Id`) VALUES
(1, 6, '2020000000000@seu.edu.bd', 0.00, 'CSE', '8th', '2020000000000'),
(2, 7, '2021111111111@seu.edu.bd', 0.00, 'BBA', '6th', '2021111111111');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `Transaction_id` int(11) NOT NULL,
  `Fund_id` int(11) NOT NULL,
  `Idea_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`Transaction_id`, `Fund_id`, `Idea_Id`) VALUES
(1, 1, 1),
(3, 3, 3),
(5, 5, 5),
(7, 7, 7),
(9, 9, 9);

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `User_Id` int(11) NOT NULL,
  `First_Name` varchar(100) NOT NULL,
  `Last_Name` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Mobile` varchar(20) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Admin_Id` int(11) DEFAULT NULL,
  `Buyer_Id` int(11) DEFAULT NULL,
  `Seller_Id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`User_Id`, `First_Name`, `Last_Name`, `Email`, `Mobile`, `Password`, `Admin_Id`, `Buyer_Id`, `Seller_Id`) VALUES
(1, 'Mahin', 'Admin', 'mahin@admin.com', '01700000001', '$2y$10$9EisEtDXKyvKKUycLFeJHOmhKV9XZLsdTzs7hNK.Pg2IqFsGfa4Su', 1, NULL, NULL),
(2, 'Nusrat', 'Admin', 'nusrat@admin.com', '01700000002', '$2y$10$JC0YEGTpFTXJ9OmLGVK4JuzzzRh.I05yZ1QFZbnfhp1OCctPCDj0W', 2, NULL, NULL),
(3, 'Aorpon', 'Admin', 'aorpon@admin.com', '01700000003', '$2y$10$I20SQ/r1xgoGl2gcomGOWeH0f0b4Ds84j6JjBoX4mi7HSvq6CPDA2', 3, NULL, NULL),
(4, 'Rahim', 'Uddin', 'rahim@gmail.com', '01800000001', '$2y$10$4r5C3vywp4Goks2rh4FOz.mhcIZ4jsrt0n40nXYNyok9RVsGn.sE6', NULL, 1, NULL),
(6, 'Mahin', 'SEU', '2020000000000@seu.edu.bd', '01900000001', '$2y$10$NYQz6uEqO/ckh93LY.GdLOovQjCudC5PywkSJtgY0tzhaXiTA6dLq', NULL, NULL, 1),
(7, 'Riya', 'Akter', '2021111111111@seu.edu.bd', '01900000002', '$2y$10$1TquW1Q.2TK0Yh59VKDtYenujFItZtcp7Zt4rZEel.HwUu3EBB4WO', NULL, NULL, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_to_cart`
--
ALTER TABLE `add_to_cart`
  ADD PRIMARY KEY (`Product_add_id`),
  ADD KEY `Cart_Id` (`Cart_Id`),
  ADD KEY `Product_Id` (`Product_Id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_Id`),
  ADD UNIQUE KEY `Admin_mail` (`Admin_mail`);

--
-- Indexes for table `business_idea`
--
ALTER TABLE `business_idea`
  ADD PRIMARY KEY (`Idea_Id`),
  ADD KEY `Seller_Id` (`Seller_Id`);

--
-- Indexes for table `buyer`
--
ALTER TABLE `buyer`
  ADD PRIMARY KEY (`Buyer_Id`),
  ADD KEY `User_Id` (`User_Id`),
  ADD KEY `Cart_Id` (`Cart_Id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_Id`),
  ADD KEY `Buyer_Id` (`Buyer_Id`);

--
-- Indexes for table `global_chat`
--
ALTER TABLE `global_chat`
  ADD PRIMARY KEY (`Message_id`),
  ADD KEY `User_Id` (`User_Id`);

--
-- Indexes for table `idea_fund`
--
ALTER TABLE `idea_fund`
  ADD PRIMARY KEY (`Fund_id`),
  ADD KEY `Buyer_Id` (`Buyer_Id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`Order_Id`),
  ADD KEY `Buyer_Id` (`Buyer_Id`),
  ADD KEY `Payment_id` (`Payment_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_id`),
  ADD KEY `Order_Id` (`Order_Id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_Id`),
  ADD KEY `Seller_Id` (`Seller_Id`),
  ADD KEY `Product_Category_Id` (`Product_Category_Id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`Product_Category_Id`);

--
-- Indexes for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`Product_Order_Id`),
  ADD KEY `Product_Id` (`Product_Id`),
  ADD KEY `Order_Id` (`Order_Id`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`Seller_Id`),
  ADD UNIQUE KEY `University_Email` (`University_Email`),
  ADD KEY `User_Id` (`User_Id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`Transaction_id`),
  ADD KEY `Fund_id` (`Fund_id`),
  ADD KEY `Idea_Id` (`Idea_Id`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`User_Id`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `Admin_Id` (`Admin_Id`),
  ADD KEY `Buyer_Id` (`Buyer_Id`),
  ADD KEY `Seller_Id` (`Seller_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_to_cart`
--
ALTER TABLE `add_to_cart`
  MODIFY `Product_add_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `business_idea`
--
ALTER TABLE `business_idea`
  MODIFY `Idea_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `buyer`
--
ALTER TABLE `buyer`
  MODIFY `Buyer_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `global_chat`
--
ALTER TABLE `global_chat`
  MODIFY `Message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `idea_fund`
--
ALTER TABLE `idea_fund`
  MODIFY `Fund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `Order_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `Product_Category_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `Product_Order_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `seller`
--
ALTER TABLE `seller`
  MODIFY `Seller_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `Transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `User_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_to_cart`
--
ALTER TABLE `add_to_cart`
  ADD CONSTRAINT `add_to_cart_ibfk_1` FOREIGN KEY (`Cart_Id`) REFERENCES `cart` (`Cart_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `add_to_cart_ibfk_2` FOREIGN KEY (`Product_Id`) REFERENCES `product` (`Product_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `business_idea`
--
ALTER TABLE `business_idea`
  ADD CONSTRAINT `business_idea_ibfk_1` FOREIGN KEY (`Seller_Id`) REFERENCES `seller` (`Seller_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `buyer`
--
ALTER TABLE `buyer`
  ADD CONSTRAINT `buyer_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `buyer_ibfk_2` FOREIGN KEY (`Cart_Id`) REFERENCES `cart` (`Cart_Id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `global_chat`
--
ALTER TABLE `global_chat`
  ADD CONSTRAINT `global_chat_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `idea_fund`
--
ALTER TABLE `idea_fund`
  ADD CONSTRAINT `idea_fund_ibfk_1` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`Payment_id`) REFERENCES `payment` (`Payment_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Order_Id`) REFERENCES `order` (`Order_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Seller_Id`) REFERENCES `seller` (`Seller_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`Product_Category_Id`) REFERENCES `product_category` (`Product_Category_Id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD CONSTRAINT `product_orders_ibfk_1` FOREIGN KEY (`Product_Id`) REFERENCES `product` (`Product_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_orders_ibfk_2` FOREIGN KEY (`Order_Id`) REFERENCES `order` (`Order_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller`
--
ALTER TABLE `seller`
  ADD CONSTRAINT `seller_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`Fund_id`) REFERENCES `idea_fund` (`Fund_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`Idea_Id`) REFERENCES `business_idea` (`Idea_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_table`
--
ALTER TABLE `user_table`
  ADD CONSTRAINT `user_table_ibfk_1` FOREIGN KEY (`Admin_Id`) REFERENCES `admin` (`Admin_Id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_table_ibfk_2` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_table_ibfk_3` FOREIGN KEY (`Seller_Id`) REFERENCES `seller` (`Seller_Id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
