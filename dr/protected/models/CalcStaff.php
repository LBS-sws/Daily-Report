<?php
// Common Functions

class CalcStaff extends Calculation {
	// Count Resigned Staff
	//
	public static function countStaffResignTech($year, $month) {
		return CalcStaff::countStaffResign('TECHNICIAN', $year, $month);
	}
	
	public static function countStaffResignSales($year, $month) {
		return CalcStaff::countStaffResign('SALES', $year, $month);
	}
	
	public static function countStaffResignOffice($year, $month) {
		return CalcStaff::countStaffResign('OFFICE', $year, $month);
	}
	
	public static function countStaffResign($stafftype, $year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.leave_dt is not null and timestampdiff(MONTH,a.join_dt,a.leave_dt) > 0
				and year(a.leave_dt)=$year and month(a.leave_dt)=$month 
				and a.staff_type='$stafftype' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	// Count Existing Staff
	//
	public static function countStaffTech($year, $month) {
		return CalcStaff::countStaff('TECHNICIAN', $year, $month);
	}
	
	public static function countStaffSales($year, $month) {
		return CalcStaff::countStaff('SALES', $year, $month);
	}
	
	public static function countStaffOffice($year, $month) {
		return CalcStaff::countStaff('OFFICE', $year, $month);
	}
	
	public static function countStaff($stafftype, $year, $month) {
		$rtn = array();
		$d = $year.'-'.$month.'-1';
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month))
				and a.join_dt < date_add('$d', interval 1 month)
				and a.staff_type='$stafftype' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	// Count Leader
	//
	public static function countLeaderGroup($year, $month) {
		return CalcStaff::countLeader('GROUP', $year, $month);
	}

	public static function countLeaderTeam($year, $month) {
		return CalcStaff::countLeader('TEAM', $year, $month);
	}

	public static function countLeader($type, $year, $month) {
		$rtn = array();
		$d = $year.'-'.$month.'-1';
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month))
				and a.join_dt < date_add('$d', interval 1 month)
				and a.leader='$type' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function countNoContract($year, $month) {
		$rtn = array();
		$d = $year.'-'.$month.'-1';
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.ctrt_start_dt is null and a.join_dt < date_add('$d', interval -1 month)
				and (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month)) 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}

		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.ctrt_start_dt < a.ctrt_renew_dt and a.ctrt_renew_dt < date_add('$d', interval -1 month)
				and a.ctrt_renew_dt is not null and a.ctrt_start_dt is not null 
				and (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month)) 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) 
				if (isset($rtn[$row['city']])) $rtn[$row['city']] += $row['counter'];
		}
		return $rtn;
	}
}

?>