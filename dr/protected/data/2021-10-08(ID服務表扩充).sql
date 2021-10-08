
-- ----------------------------
-- Table structure for swo_serviceid_info
-- ----------------------------
ALTER TABLE swo_serviceid_info ADD COLUMN rate_num decimal(11,3) NOT NULL DEFAULT 0.000 COMMENT '提成比例' AFTER out_month;
ALTER TABLE swo_serviceid_info ADD COLUMN commission int(11) NOT NULL DEFAULT 0 COMMENT '是否已經計算 1：已計算' AFTER out_month;
ALTER TABLE swo_serviceid_info ADD COLUMN comm_money decimal(11,3) NULL DEFAULT 0.000 COMMENT '實際計算金額' AFTER out_month;
