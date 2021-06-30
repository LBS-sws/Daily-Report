/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-06-30 17:31:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_service_end_reasons`
-- ----------------------------
DROP TABLE IF EXISTS `swo_service_end_reasons`;
CREATE TABLE `swo_service_end_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(1000) DEFAULT NULL COMMENT '原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


