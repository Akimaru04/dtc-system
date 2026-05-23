-- -------------------------------------------------------------
-- TablePlus 7.0.6(706)
--
-- https://tableplus.com/
--
-- Database: dtc_system
-- Generation Time: 2026-05-23 21:56:54.5860
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `announcement_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `announcement_type` enum('general','holiday','maintenance','emergency') COLLATE utf8mb4_general_ci DEFAULT 'general',
  `is_active` tinyint(1) DEFAULT '1',
  `posted_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`),
  KEY `posted_by` (`posted_by`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `appointment_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `queue_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `appointment_status` enum('scheduled','completed','missed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`),
  KEY `request_id` (`request_id`),
  KEY `idx_appointment_date` (`appointment_date`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `document_requests` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `document_requests`;
CREATE TABLE `document_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `document_type_id` int NOT NULL,
  `purpose` text COLLATE utf8mb4_general_ci,
  `quantity` int DEFAULT '1',
  `tracking_code` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending_payment','payment_uploaded','payment_verified','processing','ready_for_pickup','claimed','rejected','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending_payment',
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expected_release_date` date DEFAULT NULL,
  `claim_date` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`request_id`),
  UNIQUE KEY `tracking_code` (`tracking_code`),
  KEY `user_id` (`user_id`),
  KEY `document_type_id` (`document_type_id`),
  KEY `idx_tracking_code` (`tracking_code`),
  KEY `idx_status` (`status`),
  CONSTRAINT `document_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `document_requests_ibfk_2` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `document_status_history`;
CREATE TABLE `document_status_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `request_id` (`request_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `document_status_history_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `document_requests` (`request_id`),
  CONSTRAINT `document_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `document_types`;
CREATE TABLE `document_types` (
  `document_type_id` int NOT NULL AUTO_INCREMENT,
  `document_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `processing_days` int DEFAULT '3',
  `price` decimal(10,2) DEFAULT '0.00',
  `requires_appointment` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('gcash','maya','bank_transfer','cash') COLLATE utf8mb4_general_ci NOT NULL,
  `receipt_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('pending','verified','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `verified_by` int DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `request_id` (`request_id`),
  KEY `verified_by` (`verified_by`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `document_requests` (`request_id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `system_logs`;
CREATE TABLE `system_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `activity` text COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `student_number` varchar(15) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','registrar','admin') DEFAULT 'student',
  `course` varchar(100) NOT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `account_status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `student_number` (`student_number`),
  UNIQUE KEY `email_2` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `walkin_requests`;
CREATE TABLE `walkin_requests` (
  `walkin_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_type_id` int NOT NULL,
  `purpose` text COLLATE utf8mb4_general_ci,
  `encoded_by` int DEFAULT NULL,
  `walkin_status` enum('pending','processing','ready','claimed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`walkin_id`),
  KEY `document_type_id` (`document_type_id`),
  KEY `encoded_by` (`encoded_by`),
  CONSTRAINT `walkin_requests_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`),
  CONSTRAINT `walkin_requests_ibfk_2` FOREIGN KEY (`encoded_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `document_types` (`document_type_id`, `document_name`, `description`, `processing_days`, `price`, `requires_appointment`, `is_active`, `created_at`) VALUES
(1, 'Transcript of Records', 'Official TOR request', 7, 250.00, 1, 1, '2026-05-19 23:58:00'),
(2, 'Certificate of Enrollment', 'Proof of enrollment', 2, 100.00, 1, 1, '2026-05-19 23:58:00'),
(3, 'Good Moral Certificate', 'Student moral certificate', 3, 150.00, 1, 1, '2026-05-19 23:58:00'),
(4, 'Copy of Grades', 'Certified copy of grades', 3, 120.00, 1, 1, '2026-05-19 23:58:00'),
(5, 'Diploma Copy', 'Certified diploma copy', 5, 300.00, 1, 1, '2026-05-19 23:58:00');

INSERT INTO `users` (`user_id`, `student_number`, `first_name`, `last_name`, `middle_name`, `email`, `password`, `role`, `course`, `year_level`, `contact_number`, `profile_picture`, `account_status`, `created_at`, `updated_at`) VALUES
(7, 'ADMIN001', 'System', 'Admin', '', 'admin@dtc.com', '$2y$12$KoBUZuIIaRhHfDdHaBdZYuUmeLZcCd6j2VkaZRV8MkuYo7trVlP3u', 'admin', 'N/A', 'N/A', '0000000000', NULL, 'active', '2026-05-23 18:21:02', '2026-05-23 18:21:02'),
(8, 'REG001', 'System', 'Registrar', '', 'registrar@dtc.com', '$2y$12$6KO2bLZ6.zRUA6wbQJt...pxGU6pjt/LI1C2JAqkMLYn0PoLhvvYu', 'registrar', 'N/A', 'N/A', '0000000000', NULL, 'active', '2026-05-23 18:21:02', '2026-05-23 18:21:02'),
(9, 'SU202400001', 'Test', 'Student', 'L', 'student@dtc.com', '$2y$12$7hkkNMy246ud3dU1GgHx3.0035WhsTIYnW79g4k8BJM1z6eCu6tg.', 'student', 'BSIT', '1st Year', '09123456789', NULL, 'active', '2026-05-23 18:21:03', '2026-05-23 18:21:03');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;