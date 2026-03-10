-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 04:53 PM
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
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_username` text NOT NULL,
  `admin_name` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `admin_email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_name`, `password`, `admin_email`) VALUES
(1, 'admin', 'admin', 'Admin@123', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_name`, `customer_email`, `room_id`, `check_in`, `check_out`, `status`) VALUES
(19, 'Sumit Poudel', 'sumitpoudel325@gmail.com', 18, '2026-01-20', '2026-01-21', 'confirmed'),
(20, 'Sumit Poudel', 'sumitpoudel325@gmail.com', 16, '2026-02-02', '2026-02-03', 'confirmed'),
(21, 'Sumit Poudel', 'sumit@gmail.com', 29, '2026-02-11', '2026-02-12', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `customer_fullname` text DEFAULT NULL,
  `customer_email` text DEFAULT NULL,
  `customer_phone` text DEFAULT NULL,
  `password` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `customer_fullname`, `customer_email`, `customer_phone`, `password`) VALUES
(6, 'Sumit Poudel', 'sumitpoudel325@gmail.com', '9745388674', 'Sumit123'),
(7, 'Jack Kallis', 'jack@gmail.com', '9705596500', 'Jack1234'),
(8, 'Sudip Kandel', 'sudip@gmail.com', '9805596501', 'Sudip1234'),
(12, 'Sumit Poudel', 'sumit@gmail.com', '9855056900', 'Sumit123');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','booked') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type`, `price`, `description`, `status`) VALUES
(16, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'booked'),
(18, 'Normal', 1000.00, 'Normal room with wifi service', 'booked'),
(25, 'Normal', 1000.00, 'Normal room with wifi service', 'available'),
(26, 'Normal', 1000.00, 'Normal room with wifi service', 'available'),
(27, 'Normal', 1000.00, 'Normal room with wifi service', 'available'),
(28, 'Normal', 1000.00, 'Normal room with wifi service', 'available'),
(29, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'booked'),
(30, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'available'),
(31, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'available'),
(32, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'available'),
(33, 'Deluxe Room', 2000.00, 'Free Wi-Fi, air conditioning, room service, daily housekeeping, TV, attached bathroom, and complimentary toiletries.', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `staff_username` text DEFAULT NULL,
  `staff_name` text NOT NULL,
  `password` text NOT NULL,
  `staff_email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_username`, `staff_name`, `password`, `staff_email`) VALUES
(3, 'Bimal', 'Bimal', 'Bimal@123', 'bimal@gmail.com'),
(4, 'Nishan', 'Nishan Sedai', 'Nishan123', 'nishan@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`) USING HASH,
  ADD UNIQUE KEY `admin_email` (`admin_email`) USING HASH;

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`),
  ADD UNIQUE KEY `customer_email` (`customer_email`) USING HASH,
  ADD UNIQUE KEY `customer_phone` (`customer_phone`) USING HASH;

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `staff_username` (`staff_username`) USING HASH,
  ADD UNIQUE KEY `staff_email` (`staff_email`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
