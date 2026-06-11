-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 10:19 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_courses`
--

CREATE TABLE `academic_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_courses`
--

INSERT INTO `academic_courses` (`id`, `name`, `is_deleted`) VALUES
(1, 'BSc Software Engineering', 0),
(2, 'BSc Data Science', 0);

-- --------------------------------------------------------

--
-- Table structure for table `academic_intakes`
--

CREATE TABLE `academic_intakes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_intakes`
--

INSERT INTO `academic_intakes` (`id`, `name`, `is_deleted`) VALUES
(1, 'Intake 1 (March)', 0),
(2, 'Intake 2 (July)', 0),
(3, 'Intake 3 (November)', 0),
(4, 'Intake 4', 0);

-- --------------------------------------------------------

--
-- Table structure for table `academic_modes`
--

CREATE TABLE `academic_modes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_modes`
--

INSERT INTO `academic_modes` (`id`, `name`, `is_deleted`) VALUES
(1, 'Full-Time', 0),
(2, 'Part-Time', 0),
(3, 'Online/Distance', 0);

-- --------------------------------------------------------

--
-- Table structure for table `academic_subjects`
--

CREATE TABLE `academic_subjects` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_subjects`
--

INSERT INTO `academic_subjects` (`id`, `course_id`, `name`, `is_hidden`) VALUES
(1, 2, 'Subject A', 0),
(2, 2, 'Subject B', 0),
(3, 1, 'Subject C', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_id`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$Vic7dW3jZoyxI5xCjrekuuKLlS2hVgaSxL8S1gUPbymtWrFH1erVu', '2025-12-17 08:12:52'),
(4, '1', '$2y$10$ccLv5V.Bi4kWdVVSoocAeOhQXEvWiz5HwKTq.F13Vs9dQ0SsTA4aG', '2026-06-09 23:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `message`, `created_at`, `is_deleted`, `is_hidden`) VALUES
(10, 'Yong Ye', 'yongye@gmail.com', 'Test', '2026-06-10 07:29:17', 1, 0),
(11, '1', '1@gmail.com', '1', '2026-06-10 20:54:22', 0, 0),
(12, '2', '2@gmail.com', '2', '2026-06-10 20:54:27', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `course_offerings`
--

CREATE TABLE `course_offerings` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `intake_id` int(11) NOT NULL,
  `mode_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_offerings`
--

INSERT INTO `course_offerings` (`id`, `course_id`, `intake_id`, `mode_id`, `subject_id`, `is_hidden`) VALUES
(1, 2, 1, 3, 2, 0),
(2, 2, 2, 2, 3, 0),
(4, 2, 2, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `full_name`, `email`, `created_at`, `is_deleted`) VALUES
(1, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:31:49', 0),
(2, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:34:35', 0),
(3, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:37:01', 0),
(4, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:37:27', 0),
(5, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:37:56', 0),
(6, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:38:10', 0),
(7, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:39:12', 0),
(8, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-10 20:42:32', 0),
(11, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-11 07:23:07', 0),
(12, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-11 07:27:40', 0),
(13, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-11 08:07:37', 0),
(14, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-11 08:10:34', 0),
(15, 4, 'Tan Yong Ye', 'yongye04@gmail.com', '2026-06-11 08:14:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_items`
--

CREATE TABLE `enrollment_items` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `course_offering_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_items`
--

INSERT INTO `enrollment_items` (`id`, `enrollment_id`, `course_offering_id`, `created_at`) VALUES
(1, 11, 1, '2026-06-11 07:23:07'),
(2, 12, 1, '2026-06-11 07:27:40'),
(3, 13, 1, '2026-06-11 08:07:37'),
(4, 14, 1, '2026-06-11 08:10:34'),
(5, 15, 4, '2026-06-11 08:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_disabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`, `is_deleted`, `is_disabled`) VALUES
(4, 'Tan Yong Ye', 'yongye04@gmail.com', '$2y$10$wx4iwvpxupiTpzuSgXjKt.21xFrk3gPxR5Am7RPeTSCX2lWK4cyXO', '2026-06-09 23:12:01', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_courses`
--
ALTER TABLE `academic_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `academic_intakes`
--
ALTER TABLE `academic_intakes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `academic_modes`
--
ALTER TABLE `academic_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `academic_subjects`
--
ALTER TABLE `academic_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `intake_id` (`intake_id`),
  ADD KEY `mode_id` (`mode_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `enrollment_items`
--
ALTER TABLE `enrollment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_offering_id` (`course_offering_id`);

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
-- AUTO_INCREMENT for table `academic_courses`
--
ALTER TABLE `academic_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `academic_intakes`
--
ALTER TABLE `academic_intakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `academic_modes`
--
ALTER TABLE `academic_modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `academic_subjects`
--
ALTER TABLE `academic_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_offerings`
--
ALTER TABLE `course_offerings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `enrollment_items`
--
ALTER TABLE `enrollment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_subjects`
--
ALTER TABLE `academic_subjects`
  ADD CONSTRAINT `academic_subjects_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `academic_courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD CONSTRAINT `course_offerings_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `academic_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_offerings_ibfk_2` FOREIGN KEY (`intake_id`) REFERENCES `academic_intakes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_offerings_ibfk_3` FOREIGN KEY (`mode_id`) REFERENCES `academic_modes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_offerings_ibfk_4` FOREIGN KEY (`subject_id`) REFERENCES `academic_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollment_items`
--
ALTER TABLE `enrollment_items`
  ADD CONSTRAINT `enrollment_items_ibfk_1` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
