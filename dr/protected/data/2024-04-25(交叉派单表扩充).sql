
-- ----------------------------
-- Table structure for swo_cross
-- ----------------------------
ALTER TABLE swo_cross ADD COLUMN old_month_amt decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提交时服务单金额' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN cross_type int(2) NOT NULL DEFAULT 4 COMMENT '4:长约 3：短约 2：资质借用' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN u_update_date datetime NULL DEFAULT NULL COMMENT 'u系统确定时间' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN u_update_user varchar(100) NULL DEFAULT NULL COMMENT 'u系统确定人' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN cross_id_list varchar(200) NULL DEFAULT NULL COMMENT '交叉派单影响了哪些客户服务（不包含申请id）' AFTER audit_date;


ALTER TABLE swo_cross ADD COLUMN cross_amt decimal(10,2) NULL DEFAULT '0.00' COMMENT '承接后金额' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN qualification_ratio decimal(5,2) NULL DEFAULT '0.00' COMMENT '资质方占比（0~100）' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN qualification_city varchar(50) NULL DEFAULT '0.00' COMMENT '资质方城市' AFTER audit_date;
ALTER TABLE swo_cross ADD COLUMN qualification_amt decimal(10,2) NULL DEFAULT '0.00' COMMENT '资质方金额' AFTER audit_date;
