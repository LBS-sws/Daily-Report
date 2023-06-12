/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-06-12 16:30:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_lifeline_info
-- ----------------------------
DROP TABLE IF EXISTS `swo_lifeline_info`;
CREATE TABLE `swo_lifeline_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lifeline_id` int(10) NOT NULL COMMENT '生命線id',
  `office_id` int(10) NOT NULL COMMENT '辦事處id',
  `life_num` int(10) NOT NULL DEFAULT '0' COMMENT '生命線數值',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='銷售生命線設定表';
