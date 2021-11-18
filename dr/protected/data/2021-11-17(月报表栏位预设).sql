-- 不能编辑：16项今月服务金额、 33项锦旗今月数目、 70项去年今月利润额
update swo_monthly_field set upd_type='Y' where code in ('00016','00032','00068');

update swo_monthly_field set function_name='CalcService::countActualIAIB' where code='00071';