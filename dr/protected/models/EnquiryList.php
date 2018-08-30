<?php

class EnquiryList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'contact_dt'=>Yii::t('enquiry','Date'),
			'customer'=>Yii::t('enquiry','Customer'),
			'type'=>Yii::t('enquiry','Type'),
			'source'=>Yii::t('enquiry','Source'),
			'city_name'=>Yii::t('misc','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.description, c.name as city_name  
				from (swo_enquiry a inner join security$suffix.sec_city c on a.city=c.code) 
				left outer join swo_customer_type b on a.type=b.id 
				where city in ($city)  
			";
		$sql2 = "select count(a.id)
				from (swo_enquiry a inner join security$suffix.sec_city c on a.city=c.code) 
				left outer join swo_customer_type b on a.type=b.id  
				where city in ($city)  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
					break;
				case 'customer':
					$clause .= General::getSqlConditionClause('a.customer', $svalue);
					break;
				case 'type':
					$clause .= General::getSqlConditionClause('b.description', $svalue);
					break;
				case 'source':
					$field = "(select case a.source_code when '1' then concat('".General::getSourceDesc('1')."',a.source) 
							when '2' then concat('".General::getSourceDesc('2')."',a.source) 
							when '3' then concat('".General::getSourceDesc('3')."',a.source) 
							else concat('".General::getSourceDesc('4')."', a.source) 
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
			foreach ($records as $k=>$record) {
				$type = empty($record['description']) ? $record['type'] : $record['description'];
				$this->attr[] = array(
					'id'=>$record['id'],
					'contact_dt'=>General::toDate($record['contact_dt']),
					'customer'=>$record['customer'],
					'type'=>$type,
					'source'=>General::getSourceDesc($record['source_code']).
						(empty($record['source']) ? '' : '('.$record['source'].')'),
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a04'] = $this->getCriteria();
		return true;
	}

}
