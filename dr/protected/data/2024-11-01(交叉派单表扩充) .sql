
-- ----------------------------
-- Table structure for swo_cross
-- ----------------------------
ALTER TABLE swo_cross ADD COLUMN send_city varchar(100) NULL DEFAULT NULL COMMENT '通知城市' AFTER audit_date;
