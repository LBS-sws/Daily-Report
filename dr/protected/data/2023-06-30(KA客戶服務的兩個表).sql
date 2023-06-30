/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-06-30 11:21:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for swo_service_ka
-- ----------------------------
DROP TABLE IF EXISTS `swo_service_ka`;
CREATE TABLE `swo_service_ka` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_no` varchar(255) DEFAULT NULL COMMENT '服務編號',
  `service_new_id` int(11) DEFAULT '0' COMMENT '服務新增的id',
  `company_id` int(10) unsigned DEFAULT NULL COMMENT '公司id',
  `company_name` varchar(1000) NOT NULL COMMENT '公司名稱',
  `nature_type` int(10) unsigned DEFAULT NULL COMMENT '性质id',
  `nature_type_two` int(10) DEFAULT NULL COMMENT '性質二級欄位',
  `cust_type` int(10) unsigned DEFAULT NULL COMMENT '客户类别id（一級欄位）',
  `product_id` int(10) unsigned DEFAULT NULL COMMENT '服务內容id',
  `b4_product_id` int(10) unsigned DEFAULT NULL COMMENT '服务內容(更改前)id',
  `b4_service` varchar(1000) DEFAULT NULL COMMENT '服务內容(更改前)-名稱',
  `b4_freq` varchar(100) DEFAULT NULL COMMENT '服务次數(更改前)',
  `b4_paid_type` char(1) DEFAULT 'M' COMMENT '服务金额類(更改前)',
  `b4_amt_paid` decimal(11,2) DEFAULT '0.00' COMMENT '服务金额(更改前)',
  `b4_cust_type_end` int(11) DEFAULT NULL COMMENT '机器型号(更改前)',
  `b4_pieces` int(11) DEFAULT NULL COMMENT '机器数量(更改前)',
  `b4_amt_money` decimal(11,3) DEFAULT NULL COMMENT ' 總的金額(更改前)',
  `service` varchar(1000) DEFAULT NULL COMMENT '服务內容名字',
  `freq` varchar(100) DEFAULT NULL COMMENT '服务次數',
  `paid_type` char(1) DEFAULT 'M' COMMENT '服务金额類型 M：月 Y：年',
  `amt_paid` decimal(11,2) DEFAULT '0.00' COMMENT '服务金额',
  `amt_install` decimal(11,2) DEFAULT '0.00' COMMENT '機器押金',
  `need_install` char(1) DEFAULT 'N' COMMENT '是否收取押金 Y：是',
  `technician` varchar(1000) DEFAULT NULL COMMENT '负责技术员（名字）',
  `technician_id` int(11) DEFAULT '0' COMMENT '负责技术员id',
  `othersalesman` varchar(1000) DEFAULT NULL COMMENT '被跨区业务员(名字）',
  `othersalesman_id` int(11) DEFAULT '0' COMMENT '被跨区业务员id',
  `salesman` varchar(1000) DEFAULT NULL COMMENT '业务员(名字)',
  `salesman_id` int(11) DEFAULT '0' COMMENT '业务员id',
  `sign_dt` datetime DEFAULT NULL COMMENT '签约日期',
  `ctrt_end_dt` datetime DEFAULT NULL COMMENT '合同终止日期',
  `surplus` smallint(6) DEFAULT '0' COMMENT '服務剩余次数',
  `all_number_edit0` smallint(6) DEFAULT '0' COMMENT '服務總次數（1）',
  `surplus_edit0` smallint(6) DEFAULT '0' COMMENT '服務剩余次数（1）',
  `all_number_edit1` smallint(6) DEFAULT '0' COMMENT '服務總次數（2）',
  `surplus_edit1` smallint(6) DEFAULT '0' COMMENT '服務剩余次数（2）',
  `all_number_edit2` smallint(6) DEFAULT '0' COMMENT '服務總次數（3）',
  `surplus_edit2` smallint(6) DEFAULT '0' COMMENT '服務剩余次数（3）',
  `all_number_edit3` smallint(6) DEFAULT '0' COMMENT '服務總次數（4）',
  `surplus_edit3` smallint(6) DEFAULT '0' COMMENT '服務剩余次数（4）',
  `all_number` smallint(6) DEFAULT '0' COMMENT '服務總次數',
  `all_number_edit` smallint(6) DEFAULT NULL COMMENT '不知道幹啥用的',
  `ctrt_period` smallint(6) DEFAULT NULL COMMENT '合同年限(月)',
  `cont_info` varchar(500) DEFAULT NULL COMMENT '客户联系/电话',
  `first_dt` datetime DEFAULT NULL COMMENT '服務日期',
  `first_tech` varchar(1000) DEFAULT NULL COMMENT '首次技术员（名字）',
  `first_tech_id` int(11) DEFAULT '0' COMMENT '首次技术员id',
  `pieces` int(11) DEFAULT NULL COMMENT '机器数量',
  `cust_type_name` int(11) DEFAULT NULL COMMENT '客戶類型（二級欄位）',
  `cust_type_end` int(11) DEFAULT NULL COMMENT '机器型号（最后的栏位）',
  `pay_week` int(11) DEFAULT NULL COMMENT '付款週期',
  `amt_money` decimal(11,3) DEFAULT NULL COMMENT ' 總的金額',
  `cust_type_four` int(10) DEFAULT '0' COMMENT '客戶類型（四級欄位）',
  `cust_type_three` int(10) DEFAULT '0' COMMENT '客戶類型（三級欄位）',
  `reason` varchar(1000) DEFAULT NULL COMMENT '变动原因（不知道幹啥的）',
  `target` int(1) unsigned zerofill DEFAULT '0' COMMENT '是否放入獎金池（0：否 1：是）',
  `other_commission` char(100) DEFAULT NULL COMMENT '不知道幹啥的',
  `commission` char(100) DEFAULT NULL COMMENT '新增是金额，扣钱是提成',
  `royaltys` decimal(4,3) DEFAULT '0.000' COMMENT '不知道幹啥的',
  `royalty` decimal(6,3) DEFAULT '0.000' COMMENT '該訂單的提成點數',
  `status` char(1) DEFAULT 'N' COMMENT '狀態 N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止',
  `status_copy` int(1) DEFAULT '0' COMMENT '不知道幹啥的',
  `status_dt` datetime DEFAULT NULL COMMENT '訂單的起始日期',
  `remarks` varchar(2000) DEFAULT NULL COMMENT '跨区明细',
  `equip_install_dt` datetime DEFAULT NULL COMMENT '機器安裝日期',
  `org_equip_qty` smallint(5) unsigned DEFAULT NULL COMMENT '原机器数量',
  `rtn_equip_qty` smallint(5) unsigned DEFAULT NULL COMMENT '拆回数量',
  `remarks2` varchar(1000) DEFAULT NULL COMMENT '訂單备注',
  `city` char(5) NOT NULL COMMENT '訂單所在城市code',
  `prepay_month` smallint(6) DEFAULT '0' COMMENT '预付月数',
  `prepay_start` smallint(6) DEFAULT '0' COMMENT '预付起始月',
  `send` varchar(255) DEFAULT 'N' COMMENT '是否已經發送郵件 Y：是',
  `wage_type` int(1) DEFAULT '0' COMMENT '是否參與工資計算 0：不參與 1：參與',
  `change_money` decimal(11,3) DEFAULT NULL COMMENT '變更後的金額',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service_1` (`company_name`(100)),
  KEY `idx_service_2` (`city`,`status_dt`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for swo_service_ka_no
-- ----------------------------
DROP TABLE IF EXISTS `swo_service_ka_no`;
CREATE TABLE `swo_service_ka_no` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  `status_dt` datetime NOT NULL,
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_service_contract_no_02` (`service_id`),
  KEY `idx_service_contract_no_01` (`contract_no`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
