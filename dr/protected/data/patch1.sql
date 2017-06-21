alter table swo_company
add column address varchar(1000) after cont_phone;

DROP TABLE IF EXISTS swo_service_type;
CREATE TABLE swo_service_type(
	id int unsigned NOT NULL auto_increment primary key,
	description varchar(100),
	rpt_cat char(10),
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table swo_qc
add column service_type varchar(100) after company_name;

alter table swo_logistic
add column address varchar(1000) after company_name,
add column repair varchar(1000) after reason,
add column remarks varchar(1000) after repair;

alter table swo_staff
add column staff_type varchar(15) after position,
add column leader varchar(15) after staff_type;
