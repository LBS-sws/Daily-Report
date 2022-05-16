<?php
class MonthList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'city'=>Yii::t('report','City'),
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
            'sales dep'=>Yii::t('report','Sales Dep. Score'),
            'tech. dep'=>Yii::t('report','Tech. Dep. Score'),
            'acc. dep'=>Yii::t('report','Acc. Dep. Score'),
            'ops. dep'=>Yii::t('report','Ops. Dep. Score'),
            'hr. dep'=>Yii::t('report','HR Dep. Score'),
            'total score'=>Yii::t('report','Total Score'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
	    if(empty($this->city)){
            $city = Yii::app()->user->city();
        }
        else{
            $city =$this->city;
        }
		$sql1 = "select a.*,b.name as cityname
				from swo_monthly_hdr a
				LEFT JOIN security$suffix.sec_city b ON a.city=b.code 		
				where a.city='$city'
			";
		$sql2 = "select count(a.id)
				from swo_monthly_hdr a
				where a.city='$city'
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year_no':
					$clause .= General::getSqlConditionClause('a.year_no', $svalue);
					break;
				case 'month_no':
					$clause .= General::getSqlConditionClause('a.month_no', $svalue);
					break;
			}
		}

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by a.year_no desc, a.month_no desc";

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
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
                        'city'=>$record['cityname'],
                        'cityname'=>$record['city'],
                        'f74'=>$record['f74'],
                        'f86'=>$record['f86'],
                        'f94'=>$record['f94'],
                        'f100'=>$record['f100'],
                        'f115'=>$record['f115'],
                        'f73'=>$record['f73'],
					);
			}
		}

		$session = Yii::app()->session;
		$session['criteria_a09'] = $this->getCriteria();
		return true;
	}

	public function testAll($year){
	    $model = new MonthForm();
        $rows = Yii::app()->db->createCommand()->select("id,city")
            ->from("swo_monthly_hdr")
            ->where("year_no={$year}")
            ->order("city asc")->queryAll();
        $city = "";
        echo "Year:{$year}<br/>";
        if($rows){
            foreach ($rows as $row){
                if($city!=$row["city"]){
                    $city = $row["city"];
                    echo "city start:{$city}<br/>";
                }
                $model->retrieveData($row["id"],$row["city"]);
            }
        }
        echo "All End !";
    }
}
