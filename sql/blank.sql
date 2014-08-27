-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 27, 2014 at 03:03 PM
-- Server version: 5.5.38
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wicker`
--

-- --------------------------------------------------------

--
-- Table structure for table `aps`
--

CREATE TABLE IF NOT EXISTS `aps` (
`id` int(11) NOT NULL,
  `scan_id` int(11) NOT NULL,
  `bssid` varchar(17) NOT NULL,
  `first_seen` int(10) NOT NULL,
  `last_seen` int(10) NOT NULL,
  `channel` int(2) NOT NULL,
  `privacy` varchar(10) NOT NULL,
  `cipher` varchar(10) NOT NULL,
  `authentication` varchar(10) NOT NULL,
  `power` int(3) NOT NULL,
  `beacons` int(6) NOT NULL,
  `ivs` int(10) NOT NULL,
  `essid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attacks`
--

CREATE TABLE IF NOT EXISTS `attacks` (
`id` int(11) NOT NULL,
  `cap_id` int(11) NOT NULL,
  `attack` int(1) NOT NULL,
  `status` int(11) NOT NULL,
  `status_text` varchar(64) NOT NULL,
  `current` int(10) NOT NULL,
  `password` varchar(32) NOT NULL,
  `tmpfile` varchar(36) NOT NULL,
  `runtime` varchar(10) NOT NULL,
  `rate` int(5) NOT NULL,
  `auth` int(10) NOT NULL,
  `pid` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `caps`
--

CREATE TABLE IF NOT EXISTS `caps` (
`id` int(10) NOT NULL,
  `essid` varchar(128) NOT NULL,
  `bssid` varchar(17) NOT NULL,
  `complete` float NOT NULL,
  `runtime` int(10) NOT NULL,
  `status` int(3) NOT NULL,
  `password` varchar(128) NOT NULL,
  `checksum` varchar(32) NOT NULL,
  `location` varchar(32) NOT NULL,
  `raw` text NOT NULL,
  `packets` int(6) NOT NULL,
  `size` int(7) NOT NULL,
  `timestamp` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
`id` int(11) NOT NULL,
  `ap_id` int(6) NOT NULL,
  `mac` varchar(17) NOT NULL,
  `first_seen` int(10) NOT NULL,
  `last_seen` int(10) NOT NULL,
  `power` int(3) NOT NULL,
  `packets` int(6) NOT NULL,
  `bssid` int(17) NOT NULL,
  `probed` text NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `scans`
--

CREATE TABLE IF NOT EXISTS `scans` (
`id` int(11) NOT NULL,
  `guid` varchar(36) NOT NULL,
  `time` int(10) NOT NULL,
  `aps` int(11) NOT NULL,
  `clients` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aps`
--
ALTER TABLE `aps`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attacks`
--
ALTER TABLE `attacks`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `caps`
--
ALTER TABLE `caps`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scans`
--
ALTER TABLE `scans`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aps`
--
ALTER TABLE `aps`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `attacks`
--
ALTER TABLE `attacks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `caps`
--
ALTER TABLE `caps`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `scans`
--
ALTER TABLE `scans`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

