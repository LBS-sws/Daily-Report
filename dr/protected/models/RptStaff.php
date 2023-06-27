<?php
class RptStaff extends ReportData2 {
	public function fields() {
		return array(
			'lud'=>array('label'=>Yii::t('staff','Entry Date'),'width'=>18,'align'=>'L'),
			'code'=>array('label'=>Yii::t('staff','Code'),'width'=>15,'align'=>'L'),
			'name'=>array('label'=>Yii::t('staff','Name'),'width'=>30,'align'=>'L'),
			'city_name'=>array('label'=>Yii::t('staff','City'),'width'=>30,'align'=>'L'),
			'office_name'=>array('label'=>Yii::t('staff','Office Name'),'width'=>30,'align'=>'L'),
			'department'=>array('label'=>Yii::t('staff','Department'),'width'=>30,'align'=>'L'),
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
        $city_allow = City::model()->getDescendantList($city);
        $city_allow .= (empty($city_allow)) ? "'$city'" : ",'$city'";
        $localOffice = Yii::t("staff","local office");
		$cutoff = isset($this->criteria->end_dt) ? $this->criteria->end_dt : date("Y/m/d");
		
		$start_dt = isset($this->criteria->start_dt) ? $this->criteria->start_dt : '2000-01-01';
		$end_dt = isset($this->criteria->end_dt) ? $this->criteria->end_dt : date('Y-m-d');
		
		$sql = "select
					e.employee_id as id,
					e.code,
					e.name,
					z.name AS position,
					e.staff_type,
					if((e.staff_leader='Group Leader'),'GROUP',if((e.staff_leader='Team Leader'),'TEAM','NIL')) AS leader,
					if((isnull(e.entry_time) or (e.entry_time='')),NULL,ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d'))) AS join_dt,
					e.start_time AS ctrt_start_dt,
					timestampdiff(MONTH,e.start_time,(ifnull(str_to_date(e.end_time,'%Y/%m/%d'),str_to_date(e.end_time,'%Y-%m-%d')) + interval 1 day)) AS ctrt_period,
					if((isnull(e.end_time) or (e.end_time='')),NULL,(ifnull(str_to_date(e.end_time,'%Y/%m/%d'),str_to_date(e.end_time,'%Y-%m-%d')) + interval 1 day)) AS ctrt_renew_dt,
					e.email,
				if((isnull(f.leave_time) or (f.leave_time='')),
						NULL,
						if(ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) < date_add('$start_dt', interval 1 month),
							ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')),NULL)
					) AS leave_dt,
					if((isnull(f.leave_time) or (f.leave_time='')),
						'',
						if(ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) < date_add('$start_dt', interval 1 month),
							f.leave_reason, '')
					) AS leave_reason,
					e.remark AS remarks,
					e.city,
					y.name AS department,
					if(e.office_id=0,'{$localOffice}',office.name) AS office_name,
					e.lcu,
					e.luu,
					e.lcd,
					e.lud,
					z.dept_class
				from (
					select 
						a.id as employee_id, a.office_id, a.code, a.name, a.position, a.staff_type, a.staff_leader, a.entry_time, a.start_time, a.end_time, a.email, 
						a.leave_time, a.leave_reason, a.remark, a.city, a.department, a.lcu, a.luu, a.lcd, a.lud
					from hr$suffix.hr_employee a
					where a.city in ({$city_allow}) and a.id not in (
						select w.employee_id
						from hr$suffix.hr_employee_operate w
						left outer join hr$suffix.hr_employee_operate x on w.employee_id=x.employee_id and x.id > w.id
						where x.id is null and w.lud > '$end_dt 23:59:59' and w.city in ({$city_allow})
					)
				union
					select 
						b.employee_id, b.office_id, b.code, b.name, b.position, b.staff_type, b.staff_leader, b.entry_time, b.start_time, b.end_time, b.email, 
						b.leave_time, b.leave_reason, b.remark, b.city, b.department, b.lcu, b.luu, b.lcd, b.lud
					from hr$suffix.hr_employee_operate b
					left outer join hr$suffix.hr_employee_operate c on b.employee_id=c.employee_id and c.id > b.id
					where c.id is null and b.lud > '$end_dt 23:59:59' and b.city in ({$city_allow})
				) e
				inner join hr$suffix.hr_employee f on f.id = e.employee_id
				left join hr$suffix.hr_dept z on e.position = z.id 
				left join hr$suffix.hr_dept y on e.department = y.id
				left join hr$suffix.hr_office office on e.office_id = office.id
				where (ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) is null or 
				ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) < date_add('$start_dt', interval 1 month))
				and (ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) is null or 
				ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) >= '$start_dt')
				order by e.city,e.lud desc
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
       		$model = new VacationDayForm();
			foreach ($rows as $row) {
				$temp = array();
				$temp['code'] = $row['code'];
				$temp['name'] = $row['name'];
				$temp['position'] = $row['position'];
				$temp['city_name'] = General::getCityName($row['city']);
				$temp['office_name'] = $row['office_name'];
				$temp['department'] = $row['department'];
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
//				$temp['staff_type'] = General::getStaffTypeDesc(strtoupper($employee['staff_type']));
				$staff_type = empty($row['staff_type'])?$row['dept_class']:$row['staff_type'];
				$temp['staff_type'] = General::getStaffTypeDesc(strtoupper($staff_type));
				
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
