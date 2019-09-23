CREATE DATABASE datasyncuat CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON datasyncuat.* TO 'swuser'@'localhost';

use datasyncuat;
DROP TABLE IF EXISTS `sync_queue_down`;
CREATE TABLE `sync_queue_down` (
  id int unsigned not null auto_increment NOT NULL primary key,
  source varchar(100) NOT NULL,
  data_type varchar(100) NOT NULL,
  data_content longtext,
  status char(1) NOT NULL DEFAULT 'P',
  fin_dt datetime,
  lcd timestamp default CURRENT_TIMESTAMP,
  lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
