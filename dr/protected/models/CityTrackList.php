<?php

class CityTrackList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'code'=>Yii::t('summary','City Code'),
			'city_name'=>Yii::t('summary','City Name'),
            'show_type'=>Yii::t('summary','show type'),
			'end_name'=>"最终统计名称",
            'z_index'=>Yii::t('summary','z index'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.code,a.name as city_name,b.show_type,b.end_name,b.z_index 
				from security$suffix.sec_city a
				LEFT JOIN swo_city_track b ON a.code=b.code
				where 1=1  
			";
		$sql2 = "select count(a.code)
				from security$suffix.sec_city a
				LEFT JOIN swo_city_track b ON a.code=b.code
				where 1=1  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'end_name':
					$clause .= General::getSqlConditionClause('b.end_name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch ($this->orderField) {
				case 'type': $orderf = 'a.code'; break;
				default: $orderf = $this->orderField; break;
			}
			$order .= " order by ".$orderf." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'code'=>$record['code'],
					'city_name'=>$record['city_name'],
					'show_type'=>CitySetList::getCityCountList($record['show_type'],true),
					'end_name'=>$record['end_name'],
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['cityTrack_c04'] = $this->getCriteria();
		return true;
	}
}
