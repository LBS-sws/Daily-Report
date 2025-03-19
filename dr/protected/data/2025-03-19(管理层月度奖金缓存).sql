/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2025-03-19 12:08:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_manage_cache
-- ----------------------------
DROP TABLE IF EXISTS `swo_manage_cache`;
CREATE TABLE `swo_manage_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_no` int(4) NOT NULL,
  `month_no` int(2) NOT NULL,
  `dataJson` longtext COMMENT '员工奖金数据',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:已固定',
  `update_user` varchar(255) DEFAULT NULL COMMENT '操作用户',
  `update_date` datetime DEFAULT NULL COMMENT '操作时间',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='管理层月度奖金固定表';
