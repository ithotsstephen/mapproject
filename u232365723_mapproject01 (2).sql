-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 05, 2026 at 05:58 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u232365723_mapproject01`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 05:48:19'),
(2, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 05:49:47'),
(3, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 05:50:09'),
(4, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 05:51:34'),
(5, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 05:52:42'),
(6, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 06:28:24'),
(7, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 06:35:46'),
(8, 2, 'Updated Post', 'ID: 1, Title: Sample Incident Report - Delhi, Status: published', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 06:36:25'),
(9, 2, 'Updated Post', 'ID: 1, Title: Sample Incident Report - Delhi, Status: published', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 06:40:10'),
(10, 2, 'Updated Post', 'ID: 3, Title: Sample Property Damage Case - Bangalore, Status: published', '2405:201:e006:b16e:40c0:504d:b9ff:ffdc', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 06:42:00'),
(11, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:c1b0:a225:e5fd:15b5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 09:36:35'),
(12, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:c1b0:a225:e5fd:15b5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 09:40:55'),
(13, 1, 'Updated Admin', 'ID: 2', '2405:201:e006:b16e:c1b0:a225:e5fd:15b5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 09:44:21'),
(14, 1, 'Updated Admin', 'ID: 2', '2405:201:e006:b16e:c1b0:a225:e5fd:15b5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 09:45:46'),
(15, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:c1b0:a225:e5fd:15b5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 09:45:51'),
(16, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:209d:c3a:444c:4951', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-30 07:13:28'),
(17, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:01:18'),
(18, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:02:18'),
(19, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:02:24'),
(20, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:02:31'),
(21, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:02:41'),
(22, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:02:47'),
(23, 1, 'Created Admin', 'Username: RuahAdmin_Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:05:38'),
(24, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:06:23'),
(25, 3, 'Logged Out', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:06:38'),
(26, 1, 'Updated Admin', 'ID: 3', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:07:11'),
(27, 1, 'Updated Admin', 'ID: 3', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:07:38'),
(28, 1, 'Updated Admin', 'ID: 3', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:08:16'),
(29, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:12:27'),
(30, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:12:30'),
(31, 1, 'Created Category', 'Name: Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:15:29'),
(32, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:15:38'),
(33, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:16:23'),
(34, 1, 'Updated Category', 'ID: 9, Name: T1', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:16:36'),
(35, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:16:49'),
(36, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:20:15'),
(37, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:22:10'),
(38, 3, 'Accessed Profile Page', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:22:32'),
(39, 3, 'Accessed Profile Page', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:24:11'),
(40, 3, 'Created Post', 'ID: 4, Title: Sample Test #01, Status: draft', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:26:36'),
(41, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:26:42'),
(42, 3, 'Updated Post', 'ID: 4, Title: Sample Test #01, Status: published', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:29:29'),
(43, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:29:38'),
(44, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:31:41'),
(45, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:31:46'),
(46, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:32:03'),
(47, 3, 'Updated Post', 'ID: 4, Title: Sample Test #01, Status: published', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:34:00'),
(48, 3, 'Updated Post', 'ID: 4, Title: Sample Test #01, Status: published', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:36:09'),
(49, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:39:34'),
(50, 3, 'Accessed Profile Page', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:39:59'),
(51, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:40:06'),
(52, 3, 'Logged Out', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:40:11'),
(53, 1, 'Deleted Post', 'ID: 4', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:41:46'),
(54, 1, 'Deleted Post', 'ID: 4', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 11:44:15'),
(55, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:42:33'),
(56, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:49:00'),
(57, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:49:17'),
(58, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:49:24'),
(59, 1, 'Logged Out', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:49:46'),
(60, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:50:14'),
(61, 3, 'Accessed Profile Page', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:51:33'),
(62, 3, 'Accessed Profile Page', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:52:28'),
(63, 3, 'Created Post', 'ID: 5, Title: Test Post, Status: draft', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:54:15'),
(64, 3, 'Logged Out', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:54:55'),
(65, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:55:14'),
(66, 3, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:56:03'),
(67, 3, 'Updated Post', 'ID: 5, Title: Test Post, Status: published', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:57:04'),
(68, 3, 'Logged Out', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:58:19'),
(69, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:58:33'),
(70, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:58:43'),
(71, 1, 'Accessed Dashboard', '', '103.21.78.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:58:48'),
(72, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:44:04'),
(73, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:45'),
(74, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:52'),
(75, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:52'),
(76, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:52'),
(77, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:52'),
(78, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:52'),
(79, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:53'),
(80, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:53'),
(81, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:53'),
(82, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:53'),
(83, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:47:53'),
(84, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:48:02'),
(85, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:52:48'),
(86, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:53:43'),
(87, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:59:19'),
(88, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 03:59:40'),
(89, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:26:18'),
(90, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:26:31'),
(91, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:27:09'),
(92, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:30:18'),
(93, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:34:07'),
(94, 1, 'Created Post', 'ID: 6, Title: Test Novembe 7, Status: draft', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:35:00'),
(95, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:35:03'),
(96, 1, 'Updated Post', 'ID: 6, Title: Test Novembe 7, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:35:34'),
(97, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:42:49'),
(98, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:49:42'),
(99, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:49:58'),
(100, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:50:11'),
(101, 1, 'Created Post', 'ID: 7, Title: tst1, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:50:39'),
(102, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:50:58'),
(103, 1, 'Created Post', 'ID: 8, Title: AA, Status: draft', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:51:19'),
(104, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:51:22'),
(105, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:51:31'),
(106, 1, 'Updated Post', 'ID: 8, Title: AA, Status: admin_approval', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:51:54'),
(107, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:52:08'),
(108, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 04:53:53'),
(109, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:03:04'),
(110, 1, 'Updated Post', 'ID: 8, Title: AA, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:03:15'),
(111, 1, 'Created Post', 'ID: 9, Title: BB, Status: draft', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:03:57'),
(112, 1, 'Updated Post', 'ID: 9, Title: BB, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:04:31'),
(113, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:04:46'),
(114, 1, 'Created Post', 'ID: 10, Title: CC, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:05:06'),
(115, 1, 'Deleted Post', 'ID: 10', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:30'),
(116, 1, 'Deleted Post', 'ID: 9', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:32'),
(117, 1, 'Deleted Post', 'ID: 8', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:35'),
(118, 1, 'Deleted Post', 'ID: 7', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:38'),
(119, 1, 'Deleted Post', 'ID: 6', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:40'),
(120, 1, 'Deleted Post', 'ID: 5', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:43'),
(121, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:11:52'),
(122, 1, 'Created Post', 'ID: 11, Title: qq, Status: draft', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:12:20'),
(123, 1, 'Deleted Post', 'ID: 5', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:12:24'),
(124, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:12:36'),
(125, 1, 'Created Post', 'ID: 12, Title: zz, Status: admin_approval', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:12:54'),
(126, 1, 'Deleted Post', 'ID: 5', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:13:01'),
(127, 1, 'Updated Post', 'ID: 11, Title: qq, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:13:35'),
(128, 1, 'Updated Post', 'ID: 12, Title: zz, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:13:47'),
(129, 1, 'Updated Post', 'ID: 12, Title: zz, Status: draft', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:14:15'),
(130, 1, 'Updated Post', 'ID: 12, Title: zz, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 05:14:46'),
(131, 1, 'Created Post', 'ID: 13, Title: Test, Status: admin_approval', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 06:40:38'),
(132, 1, 'Updated Post', 'ID: 13, Title: Test, Status: published', '2405:201:e006:b16e:3f55:f8a0:387d:8de7', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 06:40:51'),
(133, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:17:15'),
(134, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:18:01'),
(135, 2, 'Created Post', 'ID: 14, Title: Test New, Status: admin_approval', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:19:43'),
(136, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:20:01'),
(137, 1, 'Updated Post', 'ID: 14, Title: Test New, Status: published', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:20:36'),
(138, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:21:11'),
(139, 1, 'Created Post', 'ID: 15, Title: WW, Status: draft', '2405:201:e006:b16e:49a6:c091:80e8:e6f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-07 11:26:15'),
(140, 2, 'Accessed Dashboard', '', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:03:07'),
(141, 2, 'Created Post', 'ID: 16, Title: Jarkan02, Status: admin_approval', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:04:00'),
(142, 2, 'Created Post', 'ID: 17, Title: Jraan03, Status: admin_approval', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:04:35'),
(143, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:05:14'),
(144, 1, 'Bulk Updated Post Status', 'Status: published, Count: 2', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:05:33'),
(145, 1, 'Bulk Updated Post Status', 'Status: published, Count: 1', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:10:35'),
(146, 1, 'Updated Post', 'ID: 17, Title: Jraan03, Status: published', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:16:14'),
(147, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:28:17'),
(148, 1, 'Updated Post', 'ID: 17, Title: Jraan03, Status: published', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:37:33'),
(149, 1, 'Updated Post', 'ID: 17, Title: Jraan03, Status: published', '2405:201:e006:b16e:500a:73e0:3407:4a40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-11-11 18:39:04'),
(150, 3, 'Accessed Dashboard', '', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:52:05'),
(151, 3, 'Accessed Profile Page', '', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:55:19'),
(152, 3, 'Created Post', 'ID: 18, Title: Test Incident - Lynching, Status: draft', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:04:19'),
(153, 3, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: draft', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:05:50'),
(154, 3, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: admin_approval', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:06:06'),
(155, 1, 'Accessed Dashboard', '', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:06:35'),
(156, 1, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: published', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:08:12'),
(157, 1, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: draft', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:14:35'),
(158, 1, 'Accessed Dashboard', '', '103.21.78.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 10:15:39'),
(159, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 14:50:00'),
(160, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 14:50:19'),
(161, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:02:58'),
(162, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:14'),
(163, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:17'),
(164, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:19'),
(165, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:21'),
(166, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:31'),
(167, 3, 'Updated Profile', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:55'),
(168, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:55'),
(169, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:06:59'),
(170, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:07:01'),
(171, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:08:03'),
(172, 3, 'Logged Out', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:08:21'),
(173, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:08:42'),
(174, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:09:52'),
(175, 3, 'Updated Profile', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:14'),
(176, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:14'),
(177, 3, 'Updated Profile', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:36'),
(178, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:36'),
(179, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:44'),
(180, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:10:46'),
(181, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:11:04'),
(182, 3, 'Created Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:37:38'),
(183, 3, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:42:37'),
(184, 3, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:43:23'),
(185, 3, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:43:30'),
(186, 3, 'Created Post', 'ID: 20, Title: lorem ipsum, Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:44:02'),
(187, 3, 'Updated Post', 'ID: 20, Title: lorem ipsum, Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:44:06'),
(188, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:44:34'),
(189, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:47:32'),
(190, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:47:45'),
(191, 3, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:57:46'),
(192, 3, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: admin_approval', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 15:57:55'),
(193, 3, 'Updated Post', 'ID: 20, Title: lorem ipsum, Status: admin_approval', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 16:01:02'),
(194, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 16:01:08'),
(195, 3, 'Logged Out', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-27 16:02:22'),
(196, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:04:32'),
(197, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:08:16'),
(198, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:08:18'),
(199, 3, 'Updated Profile', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:08:30'),
(200, 3, 'Accessed Profile Page', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:08:30'),
(201, 3, 'Logged Out', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:08:34'),
(202, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:09:06'),
(203, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:10:48'),
(204, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:10:51'),
(205, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:10:54'),
(206, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:11:09'),
(207, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:11:30'),
(208, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:11:49'),
(209, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:12:42'),
(210, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:12:43'),
(211, 1, 'Created Admin', 'Username: Dummy-Test', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:14:20'),
(212, 1, 'Updated Admin', 'ID: 4', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:14:28'),
(213, 1, 'Updated Admin', 'ID: 4', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:14:58'),
(216, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:15:29'),
(217, 1, 'Updated Admin', 'ID: 4', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:16:00'),
(220, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:16:48'),
(221, 1, 'Updated Admin', 'ID: 4', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:16:59'),
(222, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:17:09'),
(223, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:19:01'),
(224, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:19:07'),
(225, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:19:11'),
(226, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:20:30'),
(227, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:22:30'),
(228, 1, 'Updated Category', 'ID: 9, Name: Test', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:22:57'),
(229, 1, 'Updated Category', 'ID: 9, Name: Test A', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:23:49'),
(230, 1, 'Created Category', 'Name: Test B', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:24:15'),
(231, 3, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:24:50'),
(232, 3, 'Logged Out', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:25:19'),
(233, 1, 'Accessed Dashboard', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:25:32'),
(234, 1, 'Updated Category', 'ID: 1, Name: Religious Violence', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:30:51'),
(235, 1, 'Updated Category', 'ID: 1, Name: Religious Violence', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:43:58'),
(236, 1, 'Updated Post', 'ID: 20, Title: Sample lorem ipsum, Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:48:22'),
(237, 1, 'Updated Post', 'ID: 20, Title: Sample lorem ipsum, Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:48:27'),
(238, 1, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: draft', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:49:28');
INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(239, 1, 'Updated Post', 'ID: 11, Title: Test qq, Status: published', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:50:17'),
(240, 1, 'Deleted Post', 'ID: 20', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:52:10'),
(241, 1, 'Deleted Post', 'ID: 20', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:52:14'),
(242, 1, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION), Status: published', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:53:01'),
(243, 1, 'Logged Out', '', '103.21.78.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-28 10:56:54'),
(244, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:31:23'),
(245, 1, 'Updated Post', 'ID: 18, Title: Test Incident - Lynching, Status: published', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:41:31'),
(246, 1, 'Updated Post', 'ID: 19, Title: Persecution - Property Violence (FICTION)sas, Status: published', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:41:44'),
(247, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:41:57'),
(248, 1, 'Created Category', 'Name: Fight', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:42:31'),
(249, 1, 'Created Admin', 'Username: sujitha', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:43:38'),
(250, 5, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 05:45:17'),
(251, 5, 'Created Post', 'ID: 21, Title: Sujitha Test, Status: admin_approval', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 05:47:40'),
(252, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:47:45'),
(253, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:47:48'),
(254, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:47:50'),
(255, 1, 'Updated Post', 'ID: 21, Title: Sujitha Test, Status: published', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:48:42'),
(256, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:55:11'),
(257, 1, 'Deleted Post', 'ID: 17', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:55:25'),
(258, 1, 'Deleted Post', 'ID: 16', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:55:32'),
(259, 1, 'Bulk Deleted Posts', 'Count: 5', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:55:41'),
(260, 1, 'Deleted Post', 'ID: 16', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:55:41'),
(261, 1, 'Bulk Deleted Posts', 'Count: 2', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:56:05'),
(262, 1, 'Deleted Post', 'ID: 16', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:56:05'),
(263, 1, 'Accessed Dashboard', '', '2405:201:e006:b16e:6b91:cb4:1293:8968', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-12-19 05:56:32'),
(264, 1, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:02:03'),
(265, 1, 'Accessed Profile Page', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:03:02'),
(266, 1, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:03:14'),
(267, 1, 'Logged Out', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:03:27'),
(268, 5, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:03:44'),
(269, 5, 'Updated Post', 'ID: 21, Title: Sujitha Test, Status: admin_approval', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:04:13'),
(270, 1, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:05:02'),
(271, 5, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:05:50'),
(272, 1, 'Updated Admin', 'ID: 5', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:06:58'),
(273, 1, 'Created Category', 'Name: ChildAbuse', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:07:50'),
(274, 1, 'Updated Post', 'ID: 21, Title: Sujitha Testing, Status: draft', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:09:56'),
(275, 1, 'Updated Post', 'ID: 21, Title: Sujitha Testing, Status: draft', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:10:01'),
(276, 1, 'Updated Post', 'ID: 21, Title: Sujitha Testing, Status: published', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 06:10:27'),
(277, 5, 'Created Post', 'ID: 22, Title: Abuse, Status: admin_approval', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:13:27'),
(278, 5, 'Accessed Dashboard', '', '122.173.247.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-19 06:29:23'),
(279, 1, 'Accessed Dashboard', '', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-24 04:54:16'),
(280, 5, 'Created Post', 'ID: 23, Title: child, Status: draft', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-24 04:55:14'),
(281, 1, 'Accessed Dashboard', '', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-24 04:55:28'),
(282, 1, 'Accessed Dashboard', '', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-24 04:55:47'),
(283, 1, 'Updated Post', 'ID: 23, Title: child - abuse, Status: draft', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-24 04:56:17'),
(284, 5, 'Accessed Dashboard', '', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-24 04:56:27'),
(285, 1, 'Deleted Post', 'ID: 23', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-24 05:00:36'),
(286, 5, 'Accessed Dashboard', '', '122.178.94.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-24 05:01:18'),
(287, 1, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:04:54'),
(288, 1, 'Updated Admin', 'ID: 4', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:05:26'),
(289, 1, 'Deleted Admin', 'ID: 4', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:10:09'),
(290, 1, 'Updated Admin', 'ID: 2', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:12:20'),
(291, 1, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:12:32'),
(292, 1, 'Deleted Category', 'ID: 9', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:13:46'),
(293, 1, 'Deleted Category', 'ID: 2', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:13:52'),
(294, 1, 'Updated Admin', 'ID: 3', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:17:11'),
(295, 1, 'Updated Admin', 'ID: 3', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:17:18'),
(296, 1, 'Updated Admin', 'ID: 3', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:19:05'),
(297, 1, 'Updated Admin', 'ID: 3', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:19:08'),
(298, 1, 'Updated Admin', 'ID: 2', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:22:39'),
(299, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:22:44'),
(300, 2, 'Created Post', 'ID: 24, Title: Stephen Test, Status: draft', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:24:00'),
(301, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:24:04'),
(302, 1, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: published', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:24:44'),
(303, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:25:00'),
(304, 2, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: draft', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:25:23'),
(305, 1, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: published', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:25:48'),
(306, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:27:10'),
(307, 2, 'Created Post', 'ID: 25, Title: tet, Status: draft', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:27:41'),
(308, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:28:04'),
(309, 2, 'Updated Post', 'ID: 25, Title: tet, Status: admin_approval', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:28:21'),
(310, 1, 'Deleted Post', 'ID: 25', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:28:37'),
(311, 1, 'Deleted Post', 'ID: 25', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:28:46'),
(312, 1, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: draft', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:29:16'),
(313, 1, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: draft', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:35:16'),
(314, 2, 'Updated Post', 'ID: 24, Title: Stephen Test, Status: admin_approval', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:36:20'),
(315, 2, 'Accessed Dashboard', '', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:36:27'),
(316, 1, 'Deleted Post', 'ID: 24', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:36:43'),
(317, 1, 'Deleted Post', 'ID: 24', '2405:201:e006:b206:9fb0:dca1:5f49:d975', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-07 18:52:04'),
(318, 1, 'Accessed Dashboard', '', '2405:201:e006:b206:547:1eb9:ba28:149', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-08 08:36:09'),
(319, 1, 'Created Admin', 'Username: Test', '2405:201:e006:b206:547:1eb9:ba28:149', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2026-01-08 08:39:40'),
(320, 1, 'Accessed Dashboard', '', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:41:18'),
(321, 1, 'Deleted Admin', 'ID: 6', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:41:48'),
(322, 1, 'Deleted Category', 'ID: 10', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:43:12'),
(323, 1, 'Logged Out', '', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:48:41'),
(324, 1, 'Accessed Dashboard', '', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:51:49'),
(325, 1, 'Updated Category', 'ID: 5, Name: Property Destruction', '103.224.32.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-08 08:55:08'),
(326, 1, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:03:45'),
(327, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:05:18'),
(328, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:10:27'),
(329, 2, 'Accessed Dashboard', '', '120.138.12.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:10:44'),
(330, 2, 'Created Post', 'ID: 26, Title: Test, Status: admin_approval', '120.138.12.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:12:05'),
(331, 1, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:13:16'),
(332, 1, 'Updated Post', 'ID: 26, Title: Test, Status: published', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:13:44'),
(333, 1, 'Deleted Post', 'ID: 26', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:14:02'),
(334, 1, 'Deleted Category', 'ID: 12', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:20:12'),
(335, 1, 'Deleted Category', 'ID: 12', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:20:17'),
(336, 1, 'Updated Category', 'ID: 1, Name: Religious Violence', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:20:40'),
(337, 1, 'Updated Category', 'ID: 5, Name: Property Destruction', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:20:48'),
(338, 2, 'Created Post', 'ID: 27, Title: tt, Status: draft', '120.138.12.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:27:10'),
(339, 2, 'Accessed Dashboard', '', '120.138.12.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:27:40'),
(340, 2, 'Accessed Dashboard', '', '120.138.12.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 06:28:18'),
(341, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:28:19'),
(342, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:28:28'),
(343, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:28:32'),
(344, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:31:23'),
(345, 2, 'Accessed Dashboard', '', '122.178.163.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-22 06:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Religious Violence', 'Incidents involving religious persecution and violence', 'active', '2025-10-27 05:24:56', '2026-01-22 06:20:40'),
(3, 'Discrimination', 'Cases of social, economic, or institutional discrimination', 'active', '2025-10-27 05:24:56', '2025-10-27 05:24:56'),
(4, 'Mob Violence', 'Incidents involving mob attacks and lynching', 'active', '2025-10-27 05:24:56', '2025-10-27 05:24:56'),
(5, 'Property Destruction', 'Destruction of religious or personal property', 'active', '2025-10-27 05:24:56', '2026-01-22 06:20:48'),
(6, 'Forced Conversion', 'Cases of forced religious conversion', 'active', '2025-10-27 05:24:56', '2025-10-27 05:24:56'),
(7, 'Legal Harassment', 'Misuse of legal system for persecution', 'active', '2025-10-27 05:24:56', '2025-10-27 05:24:56'),
(8, 'Social Boycott', 'Social and economic boycotts', 'active', '2025-10-27 05:24:56', '2025-10-27 05:24:56'),
(11, 'Fight', '', 'active', '2025-12-19 05:42:31', '2025-12-19 05:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `short_message` text NOT NULL,
  `detailed_message` longtext NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `featured_image_path` varchar(500) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_path` varchar(500) DEFAULT NULL,
  `external_links` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `short_message`, `detailed_message`, `category_id`, `admin_id`, `state`, `district`, `incident_date`, `latitude`, `longitude`, `featured_image_path`, `image_path`, `video_path`, `external_links`, `tags`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sample Incident Report - Delhi', 'This is a sample incident report to demonstrate the system functionality.', 'This is a detailed sample report that shows how incident details are stored and displayed in the system. This is for demonstration purposes only and should be replaced with actual incident data.', 1, 2, 'Delhi', 'Central Delhi', '2024-01-15', NULL, NULL, 'uploads/images/6901b5e965b0c_9903fdf14a95b458.jpg', 'uploads/images/6901b6ca79dd5_36e732170cd40582.jpg', 'uploads/videos/6901b6ca7a5c6_fb561c847008486b.mp4', '', '', 'published', '2025-10-27 05:24:56', '2025-10-29 06:40:10'),
(18, 'Test Incident - Lynching', 'Lynching that occurred in Rajasthan', 'this is the detailed report of the lynching that occurred in Rajasthan. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 4, 3, 'Rajasthan', 'Jaipur', '2025-11-10', 26.91962000, 75.78781000, 'uploads/images/69145ba3150f1_52fdbf44185acf78.png', NULL, NULL, 'https://en.wikipedia.org/wiki/Lorem_ipsum', 'violence, lynching, physical abuse', 'published', '2025-11-12 10:04:19', '2025-12-19 05:41:31'),
(19, 'Persecution - Property Violence (FICTION)sas', 'A small Christian community center in Dharwad district, Karnataka was vandalized on 12 November 2025 by unidentified individuals. The incident involved property destruction but no physical injuries. FIR registered and Police has begun investigation.', '1. Incident Overview:\r\nIncident Type: Property-related violence\r\nNature of Incident: Vandalism, forced entry, destruction of fixtures\r\nImpact: Damage to property; no casualties reported\r\nPerpetrators: Unidentified group (as per initial police report)\r\nStatus: FIR registered; investigation ongoing\r\n\r\n2. Description of Events:\r\nEarly morning on 12 August 2025, residents noticed broken windows and damaged furniture inside the Grace Community Center (fictional). Entry appeared to have been forced through the rear door. Several items used for community programs—chairs, notice boards, sound equipment, and educational materials—were found destroyed or scattered.\r\n\r\nLocal volunteers reported seeing three unidentified individuals leaving the area on a motorcycle shortly before the damage was discovered. CCTV footage from a nearby shop captured blurred images of the vehicle but not the faces of the riders.\r\n\r\nPolice visited the site, collected evidence, and filed a First Information Report under sections related to trespassing and property destruction.', 5, 3, 'Karnataka', 'Dharwad', '2025-11-12', 15.44710000, 75.01290000, 'uploads/images/6928704271521_32437df3927f73a6.png', 'uploads/images/69287042733a0_482cc4130db0cc08.png', 'uploads/videos/6928716d2c9dd_4c7a413025601146.mp4', 'https://news-test.example/dharwad-community-center-incident\r\n\r\nhttps://localreport.example/property-case-karnataka', 'Property Violence, Vandalism, Trespassing', 'published', '2025-11-27 15:37:38', '2025-12-19 05:41:44'),
(21, 'Sujitha Testing', 'Testing Sujitha', 'Mobile Violence', 4, 5, 'Kerala', 'Kochin', '2025-12-19', NULL, -2.00000000, 'uploads/images/6944e6fcaae69_e39dbe4759a9be37.jpg', NULL, NULL, '', '', 'published', '2025-12-19 05:47:40', '2025-12-19 06:10:27'),
(22, 'Abuse', 'Testing', 'Testing', 11, 5, 'Kerala', 'Kochin', '2025-12-18', NULL, NULL, 'uploads/images/6944ed07cde2e_1fab3b8adbf293d2.png', 'uploads/images/6944ed07ce1aa_12742612be7cefdd.png', NULL, '', '', '', '2025-12-19 06:13:27', '2025-12-19 06:13:27'),
(27, 'tt', 'Hi', 'Test', 11, 2, 'Arunachal Pradesh', '', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'draft', '2026-01-22 06:27:10', '2026-01-22 06:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `post_images`
--

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Andhra Pradesh', 'AP', '2025-10-27 05:24:56'),
(2, 'Arunachal Pradesh', 'AR', '2025-10-27 05:24:56'),
(3, 'Assam', 'AS', '2025-10-27 05:24:56'),
(4, 'Bihar', 'BR', '2025-10-27 05:24:56'),
(5, 'Chhattisgarh', 'CG', '2025-10-27 05:24:56'),
(6, 'Goa', 'GA', '2025-10-27 05:24:56'),
(7, 'Gujarat', 'GJ', '2025-10-27 05:24:56'),
(8, 'Haryana', 'HR', '2025-10-27 05:24:56'),
(9, 'Himachal Pradesh', 'HP', '2025-10-27 05:24:56'),
(10, 'Jharkhand', 'JH', '2025-10-27 05:24:56'),
(11, 'Karnataka', 'KA', '2025-10-27 05:24:56'),
(12, 'Kerala', 'KL', '2025-10-27 05:24:56'),
(13, 'Madhya Pradesh', 'MP', '2025-10-27 05:24:56'),
(14, 'Maharashtra', 'MH', '2025-10-27 05:24:56'),
(15, 'Manipur', 'MN', '2025-10-27 05:24:56'),
(16, 'Meghalaya', 'ML', '2025-10-27 05:24:56'),
(17, 'Mizoram', 'MZ', '2025-10-27 05:24:56'),
(18, 'Nagaland', 'NL', '2025-10-27 05:24:56'),
(19, 'Odisha', 'OR', '2025-10-27 05:24:56'),
(20, 'Punjab', 'PB', '2025-10-27 05:24:56'),
(21, 'Rajasthan', 'RJ', '2025-10-27 05:24:56'),
(22, 'Sikkim', 'SK', '2025-10-27 05:24:56'),
(23, 'Tamil Nadu', 'TN', '2025-10-27 05:24:56'),
(24, 'Telangana', 'TG', '2025-10-27 05:24:56'),
(25, 'Tripura', 'TR', '2025-10-27 05:24:56'),
(26, 'Uttar Pradesh', 'UP', '2025-10-27 05:24:56'),
(27, 'Uttarakhand', 'UK', '2025-10-27 05:24:56'),
(28, 'West Bengal', 'WB', '2025-10-27 05:24:56'),
(29, 'Delhi', 'DL', '2025-10-27 05:24:56'),
(30, 'Jammu and Kashmir', 'JK', '2025-10-27 05:24:56'),
(31, 'Ladakh', 'LA', '2025-10-27 05:24:56'),
(32, 'Chandigarh', 'CH', '2025-10-27 05:24:56'),
(33, 'Dadra and Nagar Haveli and Daman and Diu', 'DN', '2025-10-27 05:24:56'),
(34, 'Lakshadweep', 'LD', '2025-10-27 05:24:56'),
(35, 'Puducherry', 'PY', '2025-10-27 05:24:56'),
(36, 'Andaman and Nicobar Islands', 'AN', '2025-10-27 05:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','super_admin') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'Project Administrator', 'projectadmin', 'admin@projectsdemo.link', '$2y$10$DlLv9J5ew9IoPXvigE43N.w1CYWPupP3jQXVeXOnwhZO2.vfnN74.', 'super_admin', 'active', '2025-10-27 05:24:56', '2026-01-22 06:03:45', '2026-01-22 06:03:45'),
(2, 'Demo Admin User', 'demoAdmin', 'demoadmin@projectsdemo.link', '$2y$10$ZbwlBuGqkyRK0RMMlUcMXeCRfkT2FprGBiXYGs7tqzVZRMH733Z/6', 'admin', 'active', '2025-10-27 05:24:56', '2026-01-22 06:10:44', '2026-01-22 06:10:44'),
(3, 'Ruah Admin Test', 'RuahAdmin_Test', 'johann@axlerate.com', '$2y$10$4kUycks3nAmiv.G1Cd1NNus.8svgswa9ENonFRPHsf466SPld1Vjm', 'admin', 'active', '2025-10-30 11:05:38', '2026-01-07 18:17:18', '2025-11-28 10:24:50'),
(5, 'Sujitha', 'sujitha', 'admin@ithots.com', '$2y$10$Ruf2/0ssgwj65LfTyazc0O.A3oUDkmo0i7NQnNFB5LHy4q5ylHIQ6', 'admin', 'active', '2025-12-19 05:43:38', '2025-12-19 06:03:44', '2025-12-19 06:03:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_posts_state` (`state`),
  ADD KEY `idx_posts_status` (`status`),
  ADD KEY `idx_posts_category` (`category_id`),
  ADD KEY `idx_posts_admin` (`admin_id`),
  ADD KEY `idx_posts_created` (`created_at`);

--
-- Indexes for table `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=346;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
