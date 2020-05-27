/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-05-27 16:08:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_customer_type_twoname`
-- ----------------------------
DROP TABLE IF EXISTS `swo_customer_type_twoname`;
CREATE TABLE `swo_customer_type_twoname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_type_id` int(11) DEFAULT NULL COMMENT '上级类别id',
  `conditions` int(11) DEFAULT NULL,
  `cust_type_name` varchar(255) DEFAULT '' COMMENT '客户类别二级名字',
  `fraction` int(11) DEFAULT NULL COMMENT '分数',
  `toplimit` int(11) DEFAULT NULL COMMENT '上限',
  `luu` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_customer_type_twoname
-- ----------------------------
INSERT INTO `swo_customer_type_twoname` VALUES ('5', '2', null, '阿速达啥', '5', '3', 'test', 'test', '2020-05-22 09:34:57', '2020-05-22 09:34:57');
INSERT INTO `swo_customer_type_twoname` VALUES ('6', '9', null, 'Purell机器', '1', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('7', '9', null, 'Purell-消毒液', '3', '10', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('8', '9', null, 'Gojo机器', '1', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('9', '9', null, 'Gojo-消毒液', '3', '10', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('10', '9', null, '手部消毒机', '1', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('11', '9', null, '手部消毒液', '2', '10', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('12', '9', null, '坐厕板消毒机', '1', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('13', '9', null, '坐厕板消毒液', '2', '10', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('14', '9', null, 'Biozone (PR)', '3', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('15', '9', null, 'Biozone (AC)', '4', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('16', '9', null, 'Biozone (PZ或MPZ)', '5', '0', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('17', '9', null, '洗地易或其他化学剂', '3', '12', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('18', '9', null, '洗手液', '1', '5', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('19', '9', null, '纸品', '1', '5', 'test', 'test', '2020-05-27 11:03:58', '2020-05-27 11:03:58');
INSERT INTO `swo_customer_type_twoname` VALUES ('23', '1', null, '12', '1', '2', 'test', 'test', '2020-05-27 15:59:44', '2020-05-27 15:59:44');
INSERT INTO `swo_customer_type_twoname` VALUES ('24', '1', '2', '1', '1', '2', 'test', 'test', '2020-05-27 16:05:30', '2020-05-27 16:05:30');
INSERT INTO `swo_customer_type_twoname` VALUES ('25', '1', '3', '3', '2', '3', 'test', 'test', '2020-05-27 16:06:12', '2020-05-27 16:06:12');
