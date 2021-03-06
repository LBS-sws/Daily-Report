alter table swo_task
add column task_type char(5) after description;

alter table swo_city
add column incharge varchar(30) after region;

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
('00001','上月生意额','M','N','admin','admin','CalcService::getLastMonthFigure,00002',3),
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
('00047','车辆数目','M','N','admin','admin','CalcService::getLastMonthFigure,00047',53),
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

alter table swo_service 
add column need_install char(1) default 'N' after amt_install;
