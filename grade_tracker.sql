-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 27, 2025 at 08:01 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grade_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `student_id` int NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`,`course_code`),
  KEY `course_code` (`course_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`student_id`, `course_code`, `grade`, `status`, `date`) VALUES
(1, 'EEY4189', 'B', 'Pass', '2025-05-27 23:19:45'),
(1, 'EEX4373', 'A+', 'Pass', '2025-05-27 23:19:45'),
(2, 'EEY4189', 'B', 'Pass', '2025-05-27 23:20:57'),
(3, 'EEX5464', 'B', 'Pass', '2025-05-27 23:56:30'),
(3, 'EEI6756', 'B+', 'Pass', '2025-05-28 01:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `phone`, `password`, `profile_photo`) VALUES
(1, 'uma', 'uma@gmail.com', '0716485917', '$2y$10$cn.cnCCBUxbugi3rUp5E1OLwMRXz8D.8lWzeX7Neo97biD59QT87K', 'Capture.PNG'),
(2, 'chama', 'c@gmail.com', '0716485917', '$2y$10$v0X4WX/Dtbu.8kluRW3zaed7PwB7ulzZzVTwiPR9.aHSrReO2WMxi', NULL),
(3, 'sithu', 's@gmail.com', '0716485917', '$2y$10$FW6Vv6edoIdXIcc8/s6uI.ZMESRTZkRk8yOyv/CuqUNouNssTlg4e', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

DROP TABLE IF EXISTS `student_subject`;
CREATE TABLE IF NOT EXISTS `student_subject` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`id`, `student_id`, `course_code`, `subject_name`) VALUES
(2, 1, 'EEX4373', 'Data Science'),
(3, 1, 'EEY4189', 'group project'),
(4, 2, 'EEY4189', 'group project'),
(5, 3, 'EEX5464', NULL),
(6, 3, 'EEY4189', NULL),
(7, 2, 'EEX4373', NULL),
(12, 2, 'EEI6756', 'Software'),
(13, 3, 'EEI6756', 'Software');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int NOT NULL AUTO_INCREMENT,
  `course_code` varchar(50) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `course_code`, `subject_name`, `teacher_id`) VALUES
(3, 'EEX4373', 'Data Science', 9),
(2, 'EEY4189', 'group project', 9),
(4, 'EEX5464', 'Data Communication', 9),
(5, 'EEI6756', 'Software', 11),
(6, 'EEY5432', 'Development', 11);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `teacher_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  PRIMARY KEY (`teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `name`, `email`, `phone`, `password`, `profile_photo`) VALUES
(8, 'Sama I', 'sama@gmail.com', '0716485717', '$2y$10$Tcd.KSZCtOjwRvNP4mlGB.pDpuC.mnfNODZnEvM.LCGkZX.ulpspi', 'WhatsApp Image 2023-03-09 at 2.50.32 PM.jpeg'),
(7, 'kala', 'kala@gmail.com', '0716485917', '$2y$10$yrrI/CqSnO1fCDsN.ERodODt2043BThTOftMKxsXmB6L54PGp3/Eu', ''),
(9, 'mala', 'm@gmail.com', '0716485917', '$2y$10$NNnwJCQhP7De2W2PvABDYub0zEX21bioJ5aBBv9FGSfGlsEKbEMza', 'WhatsApp Image 2023-03-09 at 2.50.32 PM.jpeg'),
(11, 'priya', 'p@gmail.com', '0716485917', '$2y$10$NicKwOEazRR/064dTsFwrueDVRgfx2oE.5EqvQx2wUADBlfcDbkj.', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
