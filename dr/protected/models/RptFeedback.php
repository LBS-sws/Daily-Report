<?php

class RptFeedback extends ReportData2 {
			'city_name'=>array('label'=>Yii::t('feedback','City'),'width'=>15,'align'=>'C'),
			'feedback_dt'=>array('label'=>Yii::t('feedback','Feedback Date'),'width'=>20,'align'=>'C'),
			'status'=>array('label'=>Yii::t('feedback','Status'),'width'=>10,'align'=>'C'),
	public function retrieveData() {
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select c.name as city_name, a.request_dt, a.feedback_dt, a.status, b.feedback_cat, b.feedback 
				from (swo_mgr_feedback a inner join security$suffix.sec_city c on a.city = c.code) 
					left outer join swo_mgr_feedback_rmk b on a.id = b.feedback_id
				where a.id > 0 
		";
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
		$sql .= " order by a.city, a.request_dt, b.feedback_cat";
			$fbf = new FeedbackForm;
			$cats = $fbf->cats;
			foreach ($rows as $row) {
				$temp['feedback_cat'] = $row['feedback_cat']==null ? '' : Yii::t('app',$cats[$row['feedback_cat']]);
				$temp['feedback'] = $row['feedback'];
}
?>