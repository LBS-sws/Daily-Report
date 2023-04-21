/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-04-21 17:54:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_system_log
-- ----------------------------
DROP TABLE IF EXISTS `swo_system_log`;
CREATE TABLE `swo_system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_code` varchar(255) DEFAULT NULL COMMENT '記錄編號',
  `log_date` datetime NOT NULL COMMENT '記錄時間',
  `log_user` varchar(70) NOT NULL COMMENT '記錄賬號',
  `log_type` varchar(20) NOT NULL COMMENT '操作模块',
  `log_type_name` varchar(100) NOT NULL COMMENT '操作模块的名稱',
  `option_str` varchar(50) NOT NULL DEFAULT '修改' COMMENT '操作類型',
  `option_text` text NOT NULL COMMENT '操作內容',
  `city` varchar(255) NOT NULL,
  `show_bool` int(11) NOT NULL DEFAULT '1' COMMENT '是否顯示',
  `leave_log` int(11) NOT NULL DEFAULT '1' COMMENT '記錄級別:1普通記錄 2：危險記錄',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='系統記錄表';
