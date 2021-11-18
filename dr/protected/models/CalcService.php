<?php

class CalcService extends Calculation {

//今月新（IA，IB）服务合同数目
	public static function countCaseIAIB($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.cust_type=b.id and b.rpt_cat in ('IA','IB') 
				and a.status='N' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function countCaseIAIBLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcService::countCaseIAIB($ly, $lm);
	}

//今月新IA服务合同数目
	public static function countCaseIA($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.cust_type=b.id and b.rpt_cat in ('IA') 
				and a.status='N' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//今月新IA需安装服务合同数目
	public static function countCaseIAWithInstall($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.cust_type=b.id and b.rpt_cat in ('IA') 
				and a.status='N' and a.need_install='Y'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function countInstall($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.status='N' and a.need_install='Y' 
				and a.cust_type=b.id and b.rpt_cat <> 'INV'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//5天成功安装机器合同数目
	public static function countInstallIn5Days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.status='N' and a.equip_install_dt is not null 
				and timestampdiff(DAY,a.status_dt,a.equip_install_dt)<=5 and a.need_install='Y' 
				and a.cust_type=b.id and b.rpt_cat='IA'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//7天成功安排首次合同数目
	public static function countFirstTimeIn7Days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.status='N' and a.first_dt is not null 
				and timestampdiff(DAY,a.status_dt,a.first_dt)<=7 
				and a.cust_type=b.id and b.rpt_cat in ('IA','IB')  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//今月新（IA，IB）服务年生意额 
	public static function sumAmountIAIB($year, $month) {
		$rtn = array();
/*
		$sql = "select a.city, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount
				from swo_service a, swo_customer_type b 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.cust_type=b.id and b.rpt_cat in ('IA','IB') 
				and a.status='N' 
				group by a.city
			";
*/
		$sql = "select 
					a.city, 
					sum(
						(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end) 
						- if(a.status='N', 0, 
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) as sum_amount
				from 
					swo_service a, swo_customer_type b 
				where 
					a.cust_type=b.id and b.rpt_cat in ('IA','IB') and 
					((a.status='N' and year(a.first_dt)=$year and month(a.first_dt)=$month) or 
						(a.status='A' and year(a.status_dt)=$year and month(a.status_dt)=$month and
							(case a.paid_type
								when 'Y' then a.amt_paid
								when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else a.amt_paid
							end) >
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
		return $rtn;
	}

	public static function sumAmountIAIBLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcService::sumAmountIAIB($ly, $lm);
	}

//今月新业务年生意额
	public static function sumAmountNEW($year, $month) {
		$rtn = array();
/*
		$sql = "select a.city, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount
				from swo_service a, swo_customer_type b  
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.cust_type=b.id 
				and b.rpt_cat in ('NEW', 'IC') 
				and a.status='N' 
				group by a.city
			";
*/
		$sql = "select 
					a.city, 
					sum(
						(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end) 
						- if(a.status='N', 0, 
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) as sum_amount
				from 
					swo_service a, swo_customer_type b 
				where 
					a.cust_type=b.id and 
					b.rpt_cat in ('NEW', 'IC', 'ID') and
					((a.status='N' and year(a.first_dt)=$year and month(a.first_dt)=$month) or 
						(a.status='A' and year(a.status_dt)=$year and month(a.status_dt)=$month and 
							(case a.paid_type
								when 'Y' then a.amt_paid
								when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else a.amt_paid
							end) >
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}

//In swo_serviceid, sign_dt = 合同开始日期, equip_install_dt = 签约日期
		$sql = "select 
					a.city, 
					sum(
						a.amt_money
						- if(a.status='N', 0, a.b4_amt_money)
					) as sum_amount
				from 
					swo_serviceid a, swo_customer_type_info b 
				where 
					a.cust_type_name=b.id and 
					b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and
					(
					(a.status='N' and b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and year(a.sign_dt)=$year and month(a.sign_dt)=$month) or 
					(a.status='A' and a.amt_money > a.b4_amt_money and year(a.status_dt)=$year and month(a.status_dt)=$month)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','') + (isset($rtn[$row['city']]) ? $rtn[$row['city']] : 0);
		}
		return $rtn;
	}

	public static function sumAmountNEWLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcService::sumAmountNEW($ly, $lm);
	}

//今月餐饮年生意额 
	public static function sumAmountRestaurant($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, 
					sum(
						(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end) 
						- if(a.status='N', 0, 
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) as sum_amount
				from 
					swo_service a, swo_customer_type b, swo_nature c 
				where 
					 
					a.cust_type=b.id and b.rpt_cat <> 'INV' and 
					a.nature_type=c.id and c.rpt_cat='A01' and
					((a.status='N' and year(a.first_dt)=$year and month(a.first_dt)=$month) or 
						(a.status='A' and year(a.status_dt)=$year and month(a.status_dt)=$month and
							(case a.paid_type
								when 'Y' then a.amt_paid
								when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else a.amt_paid
							end) >
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}

//In swo_serviceid, sign_dt = 合同开始日期, equip_install_dt = 签约日期
		$sql = "select 
					a.city, 
					sum(
						a.amt_money
						- if(a.status='N', 0, a.b4_amt_money)
					) as sum_amount
				from 
					swo_serviceid a, swo_customer_type_info b, swo_nature c 
				where 
					a.cust_type_name=b.id and 
					b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and
					a.nature_type=c.id and c.rpt_cat='A01' and
					(
					(a.status='N' and b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and year(a.sign_dt)=$year and month(a.sign_dt)=$month) or 
					(a.status='A' and a.amt_money > a.b4_amt_money and year(a.status_dt)=$year and month(a.status_dt)=$month)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','') + (isset($rtn[$row['city']]) ? $rtn[$row['city']] : 0);
		}
		return $rtn;
	}

//今月非餐饮年生意额 
	public static function sumAmountNonRestaurant($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, 
					sum(
						(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end) 
						- if(a.status='N', 0, 
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) as sum_amount
				from 
					swo_service a, swo_customer_type b, swo_nature c 
				where 
					
					a.cust_type=b.id and b.rpt_cat <> 'INV' and 
					a.nature_type=c.id and c.rpt_cat in ('B01','C01') and
					((a.status='N' and year(a.first_dt)=$year and month(a.first_dt)=$month) or 
						(a.status='A' and year(a.status_dt)=$year and month(a.status_dt)=$month and 
							(case a.paid_type
								when 'Y' then a.amt_paid
								when 'M' then a.amt_paid * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else a.amt_paid
							end) >
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0)
								when 'M' then ifnull(a.b4_amt_paid,0) * (case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
//In swo_serviceid, sign_dt = 合同开始日期, equip_install_dt = 签约日期
		$sql = "select 
					a.city, 
					sum(
						a.amt_money
						- if(a.status='N', 0, a.b4_amt_money)
					) as sum_amount
				from 
					swo_serviceid a, swo_customer_type_info b, swo_nature c 
				where 
					a.cust_type_name=b.id and 
					b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and
					a.nature_type=c.id and c.rpt_cat in ('B01','C01') and
					(
					(a.status='N' and b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and year(a.sign_dt)=$year and month(a.sign_dt)=$month) or 
					(a.status='A' and a.amt_money > a.b4_amt_money and year(a.status_dt)=$year and month(a.status_dt)=$month)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','') + (isset($rtn[$row['city']]) ? $rtn[$row['city']] : 0);
		}
		return $rtn;
	}

// 今月生意净增长 （年生意额
	public static function sumAmountNetGrowth($year, $month) {
		$rtn = array();
		$sql = "select a.city, a.status, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount,
					sum(case a.b4_paid_type
							when 'Y' then a.b4_amt_paid
							when 'M' then a.b4_amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.b4_amt_paid
						end
					) as b4_sum_amount
				from swo_service a, swo_customer_type b 
				where ((year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.status in ('N'))  
				or (year(a.status_dt)=$year and month(a.status_dt)=$month 
				and a.status in ('T','A')))
				and a.cust_type=b.id and b.rpt_cat <> 'INV'  
				group by a.city, a.status
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$city = '';
			$amt_n = 0;
			$amt_a = 0;
			$amt_r = 0;
			$amt_s = 0;
			$amt_t = 0;
			foreach ($rows as $row) {
				if ($row['city']!=$city) {
					if ($city!='') $rtn[$city] = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','');
					
					$amt_n = 0;
					$amt_r = 0;
					$amt_a = 0;
					$amt_s = 0;
					$amt_t = 0;
					$city = $row['city'];
				}
				switch ($row['status']) {
					case 'N': $amt_n = $row['sum_amount']; break;
					case 'A': $amt_a = $row['sum_amount']-$row['b4_sum_amount']; break;
					case 'R': $amt_r = $row['sum_amount']; break;
					case 'S': $amt_s = $row['sum_amount']; break;
					case 'T': $amt_t = $row['sum_amount']; break;
				}
			}
			$rtn[$city] = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','');
		}

//In swo_serviceid, sign_dt = 合同开始日期, equip_install_dt = 签约日期
		$sql = "select a.city, a.status, 
					sum(a.amt_money) as sum_amount,
					sum(a.b4_amt_money) as b4_sum_amount
				from
					swo_serviceid a, swo_customer_type_info b 
				where 
					a.cust_type_name=b.id and 
					b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and
					(
					(a.status='N' and b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and year(a.sign_dt)=$year and month(a.sign_dt)=$month) or 
					(a.status in ('T','A') and year(a.status_dt)=$year and month(a.status_dt)=$month)
					) 
				group by 
					a.city, a.status
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$city = '';
			$amt_n = 0;
			$amt_a = 0;
			$amt_r = 0;
			$amt_s = 0;
			$amt_t = 0;
			foreach ($rows as $row) {
				if ($row['city']!=$city) {
					if ($city!='') $rtn[$city] = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','') + (isset($rtn[$city]) ? $rtn[$city] : 0);
					
					$amt_n = 0;
					$amt_r = 0;
					$amt_a = 0;
					$amt_s = 0;
					$amt_t = 0;
					$city = $row['city'];
				}
				switch ($row['status']) {
					case 'N': $amt_n = $row['sum_amount']; break;
					case 'A': $amt_a = $row['sum_amount']-$row['b4_sum_amount']; break;
					case 'R': $amt_r = $row['sum_amount']; break;
					case 'S': $amt_s = $row['sum_amount']; break;
					case 'T': $amt_t = $row['sum_amount']; break;
				}
			}
			$rtn[$city] = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','') + (isset($rtn[$city]) ? $rtn[$city] : 0);
		}
		return $rtn;
	}

	public static function sumAmountNetGrowthLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		return CalcService::sumAmountNetGrowth($ly, $lm);
	}

//今月停单月生意额
	public static function sumAmountTerminate($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, 
					sum(
						if(a.status='T',
							(case a.paid_type
								when 'Y' then a.amt_paid / (case when a.ctrt_period > 0 then a.ctrt_period else 12 end)
								when 'M' then a.amt_paid
								else a.amt_paid
							end),
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0) / (case when a.ctrt_period > 0 then a.ctrt_period else 12 end)
								when 'M' then ifnull(a.b4_amt_paid,0)
								else ifnull(a.b4_amt_paid,0)
							end) -
							(case a.paid_type
								when 'Y' then a.amt_paid / (case when a.ctrt_period > 0 then a.ctrt_period else 12 end)
								when 'M' then a.amt_paid
								else a.amt_paid
							end) 
						)
					) as sum_amount
				from 
					swo_service a, swo_customer_type b 
				where 
					year(a.status_dt)=$year and month(a.status_dt)=$month and 
					a.cust_type=b.id and 
					b.rpt_cat <> 'INV' and
					(a.status='T' or 
						(a.status='A' and 
							(case a.paid_type
								when 'Y' then a.amt_paid / (case when a.ctrt_period > 0 then a.ctrt_period else 12 end)
								when 'M' then a.amt_paid
								else a.amt_paid
							end) <
							(case a.b4_paid_type
								when 'Y' then ifnull(a.b4_amt_paid,0) / (case when a.ctrt_period > 0 then a.ctrt_period else 12 end)
								when 'M' then ifnull(a.b4_amt_paid,0)
								else ifnull(a.b4_amt_paid,0)
							end)
						)
					) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
//In swo_serviceid, sign_dt = 合同开始日期, equip_install_dt = 签约日期
		$sql = "select 
					a.city, 
					sum(
						if(a.status='T', a.amt_paid, ifnull(a.b4_amt_paid,0)-a.amt_paid)
					) as sum_amount
				from 
					swo_serviceid a, swo_customer_type_info b 
				where 
					a.cust_type_name=b.id and 
					b.cust_type_name in ('租赁服务', '延长维保', 'RA机器-轻松派', 'RA机器-随意派') and
					year(a.status_dt)=$year and month(a.status_dt)=$month and 
					(a.status='T' or (a.status='A' and a.amt_paid < ifnull(a.b4_amt_paid,0))) 
				group by
					a.city
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','') + (isset($rtn[$row['city']]) ? $rtn[$row['city']] : 0);
		}
		return $rtn;
	}

	public static function countActualIAIB($year, $month) {
		$rs = array('message'=>'', 'data'=>array());
		
		$key = Yii::app()->params['unitedKey'];
		$root = Yii::app()->params['unitedRootURL'];
		$url = $root.'/remote/countIAIB.php';
		$data = array(
			"key"=>$key,
			"year"=>$year,
			"month"=>$month
		);
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			'Content-Length:'.strlen($data_string),
 		));
		$out = curl_exec($ch);
		if ($out===false) {
			$rs['message'] = curl_error($ch);
		} else {
			$json = json_decode($out);
			$rs['data'] = json_decode($out, true);
			$rs['message'] = self::getJsonError(json_last_error());
		}
		
		$rtn = array();
		if ($rs['message']=='Success') {
			foreach ($rs['data'] as $item) {
				$rtn[$item['city']] = $item['count'];
			}
		}
		return $rtn;
	}
	
	public static function getJsonError($error) {
		switch ($error) {
			case JSON_ERROR_NONE:
				return 'Success';
			case JSON_ERROR_DEPTH:
				return ' - Maximum stack depth exceeded';
			case JSON_ERROR_STATE_MISMATCH:
				return ' - Underflow or the modes mismatch';
			case JSON_ERROR_CTRL_CHAR:
				return ' - Unexpected control character found';
			case JSON_ERROR_SYNTAX:
				return ' - Syntax error, malformed JSON';
			case JSON_ERROR_UTF8:
				return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			default:
				return' - Unknown error ('.$error.')';
		}
	}
}

?>
