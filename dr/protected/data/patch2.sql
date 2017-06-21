alter table swo_logistic_dtl
add column finish char(1) default 'N' after qty,
add column deadline datetime after finish;

alter table swo_service
add column ctrt_end_dt datetime after sign_dt,
add column remarks2 varchar(1000) after rtn_equip_qty;

alter table swo_enquiry
add column nature_type int unsigned after customer,
add column address varchar(255) after tel_no,
add column follow_result varchar(1000) after follow_dt,
add column record_by varchar(100) after remarks
add column source_code char(5) after address;

update swo_service 
set ctrt_end_dt = sign_dt+interval ctrt_period month 
where sign_dt is not null and ctrt_period > 0;

update swo_enquiry
set source_code = '4';