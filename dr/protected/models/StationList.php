<?php

class StationList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'station_id'=>Yii::t('station','Station ID'),
			'station_name'=>Yii::t('station','Name'),
			'city_name'=>Yii::t('station','City'),
			'status'=>Yii::t('station','Status'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.*, b.name as city_name
				from swo_station a, security$suffix.sec_city b
				where a.city=b.code
			";
		$sql2 = "select count(a.station_id)
				from swo_station a, security$suffix.sec_city b
				where a.city=b.code
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'station_id':
					$clause .= General::getSqlConditionClause('a.station_id',$svalue);
					break;
				case 'station_name':
					$clause .= General::getSqlConditionClause('a.station_name',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'status':
					$field = "(select case a.status when 'I' then '".General::getActiveStatusDesc('I')."' 
							when 'A' then '".General::getActiveStatusDesc('A')."' 
						end)";
					$clause .= General::getSqlConditionClause($field, $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
//			$startrow = ($this->noOfItem != 0) ? ($this->pageNum-1) * $this->noOfItem : 0;
//			$itemcnt = 0;
			foreach ($records as $k=>$record) {
//				if ($k >= $startrow && ($itemcnt <= $this->noOfItem || $this->noOfItem == 0)) {
					$this->attr[] = array(
						'station_id'=>$record['station_id'],
						'station_name'=>$record['station_name'],
						'status'=>$record['status'],
						'city_name'=>$record['city_name'],
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['criteria_d03'] = $this->getCriteria();
		return true;
	}

}
