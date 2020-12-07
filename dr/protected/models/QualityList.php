<?php

class QualityList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'dt'=>Yii::t('qc','Date'),
			'job_staff'=>Yii::t('qc','Job Staff'),
			'city'=>Yii::t('app','City'),
			'result'=>Yii::t('qc','Result'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$user = Yii::app()->user->id;
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sqls="set sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERRIR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';";
        $recd = Yii::app()->db->createCommand($sqls)->execute();
		$sql1 = "select city, job_staff, date_format(qc_dt,'%Y-%m') as dt, avg(qc_result) as result from swo_qc where city in ($city) ";
		$sql2 = "select count(*) count from (select count(*)
                    from swo_qc  where city in ($city)                  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'dt':
					$clause .= General::getSqlConditionClause('date_format(qc_dt, \'%Y-%m\')',$svalue);
					break;
				case 'job_staff':
					$clause .= General::getSqlConditionClause('job_staff',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('city',$svalue);
					break;
			}
		}
		$clause .= $this->getDateRangeCondition('qc_dt');
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order ="order by qc_dt desc";
        }
        $clause.="";
		$sql = $sql2.$clause."group by  date_format(qc_dt, '%Y-%m'),job_staff ) temp";
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

		$sql = $sql1.$clause." group by city, job_staff, dt ".$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$sql.="";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
       //print_r('<pre>');
        print_r($sql);
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
//					'id'=>$record['id'],
					'dt'=>$record['dt'],
					'job_staff'=>$record['job_staff'],
					'city'=>$record['city'],
					'result'=>round ($record['result'],2),
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_e01'] = $this->getCriteria();
		return true;
	}

}
