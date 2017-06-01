<?php

class TaskList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'description'=>Yii::t('code','Description'),
			'city_name'=>Yii::t('misc','City'),
			'type'=>Yii::t('code','Type'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name 
				from swo_task a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city) 
			";
		$sql2 = "select count(a.id)
				from swo_task a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'description':
					$clause .= General::getSqlConditionClause('a.description',$svalue);
					break;
				case 'type':
					$clause .= General::getSqlConditionClause('a.task_type',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch ($this->orderField) {
				case 'type': $orderf = 'a.task_type'; break;
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
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				switch($record['task_type']) {
					case 'PAPER': $type = Yii::t('code','Paper'); break;
					case 'SOAP': $type = Yii::t('code','Soap'); break;
					case 'OTHER': $type = Yii::t('code','Other'); break;
					default: $type = '';
				}
				$this->attr[] = array(
					'id'=>$record['id'],
					'description'=>$record['description'],
					'type'=>$type,
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_c04'] = $this->getCriteria();
		return true;
	}

}
