CREATE DATABASE swoper_w CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON swoper_w.* TO 'swuser'@'localhost' IDENTIFIED BY 'swisher168';

use swoper_w;

DROP TABLE IF EXISTS swo_enquiry;
CREATE TABLE swo_enquiry(
	id int unsigned not null auto_increment primary key,
	contact_dt datetime not null,
	customer varchar(255) not null,
	type varchar(100) not null,
	contact varchar(255),
	tel_no varchar(100),
	source varchar(100),
	follow_staff varchar(500),
	follow_dt datetime,
	remarks varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_qc;
CREATE TABLE swo_qc(
	id int unsigned not null auto_increment primary key,
	entry_dt datetime not null,
	job_staff varchar(500),
	team varchar(100),
	month char(5),
	input_dt datetime,
	company_id int unsigned,
	company_name varchar(500),
	service_score varchar(100),
	cust_score varchar(100),
	cust_comment varchar(1000),
	qc_result varchar(100),
	env_grade char(1),
	qc_dt datetime,
	cust_sign varchar(100),
	qc_staff varchar(500),
	remarks varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_queue;
CREATE TABLE swo_queue (
	id int unsigned NOT NULL auto_increment primary key,
	rpt_desc varchar(250) NOT NULL,
	req_dt datetime,
	fin_dt datetime,
	username varchar(30) NOT NULL,
	status char(1) NOT NULL,
	rpt_type varchar(10) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	rpt_content longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_queue_param;
CREATE TABLE swo_queue_param (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	param_field varchar(50) NOT NULL,
	param_value varchar(500),
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_wservice;
CREATE TABLE swo_wservice (
	wsvc_key varchar(50) NOT NULL primary key,
	wsvc_desc varchar(100) NOT NULL,
	city char(5) NOT NULL,
	session_key varchar(50),
	session_time datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_service;
CREATE TABLE swo_service(
	id int unsigned NOT NULL auto_increment primary key,
	company_id int unsigned,
	company_name varchar(1000) NOT NULL,
	nature_type int unsigned,
	cust_type int unsigned,
	product_id int unsigned,
	b4_product_id int unsigned,
	b4_service varchar(1000),
	b4_freq varchar(100),
	b4_paid_type char(1) default 'M',
	b4_amt_paid decimal(11,2) default 0,
	service varchar(1000),
	freq varchar(100),
	paid_type char(1) default 'M',
	amt_paid decimal(11,2) default 0,
	amt_install decimal(11,2) default 0,
	need_install char(1) default 'N',
	salesman varchar(1000),
	sign_dt datetime,
	ctrt_end_dt datetime,
	ctrt_period tinyint default 0,
	cont_info varchar(500),
	first_dt datetime,
	first_tech varchar(1000),
	reason varchar(1000),
	status char(1) default 'N',
	status_dt datetime,
	remarks varchar(2000),
	equip_install_dt datetime,
	org_equip_qty smallint unsigned default 0,
	rtn_equip_qty smallint unsigned default 0,
	remarks2 varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_station_request;
CREATE TABLE swo_station_request (
	req_key varchar(50) NOT NULL primary key,
	email varchar(100) NOT NULL,
	station_name varchar(30) NOT NULL,
	city char(5) NOT NULL,
	station_id varchar(30) default NULL,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_station;
CREATE TABLE swo_station (
	station_id varchar(30) NOT NULL primary key,
	station_name varchar(30) NOT NULL,
	city char(5) NOT NULL,
	status char(1) default 'Y',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_login_log;
CREATE TABLE swo_login_log (
	station_id varchar(30) NOT NULL,
	username varchar(30) NOT NULL,
	client_ip varchar(20),
	login_time timestamp default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_user;
CREATE TABLE swo_user (
	username varchar(30) NOT NULL,
	password varchar(128) default NULL,
	disp_name varchar(100) default NULL,
	email varchar(100) default NULL,
	logon_time datetime default NULL,
	logoff_time datetime default NULL,
	status char(1) default NULL,
	group_id int unsigned NOT NULL,
	fail_count tinyint unsigned default 0,
	locked char(1) default 'N',
	session_key varchar(500),
	city char(5) NOT NULL default '',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	PRIMARY KEY  (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO swo_user(username, password, disp_name, logon_time, logoff_time, status, group_id, fail_count, locked, session_key, city, lcu, luu) 
	VALUES('admin','319153b210a3f6efde35e1486638f2cd','Administrator',null,null,'A',1,0,'N',null,'HK','admin','admin');

DROP TABLE IF EXISTS swo_group;
CREATE TABLE swo_group (
	group_id int unsigned auto_increment NOT NULL primary key,
	group_name varchar(255) NOT NULL,
	a_read_only varchar(255) default '',
	a_read_write varchar(255) default '',
	a_control varchar(255) default '',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO swo_group(group_id, group_name, a_read_only, a_read_write, a_control, lcu, luu) 
	VALUES(1,'Site Admin','','C01C02C05C06C07D01D02D03D04D05','','admin','admin');
INSERT INTO swo_group(group_id, group_name, a_read_only, a_read_write, a_control, lcu, luu) 
	VALUES(2,'Normal User','B01B02B03B04B05B06B07B08B09B10','A01A02A03A04A05A06A07C03C04','','admin','admin');
INSERT INTO swo_group(group_id, group_name, a_read_only, a_read_write, a_control, lcu, luu) 
	VALUES(3,'View Only User','A01A02A03A04A05A06A07C03C04B01B02B03B04B05B06B07B08B09B10','','','admin','admin');

DROP TABLE IF EXISTS swo_user_option;
CREATE TABLE swo_user_option (
	username varchar(30) NOT NULL,
	option_key varchar(30) NOT NULL,
	option_value varchar(255) default NULL,
	UNIQUE KEY idx_swo_user_option_1 (username,option_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_city;
CREATE TABLE swo_city(
	code char(5) not null primary key,
	name varchar(255) not null default '',
	region char(5),
	incharge varchar(30),
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) DEFAULT CHARSET=utf8;

INSERT INTO swo_city(code, name, lcu, luu) VALUES('HK','香港','admin','admin');
INSERT INTO swo_city(code, name, lcu, luu) VALUES('SH','上海','admin','admin');
INSERT INTO swo_city(code, name, lcu, luu) VALUES('SZ','深圳','admin','admin');

DROP TABLE IF EXISTS swo_staff;
CREATE TABLE swo_staff(
	id int unsigned NOT NULL auto_increment primary key,
	code varchar(10),
	name varchar(250) NOT NULL,
	position varchar(250),
	staff_type varchar(15),
	leader varchar(15),
	join_dt datetime,
	ctrt_start_dt datetime,
	ctrt_period tinyint default 0,
	ctrt_renew_dt datetime,
	email varchar(255),
	leave_dt datetime,
	leave_reason varchar(1000),
	remarks varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_company;
CREATE TABLE swo_company(
	id int unsigned NOT NULL auto_increment primary key,
	roster_id mediumint(9),
	code varchar(8),
	name varchar(1000) not null,
	cont_name varchar(100),
	cont_phone varchar(30),
	address varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_supplier;
CREATE TABLE swo_supplier(
	id int unsigned NOT NULL auto_increment primary key,
	code varchar(8),
	name varchar(1000) not null,
	cont_name varchar(100),
	cont_phone varchar(30),
	address varchar(1000),
	bank varchar(255),
	acct_no varchar(100),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_product;
CREATE TABLE swo_product(
	id int unsigned NOT NULL auto_increment primary key,
	code varchar(15) NOT NULL default '',
	description varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS swo_nature;
CREATE TABLE swo_nature(
	id int unsigned NOT NULL auto_increment primary key,
	description varchar(100),
	rpt_cat char(10),
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO swo_nature(id, description, rpt_cat, lcu, luu) VALUES(1, '餐飲', 'A01', 'admin', 'admin');
INSERT INTO swo_nature(id, description, rpt_cat, lcu, luu) VALUES(2, '非餐饮', 'B01', 'admin', 'admin');

DROP TABLE IF EXISTS swo_customer_type;
CREATE TABLE swo_customer_type(
	id int unsigned NOT NULL auto_increment primary key,
	description varchar(100),
	rpt_cat char(10),
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(1, 'IA 客户', 'IA-01', 'admin', 'admin');
INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(2, 'IB 客户', 'IB-01', 'admin', 'admin');
INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(3, 'BAF 客户', 'BAF-01', 'admin', 'admin');
INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(4, '飘盈香客户', 'NEW-01', 'admin', 'admin');
INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(5, '甲醛客戶', 'NEW-02', 'admin', 'admin');
INSERT INTO swo_customer_type(id, description, rpt_cat, lcu, luu) VALUES(6, 'INV 客户', 'NEW-03', 'admin', 'admin');

DROP TABLE IF EXISTS swo_location;
CREATE TABLE swo_location(
	id int unsigned NOT NULL auto_increment primary key,
	description varchar(100),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_task;
CREATE TABLE swo_task(
	id int unsigned NOT NULL auto_increment primary key,
	description varchar(100),
	task_type char(5),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_enquiry;
CREATE TABLE swo_enquiry(
	id int unsigned not null auto_increment primary key,
	contact_dt datetime not null,
	customer varchar(255) not null,
	nature_type int unsigned,
	type varchar(100) not null,
	contact varchar(255),
	tel_no varchar(100),
	address varchar(255),
	source_code char(5),
	source varchar(100),
	follow_staff varchar(500),
	follow_dt datetime,
	follow_result varchar(1000),
	remarks varchar(1000),
	record_by varchar(100),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_logistic;
CREATE TABLE swo_logistic(
	id int unsigned not null auto_increment primary key,
	log_dt datetime not null,
	seq tinyint unsigned,
	company_id int unsigned,
	company_name varchar(1000) NOT NULL,
	address varchar(1000),
	follow_staff varchar(1000),
	pay_method varchar(200),
	location int unsigned not null,
	location_dtl varchar(200),
	finish char(1) default 'N',
	deadline varchar(200),
	reason varchar(1000),
	repair varchar(1000),
	remarks varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_logistic_dtl;
CREATE TABLE swo_logistic_dtl(
	id int unsigned not null auto_increment primary key,
	log_id int unsigned not null,
	task int unsigned not null,
	qty int unsigned,
	finish char(1) default 'N',
	deadline datetime,
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_followup;
CREATE TABLE swo_followup(
	id int unsigned not null auto_increment primary key,
	entry_dt datetime not null,
	type varchar(50),
	company_id int unsigned not null,
	company_name varchar(1000) NOT NULL,
	content varchar(5000),
	cont_info varchar(500),
	resp_staff varchar(1000),
	resp_tech varchar(1000),
	mgr_notify char(1) default '',
	sch_dt datetime,
	follow_staff varchar(1000),
	leader char(1) default '',
	follow_tech varchar(1000),
	fin_dt datetime,
	follow_action varchar(1000),
	mgr_talk char(1) default '',
	changex varchar(1000),
	tech_notify varchar(1000),
	fp_fin_dt datetime,
	fp_call_dt datetime,
	fp_cust_name varchar(100),
	fp_comment varchar(1000),
	svc_next_dt datetime,
	svc_call_dt datetime,
	svc_cust_name varchar(100),
	svc_comment varchar(1000),
	mcard_remarks varchar(1000),
	mcard_staff varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS swo_email_queue;
CREATE TABLE swo_email_queue (
	id int unsigned auto_increment NOT NULL primary key,
	request_dt datetime NOT NULL,
	from_addr varchar(255) NOT NULL,
	to_addr varchar(1000) NOT NULL,
	cc_addr varchar(1000),
	subject varchar(1000),
	description varchar(1000),
	message text,
	status char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_email_queue_attm;
CREATE TABLE swo_email_queue_attm (
	queue_id int unsigned NOT NULL,
	name varchar(255) NOT NULL,
	content longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_monthly_hdr;
CREATE TABLE swo_monthly_hdr (
	id int unsigned auto_increment NOT NULL primary key,
	city char(5) NOT NULL,
	year_no smallint unsigned NOT NULL,
	month_no tinyint unsigned NOT NULL,
	status char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_monthly_dtl;
CREATE TABLE swo_monthly_dtl (
	id int unsigned auto_increment NOT NULL primary key,
	hdr_id int unsigned NOT NULL,
	data_field char(5) NOT NULL,
	data_value varchar(100),
	manual_input char(1) default 'N',
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS swo_monthly_field;
CREATE TABLE swo_monthly_field (
	code char(5) NOT NULL primary key,
	name varchar(255) NOT NULL,
	upd_type char(1) NOT NULL default 'M',
	field_type char(1) NOT NULL default 'N',
	status char(1) default 'Y',
	function_name varchar(200),
	excel_row smallint unsigned,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into swo_monthly_field(code, name, upd_type, field_type, lcu, luu, function_name, excel_row) values
('00001','上月生意额','Y','N','admin','admin','CalcService::getLastMonthFigure,00002',3),
('00002','今月生意额','M','N','admin','admin',null,4),
('00003','今月IA生意额','M','N','admin','admin',null,5),
('00004','今月IB生意额','M','N','admin','admin',null,6),
('00005','上月新（IA，IB）服务年生意额','Y','N','admin','admin','CalcService::getLastMonthFigure,00006',7),
('00006','今月新（IA，IB）服务年生意额','Y','N','admin','admin','CalcService::sumAmountIAIB',8),
('00007','去年今月新（IA，IB）服务年生意额','M','N','admin','admin',null,9),
('00008','上月新业务年生意额','Y','N','admin','admin','CalcService::getLastMonthFigure,00009',10),
('00009','今月新业务年生意额','Y','N','admin','admin','CalcService::sumAmountNEW',11),
('00010','去年今月新业务年生意额','M','N','admin','admin',null,12),
('00011','今月餐饮年生意额','Y','N','admin','admin','CalcService::sumAmountRestaurant',13),
('00012','今月非餐饮年生意额','Y','N','admin','admin','CalcService::sumAmountNonRestaurant',14),
('00013','上月生意净增长 （年生意额）','Y','N','admin','admin','CalcService::getLastMonthFigure,00014',15),
('00014','今月生意净增长 （年生意额','Y','N','admin','admin','CalcService::sumAmountNetGrowth',16),
('00015','去年今月生意额净增长 （年生意额）','M','N','admin','admin',null,17),
('00016','今月服务金额','M','N','admin','admin',null,18),
('00017','今月停单月生意额','Y','N','admin','admin','CalcService::sumAmountTerminate',19),
('00018','技术员当月平均生意额','M','N','admin','admin',null,20),
('00019','当月最高技术员生意金额','M','N','admin','admin',null,21),
('00020','问题客户（应收报表超过90天）总金额','M','N','admin','admin',null,22),
('00021','今月收款额','M','N','admin','admin',null,23),
('00022','今月材料订购金额','M','N','admin','admin',null,24),
('00023','技术员今月领货金额（IA）','M','N','admin','admin',null,25),
('00024','技术员今月领货金额（IB）','M','N','admin','admin',null,26),
('00025','今月技术员总工资','M','N','admin','admin',null,27),
('00026','今月工资总金额','M','N','admin','admin',null,28),
('00027','上月底公司累计结余','M','N','admin','admin',null,29),
('00028','上月新（IA，IB）服务合同数目','Y','N','admin','admin','CalcService::getLastMonthFigure,00029',31),
('00029','今月新（IA，IB）服务合同数目','Y','N','admin','admin','CalcService::countCaseIAIB',32),
('00030','今月新IA服务合同数目','Y','N','admin','admin','CalcService::countCaseIA',33),
('00031','去年今月新（IA，IB）服务合同数目','M','N','admin','admin',null,34),
('00032','锦旗今月数目','M','N','admin','admin',null,36),
('00033','襟章获颁技术员数目','M','N','admin','admin',null,37),
('00034','襟章发放数目','M','N','admin','admin',null,38),
('00035','上月客诉数目','Y','N','admin','admin','CalcComplaint::getLastMonthFigure,00036',40),
('00036','今月客诉数目','Y','N','admin','admin','CalcComplaint::countCase',41),
('00037','当月解决客诉数目','Y','N','admin','admin','CalcComplaint::countFinishCase',42),
('00038','2天内解决客诉数目','Y','N','admin','admin','CalcComplaint::countFinishCaseIn2Days',43),
('00039','客诉后7天内电话客户回访数目','Y','N','admin','admin','CalcComplaint::countCallIn7days',44),
('00040','队长/组长跟客诉技术员面谈数目','Y','N','admin','admin','CalcComplaint::countNotifyLeader',45),
('00041','问题客户需要队长/组长跟进数目','Y','N','admin','admin','CalcComplaint::countLeaderHandle',46),
('00042','今月质检客户数量','Y','N','admin','admin','CalcQc::countCase',47),
('00043','低于70分质检客户数量','Y','N','admin','admin','CalcQc::countResultBelow70',48),
('00044','质检拜访平均分数最高同事','Y','S','admin','admin','CalcQc::listHighestMarkStaff',49),
('00045','5天成功安装机器合同数目','Y','N','admin','admin','CalcService::countInstallIn5Days',50),
('00046','7天成功安排首次合同数目','Y','N','admin','admin','CalcService::countFirstTimeIn7Days',51),
('00047','车辆数目','M','N','admin','admin',null,53),
('00048','今月平均每部车用油金额','M','N','admin','admin',null,54),
('00049','今月应送皂液（桶）','Y','N','admin','admin','CalcLogistic::sumSoapPlanQty',55),
('00050','今月实际送皂液（桶）','Y','N','admin','admin','CalcLogistic::sumSoapActualQty',56),
('00051','今月应送纸品（箱）','Y','N','admin','admin','CalcLogistic::sumPaperPlanQty',57),
('00052','今月实际送纸品（箱）','Y','N','admin','admin','CalcLogistic::sumPaperActualQty',58),
('00053','上月盘点准确度（实际货品量/储存电脑数量）','M','N','admin','admin',null,59),
('00054','超过一个月没有签署劳动合同同事数目（张）','Y','N','admin','admin','CalcStaff::countNoContract',61),
('00055','今月销售离职人数（工作满一个月）数目','Y','N','admin','admin','CalcStaff::countStaffResignSales',62),
('00056','今月技术员离职人数（工作满一个月）数目','Y','N','admin','admin','CalcStaff::countStaffResignTech',63),
('00057','今月办公室离职人数（工作满一个月）数目','Y','N','admin','admin','CalcStaff::countStaffResignOffice',64),
('00058','技术员今月整体人员数目','Y','N','admin','admin','CalcStaff::countStaffTech',65),
('00059','现有队长数目','Y','N','admin','admin','CalcStaff::countLeaderTeam',66),
('00060','现有组长数目','Y','N','admin','admin','CalcStaff::countLeaderGroup',67),
('00061','今月销售人员数目','Y','N','admin','admin','CalcStaff::countStaffSales',68),
('00062','今月办公室人员数目','Y','N','admin','admin','CalcStaff::countStaffOffice',69),
('00063','销售划分区域','M','N','admin','admin','CalcService::getLastMonthFigure,00063',70),
('00064','销售公共区域','M','N','admin','admin','CalcService::getLastMonthFigure,00064',71)
;

