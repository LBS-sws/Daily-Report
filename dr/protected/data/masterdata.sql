CREATE DATABASE datatxnuat CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON datatxnuat.* TO 'swuser'@'localhost';

use datatxnuat;

DROP TABLE IF EXISTS `txnrecord`;
CREATE TABLE `txnrecord` (
  id int unsigned not null auto_increment NOT NULL primary key,
  cat varchar(30) NOT NULL,
  last_id int unsigned NOT NULL,
  data longtext,
  status char(1) NOT NULL default 'P',
  lcd timestamp default CURRENT_TIMESTAMP,
  lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE DATABASE masterdatauat CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON masterdatauat.* TO 'swuser'@'localhost';

use masterdatauat;

DROP TABLE IF EXISTS `app_service`;
CREATE TABLE `app_service` (
  svr_id char(10) NOT NULL,
  `service_type` int(11) NOT NULL,
  `service_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `skills` int(10) unsigned NOT NULL,
  `service_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `app_service_contract`;
CREATE TABLE `app_service_contract` (
  svr_id char(10) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `contract_number` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `service_type` int(11) NOT NULL,
  `payment_term` int(11) NOT NULL,
  `payment_method` int(11) NOT NULL,
  `first_job` int(11) NOT NULL,
  `skills` int(10) unsigned NOT NULL,
  `month_cycle` int(10) unsigned NOT NULL,
  `week_cycle` int(10) unsigned NOT NULL,
  `day_cycle` int(10) unsigned NOT NULL,
  `first_date` date NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `prepay_month` int(11) NOT NULL,
  `charge_by_job` int(11) NOT NULL,
  `item01` int(11) NOT NULL,
  `item01_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item02` int(11) NOT NULL,
  `item02_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item03` int(11) NOT NULL,
  `item03_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item04` int(11) NOT NULL,
  `item04_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item05` int(11) NOT NULL,
  `item05_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item06` int(11) NOT NULL,
  `item06_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item07` int(11) NOT NULL,
  `item07_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item08` int(11) NOT NULL,
  `item08_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item09` int(11) NOT NULL,
  `item09_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item10` int(11) NOT NULL,
  `item10_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item11` int(11) NOT NULL,
  `item11_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item12` int(11) NOT NULL,
  `item12_rmk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item13` int(11) NOT NULL,
  `item13_rmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `create_by` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `update_by` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `sales` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `sales2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_amount` float NOT NULL,
  `one_time_fee` float unsigned NOT NULL,
  `remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `tech_remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `job_time` time NOT NULL,
  `staff01` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `staff02` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `staff03` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `contact_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `last_job` date NOT NULL,
  `last_invoice` date NOT NULL,
  `stop_rmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY idx_svc_ctrt_01(svr_id, contract_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `app_customer_company`;
CREATE TABLE `app_customer_company` (
  svr_id char(10) NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name_zh` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `name_ob` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `name_bill` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `name_shop` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `addr` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `area` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `addr_remarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `addr_bill` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `addr_bi_remarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `tel` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `customer_type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `city` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `street` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `sales_rep` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `inv_remarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY idx_cust_co_01(svr_id, customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `app_customer_contact`;
CREATE TABLE `app_customer_contact` (
  svr_id char(10) NOT NULL,
  `contact_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `contact_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `dept` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `tel` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `line` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `apn` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `gcm` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `use_app` int(11) NOT NULL,
  `notify_job` int(11) NOT NULL,
  `notify_cs` int(11) NOT NULL,
  `notify_ad` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `gender` int(11) NOT NULL,
  UNIQUE KEY idx_cust_cont_01(svr_id, contact_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `app_invoice`;
CREATE TABLE `app_invoice` (
  svr_id char(10) NOT NULL,
  `invoice_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `city` int(11) NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name_bill` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `product_code` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `product_name` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(10,0) NOT NULL,
  `invoice_amount` decimal(10,0) NOT NULL,
  `remarks` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  UNIQUE KEY idx_inv_01(svr_id, invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `app_product_contract`;
CREATE TABLE `app_product_contract` (
  svr_id char(10) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `contract_number` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `product_code` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `payment_term` int(11) NOT NULL,
  `payment_method` int(11) NOT NULL,
  `month_cycle` int(10) unsigned NOT NULL,
  `week_cycle` int(10) unsigned NOT NULL,
  `day_cycle` int(10) unsigned NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `create_by` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `update_by` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `sales` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` varchar(1200) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `last_invoice` date NOT NULL,
  `stop_rmk` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY idx_prod_ctrt_01(svr_id, contract_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
