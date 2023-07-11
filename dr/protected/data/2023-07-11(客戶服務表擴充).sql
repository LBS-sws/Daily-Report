
-- ----------------------------
-- Table structure for swo_service
-- ----------------------------
ALTER TABLE swo_service ADD COLUMN tracking varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跟蹤因素' AFTER reason;

-- ----------------------------
-- Table structure for swo_service_ka
-- ----------------------------
ALTER TABLE swo_service_ka ADD COLUMN tracking varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跟蹤因素' AFTER reason;

-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
ALTER TABLE swo_serviceid ADD COLUMN tracking varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跟蹤因素' AFTER reason;
