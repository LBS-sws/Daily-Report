/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-09-10 12:01:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_stop_remark
-- ----------------------------
DROP TABLE IF EXISTS `swo_stop_remark`;
CREATE TABLE `swo_stop_remark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `remark` varchar(1000) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='服務終止原因';


-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
ALTER TABLE swo_serviceid ADD COLUMN freq varchar(100) DEFAULT NULL COMMENT '服务次數' AFTER ctrt_end_dt;
ALTER TABLE swo_serviceid ADD COLUMN reason varchar(1000) DEFAULT NULL COMMENT '終止原因' AFTER remarks2;

