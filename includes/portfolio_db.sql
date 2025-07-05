-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 05, 2025 at 08:05 AM
-- Server version: 9.1.0
-- PHP Version: 8.4.0

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
(1, 'Chan Vannak', 'Bridging technology and creativity to deliver seamless digital experiences and robust IT solutions.', 'assets/uploads/me.png', 'en', '2025-06-25 01:31:44', '2025-07-04 08:23:10');

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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_codes`
--

INSERT INTO `login_codes` (`id`, `user_id`, `code`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 88, '807553', '2025-07-04 13:09:24', 1, '2025-07-04 12:59:24'),
(2, 100, '374027', '2025-07-04 13:22:23', 1, '2025-07-04 13:12:23'),
(3, 30, '423898', '2025-07-04 13:23:03', 1, '2025-07-04 13:13:03'),
(4, 106, '810883', '2025-07-04 13:52:59', 1, '2025-07-04 13:42:59'),
(5, 106, '640377', '2025-07-04 13:53:20', 1, '2025-07-04 13:43:20'),
(6, 106, '176353', '2025-07-04 13:53:35', 1, '2025-07-04 13:43:35'),
(7, 106, '057119', '2025-07-04 13:53:39', 1, '2025-07-04 13:43:39'),
(8, 106, '932481', '2025-07-04 13:53:50', 1, '2025-07-04 13:43:50'),
(9, 107, '758532', '2025-07-04 13:57:10', 1, '2025-07-04 13:47:10'),
(10, 107, '725559', '2025-07-04 13:57:19', 1, '2025-07-04 13:47:19'),
(11, 107, '924992', '2025-07-04 14:03:43', 1, '2025-07-04 13:53:43'),
(12, 107, '309098', '2025-07-04 14:04:45', 1, '2025-07-04 13:54:45'),
(13, 107, '358398', '2025-07-04 14:08:22', 1, '2025-07-04 13:58:22'),
(14, 108, '027914', '2025-07-04 14:22:53', 1, '2025-07-04 14:12:53'),
(15, 108, '667076', '2025-07-04 14:23:02', 1, '2025-07-04 14:13:02'),
(16, 108, '159771', '2025-07-04 14:26:21', 1, '2025-07-04 14:16:21'),
(17, 108, '476029', '2025-07-04 14:28:35', 1, '2025-07-04 14:18:35'),
(18, 108, '999254', '2025-07-04 14:28:47', 1, '2025-07-04 14:18:47'),
(19, 109, '972911', '2025-07-04 14:30:15', 1, '2025-07-04 14:20:15'),
(20, 110, '293538', '2025-07-04 14:37:28', 1, '2025-07-04 14:27:28'),
(21, 111, '181077', '2025-07-04 14:40:46', 1, '2025-07-04 14:30:46'),
(22, 112, '208155', '2025-07-04 14:42:38', 0, '2025-07-04 14:32:38'),
(23, 113, '982689', '2025-07-04 14:43:16', 1, '2025-07-04 14:33:16'),
(24, 113, '342140', '2025-07-04 14:45:22', 1, '2025-07-04 14:35:22'),
(25, 113, '512762', '2025-07-04 14:46:31', 1, '2025-07-04 14:36:31'),
(26, 114, '441295', '2025-07-04 14:47:21', 1, '2025-07-04 14:37:21'),
(27, 115, '934817', '2025-07-04 14:51:24', 1, '2025-07-04 14:41:24'),
(28, 116, '297207', '2025-07-04 14:55:05', 1, '2025-07-04 14:45:05'),
(29, 116, '418413', '2025-07-04 14:55:18', 1, '2025-07-04 14:45:18'),
(30, 117, '170976', '2025-07-04 14:56:30', 1, '2025-07-04 14:46:30'),
(31, 118, '979213', '2025-07-04 15:02:51', 1, '2025-07-04 14:52:51'),
(32, 119, '063079', '2025-07-04 15:03:46', 1, '2025-07-04 14:53:46'),
(33, 119, '907874', '2025-07-04 15:04:05', 1, '2025-07-04 14:54:05'),
(34, 120, '190394', '2025-07-04 15:13:17', 1, '2025-07-04 15:03:17'),
(35, 120, '314396', '2025-07-04 15:13:26', 1, '2025-07-04 15:03:26'),
(36, 102, '671285', '2025-07-04 15:17:45', 1, '2025-07-04 15:07:45'),
(37, 121, '508114', '2025-07-04 15:20:44', 1, '2025-07-04 15:10:44'),
(38, 122, '074184', '2025-07-04 15:22:06', 1, '2025-07-04 15:12:06');

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
(16, 'vannak', 'vannakchan884@gmail.com', 'Hi, admin', 'Your portfolio is perfect.', 'Thank you.', '2025-07-04 15:21:58', 1, '2025-07-04 08:20:46'),
(17, 'Shuping', 'h7ha.shuping@gmail.com', 'Hi, Vannak', 'Your portfolio is the best.', 'Wow, thank you.', '2025-07-04 15:28:47', 1, '2025-07-04 08:27:30'),
(18, 'vannak', 'vannak@gmail.com', 'Testing', 'testing auto show message.', NULL, NULL, 0, '2025-07-05 01:10:41'),
(19, 'Test', 'test@gmail.com', 'Hello admin', 'sadfafasdfdfsaf', NULL, NULL, 0, '2025-07-05 01:11:25'),
(20, 'Chan Vannak', 'dev@gmail.com', 'Hello admin', '456989', NULL, NULL, 0, '2025-07-05 01:12:30'),
(21, 'fsdfsd', 'admin@gmail.com', 'sdfads', 'dsdfasdaf', NULL, NULL, 0, '2025-07-05 01:17:30'),
(22, 'Test10', 'test10@gmail.com', 'Test10', '10', NULL, NULL, 0, '2025-07-05 01:21:08');

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
(1, 'Graphic Design', 'Branding, Posters, ...', 'assets/uploads/1751617442_NVR_1_IP27_CCTV 1_20250301125917_106521.jpg', 'https://www.behance.net/vannakchana1c6/projects', 'en', '2025-06-23 08:54:17', '2025-07-04 08:24:02'),
(2, 'Test', 'Test', 'assets/uploads/1751617490_shuping-ga.jpg', 'Test.com', 'en', '2025-06-24 03:44:37', '2025-07-04 08:24:50');

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
(1, 'site_title', 'Vanank', '2025-07-04 03:53:59'),
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
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `user_profile`, `role`, `created_at`, `updated_at`, `is_default_admin`) VALUES
(30, 'sreynoy', 'khinsreynoy547@gmail.com', '$2y$12$wI1OUcEC0LULy6i.UEbmke.iH5Zf7ifw13CKHm0EOYC9A5Ht2CuZu', 'Khin Sreynoy', 'assets/uploads/1751686208_sreynoy1.jpg', 'admin', '2025-06-27 05:50:58', '2025-07-05 03:30:08', 1),
(120, 'h7_vannak', 'h7ha.vannak@gmail.com', '$2y$12$rEIsjtwxOFrs8l7ilZWCMeD14mTpXUjJqJAxmlzMqjYZhKeOe3YTG', 'H7/Vannak-資訊-柬1廠', 'https://lh3.googleusercontent.com/a/ACg8ocJjy9r39tl7TiWBjrUSsyxKOq973kyhP4vB-PZGy2xOpLVbeGU=s96-c', 'admin', '2025-07-04 08:03:17', '2025-07-05 08:00:23', 0),
(122, 'vannak', 'vannakchan884@gmail.com', '$2y$12$SVIA2DQuV8KRJUTNFHbs.eLy8dQWwWvff1IQSjJlF7gP/vwkIC262', 'Vannak', 'assets/uploads/1751702199_me.png', 'admin', '2025-07-04 08:12:06', '2025-07-05 07:57:00', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
