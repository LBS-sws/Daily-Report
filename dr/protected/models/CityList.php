<?php

class CityList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'code'=>Yii::t('code','Code'),
			'name'=>Yii::t('code','Name'),
			'region_name'=>Yii::t('code','Region'),
			'incharge'=>Yii::t('code','In Charge'),
			'ka_bool'=>Yii::t('code','city type'),
            'SARANK'=>Yii::t('code','rank for sales'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.*, b.name as region_name, c.disp_name as incharge,f.field_value as SARANK  
				from security$suffix.sec_city a left outer join security$suffix.sec_city b on a.region=b.code 
					left outer join security$suffix.sec_user c on a.incharge= c.username 
					left outer join security$suffix.sec_city_info f on a.code= f.code and f.field_id='SARANK' 
				where 1=1 
			";
		$sql2 = "select count(a.code)
				from security$suffix.sec_city a left outer join security$suffix.sec_city b on a.region=b.code 
					left outer join security$suffix.sec_user c on a.incharge= c.username 
					left outer join security$suffix.sec_city_info f on a.code= f.code and f.field_id='SARANK'
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'region_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'incharge':
					$clause .= General::getSqlConditionClause('c.disp_name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch ($this->orderField) {
				case 'code':
					$order .= " order by a.code ";
					break;
				case 'name':
					$order .= " order by a.name ";
					break;
				case 'region_name':
					$order .= " order by b.name ";
					break;
				case 'incharge':
					$order .= " order by c.disp_name ";
					break;
				case 'ka_bool':
					$order .= " order by a.ka_bool ";
					break;
			}
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
						'code'=>$record['code'],
						'name'=>$record['name'],
						'region_name'=>$record['region_name'],
						'incharge'=>$record['incharge'],
						'SARANK'=>$record['SARANK']==1?Yii::t("misc","On"):Yii::t("misc","Off"),
						'ka_bool'=>self::getCityTypeList($record['ka_bool'],true),
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_c05'] = $this->getCriteria();
		return true;
	}

	public static function getCityTypeList($key=0,$bool=false){
	    $list = array(
	        0=>Yii::t("code","city"),
	        1=>Yii::t("code","ka city"),
	        2=>Yii::t("code","area"),
        );
	    if($bool){
	        if(key_exists($key,$list)){
	            return $list[$key];
            }else{
	            return $key;
            }
        }else{
	        return $list;
        }
    }
}
