-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- 主機: 127.0.0.1
-- 產生時間： 2014 �?05 ??08 ??10:45
-- 伺服器版本: 5.6.16
-- PHP 版本： 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `ntou_notificationprovider`
--

-- --------------------------------------------------------

--
-- 資料表結構 `deviceandstudent`
--

CREATE TABLE IF NOT EXISTS `deviceandstudent` (
  `studentID` varchar(20) DEFAULT NULL,
  `deviceToken` varchar(255) NOT NULL,
  `deviceType` int(10) NOT NULL,
  PRIMARY KEY (`deviceToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 資料表的匯出資料 `deviceandstudent`
--

INSERT INTO `deviceandstudent` (`studentID`, `deviceToken`, `deviceType`) VALUES
('0598875', '513sdfg2136sd5f4g21sdfg', 0),
('09957012', '544c79118a76adf8ba9a8a00030d9360d2fe8e21c1071bfeacbd63c9912ef1da', 0),
('09957038', 'b4812ce59234fd8673325a8c30d10556e45a6b9389fe6d2be0ce281f28c3d328', 0),
('09957037', 'f4c952b414b80fa77f56c5d4cb2053747a2d089045332151e7fa2ac17a51b618', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `devicesetting`
--

CREATE TABLE IF NOT EXISTS `devicesetting` (
  `deviceToken` varchar(255) NOT NULL,
  `moodle` int(1) NOT NULL DEFAULT '1',
  `library` int(1) NOT NULL DEFAULT '1',
  `emergency` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`deviceToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 資料表的匯出資料 `devicesetting`
--

INSERT INTO `devicesetting` (`deviceToken`, `moodle`, `library`, `emergency`) VALUES
('544c79118a76adf8ba9a8a00030d9360d2fe8e21c1071bfeacbd63c9912ef1da', 1, 1, 1),
('b4812ce59234fd8673325a8c30d10556e45a6b9389fe6d2be0ce281f28c3d328', 1, 1, 1),
('f4c952b414b80fa77f56c5d4cb2053747a2d089045332151e7fa2ac17a51b618', 1, 1, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
