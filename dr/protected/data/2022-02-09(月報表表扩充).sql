
-- ----------------------------
-- Table structure for swo_monthly_hdr
-- ----------------------------
ALTER TABLE swo_monthly_hdr ADD COLUMN f74 float(10,2) NULL DEFAULT 0.00 COMMENT '销售部分数' AFTER month_no;
ALTER TABLE swo_monthly_hdr ADD COLUMN f86 float(10,2) NULL DEFAULT 0.00 COMMENT '外勤部分数' AFTER month_no;
ALTER TABLE swo_monthly_hdr ADD COLUMN f94 float(10,2) NULL DEFAULT 0.00 COMMENT '财务部分数' AFTER month_no;
ALTER TABLE swo_monthly_hdr ADD COLUMN f100 float(10,2) NULL DEFAULT 0.00 COMMENT '营运部分数' AFTER month_no;
ALTER TABLE swo_monthly_hdr ADD COLUMN f115 float(10,2) NULL DEFAULT 0.00 COMMENT '人事部分数' AFTER month_no;
ALTER TABLE swo_monthly_hdr ADD COLUMN f73 float(10,2) NULL DEFAULT 0.00 COMMENT '总分' AFTER month_no;
