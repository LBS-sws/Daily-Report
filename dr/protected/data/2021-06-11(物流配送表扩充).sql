
-- ----------------------------
-- Table structure for swo_logistic_dtl
-- ----------------------------
ALTER TABLE swo_logistic_dtl ADD COLUMN commission int(1) NULL DEFAULT 2 COMMENT '判斷是不是參與銷售提成表 1：參加' AFTER city;
