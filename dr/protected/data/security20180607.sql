-- MySQL dump 10.13  Distrib 5.6.26, for Linux (i686)
--
-- Host: localhost    Database: securitydev
-- ------------------------------------------------------
-- Server version	5.6.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sec_city`
--

DROP TABLE IF EXISTS `sec_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_city` (
  `code` char(5) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `region` char(5) DEFAULT NULL,
  `incharge` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_city_info`
--

DROP TABLE IF EXISTS `sec_city_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_city_info` (
  `code` char(5) NOT NULL,
  `field_id` varchar(30) NOT NULL,
  `field_value` varchar(2000) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`),
  UNIQUE KEY `idx_sec_city_info_1` (`code`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_login_log`
--

DROP TABLE IF EXISTS `sec_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_login_log` (
  `station_id` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `client_ip` varchar(20) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_station`
--

DROP TABLE IF EXISTS `sec_station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_station` (
  `station_id` varchar(30) NOT NULL,
  `station_name` varchar(30) NOT NULL,
  `city` char(5) NOT NULL,
  `status` char(1) DEFAULT 'Y',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_station_request`
--

DROP TABLE IF EXISTS `sec_station_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_station_request` (
  `req_key` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `station_name` varchar(30) NOT NULL,
  `city` char(5) NOT NULL,
  `station_id` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`req_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_template`
--

DROP TABLE IF EXISTS `sec_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_template` (
  `temp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system_id` varchar(15) NOT NULL,
  `temp_name` varchar(255) NOT NULL,
  `a_read_only` varchar(255) DEFAULT '',
  `a_read_write` varchar(255) DEFAULT '',
  `a_control` varchar(255) DEFAULT '',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`temp_id`),
  KEY `idx_sec_template_01` (`system_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_user`
--

DROP TABLE IF EXISTS `sec_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_user` (
  `username` varchar(30) NOT NULL,
  `password` varchar(128) DEFAULT NULL,
  `disp_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logon_time` datetime DEFAULT NULL,
  `logoff_time` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `fail_count` tinyint(3) unsigned DEFAULT '0',
  `locked` char(1) DEFAULT 'N',
  `session_key` varchar(500) DEFAULT NULL,
  `city` char(5) NOT NULL DEFAULT '',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_user_access`
--

DROP TABLE IF EXISTS `sec_user_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_user_access` (
  `username` varchar(30) NOT NULL,
  `system_id` varchar(15) NOT NULL,
  `a_read_only` varchar(255) DEFAULT '',
  `a_read_write` varchar(255) DEFAULT '',
  `a_control` varchar(255) DEFAULT '',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_sec_user_access_01` (`username`,`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_user_info`
--

DROP TABLE IF EXISTS `sec_user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_user_info` (
  `username` varchar(30) NOT NULL,
  `field_id` varchar(30) NOT NULL,
  `field_value` varchar(2000) DEFAULT NULL,
  `field_blob` longblob,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_info` (`username`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sec_user_option`
--

DROP TABLE IF EXISTS `sec_user_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_user_option` (
  `username` varchar(30) NOT NULL,
  `option_key` varchar(30) NOT NULL,
  `option_value` varchar(255) DEFAULT NULL,
  UNIQUE KEY `idx_sec_user_option_1` (`username`,`option_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_wservice`
--

DROP TABLE IF EXISTS `swo_wservice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_wservice` (
  `wsvc_key` varchar(50) NOT NULL,
  `wsvc_desc` varchar(100) NOT NULL,
  `city` char(5) NOT NULL,
  `session_key` varchar(50) DEFAULT NULL,
  `session_time` datetime DEFAULT NULL,
  PRIMARY KEY (`wsvc_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'securitydev'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-07 11:30:55
