<?php

class CustomerList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('customer','ID'),
			'code'=>Yii::t('customer','Code'),
			'name'=>Yii::t('customer','Customer Name'),
			'full_name'=>Yii::t('customer','Registered Company Name'),
			'cont_name'=>Yii::t('customer','Contact Name'),
			'cont_phone'=>Yii::t('customer','Contact Phone'),
			'city_name'=>Yii::t('misc','City'),
            'status'=>Yii::t('customer','Status'),
            'group_id'=>Yii::t('customer','Group ID'),
            'group_name'=>Yii::t('customer','Group Name'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name 
				from swo_company a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city)
			";
		$sql2 = "select count(a.id)
				from swo_company a, security$suffix.sec_city b
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
				case 'group_id':
					$clause .= General::getSqlConditionClause('a.group_id',$svalue);
					break;
				case 'group_name':
					$clause .= General::getSqlConditionClause('a.group_name',$svalue);
					break;
				case 'status':
					$clause .= self::getStatusSQL($svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order ="order by id desc";
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
					'group_id'=>$record['group_id'],
					'group_name'=>$record['group_name'],
					'status'=>self::getStatusStr($record['status']),
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a01'] = $this->getCriteria();
		return true;
	}

	public static function getStatusStr($status){
	    $model = new CustomerForm();
	    $list = $model->getStatusList();
        $status="".$status;
	    if(key_exists($status,$list)){
	        return $list[$status];
        }else{
	        return current($list);
        }
    }

	public static function getStatusSQL($status){

	    $model = new CustomerForm();
	    $list = $model->getStatusList();
        $status="".$status;
        $idArr=array(-1);
        foreach ($list as $key=>$value){
            if (strpos($value,$status)!==false){
                $idArr[]=$key;
            }
        }

        $return = " and a.status in (".implode(",",$idArr).")";
        return $return;
    }
}
