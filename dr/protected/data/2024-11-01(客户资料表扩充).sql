
-- ----------------------------
-- Table structure for swo_company
-- ----------------------------
ALTER TABLE swo_company ADD COLUMN u_customer_id int(11) NULL DEFAULT NULL COMMENT '派单系统id' AFTER status;
ALTER TABLE swo_company ADD COLUMN jd_customer_id varchar(100) NULL DEFAULT NULL COMMENT '金蝶系统id' AFTER status;
ALTER TABLE swo_company ADD COLUMN del_num int(1) not NULL DEFAULT 0 COMMENT '是否删除：0：否 1：是' AFTER status;

update swo_company set del_num=1 where id>0;