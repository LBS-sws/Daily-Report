/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-11-12 16:55:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_month_email`
-- ----------------------------
DROP TABLE IF EXISTS `swo_month_email`;
CREATE TABLE `swo_month_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_dt` datetime NOT NULL,
  `from_addr` varchar(1000) DEFAULT NULL,
  `city` varchar(10) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_month_email
-- ----------------------------
INSERT INTO `swo_month_email` VALUES ('11', '2020-09-01 00:00:00', 'test@lbsgroup.com.cn', 'SH', '月报表总汇-2020/9', '2018/6<br/>月报表总分：35.59    &nbsp;&nbsp;&nbsp;&nbsp;城市：上海<br/>内容分析', 'admin', '2020-11-12 16:54:16');
INSERT INTO `swo_month_email` VALUES ('12', '2020-08-01 00:00:00', 'test@lbsgroup.com.cn', 'SH', '月报表总汇-2020/8', '2018/7<br/>月报表总分：36.74    &nbsp;&nbsp;&nbsp;&nbsp;城市：上海<br/>内容分析', 'admin', '2020-11-12 16:54:43');
