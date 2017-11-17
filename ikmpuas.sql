-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 17, 2017 at 07:41 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ikmpuas`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminuserlevel`
--

CREATE TABLE `adminuserlevel` (
  `aulv_id` int(11) NOT NULL,
  `aulv_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `aulv_entryuser` varchar(100) CHARACTER SET latin1 NOT NULL,
  `aulv_entrydate` datetime NOT NULL,
  `aulv_changeuser` varchar(100) CHARACTER SET latin1 NOT NULL,
  `aulv_changedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `adminuserlevel`
--

INSERT INTO `adminuserlevel` (`aulv_id`, `aulv_name`, `aulv_entryuser`, `aulv_entrydate`, `aulv_changeuser`, `aulv_changedate`) VALUES
(1, 'Admin', '', '0000-00-00 00:00:00', '', '2015-03-07 14:24:14');

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `admusr_id` int(3) NOT NULL,
  `admusr_username` varchar(60) DEFAULT NULL,
  `admusr_userpasswd` varchar(255) DEFAULT NULL,
  `admusr_userdesc` varchar(100) DEFAULT NULL,
  `admusr_aulv_id` int(11) DEFAULT NULL,
  `admusr_usrgro_id` int(11) NOT NULL,
  `admusr_user_status` enum('y','n') DEFAULT 'y',
  `admusr_entryuser` varchar(100) NOT NULL,
  `admusr_entrydate` datetime NOT NULL,
  `admusr_changeuser` varchar(100) NOT NULL,
  `admusr_changedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `admusr_image` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`admusr_id`, `admusr_username`, `admusr_userpasswd`, `admusr_userdesc`, `admusr_aulv_id`, `admusr_usrgro_id`, `admusr_user_status`, `admusr_entryuser`, `admusr_entrydate`, `admusr_changeuser`, `admusr_changedate`, `admusr_image`) VALUES
(1, 'superadmin', 'ac43724f16e9241d990427ab7c8f4228', 'superadministrator', 1, 0, 'y', '', '0000-00-00 00:00:00', '', '2017-11-16 20:02:45', '');

-- --------------------------------------------------------

--
-- Table structure for table `button`
--

CREATE TABLE `button` (
  `butt_id` int(11) NOT NULL,
  `butt_data` varchar(50) DEFAULT NULL,
  `butt_value` int(11) DEFAULT NULL,
  `butt_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `button`
--

INSERT INTO `button` (`butt_id`, `butt_data`, `butt_value`, `butt_status`) VALUES
(1, '1', 1, 'Tidak Puass'),
(2, '2', 2, 'Puas'),
(3, '3', 3, 'Sangat Puas'),
(4, '4', 4, 'haha');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id_Setting` int(10) NOT NULL,
  `setting` varchar(255) NOT NULL,
  `nilai` varchar(10) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id_Setting`, `setting`, `nilai`, `keterangan`) VALUES
(11, 'port', '3', '-'),
(12, 'baudrate', '19200', 'test'),
(200, 'shutdown', '20:52', '');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `trans_id` int(11) NOT NULL,
  `trans_tgl` datetime DEFAULT NULL,
  `trans_butt_id` int(11) DEFAULT NULL,
  `trans_comment` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`trans_id`, `trans_tgl`, `trans_butt_id`, `trans_comment`) VALUES
(1, '2017-11-15 18:03:48', 1, ''),
(2, '2017-11-16 12:11:25', 1, '-'),
(3, '2017-11-16 12:11:28', 1, '-'),
(4, '2017-11-16 12:11:31', 2, '-'),
(5, '2017-11-16 12:11:33', 2, '-'),
(6, '2017-11-16 12:11:35', 2, '-'),
(7, '2017-11-16 12:11:38', 3, '-'),
(8, '2017-11-16 12:11:40', 3, '-'),
(9, '2017-11-16 12:11:42', 3, '-'),
(10, '2017-11-16 12:11:44', 3, '-'),
(11, '2017-11-16 12:11:46', 3, '-'),
(12, '2017-11-16 12:11:48', 3, '-'),
(13, '2017-11-16 12:11:50', 3, '-'),
(14, '2017-11-16 12:11:52', 2, '-'),
(15, '2017-11-16 12:11:54', 1, '-'),
(16, '2017-11-16 12:11:56', 1, '-'),
(17, '2017-11-16 12:11:58', 1, '-'),
(18, '2017-11-16 12:12:00', 1, '-'),
(19, '2017-11-16 12:12:02', 3, '-'),
(20, '2017-11-16 12:12:04', 3, '-'),
(21, '2017-11-16 12:12:06', 3, '-'),
(22, '2017-11-16 12:12:08', 2, '-'),
(23, '2017-11-16 12:12:10', 2, '-'),
(24, '2017-11-16 12:12:12', 2, '-'),
(25, '2017-11-16 12:12:14', 2, '-'),
(26, '2017-11-16 12:12:16', 2, '-'),
(27, '2017-11-16 12:12:18', 2, '-'),
(28, '2017-11-16 12:12:20', 2, '-'),
(29, '2017-11-16 12:12:22', 2, '-'),
(30, '2017-11-16 12:12:24', 2, '-'),
(31, '2017-11-16 12:12:26', 2, '-'),
(32, '2017-11-16 12:12:28', 2, '-'),
(33, '2017-11-16 12:12:30', 2, '-'),
(34, '2017-11-16 12:12:32', 2, '-'),
(35, '2017-11-16 12:12:34', 3, '-'),
(36, '2017-11-16 12:12:36', 3, '-'),
(37, '2017-11-16 12:12:38', 3, '-'),
(38, '2017-11-16 12:12:40', 3, '-'),
(39, '2017-11-16 12:12:42', 1, '-'),
(40, '2017-11-16 12:12:44', 1, '-'),
(41, '2017-11-16 12:12:46', 1, '-'),
(42, '2017-11-16 12:12:48', 1, '-'),
(43, '2017-11-16 12:12:50', 1, '-'),
(44, '2017-11-16 12:12:52', 1, '-'),
(45, '2017-11-16 12:12:54', 2, '-'),
(46, '2017-11-16 12:12:57', 2, '-'),
(47, '2017-11-16 12:12:58', 2, '-'),
(48, '2017-11-16 12:13:00', 2, '-'),
(49, '2017-11-16 12:13:02', 2, '-'),
(50, '2017-11-16 12:13:04', 2, '-'),
(51, '2017-11-16 12:13:07', 2, '-'),
(52, '2017-11-16 12:13:08', 3, '-'),
(53, '2017-11-16 12:13:11', 3, '-'),
(54, '2017-11-16 12:13:12', 3, '-'),
(55, '2017-11-16 12:13:14', 3, '-'),
(56, '2017-11-16 12:13:17', 3, '-'),
(57, '2017-11-16 12:13:18', 3, '-'),
(58, '2017-11-16 12:13:20', 3, '-'),
(59, '2017-11-16 12:13:23', 2, '-'),
(60, '2017-11-16 12:13:25', 2, '-'),
(61, '2017-11-16 12:13:27', 2, '-'),
(62, '2017-11-16 12:13:29', 2, '-'),
(63, '2017-11-16 12:13:31', 2, '-'),
(64, '2017-11-16 12:13:33', 2, '-'),
(65, '2017-11-16 12:13:35', 2, '-'),
(66, '2017-11-16 12:13:37', 2, '-'),
(67, '2017-11-16 12:13:39', 2, '-'),
(68, '2017-11-16 12:13:41', 2, '-'),
(69, '2017-11-16 12:13:43', 2, '-'),
(70, '2017-11-16 12:13:45', 1, '-'),
(71, '2017-11-16 12:13:47', 1, '-'),
(72, '2017-11-16 12:13:49', 1, '-'),
(73, '2017-11-16 12:13:51', 1, '-'),
(74, '2017-11-16 12:13:53', 1, '-'),
(75, '2017-11-16 12:13:55', 1, '-'),
(76, '2017-11-16 12:13:57', 1, '-'),
(77, '2017-11-16 12:13:59', 1, '-'),
(78, '2017-11-16 12:14:01', 1, '-'),
(79, '2017-11-16 12:14:03', 1, '-'),
(80, '2017-11-16 12:14:05', 1, '-'),
(81, '2017-11-16 12:14:07', 1, '-'),
(82, '2017-11-16 12:14:09', 1, '-'),
(83, '2017-11-16 12:14:11', 1, '-');

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `usrlog_log_id` int(11) NOT NULL,
  `usrlog_user_id` int(9) NOT NULL DEFAULT '0' COMMENT 'User ID from table User',
  `usrlog_login_date` datetime NOT NULL COMMENT 'Login Date & time',
  `usrlog_login_ip` varchar(50) NOT NULL DEFAULT ' ' COMMENT 'Login IP Address',
  `usrlog_logout_date` datetime NOT NULL,
  `usrlog_id_loket` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Logs for User Login';

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`usrlog_log_id`, `usrlog_user_id`, `usrlog_login_date`, `usrlog_login_ip`, `usrlog_logout_date`, `usrlog_id_loket`) VALUES
(1, 1, '2017-11-17 02:53:40', '::1', '2017-11-17 03:03:28', 0),
(2, 1, '2017-11-17 03:03:33', '::1', '2017-11-17 03:03:38', 0),
(3, 1, '2017-11-17 03:03:50', '::1', '2017-11-17 04:51:40', 0),
(4, 1, '2017-11-17 04:53:26', '::1', '2017-11-17 05:13:37', 0),
(5, 1, '2017-11-17 05:15:06', '::1', '2017-11-17 05:35:16', 0),
(6, 1, '2017-11-17 05:40:57', '::1', '2017-11-17 07:27:42', 0),
(7, 1, '2017-11-17 07:27:57', '::1', '0000-00-00 00:00:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminuserlevel`
--
ALTER TABLE `adminuserlevel`
  ADD PRIMARY KEY (`aulv_id`);

--
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
  ADD PRIMARY KEY (`admusr_id`);

--
-- Indexes for table `button`
--
ALTER TABLE `button`
  ADD PRIMARY KEY (`butt_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id_Setting`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`trans_id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`usrlog_log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminuserlevel`
--
ALTER TABLE `adminuserlevel`
  MODIFY `aulv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `adminusers`
--
ALTER TABLE `adminusers`
  MODIFY `admusr_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `button`
--
ALTER TABLE `button`
  MODIFY `butt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id_Setting` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;
--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `usrlog_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
