-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 22, 2008 at 12:40 AM
-- Server version: 5.0.22
-- PHP Version: 5.1.6
-- 
-- Example schema for RMBT.
-- This file is not covered by the RMBT license as it
-- is not source code.  It is released into the public
-- domain.  Do whatever you want with it :)
-- 
-- Database: `RMBT`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `areas`
-- 

CREATE TABLE `areas` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `coordinate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `areas`
-- 

INSERT INTO `areas` VALUES (1, 'Sandbox', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `links`
-- 

CREATE TABLE `links` (
  `id` int(11) NOT NULL auto_increment,
  `fromLocation_id` int(11) NOT NULL,
  `toLocation_id` int(11) NOT NULL,
  `door` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fromLocation_id` (`fromLocation_id`),
  KEY `toLocation_id` (`toLocation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=61 ;

-- 
-- Dumping data for table `links`
-- 

INSERT INTO `links` VALUES (3, 28, 27, 0);
INSERT INTO `links` VALUES (4, 27, 28, 0);
INSERT INTO `links` VALUES (5, 29, 28, 0);
INSERT INTO `links` VALUES (6, 28, 29, 0);
INSERT INTO `links` VALUES (7, 30, 29, 0);
INSERT INTO `links` VALUES (8, 29, 30, 0);
INSERT INTO `links` VALUES (9, 31, 30, 0);
INSERT INTO `links` VALUES (10, 30, 31, 0);
INSERT INTO `links` VALUES (11, 32, 29, 0);
INSERT INTO `links` VALUES (12, 29, 32, 0);
INSERT INTO `links` VALUES (13, 33, 31, 0);
INSERT INTO `links` VALUES (14, 31, 33, 0);
INSERT INTO `links` VALUES (15, 35, 34, 0);
INSERT INTO `links` VALUES (16, 34, 35, 0);
INSERT INTO `links` VALUES (17, 36, 35, 0);
INSERT INTO `links` VALUES (18, 35, 36, 0);
INSERT INTO `links` VALUES (19, 39, 38, 0);
INSERT INTO `links` VALUES (20, 38, 39, 0);
INSERT INTO `links` VALUES (21, 40, 39, 0);
INSERT INTO `links` VALUES (22, 39, 40, 0);
INSERT INTO `links` VALUES (23, 42, 41, 0);
INSERT INTO `links` VALUES (24, 41, 42, 0);
INSERT INTO `links` VALUES (25, 45, 44, 0);
INSERT INTO `links` VALUES (26, 44, 45, 0);
INSERT INTO `links` VALUES (27, 47, 46, 0);
INSERT INTO `links` VALUES (28, 46, 47, 0);
INSERT INTO `links` VALUES (29, 48, 47, 0);
INSERT INTO `links` VALUES (30, 47, 48, 0);
INSERT INTO `links` VALUES (31, 49, 48, 0);
INSERT INTO `links` VALUES (32, 48, 49, 0);
INSERT INTO `links` VALUES (33, 51, 50, 0);
INSERT INTO `links` VALUES (34, 50, 51, 0);
INSERT INTO `links` VALUES (35, 52, 51, 0);
INSERT INTO `links` VALUES (36, 51, 52, 0);
INSERT INTO `links` VALUES (37, 53, 52, 0);
INSERT INTO `links` VALUES (38, 52, 53, 0);
INSERT INTO `links` VALUES (39, 54, 53, 0);
INSERT INTO `links` VALUES (40, 53, 54, 0);
INSERT INTO `links` VALUES (41, 55, 50, 0);
INSERT INTO `links` VALUES (42, 50, 55, 0);
INSERT INTO `links` VALUES (43, 56, 52, 0);
INSERT INTO `links` VALUES (44, 52, 56, 0);
INSERT INTO `links` VALUES (45, 57, 54, 0);
INSERT INTO `links` VALUES (46, 54, 57, 0);
INSERT INTO `links` VALUES (47, 62, 61, 0);
INSERT INTO `links` VALUES (48, 61, 62, 0);
INSERT INTO `links` VALUES (49, 63, 62, 0);
INSERT INTO `links` VALUES (50, 62, 63, 0);
INSERT INTO `links` VALUES (51, 64, 63, 0);
INSERT INTO `links` VALUES (52, 63, 64, 0);
INSERT INTO `links` VALUES (53, 65, 64, 0);
INSERT INTO `links` VALUES (54, 64, 65, 0);
INSERT INTO `links` VALUES (55, 66, 65, 0);
INSERT INTO `links` VALUES (56, 65, 66, 0);
INSERT INTO `links` VALUES (57, 66, 60, 0);
INSERT INTO `links` VALUES (58, 60, 66, 0);
INSERT INTO `links` VALUES (59, 67, 66, 0);
INSERT INTO `links` VALUES (60, 66, 67, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `room_changes`
-- 

CREATE TABLE `room_changes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `field` varchar(100) NOT NULL,
  `room` int(11) NOT NULL,
  `to` text NOT NULL,
  `when` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;

-- 
-- Dumping data for table `room_changes`
-- 

INSERT INTO `room_changes` VALUES (1, 1, 'name', 24, 'i too has name', '2008-01-21 23:10:16');
INSERT INTO `room_changes` VALUES (2, 1, 'road', 25, '1', '2008-01-21 23:17:42');
INSERT INTO `room_changes` VALUES (3, 1, 'color', 22, 'red', '2008-01-21 23:30:27');
INSERT INTO `room_changes` VALUES (4, 1, 'name', 27, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (5, 1, 'description', 27, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (6, 1, 'falling', 27, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (7, 1, 'name', 28, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (8, 1, 'description', 28, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (9, 1, 'falling', 28, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (10, 1, 'name', 29, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (11, 1, 'description', 29, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (12, 1, 'falling', 29, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (13, 1, 'name', 30, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (14, 1, 'description', 30, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (15, 1, 'falling', 30, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (16, 1, 'name', 31, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (17, 1, 'description', 31, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (18, 1, 'falling', 31, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (19, 1, 'name', 32, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (20, 1, 'description', 32, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (21, 1, 'falling', 32, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (22, 1, 'name', 33, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (23, 1, 'description', 33, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (24, 1, 'falling', 33, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (25, 1, 'name', 34, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (26, 1, 'description', 34, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (27, 1, 'falling', 34, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (28, 1, 'name', 35, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (29, 1, 'description', 35, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (30, 1, 'falling', 35, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (31, 1, 'name', 36, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (32, 1, 'description', 36, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (33, 1, 'falling', 36, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (34, 1, 'name', 37, 'Atop a Giant R', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (35, 1, 'description', 37, 'R is for Raesanos!', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (36, 1, 'falling', 37, '1', '2008-01-22 00:24:14');
INSERT INTO `room_changes` VALUES (37, 1, 'name', 38, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (38, 1, 'description', 38, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (39, 1, 'name', 39, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (40, 1, 'description', 39, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (41, 1, 'name', 40, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (42, 1, 'description', 40, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (43, 1, 'name', 41, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (44, 1, 'description', 41, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (45, 1, 'name', 42, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (46, 1, 'description', 42, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (47, 1, 'name', 43, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (48, 1, 'description', 43, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (49, 1, 'name', 44, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (50, 1, 'description', 44, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (51, 1, 'name', 45, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (52, 1, 'description', 45, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (53, 1, 'name', 46, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (54, 1, 'description', 46, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (55, 1, 'name', 47, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (56, 1, 'description', 47, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (57, 1, 'name', 48, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (58, 1, 'description', 48, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (59, 1, 'name', 49, 'Peak of a Giant M', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (60, 1, 'description', 49, 'M is for MUD!', '2008-01-22 00:25:10');
INSERT INTO `room_changes` VALUES (61, 1, 'name', 50, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (62, 1, 'description', 50, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (63, 1, 'name', 51, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (64, 1, 'description', 51, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (65, 1, 'name', 52, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (66, 1, 'description', 52, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (67, 1, 'name', 53, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (68, 1, 'description', 53, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (69, 1, 'name', 54, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (70, 1, 'description', 54, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (71, 1, 'name', 55, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (72, 1, 'description', 55, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (73, 1, 'name', 56, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (74, 1, 'description', 56, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (75, 1, 'name', 57, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (76, 1, 'description', 57, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (77, 1, 'name', 58, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (78, 1, 'description', 58, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (79, 1, 'name', 59, 'Summit of the Letter B', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (80, 1, 'description', 59, 'B is for Building!', '2008-01-22 00:25:29');
INSERT INTO `room_changes` VALUES (81, 1, 'road', 50, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (82, 1, 'road', 51, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (83, 1, 'road', 52, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (84, 1, 'road', 53, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (85, 1, 'road', 54, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (86, 1, 'road', 55, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (87, 1, 'road', 56, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (88, 1, 'road', 57, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (89, 1, 'road', 58, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (90, 1, 'road', 59, '1', '2008-01-22 00:25:43');
INSERT INTO `room_changes` VALUES (91, 1, 'name', 60, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (92, 1, 'description', 60, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (93, 1, 'name', 61, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (94, 1, 'description', 61, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (95, 1, 'name', 62, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (96, 1, 'description', 62, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (97, 1, 'name', 63, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (98, 1, 'description', 63, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (99, 1, 'name', 64, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (100, 1, 'description', 64, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (101, 1, 'name', 65, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (102, 1, 'description', 65, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (103, 1, 'name', 66, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (104, 1, 'description', 66, 'T is for Tool!', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (105, 1, 'name', 67, 'Roof of the Letter T', '2008-01-22 00:26:39');
INSERT INTO `room_changes` VALUES (106, 1, 'description', 67, 'T is for Tool!', '2008-01-22 00:26:39');

-- --------------------------------------------------------

-- 
-- Table structure for table `rooms`
-- 

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL auto_increment,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `z` int(11) NOT NULL,
  `plane_id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `creator` int(11) NOT NULL,
  `whenCreated` datetime NOT NULL,
  `color` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `road` tinyint(4) NOT NULL,
  `indoors` tinyint(4) NOT NULL,
  `falling` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

-- 
-- Dumping data for table `rooms`
-- 

INSERT INTO `rooms` VALUES (27, 1, 14, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:13', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (28, 1, 15, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:13', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (29, 1, 16, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:13', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (30, 1, 17, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:13', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (31, 1, 18, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (32, 2, 16, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (33, 2, 18, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (34, 3, 13, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (35, 3, 14, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (36, 3, 15, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (37, 3, 17, 0, 1, 'Atop a Giant R', 0, '2008-01-22 00:23:14', '', 'R is for Raesanos!', 0, 0, 1);
INSERT INTO `rooms` VALUES (38, 5, 14, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (39, 5, 15, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (40, 5, 16, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (41, 6, 17, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (42, 6, 18, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (43, 7, 16, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (44, 8, 17, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (45, 8, 18, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (46, 9, 13, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (47, 9, 14, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (48, 9, 15, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (49, 9, 16, 0, 1, 'Peak of a Giant M', 0, '2008-01-22 00:23:14', '', 'M is for MUD!', 0, 0, 0);
INSERT INTO `rooms` VALUES (50, 11, 14, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (51, 11, 15, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (52, 11, 16, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (53, 11, 17, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (54, 11, 18, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (55, 12, 14, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (56, 12, 16, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (57, 12, 18, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (58, 13, 15, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (59, 13, 17, 0, 1, 'Summit of the Letter B', 0, '2008-01-22 00:23:14', '', 'B is for Building!', 1, 0, 0);
INSERT INTO `rooms` VALUES (60, 15, 18, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (61, 16, 13, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (62, 16, 14, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (63, 16, 15, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (64, 16, 16, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (65, 16, 17, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (66, 16, 18, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);
INSERT INTO `rooms` VALUES (67, 17, 18, 0, 1, 'Roof of the Letter T', 0, '2008-01-22 00:23:14', '', 'T is for Tool!', 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 'DefaultUser');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `links`
-- 
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_2` FOREIGN KEY (`toLocation_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`fromLocation_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

