<?php

class CalcQc extends Calculation {

//今月质检客户数量
	public static function countCase($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_qc a
				left outer join swo_qc_info b on a.id=b.qc_id and b.field_id='sign_cust'
				left outer join swo_qc_info c on a.id=c.qc_id and c.field_id='sign_qc'
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				and (b.field_blob<>'' and c.field_blob<>'')
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//低于70分质检客户数量	
	public static function countResultBelow70($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_qc a
				left outer join swo_qc_info b on a.id=b.qc_id and b.field_id='sign_cust'
				left outer join swo_qc_info c on a.id=c.qc_id and c.field_id='sign_qc'
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				and a.qc_result is not null and a.qc_result <> '' 
				and (a.qc_result*1<>0 or a.qc_result in ('000','0','0.0','0.00','0.000','000.000'))
				and a.qc_result*1 < 70
				and (b.field_blob<>'' and c.field_blob<>'')
				group by a.city
			";
//		$sql = "select a.city, count(a.id) as counter from swo_qc a
//				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
//				and a.qc_result is not null and a.qc_result <> '' and concat('',a.qc_result*1)=a.qc_result
//				and a.qc_result*1 < 70
//				group by a.city
//			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

//质检拜访平均分数最高同事
	public static function listHighestMarkStaff($year, $month) {
		$rtn = array();
		$sql = "select a.city, a.job_staff, avg(cast(a.qc_result as decimal(8,2))) as score from swo_qc a
				left outer join swo_qc_info b on a.id=b.qc_id and b.field_id='sign_cust'
				left outer join swo_qc_info c on a.id=c.qc_id and c.field_id='sign_qc'
				where year(a.qc_dt)=$year and month(a.qc_dt)=$month 
				and a.qc_result is not null and a.qc_result <> '' 
				and (b.field_blob<>'' and c.field_blob<>'')
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