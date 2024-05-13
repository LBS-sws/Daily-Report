
-- ----------------------------
-- Table structure for swo_service
-- ----------------------------
ALTER TABLE swo_service ADD COLUMN u_system_id int(11) NULL DEFAULT NULL COMMENT 'U系统对应id' AFTER office_id;

-- ----------------------------
-- Table structure for swo_service_ka
-- ----------------------------
ALTER TABLE swo_service_ka ADD COLUMN u_system_id int(11) NULL DEFAULT NULL COMMENT 'U系统对应id' AFTER office_id;

-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
ALTER TABLE swo_serviceid ADD COLUMN u_system_id int(11) NULL DEFAULT NULL COMMENT 'U系统对应id' AFTER office_id;
