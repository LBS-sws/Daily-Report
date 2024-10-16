
-- ----------------------------
-- Table structure for swo_cross
-- ----------------------------
ALTER TABLE swo_cross ADD COLUMN effective_date date NULL DEFAULT NULL COMMENT '生效日期' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN apply_category int(2) NULL DEFAULT NULL COMMENT '申请类型' AFTER audit_date;
