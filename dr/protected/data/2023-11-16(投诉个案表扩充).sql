
-- ----------------------------
-- Table structure for swo_followup
-- ----------------------------
ALTER TABLE swo_followup ADD COLUMN job_report text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '处理结果' AFTER content;
