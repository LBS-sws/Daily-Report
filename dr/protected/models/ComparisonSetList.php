<?php

class ComparisonSetList extends CListPageModel
{
    public $comparison_year;
    public $month_type;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('code','City'),
			'city'=>Yii::t('code','City'),
			'comparison_year'=>Yii::t('code','comparison_year'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, comparison_year, month_type,  pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public static function notCitySqlStr(){
	    $cityList = array("ZY","XM","WL","TY","TP","TN","TC","RN",
            "MY","KS","HN1","HK","H-N","CS","KA");
	    return implode("','",$cityList);
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
	    $this->comparison_year = (empty($this->comparison_year)||!is_numeric($this->comparison_year))?date("Y"):$this->comparison_year;
        $this->month_type = is_numeric($this->month_type)&&$this->month_type<13?$this->month_type:($this->comparison_year>=2024?date("n"):1);
        $suffix = Yii::app()->params['envSuffix'];
        $sql1 = "select b.code,b.name 
				from swo_city_set a 
				LEFT JOIN security{$suffix}.sec_city b on a.code=b.code
				where a.show_type=1 
			";
        $sql2 = "select count(a.code)
				from swo_city_set a 
				LEFT JOIN security{$suffix}.sec_city b on a.code=b.code
				where a.show_type=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by code desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = array(
                    'id'=>0,
                    'comparison_year'=>$this->comparison_year.Yii::t("report","Year"),
                    'code'=>$record['code'],
                    'name'=>$record['name'],
                    'one_gross'=>"",
                    'one_net'=>"",
                    'two_gross'=>"",
                    'two_net'=>"",
                    'three_gross'=>"",
                    'three_net'=>"",
                    'lcu'=>"",
                    'luu'=>"",
                    'lcd'=>"",
                    'lud'=>"",
                );
                $this->resetComparisonArr($arr);
                $this->attr[]=$arr;
			}
		}
		$session = Yii::app()->session;
		$session['comparisonSet_c01'] = $this->getCriteria();
		return true;
	}

    public function getCriteria() {
        return array(
            'month_type'=>$this->month_type,
            'comparison_year'=>$this->comparison_year,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'city'=>$this->city,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }

	protected function resetComparisonArr(&$arr){
        $row = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("city='{$arr['code']}' and comparison_year={$this->comparison_year} and month_type={$this->month_type}")
            ->queryRow();
        if($row){
            $arr["id"]=$row["id"];
            $arr["one_gross"]=empty($row["one_gross"])?"":floatval($row["one_gross"]);
            $arr["one_net"]=empty($row["one_net"])?"":floatval($row["one_net"]);
            $arr["two_gross"]=empty($row["two_gross"])?"":floatval($row["two_gross"]);
            $arr["two_net"]=empty($row["two_net"])?"":floatval($row["two_net"]);
            $arr["three_gross"]=empty($row["three_gross"])?"":floatval($row["three_gross"]);
            $arr["three_net"]=empty($row["three_net"])?"":floatval($row["three_net"]);
            $arr["lcu"]=$row["lcu"];
            $arr["luu"]=$row["luu"];
            $arr["lcd"]=$row["lcd"];
            $arr["lud"]=$row["lud"];
        }
    }
}
