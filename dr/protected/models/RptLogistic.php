<?php
class RptLogistic extends ReportData2 {
			'company_name'=>array('label'=>Yii::t('logistic','Customer'),'width'=>30,'align'=>'L'),
			'log_dt'=>array('label'=>Yii::t('logistic','Date'),'width'=>18,'align'=>'C'),
			'follow_staff'=>array('label'=>Yii::t('logistic','Resp. Staff'),'width'=>22,'align'=>'L'),
			'location'=>array('label'=>Yii::t('logistic','Location'),'width'=>30,'align'=>'L'),
			'task'=>array('label'=>Yii::t('logistic','Task'),'width'=>30,'align'=>'L'),
			'qty'=>array('label'=>Yii::t('logistic','Quantity'),'width'=>15,'align'=>'C'),
			'deadline'=>array('label'=>Yii::t('logistic','Deadline'),'width'=>18,'align'=>'C'),
			'repair'=>array('label'=>Yii::t('logistic','Repair Items'),'width'=>30,'align'=>'L'),
			'reason'=>array('label'=>Yii::t('logistic','Job Status'),'width'=>30,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('logistic','Remarks'),'width'=>30,'align'=>'L'),
		);
//	public function line_group() {
//		return array(
//			'seq','company_name','log_dt','follow_staff','pay_method','location','finish','deadline','reason',
//		);
//	}
	
	public function report_structure() {
		return array(
			'seq',
			'company_name',
			'address',
			'log_dt',
			'follow_staff',
			'pay_method',
			'location',
			array(
				'task',
				'task_type',
				'qty',
				'finish',
				'deadline',
			),
			'repair',
			'reason',
			'remarks',
		);
	}
	
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*, b.description as location_name 
					from swo_logistic a 
					left outer join swo_location b on a.location=b.id 
		";
		if (isset($this->criteria)) {
		$sql .= " order by a.log_dt, a.seq";
				$temp = array();
				$temp['follow_staff'] = $row['follow_staff'];
				$temp['location'] = '['.$row['location_name'].'] '.$row['location_dtl'];
				$temp['finish'] = $row['finish']=='Y' ? Yii::t('misc','Yes') : Yii::t('misc','No');
				$temp['deadline'] = General::isDate($row['deadline']) ? General::toDate($row['deadline']) : $row['deadline'];
				$temp['reason'] = $row['reason'];
				$temp['address'] = $row['address'];
				$temp['repair'] = $row['repair'];
				$temp['remarks'] = $row['remarks'];

				$detail = array();
				$sql = "select a.*, b.description as task_name, b.task_type from swo_logistic_dtl a
					left outer join swo_task b on a.task=b.id 
					where a.log_id=".$row['id']." and a.city='".$city."'";
				$drows = Yii::app()->db->createCommand($sql)->queryAll();
				if (empty($drows) ) {
					$tempd = array();
					$tempd['task'] = '';
					$tempd['qty'] = '';
					$tempd['task_type'] = '';
					$tempd['finish'] = '';
					$tempd['deadline'] = '';
					$detail[] = $tempd;
				} else {
					foreach ($drows as $drow) {
						$tempd = array();
						$tempd['task'] = $drow['task_name'];
						$tempd['task_type'] = '';
						switch ($drow['task_type']) {
							case 'NIL': $tempd['task_type'] = Yii::t('code','Nil'); break;
							case 'PAPER': $tempd['task_type'] = Yii::t('code','Paper'); break;
							case 'SOAP': $tempd['task_type'] = Yii::t('code','Soap'); break;
							case 'OTHER': $tempd['task_type'] = Yii::t('code','Other'); break;
						};
						$tempd['qty'] = $drow['qty'];
						$tempd['finish'] = (empty($drow['finish'])) ? '' : ($drow['finish']=='Y' ? Yii::t('misc','Yes') : Yii::t('misc','No'));
						$tempd['deadline'] = (empty($drow['deadline'])) ? '' : General::toDate($drow['deadline']);
						$detail[] = $tempd;
					}
				}
				$temp['detail'] = $detail; 

				$this->data[] = $temp;

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>