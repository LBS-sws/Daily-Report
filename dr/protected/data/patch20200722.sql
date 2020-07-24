alter table swo_company add column group_id varchar(20) after city;
alter table swo_company add column group_name varchar(200) after group_id;
alter table swo_company add column status int after group_name;