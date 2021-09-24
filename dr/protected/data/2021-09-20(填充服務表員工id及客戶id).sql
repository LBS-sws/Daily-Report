
-- ----------------------------
-- 擴充swo_customer_type_twoname表
-- ----------------------------
ALTER TABLE swo_customer_type_twoname ADD COLUMN index_num int(2) NOT NULL DEFAULT 2 COMMENT '几级栏位' AFTER single;

-- ----------------------------
-- 擴充swo_service表
-- ----------------------------
ALTER TABLE swo_service ADD COLUMN service_new_id int(11) DEFAULT '0' COMMENT '服務新增的id' AFTER id;
ALTER TABLE swo_service ADD COLUMN service_no varchar(255) DEFAULT NULL COMMENT '服務編號' AFTER id;
ALTER TABLE swo_service ADD COLUMN cust_type_three int(10) DEFAULT '0' COMMENT '客戶類型（三級欄位）' AFTER cust_type_name;
ALTER TABLE swo_service ADD COLUMN cust_type_four int(10) DEFAULT '0' COMMENT '客戶類型（四級欄位）' AFTER cust_type_name;
ALTER TABLE swo_service ADD COLUMN cust_type_end int(11) DEFAULT NULL COMMENT '机器型号（最后的栏位）' AFTER cust_type_name;
ALTER TABLE swo_service ADD COLUMN pay_week int(11) DEFAULT NULL COMMENT '付款週期' AFTER cust_type_end;
ALTER TABLE swo_service ADD COLUMN b4_amt_money decimal(11,3) DEFAULT NULL COMMENT ' 總的金額(更改前)' AFTER b4_amt_paid;
ALTER TABLE swo_service ADD COLUMN b4_pieces int(11) DEFAULT NULL COMMENT '机器数量(更改前)' AFTER b4_amt_paid;
ALTER TABLE swo_service ADD COLUMN b4_cust_type_end int(11) DEFAULT NULL COMMENT '机器型号(更改前)' AFTER b4_amt_paid;
ALTER TABLE swo_service ADD COLUMN amt_money decimal(11,3) DEFAULT NULL COMMENT ' 總的金額' AFTER pay_week;
ALTER TABLE swo_service ADD COLUMN technician_id int(11) DEFAULT '0' COMMENT '负责技术员id' AFTER technician;
ALTER TABLE swo_service ADD COLUMN othersalesman_id int(11) DEFAULT '0' COMMENT '被跨区业务员id' AFTER othersalesman;
ALTER TABLE swo_service ADD COLUMN salesman_id int(11) DEFAULT '0' COMMENT '业务员id' AFTER salesman;
ALTER TABLE swo_service ADD COLUMN first_tech_id int(11) DEFAULT '0' COMMENT '首次技术员id' AFTER first_tech;
ALTER TABLE swo_service ADD COLUMN change_money decimal(11,3) DEFAULT NULL COMMENT '變更後的金額' AFTER send;
ALTER TABLE swo_service ADD COLUMN wage_type int(1) DEFAULT '0' COMMENT '是否參與工資計算 0：不參與 1：參與' AFTER send;

-- ----------------------------
-- 填充客戶id
-- ----------------------------
UPDATE swo_service a SET
a.company_id = (
	SELECT b.id FROM swo_company b WHERE a.company_name = CONCAT(b.code,b.name) LIMIT 1
);

-- ----------------------------
-- 填充技术员id
-- ----------------------------
UPDATE swo_service a SET
a.technician_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE a.technician=CONCAT(b.name,' (',b.code,')') LIMIT 1
);

-- ----------------------------
-- 填充被跨区业务员id
-- ----------------------------
UPDATE swo_service a SET
a.othersalesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE a.othersalesman=CONCAT(b.name,' (',b.code,')') LIMIT 1
);

-- ----------------------------
-- 填充业务员id
-- ----------------------------
UPDATE swo_service a SET
a.salesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE a.salesman=CONCAT(b.name,' (',b.code,')') LIMIT 1
);

-- ----------------------------
-- 填充首次技术员id
-- ----------------------------
UPDATE swo_service a SET
a.first_tech_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE a.first_tech=CONCAT(b.name,' (',b.code,')') LIMIT 1
);

-- ----------------------------
-- 填充服務編號
-- ----------------------------
UPDATE swo_service a SET a.service_no = CONCAT('MS',right(1000000+a.id,6));

-- ----------------------------
-- 填充服務新增的id
-- ----------------------------
UPDATE swo_service a
SET a.service_new_id = (
	SELECT b.id FROM
		(SELECT id,company_name,cust_type,cust_type_name,sign_dt FROM swo_service WHERE status='N') b
	WHERE
		b.company_name = a.company_name
	AND b.cust_type = a.cust_type
	AND b.cust_type_name = a.cust_type_name
	AND b.sign_dt <= a.sign_dt
	LIMIT 1
)
WHERE a.status != 'N';


-- ----------------------------------
-- 由於數據異常需要模糊查詢code然後填充
-- ----------------------------------
-- ----------------------------
-- 填充客戶id
-- ----------------------------
UPDATE swo_service a SET
a.company_id = (
 SELECT b.id FROM swo_company b WHERE trim(a.company_name) LIKE concat(trim(b.code),'%') LIMIT 1
)
WHERE a.company_id=0 OR a.company_id is NULL;

-- ----------------------------
-- 填充业务员id
-- ----------------------------
UPDATE swo_service a SET
a.salesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE trim(a.salesman) LIKE CONCAT('%(',trim(b.code),')%') LIMIT 1
)
WHERE a.salesman_id=0 OR a.salesman_id is NULL;

-- ----------------------------
-- 填充被跨区业务员id
-- ----------------------------
UPDATE swo_service a SET
a.othersalesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE trim(a.othersalesman) LIKE CONCAT('%(',trim(b.code),')%') LIMIT 1
)
WHERE a.othersalesman_id=0 OR a.othersalesman_id is NULL;



-- ----------------------------------
-- 由於數據異常需要模糊查詢name然後填充
-- ----------------------------------
-- ----------------------------
-- 填充客戶id
-- ----------------------------
UPDATE swo_service a SET
a.company_id = (
 SELECT b.id FROM swo_company b WHERE trim(a.company_name) LIKE concat('%',trim(b.name),'%') LIMIT 1
)
WHERE a.company_id=0 OR a.company_id is NULL;

-- ----------------------------
-- 填充业务员id
-- ----------------------------
UPDATE swo_service a SET
a.salesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE trim(a.salesman) LIKE CONCAT('%',trim(b.name),'%') LIMIT 1
)
WHERE a.salesman_id=0 OR a.salesman_id is NULL;

-- ----------------------------
-- 填充被跨区业务员id
-- ----------------------------
UPDATE swo_service a SET
a.othersalesman_id = (
	SELECT b.id FROM hrdev.hr_employee b WHERE trim(a.othersalesman) LIKE CONCAT('%',trim(b.name),'%') LIMIT 1
)
WHERE a.othersalesman_id=0 OR a.othersalesman_id is NULL;
