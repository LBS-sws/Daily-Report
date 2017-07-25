<?php

class RptFeedback extends ReportData2 {	public function fields() {		return array(
			'city_name'=>array('label'=>Yii::t('feedback','City'),'width'=>15,'align'=>'C'),			'request_dt'=>array('label'=>Yii::t('feedback','Request Date'),'width'=>18,'align'=>'C'),
			'feedback_dt'=>array('label'=>Yii::t('feedback','Feedback Date'),'width'=>20,'align'=>'C'),
			'status'=>array('label'=>Yii::t('feedback','Status'),'width'=>10,'align'=>'C'),			'feedback_cat'=>array('label'=>Yii::t('feedback','Feedback Type'),'width'=>15,'align'=>'C'),			'feedback'=>array('label'=>Yii::t('feedback','Feedback'),'width'=>50,'align'=>'L'),		);	}
	public function retrieveData() {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select c.name as city_name, a.request_dt, a.feedback_dt, a.status, b.feedback_cat, b.feedback 
				from (swo_mgr_feedback a inner join security$suffix.sec_city c on a.city = c.code) 
					left outer join swo_mgr_feedback_rmk b on a.id = b.feedback_id
				where a.id > 0 
		";		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and a.request_dt>='".General::toDate($this->criteria->start_dt)."'";
			if (isset($this->criteria->end_dt))
				$where .= " and a.request_dt<='".General::toDate($this->criteria->end_dt)."'";
			if (isset($this->criteria->type) && !empty($this->criteria->type)) {
				if (!General::isJSON($this->criteria->type)) {
					$where .= " and b.feedback_cat='".$this->criteria->type."'";
				} else {
					$type_list = '';
					$types = json_decode($this->criteria->type);
					foreach ($types as $type) {
						$type_list .= (($type_list=="") ? "'" : ",'").$type."'";
					}
					if ($type_list!='') $where .= " and b.feedback_cat in (".$type_list.")";
				}
			}
			if (isset($this->criteria->city) && !empty($this->criteria->city)) {
				if (!General::isJSON($this->criteria->city)) {
					$where .= " and a.city='".$this->criteria->city."'";
				} else {
					$city_list = '';
					$cities = json_decode($this->criteria->city);
					foreach ($cities as $city) {
						$city_list .= (($city_list=="") ? "'" : ",'").$city."'";
					}
					if ($city_list!='') $where .= " and a.city in (".$city_list.")";
				}
			}
				
			if (!empty($where)) $sql .= $where;
		}
		$sql .= " order by a.city, a.request_dt, b.feedback_cat";		$rows = Yii::app()->db->createCommand($sql)->queryAll();		if (count($rows) > 0) {
			$fbf = new FeedbackForm;
			$cats = $fbf->cats;
			foreach ($rows as $row) {				$temp = array();				$temp['city_name'] = $row['city_name'];				$temp['request_dt'] = General::toDate($row['request_dt']);				$temp['feedback_dt'] = General::toDateTime($row['feedback_dt']);				$temp['status'] = ($row['status']=='Y') ? Yii::t('feedback','Done') : Yii::t('feedback','Not Yet');
				$temp['feedback_cat'] = $row['feedback_cat']==null ? '' : Yii::t('app',$cats[$row['feedback_cat']]);
				$temp['feedback'] = $row['feedback'];				$this->data[] = $temp;			}		}		return true;	}
}
?>