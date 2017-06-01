<?php

class RegisterList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'station_name'=>Yii::t('register','Station'),
			'email'=>Yii::t('register','Email'),
			'city'=>Yii::t('register','City'),
			'status'=>Yii::t('register','Status'),
			'lcd'=>Yii::t('register','Request Date'),
			'lud'=>Yii::t('register','Register Date'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$sql1 = "select a.*, b.name as city_name
				from swo_station_request a, swo_city b 
				where a.city=b.code 
			";
		$sql2 = "select count(a.email)
				from swo_station_request  a, swo_city b
				where a.city=b.code 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'station_name':
					$clause .= General::getSqlConditionClause('a.station_name',$svalue);
					break;
				case 'email':
					$clause .= General::getSqlConditionClause('a.email',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
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
			$timelimit = "-".Yii::app()->params['validRegDuration'];
			foreach ($records as $k=>$record) {
//				if ($k >= $startrow && ($itemcnt <= $this->noOfItem || $this->noOfItem == 0)) {
					$sts = Yii::t('register','Pending');
					if (!empty($record['station_id']) && $record['station_id'] != '--VOID--') $sts = Yii::t('register','Complete');
					if (!empty($record['station_id']) && $record['station_id'] == '--VOID--') $sts = Yii::t('register','Void');
					if (empty($record['station_id']) && strtotime($timelimit) > strtotime($record['lcd'])) $sts = Yii::t('register','Expired');
					$rdate = (empty($record['station_id'])) ? '' : $record['lud'];
					$this->attr[] = array(
						'req_key'=>$record['req_key'],
						'email'=>$record['email'],
						'station_name'=>$record['station_name'],
						'city'=>$record['city_name'],
						'station_id'=>$record['station_id'],
						'lcd'=>$record['lcd'],
						'lud'=>$rdate,
						'lud2'=>$record['lud'],
						'status'=>$sts,
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['criteria_d04'] = $this->getCriteria();
		return true;
	}

}
