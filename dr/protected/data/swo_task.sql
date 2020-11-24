/*
Navicat MySQL Data Transfer

Source Server         : ldb
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : swoperdev

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-11-24 16:23:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `swo_task`
-- ----------------------------
DROP TABLE IF EXISTS `swo_task`;
CREATE TABLE `swo_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `task_type` char(5) DEFAULT NULL,
  `sales_products` char(30) DEFAULT NULL,
  `city` char(5) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of swo_task
-- ----------------------------
INSERT INTO `swo_task` VALUES ('56', '381坐厕垫纸机 (LJ)', 'OTHER', 'purification', 'SH', 'MGTSH1', 'test', '2016-05-03 08:26:41', '2020-11-24 09:31:22');
INSERT INTO `swo_task` VALUES ('57', '392大卷厕纸机/白', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:26:48', '2017-03-10 08:58:41');
INSERT INTO `swo_task` VALUES ('58', '392大卷厕纸机/黑', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:27:00', '2017-03-10 08:59:01');
INSERT INTO `swo_task` VALUES ('59', '394长润机嘴(白)', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:27:58', '2017-03-10 08:59:12');
INSERT INTO `swo_task` VALUES ('60', '394长润机嘴(黑)', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:28:07', '2017-03-10 08:59:30');
INSERT INTO `swo_task` VALUES ('61', '394中抽式抹手纸机/黑', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:28:33', '2017-03-10 08:59:38');
INSERT INTO `swo_task` VALUES ('62', '394中抽式抹手纸机/白', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:28:47', '2017-03-10 08:59:48');
INSERT INTO `swo_task` VALUES ('63', '403小型M-FOLD抹手纸机/黑', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:29:21', '2017-03-10 08:59:58');
INSERT INTO `swo_task` VALUES ('64', '403小型M-FOLD抹手纸机/白', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:29:38', '2017-03-10 09:00:06');
INSERT INTO `swo_task` VALUES ('65', 'AC-20机器', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:31:16', '2017-03-10 09:00:31');
INSERT INTO `swo_task` VALUES ('66', 'AC-20灯管', 'OTHER', null, 'SH', 'MGTSH1', 'DorisC', '2016-05-03 08:31:25', '2017-03-10 08:01:37');
INSERT INTO `swo_task` VALUES ('67', 'Gojo泡沫皂液机', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:32:09', '2017-03-10 09:00:52');
INSERT INTO `swo_task` VALUES ('68', 'Gojo抗菌泡沫皂液', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:32:18', '2017-03-10 09:01:05');
INSERT INTO `swo_task` VALUES ('69', 'M9000/海洋清风', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:33:27', '2017-03-10 09:01:21');
INSERT INTO `swo_task` VALUES ('70', 'M9000/全新果实', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:34:02', '2017-03-10 09:01:32');
INSERT INTO `swo_task` VALUES ('71', 'M9000/巍巍群峰', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:34:16', '2017-03-10 09:02:06');
INSERT INTO `swo_task` VALUES ('72', 'M-FOLD木浆纸3001S', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:35:15', '2017-03-10 09:01:43');
INSERT INTO `swo_task` VALUES ('73', 'Purell消毒皂液Gel', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:35:25', '2017-03-10 09:02:15');
INSERT INTO `swo_task` VALUES ('75', 'Purell消毒皂液Gel机玻璃架', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:36:31', '2017-03-10 09:02:26');
INSERT INTO `swo_task` VALUES ('76', 'Purell消毒皂液Gel', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:36:43', '2017-03-10 09:02:38');
INSERT INTO `swo_task` VALUES ('77', 'ＴＣ机架（黑）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:42:31', '2017-03-10 09:02:50');
INSERT INTO `swo_task` VALUES ('78', 'ＴＣ机架（白）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:42:40', '2017-03-10 09:02:58');
INSERT INTO `swo_task` VALUES ('79', 'TC香精／夏日冰点', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:43:38', '2017-03-10 09:03:07');
INSERT INTO `swo_task` VALUES ('80', '单层抽取式餐巾纸2009-KC', 'PAPER', null, 'SH', 'MGTSH1', 'DorisC', '2016-05-03 08:44:21', '2017-03-10 07:57:48');
INSERT INTO `swo_task` VALUES ('81', '单层抽取式餐巾纸2009-XJ', 'PAPER', null, 'SH', 'MGTSH1', 'DorisC', '2016-05-03 08:44:44', '2017-03-10 08:00:48');
INSERT INTO `swo_task` VALUES ('82', '单抽式卫生纸1002AN', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 08:45:17', '2017-03-10 09:03:27');
INSERT INTO `swo_task` VALUES ('83', '中抽环保擦手纸1600A', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:01:34', '2017-03-10 09:03:39');
INSERT INTO `swo_task` VALUES ('85', '有压纹木浆大卷厕纸BJ2700 ', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:02:25', '2017-03-10 09:03:49');
INSERT INTO `swo_task` VALUES ('86', '水剂喷机', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:02:41', '2017-03-10 09:04:06');
INSERT INTO `swo_task` VALUES ('87', '无压纹木浆大卷厕纸BJL-KL ', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:02:58', '2017-03-10 09:04:23');
INSERT INTO `swo_task` VALUES ('89', '中抽木浆擦手纸X9001', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:03:22', '2017-03-10 09:04:39');
INSERT INTO `swo_task` VALUES ('90', '泡沫洗手液（粉红色）', 'SOAP', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:03:38', '2017-03-10 09:04:53');
INSERT INTO `swo_task` VALUES ('91', '普通洗手液（紫色）', 'SOAP', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:03:54', '2017-03-10 09:05:19');
INSERT INTO `swo_task` VALUES ('92', 'BJL-XJ3', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-03 09:14:05', '2017-03-10 09:05:34');
INSERT INTO `swo_task` VALUES ('106', '座厕板消毒剂', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-05 03:49:03', '2017-03-10 09:05:46');
INSERT INTO `swo_task` VALUES ('107', '座厕板消毒机', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-05 03:49:12', '2017-03-10 09:05:55');
INSERT INTO `swo_task` VALUES ('125', '泡沫皂液机（白）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-10 09:33:17', '2017-03-10 09:06:04');
INSERT INTO `swo_task` VALUES ('126', '泡沫皂液机（黑）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-10 09:33:31', '2017-03-10 09:06:15');
INSERT INTO `swo_task` VALUES ('155', '灭蝇灯（半圆形）', 'OTHER', null, 'SH', 'MGTSH1', 'DorisC', '2016-05-12 05:49:59', '2017-03-10 08:02:25');
INSERT INTO `swo_task` VALUES ('156', '座厕垫纸-XJ', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-12 05:50:33', '2017-03-10 09:06:42');
INSERT INTO `swo_task` VALUES ('157', '新定时喷机', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-12 09:55:48', '2017-03-10 09:06:32');
INSERT INTO `swo_task` VALUES ('158', '5号电池', 'OTHER', null, 'SH', 'MGTSH1', 'DorisC', '2016-05-12 09:56:03', '2017-03-10 08:02:42');
INSERT INTO `swo_task` VALUES ('159', '通用皂液机', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-13 10:28:10', '2017-03-10 09:07:00');
INSERT INTO `swo_task` VALUES ('160', '通用皂液机（泡沫机嘴）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-13 10:28:33', '2017-03-10 09:07:11');
INSERT INTO `swo_task` VALUES ('161', '通用皂液机（普通机嘴）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-13 10:28:55', '2017-03-10 09:07:21');
INSERT INTO `swo_task` VALUES ('167', '2009-KL', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-16 10:32:39', '2017-03-10 09:07:30');
INSERT INTO `swo_task` VALUES ('168', '水性喷剂（紫罗兰）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-17 02:47:57', '2017-03-10 09:07:40');
INSERT INTO `swo_task` VALUES ('174', '300CS', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-17 09:54:29', '2017-03-10 09:07:58');
INSERT INTO `swo_task` VALUES ('175', '水性喷剂（春天）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-17 09:55:05', '2017-03-10 09:08:12');
INSERT INTO `swo_task` VALUES ('176', '1号电池', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-17 09:55:20', '2017-03-10 09:08:23');
INSERT INTO `swo_task` VALUES ('185', 'BJLD=XJ1', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-23 09:47:43', '2017-03-10 09:08:34');
INSERT INTO `swo_task` VALUES ('186', 'BJL-KC', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-23 09:50:56', '2017-03-10 09:08:47');
INSERT INTO `swo_task` VALUES ('196', '12388-KC', 'PAPER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-05-31 10:21:20', '2017-03-10 09:08:56');
INSERT INTO `swo_task` VALUES ('202', '水性喷剂（绿茶）', 'OTHER', null, 'SH', 'MGTSH1', 'LOGSH', '2016-06-13 08:13:35', '2017-03-10 09:09:06');
INSERT INTO `swo_task` VALUES ('219', '维修', 'OTHER', null, 'FZ', 'ACFZ1', 'OPFZ1', '2016-07-19 01:37:15', '2017-03-10 08:19:18');
INSERT INTO `swo_task` VALUES ('220', '装机', 'OTHER', null, 'FZ', 'ACFZ1', 'OPFZ1', '2016-07-19 01:37:26', '2017-03-10 08:19:27');
INSERT INTO `swo_task` VALUES ('221', '拆机', 'OTHER', null, 'FZ', 'ACFZ1', 'OPFZ1', '2016-07-19 01:37:33', '2017-03-10 08:19:49');
INSERT INTO `swo_task` VALUES ('222', '送皂液', 'SOAP', null, 'FZ', 'ACFZ1', 'OPFZ1', '2016-07-19 01:37:41', '2017-03-10 08:20:03');
INSERT INTO `swo_task` VALUES ('223', '送纸', 'PAPER', null, 'FZ', 'ACFZ1', 'OPFZ1', '2016-07-19 01:37:50', '2017-03-10 08:20:11');
INSERT INTO `swo_task` VALUES ('235', '玫瑰皂液', 'SOAP', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:46:34', '2017-02-24 08:07:36');
INSERT INTO `swo_task` VALUES ('236', '花香皂液', 'SOAP', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:46:43', '2017-02-24 08:07:46');
INSERT INTO `swo_task` VALUES ('237', 'BJ2700木浆厕纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:46:57', '2017-02-24 08:07:57');
INSERT INTO `swo_task` VALUES ('238', '1000G厕纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:48:35', '2017-02-24 08:08:05');
INSERT INTO `swo_task` VALUES ('239', '3006抹手纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:51:09', '2017-02-24 08:08:14');
INSERT INTO `swo_task` VALUES ('240', '坐厕板消毒液', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:51:29', '2017-02-24 08:08:25');
INSERT INTO `swo_task` VALUES ('241', '3001抹手纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:54:57', '2017-02-24 08:08:33');
INSERT INTO `swo_task` VALUES ('242', '灭蝇灯', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:55:14', '2017-02-24 08:08:41');
INSERT INTO `swo_task` VALUES ('243', '灭蝇纸', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:55:21', '2017-02-24 08:08:50');
INSERT INTO `swo_task` VALUES ('244', '地拖巾', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:55:44', '2017-02-24 08:10:30');
INSERT INTO `swo_task` VALUES ('245', '洗地易', 'SOAP', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:55:50', '2017-02-24 08:12:12');
INSERT INTO `swo_task` VALUES ('246', '蚊滋水', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:56:08', '2017-02-24 08:12:22');
INSERT INTO `swo_task` VALUES ('247', '地拖棍', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:56:18', '2017-02-24 08:14:57');
INSERT INTO `swo_task` VALUES ('248', '清洁用品', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:56:26', '2017-02-24 08:15:05');
INSERT INTO `swo_task` VALUES ('249', '坐垫纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:56:49', '2017-02-24 08:07:29');
INSERT INTO `swo_task` VALUES ('250', '1002G擦手纸', 'PAPER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:57:09', '2017-02-24 08:15:15');
INSERT INTO `swo_task` VALUES ('251', '皂液机（黑色）', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:57:41', '2017-02-24 08:15:24');
INSERT INTO `swo_task` VALUES ('252', 'SS-455手部消毒液', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:58:20', '2017-02-24 08:15:31');
INSERT INTO `swo_task` VALUES ('253', 'N3240D-030垃圾袋', 'OTHER', null, 'FS', 'OPLOGFS1', 'OPLOGFS1', '2016-09-14 02:58:47', '2017-02-24 08:15:39');
INSERT INTO `swo_task` VALUES ('280', '酵素尿刚垫', 'OTHER', null, 'SH', 'OPLOGSH1', 'LOGSH', '2016-11-14 08:22:26', '2017-03-10 09:09:25');
INSERT INTO `swo_task` VALUES ('281', '58550-KC', 'PAPER', null, 'SH', 'OPLOGSH1', 'LOGSH', '2016-11-14 08:22:39', '2017-03-10 09:09:16');
INSERT INTO `swo_task` VALUES ('282', '泡沫皂液1加侖', 'PAPER', null, 'SZ', 'LOGSZ', 'LOGSZ', '2017-06-15 07:59:15', '2017-06-15 07:59:15');
INSERT INTO `swo_task` VALUES ('283', '泡沫皂液機（白）一次性維護費', 'OTHER', null, 'SZ', 'LOGSZ', 'LOGSZ', '2017-06-15 10:05:48', '2017-06-15 10:05:48');
INSERT INTO `swo_task` VALUES ('284', '送皂液', 'SOAP', null, 'TJ', 'OPTJ1', 'OPTJ1', '2017-06-17 04:59:46', '2017-06-17 04:59:46');
INSERT INTO `swo_task` VALUES ('285', '维修', 'OTHER', null, 'TJ', 'OPTJ1', 'OPTJ1', '2017-06-17 04:59:59', '2017-06-17 04:59:59');
INSERT INTO `swo_task` VALUES ('286', '2700B', 'PAPER', null, 'BJ', 'OPBJ1', 'OPBJ1', '2017-06-17 06:53:10', '2017-06-17 06:53:10');
INSERT INTO `swo_task` VALUES ('287', '送皂液', 'SOAP', null, 'BJ', 'VivienneChen', 'VivienneChen', '2017-07-06 01:44:19', '2017-07-06 01:44:19');
INSERT INTO `swo_task` VALUES ('288', '1002BI厕纸', 'PAPER', null, 'SZ', 'leo.sz', 'leo.sz', '2019-04-17 09:48:53', '2019-04-17 09:48:53');
