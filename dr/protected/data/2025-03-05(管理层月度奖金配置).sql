/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2025-03-05 16:07:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_manage_staff
-- ----------------------------
DROP TABLE IF EXISTS `swo_manage_staff`;
CREATE TABLE `swo_manage_staff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL COMMENT '生效时间',
  `employee_id` int(11) NOT NULL COMMENT '员工id',
  `city` varchar(255) NOT NULL COMMENT '员工归属城市',
  `city_allow` text,
  `city_allow_name` text,
  `job_key` int(11) NOT NULL COMMENT '职位：1：副总监 2：高级总经理.....',
  `team_rate` double(4,2) DEFAULT '0.00' COMMENT '团队提成率',
  `person_type` int(11) DEFAULT '1' COMMENT '个人提成金额类型 1：根据配置 2：其它',
  `person_money` double(11,2) DEFAULT '0.00' COMMENT '个人提成金额',
  `condition_type` int(11) DEFAULT '1' COMMENT '条件类型 1：有条件限制',
  `condition_money` double(7,2) DEFAULT '0.00' COMMENT '新签金额不低于本金额',
  `max_bonus` int(11) DEFAULT '4000' COMMENT '最大目标金额',
  `z_index` int(11) DEFAULT '1' COMMENT '排序。数值越大，显示顺序越靠前',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='管理層統計的城市';

-- ----------------------------
-- Table structure for swo_manage_stop_hdl
-- ----------------------------
DROP TABLE IF EXISTS `swo_manage_stop_hdl`;
CREATE TABLE `swo_manage_stop_hdl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hdr_id` int(11) NOT NULL,
  `operator` char(2) NOT NULL,
  `stop_rate` double(5,2) NOT NULL COMMENT '停单率',
  `coefficient` double(5,2) NOT NULL COMMENT '调解系数',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='管理層統計的城市';

-- ----------------------------
-- Records of swo_manage_stop_hdl
-- ----------------------------
INSERT INTO `swo_manage_stop_hdl` VALUES ('1', '1', 'LT', '1.00', '1.30', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('2', '1', 'LT', '1.50', '1.20', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('3', '1', 'LT', '2.50', '1.10', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('4', '1', 'LT', '3.00', '1.00', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('5', '1', 'LT', '3.50', '0.80', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('6', '1', 'LT', '5.00', '0.60', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');
INSERT INTO `swo_manage_stop_hdl` VALUES ('7', '1', 'GT', '5.00', '0.40', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:18:17');

-- ----------------------------
-- Table structure for swo_manage_stop_hdr
-- ----------------------------
DROP TABLE IF EXISTS `swo_manage_stop_hdr`;
CREATE TABLE `swo_manage_stop_hdr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL COMMENT '生效时间',
  `set_name` varchar(100) NOT NULL COMMENT '配置名称',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理層統計的城市';

-- ----------------------------
-- Records of swo_manage_stop_hdr
-- ----------------------------
INSERT INTO `swo_manage_stop_hdr` VALUES ('1', '2020-01-01', '停单率_提成调节系数（2020年1月）', 'shenchao', 'shenchao', '2025-03-04 15:12:56', '2025-03-04 15:24:21');
