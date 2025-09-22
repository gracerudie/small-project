-- --------------------------------------------------------
-- Host:                         jdcolors.micahramirez.tech
-- Server version:               8.0.43-0ubuntu0.24.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for COP4331
CREATE DATABASE IF NOT EXISTS `COP4331` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `COP4331`;

-- Dumping structure for table COP4331.Colors
CREATE TABLE IF NOT EXISTS `Colors` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL DEFAULT '',
  `UserID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table COP4331.Colors: ~36 rows (approximately)
INSERT INTO `Colors` (`ID`, `Name`, `UserID`) VALUES
	(1, 'Blue', 1),
	(2, 'White', 1),
	(3, 'Black', 1),
	(4, 'gray', 1),
	(5, 'Magenta', 1),
	(6, 'Yellow', 1),
	(7, 'Cyan', 1),
	(8, 'Salmon', 1),
	(9, 'Chartreuse', 1),
	(10, 'Lime', 1),
	(11, 'Light Blue', 1),
	(12, 'Light Gray', 1),
	(13, 'Light Red', 1),
	(14, 'Light Green', 1),
	(15, 'Chiffon', 1),
	(16, 'Fuscia', 1),
	(17, 'Brown', 1),
	(18, 'Beige', 1),
	(19, 'Blue', 3),
	(20, 'White', 3),
	(21, 'Black', 3),
	(22, 'gray', 3),
	(23, 'Magenta', 3),
	(24, 'Yellow', 3),
	(25, 'Cyan', 3),
	(26, 'Salmon', 3),
	(27, 'Chartreuse', 3),
	(28, 'Lime', 3),
	(29, 'Light Blue', 3),
	(30, 'Light Gray', 3),
	(31, 'Light Red', 3),
	(32, 'Light Green', 3),
	(33, 'Chiffon', 3),
	(34, 'Fuscia', 3),
	(35, 'Brown', 3),
	(36, 'Beige', 3),
	(37, 'aquamarine', 2),

-- Dumping structure for table COP4331.Contacts
CREATE TABLE IF NOT EXISTS `Contacts` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL DEFAULT '',
  `LastName` varchar(50) NOT NULL DEFAULT '',
  `Phone` varchar(50) NOT NULL DEFAULT '',
  `Email` varchar(50) NOT NULL DEFAULT '',
  `UserID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table COP4331.Contacts: ~0 rows (approximately)

-- Dumping structure for table COP4331.Users
CREATE TABLE IF NOT EXISTS `Users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL DEFAULT '',
  `LastName` varchar(50) NOT NULL DEFAULT '',
  `Login` varchar(50) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
