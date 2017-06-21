alter table swo_user
add column email varchar(100) after disp_name;

alter table swo_city
add column region char(5) after name;

DROP TABLE IF EXISTS swo_mgr_feedback;
CREATE TABLE swo_mgr_feedback (
	id int unsigned auto_increment NOT NULL primary key,
	username varchar(30) NOT NULL,
	request_dt datetime NOT NULL,
	feedback_dt datetime,
	feedback_cat_list varchar(300),
	city char(5) NOT NULL,
	status char(1) default 'N',
	rpt_id int unsigned,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_mgr_feedback_rmk;
CREATE TABLE swo_mgr_feedback_rmk (
	id int unsigned auto_increment NOT NULL primary key,
	feedback_id int unsigned NOT NULL,
	feedback_cat char(5) NOT NULL,
	feedback varchar(5000),
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_queue_user;
CREATE TABLE swo_queue_user (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	username varchar(30) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

