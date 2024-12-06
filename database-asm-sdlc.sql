-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2024 at 04:05 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nguyenquangthanhbh00824`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'T-Shirts'),
(2, 'Jeans'),
(3, 'Shoes');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `total` double DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `created_at`, `user_id`, `product_id`, `total`, `status`) VALUES
(1, '2024-11-29 10:00:00', 1, NULL, 69.98, 'pending'),
(2, '2024-11-29 11:12:54', 3, NULL, 19.99, 'Pending'),
(3, '2024-11-29 11:15:42', 3, NULL, 69.98, 'Pending'),
(4, '2024-11-29 15:29:14', 4, NULL, 109.98, 'Pending'),
(5, '2024-11-29 17:25:04', 4, NULL, 209.96, 'Pending'),
(6, '2024-11-29 17:27:21', 4, NULL, 199.97, 'Pending'),
(7, '2024-11-29 17:34:33', 4, NULL, 181.96, 'Pending'),
(8, '2024-11-29 19:51:40', 4, NULL, 275.95, 'Pending'),
(9, '2024-11-29 21:07:14', 4, NULL, 89.99, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `price` double DEFAULT NULL,
  `amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `product_id`, `order_id`, `price`, `amount`) VALUES
(1, 1, 1, 19.99, 2),
(2, 2, 1, 49.99, 1),
(3, 1, 2, 19.99, 1),
(4, 1, 3, 19.99, 1),
(5, 2, 3, 49.99, 1),
(6, 1, 4, 19.99, 1),
(7, 3, 4, 89.99, 1),
(8, 1, 5, 19.99, 1),
(9, 2, 5, 49.99, 2),
(10, 3, 5, 89.99, 1),
(11, 1, 6, 19.99, 1),
(12, 3, 6, 89.99, 2),
(13, 1, 7, 19.99, 1),
(14, 2, 7, 49.99, 1),
(15, 3, 7, 89.99, 1),
(16, 9, 7, 21.99, 1),
(17, 1, 8, 19.99, 1),
(18, 2, 8, 49.99, 1),
(19, 3, 8, 89.99, 1),
(20, 9, 8, 21.99, 1),
(21, 12, 8, 93.99, 1),
(22, 3, 9, 89.99, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `quantity`, `category_id`, `image`) VALUES
(1, 'Basic Red T-Shirt', 19.99, 'Soft cotton t-shirt, available in all sizes', 100, 1, 'https://contents.mediadecathlon.com/p2606947/k$1c9e0ffdefc3e67bdeabc82be7893e93/dry-men-s-running-breathable-t-shirt-red-decathlon-8771124.jpg?f=1920x0&format=auto'),
(2, 'Blue Denim Jeans', 49.99, 'Classic fit, blue denim jeans', 50, 2, 'https://product.hstatic.net/1000340796/product/z5758993730474_0d4401e0072d39371079d21fc82bd8d3_d7d9c5bfc7104e2095346048ec104b45.jpg'),
(3, 'Running Sneakers', 89.99, 'Comfortable sneakers for daily use', 30, 3, 'https://s3.amazonaws.com/www.irunfar.com/wp-content/uploads/2024/07/25053122/Best-Trail-Running-Shoes-Hoka-Speedgoat-6.jpg'),
(9, 'Blue T-Shirt', 21.99, 'Nice shirt', 90, 1, 'uploads/products/Chris_Cross_Royal_Blue_Tshirt.jpg'),
(12, 'Green soccer shoes', 93.99, 'The shoes are soft and trusted by many players', 95, 3, 'uploads/products/SC.jpg'),
(13, 'Blue Jeans', 25.99, 'Nice', 90, 2, 'uploads/products/images (2).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `role`) VALUES
(1, 'jdoe', 'jdoe@example.com', '1234567890', 'password', 'customer'),
(2, 'admin', 'admin@store.com', '0987654321', 'admin123', 'admin'),
(3, 'thanh', '123123@gmail.com', '12313123123', '$2y$10$J58M/CRiz15u0S3ZW7DKK.nxmLp5OtzX/HBy./3zEj423ug9j2312', 'customer'),
(4, 'thanhbuy', '2004@gmail.com', '0987654321', '$2y$10$tYL5SYypeQz1cFSYbO8pr.aa4OdNW.M6Mnfhn0Pl62J1UEsoWU.t2', 'customer'),
(5, 'thanhadmin', '1303@gmail.com', '012356789', '$2y$10$7f32Kszao.klvJu2WRcCnuFjYdKicbySyjpbxM6GEpFRrM1X4.iHK', 'admin'),
(6, 'thanhnguyen', 'thanhne@gmail.com', '0987654322', '$2y$10$WjW0LAEKXtoMkzSNDL6yt.89.FuBYW3dkRVaTwJ0ThNxO2Z5abpee', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
