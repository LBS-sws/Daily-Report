-- ----------------------------
-- Table structure for acc_send_set_jd
-- ----------------------------
DROP TABLE IF EXISTS `swo_send_set_jd`;
CREATE TABLE `swo_send_set_jd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` varchar(255) NOT NULL,
  `set_type` varchar(255) NOT NULL DEFAULT 'warehouse',
  `field_id` varchar(255) NOT NULL,
  `field_value` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT 'text',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` datetime DEFAULT NULL,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='金蝶关联的配置表';

alter table swo_supplier add local_bool int(1) NOT NULL DEFAULT 1 COMMENT '是否本地物料 0:否 1：是' after city;