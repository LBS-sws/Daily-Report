/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-05-26 09:12:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_lifeline
-- ----------------------------
DROP TABLE IF EXISTS `swo_lifeline`;
CREATE TABLE `swo_lifeline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` char(5) NOT NULL,
  `life_date` date NOT NULL DEFAULT '2000-01-01' COMMENT '生命線生效日期',
  `life_num` int(10) NOT NULL DEFAULT '0' COMMENT '生命線數值',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='銷售生命線設定表';
