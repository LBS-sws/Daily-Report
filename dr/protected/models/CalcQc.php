<?php

class CalcQc extends Calculation {

	public static function countCase($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_qc a
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function countResultBelow70($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_qc a
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				and a.qc_result is not null and a.qc_result <> '' and concat('',a.qc_result*1)=a.qc_result
				and a.qc_result*1 < 70
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function listHighestMarkStaff($year, $month) {
		$rtn = array();
		$sql = "select a.city, a.job_staff, avg(cast(a.qc_result as decimal(8,2))) as score from swo_qc a
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				and a.qc_result is not null and a.qc_result <> '' 
				group by a.city, a.job_staff
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$city = '';
			foreach ($rows as $row) {
				if ($city != $row['city']) {
					$city = $row['city'];
					$highest = 0;
					$rtn[$row['city']] = '';
				}
				
				if ($row['score'] == $highest) 
					$rtn[$row['city']] .= (($rtn[$row['city']]=='') ? '' : ' ').$row['job_staff'];
				
				if ($row['score'] > $highest) {
					$highest = $row['score'];
					$rtn[$row['city']] = $row['job_staff'];
				}
			}
		}
		return $rtn;
	}
}

?>