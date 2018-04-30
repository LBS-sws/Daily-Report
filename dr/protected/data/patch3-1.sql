DROP TABLE IF EXISTS swo_email_queue;
CREATE TABLE swo_email_queue (
	id int unsigned auto_increment NOT NULL primary key,
	request_dt datetime NOT NULL,
	from_addr varchar(255) NOT NULL,
	to_addr varchar(1000) NOT NULL,
	cc_addr varchar(1000),
	subject varchar(1000),
	description varchar(1000),
	message varchar(5000),
	status char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table swo_city
add column incharge varchar(30) after region;