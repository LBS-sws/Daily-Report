-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: localhost    Database: swoper
-- ------------------------------------------------------
-- Server version	5.7.18

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `swo_monthly_field`
--

LOCK TABLES `swo_monthly_field` WRITE;
/*!40000 ALTER TABLE `swo_monthly_field` DISABLE KEYS */;
INSERT INTO `swo_monthly_field` VALUES 
('00001','上月生意额','Y','N','Y','CalcService::getLastMonthFigure,00002',3,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00002','今月生意额','M','N','Y',NULL,4,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00003','今月IA生意额','Y','N','Y','CalcService::getOperationFigure,10001',5,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00004','今月IB生意额','Y','N','Y','CalcService::getOperationFigure,10002',6,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00005','上月新（IA，IB）服务年生意额','Y','N','Y','CalcService::getLastMonthFigure,00006',7,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00006','今月新（IA，IB）服务年生意额','Y','N','Y','CalcService::sumAmountIAIB',8,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00007','去年今月新（IA，IB）服务年生意额','Y','N','Y','CalcService::getLastYearFigure,00006',9,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00008','上月新业务年生意额','Y','N','Y','CalcService::getLastMonthFigure,00009',10,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00009','今月新业务年生意额','Y','N','Y','CalcService::sumAmountNEW',11,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00010','去年今月新业务年生意额','Y','N','Y','CalcService::getLastYearFigure,00009',12,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00011','今月餐饮年生意额','Y','N','Y','CalcService::sumAmountRestaurant',13,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00012','今月非餐饮年生意额','Y','N','Y','CalcService::sumAmountNonRestaurant',14,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00013','上月生意净增长 （年生意额）','Y','N','Y','CalcService::getLastMonthFigure,00014',15,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00014','今月生意净增长 （年生意额','Y','N','Y','CalcService::sumAmountNetGrowth',16,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00015','去年今月生意额净增长 （年生意额）','Y','N','Y','CalcService::getLastYearFigure,00014',17,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00016','今月服务金额','M','N','Y','CalcProfit::sumServiceAmount',18,'admin','admin','2017-02-03 00:47:07','2020-02-13 09:54:48'),
('00017','今月停单月生意额','Y','N','Y','CalcService::sumAmountTerminate',19,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00018','技术员当月平均生意额','M','N','Y',NULL,20,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00019','当月最高技术员生意金额','M','N','Y',NULL,21,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00020','问题客户（应收报表超过90天）总金额','M','N','Y',NULL,22,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00021','今月收款额','M','N','Y','CalcProfit::sumRecAmount',23,'admin','admin','2017-02-03 00:47:07','2020-02-13 09:54:48'),
('00022','今月材料订购金额','M','N','Y',NULL,24,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00023','技术员今月领货金额（IA）','M','N','Y',NULL,25,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00024','技术员今月领货金额（IB）','M','N','Y',NULL,26,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00025','今月技术员总工资','M','N','Y',NULL,27,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00026','今月工资总金额','M','N','Y',NULL,28,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00027','上月底公司累计结余','M','N','Y',NULL,29,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00028','上月新（IA，IB）服务合同数目','Y','N','Y','CalcService::getLastMonthFigure,00029',31,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00029','今月新（IA，IB）服务合同数目','Y','N','Y','CalcService::countCaseIAIB',32,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00030','今月新IA服务合同数目','Y','N','Y','CalcService::countCaseIA',33,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00031','去年今月新（IA，IB）服务合同数目','Y','N','Y','CalcService::getLastYearFigure,00029',35,'admin','admin','2017-02-03 00:47:07','2018-10-26 02:09:02'),
('00032','锦旗今月数目','M','N','Y','CalcStaff::sumFlagQty',37,'admin','admin','2017-02-03 00:47:07','2020-02-13 09:54:48'),
('00033','襟章获颁技术员数目','M','N','Y',NULL,38,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00034','襟章发放数目','M','N','Y',NULL,39,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00035','上月客诉数目','Y','N','Y','CalcComplaint::getLastMonthFigure,00036',41,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00036','今月客诉数目','Y','N','Y','CalcComplaint::countCase',42,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00037','当月解决客诉数目','Y','N','Y','CalcComplaint::countFinishCase',43,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00038','2天内解决客诉数目','Y','N','Y','CalcComplaint::countFinishCaseIn2Days',44,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00039','客诉后7天内电话客户回访数目','Y','N','Y','CalcComplaint::countCallIn7days',45,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00040','队长/组长跟客诉技术员面谈数目','Y','N','Y','CalcComplaint::countNotifyLeader',46,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00041','问题客户需要队长/组长跟进数目','Y','N','Y','CalcComplaint::countLeaderHandle',47,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00042','今月质检客户数量','Y','N','Y','CalcQc::countCase',48,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00043','低于70分质检客户数量','Y','N','Y','CalcQc::countResultBelow70',49,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00044','质检拜访平均分数最高同事','Y','S','Y','CalcQc::listHighestMarkStaff',50,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00045','5天成功安装机器合同数目','Y','N','Y','CalcService::countInstallIn5Days',51,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00046','7天成功安排首次合同数目','Y','N','Y','CalcService::countFirstTimeIn7Days',52,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00047','车辆数目','M','N','Y','CalcService::getLastMonthFigure,00047',54,'admin','admin','2017-03-09 20:36:18','2018-10-26 03:15:56'),
('00048','今月平均每部车用油金额','M','N','Y',NULL,55,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00049','今月应送皂液（桶）','Y','N','Y','CalcLogistic::sumSoapPlanQty',56,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00050','今月实际送皂液（桶）','Y','N','Y','CalcLogistic::sumSoapActualQty',57,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00051','今月应送纸品（箱）','Y','N','Y','CalcLogistic::sumPaperPlanQty',58,'admin','admin','2017-02-03 00:47:07','2020-02-13 02:14:04'),
('00052','今月实际送纸品（箱）','Y','N','Y','CalcLogistic::sumPaperActualQty',59,'admin','admin','2017-02-03 00:47:07','2020-02-13 02:14:25'),
('00053','上月盘点准确度（实际货品量/储存电脑数量）%','M','N','Y',NULL,62,'admin','admin','2017-02-03 00:47:07','2017-03-22 01:47:51'),
('00054','超过一个月没有签署劳动合同同事数目（张）','Y','N','Y','CalcStaff::countNoContract',64,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00055','今月销售离职人数（工作满一个月）数目','Y','N','Y','CalcStaff::countStaffResignSales',65,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00056','今月技术员离职人数（工作满一个月）数目','Y','N','Y','CalcStaff::countStaffResignTech',66,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00057','今月办公室离职人数（工作满一个月）数目','Y','N','Y','CalcStaff::countStaffResignOffice',67,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00058','技术员今月整体人员数目','Y','N','Y','CalcStaff::countStaffTech',68,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00059','现有队长数目','Y','N','Y','CalcStaff::countLeaderTeam',69,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00060','现有组长数目','Y','N','Y','CalcStaff::countLeaderGroup',70,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00061','今月销售人员数目','Y','N','Y','CalcStaff::countStaffSales',71,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00062','今月办公室人员数目','Y','N','Y','CalcStaff::countStaffOffice',72,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00063','销售划分区域','M','N','Y','CalcService::getLastMonthFigure,00063',73,'admin','admin','2017-02-03 00:47:07','2018-10-26 03:15:56'),
('00064','销售公共区域','M','N','Y','CalcService::getLastMonthFigure,00064',74,'admin','admin','2017-02-03 00:47:07','2018-10-26 03:15:56'),
('00065','今月新IA需安装服务合同数目','Y','N','Y','CalcService::countCaseIAWithInstall',34,'admin','admin','2017-02-03 00:47:07','2017-02-03 00:47:07'),
('00066','上月利润额','M','N','Y','CalcService::getLastMonthFigure,00067',75,'admin','admin','2019-02-18 05:04:24','2019-05-21 04:01:34'),
('00067','今月利润额','M','N','Y','CalcService::getOperationFigure,10012',76,'admin','admin','2019-02-18 05:05:36','2019-05-21 04:01:34'),
('00068','去年今月利润额','M','N','Y','CalcService::getLastYearFigure,00067',77,'admin','admin','2019-02-18 05:06:44','2019-05-21 04:01:34'),
('00069','今月应送洗地易（桶）','Y','N','Y','CalcLogistic::sumFloorPlanQty',60,'admin','admin','2017-02-03 00:47:07','2020-02-13 02:14:04'),
('00070','今月实际送洗地易（桶）','Y','N','Y','CalcLogistic::sumFloorActualQty',61,'admin','admin','2017-02-03 00:47:07','2020-02-13 02:14:25')
;
/*!40000 ALTER TABLE `swo_monthly_field` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-02 17:35:07
