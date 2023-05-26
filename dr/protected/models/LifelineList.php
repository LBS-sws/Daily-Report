<?php

class LifelineList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'life_date'=>Yii::t('summary','Effective Year'),
			'city_name'=>Yii::t('misc','City'),
			'life_num'=>Yii::t('summary','life num'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name 
				from swo_lifeline a
				LEFT JOIN security$suffix.sec_city b ON a.city=b.code
				where a.id>0 
			";
		$sql2 = "select count(a.id)
				from swo_lifeline a
				LEFT JOIN security$suffix.sec_city b ON a.city=b.code
				where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'life_num':
					$clause .= General::getSqlConditionClause('a.life_num',$svalue);
					break;
				case 'life_date':
					$clause .= General::getSqlConditionClause('a.life_date',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch ($this->orderField) {
				case 'type': $orderf = 'a.lifeline_type'; break;
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
					'id'=>$record['id'],
					'life_date'=>intval($record["life_date"]).Yii::t("summary","year"),
					'life_num'=>$record['life_num'],
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['lifeline_c04'] = $this->getCriteria();
		return true;
	}

}
