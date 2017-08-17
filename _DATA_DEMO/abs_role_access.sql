-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2017 at 02:13 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `abs_role_access`
--

CREATE TABLE `abs_role_access` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_privilege` text NOT NULL,
  `role_void` tinyint(4) NOT NULL,
  `role_created_at` datetime NOT NULL,
  `role_updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `abs_role_access`
--

INSERT INTO `abs_role_access` (`role_id`, `role_name`, `role_privilege`, `role_void`, `role_created_at`, `role_updated_at`) VALUES
(1, 'All Module Access', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16', 0, '2017-08-14 16:44:58', '2017-08-17 00:01:03'),
(2, 'Limited Module Access', '1,3,7,8,13', 0, '2017-08-14 16:45:19', '2017-08-16 23:57:50'),
(3, 'Test', '1,2,3,12', 0, '2017-08-14 18:06:34', '2017-08-15 07:19:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abs_role_access`
--
ALTER TABLE `abs_role_access`
  ADD PRIMARY KEY (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abs_role_access`
--
ALTER TABLE `abs_role_access`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
