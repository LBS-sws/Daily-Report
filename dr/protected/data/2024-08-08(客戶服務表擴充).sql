
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

ALTER TABLE swo_service ADD COLUMN is_intersect int(2) not NULL DEFAULT 0 COMMENT '是否交叉' AFTER reason;
ALTER TABLE swo_service_ka ADD COLUMN is_intersect int(2) not NULL DEFAULT 0 COMMENT '是否交叉' AFTER reason;
ALTER TABLE swo_serviceid ADD COLUMN is_intersect int(2) not NULL DEFAULT 0 COMMENT '是否交叉' AFTER reason;

ALTER TABLE swo_service ADD COLUMN surplus_amt double(10,2) NULL DEFAULT 0 COMMENT '剩余金额' AFTER surplus;
ALTER TABLE swo_service_ka ADD COLUMN surplus_amt double(10,2) NULL DEFAULT 0 COMMENT '剩余金额' AFTER surplus;
ALTER TABLE swo_serviceid ADD COLUMN surplus_amt double(10,2) NULL DEFAULT 0 COMMENT '剩余金额' AFTER surplus;

update swo_service set
 surplus_amt= 
if(all_number is null or all_number=0,0,
	(
		IF(paid_type='M',IFNULL(amt_paid,0)*IFNULL(ctrt_period,0),IFNULL(amt_paid,0))
		/all_number
	)
)*IFNULL(surplus,0)
 where status='T';
 
update swo_service_ka set
 surplus_amt= 
if(all_number is null or all_number=0,0,
	(
		IF(paid_type='M',IFNULL(amt_paid,0)*IFNULL(ctrt_period,0),IFNULL(amt_paid,0))
		/all_number
	)
)*IFNULL(surplus,0)
 where status='T';
