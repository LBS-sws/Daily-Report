
-- ----------------------------
-- Table structure for swo_customer_type_twoname
-- ----------------------------
ALTER TABLE swo_customer_type_twoname ADD COLUMN single  int(2) NULL DEFAULT 0 COMMENT '是否是一次性服务 0：非一次性  1：一次性' AFTER toplimit;
