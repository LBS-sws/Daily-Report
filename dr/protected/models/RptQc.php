<?php
class RptQc extends ReportData2 {
			'service_score'=>array('label'=>Yii::t('qc','Service Score'),'width'=>15,'align'=>'C'),
			'cust_comment'=>array('label'=>Yii::t('qc','Customer Comment'),'width'=>30,'align'=>'C'),
			'cust_sign'=>array('label'=>Yii::t('qc','Signature'),'width'=>22,'align'=>'L'),
			'qc_staff'=>array('label'=>Yii::t('qc','Staff-QC'),'width'=>30,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('enquiry','Remarks'),'width'=>40,'align'=>'L'),
		);
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*
					from swo_qc a 
		";
		if (isset($this->criteria)) {
		$sql .= " order by entry_dt desc";
				$temp['input_dt'] = General::toDate($row['input_dt']);
				$temp['service_score'] = $row['service_score'];
				$temp['cust_comment'] = $row['cust_comment'];
				$temp['qc_result'] = (is_numeric($row['qc_result']))
						? (($row['qc_result'] < 70) ? $row['qc_result'].' **' : $row['qc_result'])
						: $row['qc_result'];
				$temp['env_grade'] = $row['env_grade'];
				$temp['qc_dt'] = General::toDate($row['qc_dt']);
				$temp['cust_sign'] = $row['cust_sign'];
				$this->data[] = $temp;

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>