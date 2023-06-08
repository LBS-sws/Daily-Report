/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-06-08 11:56:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_city_set
-- ----------------------------
DROP TABLE IF EXISTS `swo_city_set`;
CREATE TABLE `swo_city_set` (
  `code` char(5) NOT NULL COMMENT '城市編號',
  `show_type` int(1) NOT NULL DEFAULT '1' COMMENT '是否統計 1：統計 0：不統計',
  `office_type` int(1) NOT NULL DEFAULT '0' COMMENT '辦事處分類 1：分類 0：不分',
  `add_type` int(1) NOT NULL DEFAULT '0' COMMENT '數據是否疊加到最終區域 0:不疊加 1：疊加',
  `region_code` char(5) DEFAULT NULL COMMENT '最終統計區域',
  `z_index` int(11) DEFAULT '0' COMMENT '層級 數值越高顯示越靠前',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理層統計的城市';
