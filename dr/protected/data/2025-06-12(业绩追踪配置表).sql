/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2025-06-12 11:56:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_city_track
-- ----------------------------
DROP TABLE IF EXISTS `swo_city_track`;
CREATE TABLE `swo_city_track` (
  `code` char(5) NOT NULL COMMENT '城市編號',
  `show_type` int(1) NOT NULL DEFAULT '1' COMMENT '是否統計 1：統計 0：不統計',
  `end_name` varchar(50) DEFAULT NULL COMMENT '最終統計名称',
  `z_index` int(11) DEFAULT '0' COMMENT '層級 數值越高顯示越靠前',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业绩追踪城市配置';
