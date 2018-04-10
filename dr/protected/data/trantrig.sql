CREATE TRIGGER `ins_Service` AFTER INSERT ON Service 
FOR EACH ROW 
  INSERT INTO lbs-txn.txnService(
  `txn_action`,`ServiceType`,`ServiceName`,`Skills`,`ServiceCode`)
  VALUES(
  'I',NEW.`ServiceType`,NEW.`ServiceName`,NEW.`Skills`,NEW.`ServiceCode`)
;

CREATE TRIGGER `upd_Service` AFTER UPDATE ON Service 
FOR EACH ROW 
  INSERT INTO lbs-txn.txnService(
  `txn_action`,`ServiceType`,`ServiceName`,`Skills`,`ServiceCode`)
  VALUES(
  'U',NEW.`ServiceType`,NEW.`ServiceName`,NEW.`Skills`,NEW.`ServiceCode`)
;

CREATE TRIGGER `upd_Service` BEFORE DELETE ON Service 
FOR EACH ROW 
  INSERT INTO lbs-txn.txnService(
  `txn_action`,`ServiceType`,`ServiceName`,`Skills`,`ServiceCode`)
  VALUES(
  'D',OLD.`ServiceType`,OLD.`ServiceName`,OLD.`Skills`,OLD.`ServiceCode`)
;

DROP TABLE IF EXISTS `txnServiceContract`;
CREATE TABLE `txnServiceContract` (
  `txn_id` int unsigned not null auto_increment primary key,
  `txn_date` timestamp default CURRENT_TIMESTAMP,
  `txn_action` char(1) NOT NULL, 
  `ContractID` int(11) NOT NULL,
  `CustomerID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ContractNumber` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `ServiceType` int(11) NOT NULL,
  `PaymentTerm` int(11) NOT NULL,
  `PaymentMethod` int(11) NOT NULL,
  `FirstJob` int(11) NOT NULL,
  `Skills` int(10) unsigned NOT NULL,
  `MonthCycle` int(10) unsigned NOT NULL,
  `WeekCycle` int(10) unsigned NOT NULL,
  `DayCycle` int(10) unsigned NOT NULL,
  `FirstDate` date NOT NULL,
  `BeginDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `PrepayMonth` int(11) NOT NULL,
  `ChargeByJob` int(11) NOT NULL,
  `Item01` int(11) NOT NULL,
  `Item01Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item02` int(11) NOT NULL,
  `Item02Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item03` int(11) NOT NULL,
  `Item03Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item04` int(11) NOT NULL,
  `Item04Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item05` int(11) NOT NULL,
  `Item05Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item06` int(11) NOT NULL,
  `Item06Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item07` int(11) NOT NULL,
  `Item07Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item08` int(11) NOT NULL,
  `Item08Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item09` int(11) NOT NULL,
  `Item09Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item10` int(11) NOT NULL,
  `Item10Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item11` int(11) NOT NULL,
  `Item11Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item12` int(11) NOT NULL,
  `Item12Rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Item13` int(11) NOT NULL,
  `Item13Rmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CreateTime` datetime NOT NULL,
  `UpdateTime` datetime NOT NULL,
  `CreateBy` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `UpdateBy` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Sales` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Sales2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `InvoiceAmount` float NOT NULL,
  `OneTimeFee` float unsigned NOT NULL,
  `Remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `TechRemarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL,
  `JobTime` time NOT NULL,
  `Staff01` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Staff02` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Staff03` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ContactName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Mobile` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `LastJob` date NOT NULL,
  `LastInvoice` date NOT NULL,
  `StopRmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `txnCustomerCompany`;
CREATE TABLE `txnCustomerCompany` (
  `txn_id` int unsigned not null auto_increment primary key,
  `txn_date` timestamp default CURRENT_TIMESTAMP,
  `txn_action` char(1) NOT NULL, 
  `CustomerID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `NameZH` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `NameEN` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `NameOB` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `NameBill` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `NameShop` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `Addr` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `Area` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `AddrRemarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `AddrBill` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `AddrBiRemarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `Tel` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `Fax` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `CustomerType` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `City` int(11) NOT NULL,
  `District` int(11) NOT NULL,
  `Street` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `SalesRep` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `InvRemarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `txnCustomerContact`;
CREATE TABLE `txnCustomerContact` (
  `txn_id` int unsigned not null auto_increment primary key,
  `txn_date` timestamp default CURRENT_TIMESTAMP,
  `txn_action` char(1) NOT NULL, 
  `ContactID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `CustomerID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ContactName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Dept` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Tel` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Mobile` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Fax` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Line` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `apn` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `gcm` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `UseApp` int(11) NOT NULL,
  `NotifyJob` int(11) NOT NULL,
  `NotifyCS` int(11) NOT NULL,
  `NotifyAd` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `CreateTime` datetime NOT NULL,
  `UpdateTime` datetime NOT NULL,
  `Gender` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `txnInvoice`;
CREATE TABLE `txnInvoice` (
  `txn_id` int unsigned not null auto_increment primary key,
  `txn_date` timestamp default CURRENT_TIMESTAMP,
  `txn_action` char(1) NOT NULL, 
  `InvoiceNumber` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `InvoiceDate` date NOT NULL,
  `City` int(11) NOT NULL,
  `CustomerID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `NameBill` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `ProductCode` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `ProductName` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `Unit` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Qty` int(11) NOT NULL,
  `UnitPrice` decimal(10,0) NOT NULL,
  `InvoiceAmount` decimal(10,0) NOT NULL,
  `Remarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `txnProductContract`;
CREATE TABLE `txnProductContract` (
  `txn_id` int unsigned not null auto_increment primary key,
  `txn_date` timestamp default CURRENT_TIMESTAMP,
  `txn_action` char(1) NOT NULL, 
  `ContractID` int(11) NOT NULL,
  `CustomerID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ContractNumber` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `ProductCode` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `Qty` int(11) NOT NULL,
  `PaymentTerm` int(11) NOT NULL,
  `PaymentMethod` int(11) NOT NULL,
  `MonthCycle` int(10) unsigned NOT NULL,
  `WeekCycle` int(10) unsigned NOT NULL,
  `DayCycle` int(10) unsigned NOT NULL,
  `BeginDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `CreateTime` datetime NOT NULL,
  `UpdateTime` datetime NOT NULL,
  `CreateBy` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `UpdateBy` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Sales` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL,
  `LastInvoice` date NOT NULL,
  `StopRmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
