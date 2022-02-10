<?php

class RankMonthList extends CListPageModel
{
    public $year;
    public $month;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'year'=>Yii::t('report','Year'),
            'month'=>Yii::t('report','Month'),
			'city'=>Yii::t('app','City'),
			'ranking'=>Yii::t('staff','Ranking'),
			'f73'=>Yii::t('staff','Score Number'),
		);
	}

    public function rules()
    {
        return array(
            array('year,month,attr, pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
	    if (empty($this->year)||empty($this->month)){
            $date = date("Y/m/01");
	        $date = date("Y-m",strtotime("{$date} - 2 month"));
            $date = explode("-",$date);
            $this->year = $date[0];
            $this->month = $date[1];
        }
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.year_no,a.month_no,a.f73,b.name 
				from swo_monthly_hdr a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code
				where a.year_no={$this->year} AND a.month_no={$this->month}
			";
		$sql2 = "select count(a.id)
				from swo_monthly_hdr a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code
				where a.year_no={$this->year} AND a.month_no={$this->month}
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = " order by a.f73 desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'ranking'=>$k+1,
						'city'=>$record['name'],
						'f73'=>$record['f73'],
						'year'=>$this->year,
						'month'=>$this->month,
					);
			}
		}
		$session = Yii::app()->session;
		$session['rankMonth_c01'] = $this->getCriteria();
		return true;
	}


    public function getCriteria() {
        return array(
            'year'=>$this->year,
            'month'=>$this->month,
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

    public static function getYearList(){
	    $year = date("Y");
	    $arr = array();
	    for ($i=$year-4;$i<=$year;$i++){
            $arr[$i] = $i." ".Yii::t('report','Year');
        }
        return $arr;
    }

    public static function getMonthList(){
	    $arr = array();
	    for ($i=1;$i<=12;$i++){
            $arr[$i] = $i." ".Yii::t('report','Month');
        }
        return $arr;
    }
}
