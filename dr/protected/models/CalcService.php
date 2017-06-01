<?php

class CalcService extends Calculation {

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

	public static function countInstall($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.status_dt)=$year and month(a.status_dt)=$month 
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

	public static function countInstallIn5Days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.status_dt)=$year and month(a.status_dt)=$month 
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

	public static function countFirstTimeIn7Days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter 
				from swo_service a, swo_customer_type b 
				where year(a.status_dt)=$year and month(a.status_dt)=$month 
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

	public static function sumAmountIAIB($year, $month) {
		$rtn = array();
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

	public static function sumAmountNEW($year, $month) {
		$rtn = array();
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
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
		return $rtn;
	}

	public static function sumAmountNEWLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcService::sumAmountNEW($ly, $lm);
	}

	public static function sumAmountRestaurant($year, $month) {
		$rtn = array();
		$sql = "select a.city, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount
				from swo_service a, swo_nature b, swo_customer_type c 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.nature_type=b.id and b.rpt_cat='A01' 
				and a.status='N' 
				and a.cust_type=c.id and c.rpt_cat <> 'INV'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
		return $rtn;
	}

	public static function sumAmountNonRestaurant($year, $month) {
		$rtn = array();
		$sql = "select a.city, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount
				from swo_service a, swo_nature b, swo_customer_type c 
				where year(a.first_dt)=$year and month(a.first_dt)=$month 
				and a.nature_type=b.id and b.rpt_cat='B01' 
				and a.status='N' 
				and a.cust_type=c.id and c.rpt_cat <> 'INV'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
		return $rtn;
	}

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
		return $rtn;
	}

	public static function sumAmountNetGrowthLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcService::sumAmountNetGrowth($ly, $lm);
	}

	public static function sumAmountTerminate($year, $month) {
		$rtn = array();
		$sql = "select a.city, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid /
								(case when a.ctrt_period > 0 then a.ctrt_period else 1 end)
							when 'M' then a.amt_paid
							else a.amt_paid
						end
					) as sum_amount
				from swo_service a, swo_customer_type b 
				where year(a.status_dt)=$year and month(a.status_dt)=$month 
				and a.status='T' 
				and a.cust_type=b.id and b.rpt_cat <> 'INV'  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = number_format($row['sum_amount'],2,'.','');
		}
		return $rtn;
	}

}

?>
