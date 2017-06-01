<?php

class QcList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'entry_dt'=>Yii::t('qc','Date'),
			'company_name'=>Yii::t('qc','Customer'),
			'job_staff'=>Yii::t('qc','Staff-Job'),
			'team'=>Yii::t('qc','Team'),
			'qc_dt'=>Yii::t('qc','QC Date'),
			'qc_staff'=>Yii::t('qc','Staff-QC'),
			'city_name'=>Yii::t('misc','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name,
				docman$suffix.countdoc('QC',a.id) as no_of_attm   
				from swo_qc a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) 
			";
		$sql2 = "select count(a.id)
				from swo_qc a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'company_name':
					$clause .= General::getSqlConditionClause('a.company_name',$svalue);
					break;
				case 'team':
					$clause .= General::getSqlConditionClause('a.team',$svalue);
					break;
				case 'job_staff':
					$clause .= General::getSqlConditionClause('a.job_staff',$svalue);
					break;
				case 'qc_staff':
					$clause .= General::getSqlConditionClause('a.qc_staff',$svalue);
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
					'entry_dt'=>General::toDate($record['entry_dt']),
					'company_name'=>$record['company_name'],
					'team'=>$record['team'],
					'job_staff'=>$record['job_staff'],
					'qc_dt'=>General::toDate($record['qc_dt']),
					'qc_staff'=>$record['qc_staff'],
					'city_name'=>$record['city_name'],
					'no_of_attm'=>$record['no_of_attm'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a06'] = $this->getCriteria();
		return true;
	}

}
