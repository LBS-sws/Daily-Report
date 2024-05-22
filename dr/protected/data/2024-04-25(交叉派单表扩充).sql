
-- ----------------------------
-- Table structure for swo_cross
-- ----------------------------
ALTER TABLE swo_cross ADD COLUMN old_month_amt decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提交时服务单金额' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN cross_type int(2) NOT NULL DEFAULT 0 COMMENT '0:长约 1：短约 2：暂代' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN u_update_date datetime NULL DEFAULT NULL COMMENT 'u系统确定时间' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN u_update_user varchar(100) NULL DEFAULT NULL COMMENT 'u系统确定人' AFTER audit_date;
