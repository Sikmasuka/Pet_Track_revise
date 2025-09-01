-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 05:46 PM
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
-- Database: `pettrackdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_name`, `admin_password`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'vinjin', 'Jin Hobin', 'Vinjin123!');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `reason` text NOT NULL,
  `status` varchar(20) DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `duration` int(11) NOT NULL DEFAULT 90
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `owner_name`, `contact_number`, `appointment_date`, `appointment_time`, `reason`, `status`, `created_at`, `updated_at`, `duration`) VALUES
(46, 'tikoy', '091230123', '2025-08-30', '13:00:00', 'Checkup', 'Scheduled', '2025-08-24 05:37:52', NULL, 90),
(47, 'tikay', '1231242131', '2025-08-30', '15:00:00', 'Surgery', 'Scheduled', '2025-08-24 05:40:33', NULL, 90),
(48, 'James Lee', '09372841234', '2025-09-10', '14:30:00', 'Checkup', 'Scheduled', '2025-08-27 14:57:40', NULL, 90),
(49, 'Mikaykay Abecia', '532324223123', '2025-09-10', '09:30:00', 'Grooming', 'Scheduled', '2025-08-27 15:01:44', NULL, 90),
(50, 'Seongji Yeok', '093925166643', '2025-08-28', '14:30:00', 'Vaccination', 'Scheduled', '2025-08-27 15:37:48', NULL, 90);

-- --------------------------------------------------------

--
-- Table structure for table `archive`
--

CREATE TABLE `archive` (
  `id` int(11) NOT NULL,
  `original_table` varchar(50) NOT NULL,
  `original_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archive`
--

INSERT INTO `archive` (`id`, `original_table`, `original_id`, `data`, `deleted_at`) VALUES
(1, 'Pet', 15, '{\"pet_id\":15,\"pet_name\":\"tigon\",\"pet_sex\":\"Female\",\"pet_weight\":\"5.00\",\"pet_breed\":\"bulldog\",\"pet_birth_date\":\"2345-11-12\",\"client_id\":16}', '2025-07-19 00:06:14'),
(8, 'Client', 16, '{\"client_id\":16,\"client_name\":\"kogang\",\"client_address\":\"maniso\",\"client_contact_number\":\"29384956758\"}', '2025-07-20 16:18:29'),
(10, 'Client', 25, '{\"client_id\":25,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:16'),
(12, 'Client', 24, '{\"client_id\":24,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:19'),
(14, 'Client', 23, '{\"client_id\":23,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:22'),
(16, 'Client', 22, '{\"client_id\":22,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:25'),
(18, 'Client', 20, '{\"client_id\":20,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:30'),
(20, 'Client', 21, '{\"client_id\":21,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:20:32'),
(21, 'Pet', 18, '{\"pet_id\":18,\"pet_name\":\"tigok\",\"pet_sex\":\"Male\",\"pet_weight\":\"8.00\",\"pet_breed\":\"bulldog\",\"pet_birth_date\":\"2123-12-31\",\"client_id\":19}', '2025-07-20 18:59:05'),
(22, 'Client', 19, '{\"client_id\":19,\"client_name\":\"kangkong\",\"client_address\":\"barangay 6\",\"client_contact_number\":\"9828383829\"}', '2025-07-20 18:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `client_contact_number` varchar(15) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `client_name`, `client_address`, `client_contact_number`, `status`, `updated_at`) VALUES
(36, 'janpaul4', 'barangay 6', '09392516664', 1, '2025-08-21 05:46:01'),
(37, 'Odemil Uyan', 'Barangay 3, Balingasag, Misamis Oriental', '09392516664', 0, '2025-08-29 15:22:02'),
(38, 'Maria Santos', 'Purok 3, Brgy. Mabini, Balingasag, Misamis Oriental', '09123456789', 1, '2025-08-29 15:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `Log_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Role` enum('admin','veterinarian') DEFAULT NULL,
  `Action_Type` varchar(50) DEFAULT NULL,
  `Table_Affected` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`Log_ID`, `User_ID`, `Role`, `Action_Type`, `Table_Affected`, `Description`, `Timestamp`) VALUES
(111, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-15 21:16:25'),
(112, 1, NULL, 'update', 'Admin', 'janpaul updated client \'janpaul2\' with pet \'chokoy\'', '2025-08-15 21:30:14'),
(113, 1, NULL, 'update', 'Admin', 'janpaul updated client \'janpaul3\' with pet \'chokoy\'', '2025-08-15 21:30:27'),
(114, 1, NULL, 'update', 'Admin', 'janpaul updated client \'janpaul2\'', '2025-08-15 22:30:22'),
(115, 1, NULL, 'update', 'Admin', 'janpaul updated client \'janpaul3\'', '2025-08-15 22:30:30'),
(116, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-15 22:30:37'),
(117, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-16 21:03:49'),
(118, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-16 21:03:57'),
(119, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-16 22:05:35'),
(120, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-16 22:06:12'),
(121, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-16 22:08:41'),
(122, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-16 22:58:24'),
(123, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-16 23:02:13'),
(124, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-16 23:05:13'),
(125, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-16 23:24:46'),
(126, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-17 18:36:09'),
(127, 0, NULL, 'Appointment', 'Guest', 'Guest Test User2 booked an appointment', '2025-08-17 19:14:30'),
(128, 0, NULL, 'Appointment', 'Guest', 'Guest janpaul25 booked an appointment on September 11, 2025 at 3:55 PM', '2025-08-17 20:21:08'),
(129, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-17 20:21:29'),
(130, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-17 20:52:44'),
(131, 0, NULL, 'Appointment', 'Guest', 'Guest Test User234 booked an appointment on September 10, 2025 at 2:45 PM', '2025-08-17 20:55:09'),
(132, 0, NULL, 'Appointment', 'Guest', 'Guest Test User6 booked an appointment on August 20, 2025 at 2:41 PM', '2025-08-17 21:27:11'),
(133, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-17 22:16:17'),
(134, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-18 09:35:33'),
(135, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-18 09:38:51'),
(136, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-18 09:40:16'),
(137, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-18 09:57:05'),
(138, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-18 09:57:15'),
(139, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-18 10:09:36'),
(140, 1, NULL, 'Login', 'Admin', 'admin Successfully Logged in', '2025-08-18 10:28:33'),
(141, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-18 10:33:37'),
(142, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-18 10:34:07'),
(143, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-18 11:38:13'),
(144, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-18 11:39:22'),
(145, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-18 11:40:08'),
(146, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-18 11:42:47'),
(147, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-18 11:44:00'),
(148, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-18 11:44:10'),
(149, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-19 21:48:06'),
(150, 0, NULL, 'Appointment', 'Guest', 'Guest Samuel Seo booked an appointment on August 21, 2025 at 3:23 PM', '2025-08-19 21:50:19'),
(151, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-19 22:10:46'),
(152, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-20 11:01:33'),
(153, 1, NULL, 'Login', 'Veterinarian', 'Jan Paul Michael M. Dela Cera Successfully Logged in', '2025-08-20 11:03:56'),
(154, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 11:51:49'),
(155, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 11:52:03'),
(156, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 11:56:11'),
(157, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 11:56:27'),
(158, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 18:12:13'),
(159, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 18:22:24'),
(160, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 19:15:21'),
(161, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 19:25:31'),
(162, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 19:31:20'),
(163, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 19:31:41'),
(164, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 19:34:10'),
(165, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 19:59:47'),
(166, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-20 20:00:24'),
(167, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-20 20:18:32'),
(168, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-21 11:21:21'),
(169, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-21 12:49:12'),
(170, 0, NULL, 'Appointment', 'Guest', 'Guest Daniel Park booked an appointment on August 22, 2025 at 2:23 PM', '2025-08-21 12:49:46'),
(171, 0, NULL, 'Appointment', 'Guest', 'Guest Shingen Yamazaki booked an appointment on August 23, 2025 at 3:21 PM', '2025-08-21 12:52:46'),
(172, 4, NULL, 'delete', 'Admin', 'samuel archived client \'janpaul3\'', '2025-08-21 13:36:16'),
(173, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-22 08:12:41'),
(174, 4, NULL, 'update', 'Admin', 'samuel updated client \'janpaul4\'', '2025-08-22 08:13:14'),
(175, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-22 08:13:50'),
(176, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-22 08:46:25'),
(177, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-22 09:55:06'),
(178, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-24 10:25:16'),
(179, 0, NULL, 'Appointment', 'Guest', 'Guest shikoy2 booked an appointment on August 30, 2025 at 2:12 PM', '2025-08-24 11:52:19'),
(180, 0, NULL, 'Appointment', 'Guest', 'Guest tikoy booked an appointment on September 12, 2025 at 2:00 PM', '2025-08-24 12:20:51'),
(181, 0, NULL, 'Appointment', 'Guest', 'Guest tikoy booked an appointment on September 20, 2025 at 1:30 PM', '2025-08-24 12:23:08'),
(182, 0, NULL, 'Appointment', 'Guest', 'Guest tikoykoy booked an appointment on August 28, 2025 at 2:30 PM', '2025-08-24 13:12:20'),
(183, 0, NULL, 'Appointment', 'Guest', 'Guest tikoy booked an appointment on August 30, 2025 at 1:00 PM', '2025-08-24 13:37:52'),
(184, 0, NULL, 'Appointment', 'Guest', 'Guest tikay booked an appointment on August 30, 2025 at 3:00 PM', '2025-08-24 13:40:33'),
(185, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-25 00:09:31'),
(186, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-25 00:13:48'),
(187, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-25 14:06:28'),
(188, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-27 22:57:04'),
(189, 0, NULL, 'Appointment', 'Guest', 'Guest James Lee booked an appointment on September 10, 2025 at 2:30 PM', '2025-08-27 22:57:40'),
(190, 0, NULL, 'Appointment', 'Guest', 'Guest Mikaykay Abecia booked an appointment on September 10, 2025 at 9:30 AM', '2025-08-27 23:01:44'),
(191, 0, NULL, 'Appointment', 'Guest', 'Guest Seongji Yeok booked an appointment on August 28, 2025 at 2:30 PM', '2025-08-27 23:37:48'),
(192, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-28 22:53:34'),
(193, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-29 07:13:42'),
(194, 2, NULL, 'Login', 'Admin', 'vinjin Successfully Logged in', '2025-08-29 07:14:05'),
(195, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-29 07:19:23'),
(196, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-29 10:03:39'),
(197, 4, NULL, 'Login', 'Veterinarian', 'Samuel Seo Successfully Logged in', '2025-08-29 20:53:50'),
(198, 4, NULL, 'add', 'Admin', 'samuel added a new client \'Maria Santos\', pet \'Buddy\', and medical record', '2025-08-29 22:24:08'),
(199, 4, NULL, 'delete', 'Admin', 'samuel archived client \'Maria Santos\'', '2025-08-29 23:01:59'),
(200, 4, NULL, 'delete', 'Admin', 'samuel archived client \'Odemil Uyan\'', '2025-08-29 23:02:44'),
(201, 4, NULL, 'delete', 'Admin', 'samuel archived client \'Odemil Uyan\'', '2025-08-29 23:08:42'),
(202, 4, NULL, 'restore', 'Admin', 'samuel restored client \'Odemil Uyan\' and associated pets and medical records', '2025-08-29 23:22:02'),
(203, 4, NULL, 'delete', 'Admin', 'samuel archived client \'Odemil Uyan\'', '2025-08-29 23:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `medical_condition` text DEFAULT NULL,
  `medical_diagnosis` text DEFAULT NULL,
  `medical_symptoms` text DEFAULT NULL,
  `medical_treatment` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `record_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `pet_id`, `date`, `medical_condition`, `medical_diagnosis`, `medical_symptoms`, `medical_treatment`, `updated_at`, `status`, `record_date`) VALUES
(23, 35, '2025-08-04', 'I dont know', 'I dont know too', 'Vomiting', 'Apply ointment.', NULL, 1, '2025-08-04'),
(24, 36, '2025-08-04', 'Runny nose', 'Example', 'headache', 'Apply an anti nose bacterial ointment.', '2025-08-29 15:22:02', 0, '2025-08-04');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `method_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `client_name`, `method_id`, `amount`, `description`, `date`) VALUES
(1, 'Danreb B. Salvacion', 1, 1500.00, 'I want to pay it with cash.', '2025-07-09 22:23:07'),
(2, 'Jan Paul Michael M. Dela Cera', 1, 500.00, 'Payment for check ups.', '2025-07-13 21:36:04'),
(3, 'Mr. Odemil Uyan', 1, 1500.00, 'For checkup payments.', '2025-08-07 00:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `method_id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`method_id`, `method_name`) VALUES
(1, 'Cash'),
(2, 'GCash'),
(3, 'Credit Card'),
(4, 'Bank Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `pet_id` int(11) NOT NULL,
  `pet_name` varchar(100) DEFAULT NULL,
  `pet_sex` varchar(10) DEFAULT NULL,
  `pet_weight` decimal(5,2) DEFAULT NULL,
  `pet_breed` varchar(50) DEFAULT NULL,
  `pet_birth_date` date DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `pet_species` enum('Dog','Cat') NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet`
--

INSERT INTO `pet` (`pet_id`, `pet_name`, `pet_sex`, `pet_weight`, `pet_breed`, `pet_birth_date`, `client_id`, `status`, `pet_species`, `updated_at`) VALUES
(35, 'chokoy', 'Male', 15.00, 'bulldog', '2024-12-31', 36, 1, 'Dog', '2025-08-21 05:46:01'),
(36, 'Gulaman', 'Male', 7.00, 'Aspin', '2018-08-01', 37, 0, 'Cat', '2025-08-29 15:22:02'),
(37, 'Buddy', 'Male', 24.00, 'Golden Retriever', '2024-11-12', 38, 1, 'Dog', '2025-08-29 15:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `record_id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `vet_id` int(11) DEFAULT NULL,
  `time_and_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `veterinarian`
--

CREATE TABLE `veterinarian` (
  `vet_id` int(11) NOT NULL,
  `vet_name` varchar(100) DEFAULT NULL,
  `vet_contact_number` varchar(15) DEFAULT NULL,
  `vet_username` varchar(50) DEFAULT NULL,
  `vet_password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `veterinarian`
--

INSERT INTO `veterinarian` (`vet_id`, `vet_name`, `vet_contact_number`, `vet_username`, `vet_password`) VALUES
(1, 'Jan Paul Michael M. Dela Cera', '01234567890', 'janpaul', '$2y$10$8bwkTCKQjkTGJEdKfc55F.aj7KSSPseB0Bl1QGdXP07ZYRcub4xiS'),
(3, 'Mr. Osas', '09759420944', 'osas', '$2y$10$XzOK9PildBlvttODtOoY4ecxWYCnVbU3ldpwGrExT59KccUuLHWYK'),
(4, 'Samuel Seo', '01234567890', 'samuel', '$2y$10$qfUYyNHBEorgQ4zkdu7lpeE4FOq4iS/.LUqfKYMAr18JF2sA.X9Z.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`Log_ID`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_method_id` (`method_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`method_id`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`pet_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `vet_id` (`vet_id`);

--
-- Indexes for table `veterinarian`
--
ALTER TABLE `veterinarian`
  ADD PRIMARY KEY (`vet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `archive`
--
ALTER TABLE `archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pet`
--
ALTER TABLE `pet`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `veterinarian`
--
ALTER TABLE `veterinarian`
  MODIFY `vet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pet` (`pet_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_method_id` FOREIGN KEY (`method_id`) REFERENCES `payment_methods` (`method_id`);

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `medical_records` (`record_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pet` (`pet_id`),
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`vet_id`) REFERENCES `veterinarian` (`vet_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
