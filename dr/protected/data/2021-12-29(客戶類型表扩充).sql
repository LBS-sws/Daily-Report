
-- ----------------------------
-- Table structure for swo_customer_type_twoname
-- ----------------------------
ALTER TABLE swo_customer_type_twoname ADD COLUMN bring  int(2) NULL DEFAULT 0 COMMENT '是否計算創新獎勵點 0：不計算 1：計算' AFTER toplimit;
