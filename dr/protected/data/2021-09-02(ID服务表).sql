/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-09-02 16:34:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_customer_type_id
-- ----------------------------
DROP TABLE IF EXISTS `swo_customer_type_id`;
CREATE TABLE `swo_customer_type_id` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL COMMENT '服務名稱',
  `rpt_cat` char(10) DEFAULT NULL COMMENT '報表類型',
  `single` int(2) DEFAULT '0' COMMENT '是否是一次性服务 0：非一次性  1：一次性',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='客戶服務（ID服務專用）';

-- ----------------------------
-- Records of swo_customer_type_id
-- ----------------------------
INSERT INTO `swo_customer_type_id` VALUES ('1', 'ID服务', 'ID', '0', 'test', 'test', '2021-06-11 15:18:29', '2021-08-27 14:20:06');

-- ----------------------------
-- Table structure for swo_customer_type_info
-- ----------------------------
DROP TABLE IF EXISTS `swo_customer_type_info`;
CREATE TABLE `swo_customer_type_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_type_id` int(11) DEFAULT NULL COMMENT '上级类别id',
  `conditions` int(11) DEFAULT NULL COMMENT '条件',
  `cust_type_name` varchar(255) DEFAULT '' COMMENT '客户类别二级名字',
  `fraction` int(11) DEFAULT NULL COMMENT '分数',
  `toplimit` int(11) DEFAULT NULL COMMENT '上限',
  `single` int(2) DEFAULT '0' COMMENT '是否是一次性服务 0：非一次性  1：一次性',
  `index_num` int(2) NOT NULL DEFAULT '2' COMMENT '几级栏位',
  `luu` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='ID服务的多级栏位';

-- ----------------------------
-- Table structure for swo_payweek
-- ----------------------------
DROP TABLE IF EXISTS `swo_payweek`;
CREATE TABLE `swo_payweek` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) NOT NULL DEFAULT '',
  `description` varchar(1000) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
DROP TABLE IF EXISTS `swo_serviceid`;
CREATE TABLE `swo_serviceid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_new_id` int(11) DEFAULT '0' COMMENT '服務新增的id',
  `service_no` varchar(255) DEFAULT NULL COMMENT '服務編號',
  `company_id` int(10) unsigned DEFAULT NULL COMMENT '公司id',
  `company_name` varchar(1000) NOT NULL COMMENT '公司名稱',
  `nature_type` int(10) unsigned DEFAULT NULL COMMENT '性质id',
  `cust_type` int(10) unsigned DEFAULT NULL COMMENT '客户类别id（一級欄位）',
  `cust_type_name` int(11) DEFAULT '0' COMMENT '客戶類型（二級欄位）',
  `cust_type_three` int(10) DEFAULT '0' COMMENT '客戶類型（三級欄位）',
  `cust_type_four` int(10) DEFAULT '0' COMMENT '客戶類型（四級欄位）',
  `product_id` int(10) unsigned DEFAULT NULL COMMENT '服务內容id',
  `service` varchar(1000) DEFAULT NULL COMMENT '服务內容名字',
  `pay_week` int(11) DEFAULT NULL COMMENT '付款週期',
  `b4_product_id` int(10) unsigned DEFAULT NULL COMMENT '服务內容(更改前)id',
  `b4_service` varchar(1000) DEFAULT NULL COMMENT '服务內容(更改前)-名稱',
  `b4_amt_paid` decimal(11,2) DEFAULT '0.00' COMMENT '服务金额(更改前)',
  `b4_amt_money` decimal(11,3) DEFAULT NULL COMMENT ' 總的金額(更改前)',
  `b4_pieces` int(11) DEFAULT NULL COMMENT '机器数量(更改前)',
  `b4_cust_type_end` int(11) DEFAULT NULL COMMENT '机器型号(更改前)',
  `amt_paid` decimal(11,2) DEFAULT '0.00' COMMENT '服务金额',
  `amt_money` decimal(11,3) DEFAULT NULL COMMENT ' 總的金額',
  `pieces` int(11) DEFAULT NULL COMMENT '机器数量',
  `cust_type_end` int(11) DEFAULT NULL COMMENT '机器型号（最后的栏位）',
  `amt_install` decimal(11,2) DEFAULT '0.00' COMMENT '機器押金',
  `need_install` char(1) DEFAULT 'N' COMMENT '是否收取押金 Y：是',
  `technician_id` int(11) DEFAULT '0' COMMENT '负责技术员id',
  `technician` varchar(1000) DEFAULT NULL COMMENT '负责技术员（名字）',
  `othersalesman_id` int(11) DEFAULT '0' COMMENT '被跨区业务员id',
  `othersalesman` varchar(1000) DEFAULT NULL COMMENT '被跨区业务员(名字）',
  `salesman_id` int(11) DEFAULT '0' COMMENT '业务员id',
  `salesman` varchar(1000) DEFAULT NULL COMMENT '业务员(名字)',
  `sign_dt` datetime DEFAULT NULL COMMENT '签约日期',
  `ctrt_end_dt` datetime DEFAULT NULL COMMENT '合同终止日期',
  `all_number` smallint(6) DEFAULT '0' COMMENT '实际发放月数',
  `surplus` smallint(6) DEFAULT '0' COMMENT '剩余月数',
  `all_number_edit` smallint(6) DEFAULT NULL COMMENT '不知道幹啥用的',
  `ctrt_period` smallint(6) DEFAULT NULL COMMENT '合同年限(月)',
  `cont_info` varchar(500) DEFAULT NULL COMMENT '客户联系/电话',
  `first_dt` datetime DEFAULT NULL COMMENT '首次日期',
  `first_tech_id` int(11) DEFAULT '0' COMMENT '首次技术员id',
  `first_tech` varchar(1000) DEFAULT NULL COMMENT '首次技术员（名字）',
  `reason` varchar(1000) DEFAULT NULL COMMENT '变动原因（不知道幹啥的）',
  `target` int(1) unsigned zerofill DEFAULT '0' COMMENT '不知道幹啥的',
  `other_commission` char(100) DEFAULT NULL COMMENT '不知道幹啥的',
  `commission` char(100) DEFAULT NULL COMMENT '新增是金额，扣钱是提成（不知道幹啥的）',
  `royaltys` decimal(4,3) DEFAULT '0.000',
  `royalty` decimal(6,3) DEFAULT '0.000' COMMENT '該訂單的提成點數',
  `status` char(1) DEFAULT 'N' COMMENT '狀態 N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止',
  `status_copy` int(1) DEFAULT '0' COMMENT '不知道幹啥的',
  `status_dt` datetime DEFAULT NULL COMMENT '訂單的起始日期',
  `remarks` varchar(2000) DEFAULT NULL COMMENT '跨区明细',
  `equip_install_dt` datetime DEFAULT NULL COMMENT '装机器日子',
  `remarks2` varchar(1000) DEFAULT NULL COMMENT '訂單备注',
  `city` char(5) NOT NULL COMMENT '訂單所在城市code',
  `prepay_month` smallint(6) DEFAULT '0' COMMENT '预付月数',
  `prepay_start` smallint(6) DEFAULT '0' COMMENT '预付起始月',
  `change_money` decimal(11,3) DEFAULT NULL COMMENT '變更後的金額',
  `wage_type` int(1) DEFAULT '0' COMMENT '是否參與工資計算 0：不參與 1：參與',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_1` (`company_name`(100)),
  KEY `idx_service_2` (`city`,`status_dt`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='ID服務表（從原來服務表複製過來的）- 字段只增不減';

-- ----------------------------
-- Table structure for swo_serviceid_info
-- ----------------------------
DROP TABLE IF EXISTS `swo_serviceid_info`;
CREATE TABLE `swo_serviceid_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `serviceID_id` int(11) NOT NULL,
  `back_date` date NOT NULL COMMENT '回款日期',
  `back_money` decimal(11,3) NOT NULL COMMENT '回款金額',
  `put_month` int(11) NOT NULL COMMENT '實際發放月數',
  `out_month` int(11) NOT NULL COMMENT '剩餘發放月數',
  `luu` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='ID服務的回款記錄表';
