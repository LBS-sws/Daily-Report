-- MySQL dump 10.13  Distrib 5.6.26, for Linux (i686)
--
-- Host: localhost    Database: docmandev
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
-- Table structure for table `dm_doc_type`
--

DROP TABLE IF EXISTS `dm_doc_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dm_doc_type` (
  `doc_type_code` varchar(10) NOT NULL,
  `doc_type_desc` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`doc_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dm_doc_type`
--

LOCK TABLES `dm_doc_type` WRITE;
/*!40000 ALTER TABLE `dm_doc_type` DISABLE KEYS */;
INSERT INTO `dm_doc_type` VALUES ('SERVICE','Service Contract','2017-02-24 09:37:43','2017-02-24 09:37:43');
/*!40000 ALTER TABLE `dm_doc_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dm_file`
--

DROP TABLE IF EXISTS `dm_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dm_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mast_id` int(10) unsigned NOT NULL,
  `phy_file_name` varchar(300) NOT NULL,
  `phy_path_name` varchar(100) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `archive` char(1) DEFAULT 'N',
  `remove` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dm_file_01` (`mast_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dm_file`
--

LOCK TABLES `dm_file` WRITE;
/*!40000 ALTER TABLE `dm_file` DISABLE KEYS */;
INSERT INTO `dm_file` VALUES (1,1,'dc792e1e2b6aac5167bcea3b2c2d2206.jpg','/docman/dev/5/0','布達佩斯.jpg','image/jpeg','N','N','test','test','2017-04-08 09:19:43','2017-04-08 09:19:43'),(2,2,'a2b249f1dfe645bd6f4b9e43cf2e6785.jpg','/docman/dev/8/0','sailor_moon_s_banner.jpg','image/jpeg','N','N','test','test','2017-04-08 09:21:16','2017-04-08 09:21:16'),(3,3,'dc792e1e2b6aac5167bcea3b2c2d2206.jpg','/docman/dev/5/0','布達佩斯.jpg','image/jpeg','N','N','admin','admin','2017-04-08 09:21:24','2017-04-08 09:21:24'),(4,3,'a2b249f1dfe645bd6f4b9e43cf2e6785.jpg','/docman/dev/8/0','sailor_moon_s_banner.jpg','image/jpeg','N','N','admin','admin','2017-04-08 09:21:24','2017-04-08 09:21:24'),(6,4,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-04-08 10:26:21','2017-04-08 10:26:21'),(7,5,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-04-08 16:11:17','2017-04-08 16:11:17'),(8,6,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-04-08 16:13:46','2017-04-08 16:13:46'),(9,7,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-04-08 16:25:21','2017-04-08 16:25:21'),(10,8,'8cfa3668310f72979b3ddf9f3fd2971d.jpg','/docman/dev/0/0','starfish.jpg','image/jpeg','N','N','test','test','2017-04-08 16:26:17','2017-04-08 16:26:17'),(11,9,'dc792e1e2b6aac5167bcea3b2c2d2206.jpg','/docman/dev/5/0','布達佩斯.jpg','image/jpeg','N','N','test','test','2017-04-08 16:52:41','2017-04-08 16:52:41'),(12,10,'3361a1135a2098b43300ab0dd5a6d3f6.jpg','/docman/dev/0/0','panda.jpg','image/jpeg','N','N','test','test','2017-04-09 02:39:03','2017-04-09 02:39:03'),(13,11,'dc792e1e2b6aac5167bcea3b2c2d2206.jpg','/docman/dev/5/0','布達佩斯.jpg','image/jpeg','N','N','admin','admin','2017-04-09 02:40:45','2017-04-09 02:40:45'),(14,11,'a2b249f1dfe645bd6f4b9e43cf2e6785.jpg','/docman/dev/8/0','sailor_moon_s_banner.jpg','image/jpeg','N','N','admin','admin','2017-04-09 02:40:45','2017-04-09 02:40:45'),(15,11,'3361a1135a2098b43300ab0dd5a6d3f6.jpg','/docman/dev/0/0','panda.jpg','image/jpeg','N','N','admin','admin','2017-04-09 02:40:45','2017-04-09 02:40:45'),(16,12,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','test','test','2017-04-11 02:55:44','2017-04-11 02:55:44'),(17,13,'f22612f3f2cb9a50ef0e28af3d9941fb.jpg','/docman/dev/0/0','crab.jpg','image/jpeg','N','N','test','test','2017-04-28 21:33:38','2017-04-28 21:33:38'),(18,14,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','Y','test','test','2017-04-29 06:45:45','2017-04-30 20:49:47'),(19,15,'265d99e0eb7f77295331ddca2e4ef299.jpg','/docman/dev/4c/2','seashell1.jpg','image/jpeg','N','N','test','test','2017-04-29 08:46:27','2017-04-29 08:46:27'),(20,16,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-04-30 20:52:43','2017-04-30 20:52:43'),(21,14,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','Y','test','test','2017-05-02 02:18:46','2017-05-02 02:29:12'),(22,14,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','Y','test','test','2017-05-02 02:22:24','2017-05-02 02:58:04'),(23,17,'c4d68e1ce3e62be38bba5304776367dd.jpg','/docman/dev/8/0','lancable.jpg','image/jpeg','N','N','test','test','2017-05-02 02:22:55','2017-05-02 02:22:55'),(24,14,'07034df7ccdf1363d3f7378d58fd8481.jpg','/docman/dev/9/0','Digital_Comms_Networks_MSc.docx.jpg','image/jpeg','N','N','test','test','2017-05-02 02:57:41','2017-05-02 02:57:41'),(25,18,'c4d68e1ce3e62be38bba5304776367dd.jpg','/docman/dev/8/0','lancable.jpg','image/jpeg','N','N','test','test','2017-05-02 02:59:12','2017-05-02 02:59:12'),(26,18,'af213be8979417677b1519503d457441.jpg','/docman/dev/0/0','image.jpg','image/jpeg','N','N','test','test','2017-05-02 02:59:13','2017-05-02 02:59:13'),(27,19,'af213be8979417677b1519503d457441.jpg','/docman/dev/0/0','image (1).jpg','image/jpeg','N','N','test','test','2017-05-02 03:29:08','2017-05-02 03:29:08'),(28,19,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01 (2).jpg','image/jpeg','N','N','test','test','2017-05-02 03:29:08','2017-05-02 03:29:08'),(29,20,'07034df7ccdf1363d3f7378d58fd8481.jpg','/docman/dev/9/0','Digital_Comms_Networks_MSc.docx (1).jpg','image/jpeg','N','N','test','test','2017-05-02 04:37:27','2017-05-02 04:37:27'),(30,20,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','test','test','2017-05-02 04:37:27','2017-05-02 04:37:27'),(31,21,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01 (2).jpg','image/jpeg','N','N','test','test','2017-05-02 05:03:52','2017-05-02 05:03:52'),(32,21,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','N','test','test','2017-05-02 05:04:02','2017-05-02 05:04:02'),(33,22,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','N','test','test','2017-05-02 06:41:35','2017-05-02 06:41:35'),(34,22,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','Y','test','test','2017-05-02 06:41:35','2017-05-02 06:43:44'),(35,22,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','test','test','2017-05-02 06:43:33','2017-05-02 06:43:33'),(36,23,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','test','test','2017-05-02 06:43:55','2017-05-02 06:43:55'),(37,22,'07034df7ccdf1363d3f7378d58fd8481.jpg','/docman/dev/9/0','Digital_Comms_Networks_MSc.docx.jpg','image/jpeg','N','N','test','test','2017-05-02 06:45:19','2017-05-02 06:45:19'),(38,24,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (1).jpg','image/jpeg','N','N','test','test','2017-05-02 06:51:21','2017-05-02 06:51:21'),(39,24,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01 (1).jpg','image/jpeg','N','N','test','test','2017-05-02 06:51:21','2017-05-02 06:51:21'),(40,25,'07034df7ccdf1363d3f7378d58fd8481.jpg','/docman/dev/9/0','Digital_Comms_Networks_MSc.docx.jpg','image/jpeg','N','N','test','test','2017-05-02 06:51:35','2017-05-02 06:51:35'),(41,24,'af213be8979417677b1519503d457441.jpg','/docman/dev/0/0','image.jpg','image/jpeg','N','N','test','test','2017-05-02 07:12:31','2017-05-02 07:12:31'),(42,26,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','Y','test','test','2017-05-02 09:14:11','2017-05-02 09:56:43'),(43,26,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01 (1).jpg','image/jpeg','N','N','test','test','2017-05-02 09:54:06','2017-05-02 09:54:06'),(44,27,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (3).jpg','image/jpeg','N','N','test','test','2017-05-02 09:55:30','2017-05-02 09:55:30'),(45,28,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (3).jpg','image/jpeg','N','N','admin','admin','2017-05-02 10:20:39','2017-05-02 10:20:39'),(46,29,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','test','test','2017-05-04 09:35:33','2017-05-04 09:35:33'),(47,30,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','test','test','2017-05-04 09:35:43','2017-05-04 09:35:43'),(48,31,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','N','test','test','2017-05-04 10:50:53','2017-05-04 10:50:53'),(49,32,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(50,32,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(51,32,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(52,33,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(53,33,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(54,33,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01.jpg','image/jpeg','N','N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(55,34,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','admin','admin','2017-05-11 02:16:34','2017-05-11 02:16:34'),(56,35,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','test','test','2017-05-13 15:49:05','2017-05-13 15:49:05'),(57,35,'4a1ae0d61387f2aa01e5ddc6184002ea.jpg','/docman/dev/5/0','tp-link.jpg','image/jpeg','N','N','test','test','2017-05-13 15:49:05','2017-05-13 15:49:05'),(58,36,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','Y','test','test','2017-05-13 17:48:47','2017-05-13 17:48:53'),(59,36,'27ff877d36fda97eaf9b62e0317adc0c.png','/docman/dev/5/0','未命名.png','image/png','N','N','test','test','2017-05-13 17:49:00','2017-05-13 17:49:00'),(60,37,'7e89f306ad2942c279f65693c0e839ae.jpg','/docman/dev/0/0','醬料4.jpg','image/jpeg','N','N','test','test','2017-05-14 11:43:16','2017-05-14 11:43:16'),(61,38,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','test','test','2017-05-14 14:22:17','2017-05-14 14:22:17'),(62,38,'27ff877d36fda97eaf9b62e0317adc0c.png','/docman/dev/5/0','未命名.png','image/png','N','N','test','test','2017-05-14 14:22:17','2017-05-14 14:22:17'),(63,39,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','test','test','2017-05-14 15:36:43','2017-05-14 15:36:43'),(64,40,'27ff877d36fda97eaf9b62e0317adc0c.png','/docman/dev/5/0','未命名.png','image/png','N','N','test','test','2017-05-14 16:03:05','2017-05-14 16:03:05'),(65,41,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (1).jpg','image/jpeg','N','N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(66,41,'41636094a7558607b043ff78c9fe6227.jpg','/docman/dev/3a/23','dataCenter01 (1).jpg','image/jpeg','N','N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(67,41,'af213be8979417677b1519503d457441.jpg','/docman/dev/0/0','image.jpg','image/jpeg','N','N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(68,41,'07034df7ccdf1363d3f7378d58fd8481.jpg','/docman/dev/9/0','Digital_Comms_Networks_MSc.docx.jpg','image/jpeg','N','N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(69,41,'27ff877d36fda97eaf9b62e0317adc0c.png','/docman/dev/5/0','未命名.png','image/png','N','N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(72,42,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (1).jpg','image/jpeg','N','N','test','test','2017-05-17 04:26:27','2017-05-17 04:26:27'),(73,43,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company (1).jpg','image/jpeg','N','N','admin','admin','2017-05-17 04:26:32','2017-05-17 04:26:32'),(74,44,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','test','test','2017-05-17 04:29:13','2017-05-17 04:29:13'),(75,45,'1572176a07cbe75d84e7a4d27bdcc885.jpg','/docman/dev/ff/ff','alphabet_collage.jpg','image/jpeg','N','N','admin','admin','2017-05-17 04:30:47','2017-05-17 04:30:47'),(76,46,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','test','test','2017-05-18 18:56:06','2017-05-18 18:56:06'),(77,47,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','test','test','2017-05-18 18:57:34','2017-05-18 18:57:34'),(78,48,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','test','test','2017-05-19 01:41:19','2017-05-19 01:41:19'),(79,49,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(80,49,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(81,49,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(82,50,'59a7329a427382109109faef968f9668.jpg','/docman/dev/1/0','2033060.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(83,50,'9f802f121c83c63b3f702549e7c32691.jpg','/docman/dev/0/0','np360.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(84,50,'1b2357b92945d2a111d30f45ec357482.jpg','/docman/dev/f/0','company.jpg','image/jpeg','N','N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25');
/*!40000 ALTER TABLE `dm_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dm_master`
--

DROP TABLE IF EXISTS `dm_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dm_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_type_code` varchar(10) NOT NULL,
  `doc_id` int(10) unsigned NOT NULL,
  `remove` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dm_master`
--

LOCK TABLES `dm_master` WRITE;
/*!40000 ALTER TABLE `dm_master` DISABLE KEYS */;
INSERT INTO `dm_master` VALUES (1,'PAYREQ',1,'N','test',NULL,'2017-04-08 09:19:42','2017-04-08 09:19:50'),(2,'PAYREAL',1,'N','test',NULL,'2017-04-08 09:21:16','2017-04-08 09:21:16'),(3,'TRANS',6,'N','admin','admin','2017-04-08 09:21:24','2017-04-08 09:21:24'),(4,'TRANS',0,'N','test',NULL,'2017-04-08 10:26:21','2017-04-08 10:26:21'),(5,'TRANS',0,'N','test',NULL,'2017-04-08 16:11:16','2017-04-08 16:11:16'),(6,'TRANS',0,'N','test',NULL,'2017-04-08 16:13:46','2017-04-08 16:13:46'),(7,'TRANS',7,'N','test',NULL,'2017-04-08 16:25:21','2017-04-08 16:25:21'),(8,'TRANS',8,'N','test',NULL,'2017-04-08 16:26:17','2017-04-08 16:26:20'),(9,'TRANS',4,'N','test',NULL,'2017-04-08 16:52:41','2017-04-08 16:52:46'),(10,'PAYREQ',1,'N','test',NULL,'2017-04-09 02:39:02','2017-04-09 02:39:12'),(11,'TRANS',5,'N','admin','admin','2017-04-09 02:40:45','2017-04-09 02:40:45'),(12,'PAYREQ',2,'N','test',NULL,'2017-04-11 02:55:44','2017-04-11 02:56:01'),(13,'PAYREQ',0,'N','test',NULL,'2017-04-28 21:33:38','2017-04-28 21:33:38'),(14,'PAYREQ',7,'N','test',NULL,'2017-04-29 06:45:45','2017-04-29 07:10:48'),(15,'PAYREQ',0,'N','test',NULL,'2017-04-29 08:46:27','2017-04-29 08:46:27'),(16,'PAYREQ',0,'N','test',NULL,'2017-04-30 20:52:43','2017-04-30 20:52:43'),(17,'TAX',7,'N','test',NULL,'2017-05-02 02:22:55','2017-05-02 02:22:55'),(18,'PAYREQ',0,'N','test',NULL,'2017-05-02 02:59:12','2017-05-02 02:59:12'),(19,'PAYREQ',0,'N','test',NULL,'2017-05-02 03:29:08','2017-05-02 03:29:08'),(20,'PAYREQ',0,'N','test',NULL,'2017-05-02 04:37:27','2017-05-02 04:37:27'),(21,'PAYREQ',0,'N','test',NULL,'2017-05-02 05:03:52','2017-05-02 05:03:52'),(22,'PAYREQ',9,'N','test',NULL,'2017-05-02 06:41:35','2017-05-02 06:44:56'),(23,'TAX',9,'N','test',NULL,'2017-05-02 06:43:55','2017-05-02 06:44:56'),(24,'PAYREQ',10,'N','test',NULL,'2017-05-02 06:51:21','2017-05-02 06:51:52'),(25,'TAX',10,'N','test',NULL,'2017-05-02 06:51:35','2017-05-02 06:51:52'),(26,'TAX',3,'N','test',NULL,'2017-05-02 09:14:11','2017-05-02 09:14:11'),(27,'PAYREAL',3,'N','test',NULL,'2017-05-02 09:55:29','2017-05-02 09:55:29'),(28,'TRANS',8,'N','admin','admin','2017-05-02 10:20:39','2017-05-02 10:20:39'),(29,'PAYREQ',12,'N','test',NULL,'2017-05-04 09:35:33','2017-05-04 09:38:24'),(30,'TAX',12,'N','test',NULL,'2017-05-04 09:35:43','2017-05-04 09:38:24'),(31,'PAYREAL',12,'N','test',NULL,'2017-05-04 10:50:53','2017-05-04 10:50:53'),(32,'TRANS',10,'N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(33,'TRANS',11,'N','admin','admin','2017-05-04 10:51:54','2017-05-04 10:51:54'),(34,'TRANS',12,'N','admin','admin','2017-05-11 02:16:34','2017-05-11 02:16:34'),(35,'PAYREQ',13,'N','test',NULL,'2017-05-13 15:49:05','2017-05-13 15:49:12'),(36,'TRANS',13,'N','test',NULL,'2017-05-13 17:48:47','2017-05-13 17:49:08'),(37,'TRANS',15,'N','test',NULL,'2017-05-14 11:43:16','2017-05-14 11:43:18'),(38,'PAYREQ',5,'N','test',NULL,'2017-05-14 14:22:17','2017-05-14 14:22:17'),(39,'PAYREQ',6,'N','test',NULL,'2017-05-14 15:36:43','2017-05-14 15:36:43'),(40,'PAYREAL',10,'N','test',NULL,'2017-05-14 16:03:05','2017-05-14 16:03:05'),(41,'TRANS',17,'N','admin','admin','2017-05-14 16:03:22','2017-05-14 16:03:22'),(42,'TAX',4,'N','test',NULL,'2017-05-17 04:26:27','2017-05-17 04:26:27'),(43,'TRANS',19,'N','admin','admin','2017-05-17 04:26:32','2017-05-17 04:26:32'),(44,'TAX',14,'N','test',NULL,'2017-05-17 04:29:13','2017-05-17 04:29:24'),(45,'TRANS',20,'N','admin','admin','2017-05-17 04:30:47','2017-05-17 04:30:47'),(46,'PAYREQ',15,'N','test',NULL,'2017-05-18 18:56:06','2017-05-18 18:56:12'),(47,'PAYREAL',15,'N','test',NULL,'2017-05-18 18:57:34','2017-05-18 18:57:34'),(48,'TAX',15,'N','test',NULL,'2017-05-19 01:41:19','2017-05-19 01:41:19'),(49,'TRANS',23,'N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25'),(50,'TRANS',24,'N','admin','admin','2017-05-19 01:41:25','2017-05-19 01:41:25');
/*!40000 ALTER TABLE `dm_master` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-01 15:37:58
