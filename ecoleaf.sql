-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 11, 2026 at 08:15 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecoleaf`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `adminId` int NOT NULL AUTO_INCREMENT,
  `position` varchar(30) NOT NULL,
  `joinDate` datetime NOT NULL,
  `userId` int NOT NULL,
  PRIMARY KEY (`adminId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminId`, `position`, `joinDate`, `userId`) VALUES
(7, 'system_admin', '2026-01-04 15:28:55', 26),
(8, 'content_admin', '2026-01-04 15:37:30', 27);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attId` int NOT NULL AUTO_INCREMENT,
  `createAt` datetime NOT NULL,
  `pointsAwards` int NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentId` int NOT NULL,
  `eventId` int NOT NULL,
  PRIMARY KEY (`attId`),
  KEY `studentId` (`studentId`),
  KEY `attendance_ibfk_1` (`eventId`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

DROP TABLE IF EXISTS `badge`;
CREATE TABLE IF NOT EXISTS `badge` (
  `badgeId` int NOT NULL AUTO_INCREMENT,
  `badgeName` varchar(255) NOT NULL,
  `criteria` varchar(255) NOT NULL,
  `value` int NOT NULL,
  `icon_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `icon_colour` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `adminId` int NOT NULL,
  PRIMARY KEY (`badgeId`),
  KEY `adminId` (`adminId`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `badge`
--

INSERT INTO `badge` (`badgeId`, `badgeName`, `criteria`, `value`, `icon_key`, `icon_colour`, `status`, `adminId`) VALUES
(65, 'asdas', 'diy', 1, 'bx-leaf-alt', '#4CAF50', 'visible', 7),
(66, 'fefe', 'carbon', 1, 'bx-plant-pot', ' #8D6E63', 'visible', 7),
(67, '1event', 'event', 1, 'bx-sapling', '#81C784', 'visible', 7),
(69, 'asdad', 'leaf', 12, 'bx-leaf-alt', '#4CAF50', 'visible', 7);

-- --------------------------------------------------------

--
-- Table structure for table `carboncalculator`
--

DROP TABLE IF EXISTS `carboncalculator`;
CREATE TABLE IF NOT EXISTS `carboncalculator` (
  `calcId` int NOT NULL AUTO_INCREMENT,
  `fuel` float NOT NULL,
  `transport` float NOT NULL,
  `cycling_walking` float NOT NULL,
  `recycling` float NOT NULL,
  `waste` float NOT NULL,
  `electric` float NOT NULL,
  `result` float NOT NULL,
  `amountSaved` float NOT NULL,
  `advice` text NOT NULL,
  `calcDate` datetime NOT NULL,
  `studentId` int DEFAULT NULL,
  `organizerId` int DEFAULT NULL,
  `adminId` int DEFAULT NULL,
  PRIMARY KEY (`calcId`),
  KEY `organizerId` (`organizerId`),
  KEY `adminId` (`adminId`),
  KEY `studentId` (`studentId`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carboncalculator`
--

INSERT INTO `carboncalculator` (`calcId`, `fuel`, `transport`, `cycling_walking`, `recycling`, `waste`, `electric`, `result`, `amountSaved`, `advice`, `calcDate`, `studentId`, `organizerId`, `adminId`) VALUES
(41, 12, 12, 12, 12, 12, 12, 58.788, 18, 'Reduce waste, reuse items, and compost organics.', '2026-01-09 00:23:27', 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diyhub`
--

DROP TABLE IF EXISTS `diyhub`;
CREATE TABLE IF NOT EXISTS `diyhub` (
  `itemId` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `leaf` int NOT NULL,
  `imageFile` varchar(100) NOT NULL,
  `postAt` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `approvedAt` datetime NOT NULL,
  `studentId` int NOT NULL,
  `adminId` int DEFAULT NULL,
  PRIMARY KEY (`itemId`),
  KEY `adminId` (`adminId`),
  KEY `studentId` (`studentId`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diyhub`
--

INSERT INTO `diyhub` (`itemId`, `title`, `description`, `leaf`, `imageFile`, `postAt`, `status`, `approvedAt`, `studentId`, `adminId`) VALUES
(106, 'plastic bottle', 'this is a bottle', 122, 'S00013_0.jpg', '2026-01-09 00:08:32', 'approve', '2026-01-10 12:55:19', 13, 7);

-- --------------------------------------------------------

--
-- Table structure for table `evention`
--

DROP TABLE IF EXISTS `evention`;
CREATE TABLE IF NOT EXISTS `evention` (
  `eventId` int NOT NULL AUTO_INCREMENT,
  `eventName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `eventDate` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `venue` varchar(100) NOT NULL,
  `capacity` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `createAt` datetime NOT NULL,
  `leaf` int NOT NULL,
  `OTP_code` int NOT NULL,
  `imageFile` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `approvedAt` datetime NOT NULL,
  `organizerId` int NOT NULL,
  `adminId` int DEFAULT NULL,
  PRIMARY KEY (`eventId`),
  KEY `organizerId` (`organizerId`),
  KEY `adminId` (`adminId`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `notifyId` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `isRead` tinyint(1) NOT NULL,
  `sendAt` datetime NOT NULL,
  `userId` int NOT NULL,
  PRIMARY KEY (`notifyId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notifyId`, `title`, `message`, `isRead`, `sendAt`, `userId`) VALUES
(169, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-04 21:23:15', 26),
(170, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-04 21:23:15', 27),
(171, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-08 21:43:09', 26),
(172, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-08 21:43:09', 27),
(173, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-09 00:08:32', 26),
(174, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 00:08:32', 27),
(175, 'DIY Post Approved', 'Your DIY post with title plastic bottle is now visible to public.', 0, '2026-01-09 00:10:40', 29),
(176, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdasd\'', 0, '2026-01-09 00:22:12', 22),
(177, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdas\'', 0, '2026-01-09 00:22:44', 29),
(178, 'New Badge Unlock!', 'You have successfully unlock the Badge \'fefe\'', 0, '2026-01-09 00:23:27', 29),
(179, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-10 09:41:39', 26),
(180, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-10 09:41:39', 27),
(181, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdasd\'', 0, '2026-01-10 09:49:41', 23),
(182, 'New trade request received', 'There are new trading post waiting for your actions', 0, '2026-01-10 09:49:57', 29),
(183, 'Trade Request Approved', 'Your trading of plastic bottle is approved by the post owner.', 0, '2026-01-10 10:52:34', 23),
(184, 'Reserved', 'This item has been reserved for another buyer who contacted\r\n                                earlier.', 0, '2026-01-10 10:53:06', 23),
(185, 'Trade Request Approved', 'Your trading of plastic bottle is approved by the post owner.', 0, '2026-01-10 10:53:34', 23),
(186, 'Seller side confirmation', 'Your trading post has been complete from seller side', 0, '2026-01-10 10:59:03', 23),
(187, 'Buyer side confirmation', 'Your trading post has been complete from buyer side', 0, '2026-01-10 11:01:13', 29),
(188, 'Trading post completed', 'The post is permanently closed.', 0, '2026-01-10 11:01:13', 23),
(189, 'Trading post completed', 'The post is permanently closed.', 0, '2026-01-10 11:01:13', 29),
(190, 'New trade request received', 'There are new trading post waiting for your actions', 0, '2026-01-10 11:04:09', 29),
(191, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdasd\'', 0, '2026-01-10 11:04:15', 29),
(192, 'Nudity or Sexual Content', 'The amount of leaf does not worth the product own.', 0, '2026-01-10 12:54:57', 23),
(193, 'DIY Post Approved', 'Your DIY post with title plastic bottle is now visible to public.', 0, '2026-01-10 12:55:19', 29),
(194, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdas\'', 0, '2026-01-10 13:48:36', 29),
(195, 'New Badge Unlock!', 'You have successfully unlock the Badge \'fefe\'', 0, '2026-01-10 13:48:36', 29),
(196, 'New Badge Unlock!', 'You have successfully unlock the Badge \'asdad\'', 0, '2026-01-10 20:08:52', 23);

-- --------------------------------------------------------

--
-- Table structure for table `organizer`
--

DROP TABLE IF EXISTS `organizer`;
CREATE TABLE IF NOT EXISTS `organizer` (
  `organizerId` int NOT NULL AUTO_INCREMENT,
  `club` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `joinDate` datetime NOT NULL,
  `userId` int NOT NULL,
  PRIMARY KEY (`organizerId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `organizer`
--

INSERT INTO `organizer` (`organizerId`, `club`, `position`, `joinDate`, `userId`) VALUES
(6, 'Wildlife & Nature Conservation Club', 'secretary', '2026-01-04 15:27:37', 24),
(7, 'Wildlife & Nature Conservation Club', 'vice', '2026-01-04 15:28:02', 25);

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

DROP TABLE IF EXISTS `participation`;
CREATE TABLE IF NOT EXISTS `participation` (
  `registerId` int NOT NULL AUTO_INCREMENT,
  `registerDate` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentId` int NOT NULL,
  `eventId` int NOT NULL,
  `organizerId` int NOT NULL,
  PRIMARY KEY (`registerId`),
  KEY `organizerId` (`organizerId`),
  KEY `studentId` (`studentId`),
  KEY `participation_ibfk_2` (`eventId`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redemption`
--

DROP TABLE IF EXISTS `redemption`;
CREATE TABLE IF NOT EXISTS `redemption` (
  `redemptId` int NOT NULL AUTO_INCREMENT,
  `redemptAt` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `studentId` int NOT NULL,
  `rewardId` int NOT NULL,
  PRIMARY KEY (`redemptId`),
  KEY `studentId` (`studentId`),
  KEY `redemption_ibfk_1` (`rewardId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rewarditem`
--

DROP TABLE IF EXISTS `rewarditem`;
CREATE TABLE IF NOT EXISTS `rewarditem` (
  `rewardId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pointsRequired` int NOT NULL,
  `quantity` int NOT NULL,
  `imageFile` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `adminId` int NOT NULL,
  PRIMARY KEY (`rewardId`),
  KEY `adminId` (`adminId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `studentId` int NOT NULL AUTO_INCREMENT,
  `tpNumber` varchar(50) NOT NULL,
  `programme` varchar(255) NOT NULL,
  `intakeCode` varchar(255) NOT NULL,
  `leaf` int NOT NULL,
  `joinDate` datetime NOT NULL,
  `userId` int NOT NULL,
  PRIMARY KEY (`studentId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentId`, `tpNumber`, `programme`, `intakeCode`, `leaf`, `joinDate`, `userId`) VALUES
(11, 'TP081291', 'BSc (Hons) Software Engineering', 'UCDF2918ICT', 200, '2026-01-04 15:05:32', 22),
(12, 'TP827193', 'BSc (Hons) Software Engineering', 'UCDF2910DT', 200, '2026-01-04 15:21:11', 23),
(13, 'TP092837', 'BSc (Hons) Software Engineering', 'UCDF01291BI', 122, '2026-01-04 20:43:01', 29);

-- --------------------------------------------------------

--
-- Table structure for table `studentbadge`
--

DROP TABLE IF EXISTS `studentbadge`;
CREATE TABLE IF NOT EXISTS `studentbadge` (
  `studentBadgeId` int NOT NULL AUTO_INCREMENT,
  `earnAt` datetime NOT NULL,
  `badgeId` int NOT NULL,
  `studentId` int NOT NULL,
  PRIMARY KEY (`studentBadgeId`),
  KEY `studentId` (`studentId`),
  KEY `studentbadge_ibfk_1` (`badgeId`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `studentbadge`
--

INSERT INTO `studentbadge` (`studentBadgeId`, `earnAt`, `badgeId`, `studentId`) VALUES
(106, '2026-01-10 13:48:36', 65, 13),
(107, '2026-01-10 13:48:36', 66, 13),
(108, '2026-01-10 20:08:52', 69, 12);

-- --------------------------------------------------------

--
-- Table structure for table `summary`
--

DROP TABLE IF EXISTS `summary`;
CREATE TABLE IF NOT EXISTS `summary` (
  `summaryId` int NOT NULL AUTO_INCREMENT,
  `treePlanted` int NOT NULL,
  `wasteCollected` float NOT NULL,
  `recycleItem` float NOT NULL,
  `submittedDate` datetime NOT NULL,
  `eventId` int NOT NULL,
  `organizerId` int NOT NULL,
  PRIMARY KEY (`summaryId`),
  KEY `eventId` (`eventId`),
  KEY `organizerId` (`organizerId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traderequest`
--

DROP TABLE IF EXISTS `traderequest`;
CREATE TABLE IF NOT EXISTS `traderequest` (
  `tradeId` int NOT NULL AUTO_INCREMENT,
  `status` varchar(20) NOT NULL,
  `sellerConfirm` tinyint(1) NOT NULL,
  `requestConfirm` tinyint(1) NOT NULL,
  `location` varchar(100) NOT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime DEFAULT NULL,
  `requestAt` datetime NOT NULL,
  `completedAt` datetime NOT NULL,
  `itemId` int NOT NULL,
  `sellerId` int NOT NULL,
  `buyerId` int NOT NULL,
  PRIMARY KEY (`tradeId`),
  KEY `sellerId` (`sellerId`),
  KEY `buyerId` (`buyerId`),
  KEY `traderequest_ibfk_1` (`itemId`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `email`, `password`, `role`, `name`, `DateOfBirth`, `gender`, `phone`, `status`) VALUES
(22, 'studentA', 'studentA@gmail.com', '$2y$10$oTio7L18vJzz0qMqNMvufe/4O9aZd4mQYjTVC3GC.0FLIevNylbYG', 'student', 'Ahmad Baratha', '2025-10-30', 'female', '0292817312', 'active'),
(23, 'studentB', 'studentB@gmail.com', '$2y$10$4N94ZPO9ziyCoC4uoB6gy.BppLukp0tQUpTeD/llkOmTi4Fd0VnYG', 'student', 'Jestina Halo', '2006-02-10', 'male', '0928219212', 'active'),
(24, 'orgA', 'orgA@gmail.com', '$2y$10$b/XpaDo8pvreJl18.GI2cuvaLcjOKygq9SLn8whOfQNNVJvBFbSYy', 'organizer', 'Aw Yang Jia Jia', '2017-02-01', 'female', '0293736492', 'active'),
(25, 'orgB', 'orgB@gmail.com', '$2y$10$8VueWf.DlaSP/bxzzHkQoeC5BSbSijsY8vnuklFpmQ4SoNmHEbthq', 'organizer', 'City Boom', '2025-12-31', 'Male', '121321312312321', 'active'),
(26, 'adminA', 'adminA@gmail.com', '$2y$10$Sdrgj.ZcgLxyA/xvke4dG.kU1MJxJ1V.9Z8n/7I0UGwaZCFK5GFRG', 'admin', 'Addy Halloween', '2025-11-26', 'male', '2123546862', 'active'),
(27, 'adminB', 'adminB@gmail.com', '$2y$10$pHZLid.iEvvtD/duWshZ7.rzIrWSRKSzSK/XMxz5jTKVeeBG0utOm', 'admin', 'Jane Sue', '2008-07-04', 'Female', '1222122424', 'active'),
(29, 'studentC', 'studentC@gmail.com', '$2y$10$GdO8kA2Rthzc4s6i1yfCkODGh4c8CnelErdML/KhtgrXt19roZb5m', 'student', 'Michelle Kuan', '2026-01-01', 'male', '0928219211', 'active');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `evention` (`eventId`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `badge`
--
ALTER TABLE `badge`
  ADD CONSTRAINT `badge_ibfk_1` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`);

--
-- Constraints for table `carboncalculator`
--
ALTER TABLE `carboncalculator`
  ADD CONSTRAINT `carboncalculator_ibfk_1` FOREIGN KEY (`organizerId`) REFERENCES `organizer` (`organizerId`),
  ADD CONSTRAINT `carboncalculator_ibfk_2` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`),
  ADD CONSTRAINT `carboncalculator_ibfk_3` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `diyhub`
--
ALTER TABLE `diyhub`
  ADD CONSTRAINT `diyhub_ibfk_1` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`),
  ADD CONSTRAINT `diyhub_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `evention`
--
ALTER TABLE `evention`
  ADD CONSTRAINT `evention_ibfk_1` FOREIGN KEY (`organizerId`) REFERENCES `organizer` (`organizerId`),
  ADD CONSTRAINT `evention_ibfk_2` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `organizer`
--
ALTER TABLE `organizer`
  ADD CONSTRAINT `organizer_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`organizerId`) REFERENCES `organizer` (`organizerId`),
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`eventId`) REFERENCES `evention` (`eventId`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_3` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `redemption`
--
ALTER TABLE `redemption`
  ADD CONSTRAINT `redemption_ibfk_1` FOREIGN KEY (`rewardId`) REFERENCES `rewarditem` (`rewardId`) ON DELETE CASCADE,
  ADD CONSTRAINT `redemption_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `rewarditem`
--
ALTER TABLE `rewarditem`
  ADD CONSTRAINT `rewarditem_ibfk_1` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `studentbadge`
--
ALTER TABLE `studentbadge`
  ADD CONSTRAINT `studentbadge_ibfk_1` FOREIGN KEY (`badgeId`) REFERENCES `badge` (`badgeId`) ON DELETE CASCADE,
  ADD CONSTRAINT `studentbadge_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `student` (`studentId`);

--
-- Constraints for table `summary`
--
ALTER TABLE `summary`
  ADD CONSTRAINT `summary_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `evention` (`eventId`),
  ADD CONSTRAINT `summary_ibfk_2` FOREIGN KEY (`organizerId`) REFERENCES `organizer` (`organizerId`);

--
-- Constraints for table `traderequest`
--
ALTER TABLE `traderequest`
  ADD CONSTRAINT `traderequest_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `diyhub` (`itemId`) ON DELETE CASCADE,
  ADD CONSTRAINT `traderequest_ibfk_2` FOREIGN KEY (`sellerId`) REFERENCES `student` (`studentId`),
  ADD CONSTRAINT `traderequest_ibfk_3` FOREIGN KEY (`buyerId`) REFERENCES `student` (`studentId`);

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `Project_Request_Centre`$$
CREATE DEFINER=`root`@`localhost` EVENT `Project_Request_Centre` ON SCHEDULE EVERY 1 HOUR STARTS '2025-12-05 20:12:38' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM traderequest
WHERE status = 'pending'
AND NOW() >= DATE_ADD(requestAt, INTERVAL 24 HOUR)$$

DROP EVENT IF EXISTS `Upcycle_Trade_Tracking`$$
CREATE DEFINER=`root`@`localhost` EVENT `Upcycle_Trade_Tracking` ON SCHEDULE EVERY 1 HOUR STARTS '2025-12-05 17:30:15' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM traderequest
WHERE status = 'approve'
  AND (sellerConfirm = 0 OR requestConfirm = 0)
  AND NOW() >= DATE_ADD(endTime, INTERVAL 2 HOUR)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
