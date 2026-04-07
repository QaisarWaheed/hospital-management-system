-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2022 at 09:44 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ycdo`
--

-- --------------------------------------------------------

--
-- Table structure for table `branchs`
--

CREATE TABLE `branchs` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `tag_name` varchar(2) NOT NULL DEFAULT ' ',
  `phone` varchar(11) NOT NULL,
  `address` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branchs`
--

INSERT INTO `branchs` (`id`, `name`, `tag_name`, `phone`, `address`, `status`, `created`) VALUES
(1, 'YCDO HEALTH CARE CENTER AND EYE HOSPITAL', 'HP', '03032827007', 'MASOOM SHAH ROAD, MULTAN', 1, '2022-03-31 10:00:34'),
(2, 'YCDO HOSPITAL', 'HA', '03022827777', 'HASSAN ABAD, MULTAN', 1, '2022-03-31 10:00:34'),
(3, 'YCDO HOSPITAL', 'HP', '03072827777', 'HASAN PARWANA, MULTAN', 1, '2022-03-31 10:00:34'),
(4, 'YCDO HEALTH CARE CENTER', 'DB', '03042827777', 'DAIRA BASTI, MULTAN', 1, '2022-03-31 10:00:34'),
(5, 'YCDO HOSPITAL', 'RP', '03082827777', 'RANGEEL PUR, MULTAN', 1, '2022-03-31 10:00:34'),
(6, 'YCDO HOSPITAL', 'QP', '03041110222', 'QASIM PUR, MULTAN', 1, '2022-03-31 10:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`, `created`) VALUES
(1, 'MEDICANE', 1, '2022-03-07 07:21:11'),
(2, 'TEST', 1, '2022-03-07 07:21:11'),
(3, 'PROCEDURE', 1, '2022-03-07 07:21:11'),
(4, 'INJECTION', 1, '2022-03-26 19:52:20'),
(5, 'SYRUP', 1, '2022-03-26 19:52:20'),
(6, 'DROPS', 1, '2022-03-26 19:53:24'),
(7, 'TUBE', 1, '2022-03-26 20:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `reg_patient_id` int(11) NOT NULL,
  `accupation` text NOT NULL,
  `duration` int(3) NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `amount` int(11) NOT NULL,
  `other` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `reg_patient_id`, `accupation`, `duration`, `start_date`, `amount`, `other`, `status`, `created`) VALUES
(1, 4, '', 0, '0000-00-00', 0, NULL, 0, '2022-03-31 14:18:58'),
(2, 7, 'eng', 1, '0000-00-00', 0, NULL, 0, '2022-03-31 14:46:26'),
(3, 8, 'software', 1, '0000-00-00', 1500, NULL, 0, '2022-03-31 14:49:15'),
(4, 9, 'govt job', 1, '2022-04-01', 1500, NULL, 0, '2022-03-31 14:51:22'),
(5, 10, 'mobile', 1, '2022-04-01', 2500, NULL, 0, '2022-03-31 14:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

CREATE TABLE `feeds` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `feed_value` varchar(3) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `barcode` text DEFAULT NULL,
  `retail` float NOT NULL,
  `deserving` float NOT NULL,
  `poor` float NOT NULL,
  `member` float NOT NULL,
  `general` float NOT NULL,
  `min_limit` int(11) NOT NULL DEFAULT 0,
  `max_limit` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category_id`, `name`, `barcode`, `retail`, `deserving`, `poor`, `member`, `general`, `min_limit`, `max_limit`, `status`, `created`) VALUES
(1, 1, 'avail', NULL, 2, 0, 1.5, 1.7, 1.8, 100, 500, 1, '2022-03-31 16:35:23'),
(2, 1, 'Penadol', NULL, 2, 0, 1.5, 1.7, 1.8, 200, 1000, 1, '2022-03-31 16:35:23'),
(3, 1, 'Disprine', NULL, 3, 0, 2, 2.2, 2.5, 100, 500, 1, '2022-03-31 16:36:48'),
(4, 1, 'Insed', NULL, 5, 0, 3.5, 3.8, 4, 10, 150, 1, '2022-03-31 16:36:48'),
(5, 1, 'Septrane', NULL, 5, 0, 3, 4.2, 4.5, 10, 100, 1, '2022-03-31 16:40:44'),
(6, 1, 'Brufine', NULL, 3, 0, 2.5, 2.8, 2.9, 100, 1000, 1, '2022-03-31 16:40:44'),
(7, 2, 'Suger', NULL, 100, 0, 50, 60, 70, 0, 0, 1, '2022-03-31 16:43:11'),
(8, 2, 'Meleria', NULL, 200, 0, 80, 100, 120, 0, 0, 1, '2022-03-31 16:43:11'),
(9, 2, 'Ultra Sound', NULL, 500, 0, 100, 120, 150, 0, 0, 1, '2022-03-31 16:43:11'),
(10, 2, 'Urine', NULL, 400, 0, 250, 260, 270, 0, 0, 1, '2022-03-31 16:44:25'),
(11, 2, 'Tifide', NULL, 300, 0, 180, 200, 220, 0, 0, 1, '2022-03-31 16:44:25'),
(12, 2, 'Blood Test', NULL, 700, 0, 300, 320, 350, 0, 0, 1, '2022-03-31 16:44:25'),
(13, 3, 'Skin Change', NULL, 15000, 0, 5000, 5500, 8000, 0, 0, 1, '2022-03-31 16:46:44'),
(14, 3, 'Kidny Transplant', NULL, 100000, 0, 50000, 55000, 80000, 0, 0, 1, '2022-03-31 16:47:36'),
(15, 3, 'Bypass', NULL, 10000, 0, 3000, 3400, 5000, 0, 0, 1, '2022-03-31 16:48:17'),
(16, 6, 'files', '', 1, 0, 1, 1, 1, 100, 1000, 1, '2022-04-12 07:16:43'),
(17, 1, 'images', '', 12.5, 0.1, 10.5, 10.8, 11.5, 10, 100, 1, '2022-04-12 07:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `item_by_doctor`
--

CREATE TABLE `item_by_doctor` (
  `id` int(11) NOT NULL,
  `tokan_no` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `dose` tinyint(4) NOT NULL,
  `feed` float NOT NULL,
  `days` smallint(6) NOT NULL,
  `fix_dose` tinyint(4) NOT NULL DEFAULT 0,
  `doctor_id` int(11) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_by_doctor`
--

INSERT INTO `item_by_doctor` (`id`, `tokan_no`, `item_id`, `dose`, `feed`, `days`, `fix_dose`, `doctor_id`, `branch_id`, `user_id`, `status`, `created`) VALUES
(11, 17, 1, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 19:26:38'),
(13, 17, 5, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 19:31:31'),
(14, 17, 7, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 19:31:42'),
(15, 17, 10, 1, 1, 10, 0, 2, 1, 1, 2, '2022-04-01 19:34:13'),
(17, 18, 1, 1, 2, 1, 0, 2, 1, 1, 2, '2022-04-01 20:22:43'),
(18, 18, 8, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 20:23:40'),
(19, 18, 2, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 20:23:47'),
(20, 18, 4, 1, 1, 1, 0, 2, 1, 1, 2, '2022-04-01 20:23:52'),
(22, 19, 3, 1, 1, 1, 0, 1, 1, 1, 2, '2022-04-02 09:09:04'),
(23, 20, 5, 1, 1, 1, 0, 0, 1, 1, 2, '2022-04-02 13:25:54'),
(24, 20, 3, 2, 3, 1, 0, 0, 1, 1, 2, '2022-04-02 14:57:07'),
(25, NULL, 6, 1, 1, 1, 0, NULL, 1, 1, 1, '2022-04-05 18:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `item_register_to_branches`
--

CREATE TABLE `item_register_to_branches` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_limit` int(11) NOT NULL DEFAULT 0,
  `max_limit` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `item_register_to_branches`
--

INSERT INTO `item_register_to_branches` (`id`, `item_id`, `branch_id`, `quantity`, `min_limit`, `max_limit`, `status`, `created`, `user_id`) VALUES
(1, 1, 1, 10, 2, 10, 1, '2022-03-31 16:51:53', 0),
(2, 2, 1, 100, 10, 100, 1, '2022-03-31 16:51:53', 0),
(3, 3, 1, 150, 10, 100, 1, '2022-03-31 16:51:53', 0),
(4, 4, 1, 150, 10, 100, 1, '2022-03-31 16:51:53', 0),
(5, 5, 1, 140, 10, 150, 1, '2022-03-31 16:51:53', 0),
(6, 6, 1, 20, 4, 20, 1, '2022-03-31 16:51:53', 0),
(7, 7, 1, 50, 5, 20, 1, '2022-03-31 16:51:53', 0),
(8, 8, 1, 50, 10, 20, 1, '2022-03-31 16:51:53', 0),
(9, 9, 1, 150, 10, 100, 1, '2022-03-31 16:51:53', 0),
(10, 10, 1, 150, 10, 100, 1, '2022-03-31 16:51:53', 0),
(11, 11, 2, 1, 5, 0, 1, '2022-03-31 16:51:53', 0),
(12, 12, 2, 1200, 100, 1000, 1, '2022-03-31 16:51:53', 0),
(13, 13, 2, 1500, 100, 1000, 1, '2022-03-31 16:51:53', 0),
(14, 14, 2, 800, 150, 1500, 1, '2022-03-31 16:51:53', 0),
(15, 15, 2, 15000, 10000, 40000, 1, '2022-03-31 16:51:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `reg_patient_id` int(11) NOT NULL,
  `duration` int(2) NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `amount` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `reg_patient_id`, `duration`, `start_date`, `amount`, `status`, `created`) VALUES
(1, 5, 0, '0000-00-00', 0, 1, '2022-03-31 14:21:02'),
(2, 6, 0, '0000-00-00', 0, 1, '2022-03-31 14:42:24'),
(3, 11, 1, '2022-04-01', 1000, 1, '2022-03-31 14:58:57');

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` varchar(11) NOT NULL,
  `address` text NOT NULL,
  `total_bill` float DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `parties`
--

INSERT INTO `parties` (`id`, `name`, `phone`, `address`, `total_bill`, `status`, `created`) VALUES
(1, 'ahmad', '03057629149', 'rajan pur', 0, 1, '2022-04-12 07:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `last_name` text DEFAULT NULL,
  `cnic` varchar(13) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `age` int(3) NOT NULL DEFAULT 0,
  `address` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ref_name` text DEFAULT NULL,
  `ref_phone` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `last_name`, `cnic`, `phone`, `age`, `address`, `image`, `gender`, `status`, `created`, `ref_name`, `ref_phone`) VALUES
(1, 'sana ullah', NULL, NULL, NULL, 20, NULL, NULL, 2, 1, '2022-03-31 12:40:34', NULL, NULL),
(2, 'Ahmad khan', NULL, NULL, NULL, 1, NULL, NULL, 2, 1, '2022-03-31 12:42:02', NULL, NULL),
(3, 'SANA', NULL, NULL, NULL, 20, NULL, NULL, 1, 1, '2022-03-31 12:57:47', NULL, NULL),
(4, 'sana ullah', NULL, NULL, NULL, 25, NULL, NULL, 2, 1, '2022-03-31 14:18:57', NULL, NULL),
(5, 'ali hamza', NULL, NULL, NULL, 24, NULL, NULL, 2, 1, '2022-03-31 14:21:02', NULL, NULL),
(6, 'khan', NULL, NULL, NULL, 20, NULL, NULL, 2, 1, '2022-03-31 14:42:23', NULL, NULL),
(7, 'baqir', NULL, NULL, NULL, 28, NULL, NULL, 2, 1, '2022-03-31 14:46:25', NULL, NULL),
(8, 'sana ullah', NULL, NULL, NULL, 25, NULL, NULL, 2, 1, '2022-03-31 14:49:15', NULL, NULL),
(9, 'arbab', NULL, NULL, NULL, 15, NULL, NULL, 2, 1, '2022-03-31 14:51:22', NULL, NULL),
(10, 'sanaullah', NULL, NULL, NULL, 22, NULL, NULL, 2, 1, '2022-03-31 14:54:23', NULL, NULL),
(11, 'saima', NULL, NULL, NULL, 20, NULL, NULL, 1, 1, '2022-03-31 14:58:57', NULL, NULL),
(12, 'sana ullah', NULL, NULL, NULL, 15, NULL, NULL, 2, 1, '2022-03-31 15:03:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `batch_no` text NOT NULL,
  `warrantee` text DEFAULT NULL,
  `mfg_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `tolal_price` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `per_item_price` float NOT NULL,
  `mm_id` int(11) NOT NULL,
  `sm_id` int(11) DEFAULT NULL,
  `tries` tinyint(4) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `party_id`, `item_id`, `batch_no`, `warrantee`, `mfg_date`, `expiry_date`, `tolal_price`, `quantity`, `per_item_price`, `mm_id`, `sm_id`, `tries`, `status`, `created`) VALUES
(1, 1, 1, '15001', NULL, '2022-03-01', '2022-08-31', 1500, 100, 15, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(2, 1, 2, '15504', NULL, '2022-03-01', '2022-08-31', 2500, 250, 10, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(3, 1, 3, '15007', NULL, '2022-03-01', '2022-11-30', 8500, 100, 850, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(4, 1, 4, '15440', NULL, '2021-12-01', '2023-03-01', 1000, 100, 10, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(5, 1, 5, '15008', NULL, '2022-01-01', '2023-01-31', 15000, 1000, 15, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(6, 1, 6, '15284', NULL, '2021-10-01', '2023-11-30', 100, 10, 10, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(7, 1, 7, '52487', NULL, '2021-04-01', '2024-01-31', 4000, 400, 10, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(8, 1, 8, '3544', NULL, '2022-03-01', '2024-03-31', 14000, 280, 50, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(9, 1, 9, '14225', NULL, '2021-12-01', '2025-12-01', 9000, 1000, 9, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(10, 1, 10, '15220', NULL, '2010-12-10', '2030-12-10', 8000, 10, 800, 0, NULL, 1, 1, '2022-03-31 18:53:50'),
(11, 1, 11, '14250', NULL, '2021-12-01', '2025-12-01', 15000, 1000, 15, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(12, 1, 12, '15240', NULL, '2022-01-01', '2026-01-01', 1500, 1, 1500, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(13, 1, 13, '2524', NULL, '2015-01-01', '2025-01-01', 1500, 100, 15, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(14, 1, 14, '21578', NULL, '2015-01-01', '2030-01-01', 5000, 5000, 1, 1, NULL, 1, 1, '2022-03-31 18:53:50'),
(15, 1, 15, '1571', NULL, '2019-12-01', '2030-12-01', 15000, 10, 1500, 1, NULL, 1, 1, '2022-03-31 18:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `reg_patients`
--

CREATE TABLE `reg_patients` (
  `id` int(11) NOT NULL,
  `tokan_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `last_name` text DEFAULT NULL,
  `cnic` varchar(13) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `age` int(3) NOT NULL DEFAULT 0,
  `address` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ref_name` text DEFAULT NULL,
  `ref_phone` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reg_patients`
--

INSERT INTO `reg_patients` (`id`, `tokan_id`, `name`, `last_name`, `cnic`, `phone`, `age`, `address`, `image`, `gender`, `status`, `created`, `ref_name`, `ref_phone`) VALUES
(4, 0, 'sana ullah', 'khalil ahmad shahid', '3240329792713', '03367767600', 25, 'kot mithan', NULL, 2, 1, '2022-03-31 14:18:57', 'no name', '00000000000'),
(5, 0, 'ali hamza', 'shoaid', '3240329792715', '03352648521', 24, 'kot mithan', NULL, 2, 1, '2022-03-31 14:21:01', 'sana ullah', '03367767600'),
(6, 0, 'khan', 'BABA', '3240329795418', '03307767600', 20, 'KOT MITHAN', NULL, 2, 1, '2022-03-31 14:42:23', 'SANA ULLAH', '03367767600'),
(7, 0, 'baqir', 'khan', '3240518452481', '03027584251', 28, 'kot mithan', NULL, 2, 1, '2022-03-31 14:46:25', 'sana ullah', '03367767600'),
(8, 0, 'sana ullah', 'khalil ahmad', '3240629792718', '03351167600', 25, 'kot mithan', NULL, 2, 1, '2022-03-31 14:49:15', 'ali hamza', '03364497800'),
(9, 0, 'arbab', 'haider', '3240215784251', '03321548750', 15, 'multan', NULL, 2, 1, '2022-03-31 14:51:21', 'sana ullah', '03367767600'),
(10, 0, 'sanaullah', 'khalil', '3240328154873', '03367878100', 22, 'rajan pur', NULL, 2, 1, '2022-03-31 14:54:23', 'ali', '03057619742'),
(11, 0, 'saima', 'khan', '3240516597514', '03367845100', 20, 'multan', NULL, 1, 1, '2022-03-31 14:58:56', 'sana ', '03052975842'),
(12, 0, 'sana ullah', 'khalil ahmad', '3240628451852', '03367794900', 15, 'mithan kot', NULL, 2, 1, '2022-03-31 15:03:24', 'ali', '03301542845');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `status`, `created`) VALUES
(1, 'ADMIN', 1, '2022-03-31 12:47:17'),
(2, 'RECEPTIONIST', 1, '2022-03-31 12:47:17'),
(3, 'DOCTOR', 1, '2022-03-31 12:47:17'),
(4, 'STORE MANAGER', 1, '2022-03-31 12:47:17'),
(5, 'STORE MANAGER ASSISSTANT', 1, '2022-03-31 12:47:17'),
(6, 'MEDICINE MANGER', 1, '2022-03-31 12:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `no` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tokans`
--

CREATE TABLE `tokans` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `tokan_type_id` int(11) NOT NULL,
  `cash` int(11) NOT NULL DEFAULT 0,
  `cash_received` int(11) NOT NULL,
  `previous_tokan_no` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tokans`
--

INSERT INTO `tokans` (`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`, `cash_received`, `previous_tokan_no`, `user_id`, `status`, `created`) VALUES
(1, 1, 1, 2, 0, 30, NULL, 1, 1, '2022-03-31 12:40:34'),
(2, 2, 2, 3, 0, 50, NULL, 1, 1, '2022-03-31 12:42:03'),
(3, 3, 3, 1, 0, 10, NULL, 1, 1, '2022-03-31 12:57:47'),
(4, 4, 3, 100, 0, 0, NULL, 1, 1, '2022-03-31 14:18:58'),
(5, 5, 0, 101, 0, 100, NULL, 1, 1, '2022-03-31 14:21:02'),
(6, 6, 0, 101, 0, 100, NULL, 1, 1, '2022-03-31 14:42:24'),
(7, 7, 0, 100, 0, 100, NULL, 1, 1, '2022-03-31 14:46:26'),
(8, 8, 0, 100, 0, 100, NULL, 1, 1, '2022-03-31 14:49:16'),
(9, 9, 0, 100, 0, 100, NULL, 1, 1, '2022-03-31 14:51:23'),
(10, 10, 0, 100, 0, 100, NULL, 1, 1, '2022-03-31 14:54:24'),
(11, 11, 3, 101, 0, 100, NULL, 1, 1, '2022-03-31 14:58:57'),
(12, 12, 3, 8, 0, 100, NULL, 1, 1, '2022-03-31 15:03:25'),
(13, 5, 2, 2, 2776, 2800, 2, 1, 1, '2022-04-01 20:07:31'),
(14, 1, 1, 2, 2776, 8000, 1, 1, 1, '2022-04-01 20:08:23'),
(15, 2, 2, 2, 2776, 3000, 2, 1, 1, '2022-04-01 20:13:49'),
(16, 2, 2, 2, 2776, 1000, 2, 1, 1, '2022-04-01 20:19:28'),
(17, 2, 2, 2, 2776, 10000, 2, 1, 1, '2022-04-01 20:20:11'),
(18, 2, 2, 2, 128, 130, 2, 1, 1, '2022-04-01 20:25:14'),
(19, 1, 1, 2, 3, 5, 1, 1, 1, '2022-04-02 09:09:28'),
(20, 5, 0, 2, 10, 10, 5, 1, 1, '2022-04-03 14:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `tokan_types`
--

CREATE TABLE `tokan_types` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tokan_types`
--

INSERT INTO `tokan_types` (`id`, `title`, `status`, `created`) VALUES
(1, 'Poor', 1, '2022-03-24 14:34:35'),
(2, 'General', 1, '2022-03-24 14:34:35'),
(3, 'Private', 1, '2022-03-24 14:34:35'),
(4, 'Urgent', 1, '2022-03-24 14:34:35'),
(5, 'Cons-G', 1, '2022-03-24 14:34:35'),
(6, 'Cons-M', 1, '2022-03-24 14:34:35'),
(7, 'Cons-P', 1, '2022-03-24 14:34:35'),
(8, 'Deserving', 1, '2022-03-31 14:09:44'),
(9, 'member', 1, '2022-04-01 19:52:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `u_name` text NOT NULL,
  `emp_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `password` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `branch_id` int(11) NOT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `u_name`, `emp_id`, `role_id`, `password`, `status`, `created`, `branch_id`, `is_admin`) VALUES
(1, 'Admin', 0, 1, '3b712de48137572f3849aabd5666a4e3', 1, '2022-03-04 14:19:32', 1, 1),
(2, 'DR AHMAD', 1, 3, '3b712de48137572f3849aabd5666a4e3', 1, '2022-03-31 12:47:55', 1, 1),
(3, 'DR IKHLAQ AHMAD', 2, 3, '3b712de48137572f3849aabd5666a4e3', 1, '2022-03-31 12:48:22', 1, 1),
(4, 'DR AGHA MOHSIN', 3, 3, '3b712de48137572f3849aabd5666a4e3', 1, '2022-03-31 12:56:24', 3, 1),
(5, 'DR RUBAB', 4, 3, '3b712de48137572f3849aabd5666a4e3', 1, '2022-03-31 12:57:09', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branchs`
--
ALTER TABLE `branchs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feeds`
--
ALTER TABLE `feeds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_with_item` (`category_id`);

--
-- Indexes for table `item_by_doctor`
--
ALTER TABLE `item_by_doctor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_register_to_branches`
--
ALTER TABLE `item_register_to_branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reg_patients`
--
ALTER TABLE `reg_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokans`
--
ALTER TABLE `tokans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokan_types`
--
ALTER TABLE `tokan_types`
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
-- AUTO_INCREMENT for table `branchs`
--
ALTER TABLE `branchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feeds`
--
ALTER TABLE `feeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `item_by_doctor`
--
ALTER TABLE `item_by_doctor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `item_register_to_branches`
--
ALTER TABLE `item_register_to_branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reg_patients`
--
ALTER TABLE `reg_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tokans`
--
ALTER TABLE `tokans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tokan_types`
--
ALTER TABLE `tokan_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `category_with_item` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
