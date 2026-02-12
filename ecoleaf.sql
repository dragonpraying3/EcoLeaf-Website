-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 10, 2026 at 12:29 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminId`, `position`, `joinDate`, `userId`) VALUES
(7, 'content_admin', '2026-01-09 11:12:31', 26),
(8, 'content_admin', '2026-01-09 12:20:58', 29),
(9, 'event_admin', '2026-01-09 12:59:22', 33);

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attId`, `createAt`, `pointsAwards`, `status`, `studentId`, `eventId`) VALUES
(15, '2026-01-09 13:19:46', 60, 'present', 11, 17),
(16, '2026-01-09 12:00:08', 0, 'absent', 11, 19);

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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `badge`
--

INSERT INTO `badge` (`badgeId`, `badgeName`, `criteria`, `value`, `icon_key`, `icon_colour`, `status`, `adminId`) VALUES
(64, 'Eco Starter', 'carbon', 200, 'bx-sapling', '#81C784', 'visible', 7),
(65, 'Eco Platinium', 'diy', 100, 'bx-plant-pot', ' #8D6E63', 'visible', 7),
(66, 'Sustainability Hero', 'leaf', 150, 'bx-water-drop-alt', '#2196F3', 'visible', 7),
(67, 'eco saver', 'leaf', 1, 'bx-sapling', '#81C784', 'visible', 7),
(68, 'eco lover', 'diy', 1, 'bx-sapling', '#81C784', 'visible', 7);

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
(41, 4, 5, 2, 5, 2, 5, 16.485, 7.5, 'Add short walking or cycling trips to your routine.', '2026-01-09 13:27:08', 12, NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diyhub`
--

INSERT INTO `diyhub` (`itemId`, `title`, `description`, `leaf`, `imageFile`, `postAt`, `status`, `approvedAt`, `studentId`, `adminId`) VALUES
(104, 'Plastic Bottle', 'this is plastic bottle', 122, 'S00011_0.jpg', '2026-01-09 11:16:19', 'approve', '2026-01-09 11:21:53', 11, 7),
(105, 'Pencel Case', 'this is pencil case', 90, 'S00012_0.jpg', '2026-01-09 11:16:59', 'approve', '2026-01-09 11:21:54', 12, 7),
(106, 'Work Book', 'this is a work book', 120, 'S00012_1.jpg', '2026-01-09 11:17:32', 'approve', '2026-01-09 11:21:54', 12, 7),
(107, 'Recycle Bag', 'this is a recycle bag', 80, 'S00011_1.jpg', '2026-01-09 11:21:30', 'approve', '2026-01-09 11:21:55', 11, 7),
(108, 'Plastic Bottle', 'this is plastic bottle', 120, 'S00011_2.jpg', '2026-01-09 11:22:23', 'pending', '0000-00-00 00:00:00', 11, NULL),
(109, 'Recycle Box', 'this is recycle box', 90, 'S00012_2.jpg', '2026-01-09 11:23:29', 'pending', '0000-00-00 00:00:00', 12, NULL),
(110, 'Bottle Hub', 'this is bottle hub', 90, 'S00012_3.jpg', '2026-01-09 07:24:05', 'reject', '2026-01-09 13:23:26', 12, 7),
(111, 'Plastic yes', 'this is post', 122, 'S00011_3.jpg', '2026-01-09 13:22:50', 'approve', '2026-01-09 13:23:21', 11, 7);

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `evention`
--

INSERT INTO `evention` (`eventId`, `eventName`, `description`, `eventDate`, `startTime`, `endTime`, `venue`, `capacity`, `category`, `createAt`, `leaf`, `OTP_code`, `imageFile`, `status`, `approvedAt`, `organizerId`, `adminId`) VALUES
(17, 'Sustainability Talk Series', 'Monthly talk featuring sustainability experts sharing', '2026-01-09', '11:31:00', '22:31:00', 'APU Lecture Hall 1', 50, 'Lecture Promotion', '2026-01-09 11:31:58', 60, 468, 'O00006_0.jpg', 'approved', '2026-01-09 11:32:38', 6, 7),
(18, 'Green Campus Workshop Series', 'Hands-on sustainability workshops focused on eco-friendly habits, waste reduction', '2026-01-09', '11:35:00', '22:35:00', 'APU Block A Courtyar', 55, 'Recycle', '2026-01-09 11:36:20', 80, 105, 'O00006_1.jpg', 'pending', '2026-01-09 11:41:06', 6, 7),
(19, 'Campus Tree Planting Day', 'Join us to plant trees around apu', '2026-01-08', '11:38:00', '23:59:00', 'Outdoor Carpark Area', 20, 'Tree Planting', '2026-01-09 11:39:23', 100, 370, 'O00006_2.jpg', 'end', '2026-01-09 11:41:05', 6, 7),
(20, 'Zero-Waste Starter Workshop', 'Learn pratical way', '2026-01-11', '11:00:00', '23:59:00', 'APU Main Entrance', 1, 'Lecture Promotion', '2026-01-09 11:44:47', 40, 710, 'O00006_3.jpg', 'approved', '0000-00-00 00:00:00', 6, NULL),
(21, 'Campus Recycling Drive', 'Learn simple eco friendly habits to reduce waste', '2026-01-09', '11:46:00', '23:59:00', 'APU Block A Courtyar', 88, 'Recycle', '2026-01-09 11:46:47', 25, 455, 'O00006_4.jpg', 'reject', '2026-01-09 13:17:24', 6, 7),
(22, 'Sustainable Food Awareness Day', 'Food choices and learn ', '2026-01-10', '12:00:00', '20:00:00', 'APU Block A Courtyar', 10, 'Lecture Promotion', '2026-01-09 12:03:38', 70, 337, 'O00006_5.jpg', 'approved', '2026-01-09 12:04:03', 6, 7),
(23, 'Green Planting', 'Green Planting', '2026-01-09', '13:09:00', '17:09:00', 'APU Lecture Hall 1', 10, 'Lecture Promotion', '2026-01-09 13:10:07', 80, 755, 'O00006_6.jpg', 'approved', '2026-01-09 13:17:10', 6, 7),
(24, 'Green Planting In Apu', 'Green Planting Apu', '2026-01-09', '13:45:00', '17:45:00', 'APU Block A Courtyar', 30, 'Tree Planting', '2026-01-09 13:46:12', 100, 181, 'O00006_7.jpg', 'pending', '0000-00-00 00:00:00', 6, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notifyId`, `title`, `message`, `isRead`, `sendAt`, `userId`) VALUES
(169, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:16:19', 26),
(170, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:16:59', 26),
(171, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:17:32', 26),
(172, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:21:30', 26),
(173, 'DIY Post Approved', 'Your DIY post with title Plastic Bottle is now visible to public.', 0, '2026-01-09 11:21:53', 22),
(174, 'DIY Post Approved', 'Your DIY post with title Pencel Case is now visible to public.', 0, '2026-01-09 11:21:54', 23),
(175, 'DIY Post Approved', 'Your DIY post with title Work Book is now visible to public.', 0, '2026-01-09 11:21:54', 23),
(176, 'DIY Post Approved', 'Your DIY post with title Recycle Bag is now visible to public.', 0, '2026-01-09 11:21:55', 22),
(177, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:22:23', 26),
(178, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:23:29', 26),
(179, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 11:24:05', 26),
(180, 'New trade request received', 'There are new trading post waiting for your actions', 0, '2026-01-09 11:25:01', 23),
(181, 'Event Approved', 'Your event \'Sustainability Talk Series\' has been approved.', 0, '2026-01-09 11:32:38', 25),
(182, 'Event Approved', 'Your event \'Campus Tree Planting Day\' has been approved.', 0, '2026-01-09 11:41:05', 25),
(183, 'Event Approved', 'Your event \'Green Campus Workshop Series\' has been approved.', 0, '2026-01-09 11:41:06', 25),
(184, 'Event Participation approved - Sustainability Talk Series', 'Your participation request for \'Sustainability Talk Series\' has been approved!', 0, '2026-01-09 11:42:27', 22),
(185, 'New Badge Unlock!', 'You have successfully unlock the Badge \'Sustainability Hero\'', 0, '2026-01-09 11:58:09', 22),
(186, 'Event Participation approved - Campus Tree Planting Day', 'Your participation request for \'Campus Tree Planting Day\' has been approved!', 0, '2026-01-09 12:00:08', 22),
(187, 'Event Approved', 'Your event \'Sustainable Food Awareness Day\' has been approved.', 0, '2026-01-09 12:04:03', 25),
(188, 'Event Participation rejected - Sustainable Food Awareness Day', 'Your participation request for \'Sustainable Food Awareness Day\' has been rejected. Reason: Not suitable', 0, '2026-01-09 13:12:37', 22),
(189, 'Event Approved', 'Your event \'Green Planting\' has been approved.', 0, '2026-01-09 13:17:10', 25),
(190, 'Event Rejected', 'Your event \'Campus Recycling Drive\' has been rejected. Reason: Unable to Approve at This Time', 0, '2026-01-09 13:17:24', 25),
(191, 'New DIY post received', 'There are new post waiting for your actions', 1, '2026-01-09 13:22:50', 26),
(192, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-09 13:22:50', 29),
(193, 'New DIY post received', 'There are new post waiting for your actions', 0, '2026-01-09 13:22:50', 33),
(194, 'DIY Post Approved', 'Your DIY post with title Plastic yes is now visible to public.', 0, '2026-01-09 13:23:21', 22),
(195, 'Offensive Content', 'Vulgar language, hate speech, discrimination, or disrespectful\r\n                                content.', 0, '2026-01-09 13:23:26', 23),
(196, 'New trade request received', 'There are new trading post waiting for your actions', 0, '2026-01-09 13:24:45', 22),
(197, 'Trade Request Approved', 'Your trading of Recycle Bag is approved by the post owner.', 0, '2026-01-09 13:25:13', 23),
(198, 'Seller side confirmation', 'Your trading post has been complete from seller side', 0, '2026-01-09 13:25:34', 23),
(199, 'Buyer side confirmation', 'Your trading post has been complete from buyer side', 0, '2026-01-09 13:25:55', 22),
(200, 'Trading post completed', 'The post is permanently closed.', 0, '2026-01-09 13:25:55', 23),
(201, 'Trading post completed', 'The post is permanently closed.', 0, '2026-01-09 13:25:55', 22),
(202, 'New Badge Unlock!', 'You have successfully unlock the Badge \'eco saver\'', 0, '2026-01-09 13:29:59', 22),
(203, 'New Badge Unlock!', 'You have successfully unlock the Badge \'eco lover\'', 0, '2026-01-09 13:29:59', 22),
(204, 'New Badge Unlock!', 'You have successfully unlock the Badge \'eco saver\'', 0, '2026-01-09 13:35:26', 23),
(205, 'New Badge Unlock!', 'You have successfully unlock the Badge \'eco lover\'', 0, '2026-01-09 13:35:26', 23),
(206, 'Event Participation rejected - Sustainability Talk Series', 'Your participation request for \'Sustainability Talk Series\' has been rejected. Reason: Not suitable', 0, '2026-01-09 13:47:27', 22),
(207, 'New Badge Unlock!', 'You have successfully unlock the Badge \'eco saver\'', 0, '2026-01-10 04:01:16', 24);

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
(6, 'Renewable Energy Society', 'treasurer', '2026-01-09 11:11:55', 25),
(7, 'Recycling & Waste Management Club', 'member', '2026-01-09 12:20:11', 28),
(8, 'Wildlife & Nature Conservation Club', 'treasurer', '2026-01-09 12:58:37', 32);

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `participation`
--

INSERT INTO `participation` (`registerId`, `registerDate`, `status`, `studentId`, `eventId`, `organizerId`) VALUES
(29, '2026-01-09 11:33:00', 'rejected', 11, 17, 6),
(30, '2026-01-09 11:59:29', 'pending', 11, 19, 6),
(31, '2026-01-09 13:11:51', 'rejected', 11, 22, 6),
(32, '2026-01-09 13:17:54', 'pending', 11, 23, 6);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `redemption`
--

INSERT INTO `redemption` (`redemptId`, `redemptAt`, `status`, `studentId`, `rewardId`) VALUES
(6, '2026-01-09 13:32:13', 'success', 11, 14),
(7, '2026-01-10 04:01:57', 'collected', 13, 13);

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rewarditem`
--

INSERT INTO `rewarditem` (`rewardId`, `name`, `description`, `pointsRequired`, `quantity`, `imageFile`, `status`, `adminId`) VALUES
(13, 'Eco Tupperware', 'BUY IT NOW!!!', 10, 3, 'A00007_0.jpg', 'active', 7),
(14, 'REWARD NEW TUPPERWARE', 'i am new', 200, 1, 'A00007_1.jpg', 'inactive', 7);

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentId`, `tpNumber`, `programme`, `intakeCode`, `leaf`, `joinDate`, `userId`) VALUES
(11, 'TP00001', 'cs', 'UCDF0001', 120, '2026-01-09 11:08:04', 22),
(12, 'TP00002', 'cs', 'UCDF0001', 20, '2026-01-09 11:09:00', 23),
(13, 'TP00003', 'cs', 'UCDF0001', 90, '2026-01-09 11:10:24', 24),
(14, 'TP082222', 'BSc (Hons) Software Engineering', 'UCDF0001', 0, '2026-01-09 12:19:22', 27),
(15, 'TP082111', 'BSc (Hons) Software Engineering', 'UCDF0001', 0, '2026-01-09 12:53:49', 30),
(16, 'TP0821111', 'cs', 'UCDF0001', 300, '2026-01-09 12:57:50', 31);

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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `studentbadge`
--

INSERT INTO `studentbadge` (`studentBadgeId`, `earnAt`, `badgeId`, `studentId`) VALUES
(101, '2026-01-09 11:58:09', 66, 11),
(102, '2026-01-09 13:29:59', 67, 11),
(103, '2026-01-09 13:29:59', 68, 11),
(104, '2026-01-09 13:35:26', 67, 12),
(105, '2026-01-09 13:35:26', 68, 12),
(106, '2026-01-10 04:01:16', 67, 13);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `summary`
--

INSERT INTO `summary` (`summaryId`, `treePlanted`, `wasteCollected`, `recycleItem`, `submittedDate`, `eventId`, `organizerId`) VALUES
(4, 5, 10, 30, '2026-01-09 13:13:06', 19, 6),
(5, 100, 30, 20, '2026-01-09 13:48:15', 19, 6);

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

--
-- Dumping data for table `traderequest`
--

INSERT INTO `traderequest` (`tradeId`, `status`, `sellerConfirm`, `requestConfirm`, `location`, `startTime`, `endTime`, `requestAt`, `completedAt`, `itemId`, `sellerId`, `buyerId`) VALUES
(172, 'approve', 1, 1, 'APU Canteen – Center Zone', '2026-01-15 13:24:00', '2026-01-15 15:24:00', '2026-01-09 13:24:45', '2026-01-09 13:25:55', 107, 11, 12);

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `email`, `password`, `role`, `name`, `DateOfBirth`, `gender`, `phone`, `status`) VALUES
(22, 'studentA', 'studentA@gmail.com', '$2y$10$2BL0CMrsL9x0/gU4Kv3bNuFQ2DYnTVQsmPyAQOq7LUCav1ayp0sD2', 'student', 'Michalle Tan', '2026-01-03', 'Male', '01173459036', 'active'),
(23, 'studentB', 'studentb@gmail.com', '$2y$10$12LXDVE5StIQmft85L8tr.Eigq3RL/9uJ35yrASIblGYPjkXHZpp.', 'student', 'JACKSON CHONG', '2026-01-08', 'Male', '60198765562', 'active'),
(24, 'studentC', 'studentC@gmail.com', '$2y$10$HcrKAe0aIiN7n1OPWhJcz.uwwgNZr8pj6hGdmDUW7A4tRKMXn1En6', 'student', 'Crystal lim', '2025-12-31', 'Male', '0113568932', 'active'),
(25, 'orgA', 'orgA@gmail.com', '$2y$10$f3nt7B40.p2q7BPidUVT5.YGsIKxqtto.oUzAobpjAD5Q22Lylc.G', 'organizer', 'JANE LAI', '2026-01-07', 'female', '01221123332', 'active'),
(26, 'adminA', 'adminA@gmail.com', '$2y$10$bkXLD6L.Afgv3sL38w8.0OsYnycUaNToGMkJOALojX6695B4knSMW', 'admin', 'JOE TAN', '2026-01-07', 'Male', '0112397323', 'active'),
(27, 'studentD', 'studentD@gmail.com', '$2y$10$agl6o3Kufy6JdaBAMo2hLutWBZpFzNBb4gMOyOFMamwFgkMggHaoy', 'student', 'JIN MING', '2026-01-08', 'male', '0111198282', 'active'),
(28, 'orgB', 'orgB@gmail.com', '$2y$10$cANizKVNtBQ6jvmoLzTdZOUHKL9tKS/yCxsoDFOvJ.Yd.jjCPA1gm', 'organizer', 'JOHN TAN', '2026-01-02', 'male', '0234567890', 'active'),
(29, 'adminB', 'adminB@gmail.com', '$2y$10$ZPB8GFiyhHrpaLSY4esuLuHlVEJD253kruNZroO68tj4pATf1/ZLK', 'admin', 'ADMIN MING', '2026-01-01', 'male', '0123456789', 'active'),
(30, 'studentG', 'studentg@gmail.com', '$2y$10$OnuSbAj8bwQ/ixMHkSfuJu4L9oU9xQir1YXHVtMh9EvPwUS0gIAYK', 'student', 'jinming low', '2026-01-01', 'male', '0111123453', 'active'),
(31, 'studentE', 'jett2holiday@gmail.com', '$2y$10$OIqO55wS6mIDU5ZjZkvsm.JMHhQwEk/6taUXotQpgJByVJ3KGaKFC', 'student', 'JINMING123', '2026-01-07', 'Male', '0111928221', 'inactive'),
(32, 'orgC', 'orgC@gmail.com', '$2y$10$fgDyQ71M1eJTWKnIT4dWyOR1bWMgnL3NHW5nNt4FPL065YIvDZAuu', 'organizer', 'ORGANIZER LOW', '2026-01-02', 'male', '0112345678', 'active'),
(33, 'adminC', 'adminC@gmail.com', '$2y$10$KlNr3Zqw9tDLiPubKoSt2eHNQrf57vg1y0KknNDU42dMEFxBMeLDW', 'admin', 'ADMIN LOW', '2026-01-06', 'male', '01234567891', 'active');

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
DROP EVENT IF EXISTS `Upcycle_Trade_Tracking`$$
CREATE DEFINER=`root`@`localhost` EVENT `Upcycle_Trade_Tracking` ON SCHEDULE EVERY 1 HOUR STARTS '2025-12-05 17:30:15' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM traderequest
WHERE status = 'approve'
  AND (sellerConfirm = 0 OR requestConfirm = 0)
  AND NOW() >= DATE_ADD(endTime, INTERVAL 2 HOUR)$$

DROP EVENT IF EXISTS `Project_Request_Centre`$$
CREATE DEFINER=`root`@`localhost` EVENT `Project_Request_Centre` ON SCHEDULE EVERY 1 HOUR STARTS '2025-12-05 20:12:38' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM traderequest
WHERE status = 'pending'
AND NOW() >= DATE_ADD(requestAt, INTERVAL 24 HOUR)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
