<?php

class FeedbackList extends CListPageModel
{
	public $cats = array(
			'A1~'=>'Customer Service',
			'A2~'=>'Complaint Cases',
			'A3~'=>'Customer Enquiry',
			'A4~'=>'Product Delivery',
			'A5~'=>'QC Record',
			'A6~'=>'Staff Info',
			'A7~'=>'Others',
		);

	public function attributeLabels()
	{
		return array(	
			'request_dt'=>Yii::t('feedback','Request Date'),
			'feedback_dt'=>Yii::t('feedback','Feedback Date'),
			'feedback_cat'=>Yii::t('feedback','Feedback Type'),
			'status'=>Yii::t('feedback','Status'),
			'feedbacker'=>Yii::t('feedback','Feedback Person'),
			'city_name'=>Yii::t('misc','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = empty($this->city)?Yii::app()->user->city_allow():"'$this->city'";
		$sql1 = "select a.*, b.disp_name,f.name as city_name
				from swo_mgr_feedback a 
				LEFT JOIN security$suffix.sec_user b on a.username=b.username 
				LEFT JOIN security$suffix.sec_city f on a.city=f.code 
				where a.city in ($city_allow)
			";
		$sql2 = "select count(a.id)
				from swo_mgr_feedback a 
				LEFT JOIN security$suffix.sec_user b on a.username=b.username 
				LEFT JOIN security$suffix.sec_city f on a.city=f.code 
				where a.city in ($city_allow)
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('f.name', $svalue);
					break;
				case 'feedbacker':
					$clause .= General::getSqlConditionClause('b.disp_name', $svalue);
					break;
				case 'request_dt':
					$clause .= General::getSqlConditionClause('a.request_dt', $svalue);
					break;
				case 'feedback_dt':
					$clause .= General::getSqlConditionClause('a.feedback_dt', $svalue);
					break;
				case 'status':
					$field = "(select case a.status when 'Y' then '".General::getFeedbackStatusDesc('Y')."' 
							else '".General::getFeedbackStatusDescDesc('N')."'  
						end)";
					$clause .= General::getSqlConditionClause($field, $svalue);
					break;
				case 'feedback_cat':
					$field = "";
					foreach ($this->cats as $cat=>$desc) {
						if (empty($field)) {
							$field = "replace(a.feedback_cat_list,'".$cat."','".Yii::t('app',$desc).",')";
						} else {
							$field = "replace(".$field.",'".$cat."','".Yii::t('app',$desc).",')";
						}
					}
					$clause .= General::getSqlConditionClause($field, $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch($this->orderField) {
				case 'feedbacker' : $orderfield = 'disp_name'; break;
				case 'feedback_cat' : $orderfield = 'feedback_cat_list'; break;
				default : $orderfield = $this->orderField; break;
			}
			$order .= " order by $orderfield ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by request_dt desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$tmp = $record['feedback_cat_list'];
					foreach ($this->cats as $cat=>$desc) {
						$tmp = str_replace($cat,Yii::t('app',$desc).',',$tmp);
					}
					$this->attr[] = array(
						'id'=>$record['id'],
						'city_name'=>$record['city_name'],
						'request_dt'=>$record['request_dt'],
						'feedback_dt'=>$record['feedback_dt'],
						'status'=>General::getFeedbackStatusDesc($record['status']),
						'feedback_cat'=>$tmp,
						'feedbacker'=>$record['disp_name'],
						'username'=>$record['username'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a08'] = $this->getCriteria();
		return true;
	}

}
