/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-05-23 11:52:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_service_history
-- ----------------------------
DROP TABLE IF EXISTS `swo_service_history`;
CREATE TABLE `swo_service_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_type` int(2) NOT NULL DEFAULT '1' COMMENT '類型：1客戶服務 2：ID客戶服務',
  `update_type` int(11) NOT NULL DEFAULT '1' COMMENT '修改類型 1：修改',
  `update_html` text NOT NULL COMMENT '修改內容',
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='客户服務修改記錄表';
