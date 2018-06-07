<?php
class RptStaff extends ReportData2 {	public function fields() {		return array(
			'lud'=>array('label'=>Yii::t('staff','Entry Date'),'width'=>18,'align'=>'L'),			'code'=>array('label'=>Yii::t('staff','Code'),'width'=>15,'align'=>'L'),			'name'=>array('label'=>Yii::t('staff','Name'),'width'=>30,'align'=>'L'),			'position'=>array('label'=>Yii::t('staff','Position'),'width'=>30,'align'=>'L'),			'staff_type'=>array('label'=>Yii::t('staff','Staff Type'),'width'=>20,'align'=>'C'),
			'leader'=>array('label'=>Yii::t('staff','Team/Group Leader'),'width'=>20,'align'=>'C'),
			'join_dt'=>array('label'=>Yii::t('staff','Join Date'),'width'=>18,'align'=>'C'),			'ctrt_duration'=>array('label'=>Yii::t('staff','Cont. Duration'),'width'=>40,'align'=>'C'),			'ctrt_period'=>array('label'=>Yii::t('staff','Cont. Period'),'width'=>18,'align'=>'C'),			'ctrt_renew_dt'=>array('label'=>Yii::t('staff','Cont. Renew Date'),'width'=>18,'align'=>'C'),
			'remarks'=>array('label'=>Yii::t('staff','Remarks'),'width'=>20,'align'=>'L'),
			'email'=>array('label'=>Yii::t('staff','Email'),'width'=>28,'align'=>'L'),			'leave_dt'=>array('label'=>Yii::t('staff','Leave Date'),'width'=>22,'align'=>'C'),			'leave_reason'=>array('label'=>Yii::t('staff','Leave Reason'),'width'=>28,'align'=>'L'),		);	}
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select * from swo_staff_v ";		$where = "where city='".$city."'";
		if (isset($this->criteria)) {
			$where_leave_dt = '';
			$where_start_dt = '';			if (isset($this->criteria->start_dt)) {
				$where_leave_dt = "leave_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			}			if (isset($this->criteria->end_dt)) {				$where_start_dt = "join_dt is null or join_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
//				$where_leave_dt .= (($where_leave_dt=='') ? " " : " and ")
//					."leave_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			}
			$where .= (($where=='where') ? " " : " and ")
				. " (leave_dt is null"
				. (($where_leave_dt=='') ? ")" : " or (".$where_leave_dt."))")
				. (($where_start_dt=='') ? "" : " and (".$where_start_dt.")");
		} else 
			$where .= (($where=='where') ? " " : " and ")." leave_dt is null";
		if ($where!='where') $sql .= $where;	
		$sql .= " order by lud desc";		$rows = Yii::app()->db->createCommand($sql)->queryAll();		if (count($rows) > 0) {			foreach ($rows as $row) {				$temp = array();				$temp['code'] = $row['code'];				$temp['name'] = $row['name'];				$temp['position'] = $row['position'];				$temp['join_dt'] = General::toDate($row['join_dt']);				$temp['ctrt_start_dt'] = General::toDate($row['ctrt_start_dt']);				$temp['ctrt_period'] = $row['ctrt_period'];				$temp['ctrt_renew_dt'] = General::toDate($row['ctrt_renew_dt']);
//				$temp['ctrt_renew_dt'] = date('Y/m/d',strtotime('+'.$temp['ctrt_period'].' year',strtotime($temp['ctrt_start_dt'])));
				$temp['ctrt_duration'] = $temp['ctrt_start_dt'].'-'.$temp['ctrt_renew_dt'];
				$temp['email'] = $row['email'];				$temp['leave_dt'] = General::toDate($row['leave_dt']);				$temp['leave_reason'] = $row['leave_reason'];				$temp['remarks'] = $row['remarks'];				$temp['staff_type'] = General::getStaffTypeDesc($row['staff_type']);
				$temp['leader'] = General::getLeaderDesc($row['leader']);
				$temp['lud'] = General::toDate($row['lud']);
				$this->data[] = $temp;			}		}		return true;	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>