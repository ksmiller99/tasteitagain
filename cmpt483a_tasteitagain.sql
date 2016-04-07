-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2016 at 11:04 PM
-- Server version: 5.5.48-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cmpt483a_tasteitagain`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`cmpt483a`@`localhost` PROCEDURE `validate_timesheet`(
	IN punch_in DATETIME,
	IN punch_out DATETIME
)
    NO SQL
    DETERMINISTIC
BEGIN
	IF punch_out <= punch_in THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Punch out must be later than punch in.';
	END IF;
	
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `CATEGORIES`
--

CREATE TABLE IF NOT EXISTS `CATEGORIES` (
  `CATID` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Auto-increment integer',
  `NAME` varchar(32) NOT NULL COMMENT 'Name of product Category',
  PRIMARY KEY (`CATID`),
  UNIQUE KEY `NAME` (`NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Categories for PRODUCTS' AUTO_INCREMENT=29 ;

--
-- Dumping data for table `CATEGORIES`
--

INSERT INTO `CATEGORIES` (`CATID`, `NAME`) VALUES
(000001, 'Appetizers'),
(000002, 'Beverages'),
(000017, 'Coupons'),
(000011, 'Desserts'),
(000010, 'Entrées'),
(000026, 'Entrées - Beef'),
(000027, 'Entrées - Chicken'),
(000025, 'Entrées - Goat'),
(000024, 'Entrées - Pork'),
(000022, 'Entrées - Seafood'),
(000023, 'Entrées - Vegetarian'),
(000018, 'Merchandise'),
(000009, 'Salads'),
(000006, 'Soups'),
(000028, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `COMPANYINFO`
--

CREATE TABLE IF NOT EXISTS `COMPANYINFO` (
  `COMPID` int(4) NOT NULL AUTO_INCREMENT COMMENT 'Company ID',
  `NAME` varchar(32) NOT NULL COMMENT 'Name of company',
  `ADDLINE1` varchar(32) NOT NULL COMMENT 'Address of company',
  `ADDLINE2` varchar(32) NOT NULL,
  `CITY` varchar(32) NOT NULL,
  `STATE` char(2) NOT NULL,
  `ZIP` int(5) NOT NULL,
  `PHONE` bigint(10) NOT NULL,
  `TAXRATE` float NOT NULL COMMENT 'Local Tax rate - .07 = 7%',
  PRIMARY KEY (`COMPID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='One record for company info' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `COMPANYINFO`
--

INSERT INTO `COMPANYINFO` (`COMPID`, `NAME`, `ADDLINE1`, `ADDLINE2`, `CITY`, `STATE`, `ZIP`, `PHONE`, `TAXRATE`) VALUES
(2, 'Taste It Again', '271 Glenwood Avenue', '', 'Bloomfield', 'NJ', 7003, 9737435140, 0.07);

-- --------------------------------------------------------

--
-- Table structure for table `CPANELUSERS`
--

CREATE TABLE IF NOT EXISTS `CPANELUSERS` (
  `USERID` varchar(32) NOT NULL COMMENT 'Auto-increment integer',
  `PWHASH` varchar(32) NOT NULL COMMENT 'md5 hash of password',
  `ADMINFLAG` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE if user can administer this table',
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Gateway to website only. All users must pass through here.';

--
-- Dumping data for table `CPANELUSERS`
--

INSERT INTO `CPANELUSERS` (`USERID`, `PWHASH`, `ADMINFLAG`) VALUES
('ksmiller99', 'c57d4dbfea564b7c33cfae2b41864503', 1),
('marios', 'eea8c3fb57e97d8a42b1814020522638', 0),
('mhaight', '5f4dcc3b5aa765d61d8327deb882cf99', 1),
('test2', '3afc79b597f88a72528e864cf81856d2', 0),
('testAdmin', '0b98f012d0b48b0c75528aa0673f9909', 1),
('testuser', '5f4dcc3b5aa765d61d8327deb882cf99', 0);

-- --------------------------------------------------------

--
-- Table structure for table `CUSTOMERS`
--

CREATE TABLE IF NOT EXISTS `CUSTOMERS` (
  `USERID` int(10) unsigned NOT NULL COMMENT 'FK from USERS',
  `ADDLINE1` varchar(30) NOT NULL COMMENT 'Customer''s registered address. Used as default delivery address',
  `ADDLINE2` varchar(30) NOT NULL,
  `CITY` varchar(30) NOT NULL,
  `STATE` char(2) NOT NULL,
  `ZIP` varchar(5) NOT NULL,
  `EMAILFLAG` tinyint(1) NOT NULL COMMENT 'TRUE if customer has signed up for e-mail news letters and coupons',
  `TXTMSGFLAG` tinyint(1) NOT NULL COMMENT 'TRUE if customer has signed up for text message coupons',
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registered customers of the restaurant';

--
-- Dumping data for table `CUSTOMERS`
--

INSERT INTO `CUSTOMERS` (`USERID`, `ADDLINE1`, `ADDLINE2`, `CITY`, `STATE`, `ZIP`, `EMAILFLAG`, `TXTMSGFLAG`) VALUES
(100, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', 1, 1),
(146, '789 Fake', 'apt 12', 'Bloomfield', 'NJ', '07003', 0, 0),
(147, '789 Fake', 'apt 12', 'Bloomfield', 'NJ', '07003', 0, 0),
(152, '123', 'apt12', 'Bloomfield', 'NJ', '07003', 1, 1),
(153, '56 Woodmont Road', '', 'Pine Brook', 'NJ', '07058', 0, 0),
(154, '123 Main St', 'Apt 12', 'Bloomfield', 'NJ', '07003', 0, 0),
(155, '789 Fake', 'Basement', 'Upper Montclair', 'NJ', '07013', 0, 0),
(156, '10 Downing St', '', 'Newark', 'NJ', '07002', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `EMPLOYEES`
--

CREATE TABLE IF NOT EXISTS `EMPLOYEES` (
  `USERID` int(10) unsigned NOT NULL COMMENT 'FK from USERS',
  `ADDLINE1` varchar(30) NOT NULL COMMENT 'Employees''s registered address. Used as default USPS address',
  `ADDLINE2` varchar(30) NOT NULL,
  `CITY` varchar(30) NOT NULL,
  `STATE` char(2) NOT NULL,
  `ZIP` varchar(5) NOT NULL,
  `WAGE_TYPE` enum('hourly','weekly') NOT NULL,
  `WAGE_AMOUNT` float NOT NULL,
  `EMAILFLAG` tinyint(1) NOT NULL COMMENT 'TRUE if employee has signed up for e-mail news letters and alerts',
  `TXTMSGFLAG` tinyint(1) NOT NULL COMMENT 'TRUE if employess has signed up for text message alerts',
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registered employees of the restaurant';

--
-- Dumping data for table `EMPLOYEES`
--

INSERT INTO `EMPLOYEES` (`USERID`, `ADDLINE1`, `ADDLINE2`, `CITY`, `STATE`, `ZIP`, `WAGE_TYPE`, `WAGE_AMOUNT`, `EMAILFLAG`, `TXTMSGFLAG`) VALUES
(100, '123 Main', '', 'Anytown', 'NJ', '12345', 'hourly', 17.02, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ORDERITEMS`
--

CREATE TABLE IF NOT EXISTS `ORDERITEMS` (
  `ORDID` varchar(13) NOT NULL COMMENT 'FK from ORDERS',
  `LINENUM` int(6) NOT NULL COMMENT 'Line Item number',
  `NAME` varchar(32) NOT NULL COMMENT 'Product Name. Copied from PRODUCTS when order is created',
  `PRICE` float NOT NULL COMMENT 'Copied from PRODUCTS when order is created',
  `TAXABLE` tinyint(1) NOT NULL COMMENT 'Copied from PRODUCTS when order is created',
  `QTY` int(11) NOT NULL COMMENT 'Inserted from shopping cart  when order is created',
  `EXTPRICE` float NOT NULL COMMENT '(Price + Tax)* QTY. Calculated and inserted when order is created',
  PRIMARY KEY (`ORDID`,`LINENUM`),
  KEY `ORDID` (`ORDID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Line Items for an ORDERS, child records of ORDERS';

--
-- Dumping data for table `ORDERITEMS`
--

INSERT INTO `ORDERITEMS` (`ORDID`, `LINENUM`, `NAME`, `PRICE`, `TAXABLE`, `QTY`, `EXTPRICE`) VALUES
('50cd473367452', 1, 'Solomon Gundy', 4.95, 0, 5, 24.75),
('50cd473367452', 2, 'Codfish Cake', 4.95, 0, 5, 24.75),
('50cd473367452', 3, 'Cocktail Patties.', 4.95, 0, 3, 14.85),
('50cd473367452', 4, 'Soup of the day', 3, 0, 2, 6),
('50cd473367452', 5, 'Spicy Curried Chicken', 9, 0, 3, 27),
('50cd49c16d4fe', 1, 'Ecovitched Chicken Strips', 4.95, 0, 3, 14.85),
('50cd4a0ee83da', 1, 'Sprite', 1.25, 0, 9, 11.25),
('50cd4f8b62b7b', 1, 'Ecovitched Chicken Strips', 4.95, 0, 4, 19.8),
('50ce892d3624b', 1, 'Solomon Gundy', 4.95, 0, 1, 4.95),
('50ce892d3624b', 2, 'Sprite', 1.25, 0, 1, 1.25),
('50ce892d3624b', 3, 'Jerked Chicken Salad', 7.5, 0, 3, 22.5),
('50ce8982d3902', 1, 'Solomon Gundy', 4.95, 0, 1, 4.95),
('50ce89f998af4', 1, 'Ecovitched Chicken Strips', 4.95, 0, 1, 4.95),
('50ce96767ecab', 1, 'Ginger Ale', 1.25, 0, 2, 2.5),
('50ce96767ecab', 2, 'Chicken House Salad', 8, 0, 4, 32),
('50ce96767ecab', 3, 'Spicy Curried Chicken', 9, 0, 3, 27),
('50cf3e86641a5', 1, 'Solomon Gundy', 4.95, 0, 1, 4.95),
('50cf3e86641a5', 2, 'Soup of the day', 3, 0, 4, 12),
('50d29905d11e9', 1, 'Curried Goat (Spicy)', 12, 0, 1, 12),
('50d3ad5392061', 1, 'Solomon Gundy', 4.95, 0, 1, 4.95),
('50d3ad5392061', 2, 'Sprite', 1.25, 0, 1, 1.25),
('50d3ad5392061', 3, 'Jerked Chicken Salad', 7.5, 0, 3, 22.5),
('50d3ad5392061', 4, 'Ice Cream', 2.5, 0, 2, 5),
('56d61c59b1da2', 1, 'Solomon Gundy', 4.95, 0, 1, 4.95);

-- --------------------------------------------------------

--
-- Table structure for table `ORDERS`
--

CREATE TABLE IF NOT EXISTS `ORDERS` (
  `ORDID` varchar(13) NOT NULL COMMENT 'PHP created UniqueID',
  `CUSTID` int(10) unsigned NOT NULL COMMENT 'Customer who created order, not necessarily who will receive it',
  `FULLNAME` varchar(32) NOT NULL COMMENT 'Default is customer name',
  `CONTACTPHONE` int(10) NOT NULL COMMENT 'Default is customer phone',
  `DELIVERFLAG` tinyint(1) DEFAULT NULL,
  `DELADDLINE1` varchar(32) NOT NULL COMMENT 'Delivery Address Line 1. Default is Customer address',
  `DELADDLINE2` varchar(32) NOT NULL COMMENT 'Delivery Address Line 2',
  `DELCITY` varchar(32) NOT NULL COMMENT 'Delivery City',
  `DELSTATE` char(2) NOT NULL COMMENT 'Delivery State',
  `DELZIP` varchar(5) NOT NULL COMMENT 'Deivery Zip Code',
  `TIME` datetime NOT NULL COMMENT 'Date/Time order created',
  PRIMARY KEY (`ORDID`),
  KEY `CUSTID` (`CUSTID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ORDERS`
--

INSERT INTO `ORDERS` (`ORDID`, `CUSTID`, `FULLNAME`, `CONTACTPHONE`, `DELIVERFLAG`, `DELADDLINE1`, `DELADDLINE2`, `DELCITY`, `DELSTATE`, `DELZIP`, `TIME`) VALUES
('50cd473367452', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-15 22:59:47'),
('50cd49c16d4fe', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-15 23:10:41'),
('50cd4a0ee83da', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-15 23:11:58'),
('50cd4f8b62b7b', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-15 23:35:23'),
('50ce892d3624b', 153, 'Matt Haight', 2012133792, 0, '56 woodmont rd', '12', 'pine brook', 'nj', '07058', '2012-12-16 21:53:33'),
('50ce8982d3902', 153, 'Matt Haight', 2012133792, 0, '56 woodmont rd', '', 'pine brook', 'nj', '07058', '2012-12-16 21:54:58'),
('50ce89f998af4', 153, 'Matt Haight', 2012133792, 0, '56 woodmont rd', '', 'pine brook', 'nj', '07058', '2012-12-16 21:56:57'),
('50ce96767ecab', 153, 'Matt Haight', 2012133792, 0, '56 woodmont rd', '', 'pine brook', 'nj', '07058', '2012-12-16 22:50:14'),
('50cf3e86641a5', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-17 10:47:18'),
('50d29905d11e9', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2012-12-19 23:50:13'),
('50d3ad5392061', 153, 'Matt Haight', 2012133793, 0, '56 Woodmont Road', '', 'Pine Brook', 'NJ', '07058', '2012-12-20 19:29:07'),
('56d61c59b1da2', 100, 'Kevin Miller', 2147483647, 0, '97 Maolis Ave', '', 'Bloomfield', 'NJ', '07003', '2016-03-01 17:48:57');

-- --------------------------------------------------------

--
-- Table structure for table `PRODUCTS`
--

CREATE TABLE IF NOT EXISTS `PRODUCTS` (
  `PRODID` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Auto-increment integer',
  `NAME` varchar(32) DEFAULT NULL COMMENT 'Product name',
  `DESCRIPTION` varchar(256) DEFAULT NULL COMMENT 'Product description',
  `CATID` int(6) unsigned zerofill NOT NULL COMMENT 'FK from CATEGORIES',
  `PRICE` float NOT NULL,
  `TAXABLE` tinyint(1) NOT NULL COMMENT 'TRUE if product is taxable',
  `COUPONSTARTDATE` date DEFAULT NULL COMMENT 'Start date if product is a coupon',
  `COUPONENDDATE` date DEFAULT NULL COMMENT 'End date if product is a coupon',
  `HIDEFLAG` tinyint(1) NOT NULL COMMENT 'TRUE if product is hidden from users and customers',
  `PHOTO` mediumblob COMMENT 'Photo of product. For future implmentation',
  PRIMARY KEY (`PRODID`),
  KEY `FK_PRODUCT_CATEGORIES` (`CATID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='All products from all categories; coupons' AUTO_INCREMENT=1069 ;

--
-- Dumping data for table `PRODUCTS`
--

INSERT INTO `PRODUCTS` (`PRODID`, `NAME`, `DESCRIPTION`, `CATID`, `PRICE`, `TAXABLE`, `COUPONSTARTDATE`, `COUPONENDDATE`, `HIDEFLAG`, `PHOTO`) VALUES
(000001, 'Sprite', 'Soda', 000002, 1.25, 0, '0000-00-00', '0000-00-00', 0, NULL),
(000003, 'Ginger Ale', 'Soda', 000002, 1.25, 0, NULL, NULL, 0, NULL),
(000004, 'Coca Cola', 'Sodas', 000002, 1.25, 0, NULL, NULL, 0, NULL),
(001012, 'Buy 1', 'Buy 1, Get 1', 000017, 0, 0, '2013-01-01', '2031-01-31', 1, NULL),
(001017, 'Homemade Fruit Punch', 'Homemade Fruit Punch', 000002, 3, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001020, 'Homemade Ginger Beer', 'Homemade Ginger Beer made from fresh ingredients', 000002, 3, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001021, 'Jamaican Sodas', 'Jamaican Sodas', 000002, 1.75, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001026, 'Soup of the day', 'Call for available soups', 000006, 3, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001028, 'Sorrel', '', 000002, 3, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001029, 'Solomon Gundy', 'A savory blend of herbs, spices and smoked herring served on top of water crackers. (choose spicy or mild)', 000001, 4.95, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001030, 'Ecovitched Chicken Strips', 'Finger size pieces of seasoned chicken breast battered and baked served with julienne carrots, sweet peppers and tangy sauce. (choose spicy or mild)', 000001, 4.95, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001031, 'Codfish Cake', 'Shredded codfish combined with savory seasonings and pan-fried golden brown to perfection,', 000001, 4.95, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001032, 'Cocktail Patties.', 'Puffed pastry filled with seasoned ground beef, chicken or vegetable.', 000001, 4.95, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001033, 'Jerked Chicken Salad', 'Tender pieces of chicken breast atop lettuce., tomato., carrot, spinach and onions,', 000009, 7.5, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001034, 'Grilled Chicken Salad', 'Tender pieces of chicken breast atop lettuce., tomato., carrot, spinach and onions,', 000009, 7.5, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001035, 'House Salad', 'Mixed greens, cherry tomatoes, red peppers, green peppers, cabbage and orange slices.', 000009, 6, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001036, 'Chicken House Salad', 'Mixed greens, cherry tomatoes, red peppers, green peppers, cabbage and orange slices.', 000009, 8, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001037, 'Shrimp House Salad', 'Mixed greens, cherry tomatoes, red peppers, green peppers, cabbage and orange slices.', 000009, 9, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001038, 'Spicy Curried Chicken', 'Tender pieces of chicken simmered in curry sauce with potatoes,', 000027, 9, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001039, 'Mild Curried Chicken', 'Tender pieces of chicken simmered in curry sauce with potatoes,', 000027, 9, 0, '0000-00-00', '0000-00-00', 0, NULL),
(001043, 'Brown Stew Chicken', 'Pan-seared chicken seasoned with allspice and simmered in its own juices.', 000027, 10.5, 0, NULL, NULL, 0, NULL),
(001044, 'Chicken Roti', 'Tender chunks of curried chicken with potatoes wrapped in Dhalpuri roti skin.', 000027, 7.5, 0, NULL, NULL, 0, NULL),
(001045, 'Oxtail', 'Tender pieces of oxtail stewed with herbs, spices, and butter beans.', 000026, 12, 0, NULL, NULL, 0, NULL),
(001046, 'Stewed Beef', 'Tender chunks of beef stewed with carrot and potatoes.', 000026, 11, 0, NULL, NULL, 0, NULL),
(001047, 'Stew Peas w/Beef', 'Red Kidney beans stewed with herbs and spices and beef chunks.', 000026, 10.5, 0, NULL, NULL, 0, NULL),
(001048, 'Curried Goat (Spicy)', 'Tender goat meat simmered in curry sauce with potatoes', 000025, 12, 0, NULL, NULL, 0, NULL),
(001049, 'Curried Goat (Mild)', 'Tender goat meat simmered in curry sauce with potatoes', 000025, 12, 0, NULL, NULL, 0, NULL),
(001050, 'Goat Roti', 'Tender chunks of curried goat with potatoes wrapped in Dhalpuri roti skin.', 000025, 9, 0, NULL, NULL, 0, NULL),
(001051, 'Jerk Pork (Spicy)', 'Pork rubbed with a spicy Jamaican jerk seasoning. ', 000024, 10.5, 0, NULL, NULL, 0, NULL),
(001052, 'Jerk Pork (Mild)', 'Pork rubbed with a spicy Jamaican jerk seasoning. ', 000024, 10.5, 0, NULL, NULL, 0, NULL),
(001053, 'Ackee and Sailfish - Jamaica''s N', 'Boiled and shredded salted cod seasoned with onions, tomatoes, black pepper and other island spices and then simmered livith Ackee, Jamaica''s National Fruit.', 000022, 13, 0, NULL, NULL, 0, NULL),
(001054, 'Brown Stew Snapper', 'A whole or filet snapper, crisply pan-fried, then simmered in its own gravy with onions, sweet peppers, and tomatoes.', 000022, 15.95, 0, NULL, NULL, 0, NULL),
(001055, 'Steamed Snapper', 'A steamed whole or filet snapper seasoned with onions, scallion, and garlic and served with carrots and okra.', 000022, 15.95, 0, NULL, NULL, 0, NULL),
(001056, 'Snapper in Run-Down', 'A whole or filet snapper! crisply pan-fried then simmered in a coconut (run-down) sauce.', 000022, 17.95, 0, NULL, NULL, 0, NULL),
(001057, 'Escovitched Fish (Spicy)', 'A whole or filet snapper, crisply pan-fried then topped with carrots., onion and green pepper saut?ed in a light tangy sauce,', 000022, 15.95, 0, NULL, NULL, 0, NULL),
(001058, 'Escovitched Fish (Mild)', 'A whole or filet snapper, crisply pan-fried then topped with carrots., onion and green pepper saut?ed in a light tangy sauce,', 000022, 15.95, 0, NULL, NULL, 0, NULL),
(001059, 'Curried Shrimp (Spicy)', 'Shrimp simmered in curly sauce with potatoes, (choose spicy or mild)', 000022, 18, 0, NULL, NULL, 0, NULL),
(001060, 'Curried Shrimp (Mild)', 'Shrimp simmered in curly sauce with potatoes, (choose spicy or mild)', 000022, 18, 0, NULL, NULL, 0, NULL),
(001061, 'Coconut Crusted Salmon', 'Seasoned pan seared salmon portioned rolled in coconut flakes then baked to perfection', 000022, 18, 0, NULL, NULL, 0, NULL),
(001062, 'Sata-massa-gana (Spicy)', 'Mixed vegetables steamed in coconut milk and curry sauce.', 000023, 9, 0, NULL, NULL, 0, NULL),
(001063, 'Stew Peas (Vegetarian)', 'Red Kidney beans stewed with herbs and spices and beef chunks.', 000023, 10.5, 0, NULL, NULL, 0, NULL),
(001064, 'Ital Stew', 'Soya chunks (choose chicken or beef flavor) cooked with potato and vegetables.', 000023, 9, 0, NULL, NULL, 0, NULL),
(001065, 'Roti', 'Curried potatoes wrapped in Dhalpuri roti skin.', 000023, 6.5, 0, NULL, NULL, 0, NULL),
(001066, 'Jamaican Fruit Cake', 'Jamaican Fruit Cake', 000011, 3.5, 0, NULL, NULL, 0, NULL),
(001067, 'Potato Pudding', 'Potato Pudding', 000011, 3, 0, NULL, NULL, 0, NULL),
(001068, 'Ice Cream', 'Ice Cream', 000011, 2.5, 0, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `TIMESHEET`
--

CREATE TABLE IF NOT EXISTS `TIMESHEET` (
  `USERID` int(10) unsigned NOT NULL,
  `PUNCH_IN` datetime NOT NULL,
  `PUNCH_OUT` datetime NOT NULL,
  PRIMARY KEY (`USERID`),
  KEY `USERID` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TIMESHEET`
--

INSERT INTO `TIMESHEET` (`USERID`, `PUNCH_IN`, `PUNCH_OUT`) VALUES
(100, '2016-03-29 06:00:00', '2016-03-29 06:00:01');

--
-- Triggers `TIMESHEET`
--
DROP TRIGGER IF EXISTS `before_timesheet_insert`;
DELIMITER //
CREATE TRIGGER `before_timesheet_insert` BEFORE INSERT ON `TIMESHEET`
 FOR EACH ROW BEGIN
	CALL validate_timesheet(NEW.punch_in, NEW.punch_out);
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `validate_timesheet_update`;
DELIMITER //
CREATE TRIGGER `validate_timesheet_update` BEFORE UPDATE ON `TIMESHEET`
 FOR EACH ROW BEGIN
	CALL validate_timesheet(NEW.punch_in, NEW.punch_out);
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE IF NOT EXISTS `USERS` (
  `USERID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Auto-increment integer',
  `EMAILADD` varchar(255) NOT NULL COMMENT 'e-mail address; unique index',
  `PWHASH` varchar(255) DEFAULT NULL COMMENT 'md5 hash of password',
  `FIRSTNAME` varchar(255) NOT NULL COMMENT 'First Name',
  `MINIT` char(1) NOT NULL COMMENT 'Middle Initial',
  `LASTNAME` varchar(255) NOT NULL COMMENT 'Last Name',
  `PHONE` bigint(10) unsigned NOT NULL COMMENT '10 Digit Telephone Number',
  `ADMINFLAG` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Y if user is an administrator',
  `OWNERFLAG` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Y if user is an owner of the business',
  `CUSTFLAG` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Y if user is a customer',
  PRIMARY KEY (`USERID`),
  UNIQUE KEY `EMAILADD` (`EMAILADD`),
  KEY `PWHASH` (`PWHASH`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Admins & owners; Parent record for customers' AUTO_INCREMENT=157 ;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`USERID`, `EMAILADD`, `PWHASH`, `FIRSTNAME`, `MINIT`, `LASTNAME`, `PHONE`, `ADMINFLAG`, `OWNERFLAG`, `CUSTFLAG`) VALUES
(0000000100, 'ksmiller99@yahoo.com', 'c57d4dbfea564b7c33cfae2b41864503', 'Kevin', 'S', 'Miller', 9734297945, 'Y', 'Y', 'Y'),
(0000000146, 'test1@test.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Test1', '', 'User', 1234567890, 'N', 'N', 'Y'),
(0000000147, 'test2@test.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'test2', '', 'User', 1234567890, 'N', 'N', 'Y'),
(0000000152, 'testa@test.com', 'c57d4dbfea564b7c33cfae2b41864503', 'TestA', '', 'User', 1234567890, 'N', 'N', 'Y'),
(0000000153, 'spinz001@gmail.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Matt', '', 'Haight', 2012133793, 'Y', 'Y', 'Y'),
(0000000154, 'testcust@test.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Testcust', '', 'Customer', 9731234567, 'N', 'N', 'Y'),
(0000000155, 'testadmin@test.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'TestAdmin', '', 'Admin', 1234567890, 'Y', 'N', 'Y'),
(0000000156, 'testowner@test.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'TestOwner', '', 'Owner', 7539518520, 'Y', 'Y', 'Y');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CUSTOMERS`
--
ALTER TABLE `CUSTOMERS`
  ADD CONSTRAINT `CUSTOMERS_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USERS` (`USERID`);

--
-- Constraints for table `EMPLOYEES`
--
ALTER TABLE `EMPLOYEES`
  ADD CONSTRAINT `EMPLOYEES_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `USERS` (`USERID`);

--
-- Constraints for table `ORDERITEMS`
--
ALTER TABLE `ORDERITEMS`
  ADD CONSTRAINT `ORDERITEMS_ibfk_1` FOREIGN KEY (`ORDID`) REFERENCES `ORDERS` (`ORDID`);

--
-- Constraints for table `ORDERS`
--
ALTER TABLE `ORDERS`
  ADD CONSTRAINT `ORDERS_ibfk_1` FOREIGN KEY (`CUSTID`) REFERENCES `CUSTOMERS` (`USERID`);

--
-- Constraints for table `PRODUCTS`
--
ALTER TABLE `PRODUCTS`
  ADD CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`CATID`) REFERENCES `CATEGORIES` (`CATID`);

--
-- Constraints for table `TIMESHEET`
--
ALTER TABLE `TIMESHEET`
  ADD CONSTRAINT `FK_TIMESHEET_EMPLOYEE` FOREIGN KEY (`USERID`) REFERENCES `EMPLOYEES` (`USERID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
