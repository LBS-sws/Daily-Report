CREATE DATABASE announcementuat CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON announcementuat.* TO 'swuser'@'localhost';

use announcementuat;
DROP TABLE IF EXISTS `ann_announce`;
CREATE TABLE `ann_announce` (
  id int unsigned not null auto_increment NOT NULL primary key,
  name varchar(30) NOT NULL,
  start_dt datetime NOT NULL,
  end_dt datetime NOT NULL,
  priority tinyint NOT NULL DEFAULT 0,
  content text,
  image_caption varchar(1000),
  image_type varchar(30), 
  image longblob,
  lcu varchar(30),
  luu varchar(30),
  lcd timestamp default CURRENT_TIMESTAMP,
  lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
