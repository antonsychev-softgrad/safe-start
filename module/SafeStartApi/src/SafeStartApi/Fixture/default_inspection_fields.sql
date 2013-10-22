-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 21, 2013 at 07:04 PM
-- Server version: 5.5.23
-- PHP Version: 5.5.1-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `safe-start`
--

-- --------------------------------------------------------

--
-- Table structure for table `default_inspection_fields`
--

CREATE TABLE IF NOT EXISTS `default_inspection_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alert_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alert_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trigger_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `creation_date` date NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `alert_critical` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `additional` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B9A99DBA727ACA70` (`parent_id`),
  KEY `IDX_B9A99DBAF675F31B` (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=96 ;

--
-- Dumping data for table `default_inspection_fields`
--

INSERT INTO `default_inspection_fields` (`id`, `parent_id`, `author_id`, `type`, `title`, `description`, `alert_title`, `alert_description`, `trigger_value`, `sort_order`, `creation_date`, `enabled`, `alert_critical`, `deleted`, `additional`) VALUES
(1, NULL, NULL, 'root', 'Safety', '', '', '', 'yes', 1, '2013-10-04', 1, 1, 0, 0),
(2, NULL, NULL, 'root', 'Cabin', '', '', '', '', 2, '2013-10-04', 1, 1, 0, 0),
(3, NULL, NULL, 'root', 'Structural', '', '', '', '', 3, '2013-10-04', 1, 1, 0, 0),
(4, NULL, NULL, 'root', 'Mechanical', '', '', '', '', 4, '2013-10-04', 1, 1, 0, 0),
(5, NULL, NULL, 'root', 'Trailer', '', '', '', '', 5, '2013-10-04', 1, 1, 0, 1),
(6, NULL, NULL, 'root', 'Crane', '', '', '', '', 7, '2013-10-04', 1, 1, 0, 1),
(7, NULL, NULL, 'root', 'Auxiliary motor', '', '', '', '', 6, '2013-10-04', 1, 1, 0, 1),
(8, NULL, NULL, 'root', 'Earthmoving plant', '', '', '', '', 8, '2013-10-04', 1, 1, 0, 1),
(9, NULL, NULL, 'root', 'Work platform', '', '', '', '', 9, '2013-10-04', 1, 1, 0, 1),
(10, 1, 1, 'radio', 'Is the vehicle stable?', 'Vehicle Stability', 'Danger. Please secure the vehicle before continuing', 'Vehicle Stability', 'no', 1, '2013-10-04', 1, 1, 0, 0),
(11, 1, 1, 'radio', 'Is the operators manual in the vehicle?', 'Operators manual', '', 'Operators manual', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(12, 1, NULL, 'radio', 'Are you authorised in the use of this vehicle?', 'Authorised person', 'Do not inspect any vehicle unless you are authorised', 'Authorised person', 'no', 3, '2013-10-04', 1, 1, 0, 0),
(13, 1, 1, 'radio', 'Is the first aid kit present and fully stocked?', 'First aid kit', '', 'First aid kit', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(14, 1, NULL, 'radio', 'Is the fire extinguisher present?', 'Fire extinguisher', '', 'Fire extinguisher', 'no', 5, '2013-10-04', 1, 1, 0, 0),
(15, 1, NULL, 'datePicker', 'Date of next inspection- fire extinguisher', '', '', '', '', 6, '2013-10-04', 1, 1, 0, 0),
(16, 1, 1, 'radio', 'Is the vehicle clean and all items secure?', 'Vehicle clean and all items secure', '', 'Vehicle clean and all items secure', 'no', 7, '2013-10-04', 1, 0, 0, 0),
(17, 1, 1, 'radio', 'Are the safety tools present? (Jack, Wheel brace, glass breaker)', 'Safety tools', '', 'Safety tools', 'no', 8, '2013-10-04', 1, 0, 0, 0),
(18, 2, NULL, 'group', 'Are the following items operating as designed?', '', '', '', '', 1, '2013-10-04', 1, 1, 0, 0),
(19, 18, 1, 'radio', 'Lights and indicators', 'Lights and indicators', 'Critical alert', 'Lights and indicators', 'no', 1, '2013-10-04', 1, 1, 0, 0),
(20, 18, 1, 'radio', 'Warning alarms and horn', 'Warning alarms and horn', 'Critical alert', 'Warning alarms and horn', 'no', 2, '2013-10-04', 1, 1, 0, 0),
(21, 18, 1, 'radio', 'Gauges', 'Gauges', '', 'Gauges', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(22, 18, 1, 'radio', 'Windscreen wipers', 'Windscreen wipers', '', 'Windscreen wipers', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(23, 18, 1, 'radio', 'Two-way radio', 'Two-way radio', '', 'Two-way radio', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(24, 18, 1, 'radio', 'Seatbelt', 'Seatbelt', 'Critical alert', 'Seatbelt', 'no', 6, '2013-10-04', 1, 1, 0, 0),
(25, 18, 1, 'radio', 'Air conditioning', 'Air conditioning', '', 'Air conditioning', 'no', 7, '2013-10-04', 1, 0, 0, 0),
(26, 3, 1, 'radio', 'Is the vehicle free of damage?', 'Damage to body', '', 'Damage to body', 'no', 1, '2013-10-04', 1, 0, 0, 0),
(27, 3, 1, 'radio', 'Are all safety guards in place?', 'Safety guards', '', 'Safety guards', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(28, 3, 1, 'radio', 'Are the tyres correctly inflated with good tread and wheel nuts tight?', 'Tyres', '', 'Tyres', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(29, 28, NULL, 'radio', 'Are you authorised to inflate or change tyres?', '', 'Do not work on tyres unless authorised', '', 'no', 4, '2013-10-04', 1, 1, 0, 0),
(30, 3, 1, 'radio', 'Are the windscreen and mirrors clean and free of damage?', 'Windscreen', '', 'Windscreen', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(31, 4, 1, 'radio', 'Have you isolated the vehicle?', 'Isolation', 'Isolate vehicle before continuing', 'Isolation', 'no', 1, '2013-10-04', 1, 1, 0, 0),
(32, 4, NULL, 'group', 'Are the fluid levels acceptable?', '', '', '', '', 2, '2013-10-04', 1, 1, 0, 0),
(33, 32, 1, 'radio', 'Water', 'Water', '', 'Water', 'no', 1, '2013-10-04', 1, 0, 0, 0),
(34, 32, 1, 'radio', 'Hydraulic', 'Hydraulic', '', 'Hydraulic', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(35, 32, 1, 'radio', 'Brake', 'Brake', '', 'Brake', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(36, 32, 1, 'radio', 'Coolant', 'Coolant', '', 'Coolant', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(37, 32, 1, 'radio', 'Transmission', 'Transmission', '', 'Transmission', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(38, 32, 1, 'radio', 'Battery', 'Battery', '', 'Battery', 'no', 6, '2013-10-04', 1, 0, 0, 0),
(39, 4, 1, 'radio', 'Are the fan and drive belts in good working order?', 'Fan and drive belts', '', 'Fan and drive belts', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(40, 4, 1, 'radio', 'Has the daily greasing been carried out?', 'Daily greasing', '', 'Daily greasing', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(41, 4, 1, 'radio', 'Does the vehicle start?', 'Vehicle start', 'Critical alert', 'Vehicle start', 'no', 5, '2013-10-04', 1, 1, 0, 0),
(42, 4, 1, 'radio', 'Is the steering functioning properly?', 'Steering', 'Critical alert', 'Steering', 'no', 6, '2013-10-04', 1, 1, 0, 0),
(43, 4, 1, 'radio', 'Do the foot and hand brakes work properly?', 'Handbrake', 'Critical alert', 'Handbrake', 'no', 7, '2013-10-04', 1, 1, 0, 0),
(44, 5, NULL, 'text', 'Plant ID', '', '', '', '', 1, '2013-10-04', 1, 1, 0, 0),
(45, 5, NULL, 'text', 'Type of trailer', '', '', '', '', 2, '2013-10-04', 1, 1, 0, 0),
(46, 5, NULL, 'datePicker', 'Registration expiry', '', '', '', '', 3, '2013-10-04', 1, 1, 0, 0),
(47, 5, 1, 'radio', 'Is the trailer stable?', 'Trailer stability', 'Please secure the vehicle before continuing', 'Trailer stability', 'no', 4, '2013-10-04', 1, 1, 0, 0),
(48, 5, 1, 'radio', 'Is the trailer free of damage?', 'Trailer damage', '', 'Trailer damage', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(49, 5, 1, 'radio', 'Are all guards in place?', 'Guards', '', 'Guards', 'no', 6, '2013-10-04', 1, 0, 0, 0),
(50, 5, 1, 'radio', 'Are the tyres correctly inflated, in good working order with wheel nuts tightened?', 'Tyres', 'Critical alert', 'Tyres', 'no', 7, '2013-10-04', 1, 1, 0, 0),
(51, 5, 1, 'radio', 'Is the tow hitch free of damage and greased if necessary?', 'Hitch damage and grease', '', 'Hitch damage and grease', 'no', 9, '2013-10-04', 1, 0, 0, 0),
(52, 5, 1, 'radio', 'Are all lights and indicators functioning?', 'Lights and indicators', '', 'Lights and indicators', 'no', 10, '2013-10-04', 1, 0, 0, 0),
(53, 5, 1, 'radio', 'Are electrical cables or hydraulic hoses free of damage?', 'Electrical cables or hydraulic hoses damage', '', 'Electrical cables or hydraulic hoses damage', 'no', 11, '2013-10-04', 1, 0, 0, 0),
(54, 50, NULL, 'radio', 'Are you authorised to inflate/change tyres?', 'Person Authorised For Tyres?', 'Do not work on tyres unless authorised', 'Person Authorised For Tyres?', 'no', 8, '2013-10-04', 1, 1, 0, 0),
(55, 7, NULL, 'text', 'Hours', '', '', '', '', 1, '2013-10-04', 1, 1, 0, 0),
(56, 7, 1, 'radio', 'Is the motor housed correctly and free of damage?', 'Motor damage', '', 'Motor damage', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(57, 7, 1, 'radio', 'Are oil and fuel levels acceptable?', 'Oil and fuel', '', 'Oil and fuel', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(58, 7, 1, 'radio', 'Are all hoses fitted correctly and free from leaks? (Pump motors)', 'Hoses', '', 'Hoses', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(59, 7, 1, 'radio', 'Test the Residual Current Device. Does it function as designed? (Electrical generators)', 'Residual Current Device', 'Do not operate electrical equipment', 'Residual Current Device', 'no', 6, '2013-10-04', 1, 1, 0, 0),
(60, 7, 1, 'radio', 'Does the motor start?', 'Motor start', '', 'Motor start', 'no', 7, '2013-10-04', 1, 0, 0, 0),
(61, 6, 1, 'radio', 'Is the data plate intact and legible?', 'Data plate', '', 'Data plate', 'no', 1, '2013-10-04', 1, 0, 0, 0),
(62, 6, 1, 'radio', 'Are hydraulics in good working order? (rams, pumps, hoses)', 'Hydraulics', '', 'Hydraulics', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(63, 6, 1, 'radio', 'Are outriggers and their locking mechanism working properly?', 'Outriggers', '', 'Outriggers', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(64, 6, 1, 'radio', 'Is the slew ring or articulation point free of damage?', 'Slew ring/articulation point', '', 'Slew ring/articulation point', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(65, 6, 1, 'radio', 'Is the load cell, limiter and anti-two block working properly?', 'Load cell, limiter, anti-two block', '', 'Load cell, limiter, anti-two block', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(66, 6, 1, 'radio', 'Are all welds, boom sections and other structural elements free of damage?', 'Welds, boom sections', '', 'Welds, boom sections', 'no', 6, '2013-10-04', 1, 0, 0, 0),
(67, 6, 1, 'radio', 'Are wire rope, sheaves, hook and wear pads in good working order?', 'Wire rope, sheaves, hook', '', 'Wire rope, sheaves, hook', 'no', 7, '2013-10-04', 1, 0, 0, 0),
(68, 6, 1, 'radio', 'Are all bolts, locking pins and guides in place?', 'Bolts, locking pins, guides', '', 'Bolts, locking pins, guides', 'no', 8, '2013-10-04', 1, 0, 0, 0),
(69, 8, 1, 'radio', 'Is the data plate intact and legible?', 'Data plate', '', 'Data plate', 'no', 1, '2013-10-04', 1, 0, 0, 0),
(70, 8, 1, 'radio', 'Are hydraulics in good working order? (rams, pumps, hoses)', 'Hydraulics', '', 'Hydraulics', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(71, 8, 1, 'radio', 'Are all welds, booms sections and other structural elements free of damage?', 'Welds, booms sections', '', 'Welds, booms sections', 'no', 3, '2013-10-04', 1, 0, 0, 0),
(72, 8, 1, 'radio', 'Is the slew ring or articulation point free of damage?', 'Slew ring/articulation point', '', 'Slew ring/articulation point', 'no', 4, '2013-10-04', 1, 0, 0, 0),
(73, 8, 1, 'radio', 'Are the ground excavating tools in good working order? (Bucket, cutting edge, augers)', 'Ground excavating tools', '', 'Ground excavating tools', 'no', 5, '2013-10-04', 1, 0, 0, 0),
(74, 8, 1, 'radio', 'Are all bolts, locking pins and guides in place?', 'Bolts, locking pins, guides', '', 'Bolts, locking pins, guides', 'no', 6, '2013-10-04', 1, 0, 0, 0),
(75, 9, 1, 'radio', 'Are the ground conditions suitable?', 'Ground conditions', 'Do not continue if ground conditions are not suitable', 'Ground conditions', 'no', 1, '2013-10-04', 1, 0, 0, 0),
(76, 9, 1, 'radio', 'Are hydraulics in good working order? (rams, pumps, hoses)', 'Hydraulics', '', 'Hydraulics', 'no', 2, '2013-10-04', 1, 0, 0, 0),
(77, 9, NULL, 'radio', 'Are the outriggers and their locking mechanism working properly?', 'Outriggers', '', 'Outriggers', 'no', 3, '2013-10-04', 1, 1, 0, 0),
(78, 9, NULL, 'radio', 'Is the slew ring free of damage?', 'Slew ring', '', 'Slew ring', 'no', 4, '2013-10-04', 1, 1, 0, 0),
(79, 9, NULL, 'radio', 'Are all welds, boom sections and other structural elements free of damage?', 'Welds, boom sections', '', 'Welds, boom sections', 'no', 5, '2013-10-04', 1, 1, 0, 0),
(80, 9, NULL, 'radio', 'Are the attachment points, emergency descent device and handrails in place?', 'Attachment points, handrails', '', 'Attachment points, handrails', 'no', 6, '2013-10-04', 1, 1, 0, 0),
(81, 9, NULL, 'radio', 'Have the secondary ground controls and communication methods been checked?', 'Secondary ground controls', '', 'Secondary ground controls', 'no', 7, '2013-10-04', 1, 1, 0, 0),
(82, 9, NULL, 'radio', 'Is the load sensors and wind meter working properly?', 'Load sensors, wind meter', '', 'Load sensors, wind meter', 'no', 8, '2013-10-04', 1, 1, 0, 0),
(83, 9, NULL, 'radio', 'Are all bolts, locking pins and guides in place?', 'Bolts, locking pins, guides', '', 'Bolts, locking pins, guides', 'no', 9, '2013-10-04', 1, 1, 0, 0),
(84, 28, 1, 'radio', 'dfhrtg', 'dgsdgs', 'george test message', 'dsdsf', 'no', 2, '2013-10-15', 1, 1, 1, 0),
(85, 7, 1, 'root', 'Are all guards in place?', 'Guards', '', '', '', 2, '2013-10-15', 1, 0, 1, 0),
(86, 85, 1, 'radio', 'Are all guards in place?', 'Guards', '', 'Guards', 'no', 2, '2013-10-15', 1, 0, 0, 0),
(87, 7, 1, 'root', 'Are all guards in place?', 'Guards', '', '', 'no', 1, '2013-10-15', 1, 0, 1, 0),
(88, 7, 1, 'radio', 'Are all guards in place?', 'Guards', '', 'Guards', 'no', 3, '2013-10-15', 1, 0, 0, 0),
(89, 7, 1, 'root', 'Are all guards in place?', 'Guards', '', '', 'no', 2, '2013-10-15', 1, 1, 1, 0),
(90, 8, 1, 'root', 'fe', 'rew', '', '', 'yes', 1, '2013-10-16', 1, 1, 1, 0),
(91, 90, 1, 'radio', 'rwe', 'rew', 'rewr', 'rewr', 'yes', 8, '2013-10-16', 1, 1, 0, 0),
(92, NULL, 1, 'root', 'appendix', 'appendix', '', '', '', 13, '2013-10-16', 1, 1, 1, 0),
(93, 92, 1, 'radio', 'Question 2', 'Title 2', '', '', 'no', 1, '2013-10-16', 1, 0, 0, 0),
(94, 92, 1, 'root', 'Question 1', 'Title 1', '', '', 'yes', 1, '2013-10-16', 1, 1, 1, 0),
(95, 92, 1, 'root', 'Question 3', 'Title 3', '', '', 'yes', 1, '2013-10-16', 1, 1, 0, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `default_inspection_fields`
--
ALTER TABLE `default_inspection_fields`
  ADD CONSTRAINT `FK_B9A99DBAF675F31B` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_B9A99DBA727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `default_inspection_fields` (`id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
