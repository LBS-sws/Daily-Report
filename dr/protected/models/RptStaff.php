<?php
class RptStaff extends ReportData2 {
			'lud'=>array('label'=>Yii::t('staff','Entry Date'),'width'=>18,'align'=>'L'),
			'leader'=>array('label'=>Yii::t('staff','Team/Group Leader'),'width'=>20,'align'=>'C'),
			'join_dt'=>array('label'=>Yii::t('staff','Join Date'),'width'=>18,'align'=>'C'),
			'remarks'=>array('label'=>Yii::t('staff','Remarks'),'width'=>20,'align'=>'L'),
			'email'=>array('label'=>Yii::t('staff','Email'),'width'=>28,'align'=>'L'),
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select * from swo_staff ";
		if (isset($this->criteria)) {
			$where_leave_dt = '';
			$where_start_dt = '';
				$where_leave_dt = "leave_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			}
//				$where_leave_dt .= (($where_leave_dt=='') ? " " : " and ")
//					."leave_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			}
			$where .= (($where=='where') ? " " : " and ")
				. " (leave_dt is null"
				. (($where_leave_dt=='') ? ")" : " or (".$where_leave_dt."))")
				. (($where_start_dt=='') ? "" : " and (".$where_start_dt.")");
		} else 
			$where .= (($where=='where') ? " " : " and ")." leave_dt is null";

		$sql .= " order by lud desc";
//				$temp['ctrt_renew_dt'] = date('Y/m/d',strtotime('+'.$temp['ctrt_period'].' year',strtotime($temp['ctrt_start_dt'])));
				$temp['ctrt_duration'] = $temp['ctrt_start_dt'].'-'.$temp['ctrt_renew_dt'];
				$temp['email'] = $row['email'];
				$temp['leader'] = General::getLeaderDesc($row['leader']);
				$temp['lud'] = General::toDate($row['lud']);
				$this->data[] = $temp;

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>