/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-05-11 15:42:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_nature_type
-- ----------------------------
DROP TABLE IF EXISTS `swo_nature_type`;
CREATE TABLE `swo_nature_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '欄位名稱',
  `nature_id` int(11) NOT NULL COMMENT '性質id（一級欄位）',
  `rpt_u` int(11) DEFAULT NULL COMMENT 'u系統對應id',
  `score_bool` int(11) NOT NULL DEFAULT '0' COMMENT '是否計算積分0：不計算 1：計算',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='性質二級欄位表';

-- ----------------------------
-- Table structure for swo_service
-- ----------------------------
ALTER TABLE swo_service ADD COLUMN nature_type_two int(10) NULL DEFAULT NULL COMMENT '性質二級欄位' AFTER nature_type;

-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
ALTER TABLE swo_serviceid ADD COLUMN nature_type_two int(10) NULL DEFAULT NULL COMMENT '性質二級欄位' AFTER nature_type;
