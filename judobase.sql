-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 05, 2018 at 07:38 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `judo2`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_player`
--

CREATE TABLE `m_player` (
  `id` int(11) NOT NULL,
  `id_person` int(11) NOT NULL,
  `competitor` varchar(100) DEFAULT NULL,
  `player_first_name` varchar(45) DEFAULT NULL,
  `player_second_name` varchar(45) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `country_short` varchar(10) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `del_flag` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `t_competition`
--

CREATE TABLE `t_competition` (
  `id` int(11) NOT NULL,
  `id_competition` int(11) NOT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `has_results` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `comp_year` int(11) DEFAULT NULL,
  `prime_event` int(11) DEFAULT NULL,
  `continent_short` varchar(50) DEFAULT NULL,
  `has_logo` int(11) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `id_country` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `del_flag` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `t_event`
--

CREATE TABLE `t_event` (
  `id` int(11) NOT NULL,
  `contest_code` varchar(100) NOT NULL,
  `id_competition` int(11) NOT NULL,
  `id_event` int(11) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `competition_date` date DEFAULT NULL,
  `competition_name` varchar(100) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `round_name` varchar(50) DEFAULT NULL,
  `time_sc` int(11) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `create_time` date DEFAULT NULL,
  `update_time` date DEFAULT NULL,
  `del_flag` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `t_patch`
--

CREATE TABLE `t_patch` (
  `patch_id` int(11) NOT NULL,
  `version` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime DEFAULT NULL,
  `del_flag` decimal(1,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_patch`
--

INSERT INTO `t_patch` (`patch_id`, `version`, `description`, `create_time`, `update_time`, `del_flag`) VALUES
(1, '2.0', 'スクレーピング機能追加', '2018-02-07 09:45:39', '2018-02-07 09:45:39', '0');

-- --------------------------------------------------------

--
-- Table structure for table `t_profile_contests`
--

CREATE TABLE `t_profile_contests` (
  `id` int(11) NOT NULL,
  `id_competition` int(11) NOT NULL,
  `id_fight` int(11) NOT NULL,
  `fight_no` int(11) DEFAULT NULL,
  `country_short_blue` varchar(30) DEFAULT NULL,
  `person_blue` varchar(60) DEFAULT NULL,
  `country_short_white` varchar(30) DEFAULT NULL,
  `person_white` varchar(60) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `round_name` varchar(40) DEFAULT NULL,
  `ippon` int(11) DEFAULT NULL,
  `waza` int(11) DEFAULT NULL,
  `yuko` int(11) DEFAULT NULL,
  `penalty` int(11) DEFAULT NULL,
  `ippon_b` int(11) DEFAULT NULL,
  `waza_b` int(11) DEFAULT NULL,
  `yuko_b` int(11) DEFAULT NULL,
  `penalty_b` int(11) DEFAULT NULL,
  `ippon_w` int(11) DEFAULT NULL,
  `waza_w` int(11) DEFAULT NULL,
  `yuko_w` int(11) DEFAULT NULL,
  `penalty_w` int(11) DEFAULT NULL,
  `contest_code_long` varchar(100) DEFAULT NULL,
  `competition_name` varchar(100) DEFAULT NULL,
  `competition_date` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `del_flag` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_player`
--
ALTER TABLE `m_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_competition`
--
ALTER TABLE `t_competition`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_event`
--
ALTER TABLE `t_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_patch`
--
ALTER TABLE `t_patch`
  ADD PRIMARY KEY (`patch_id`);

--
-- Indexes for table `t_profile_contests`
--
ALTER TABLE `t_profile_contests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_player`
--
ALTER TABLE `m_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;
--
-- AUTO_INCREMENT for table `t_competition`
--
ALTER TABLE `t_competition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2732;
--
-- AUTO_INCREMENT for table `t_event`
--
ALTER TABLE `t_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `t_patch`
--
ALTER TABLE `t_patch`
  MODIFY `patch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_profile_contests`
--
ALTER TABLE `t_profile_contests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3080;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
