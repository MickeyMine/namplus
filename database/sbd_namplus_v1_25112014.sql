-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2014 at 03:36 AM
-- Server version: 5.6.11
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sbd_namplus_v1`
--
CREATE DATABASE IF NOT EXISTS `sbd_namplus_v1` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `sbd_namplus_v1`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_description` varchar(200) DEFAULT NULL,
  `cat_parent_id` int(11) DEFAULT NULL,
  `cat_is_gallery` tinyint(4) DEFAULT '0',
  `cat_is_offer` tinyint(4) DEFAULT '0',
  `cat_is_competition` tinyint(4) DEFAULT '0',
  `cat_order` int(11) NOT NULL,
  `cat_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_description`, `cat_parent_id`, `cat_is_gallery`, `cat_is_offer`, `cat_is_competition`, `cat_order`, `cat_status`) VALUES
(15, 'Women', 'Women', NULL, 1, 0, 0, 1, 1),
(16, 'Style', 'Style', NULL, 0, 0, 0, 2, 1),
(17, 'Life', 'Life', NULL, 0, 0, 0, 3, 1),
(18, 'Nam Man', 'Nam Man', NULL, 0, 0, 0, 4, 1),
(19, 'Forum', 'Forum', NULL, 0, 0, 0, 5, 1),
(20, 'Gallery', 'Gallery', 15, 1, 0, 0, 1, 1),
(21, 'Models', 'Models', 15, 1, 0, 0, 2, 1),
(22, 'Stars', 'Stars', 15, 1, 0, 0, 3, 1),
(23, 'Fashion', 'Fashion', 16, 0, 0, 0, 1, 1),
(24, 'Grooming', 'Grooming', 16, 0, 0, 0, 2, 1),
(25, 'Cars & Bikes', 'Cars & Bikes', 17, 0, 0, 0, 1, 1),
(26, 'Teach & Gear', 'Teach & Gear', 17, 0, 0, 0, 2, 1),
(27, 'Drink', 'Drink', 17, 0, 0, 0, 3, 1),
(28, 'Dine & Wine', 'Dine & Wine', 17, 0, 0, 0, 4, 1),
(29, 'Health & Fitness', 'Health & Fitness', 17, 0, 0, 0, 5, 1),
(30, 'Travel', 'Travel', 17, 0, 0, 0, 6, 1),
(31, 'Hobby', 'Hobby', 17, 0, 0, 0, 7, 1),
(32, 'Experiences', 'Experiences', 17, 0, 0, 0, 8, 1),
(33, 'Relationships', 'Relationships', 17, 0, 0, 0, 9, 1),
(34, 'Icon', 'Icon', 18, 0, 0, 0, 1, 1),
(35, 'Stars', 'Stars', 18, 0, 0, 0, 2, 1),
(36, 'Power & Money', 'Power & Money', 18, 0, 0, 0, 3, 1),
(37, 'Diệp Lâm Anh', 'Diệp Lâm Anh', 20, 1, 0, 0, 1, 1),
(38, 'Nhã Trúc', 'Nhã Trúc', 20, 1, 0, 0, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(21) DEFAULT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_pass` varchar(50) NOT NULL,
  `customer_first_name` varchar(50) NOT NULL,
  `customer_last_name` varchar(150) NOT NULL,
  `customer_profession` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(12) NOT NULL,
  `customer_address` varchar(250) NOT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `customer_facebook` varchar(250) NOT NULL,
  `customer_author_uid` varchar(250) NOT NULL,
  `customer_provider` varchar(250) NOT NULL,
  `customer_payment_type` int(11) NOT NULL,
  `customer_status` tinyint(4) NOT NULL COMMENT '-1: pending; 0: active; 1: login; 2: blocking',
  `customer_first_login` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: yes; 1: no',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `image_gallery`
--

CREATE TABLE IF NOT EXISTS `image_gallery` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_path` varchar(150) NOT NULL,
  `img_description` varchar(150) NOT NULL,
  `img_cat_id` int(11) DEFAULT NULL,
  `img_new_id` int(11) DEFAULT NULL,
  `img_offer_id` int(11) DEFAULT NULL,
  `img_nam_archive` tinyint(4) NOT NULL DEFAULT '0',
  `img_is_banner` tinyint(4) NOT NULL DEFAULT '0',
  `img_order` int(11) NOT NULL,
  `img_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `new_id` int(11) NOT NULL AUTO_INCREMENT,
  `new_title` varchar(100) NOT NULL,
  `new_description` varchar(200) NOT NULL,
  `new_content` text NOT NULL,
  `new_type` tinyint(4) NOT NULL DEFAULT '0',
  `new_img_path` varchar(150) NOT NULL,
  `new_publish_date` date NOT NULL,
  `new_cat_id` int(11) NOT NULL,
  `new_link_id` int(11) DEFAULT NULL,
  `new_link_order` int(11) DEFAULT NULL,
  `new_status` int(11) NOT NULL,
  PRIMARY KEY (`new_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE IF NOT EXISTS `offers` (
  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_title` varchar(50) NOT NULL,
  `offer_description` varchar(100) NOT NULL,
  `offer_content` text NOT NULL,
  `offer_question_content` text NOT NULL,
  `offer_image_path` varchar(150) NOT NULL,
  `offer_top_image` varchar(150) NOT NULL,
  `offer_bottom_image` varchar(150) NOT NULL,
  `offer_start_date` date NOT NULL,
  `offer_end_date` date NOT NULL,
  `offer_start_time` time NOT NULL,
  `offer_end_time` time NOT NULL,
  `offer_rules` text NOT NULL,
  `offer_value` varchar(50) NOT NULL,
  `offer_cat_id` int(11) NOT NULL,
  `offer_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`offer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_answers`
--

CREATE TABLE IF NOT EXISTS `offer_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer_content` text NOT NULL,
  `answer_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_cus_answers`
--

CREATE TABLE IF NOT EXISTS `offer_cus_answers` (
  `cusans_id` int(11) NOT NULL AUTO_INCREMENT,
  `cusans_customer_id` int(11) NOT NULL,
  `cusans_offer_id` int(11) NOT NULL,
  `cusans_content` text NOT NULL,
  PRIMARY KEY (`cusans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_locations`
--

CREATE TABLE IF NOT EXISTS `offer_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `location_address` varchar(200) NOT NULL,
  `location_map_x` varchar(14) NOT NULL,
  `location_map_y` varchar(14) NOT NULL,
  `location_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_questions`
--

CREATE TABLE IF NOT EXISTS `offer_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_content` text NOT NULL,
  `question_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: text; 1: mulity choices',
  `offer_id` int(11) NOT NULL,
  `question_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `offer_vouchers`
--

CREATE TABLE IF NOT EXISTS `offer_vouchers` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_number` varchar(15) NOT NULL,
  `voucher_offer_id` int(11) NOT NULL,
  `voucher_status` int(11) NOT NULL,
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_type`
--

CREATE TABLE IF NOT EXISTS `payment_type` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(50) NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `payment_type`
--

INSERT INTO `payment_type` (`payment_id`, `payment_type`) VALUES
(1, 'Cash'),
(2, 'Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `register_form`
--

CREATE TABLE IF NOT EXISTS `register_form` (
  `register_id` int(11) NOT NULL AUTO_INCREMENT,
  `register_title` varchar(50) NOT NULL,
  `register_description` text NOT NULL,
  `register_type` tinyint(4) NOT NULL COMMENT '0: Customer; 1: Professional',
  PRIMARY KEY (`register_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `register_form`
--

INSERT INTO `register_form` (`register_id`, `register_title`, `register_description`, `register_type`) VALUES
(1, 'Join NAMplus by subscription', '<p>NAMPlus is exclusive to subscribers and provides to unique and experiences, great offers from some of Esquire''s favourite brands, plus exclusive giveaways and competitions</p>', 0),
(2, 'Join NAMplus by profession', '<p>NAMPlus is exclusive to subscribers and provides to unique and experiences, great offers from some of Esquire''s favourite brands, plus exclusive giveaways and competitions</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `subscription_id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_type` varchar(100) NOT NULL,
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `subscription_type`) VALUES
(1, '6 months'),
(2, '1 year'),
(3, 'Digital');

-- --------------------------------------------------------

--
-- Table structure for table `userlevelpermissions`
--

CREATE TABLE IF NOT EXISTS `userlevelpermissions` (
  `userlevelid` int(11) NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `permission` int(11) NOT NULL,
  PRIMARY KEY (`userlevelid`,`tablename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userlevelpermissions`
--

INSERT INTO `userlevelpermissions` (`userlevelid`, `tablename`, `permission`) VALUES
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}categories', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}customers', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}image_gallery', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}news', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}offers', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_answers', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_details', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_locations', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}offer_questions', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}subscriptions', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}userlevelpermissions', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}userlevels', 0),
(0, '{621448A2-A15A-4302-8B90-FC8E171BD28F}users', 0);

-- --------------------------------------------------------

--
-- Table structure for table `userlevels`
--

CREATE TABLE IF NOT EXISTS `userlevels` (
  `userlevelid` int(11) NOT NULL,
  `userlevelname` varchar(255) NOT NULL,
  PRIMARY KEY (`userlevelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userlevels`
--

INSERT INTO `userlevels` (`userlevelid`, `userlevelname`) VALUES
(-1, 'Administrator'),
(0, 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(150) NOT NULL,
  `user_pass` varchar(50) NOT NULL,
  `privilege_id` int(11) NOT NULL,
  `user_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_pass`, `privilege_id`, `user_status`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', -1, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `viewcategoriesparent`
--
CREATE TABLE IF NOT EXISTS `viewcategoriesparent` (
`cat_id` int(11)
,`cat_name` varchar(50)
,`cat_description` varchar(200)
,`cat_parent_id` int(11)
,`cat_order` int(11)
,`cat_status` tinyint(4)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `viewnews`
--
CREATE TABLE IF NOT EXISTS `viewnews` (
`cat_id` int(11)
,`cat_name` varchar(50)
,`cat_description` varchar(200)
,`cat_parent_id` int(11)
,`cat_is_offer` tinyint(4)
,`cat_is_competition` tinyint(4)
,`cat_order` int(11)
,`cat_status` tinyint(4)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `viewnewstops`
--
CREATE TABLE IF NOT EXISTS `viewnewstops` (
`new_id` int(11)
,`new_title` varchar(100)
,`new_description` varchar(200)
,`new_content` text
,`new_type` tinyint(4)
,`new_img_path` varchar(150)
,`new_publish_date` date
,`new_cat_id` int(11)
,`new_link_id` int(11)
,`new_status` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `viewoffers`
--
CREATE TABLE IF NOT EXISTS `viewoffers` (
`cat_id` int(11)
,`cat_name` varchar(50)
,`cat_description` varchar(200)
,`cat_parent_id` int(11)
,`cat_is_offer` tinyint(4)
,`cat_is_competition` tinyint(4)
,`cat_order` int(11)
,`cat_status` tinyint(4)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `viewquestionmulti`
--
CREATE TABLE IF NOT EXISTS `viewquestionmulti` (
`question_id` int(11)
,`question_content` text
,`question_type` tinyint(4)
,`offer_id` int(11)
,`question_status` tinyint(4)
);
-- --------------------------------------------------------

--
-- Structure for view `viewcategoriesparent`
--
DROP TABLE IF EXISTS `viewcategoriesparent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewcategoriesparent` AS select `categories`.`cat_id` AS `cat_id`,`categories`.`cat_name` AS `cat_name`,`categories`.`cat_description` AS `cat_description`,`categories`.`cat_parent_id` AS `cat_parent_id`,`categories`.`cat_order` AS `cat_order`,`categories`.`cat_status` AS `cat_status` from `categories` where isnull(`categories`.`cat_parent_id`);

-- --------------------------------------------------------

--
-- Structure for view `viewnews`
--
DROP TABLE IF EXISTS `viewnews`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewnews` AS select `cat`.`cat_id` AS `cat_id`,`cat`.`cat_name` AS `cat_name`,`cat`.`cat_description` AS `cat_description`,`cat`.`cat_parent_id` AS `cat_parent_id`,`cat`.`cat_is_offer` AS `cat_is_offer`,`cat`.`cat_is_competition` AS `cat_is_competition`,`cat`.`cat_order` AS `cat_order`,`cat`.`cat_status` AS `cat_status` from `categories` `cat` where ((`cat`.`cat_status` = 1) and (`cat`.`cat_is_offer` = 0) and (`cat`.`cat_is_competition` = 0) and (not(`cat`.`cat_id` in (select `c`.`cat_parent_id` from `categories` `c` where (`c`.`cat_parent_id` = `cat`.`cat_id`) group by `c`.`cat_parent_id`))));

-- --------------------------------------------------------

--
-- Structure for view `viewnewstops`
--
DROP TABLE IF EXISTS `viewnewstops`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewnewstops` AS select `news`.`new_id` AS `new_id`,`news`.`new_title` AS `new_title`,`news`.`new_description` AS `new_description`,`news`.`new_content` AS `new_content`,`news`.`new_type` AS `new_type`,`news`.`new_img_path` AS `new_img_path`,`news`.`new_publish_date` AS `new_publish_date`,`news`.`new_cat_id` AS `new_cat_id`,`news`.`new_link_id` AS `new_link_id`,`news`.`new_status` AS `new_status` from `news` where ((`news`.`new_status` = 1) and (`news`.`new_cat_id` is not null) and isnull(`news`.`new_link_id`) and `news`.`new_type` in (select `news`.`new_type` from `news` where ((`news`.`new_type` = 1) or (`news`.`new_type` = 2))));

-- --------------------------------------------------------

--
-- Structure for view `viewoffers`
--
DROP TABLE IF EXISTS `viewoffers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewoffers` AS select `categories`.`cat_id` AS `cat_id`,`categories`.`cat_name` AS `cat_name`,`categories`.`cat_description` AS `cat_description`,`categories`.`cat_parent_id` AS `cat_parent_id`,`categories`.`cat_is_offer` AS `cat_is_offer`,`categories`.`cat_is_competition` AS `cat_is_competition`,`categories`.`cat_order` AS `cat_order`,`categories`.`cat_status` AS `cat_status` from `categories` where ((`categories`.`cat_status` = 1) and `categories`.`cat_id` in (select `categories`.`cat_id` from `categories` where ((`categories`.`cat_is_offer` = 1) or (`categories`.`cat_is_competition` = 1))));

-- --------------------------------------------------------

--
-- Structure for view `viewquestionmulti`
--
DROP TABLE IF EXISTS `viewquestionmulti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewquestionmulti` AS select `offer_questions`.`question_id` AS `question_id`,`offer_questions`.`question_content` AS `question_content`,`offer_questions`.`question_type` AS `question_type`,`offer_questions`.`offer_id` AS `offer_id`,`offer_questions`.`question_status` AS `question_status` from `offer_questions` where (`offer_questions`.`question_type` = 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
