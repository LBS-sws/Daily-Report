<?php

class CrossSearchList extends CListPageModel
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

            'cross_type'=>Yii::t('service','Cross type'),
            'company_name'=>Yii::t('service','Customer name'),
            'qualification_ratio'=>Yii::t('service','Qualification ratio'),
            'qualification_city'=>Yii::t('service','Qualification city'),
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
				where (a.cross_city in ({$city_allow}) or a.old_city in ({$city_allow}) or (a.cross_type=5 and a.qualification_city in ({$city_allow}))) and a.status_type in (3,5,6) 
			";
		$sql2 = "select count(a.id)
				from swo_cross a
				LEFT JOIN security{$suffix}.sec_city b ON a.old_city=b.code
				LEFT JOIN security{$suffix}.sec_city f ON a.cross_city=f.code
				where (a.cross_city in ({$city_allow}) or a.old_city in ({$city_allow}) or (a.cross_type=5 and a.qualification_city in ({$city_allow})))  and a.status_type in (3,5,6) 
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
                case 'company_name':
                    $companySql = CrossApplyList::searchCompanySql($svalue);
                    $clause .= "and ({$companySql}) ";
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.status_type asc,a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $companyName = CrossApplyList::getCompanyNameToCrossList($record);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'contract_no'=>$record['contract_no'],
                    'apply_date'=>General::toDate($record['apply_date']),
                    'month_amt'=>floatval($record['month_amt']),
                    'rate_num'=>$record['rate_num']===null?"":floatval($record['rate_num'])."%",
                    'old_city'=>$record['old_city_name'],
                    'cross_city'=>$record['cross_city_name'],
                    'qualification_city'=>empty($record['qualification_city'])?"":General::getCityName($record['qualification_city']),
                    'qualification_ratio'=>$record['qualification_ratio']===null?"":floatval($record['qualification_ratio'])."%",
                    'status_type'=>$record['status_type'],
                    'company_name'=>$companyName,
                    'table_type'=>CrossApplyForm::getCrossTableTypeNameForKey($record["table_type"]),
                    'cross_type_name'=>CrossApplyForm::getCrossTypeStrToKey($record["cross_type"]),
                    'status_str'=>CrossApplyList::getStatusStrForStatusType($record['status_type']),
                    'color'=>CrossApplyList::getColorForStatusType($record['status_type']),
                );
			}
		}
		$session = Yii::app()->session;
		$session['crossSearch_c01'] = $this->getCriteria();
		return true;
	}
}
