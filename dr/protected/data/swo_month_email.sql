/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-11-09 11:24:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_month_email`
-- ----------------------------
DROP TABLE IF EXISTS `swo_month_email`;
CREATE TABLE `swo_month_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_dt` datetime NOT NULL,
  `from_addr` varchar(1000) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_month_email
-- ----------------------------
