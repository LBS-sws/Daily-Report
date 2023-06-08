<?php

class CitySetList extends CListPageModel
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
			'region_name'=>Yii::t('summary','end region'),
            'add_type'=>Yii::t('summary','add type'),
            'z_index'=>Yii::t('summary','z index'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.code,a.name as city_name,f.name as region_name,b.show_type,b.add_type,b.z_index 
				from security$suffix.sec_city a
				LEFT JOIN swo_city_set b ON a.code=b.code
				LEFT JOIN security$suffix.sec_city f ON f.code=b.region_code
				where 1=1  
			";
		$sql2 = "select count(a.code)
				from security$suffix.sec_city a
				LEFT JOIN swo_city_set b ON a.code=b.code
				LEFT JOIN security$suffix.sec_city f ON f.code=b.region_code
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
				case 'region_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
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
					'show_type'=>self::getCityCountList($record['show_type'],true),
					'add_type'=>self::getAddCountList($record['add_type'],true),
					'region_name'=>$record['region_name'],
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['citySet_c04'] = $this->getCriteria();
		return true;
	}

	public static function getCityCountList($type='',$bool=false){
	    $list = array(
	        0=>Yii::t("summary","No"),
	        1=>Yii::t("summary","Yes"),
        );
	    if($bool){
	        if(key_exists($type,$list)){
	            return $list[$type];
            }else{
	            return $type;
            }
        }
        return $list;
    }

	public static function getAddCountList($type='',$bool=false){
	    $list = array(
	        0=>Yii::t("summary","No"),
	        1=>Yii::t("summary","Yes"),
        );
	    if($bool){
	        if(key_exists($type,$list)){
	            return $list[$type];
            }else{
	            return $type;
            }
        }
        return $list;
    }

	public static function getCityAllList(){
        $list=array(""=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.code,a.name as city_name")
            ->from("security$suffix.sec_city a")
            ->order("a.name asc")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["code"]] = $row["city_name"];
            }
        }
        return $list;
    }
}
