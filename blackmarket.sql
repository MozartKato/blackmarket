-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2024 at 04:22 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blackmarket`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `Id` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`Id`, `Name`, `Email`, `Password`) VALUES
(1, 'Muhammad Yazid', 'coba@gmail.com', '$2y$10$vdDNv7rLxSA.rN8iUXQ7Iu8L/zhoP9RGdEI9GZroiUtvxF.M5f1M2'),
(4, 'Angon Fabregas', 'angon@fabregas', '$2y$10$oww7OdJYxgftIA0.fdnP5eU/xqVw8n9GPRiWNrcJezPcUFcdU5Bgu');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `Id` int NOT NULL,
  `Category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`Id`, `Category`) VALUES
(9, 'Assault Riffle'),
(10, 'Pistol'),
(11, 'Meele'),
(13, 'Sniper Rifle'),
(14, 'Bullets');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `Id` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `No_telepon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`Id`, `Name`, `Email`, `No_telepon`) VALUES
(1, 'Muhammad Yazid', 'meyazid9@gmail.com', '08247282374'),
(2, 'Ambar Pisang', 'lelepolkadot@anjay.com', '0896454467'),
(3, 'Immanuel Julianto', 'kriminal@banget', '087862347323'),
(4, 'Leonard Noveno Putra', 'veno@gbk.com', '0834636745'),
(5, 'Yulius Setiawan', 'yulius@setiawan', '09876543456');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Id` int NOT NULL,
  `Admin_id` int NOT NULL,
  `Customer_id` int DEFAULT NULL,
  `Created_at` timestamp NOT NULL,
  `Total_payment` decimal(10,2) NOT NULL,
  `Discount` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`Id`, `Admin_id`, `Customer_id`, `Created_at`, `Total_payment`, `Discount`) VALUES
(39, 4, 4, '2024-10-03 07:44:37', '1080000.00', 10),
(40, 4, 3, '2024-10-03 07:45:37', '29682240.00', 2),
(41, 1, NULL, '2024-10-03 07:56:42', '30000.00', 0),
(42, 1, 3, '2024-10-06 03:00:54', '5427000.00', 10);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `Id` int NOT NULL,
  `Order_id` int DEFAULT NULL,
  `Product_id` int DEFAULT NULL,
  `Quantity` int DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) GENERATED ALWAYS AS ((`Price` * `Quantity`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`Id`, `Order_id`, `Product_id`, `Quantity`, `Price`) VALUES
(44, 39, 11, 1, '1200000.00'),
(45, 40, 8, 1, '30000000.00'),
(46, 40, 15, 18, '16000.00'),
(47, 41, 14, 1, '30000.00'),
(48, 42, 14, 1, '30000.00'),
(49, 42, 10, 1, '6000000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Id` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Category_id` int NOT NULL,
  `Price` int NOT NULL,
  `Stock` int NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`Id`, `Name`, `Category_id`, `Price`, `Stock`, `image_path`) VALUES
(8, 'Magnum Research Desert Eagle .50 AE Brushed Chrome', 10, 30000000, 11, '/uploads/dg.png'),
(9, 'Ak-47', 9, 4300000, 12, '/uploads/pngimg.com - ak47_PNG15449.png'),
(10, 'Colt Diamondback Revolver', 10, 6000000, 4, '/uploads/596338.jpg'),
(11, 'Katana', 11, 1200000, 10, '/uploads/katana.png'),
(12, 'Artic Warfare Magnum (AWM)', 13, 120000000, 12, '/uploads/wp5289375-awm-gun-wallpapers.jpg'),
(14, '.300 Winchester Magnum /pcs', 14, 30000, 297, '/uploads/pelor.png'),
(15, '.50 Action Express (AE) /pcs', 14, 16000, 504, '/uploads/pelor1.jpg'),
(16, '7.62x39 110gr Armour Piercing 1 pack (30pcs)', 14, 80000, 670, '/uploads/peloranjay.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Email` (`Email`,`No_telepon`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Order_id` (`Order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Category_On_Categories` (`Category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`Order_id`) REFERENCES `orders` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `Category_On_Categories` FOREIGN KEY (`Category_id`) REFERENCES `categories` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
