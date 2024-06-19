<?php

class CrossAuditList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'table_type'=>Yii::t('summary','menu name'),
			'contract_no'=>Yii::t('service','Contract No'),
			'apply_date'=>Yii::t('service','Apply date'),
			'month_amt'=>Yii::t('service','Monthly'),
            'rate_num'=>Yii::t('service','accept rate'),
            'rate_amt'=>Yii::t('service','accept amt'),
			'old_city'=>Yii::t('service','City'),
			'cross_city'=>Yii::t('service','Cross city'),
			'status_type'=>Yii::t('service','status type'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,
                  b.name as old_city_name,f.name as cross_city_name 
				from swo_cross a
				LEFT JOIN security{$suffix}.sec_city b ON a.old_city=b.code
				LEFT JOIN security{$suffix}.sec_city f ON a.cross_city=f.code
				where a.cross_city in ({$city_allow}) and a.status_type=1 
			";
		$sql2 = "select count(a.id)
				from swo_cross a
				LEFT JOIN security{$suffix}.sec_city b ON a.old_city=b.code
				LEFT JOIN security{$suffix}.sec_city f ON a.cross_city=f.code
				where a.cross_city in ({$city_allow}) and a.status_type=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'contract_no':
					$clause .= General::getSqlConditionClause('a.contract_no',$svalue);
					break;
				case 'apply_date':
					$clause .= General::getSqlConditionClause('a.apply_date',$svalue);
					break;
				case 'old_city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'cross_city':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
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
						'id'=>$record['id'],
						'contract_no'=>$record['contract_no'],
						'apply_date'=>General::toDate($record['apply_date']),
						'month_amt'=>$record['month_amt'],
						'rate_num'=>$record['rate_num']."%",
						'rate_amt'=>$record['cross_amt'],
						'old_city'=>$record['old_city_name'],
						'cross_city'=>$record['cross_city_name'],
						'status_type'=>$record['status_type'],
						'status_str'=>CrossApplyList::getStatusStrForStatusType($record['status_type']),
                        'table_type'=>CrossApplyForm::getCrossTableTypeNameForKey($record["table_type"]),
						'color'=>CrossApplyList::getColorForStatusType($record['status_type']),
                    );
			}
		}
		$session = Yii::app()->session;
		$session['crossAudit_c01'] = $this->getCriteria();
		return true;
	}
}
