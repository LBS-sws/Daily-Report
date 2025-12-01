<?php

class CalcLogistic extends Calculation {

	public static function sumSoapPlanQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='SOAP' and 
					year(a.log_dt)=$year and month(a.log_dt)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function sumSoapActualQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='SOAP' and 
					b.finish='Y' and b.deadline is not null and 
					year(a.log_dt)=$year and month(a.log_dt)=$month and 
					year(b.deadline)=$year and month(b.deadline)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function sumPaperPlanQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='PAPER' and 
					year(a.log_dt)=$year and month(a.log_dt)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function sumPaperActualQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='PAPER' and 
					b.finish='Y' and b.deadline is not null and 
					year(a.log_dt)=$year and month(a.log_dt)=$month and 
					year(b.deadline)=$year and month(b.deadline)=$month  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function sumFloorPlanQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='FLOOR' and 
					year(a.log_dt)=$year and month(a.log_dt)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function sumFloorActualQty($year, $month) {
		$rtn = array();
		$sql = "select 
					a.city, sum(b.qty) as counter 
				from 
					swo_logistic a, swo_logistic_dtl b, swo_task c
				where
					a.id=b.log_id and b.task=c.id and 
					c.task_type='FLOOR' and 
					b.finish='Y' and b.deadline is not null and 
					year(a.log_dt)=$year and month(a.log_dt)=$month and 
					year(b.deadline)=$year and month(b.deadline)=$month  
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

}

?>