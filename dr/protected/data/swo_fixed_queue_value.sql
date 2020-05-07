/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-05-07 08:46:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_fixed_queue_value`
-- ----------------------------
DROP TABLE IF EXISTS `swo_fixed_queue_value`;
CREATE TABLE `swo_fixed_queue_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) DEFAULT NULL,
  `touser` varchar(255) DEFAULT NULL,
  `ccuser` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_fixed_queue_value
-- ----------------------------
INSERT INTO `swo_fixed_queue_value` VALUES ('1', 'CD', 'test', '[\"lixin\"]');
INSERT INTO `swo_fixed_queue_value` VALUES ('2', 'S', 'test', '[\"lixin\"]');
INSERT INTO `swo_fixed_queue_value` VALUES ('3', 'SZ', 'test', '[\"lixin\"]');
