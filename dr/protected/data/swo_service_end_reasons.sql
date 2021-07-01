/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-07-01 16:39:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_service_end_reasons`
-- ----------------------------
DROP TABLE IF EXISTS `swo_service_end_reasons`;
CREATE TABLE `swo_service_end_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(1000) DEFAULT NULL COMMENT '原因',
  `content` varchar(1000) DEFAULT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

