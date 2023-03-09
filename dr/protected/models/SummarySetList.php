<?php

class SummarySetList extends CListPageModel
{
    public $summary_year;
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
			'summary_year'=>Yii::t('code','summary_year'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, summary_year,  pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
	    $this->summary_year = (empty($this->summary_year)||!is_numeric($this->summary_year))?date("Y"):$this->summary_year;
        $suffix = Yii::app()->params['envSuffix'];
        $notCityStr = ComparisonSetList::notCitySqlStr();
        $sql1 = "select code,name 
				from security{$suffix}.sec_city 
				where code not in (SELECT b.region FROM security{$suffix}.sec_city b WHERE b.region is not NULL and b.region!='' GROUP BY b.region)
				 AND code NOT in ('{$notCityStr}') AND name != '停用'
			";
        $sql2 = "select count(code)
				from security{$suffix}.sec_city 
				where code not in (SELECT b.region FROM security{$suffix}.sec_city b WHERE b.region is not NULL and b.region!='' GROUP BY b.region) 
				AND code NOT in ('{$notCityStr}') AND name != '停用'
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
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
                    'summary_year'=>$this->summary_year.Yii::t("report","Year"),
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
                $this->resetSummaryArr($arr);
                $this->attr[]=$arr;
			}
		}
		$session = Yii::app()->session;
		$session['summarySet_c01'] = $this->getCriteria();
		return true;
	}

    public function getCriteria() {
        return array(
            'summary_year'=>$this->summary_year,
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

	protected function resetSummaryArr(&$arr){
        $row = Yii::app()->db->createCommand()->select("*")->from("swo_summary_set")
            ->where("city='{$arr['code']}' and summary_year={$this->summary_year}")
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

    public static function getSelectYear(){
	    $arr = array();
        $year = date("Y");
        for($i=$year-3;$i<$year+3;$i++){
            if($i>=2022){
                $arr[$i] = $i.Yii::t("report","Year");
            }
        }
	    return $arr;
    }
}
