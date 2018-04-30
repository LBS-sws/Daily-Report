DELIMITER //
DROP FUNCTION IF EXISTS CustomerStatus //
CREATE FUNCTION CustomerStatus(p_id int unsigned, p_code varchar(20), p_name varchar(1000), p_city char(5)) RETURNS char(1)
BEGIN
DECLARE done int default false;
DECLARE cnt_all varchar(5000);
DECLARE cnt_term varchar(255);
DECLARE status char(1);

DECLARE cur1 CURSOR FOR
SELECT count(a.id), sum(case when a.status<>'T' then 1 else 0 end) 
FROM swo_service a
LEFT OUTER JOIN swo_service b ON a.company_name=b.company_name AND a.status_dt < b.status_dt AND a.cust_type=b.cust_type
WHERE b.id IS NULL AND a.city=p_city
AND (a.company_id=p_id OR a.company_name like concat(p_code,'%') OR a.company_name like concat('%',p_name));
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = true;

OPEN cur1;
read_loop: LOOP
FETCH cur1 INTO cnt_all, cnt_term;
IF done THEN
LEAVE read_loop;
END IF;
SET status = IF(cnt_all=0, 'U', IF(cnt_term > 0, 'A', 'T'));
END LOOP;
CLOSE cur1;
RETURN status;

END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS CustomerStatus //
CREATE FUNCTION CustomerStatus(p_id int unsigned, p_code varchar(20), p_name varchar(1000), p_city char(5)) RETURNS char(1)
BEGIN
DECLARE status char(1);
SET status = (
SELECT case when count(a.id)=0 then 'U'
       when sum(case when a.status<>'T' then 1 else 0 end) > 0 then 'A'
       else 'T'
       end	   
FROM swo_service a
LEFT OUTER JOIN swo_service b ON a.company_name=b.company_name AND a.status_dt < b.status_dt AND a.cust_type=b.cust_type
WHERE b.id IS NULL AND a.city=p_city
AND (a.company_id=p_id OR a.company_name like concat(p_code,'%') OR a.company_name like concat('%',p_name))
);
RETURN status;

END //
DELIMITER ;

	DECLARE balance decimal(11,2);

 create index idx_service_1 on swo_service(company_name(100));
 create index idx_service_2 on swo_service(city, status_dt);
DROP TABLE IF EXISTS swo_company_status;
CREATE TABLE swo_company_status (
	id int unsigned NOT NULL auto_increment primary key,
	status char(1) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into swo_company_status(id, status) select a.id, CustomerStatus(a.id, a.code, a.name, a.city) from swo_company a on duplicate key update status=CustomerStatus(a.id, a.code, a.name, a.city);
