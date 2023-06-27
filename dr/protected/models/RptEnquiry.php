<?php
class RptEnquiry extends ReportData2 {
	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
			'contact_dt'=>array('label'=>Yii::t('enquiry','Contact Date'),'width'=>22,'align'=>'C'),
			'customer'=>array('label'=>Yii::t('enquiry','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('enquiry','Nature'),'width'=>15,'align'=>'C'),
			'type'=>array('label'=>Yii::t('enquiry','Type'),'width'=>22,'align'=>'C'),
			'contact'=>array('label'=>Yii::t('enquiry','Contact Person'),'width'=>30,'align'=>'L'),
			'tel_no'=>array('label'=>Yii::t('enquiry','Contact Phone'),'width'=>30,'align'=>'L'),
			'address'=>array('label'=>Yii::t('enquiry','Contact Address'),'width'=>40,'align'=>'L'),
			'source'=>array('label'=>Yii::t('enquiry','Source'),'width'=>30,'align'=>'L'),
			'record_by'=>array('label'=>Yii::t('enquiry','Record By'),'width'=>30,'align'=>'L'),
			'follow_staff'=>array('label'=>Yii::t('enquiry','Resp. Staff'),'width'=>30,'align'=>'L'),
			'follow_dt'=>array('label'=>Yii::t('enquiry','Follow-up Date'),'width'=>18,'align'=>'C'),
			'follow_result'=>array('label'=>Yii::t('enquiry','Result'),'width'=>40,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('enquiry','Remarks'),'width'=>40,'align'=>'L'),
		);
	}

	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
        $city_allow = City::model()->getDescendantList($city);
        $city_allow .= (empty($city_allow)) ? "'$city'" : ",'$city'";
		$sql = "select a.*, b.description, c.description as nature_desc 
					from (swo_enquiry a left outer join swo_customer_type b
					on a.type=b.id)
					left outer join swo_nature c
					on a.nature_type=c.id
		";
		$where = "where a.city in ({$city_allow}) ";
		if (isset($this->criteria)) {
			if (isset($this->criteria->start_dt))
				$where .= (($where=='where') ? " " : " and ")."a.contact_dt>='".General::toDate($this->criteria->start_dt)."'";
			if (isset($this->criteria->end_dt))
				$where .= (($where=='where') ? " " : " and ")."a.contact_dt<='".General::toDate($this->criteria->end_dt)."'";
		}
		if ($where!='where') $sql .= $where;	
		$sql .= " order by a.city,a.contact_dt";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
                $temp['city_name'] = General::getCityName($row["city"]);
				$temp['contact_dt'] = General::toDate($row['contact_dt']);
				$temp['follow_staff'] = $row['follow_staff'];
				$temp['record_by'] = $row['record_by'];
				$temp['follow_result'] = $row['follow_result'];
				$temp['type'] = $row['description'];
				$temp['nature'] = $row['nature_desc'];
				$temp['contact'] = $row['contact'];
				$temp['tel_no'] = $row['tel_no'];
				$temp['address'] = $row['address'];
				$temp['customer'] = $row['customer'];
				$temp['follow_dt'] = General::toDate($row['follow_dt']);
				$temp['source'] = General::getSourceDesc($row['source_code']).
							(empty($row['source']) ? '' : '('.$row['source'].')');
				$temp['remarks'] = $row['remarks'];
				$this->data[] = $temp;
			}
		}
		return true;
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>