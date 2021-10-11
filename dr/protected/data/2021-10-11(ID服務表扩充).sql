
-- ----------------------------
-- Table structure for swo_serviceid_info
-- ----------------------------
ALTER TABLE swo_serviceid_info ADD COLUMN back_ratio int(3) NOT NULL DEFAULT 100 COMMENT '百分比' AFTER back_money;
