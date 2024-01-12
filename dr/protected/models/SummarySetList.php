<?php

class SummarySetList extends CListPageModel
{
    public $summary_year;
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
			'summary_year'=>Yii::t('code','summary_year'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, summary_year,month_type,  pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
	    $this->summary_year = (empty($this->summary_year)||!is_numeric($this->summary_year))?date("Y"):$this->summary_year;
	    $this->month_type = (!in_array($this->month_type,array(1,4,7,10)))?1:$this->month_type;
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
            'month_type'=>$this->month_type,
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
            ->where("city='{$arr['code']}' and summary_year={$this->summary_year} and month_year={$this->month_type}")
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

    public static function getSelectMonth(){
        $arr = array();
        for($i=1;$i<=12;$i++){
            $arr[$i] = $i.Yii::t("report","Month");
        }
        return $arr;
    }

    public static function getSelectType(){
        $arr = array();
        if(Yii::app()->user->validFunction('CN16')){
            $arr[1]=Yii::t("summary","search quarter");//季度
        }
        if(Yii::app()->user->validFunction('CN17')){
            $arr[2]=Yii::t("summary","search month");//月度
        }
        $arr[3]=Yii::t("summary","search day");//日期
        return $arr;
    }

    public static function getSummaryMonthList($key=1,$bool=false,$year=2023){
        if($year==2024){//特别处理2024年
            $arr = array(
                1=>Yii::t("summary","1 month - 3 month"),
                4=>Yii::t("summary","4 month - 7 month"),
                7=>Yii::t("summary","8 month - 12 month"),
            );
        }else{
            $arr = array(
                1=>Yii::t("summary","1 month - 3 month"),
                4=>Yii::t("summary","4 month - 6 month"),
                7=>Yii::t("summary","7 month - 9 month"),
                10=>Yii::t("summary","10 month - 12 month"),
            );
        }
        if($bool){
            if(key_exists($key,$arr)){
                return $arr[$key];
            }else{
                return $key;
            }
        }
        return $arr;
    }

    public static function getReminderTitle($year,$month_type){
        $monthList = self::getSummaryMonthList();
        $titleStart = Yii::t("summary","Whether to modify ");
        $list = array();
        foreach ($monthList as $key=>$str){
            if($month_type<$key){
                $list[]=$year.Yii::t("report","Year").$str;
            }
        }
        $titleEnd= Yii::t("summary"," for value?");
        if(empty($list)){
            $title = "<span id='reminderTitle'></span>";
        }else{
            $title = "<span id='reminderTitle'>".$titleStart.implode("、",$list).$titleEnd."</span>";
        }
        return $title;
    }
}
