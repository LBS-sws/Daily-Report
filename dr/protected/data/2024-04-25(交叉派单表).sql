/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-04-25 15:18:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_cross
-- ----------------------------
DROP TABLE IF EXISTS `swo_cross`;
CREATE TABLE `swo_cross` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_type` int(11) NOT NULL DEFAULT '0' COMMENT '0：swo_service 1：swo_service_ka',
  `service_id` int(11) NOT NULL,
  `contract_no` varchar(255) NOT NULL COMMENT '合约编号',
  `apply_date` date NOT NULL COMMENT '申请日期',
  `month_amt` decimal(10,2) NOT NULL COMMENT '月金额',
  `rate_num` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '占比（0~100）',
  `old_city` varchar(10) NOT NULL,
  `cross_city` varchar(10) NOT NULL COMMENT '承接城市',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '状态：1：待审核 2：已拒绝 3：已审核 5:已完成',
  `reject_note` text COMMENT '拒绝原因',
  `remark` text COMMENT '备注',
  `audit_user` varchar(255) DEFAULT NULL COMMENT '审核人(员工)',
  `audit_date` datetime DEFAULT NULL COMMENT '审核时间',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='交叉派单表';
