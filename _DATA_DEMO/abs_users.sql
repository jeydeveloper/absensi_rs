-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2017 at 02:12 AM
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
-- Table structure for table `abs_users`
--

CREATE TABLE `abs_users` (
  `usr_id` int(11) NOT NULL,
  `usr_username` varchar(100) NOT NULL,
  `usr_password` varchar(100) NOT NULL,
  `usr_void` tinyint(4) NOT NULL,
  `usr_last_login` datetime NOT NULL,
  `usr_created_at` datetime NOT NULL,
  `usr_updated_at` datetime NOT NULL,
  `usr_role_id` int(11) NOT NULL,
  `usr_emp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `abs_users`
--

INSERT INTO `abs_users` (`usr_id`, `usr_username`, `usr_password`, `usr_void`, `usr_last_login`, `usr_created_at`, `usr_updated_at`, `usr_role_id`, `usr_emp_id`) VALUES
(1, 'superadmin', 'fd2a239fe44b59e90a8feba7bffb9ba8', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0),
(2, 'admin1', 'ac43724f16e9241d990427ab7c8f4228', 0, '0000-00-00 00:00:00', '2017-08-14 16:54:22', '2017-08-16 23:58:09', 1, 1),
(3, 'admin2', 'ac43724f16e9241d990427ab7c8f4228', 0, '0000-00-00 00:00:00', '2017-08-14 16:58:04', '2017-08-16 23:31:23', 2, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abs_users`
--
ALTER TABLE `abs_users`
  ADD PRIMARY KEY (`usr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abs_users`
--
ALTER TABLE `abs_users`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
