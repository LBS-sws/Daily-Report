<?php

class SupplierList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('supplier','ID'),
			'code'=>Yii::t('supplier','Code'),
			'name'=>Yii::t('supplier','Name'),
			'full_name'=>Yii::t('supplier','Full Name'),
			'cont_name'=>Yii::t('supplier','Contact Name'),
			'cont_phone'=>Yii::t('supplier','Contact Phone'),
			'city_name'=>Yii::t('misc','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name 
				from swo_supplier a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city)
			";
		$sql2 = "select count(a.id)
				from swo_supplier a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'full_name':
					$clause .= General::getSqlConditionClause('a.full_name',$svalue);
					break;
				case 'cont_name':
					$clause .= General::getSqlConditionClause('a.cont_name',$svalue);
					break;
				case 'cont_phone':
					$clause .= General::getSqlConditionClause('a.cont_phone',$svalue);
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
				$this->attr[] = array(
					'id'=>$record['id'],
					'code'=>$record['code'],
					'name'=>$record['name'],
					'full_name'=>$record['full_name'],
					'cont_name'=>$record['cont_name'],
					'cont_phone'=>$record['cont_phone'],
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a10'] = $this->getCriteria();
		return true;
	}

}
