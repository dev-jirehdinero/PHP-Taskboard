-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 23, 2026 at 02:08 AM
-- Server version: 8.4.3
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taskboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_log`
--

CREATE TABLE `login_log` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `success` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_log`
--

INSERT INTO `login_log` (`id`, `email`, `ip`, `success`, `created_at`) VALUES
(1, 'jireh.c@digidirect.com.au', '127.0.0.1', 0, '2026-10-22 05:21:43'),
(2, 'jireh.c@digidirect.com.au', '127.0.0.1', 0, '2026-10-22 05:21:56'),
(3, 'jireh.c@digidirect.com.au', '127.0.0.1', 0, '2026-10-22 05:21:59'),
(4, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 05:25:29'),
(5, 'test@gmail.com', '127.0.0.1', 1, '2026-10-22 05:26:51'),
(6, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 05:27:10'),
(7, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 07:04:18'),
(8, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 07:05:19'),
(9, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 07:05:27'),
(10, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-22 07:22:39'),
(11, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-23 03:54:32'),
(12, 'test@gmail.com', '127.0.0.1', 1, '2026-10-23 04:22:04'),
(13, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-23 04:22:31'),
(14, 'jireh.c@digidirect.com.au', '127.0.0.1', 0, '2026-10-23 04:24:30'),
(15, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-23 04:24:35'),
(16, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-10-23 04:31:21'),
(17, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-11-04 01:08:06'),
(18, 'testnew@gmail.com', '127.0.0.1', 1, '2026-11-04 01:09:17'),
(19, 'jireh.c@digidirect.com.au', '127.0.0.1', 0, '2026-11-23 01:55:59'),
(20, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-11-23 01:56:03'),
(21, 'cruz@gmail.com', '127.0.0.1', 1, '2026-11-23 01:59:35'),
(22, 'jireh.c@digidirect.com.au', '127.0.0.1', 1, '2026-11-23 01:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('todo','doing','done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'WordPress Development', 'wordpress development description', 'todo', '2026-10-22 04:03:10', '2026-10-22 04:03:10', NULL),
(3, 1, 'test', 'dasda', 'done', '2026-10-22 04:36:25', '2026-10-22 04:36:32', NULL),
(4, 1, 'JSON', 'dsada', 'doing', '2026-10-22 04:41:01', '2026-10-22 04:41:01', NULL),
(5, 1, 'test2', 'dsad', 'todo', '2026-10-22 07:20:38', '2026-10-22 07:20:38', NULL),
(6, 1, 'test3', 'dsada', 'doing', '2026-10-22 07:20:46', '2026-10-22 07:20:46', NULL),
(7, 1, 'test4', 'dasd', 'done', '2026-10-22 07:20:53', '2026-10-23 04:02:53', NULL),
(8, 1, 'test5', 'dsad', 'done', '2026-10-22 07:21:02', '2026-10-23 04:02:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`, `role`) VALUES
(1, 'Jireh Capao', 'jireh.c@digidirect.com.au', '$2y$12$G0eXLWcAqqYKeTXy04VZ0eE9.Q0nGRWvqXlXbKj9TySItwBjTXx3a', '2026-10-22 04:01:51', 'admin'),
(2, 'Test Account', 'test@gmail.com', '$2y$12$O8XOzMyxg9RMZ4IhkoyYYO1N4ockEC3vdQGpNiCs7XFwsHP8BHZ2a', '2026-10-22 05:26:40', 'user'),
(3, 'Testnew', 'testnew@gmail.com', '$2y$12$XkuHKN7QZxGvtLnX7i4RQeNWMs16lEiwlcsYjlO4x./JD3AnGVkI.', '2026-10-23 04:23:48', 'user'),
(4, 'DELA CRUZ JUAN P.', 'cruz@gmail.com', '$2y$12$d/GmBmd4IaJ73bzHKvmyWucsjtTYtzvCtzDou3paqqh4JpC5BWfUG', '2026-11-23 01:59:24', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attempts_email` (`email`,`attempted_at`),
  ADD KEY `idx_attempts_ip` (`ip`,`attempted_at`);

--
-- Indexes for table `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_email` (`email`,`created_at`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tasks_user` (`user_id`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login_log`
--
ALTER TABLE `login_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
