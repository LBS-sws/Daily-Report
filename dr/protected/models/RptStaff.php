<?php
class RptStaff extends ReportData2 {
	public function fields() {
		return array(
			'lud'=>array('label'=>Yii::t('staff','Entry Date'),'width'=>18,'align'=>'L'),
			'code'=>array('label'=>Yii::t('staff','Code'),'width'=>15,'align'=>'L'),
			'name'=>array('label'=>Yii::t('staff','Name'),'width'=>30,'align'=>'L'),
			'position'=>array('label'=>Yii::t('staff','Position'),'width'=>30,'align'=>'L'),
			'staff_type'=>array('label'=>Yii::t('staff','Staff Type'),'width'=>20,'align'=>'C'),
			'leader'=>array('label'=>Yii::t('staff','Team/Group Leader'),'width'=>20,'align'=>'C'),
			'education'=>array('label'=>Yii::t('staff','Education'),'width'=>20,'align'=>'C'),
			'join_dt'=>array('label'=>Yii::t('staff','Join Date'),'width'=>18,'align'=>'C'),
			'ctrt_duration'=>array('label'=>Yii::t('staff','Cont. Duration'),'width'=>40,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('staff','Cont. Period'),'width'=>18,'align'=>'C'),
			'year_day'=>array('label'=>Yii::t('staff','AL Days'),'width'=>18,'align'=>'C'),
			'leave_days'=>array('label'=>Yii::t('staff','Acc. Leave Days'),'width'=>18,'align'=>'C'),
			'ctrt_renew_dt'=>array('label'=>Yii::t('staff','Cont. Renew Date'),'width'=>18,'align'=>'C'),
			'remarks'=>array('label'=>Yii::t('staff','Remarks'),'width'=>20,'align'=>'L'),
			'email'=>array('label'=>Yii::t('staff','Email'),'width'=>28,'align'=>'L'),
			'leave_dt'=>array('label'=>Yii::t('staff','Leave Date'),'width'=>22,'align'=>'C'),
			'leave_reason'=>array('label'=>Yii::t('staff','Leave Reason'),'width'=>28,'align'=>'L'),
		);
	}

	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$suffix = Yii::app()->params['envSuffix'];
		$city = $this->criteria->city;
		$cutoff = isset($this->criteria->end_dt) ? $this->criteria->end_dt : date("Y/m/d");
		
		$sql = "select a.* from swo_staff_v a ";
		$where = "where a.city='".$city."'";
		if (isset($this->criteria)) {
			$where_leave_dt = '';
			$where_start_dt = '';
			if (isset($this->criteria->start_dt)) {
				$where_leave_dt = "a.leave_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			}
			if (isset($this->criteria->end_dt)) {
				$where_start_dt = "a.join_dt is null or a.join_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
//				$where_leave_dt .= (($where_leave_dt=='') ? " " : " and ")
//					."leave_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			}
			$where .= (($where=='where') ? " " : " and ")
				. " (a.leave_dt is null"
				. (($where_leave_dt=='') ? ")" : " or (".$where_leave_dt."))")
				. (($where_start_dt=='') ? "" : " and (".$where_start_dt.")");
		} else 
			$where .= (($where=='where') ? " " : " and ")." a.leave_dt is null";

		if ($where!='where') $sql .= $where;	
		$sql .= " order by a.lud desc";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
            		$model = new VacationDayForm();
			foreach ($rows as $row) {
				$temp = array();
				$temp['code'] = $row['code'];
				$temp['name'] = $row['name'];
				$temp['position'] = $row['position'];
				$temp['join_dt'] = General::toDate($row['join_dt']);
				$temp['ctrt_start_dt'] = General::toDate($row['ctrt_start_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_renew_dt'] = General::toDate($row['ctrt_renew_dt']);
//				$temp['ctrt_renew_dt'] = date('Y/m/d',strtotime('+'.$temp['ctrt_period'].' year',strtotime($temp['ctrt_start_dt'])));
				$temp['ctrt_duration'] = $temp['ctrt_start_dt'].'-'.$temp['ctrt_renew_dt'];
				$temp['email'] = $row['email'];
				$temp['leave_dt'] = General::toDate($row['leave_dt']);
				$temp['leave_reason'] = $row['leave_reason'];
				$temp['remarks'] = $row['remarks'];
				$temp['leader'] = General::getLeaderDesc($row['leader']);
				$temp['lud'] = General::toDate($row['lud']);
				
				$employee = $this->getEmployeeRecord($row['code'],$model);
				$temp['year_day'] = $employee['year_day'];
				$temp['education'] = $employee['education'];
				$temp['leave_days'] = $employee['leave_days'];
				$temp['staff_type'] = General::getStaffTypeDesc($employee['staff_type']);
				
				$this->data[] = $temp;
			}
		}
		return true;
	}

	private function getEmployeeRecord($code,&$model) {
		$education = array(
            ""=>"",
            "Primary school"=>Yii::t("staff","Primary school"),
            "Junior school"=>Yii::t("staff","Junior school"),
            "High school"=>Yii::t("staff","High school"),
            "Technical school"=>Yii::t("staff","Technical school"),
            "College school"=>Yii::t("staff","College school"),
            "Undergraduate"=>Yii::t("staff","Undergraduate"),
            "Graduate"=>Yii::t("staff","Graduate"),
            "Doctorate"=>Yii::t("staff","Doctorate")
        );
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id,a.year_day, a.education,a.staff_type,b.dept_class from hr$suffix.hr_employee a LEFT JOIN hr$suffix.hr_dept b on a.position=b.id where a.code='$code' limit 1";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$row['staff_type'] = empty($row['staff_type'])?$row['dept_class']:$row['staff_type'];
			$model->setEmployeeList($row['id']);
			$row['leave_days'] = $model->getVacationSum();//剩餘年假天數
			$row['year_day'] = $model->getSumDay();//總年假天數
			$row['education'] = $education[$row['education']];
			return $row;
		} else {
			return array('year_day'=>'', 'education'=>'', 'leave_days'=>'', 'staff_type'=>'');
		}
	}

	public function getLeaveDays($id, $joindt, $cutoff) {
		$result = 0;
		$yearday = 0;
		$leaveday = 0;
		$suffix = Yii::app()->params['envSuffix'];

		$mthday = date("m/d",strtotime($cutoff));
		$lastyr = (date("m/d",strtotime($joindt))>$mthday);
		$year = date("Y",strtotime($cutoff));
		if ($lastyr) $year--;

		$d1 = new DateTime($cutoff);
		$d2 = new DateTime($joindt);
		$diff = $d2->diff($d1);
		
		if ($diff->y > 0) {
			$sql = "select a.id, a.year_day
					from hr$suffix.hr_employee a 
					where a.id=$id
					";
			$row0 = Yii::app()->db->createCommand($sql)->queryRow();
			if ($row0 !== false) {
				$yearday += floatval($row0["year_day"]);
			
				$sql = "select sum(add_num) as sumday 
						from hr$suffix.hr_staff_year
						where employee_id=$id and year=$year 
						";
				$row1 = Yii::app()->db->createCommand($sql)->queryRow();
				if ($row1 !== false) $yearday += floatval($row1["sumday"]);
			
				$startdt = $year.'/'.date("m/d",strtotime($joindt)).' 00:00:00';
				$enddt = ($year+1).'/'.date("m/d",strtotime($joindt)).' 23:59:59';
//				$enddt = date("Y/m/d",strtotime($cutoff)).' 23:59:59';
				$sql = "select sum(a.log_time) as sumleave
						from hr$suffix.hr_employee_leave a
						left outer join hr$suffix.hr_vacation b on a.vacation_id = b.id
						where a.start_time > '$startdt' and a.start_time <= '$enddt' and a.status not in (0,3)
						and b.vaca_type='E' and a.employee_id=$id
						";
				$row2 = Yii::app()->db->createCommand($sql)->queryRow();
				$leaveday =($row2 !== false) ? $row2['sumleave'] : 0;
				$result = $yearday - $leaveday;
			}
		}
		
		return $result;
	}
	
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>
