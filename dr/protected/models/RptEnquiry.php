<?php
class RptEnquiry extends ReportData2 {
			'nature'=>array('label'=>Yii::t('enquiry','Nature'),'width'=>15,'align'=>'C'),
			'type'=>array('label'=>Yii::t('enquiry','Type'),'width'=>22,'align'=>'C'),
			'contact'=>array('label'=>Yii::t('enquiry','Contact Person'),'width'=>30,'align'=>'L'),
			'tel_no'=>array('label'=>Yii::t('enquiry','Contact Phone'),'width'=>30,'align'=>'L'),
			'address'=>array('label'=>Yii::t('enquiry','Contact Address'),'width'=>40,'align'=>'L'),
			'source'=>array('label'=>Yii::t('enquiry','Source'),'width'=>30,'align'=>'L'),
			'record_by'=>array('label'=>Yii::t('enquiry','Record By'),'width'=>30,'align'=>'L'),
			'follow_staff'=>array('label'=>Yii::t('enquiry','Resp. Staff'),'width'=>30,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('enquiry','Remarks'),'width'=>40,'align'=>'L'),
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*, b.description, c.description as nature_desc 
					from (swo_enquiry a left outer join swo_customer_type b
					on a.type=b.id)
					left outer join swo_nature c
					on a.nature_type=c.id
		";
		if (isset($this->criteria)) {
		$sql .= " order by a.contact_dt";
				$temp['follow_result'] = $row['follow_result'];
				$temp['type'] = $row['description'];
				$temp['contact'] = $row['contact'];
				$temp['tel_no'] = $row['tel_no'];
				$temp['address'] = $row['address'];
				$temp['customer'] = $row['customer'];
				$temp['follow_dt'] = General::toDate($row['follow_dt']);
							(empty($row['source']) ? '' : '('.$row['source'].')');

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>