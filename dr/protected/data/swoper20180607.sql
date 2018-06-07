-- MySQL dump 10.13  Distrib 5.6.26, for Linux (i686)
--
-- Host: localhost    Database: swoper_w
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
-- Table structure for table `swo_company`
--

DROP TABLE IF EXISTS `swo_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roster_id` mediumint(9) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(1000) NOT NULL,
  `full_name` varchar(1000) DEFAULT NULL,
  `tax_reg_no` varchar(100) DEFAULT NULL,
  `cont_name` varchar(100) DEFAULT NULL,
  `cont_phone` varchar(30) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4711 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_company_status`
--

DROP TABLE IF EXISTS `swo_company_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_company_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` char(1) NOT NULL,
  `type_list` varchar(255) DEFAULT NULL,
  `ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4711 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_customer_type`
--

DROP TABLE IF EXISTS `swo_customer_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_customer_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `rpt_cat` char(10) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_email_queue`
--

DROP TABLE IF EXISTS `swo_email_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_email_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_dt` datetime DEFAULT CURRENT_TIMESTAMP,
  `from_addr` varchar(255) NOT NULL,
  `to_addr` varchar(1000) NOT NULL,
  `cc_addr` varchar(1000) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `message` text,
  `status` char(1) DEFAULT 'P',
  `ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_email_queue_attm`
--

DROP TABLE IF EXISTS `swo_email_queue_attm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_email_queue_attm` (
  `queue_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_enquiry`
--

DROP TABLE IF EXISTS `swo_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_enquiry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_dt` datetime NOT NULL,
  `customer` varchar(255) NOT NULL,
  `nature_type` int(10) unsigned DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `tel_no` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `source_code` char(5) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `follow_staff` varchar(500) DEFAULT NULL,
  `follow_dt` datetime DEFAULT NULL,
  `follow_result` varchar(1000) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `record_by` varchar(100) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_followup`
--

DROP TABLE IF EXISTS `swo_followup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_followup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_dt` datetime NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `company_name` varchar(1000) NOT NULL,
  `content` varchar(5000) DEFAULT NULL,
  `cont_info` varchar(500) DEFAULT NULL,
  `resp_staff` varchar(1000) DEFAULT NULL,
  `resp_tech` varchar(1000) DEFAULT NULL,
  `mgr_notify` char(1) DEFAULT '',
  `sch_dt` datetime DEFAULT NULL,
  `follow_staff` varchar(1000) DEFAULT NULL,
  `leader` char(1) DEFAULT '',
  `follow_tech` varchar(1000) DEFAULT NULL,
  `fin_dt` datetime DEFAULT NULL,
  `follow_action` varchar(1000) DEFAULT NULL,
  `mgr_talk` char(1) DEFAULT '',
  `changex` varchar(1000) DEFAULT NULL,
  `tech_notify` varchar(1000) DEFAULT NULL,
  `fp_fin_dt` datetime DEFAULT NULL,
  `fp_call_dt` datetime DEFAULT NULL,
  `fp_cust_name` varchar(100) DEFAULT NULL,
  `fp_comment` varchar(1000) DEFAULT NULL,
  `svc_next_dt` datetime DEFAULT NULL,
  `svc_call_dt` datetime DEFAULT NULL,
  `svc_cust_name` varchar(100) DEFAULT NULL,
  `svc_comment` varchar(1000) DEFAULT NULL,
  `mcard_remarks` varchar(1000) DEFAULT NULL,
  `mcard_staff` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_group`
--

DROP TABLE IF EXISTS `swo_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `a_read_only` varchar(255) DEFAULT '',
  `a_read_write` varchar(255) DEFAULT '',
  `a_control` varchar(255) DEFAULT '',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_location`
--

DROP TABLE IF EXISTS `swo_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_login_log`
--

DROP TABLE IF EXISTS `swo_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_login_log` (
  `station_id` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `client_ip` varchar(20) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_logistic`
--

DROP TABLE IF EXISTS `swo_logistic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_logistic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_dt` datetime NOT NULL,
  `seq` tinyint(3) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `company_name` varchar(1000) NOT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `follow_staff` varchar(1000) DEFAULT NULL,
  `pay_method` varchar(200) DEFAULT NULL,
  `location` int(10) unsigned NOT NULL,
  `location_dtl` varchar(200) DEFAULT NULL,
  `finish` char(1) DEFAULT 'N',
  `deadline` varchar(200) DEFAULT NULL,
  `reason` varchar(1000) DEFAULT NULL,
  `repair` varchar(1000) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_logistic_dtl`
--

DROP TABLE IF EXISTS `swo_logistic_dtl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_logistic_dtl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `task` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned DEFAULT NULL,
  `finish` char(1) DEFAULT 'N',
  `deadline` datetime DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_mgr_feedback`
--

DROP TABLE IF EXISTS `swo_mgr_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_mgr_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `request_dt` datetime NOT NULL,
  `feedback_dt` datetime DEFAULT NULL,
  `feedback_cat_list` varchar(300) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `status` char(1) DEFAULT 'N',
  `rpt_id` int(10) unsigned DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_mgr_feedback_rmk`
--

DROP TABLE IF EXISTS `swo_mgr_feedback_rmk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_mgr_feedback_rmk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned NOT NULL,
  `feedback_cat` char(5) NOT NULL,
  `feedback` varchar(5000) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_monthly_dtl`
--

DROP TABLE IF EXISTS `swo_monthly_dtl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_monthly_dtl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hdr_id` int(10) unsigned NOT NULL,
  `data_field` char(5) NOT NULL,
  `data_value` varchar(100) DEFAULT NULL,
  `manual_input` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_monthly_field`
--

DROP TABLE IF EXISTS `swo_monthly_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_monthly_field` (
  `code` char(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `upd_type` char(1) NOT NULL DEFAULT 'M',
  `field_type` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) DEFAULT 'Y',
  `function_name` varchar(200) DEFAULT NULL,
  `excel_row` smallint(5) unsigned DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_monthly_hdr`
--

DROP TABLE IF EXISTS `swo_monthly_hdr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_monthly_hdr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` char(5) NOT NULL,
  `year_no` smallint(5) unsigned NOT NULL,
  `month_no` tinyint(3) unsigned NOT NULL,
  `status` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_nature`
--

DROP TABLE IF EXISTS `swo_nature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_nature` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `rpt_cat` char(10) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_notification`
--

DROP TABLE IF EXISTS `swo_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system_id` varchar(15) NOT NULL,
  `note_type` varchar(5) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `message` text,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_notification_user`
--

DROP TABLE IF EXISTS `swo_notification_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_notification_user` (
  `note_id` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `status` char(1) DEFAULT 'N',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `notification_user` (`note_id`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_product`
--

DROP TABLE IF EXISTS `swo_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL DEFAULT '',
  `description` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_qc`
--

DROP TABLE IF EXISTS `swo_qc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_qc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_dt` datetime NOT NULL,
  `job_staff` varchar(500) DEFAULT NULL,
  `team` varchar(100) DEFAULT NULL,
  `month` char(5) DEFAULT NULL,
  `input_dt` datetime DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `company_name` varchar(500) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `service_score` varchar(100) DEFAULT NULL,
  `cust_score` varchar(100) DEFAULT NULL,
  `cust_comment` varchar(1000) DEFAULT NULL,
  `qc_result` varchar(100) DEFAULT NULL,
  `env_grade` char(1) DEFAULT NULL,
  `qc_dt` datetime DEFAULT NULL,
  `cust_sign` varchar(100) DEFAULT NULL,
  `qc_staff` varchar(500) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_qc_info`
--

DROP TABLE IF EXISTS `swo_qc_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_qc_info` (
  `qc_id` int(10) unsigned NOT NULL,
  `field_id` varchar(50) NOT NULL,
  `field_value` varchar(2000) DEFAULT NULL,
  `field_blob` longblob,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `qc_id` (`qc_id`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_queue`
--

DROP TABLE IF EXISTS `swo_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rpt_desc` varchar(250) NOT NULL,
  `req_dt` datetime DEFAULT NULL,
  `fin_dt` datetime DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `status` char(1) NOT NULL,
  `rpt_type` varchar(10) NOT NULL,
  `ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rpt_content` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_queue_param`
--

DROP TABLE IF EXISTS `swo_queue_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_queue_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `param_field` varchar(50) NOT NULL,
  `param_value` varchar(500) DEFAULT NULL,
  `ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2476 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_queue_user`
--

DROP TABLE IF EXISTS `swo_queue_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_queue_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_service`
--

DROP TABLE IF EXISTS `swo_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `company_name` varchar(1000) NOT NULL,
  `nature_type` int(10) unsigned DEFAULT NULL,
  `cust_type` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `b4_product_id` int(10) unsigned DEFAULT NULL,
  `b4_service` varchar(1000) DEFAULT NULL,
  `b4_freq` varchar(100) DEFAULT NULL,
  `b4_paid_type` char(1) DEFAULT 'M',
  `b4_amt_paid` decimal(11,2) DEFAULT '0.00',
  `service` varchar(1000) DEFAULT NULL,
  `freq` varchar(100) DEFAULT NULL,
  `paid_type` char(1) DEFAULT 'M',
  `amt_paid` decimal(11,2) DEFAULT '0.00',
  `amt_install` decimal(11,2) DEFAULT '0.00',
  `need_install` char(1) DEFAULT 'N',
  `salesman` varchar(1000) DEFAULT NULL,
  `sign_dt` datetime DEFAULT NULL,
  `ctrt_end_dt` datetime DEFAULT NULL,
  `ctrt_period` tinyint(4) DEFAULT '0',
  `cont_info` varchar(500) DEFAULT NULL,
  `first_dt` datetime DEFAULT NULL,
  `first_tech` varchar(1000) DEFAULT NULL,
  `reason` varchar(1000) DEFAULT NULL,
  `status` char(1) DEFAULT 'N',
  `status_dt` datetime DEFAULT NULL,
  `remarks` varchar(2000) DEFAULT NULL,
  `equip_install_dt` datetime DEFAULT NULL,
  `org_equip_qty` smallint(5) unsigned DEFAULT NULL,
  `rtn_equip_qty` smallint(5) unsigned DEFAULT NULL,
  `remarks2` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_swo_service_1` (`company_name`(100))
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_service_type`
--

DROP TABLE IF EXISTS `swo_service_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_service_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `rpt_cat` char(10) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_staff`
--

DROP TABLE IF EXISTS `swo_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_staff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `position` varchar(250) DEFAULT NULL,
  `staff_type` varchar(15) DEFAULT NULL,
  `leader` varchar(15) DEFAULT NULL,
  `join_dt` datetime DEFAULT NULL,
  `ctrt_start_dt` datetime DEFAULT NULL,
  `ctrt_period` tinyint(4) DEFAULT '0',
  `ctrt_renew_dt` datetime DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `leave_dt` datetime DEFAULT NULL,
  `leave_reason` varchar(1000) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `swo_staff_v`
--

DROP TABLE IF EXISTS `swo_staff_v`;
/*!50001 DROP VIEW IF EXISTS `swo_staff_v`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `swo_staff_v` AS SELECT 
 1 AS `id`,
 1 AS `code`,
 1 AS `name`,
 1 AS `position`,
 1 AS `staff_type`,
 1 AS `leader`,
 1 AS `join_dt`,
 1 AS `ctrt_start_dt`,
 1 AS `ctrt_period`,
 1 AS `ctrt_renew_dt`,
 1 AS `email`,
 1 AS `leave_dt`,
 1 AS `leave_reason`,
 1 AS `remarks`,
 1 AS `city`,
 1 AS `lcu`,
 1 AS `luu`,
 1 AS `lcd`,
 1 AS `lud`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `swo_station`
--

DROP TABLE IF EXISTS `swo_station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_station` (
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
-- Table structure for table `swo_station_request`
--

DROP TABLE IF EXISTS `swo_station_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_station_request` (
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
-- Table structure for table `swo_supplier`
--

DROP TABLE IF EXISTS `swo_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(1000) NOT NULL,
  `full_name` varchar(1000) DEFAULT NULL,
  `tax_reg_no` varchar(100) DEFAULT NULL,
  `cont_name` varchar(100) DEFAULT NULL,
  `cont_phone` varchar(30) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `acct_no` varchar(100) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_task`
--

DROP TABLE IF EXISTS `swo_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `task_type` char(5) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `swo_user_option`
--

DROP TABLE IF EXISTS `swo_user_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swo_user_option` (
  `username` varchar(30) NOT NULL,
  `option_key` varchar(30) NOT NULL,
  `option_value` varchar(255) DEFAULT NULL,
  UNIQUE KEY `idx_swo_user_option_1` (`username`,`option_key`)
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
-- Dumping routines for database 'swoper_w'
--
/*!50003 DROP FUNCTION IF EXISTS `CustomerStatus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `CustomerStatus`(p_id int unsigned, p_code varchar(20), p_name varchar(1000), p_city char(5)) RETURNS char(1) CHARSET utf8
BEGIN
DECLARE done int default false;
DECLARE cnt_all varchar(5000);
DECLARE cnt_term varchar(255);
DECLARE status char(1);

DECLARE cur1 CURSOR FOR
SELECT count(a.id), sum(case when a.status<>'T' then 1 else 0 end) 
FROM swo_service a
LEFT OUTER JOIN swo_service b ON a.company_name=b.company_name AND a.status_dt < b.status_dt AND a.cust_type=b.cust_type
WHERE b.id IS NULL AND a.city=p_city
AND (a.company_id=p_id OR a.company_name like concat(p_code,'%') OR a.company_name like concat('%',p_name));
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = true;

OPEN cur1;
read_loop: LOOP
FETCH cur1 INTO cnt_all, cnt_term;
IF done THEN
LEAVE read_loop;
END IF;
SET status = IF(cnt_all=0, 'U', IF(cnt_term > 0, 'A', 'T'));
END LOOP;
CLOSE cur1;
RETURN status;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `CustomerType` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `CustomerType`(p_id int unsigned, p_code varchar(20), p_name varchar(1000), p_city char(5)) RETURNS varchar(255) CHARSET utf8
BEGIN
DECLARE done int default false;
DECLARE custtype varchar(10);
DECLARE list varchar(255);

DECLARE cur1 CURSOR FOR
select distinct cast(a.cust_type as char(10))
from swo_service a
where a.city=p_city
and (a.company_id=p_id or a.company_name like concat(p_code,'%') 
or a.company_name like concat('%',p_name));
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = true;

SET list = '';

OPEN cur1;
read_loop: LOOP
FETCH cur1 INTO custtype;
IF done THEN
LEAVE read_loop;
END IF;
SET list = concat(list,'/',custtype,'/,');
END LOOP;
CLOSE cur1;
RETURN list;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `swo_staff_v`
--

/*!50001 DROP VIEW IF EXISTS `swo_staff_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `swo_staff_v` AS select `a`.`id` AS `id`,`a`.`code` AS `code`,`a`.`name` AS `name`,`b`.`name` AS `position`,if((`a`.`staff_type` = 'Office'),'OFFICE',if((`a`.`staff_type` = 'Technician'),'TECHNICIAN',if((`a`.`staff_type` = 'Sales'),'SALES','OTHERS'))) AS `staff_type`,if((`a`.`staff_leader` = 'Group Leader'),'GROUP',if((`a`.`staff_leader` = 'Team Leader'),'TEAM','NIL')) AS `leader`,if((isnull(`a`.`entry_time`) or (`a`.`entry_time` = '')),NULL,ifnull(str_to_date(`a`.`entry_time`,'%Y/%m/%d'),str_to_date(`a`.`entry_time`,'%Y-%m-%d'))) AS `join_dt`,`a`.`start_time` AS `ctrt_start_dt`,timestampdiff(MONTH,`a`.`start_time`,(ifnull(str_to_date(`a`.`end_time`,'%Y/%m/%d'),str_to_date(`a`.`end_time`,'%Y-%m-%d')) + interval 1 day)) AS `ctrt_period`,if((isnull(`a`.`end_time`) or (`a`.`end_time` = '')),NULL,(ifnull(str_to_date(`a`.`end_time`,'%Y/%m/%d'),str_to_date(`a`.`end_time`,'%Y-%m-%d')) + interval 1 day)) AS `ctrt_renew_dt`,`a`.`email` AS `email`,if((isnull(`a`.`leave_time`) or (`a`.`leave_time` = '')),NULL,ifnull(str_to_date(`a`.`leave_time`,'%Y/%m/%d'),str_to_date(`a`.`leave_time`,'%Y-%m-%d'))) AS `leave_dt`,`a`.`leave_reason` AS `leave_reason`,`a`.`remark` AS `remarks`,`a`.`city` AS `city`,`a`.`lcu` AS `lcu`,`a`.`luu` AS `luu`,`a`.`lcd` AS `lcd`,`a`.`lud` AS `lud` from (`hrdev`.`hr_employee` `a` left join `hrdev`.`hr_dept` `b` on((`a`.`position` = `b`.`id`))) */;
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

-- Dump completed on 2018-06-07 11:26:53
