/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-05-22 15:11:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_customer_type_twoname`
-- ----------------------------
DROP TABLE IF EXISTS `swo_customer_type_twoname`;
CREATE TABLE `swo_customer_type_twoname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_type_id` int(11) DEFAULT NULL COMMENT '上级类别id',
  `cust_type_name` varchar(255) DEFAULT '' COMMENT '客户类别二级名字',
  `fraction` int(11) DEFAULT NULL COMMENT '分数',
  `toplimit` int(11) DEFAULT NULL COMMENT '上限',
  `luu` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_customer_type_twoname
-- ----------------------------
INSERT INTO `swo_customer_type_twoname` VALUES ('1', '1', '阿速达', '2', '0', 'test', 'test', '2020-05-22 09:19:06', '2020-05-22 09:19:06');
INSERT INTO `swo_customer_type_twoname` VALUES ('2', '1', '发发发', '222', '3', 'test', 'test', '2020-05-22 09:28:48', '2020-05-22 09:28:48');
INSERT INTO `swo_customer_type_twoname` VALUES ('4', '1', '咕咕咕', '3', '5', 'test', 'test', '2020-05-22 09:33:32', '2020-05-22 09:33:32');
INSERT INTO `swo_customer_type_twoname` VALUES ('5', '2', '阿速达啥', '5', '3', 'test', 'test', '2020-05-22 09:34:57', '2020-05-22 09:34:57');
