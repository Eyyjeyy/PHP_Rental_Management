-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 06:44 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `tenants_id` int(11) NOT NULL,
  `vacancydate` date NOT NULL,
  `archive_users_username` varchar(200) NOT NULL,
  `archive_users_firstname` varchar(200) NOT NULL,
  `archive_users_middlename` varchar(200) NOT NULL,
  `archive_users_lastname` varchar(200) NOT NULL,
  `archive_users_password` varchar(200) NOT NULL,
  `archive_users_email` varchar(200) NOT NULL,
  `archive_users_phonenumber` varchar(200) NOT NULL,
  `archive_houses_house_name` varchar(50) NOT NULL,
  `archive_tenants_date_start` varchar(100) NOT NULL,
  `archive_tenants_date_preferred` varchar(100) NOT NULL,
  `archive_contracts_filename` varchar(200) NOT NULL,
  `archive_contracts_fileurl` varchar(200) NOT NULL,
  `archive_contracts_datestart` varchar(200) NOT NULL,
  `archive_contracts_expirationdate` varchar(200) NOT NULL,
  `archive_contracts_upload_date` varchar(200) NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(50) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'studio');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(50) NOT NULL,
  `tenants_id` int(50) NOT NULL,
  `adminname` varchar(500) NOT NULL,
  `tenantname` varchar(500) NOT NULL,
  `filename` varchar(500) NOT NULL,
  `fileurl` varchar(500) NOT NULL,
  `tenantapproval` varchar(50) NOT NULL,
  `datestart` date DEFAULT current_timestamp(),
  `expirationdate` date NOT NULL DEFAULT current_timestamp(),
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contract_images`
--

CREATE TABLE `contract_images` (
  `id` int(11) NOT NULL,
  `physical_contract_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `id` int(11) NOT NULL,
  `tenantid` int(11) NOT NULL,
  `adminid` int(11) NOT NULL,
  `deposit_filepath` varchar(500) NOT NULL,
  `houses_id` int(11) NOT NULL,
  `depositamount` int(11) NOT NULL,
  `depositdate` date NOT NULL DEFAULT current_timestamp(),
  `approval` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `eviction_popup`
--

CREATE TABLE `eviction_popup` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `seen` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `eviction_setting`
--

CREATE TABLE `eviction_setting` (
  `id` int(11) NOT NULL,
  `landlord_address` varchar(200) NOT NULL,
  `days_to_pay` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `eviction_setting`
--

INSERT INTO `eviction_setting` (`id`, `landlord_address`, `days_to_pay`) VALUES
(1, 'Red Dragon Compound', 35);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `info` varchar(50) NOT NULL,
  `amount` float NOT NULL,
  `house_id` int(11) DEFAULT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `houseaccounts`
--

CREATE TABLE `houseaccounts` (
  `id` int(50) NOT NULL,
  `houses_id` int(50) NOT NULL,
  `elec_accname` varchar(50) NOT NULL,
  `elec_accnum` int(50) NOT NULL,
  `water_accname` varchar(50) NOT NULL,
  `water_accnum` int(50) NOT NULL,
  `gcash` varchar(50) NOT NULL,
  `bank` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `houseaccounts`
--

INSERT INTO `houseaccounts` (`id`, `houses_id`, `elec_accname`, `elec_accnum`, `water_accname`, `water_accnum`, `gcash`, `bank`) VALUES
(1, 1, 'serpentmeralco', 2147483647, 'serpentmaynilad', 2147483647, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `id` int(50) NOT NULL,
  `house_name` varchar(100) NOT NULL,
  `price` double NOT NULL,
  `category_id` int(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `date_registered` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`id`, `house_name`, `price`, `category_id`, `address`, `date_registered`) VALUES
(1, 'Serpent Lodging', 565, 1, 'Hongkong Disneyland', '2024-01-02'),
(2, 'Cable House', 565, 1, 'Hongkong Disneyland', '2024-01-11');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(50) NOT NULL,
  `sender_id` int(50) NOT NULL,
  `receiver_id` int(50) NOT NULL,
  `users_id` int(50) NOT NULL,
  `message` varchar(500) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `paper_categories`
--

CREATE TABLE `paper_categories` (
  `id` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `paper_files`
--

CREATE TABLE `paper_files` (
  `id` int(50) NOT NULL,
  `category_id` int(50) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `file_name` varchar(500) NOT NULL,
  `file_url` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` double NOT NULL,
  `tenants_id` int(50) NOT NULL,
  `houses_id` int(50) NOT NULL,
  `filepath` varchar(200) NOT NULL,
  `date_payment` date NOT NULL,
  `approval` varchar(50) NOT NULL,
  `archive` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `physical_contracts`
--

CREATE TABLE `physical_contracts` (
  `id` int(150) NOT NULL,
  `tenantid` int(150) NOT NULL,
  `adminid` int(150) NOT NULL,
  `fileurl` varchar(500) NOT NULL,
  `datestart` date NOT NULL DEFAULT current_timestamp(),
  `expirationdate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `users_id` varchar(50) NOT NULL,
  `users_username` varchar(50) NOT NULL,
  `house_id` int(50) NOT NULL,
  `house_category` varchar(50) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `date_preferred` date DEFAULT NULL,
  `notification_sent_months` int(11) NOT NULL,
  `reminder_sent_months` int(22) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `Date` datetime NOT NULL DEFAULT current_timestamp(),
  `phonenumber` varchar(15) NOT NULL,
  `otp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `middlename`, `lastname`, `password`, `email`, `role`, `Date`, `phonenumber`, `otp`) VALUES
(1, 'admin', 'John', 'Reese', 'Milton', '1234567', 'YourEmail@gmail.com', 'admin', '2025-03-18 01:34:41', '09xxxxxxxxx', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contract_images`
--
ALTER TABLE `contract_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposit`
--
ALTER TABLE `deposit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eviction_popup`
--
ALTER TABLE `eviction_popup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eviction_setting`
--
ALTER TABLE `eviction_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `houseaccounts`
--
ALTER TABLE `houseaccounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id_fk` (`category_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paper_categories`
--
ALTER TABLE `paper_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paper_files`
--
ALTER TABLE `paper_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id_fk` (`category_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `physical_contracts`
--
ALTER TABLE `physical_contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_images`
--
ALTER TABLE `contract_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposit`
--
ALTER TABLE `deposit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eviction_popup`
--
ALTER TABLE `eviction_popup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eviction_setting`
--
ALTER TABLE `eviction_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `houseaccounts`
--
ALTER TABLE `houseaccounts`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `houses`
--
ALTER TABLE `houses`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paper_categories`
--
ALTER TABLE `paper_categories`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paper_files`
--
ALTER TABLE `paper_files`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physical_contracts`
--
ALTER TABLE `physical_contracts`
  MODIFY `id` int(150) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `houses`
--
ALTER TABLE `houses`
  ADD CONSTRAINT `houses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
