
-- ----------------------------
-- Table structure for swo_service
-- ----------------------------
ALTER TABLE swo_service ADD COLUMN contract_type int(2) NULL DEFAULT NULL COMMENT '合约类型' AFTER reason;
update swo_service set contract_type=0 where id>0;

-- ----------------------------
-- Table structure for swo_service_ka
-- ----------------------------
ALTER TABLE swo_service_ka ADD COLUMN contract_type int(2) NULL DEFAULT NULL COMMENT '合约类型' AFTER reason;
update swo_service_ka set contract_type=1 where id>0;

-- ----------------------------
-- Table structure for swo_serviceid
-- ----------------------------
ALTER TABLE swo_serviceid ADD COLUMN contract_type int(2) NULL DEFAULT NULL COMMENT '合约类型' AFTER reason;
update swo_serviceid set contract_type=0 where id>0;
