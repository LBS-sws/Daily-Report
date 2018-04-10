DROP TABLE IF EXISTS swo_qc_info;
CREATE TABLE swo_qc_info(
	qc_id int unsigned not null,
	field_id varchar(50) not null,
	field_value varchar(2000),
	field_blob longblob,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	UNIQUE (qc_id, field_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
