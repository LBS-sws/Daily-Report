
-- ----------------------------
-- Table structure for sal_cust_district
-- ----------------------------
ALTER TABLE swo_customer_type ADD COLUMN sales_rate int(1) NOT NULL DEFAULT 0 COMMENT '0：不參加  1：參加' AFTER rpt_cat;
ALTER TABLE swo_customer_type ADD COLUMN display int(1) NOT NULL DEFAULT 1 COMMENT '0：不顯示  1：顯示' AFTER rpt_cat;
ALTER TABLE swo_customer_type ADD COLUMN z_index int(11) NOT NULL DEFAULT 0 AFTER rpt_cat;
