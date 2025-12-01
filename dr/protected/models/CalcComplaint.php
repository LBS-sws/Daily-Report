<?php

class CalcComplaint extends Calculation {

	public static function countCase($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function countCaseLastMonth($year, $month) {
		$d = strtotime('-1 month', strtotime($year.'-'.$month.'-1'));
		$ly = date('Y', $d);
		$lm = date('m', $d);
		$rtn = CalcComplaint::countCase($ly, $lm);
	}
	
	public static function countFinishCase($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				and timestampdiff(MONTH,a.entry_dt,a.fin_dt)=0
				and a.fin_dt is not null
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function countFinishCaseIn2Days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				and timestampdiff(DAY,a.entry_dt,a.fin_dt)<=2
				and a.fin_dt is not null
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function countCallIn7days($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				and timestampdiff(DAY,a.entry_dt,a.fp_call_dt)<=7
				and a.fp_call_dt is not null
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function countNotifyLeader($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				and mgr_notify='Y' and mgr_talk='Y' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	public static function countLeaderHandle($year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_followup a
				where year(a.entry_dt)=$year and month(a.entry_dt)=$month 
				and leader='Y'
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