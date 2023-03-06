/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-03-06 15:53:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_comparison_set
-- ----------------------------
DROP TABLE IF EXISTS `swo_comparison_set`;
CREATE TABLE `swo_comparison_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comparison_year` int(4) NOT NULL,
  `city` char(5) NOT NULL,
  `one_gross` float(14,2) DEFAULT '0.00',
  `one_net` float(14,2) DEFAULT NULL,
  `two_gross` float(14,2) DEFAULT NULL,
  `two_net` float(14,2) DEFAULT NULL,
  `three_gross` float(14,2) DEFAULT NULL,
  `three_net` float(14,2) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='comparison的目標年金額設置表';

-- ----------------------------
-- Table structure for swo_summary_set
-- ----------------------------
DROP TABLE IF EXISTS `swo_summary_set`;
CREATE TABLE `swo_summary_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `summary_year` int(4) NOT NULL,
  `city` char(5) NOT NULL,
  `one_gross` float(14,2) DEFAULT '0.00',
  `one_net` float(14,2) DEFAULT NULL,
  `two_gross` float(14,2) DEFAULT NULL,
  `two_net` float(14,2) DEFAULT NULL,
  `three_gross` float(14,2) DEFAULT NULL,
  `three_net` float(14,2) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='summary的目標年金額設置表';
