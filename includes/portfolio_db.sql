-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 04, 2025 at 03:27 PM
-- Server version: 9.1.0
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfolio_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
CREATE TABLE IF NOT EXISTS `about` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `title`, `description`, `lang`, `created_at`, `updated_at`) VALUES
(1, 'Experiences & Career', 'My job history.', 'en', '2025-06-24 06:41:08', '2025-06-26 00:52:07'),
(4, 'My Journey', 'hi', 'en', '2025-06-25 08:33:43', '2025-06-26 00:47:56'),
(3, 'Education', 'My studied.', 'en', '2025-06-24 08:22:31', '2025-06-26 00:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

DROP TABLE IF EXISTS `experiences`;
CREATE TABLE IF NOT EXISTS `experiences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `about_id` int NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `about_id` (`about_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `about_id`, `title`, `company`, `start_date`, `end_date`, `description`, `created_at`, `updated_at`, `order`) VALUES
(2, 1, 'IT Support', 'Hand 7 Garment CO.,LTD', '2025-03-10', '0000-00-00', 'Current work here', '2025-06-24 07:18:26', '2025-06-24 08:26:20', 0),
(3, 1, 'IT Support', 'Hand Seven Apparel CO.,LTD', '2024-06-10', '2025-03-10', 'Work as an IT Support here.', '2025-06-24 08:19:10', '2025-06-26 05:07:40', 2),
(4, 3, 'Management Information Systems (MIS)', 'SETEC INSTITUTE', '2019-08-23', '2023-08-23', 'I studied for a bachelor\'s degree at Setec Institute for 4 years, majoring in management information systems.', '2025-06-24 08:23:48', '2025-06-26 01:08:57', 0),
(5, 4, 'My Journey', 'Myself', '2025-06-26', '2025-06-26', 'With a background in both design and information systems, I\'ve cultivated a unique skill set that allows me to bridge the gap between technical functionality and aesthetic appeal.\r\n\r\nMy journey began in graphic design, where I developed a keen eye for visual communication. This foundation led me to explore the technical side of digital experiences, eventually specializing in IT support and web development.\r\n\r\nToday, I combine these skills to create solutions that are not only technically sound but also visually compelling and user-friendly.', '2025-06-26 00:48:02', '2025-06-26 01:30:20', 0),
(6, 3, 'Primary School', 'Angtnoat Primary School', '2006-01-01', '2012-12-31', 'Primary school, also known as elementary or grade school, is the first stage of formal education for children, typically starting around age 5 or 6 and lasting until around age 11 or 12.', '2025-06-26 00:55:03', '2025-06-26 01:15:32', 2),
(7, 3, 'High School', 'Hun Sen Angtasoam High School', '2012-01-01', '2018-08-20', 'High school, also known as secondary school or senior school, is a type of educational institution that provides secondary education, typically for students aged 12-18.', '2025-06-26 01:13:59', '2025-06-26 01:15:32', 1),
(8, 1, 'Graphic Designer', 'Reach Both Graphic Digital', '2019-01-01', '2024-06-10', 'A graphic designer creates visual concepts to communicate ideas and information.', '2025-06-26 01:19:35', '2025-06-26 05:07:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `home`
--

DROP TABLE IF EXISTS `home`;
CREATE TABLE IF NOT EXISTS `home` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `profile_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `name`, `bio`, `profile_image`, `lang`, `created_at`, `updated_at`) VALUES
(1, 'Chan Vannak', 'Bridging technology and creativity to deliver seamless digital experiences and robust IT solutions.', 'assets/uploads/vannak3.jpg', 'en', '2025-06-25 01:31:44', '2025-06-25 07:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `login_codes`
--

DROP TABLE IF EXISTS `login_codes`;
CREATE TABLE IF NOT EXISTS `login_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_codes`
--

INSERT INTO `login_codes` (`id`, `user_id`, `code`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 68, '080760', '2025-07-01 15:37:20', 1, '2025-07-01 15:27:20'),
(2, 68, '831342', '2025-07-01 15:38:20', 1, '2025-07-01 15:28:20'),
(3, 68, '108317', '2025-07-01 15:38:46', 1, '2025-07-01 15:28:46'),
(4, 68, '903979', '2025-07-01 15:39:14', 1, '2025-07-01 15:29:14'),
(5, 68, '013949', '2025-07-01 15:39:41', 1, '2025-07-01 15:29:41'),
(8, 71, '083664', '2025-07-02 07:27:08', 1, '2025-07-02 07:17:08'),
(7, 70, '604635', '2025-07-01 15:47:25', 1, '2025-07-01 15:37:25'),
(9, 72, '609751', '2025-07-02 10:11:23', 1, '2025-07-02 10:01:23'),
(10, 73, '764183', '2025-07-02 10:17:06', 1, '2025-07-02 10:07:06'),
(11, 74, '230012', '2025-07-02 10:55:01', 1, '2025-07-02 10:45:01'),
(12, 75, '939826', '2025-07-02 10:56:02', 1, '2025-07-02 10:46:02'),
(13, 77, '922264', '2025-07-02 13:08:41', 1, '2025-07-02 12:58:41'),
(14, 74, '450257', '2025-07-02 13:30:18', 1, '2025-07-02 13:20:18'),
(15, 74, '102778', '2025-07-02 13:31:46', 1, '2025-07-02 13:21:46'),
(16, 74, '761250', '2025-07-02 13:32:25', 1, '2025-07-02 13:22:25'),
(17, 78, '281396', '2025-07-02 13:59:20', 1, '2025-07-02 13:49:20'),
(18, 78, '234288', '2025-07-02 13:59:38', 1, '2025-07-02 13:49:38'),
(19, 78, '677723', '2025-07-02 13:59:56', 1, '2025-07-02 13:49:56'),
(20, 79, '076000', '2025-07-02 14:08:28', 1, '2025-07-02 13:58:28'),
(21, 80, '782218', '2025-07-02 14:24:19', 1, '2025-07-02 14:14:19'),
(22, 81, '903310', '2025-07-02 15:11:39', 1, '2025-07-02 15:01:39'),
(23, 82, '023317', '2025-07-03 11:08:44', 1, '2025-07-03 10:58:44'),
(24, 84, '585553', '2025-07-03 13:09:16', 1, '2025-07-03 12:59:16'),
(25, 85, '660077', '2025-07-03 15:00:22', 1, '2025-07-03 14:50:22'),
(26, 86, '878102', '2025-07-03 15:04:20', 1, '2025-07-03 14:54:20'),
(27, 30, '439801', '2025-07-04 20:57:15', 1, '2025-07-04 20:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `replied_at` datetime DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `reply`, `replied_at`, `is_read`, `sent_at`) VALUES
(16, 'Chan Vannak', 'khinsreynoy547@gmail.com', 'Hello', 'fgfdgdsgf', NULL, NULL, 0, '2025-07-04 14:16:05'),
(17, 'Vannak', 'vannakchan884@gmail.com', 'Hey', 'dsfadfs', NULL, NULL, 0, '2025-07-04 14:21:21'),
(18, 'Test1', 'test1@gmail.com', 'test', 'testesererew', NULL, NULL, 0, '2025-07-04 14:21:33'),
(19, 'Test', 'test@gmail.com', 'eter', 'erere', NULL, NULL, 0, '2025-07-04 14:21:44'),
(20, 'tetrt', 'you@example.com', 'ertert', 'rewrtw', NULL, NULL, 0, '2025-07-04 15:22:59'),
(21, 'afsdfasfd', 'test1@gmail.com', 'sdfasd', 'afdsadf', NULL, NULL, 0, '2025-07-04 15:23:26'),
(22, 'fsdfafasfa', 'adsa@gmail.com', 'afasfdasfasdfdafsd', 'dsafdafadfasgdafdgfdgahrythrhtrhgreqwrtrwerewtetretretretretretretwretrtar.', NULL, NULL, 0, '2025-07-04 15:24:50');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `image`, `project_link`, `lang`, `created_at`, `updated_at`) VALUES
(1, 'Graphic Design', 'Branding, Posters, ...', 'assets/uploads/1751012903_vannak1.jpg', 'https://www.behance.net/vannakchana1c6/projects', 'en', '2025-06-23 08:54:17', '2025-06-27 08:28:23'),
(2, 'Test', 'Test', 'assets/uploads/1751012908_vannak3.jpg', 'Test.com', 'en', '2025-06-24 03:44:37', '2025-06-27 08:28:28');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_title', 'My Portfolio', '2025-06-24 05:52:39'),
(2, 'email', 'vannakchan884@gmail.com', '2025-06-24 05:52:39'),
(3, 'phone', '(+855) 886004544', '2025-06-24 05:52:39'),
(4, 'description', 'Hello', '2025-06-24 06:17:15'),
(5, 'logo', 'assets/uploads/6865fa3e520ce_me.png', '2025-07-03 03:34:22'),
(6, 'facebook', 'https://www.facebook.com/chan.vannak.884/', '2025-06-24 06:16:34'),
(7, 'telegram', 'https://t.me/vannak_IT', '2025-06-24 06:16:34'),
(8, 'github', 'https://github.com/VannnakChan884', '2025-06-24 06:16:34');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `home_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `home_id` (`home_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `home_id`, `name`, `level`, `lang`, `created_at`, `updated_at`) VALUES
(6, 1, 'IT Support', '75', 'en', '2025-06-25 06:53:53', '2025-06-25 07:47:39'),
(7, 1, 'Graphic Design', '35', 'en', '2025-06-25 07:21:30', '2025-06-25 07:47:47'),
(8, 1, 'Web Development', '68', 'en', '2025-06-25 07:53:49', '2025-06-25 07:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_default_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `user_profile`, `role`, `created_at`, `updated_at`, `is_default_admin`) VALUES
(30, 'sreynoy', 'khinsreynoy547@gmail.com', '$2y$10$uH0fKtX6CIp2wEKBGMqTJOhwkyKXX.rp9sH8NT9/hRQTKAYxdEL/q', 'Khin Sreynoy', 'assets/uploads/1751522771_dcfe8bc1.jpg', 'admin', '2025-06-27 05:50:58', '2025-07-03 07:49:56', 1),
(85, 'H7 Vannak-資訊-柬1廠', 'h7ha.vannak@gmail.com', NULL, 'H7 Vannak-資訊-柬1廠', 'https://lh3.googleusercontent.com/a/ACg8ocJjy9r39tl7TiWBjrUSsyxKOq973kyhP4vB-PZGy2xOpLVbeGU=s96-c', 'admin', '2025-07-03 07:50:22', '2025-07-03 08:30:39', 0),
(86, 'Chan Vannak', 'vannakchan884@gmail.com', NULL, 'Vannak', 'https://lh3.googleusercontent.com/a/ACg8ocJs_S8o-utujtYCvNGavJ2rZurHYYwaJYkdsJ-vCuIncy-gCy1FTg=s96-c', 'admin', '2025-07-03 07:54:20', '2025-07-04 13:53:49', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
