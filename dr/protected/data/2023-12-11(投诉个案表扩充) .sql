
-- ----------------------------
-- Table structure for swo_followup
-- ----------------------------
ALTER TABLE swo_followup ADD COLUMN pest_type_id varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '虫害类型id（逗号分割）' AFTER content;
ALTER TABLE swo_followup ADD COLUMN pest_type_name varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '虫害类型名称' AFTER content;
ALTER TABLE swo_followup ADD COLUMN cust_sfn int(10) NULL DEFAULT NULL COMMENT '满意度' AFTER content;
ALTER TABLE swo_followup ADD COLUMN cust_vfn int(10) NULL DEFAULT NULL COMMENT '满意度评分' AFTER content;

-- ----------------------------
-- Table structure for swo_pest_type
-- ----------------------------
CREATE TABLE `swo_pest_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pest_name` varchar(250) NOT NULL,
  `z_index` int(10) NOT NULL DEFAULT '0' COMMENT '层级',
  `display_num` int(1) NOT NULL DEFAULT '1' COMMENT '是否显示 0：不显示 1：显示',
  `city` char(5) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='投诉个案的虫害类型设置';