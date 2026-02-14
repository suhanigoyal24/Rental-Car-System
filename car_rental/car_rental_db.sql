-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 09:52 AM
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
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `agency_details`
--

CREATE TABLE `agency_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `agency_name` varchar(150) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agency_details`
--

INSERT INTO `agency_details` (`id`, `user_id`, `agency_name`, `owner_name`, `address`, `created_at`) VALUES
(1, 1, 'Speedy Rentals', 'Ramesh Kumar', 'Rajouri Garden, Delhi', '2026-02-14 06:01:05'),
(2, 2, 'Fast Wheels', 'Sonia Verma', 'MG Road, Bengaluru', '2026-02-14 06:01:05'),
(3, 6, 'Suhani Goyal', 'Suhani Goyal', 'Gwalior, Madhya pradesh', '2026-02-14 06:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `total_amount` double DEFAULT NULL,
  `amount_due` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `car_id`, `user_id`, `customer_name`, `customer_phone`, `booking_date`, `return_date`, `total_amount`, `amount_due`, `status`, `created_at`) VALUES
(1, 1, 3, NULL, NULL, '2026-02-14', '2026-02-16', NULL, NULL, 'confirmed', '2026-02-14 06:01:05'),
(2, 2, 4, NULL, NULL, '2026-02-15', '2026-02-17', NULL, NULL, 'pending', '2026-02-14 06:01:05'),
(3, 2, 5, 'Dev Dixit Industries', '8569922845', '2026-02-18', '0000-00-00', 5400, NULL, 'pending', '2026-02-14 08:21:24'),
(4, 2, 5, 'Dev Dixit Industries', '8569922845', '2026-02-19', '2026-02-27', 16200, NULL, 'pending', '2026-02-14 08:26:17'),
(5, 2, 5, 'Dev Dixit', '8569922845', '2026-02-26', '2026-02-11', -25200, NULL, 'pending', '2026-02-14 08:43:08'),
(6, 5, 5, 'Dev Dixit', '8569922845', '2026-02-21', '2026-02-21', 1500, NULL, 'pending', '2026-02-14 08:49:29');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `seating_capacity` int(11) NOT NULL,
  `rent_per_day` decimal(10,2) NOT NULL,
  `status` enum('available','sold') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `agency_id`, `vehicle_model`, `vehicle_number`, `seating_capacity`, `rent_per_day`, `status`, `created_at`) VALUES
(1, 1, 'Toyota Innova Crysta', 'DL1AB1234', 7, 2500.00, 'available', '2026-02-14 06:01:05'),
(2, 1, 'Honda City', 'DL1XY5678', 5, 1800.00, 'available', '2026-02-14 06:01:05'),
(3, 2, 'Maruti Suzuki Swift', 'KA03CD2345', 5, 1200.00, 'available', '2026-02-14 06:01:05'),
(4, 2, 'Mahindra XUV500', 'KA03EF6789', 7, 2200.00, 'available', '2026-02-14 06:01:05'),
(5, 6, 'Hatchback (Wagon-R)', 'MP-07-45-0004', 5, 1500.00, 'available', '2026-02-14 06:41:18'),
(6, 6, 'Maruti Dzire (Sedan)', 'UP-07-65-0045', 5, 2000.00, 'available', '2026-02-14 07:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','agency') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Speedy Rentals', 'agency1@gmail.com', NULL, '$2y$10$wG0I0N0XqJ5/YL1p0kHYOeDxG1sFbfjWvOaQyGZZR1lh0n5Vp2H5O', 'agency', '2026-02-14 06:01:04'),
(2, 'Fast Wheels', 'agency2@gmail.com', NULL, '$2y$10$wG0I0N0XqJ5/YL1p0kHYOeDxG1sFbfjWvOaQyGZZR1lh0n5Vp2H5O', 'agency', '2026-02-14 06:01:04'),
(3, 'Amit Sharma', 'amit@gmail.com', NULL, '$2y$10$wG0I0N0XqJ5/YL1p0kHYOeDxG1sFbfjWvOaQyGZZR1lh0n5Vp2H5O', 'customer', '2026-02-14 06:01:05'),
(4, 'Priya Singh', 'priya@gmail.com', NULL, '$2y$10$wG0I0N0XqJ5/YL1p0kHYOeDxG1sFbfjWvOaQyGZZR1lh0n5Vp2H5O', 'customer', '2026-02-14 06:01:05'),
(5, 'Dev Dixit', 'DevDixit17@gmail.com', '8569922845', '$2y$10$sprBA4uhecx3TiygZfZ8Z.81L0i.OsJBP039RrN4KzKQm/Oiwpqeq', 'customer', '2026-02-14 06:11:21'),
(6, 'Suhani Goyal', 'gsuhani433@gmail.com', NULL, '$2y$10$8kGUIFQafNv1ZrR7q90Zb.Xr75sN//MCCrgovam0kqx1in/hQv5fe', 'agency', '2026-02-14 06:15:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agency_details`
--
ALTER TABLE `agency_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`),
  ADD KEY `agency_id` (`agency_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);


--
-- AUTO_INCREMENT for table `agency_details`
--
ALTER TABLE `agency_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


--
-- Constraints for table `agency_details`
--
ALTER TABLE `agency_details`
  ADD CONSTRAINT `agency_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
