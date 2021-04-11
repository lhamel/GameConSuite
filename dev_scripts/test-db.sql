-- MariaDB dump 10.17  Distrib 10.4.10-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ucon_scrub
-- ------------------------------------------------------
-- Server version	10.4.10-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ucon_auth_member`
--

DROP TABLE IF EXISTS `ucon_auth_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_auth_member` (
  `uid` int(11) NOT NULL,
  `id_member` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Temporary table structure for view `ucon_available`
--

DROP TABLE IF EXISTS `ucon_available`;
/*!50001 DROP VIEW IF EXISTS `ucon_available`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ucon_available` (
  `barcode` tinyint NOT NULL,
  `remaining` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ucon_convention`
--

DROP TABLE IF EXISTS `ucon_convention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_convention` (
  `id_convention` int(11) NOT NULL DEFAULT 0,
  `i_month` int(4) NOT NULL DEFAULT 11,
  `i_day` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_convention`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucon_event`
--

DROP TABLE IF EXISTS `ucon_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_event` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `id_convention` int(11) NOT NULL DEFAULT 0,
  `id_gm` int(11) NOT NULL DEFAULT 0,
  `s_number` varchar(50) NOT NULL DEFAULT '',
  `s_title` varchar(100) NOT NULL DEFAULT '',
  `id_event_type` int(8) NOT NULL,
  `s_game` varchar(100) NOT NULL DEFAULT '',
  `s_desc` text DEFAULT NULL,
  `s_desc_web` text NOT NULL,
  `i_minplayers` smallint(6) NOT NULL DEFAULT 0,
  `i_maxplayers` smallint(6) NOT NULL DEFAULT 0,
  `i_agerestriction` smallint(6) NOT NULL DEFAULT 0,
  `e_exper` enum('1','2','3','4','5') DEFAULT NULL,
  `e_complex` enum('A','B','C','D','E') DEFAULT NULL,
  `s_comments` text DEFAULT NULL,
  `s_setup` varchar(255) DEFAULT NULL,
  `s_table_type` varchar(50) DEFAULT NULL,
  `i_length` int(11) NOT NULL DEFAULT 0,
  `s_tourntype` varchar(255) DEFAULT NULL,
  `s_eventcom` text DEFAULT NULL,
  `b_approval` int(1) NOT NULL DEFAULT 0,
  `b_edited` int(1) NOT NULL DEFAULT 0,
  `e_day` enum('','FRI','SAT','SUN') DEFAULT NULL,
  `i_time` int(8) DEFAULT NULL,
  `id_room` smallint(6) DEFAULT NULL,
  `s_table` varchar(100) DEFAULT NULL,
  `i_c1` int(8) NOT NULL DEFAULT 0,
  `i_c2` int(8) NOT NULL DEFAULT 0,
  `i_c3` int(8) NOT NULL DEFAULT 0,
  `i_actual` smallint(6) DEFAULT NULL,
  `i_prereg` smallint(6) NOT NULL DEFAULT 0,
  `b_showed_up` tinyint(1) NOT NULL DEFAULT 1,
  `i_cost` float(7,2) DEFAULT 4.00,
  `d_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `d_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `b_full` smallint(1) NOT NULL DEFAULT 0,
  `b_prize` mediumint(9) DEFAULT NULL,
  `i_profit` float(7,2) DEFAULT 0.00,
  `i_slot` tinyint(6) DEFAULT 0,
  `i_remaining_tickets` int(8) DEFAULT NULL,
  `s_note` text DEFAULT NULL,
  `i_real_tickets` int(8) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_event`),
  KEY `schedule_by_time` (`id_convention`,`e_day`,`i_time`),
  KEY `schedule_by_type_day` (`id_convention`,`id_event_type`,`e_day`),
  KEY `gm` (`id_gm`,`id_convention`) USING BTREE,
  KEY `schedule_by_location` (`id_convention`,`id_room`),
  FULLTEXT KEY `search_index` (`s_title`,`s_game`,`s_desc`),
  FULLTEXT KEY `s_number` (`s_number`,`s_title`,`s_game`,`s_desc`,`s_desc_web`)
) ENGINE=MyISAM AUTO_INCREMENT=10126 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ucon_event_tag`
--

DROP TABLE IF EXISTS `ucon_event_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_event_tag` (
  `id_event` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  PRIMARY KEY (`id_event`,`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucon_event_type`
--

DROP TABLE IF EXISTS `ucon_event_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_event_type` (
  `id_event_type` int(11) NOT NULL AUTO_INCREMENT,
  `s_abbr` varchar(10) NOT NULL DEFAULT '',
  `s_type` varchar(100) DEFAULT NULL,
  `i_order` smallint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_event_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_event_type`
--

LOCK TABLES `ucon_event_type` WRITE;
/*!40000 ALTER TABLE `ucon_event_type` DISABLE KEYS */;
INSERT INTO `ucon_event_type` VALUES (1,'BG','Board and Card Games',2),(2,'MN','Miniatures',4),(3,'CG','Collectable Card Games',3),(4,'RP','Role Playing',5),(5,'OP','Organized Play',6),(6,'EV','Special Events',1),(7,'VG','Video Games',7);
/*!40000 ALTER TABLE `ucon_event_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `ucon_inventory`
--

DROP TABLE IF EXISTS `ucon_inventory`;
/*!50001 DROP VIEW IF EXISTS `ucon_inventory`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ucon_inventory` (
  `barcode` tinyint NOT NULL,
  `remaining` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ucon_item`
--

DROP TABLE IF EXISTS `ucon_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_item` (
  `barcode` varchar(12) NOT NULL DEFAULT '',
  `year` int(11) DEFAULT NULL,
  `itemtype` varchar(20) DEFAULT NULL,
  `subtype` varchar(20) DEFAULT NULL,
  `special` varchar(100) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `price` float DEFAULT NULL,
  `quantity` int(8) DEFAULT NULL,
  `i_commonsheet` smallint(2) DEFAULT NULL,
  PRIMARY KEY (`barcode`),
  UNIQUE KEY `ucon_item_barcode` (`barcode`) USING BTREE,
  KEY `item_subtype` (`itemtype`,`subtype`) USING BTREE,
  KEY `item_year_type` (`year`,`itemtype`,`subtype`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ucon_member`
--

DROP TABLE IF EXISTS `ucon_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_member` (
  `id_member` int(11) NOT NULL AUTO_INCREMENT,
  `s_lname` varchar(100) DEFAULT NULL,
  `s_fname` varchar(100) DEFAULT NULL,
  `s_addr1` varchar(100) DEFAULT NULL,
  `s_addr2` varchar(100) DEFAULT NULL,
  `s_city` varchar(100) DEFAULT NULL,
  `s_state` varchar(100) DEFAULT NULL,
  `s_zip` varchar(100) DEFAULT NULL,
  `s_phone` varchar(100) DEFAULT NULL,
  `s_email` varchar(100) DEFAULT NULL,
  `b_volunteer` int(1) NOT NULL DEFAULT 0,
  `d_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `d_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `b_email` int(1) NOT NULL DEFAULT 1,
  `s_international` text DEFAULT NULL,
  PRIMARY KEY (`id_member`),
  FULLTEXT KEY `s_lname` (`s_lname`,`s_fname`)
) ENGINE=MyISAM AUTO_INCREMENT=8289 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ucon_order`
--

DROP TABLE IF EXISTS `ucon_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_order` (
  `id_order` int(11) NOT NULL AUTO_INCREMENT,
  `id_convention` int(11) NOT NULL DEFAULT 0,
  `id_member` int(11) NOT NULL DEFAULT 0,
  `s_type` varchar(100) NOT NULL DEFAULT '',
  `s_subtype` varchar(100) NOT NULL DEFAULT '',
  `i_quantity` int(11) NOT NULL DEFAULT 0,
  `s_special` varchar(100) NOT NULL DEFAULT '',
  `i_price` float(7,2) NOT NULL DEFAULT 0.00,
  `d_transaction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `d_cancelled` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_order`),
  KEY `order_type` (`id_convention`,`s_type`,`s_subtype`) USING BTREE,
  KEY `id_convention` (`id_convention`,`id_member`,`s_type`)
) ENGINE=MyISAM AUTO_INCREMENT=34654 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucon_prereg_items`
--

DROP TABLE IF EXISTS `ucon_prereg_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_prereg_items` (
  `id_prereg_item` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `itemtype` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtype` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unit_price` float DEFAULT NULL,
  `display_order` smallint(2) DEFAULT NULL,
  `is_public` smallint(1) DEFAULT 0,
  PRIMARY KEY (`id_prereg_item`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_prereg_items`
--

LOCK TABLES `ucon_prereg_items` WRITE;
/*!40000 ALTER TABLE `ucon_prereg_items` DISABLE KEYS */;
INSERT INTO `ucon_prereg_items` VALUES (1,'010018','Badge','Weekend Student/Mili','Badge - Weekend Student/Military',25,16,0),(2,'040003','Misc','Generic Ribbon','Generics Ribbon \"Play Games All Weekend\"',20,23,1),(3,'040001','Misc','Dice','U-Con Dice',1,21,0),(4,'040000','Misc','Generic Ticket','Generic Ticket',2,22,1),(5,'010017','Badge','Child Weekend','Badge - Child Weekend',10,4,1),(6,'010015','Badge','Visitor','Badge - Visitor',0,19,0),(7,'010016','Badge','Guest Liaison','Badge - Guest Liaison',0,18,0),(8,'010013','Badge','Special Guest','Badge - Special Guest',0,8,0),(9,'010014','Badge','Vendor','Badge - Vendor',0,6,0),(10,'010019','Badge','Guest of Honor','Badge - Guest of Honor',0,9,0),(11,'010011','Badge','Press','Badge - Press',0,17,0),(12,'010010','Badge','Gamemaster Refund','Badge - Gamemaster Refund',-10,15,0),(13,'010009','Badge','Gamemaster (comped)','Badge - Gamemaster - comped',0,2,0),(14,'010008','Badge','Sunday','Badge - Sunday',15,13,1),(15,'010007','Badge','Saturday','Badge - Saturday',25,12,1),(16,'010006','Badge','Friday','Badge - Friday',15,11,1),(17,'010005','Badge','Weekend','Badge - Weekend',35,3,1),(18,'010004','Badge','Gamemaster','Badge - Gamemaster - deposit',10,1,0),(19,'010003','Badge','Volunteer','Badge - Volunteer',0,7,0),(20,'010002','Badge','Dealer','Badge - Dealer',0,20,0),(21,'010001','Badge','Staff','Badge - Staff',0,5,0),(22,'030006','Shirt','4XL','Unisex - 4XL',24,31,1),(23,'030005','Shirt','3XL','Unisex - 3XL',24,30,1),(24,'030004','Shirt','2XL','Unisex Shirt - 2XL',24,29,1),(25,'030003','Shirt','XL','Unisex Shirt - XL',22,28,1),(26,'030002','Shirt','L','Unisex Shirt - L',22,27,1),(27,'030001','Shirt','M','Unisex Shirt - M',22,26,1),(28,'040004','Misc','Legacy Tee Shirt - 2','Legacy Tee Shirt - 2014 and earlier 18/20',18,24,0),(29,'040005','Misc','Logo Bag','U-Con Logo Bag',12,38,0),(30,'040006','Misc','Donation-5','Donation ($5)',5,39,0),(31,'040007','Misc','Donation-1','Donation ($1)',1,40,0),(32,'050001','Exhibit','Table','Vendor standard space (6x6)',115,41,0),(33,'050002','Exhibit','SmallTable','Vendor small space (half table)',50,42,0),(34,'050003','Exhibit','PrizeSupportCredit','Vendor credit',-1,43,0),(35,'050100','Exhibit','Full Page Ad','Full Page Ad',90,44,0),(36,'050101','Exhibit','Half Page Ad','Half Page Ad',60,45,0),(37,'050102','Exhibit','Quarter Page Ad','Quarter Page Ad',35,46,0),(38,'000000','HasBadge','HasBadge','Has Badge from Group or Vendor',0,47,0),(39,'000001','Permission','Pay Onsite','Permission to pay onsite',0,48,0),(40,'010021','Badge','Volunteer*','Sign up as volunteer',25,14,1),(41,'010022','Badge','Volunteer* Refund','Refund for prepaid volunteers',-25,0,0),(42,'030007','Shirt','S','Unisex Shirt - S',22,25,1),(43,'030008','Shirt','WS','Ladies\' Shirt - S',22,32,0),(44,'030009','Shirt','WM','Ladies\' Shirt - M',22,33,1),(45,'030010','Shirt','WL','Ladies\' Shirt - L',22,34,1),(46,'030011','Shirt','WXL','Ladies\' Shirt - XL',22,35,1),(47,'030012','Shirt','W2XL','Ladies\' Shirt - 2XL',24,36,1),(48,'030013','Shirt','W3XL','Ladies\' Shirt - 3XL',24,37,1),(49,'010020','Badge','Industry Insider','Badge - Industry Insider',0,10,0);
/*!40000 ALTER TABLE `ucon_prereg_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ucon_reserved`
--

DROP TABLE IF EXISTS `ucon_reserved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_reserved` (
  `id_reserved` int(11) NOT NULL AUTO_INCREMENT,
  `id_register` varchar(255) NOT NULL,
  `barcode` varchar(12) NOT NULL,
  `quantity` int(8) NOT NULL,
  `t_when` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `b_manual_clear` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_reserved`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ucon_room`
--

DROP TABLE IF EXISTS `ucon_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_room` (
  `id_room` int(11) NOT NULL AUTO_INCREMENT,
  `s_room` varchar(100) DEFAULT NULL,
  `id_venue` int(4) DEFAULT NULL,
  PRIMARY KEY (`id_room`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_room`
--

LOCK TABLES `ucon_room` WRITE;
/*!40000 ALTER TABLE `ucon_room` DISABLE KEYS */;
INSERT INTO `ucon_room` VALUES (1,'2105A',1),(2,'2105B',1),(3,'2105C',1),(4,'2105D',1),(5,'Anderson',1),(6,'2nd Floor Ballroom',1),(7,'Bates',1),(8,'Blain',1),(9,'Crofoot',1),(10,'Kuenzel',1),(11,'Michigan',1),(12,'Parker',1),(13,'Pendleton',1),(14,'Pond',1),(15,'SB Jones',1),(17,'Tappan',1),(18,'U-Club',1),(19,'Welker',1),(20,'Wolverine',1),(21,'Pond BC',1),(22,'Pond A',1),(24,'Michigan',2),(25,'Huron',2),(26,'Ontario',2),(27,'Theater',2),(28,'Superior',2),(29,'North Foyer',2),(30,'West Foyer',2),(31,'Salon 1',2),(32,'Salon 2',2),(33,'Salon 3',2),(34,'Salon 4',2),(35,'St. Clair',2),(36,'Meeting Suite 2 (2nd floor)',2),(37,'Meeting Suite 3 (3rd floor)',2),(38,'Meeting Suite 4 (4th floor)',2),(67,'Clubhouse',3),(42,'Ballroom',3),(66,'Seminar',3),(65,'Hotel Restaurant',3),(45,'Conference A',3),(46,'Conference B',3),(47,'Conference C',3),(48,'Conference D',3),(49,'Conference E',3),(50,'Conference F',3),(51,'Conference G',3),(52,'Conference H',3),(53,'Board Room',3),(54,'Seminar 1',3),(55,'Seminar 5',3),(56,'Atrium',3),(57,'View Bar',3),(58,'Exhibitor Hall',3),(59,'Seminar 2',3),(60,'Seminar 3',3),(61,'Seminar 4',3),(62,'Elizabeth Ann',3),(63,'Auditorium 1',3),(64,'Auditorium 2',3);
/*!40000 ALTER TABLE `ucon_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ucon_state`
--

DROP TABLE IF EXISTS `ucon_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_state` (
  `s_ab` char(2) NOT NULL DEFAULT '',
  `s_state` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_state`
--

LOCK TABLES `ucon_state` WRITE;
/*!40000 ALTER TABLE `ucon_state` DISABLE KEYS */;
INSERT INTO `ucon_state` VALUES ('AL','ALABAMA'),('AK','ALASKA'),('AS','AMERICAN SAMOA'),('AZ','ARIZONA'),('AR','ARKANSAS'),('CA','CALIFORNIA'),('CO','COLORADO'),('CT','CONNECTICUT'),('DE','DELAWARE'),('DC','DISTRICT OF COLUMBIA'),('FM','FEDERATED STATES OF MICRONESIA'),('FL','FLORIDA'),('GA','GEORGIA'),('GU','GUAM'),('HI','HAWAII'),('ID','IDAHO'),('IL','ILLINOIS'),('IN','INDIANA'),('IA','IOWA'),('KS','KANSAS'),('KY','KENTUCKY'),('LA','LOUISIANA'),('ME','MAINE'),('MH','MARSHALL ISLANDS'),('MD','MARYLAND'),('MA','MASSACHUSETTS'),('MI','MICHIGAN'),('MN','MINNESOTA'),('MS','MISSISSIPPI'),('MO','MISSOURI'),('MT','MONTANA'),('NE','NEBRASKA'),('NV','NEVADA'),('NH','NEW HAMPSHIRE'),('NJ','NEW JERSEY'),('NM','NEW MEXICO'),('NY','NEW YORK'),('NC','NORTH CAROLINA'),('ND','NORTH DAKOTA'),('MP','NORTHERN MARIANA ISLANDS'),('OH','OHIO'),('OK','OKLAHOMA'),('OR','OREGON'),('PW','PALAU'),('PA','PENNSYLVANIA'),('PR','PUERTO RICO'),('RI','RHODE ISLAND'),('SC','SOUTH CAROLINA'),('SD','SOUTH DAKOTA'),('TN','TENNESSEE'),('TX','TEXAS'),('UT','UTAH'),('VT','VERMONT'),('VI','VIRGIN ISLANDS'),('VA','VIRGINIA'),('WA','WASHINGTON'),('WV','WEST VIRGINIA'),('WI','WISCONSIN'),('WY','WYOMING');
/*!40000 ALTER TABLE `ucon_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ucon_tag`
--

DROP TABLE IF EXISTS `ucon_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_tag` (
  `id_tag` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_tag`
--


--
-- Table structure for table `ucon_transaction`
--

DROP TABLE IF EXISTS `ucon_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_transaction` (
  `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `prereg` int(1) NOT NULL DEFAULT 0,
  `id_register` varchar(255) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  `opened` datetime DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  PRIMARY KEY (`id_transaction`)
) ENGINE=MyISAM AUTO_INCREMENT=2147096392 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ucon_transaction_item`
--

DROP TABLE IF EXISTS `ucon_transaction_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_transaction_item` (
  `id_transaction` int(11) NOT NULL DEFAULT 0,
  `barcode` varchar(12) NOT NULL DEFAULT '',
  `special` text DEFAULT NULL,
  `quantity` int(8) NOT NULL DEFAULT 0,
  `price` float NOT NULL DEFAULT 0,
  KEY `ucon_transaction_item_barcode` (`barcode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucon_venue`
--

DROP TABLE IF EXISTS `ucon_venue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucon_venue` (
  `id_venue` int(4) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_venue`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ucon_venue`
--

LOCK TABLES `ucon_venue` WRITE;
/*!40000 ALTER TABLE `ucon_venue` DISABLE KEYS */;
INSERT INTO `ucon_venue` VALUES (1,'Michigan Union'),(2,'Metropolitan Hotel'),(3,'Eagle Crest Marriot');
/*!40000 ALTER TABLE `ucon_venue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ucon_scrub'
--
/*!50003 DROP FUNCTION IF EXISTS `RandNum` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` FUNCTION `RandNum`(length SMALLINT(3)) RETURNS varchar(100) CHARSET utf8
begin
    SET @returnStr = '';
    SET @allowedChars = '0123456789';
    SET @i = 0;
    WHILE (@i < length) DO
        SET @returnStr = CONCAT(@returnStr, substring(@allowedChars, FLOOR(RAND() * LENGTH(@allowedChars) + 1), 1));
        SET @i = @i + 1;
    END WHILE;
    RETURN @returnStr;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `RandString` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` FUNCTION `RandString`(length SMALLINT(3)) RETURNS varchar(100) CHARSET utf8
begin
    SET @returnStr = '';
    SET @allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    SET @i = 0;
    WHILE (@i < length) DO
        SET @returnStr = CONCAT(@returnStr, substring(@allowedChars, FLOOR(RAND() * LENGTH(@allowedChars) + 1), 1));
        SET @i = @i + 1;
    END WHILE;
    RETURN @returnStr;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `forceReleaseTicket` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` PROCEDURE `forceReleaseTicket`(IN `myRegister` VARCHAR(255), IN `ticketBarcode` VARCHAR(12), OUT `success` INT)
    SQL SECURITY INVOKER
BEGIN
  update ucon_reserved set b_manual_clear=1 
    where id_register=myRegister and barcode=ticketBarcode;
  
  SET success=1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `moveReserveToTransaction` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` PROCEDURE `moveReserveToTransaction`(IN `transactionId` INT, IN `myRegister` VARCHAR(255), OUT `success` INT)
    SQL SECURITY INVOKER
BEGIN

  
  insert into ucon_transaction_item 
  select transactionId as id_transaction, RES.barcode, IT.special, RES.quantity, IT.price 
  from ucon_reserved as RES, ucon_item as IT
  where RES.barcode=IT.barcode
    and RES.id_register = myRegister
    and b_manual_clear = 0;

  
  delete from ucon_reserved
  where id_register = myRegister and b_manual_clear = 0;

  SET success=1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `releaseTicket` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` PROCEDURE `releaseTicket`(IN `myRegister` VARCHAR(255), IN `ticketBarcode` VARCHAR(12))
    SQL SECURITY INVOKER
BEGIN
  
  delete from ucon_reserved 
  where id_register = myRegister 
    and barcode = ticketBarcode 
    and b_manual_clear=0;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `setReserveTicket` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`ucon`@`localhost` PROCEDURE `setReserveTicket`(IN `myRegister` VARCHAR(255), IN `ticketBarcode` VARCHAR(12), IN `ticketQuantity` INT, OUT `success` INT)
    SQL SECURITY INVOKER
BEGIN
  START TRANSACTION;

  SET success=0;

  
  delete from ucon_reserved 
  where id_register=myRegister 
    and barcode = ticketBarcode 
    and b_manual_clear=0;

  
  select count(id_reserved) into @reservedCount 
  from ucon_reserved 
  where barcode = ticketBarcode;

  
  select remaining into @available
  from ucon_available where barcode=ticketBarcode;

  IF @available >= ticketQuantity THEN
    
    insert into ucon_reserved
      set barcode=ticketBarcode,
        quantity=ticketQuantity,
        id_register=myRegister;
    SET success=1;
    COMMIT;
  ELSEIF @reservedCount <= ticketQuantity THEN
    
    SET success=2;
    ROLLBACK;
  ELSE
    ROLLBACK;
  END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `ucon_available`
--

/*!50001 DROP TABLE IF EXISTS `ucon_available`*/;
/*!50001 DROP VIEW IF EXISTS `ucon_available`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ucon`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ucon_available` AS select `inv`.`barcode` AS `barcode`,`inv`.`remaining` - if(`res`.`quantity` is null,0,sum(`res`.`quantity`)) AS `remaining` from (`ucon_inventory` `inv` left join `ucon_reserved` `res` on(convert(`inv`.`barcode` using utf8) = convert(`res`.`barcode` using utf8) and `res`.`b_manual_clear` = 0)) group by `inv`.`barcode`,`inv`.`remaining` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ucon_inventory`
--

/*!50001 DROP TABLE IF EXISTS `ucon_inventory`*/;
/*!50001 DROP VIEW IF EXISTS `ucon_inventory`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ucon`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ucon_inventory` AS select `i`.`barcode` AS `barcode`,`i`.`quantity` - if(`ti`.`quantity` is null,0,sum(`ti`.`quantity`)) AS `remaining` from (`ucon_item` `i` left join `ucon_transaction_item` `ti` on(`i`.`barcode` = `ti`.`barcode`)) where `i`.`quantity` is not null group by `i`.`barcode`,`i`.`quantity` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-05 21:45:09
