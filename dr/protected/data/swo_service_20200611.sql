alter table swo_service add column prepay_month smallint null default 0 after city;
alter table swo_service add column prepay_start smallint null default 0 after prepay_month;