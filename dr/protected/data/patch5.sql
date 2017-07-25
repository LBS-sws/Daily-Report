use security;

insert into sec_user 
(username, password, disp_name, email, logon_time, logoff_time, status, fail_count, locked, session_key, city, lcu, luu)
select 
username, password, disp_name, email, logon_time, logoff_time, status, fail_count, locked, session_key, city, lcu, luu
from swoper.swo_user;

insert into sec_city
(code, name, region, incharge, lcu, luu)
select
code, name, region, incharge, lcu, luu
from swoper.swo_city;

insert into sec_user_access
(username, system_id, a_read_only, a_read_write, a_control, lcu, luu)
select
a.username, 'drs', b.a_read_only, b.a_read_write, b.a_control, 'admin', 'admin'
from swoper.swo_user a, swoper.swo_group b
where a.group_id=b.group_id;

insert into sec_user_option
select * from swoper.swo_user_option;