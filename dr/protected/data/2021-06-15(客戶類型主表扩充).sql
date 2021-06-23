
-- ----------------------------
-- Table structure for swo_customer_type
-- ----------------------------
ALTER TABLE swo_customer_type ADD COLUMN single  int(2) NULL DEFAULT 0 COMMENT '是否是一次性服务 0：非一次性  1：一次性' AFTER rpt_cat;
