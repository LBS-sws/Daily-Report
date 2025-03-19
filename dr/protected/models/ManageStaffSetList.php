<?php

class ManageStaffSetList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'start_date'=>Yii::t('summary','Effective date'),
            'end_date'=>"失效日期",
			'employee_code'=>Yii::t('summary','employee code'),
			'employee_name'=>Yii::t('summary','Employee Name'),
			'city_name'=>Yii::t('summary','bonus city'),
            'job_key'=>Yii::t('summary','bonus position'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.*,b.name as employee_name,b.code as employee_code,f.name as city_name
				from swo_manage_staff a
				LEFT JOIN hr{$suffix}.hr_employee b ON a.employee_id=b.id
				LEFT JOIN security{$suffix}.sec_city f ON a.city=f.code
				where 1=1 
			";
		$sql2 = "select count(a.id)
				from swo_manage_staff a
				LEFT JOIN hr{$suffix}.hr_employee b ON a.employee_id=b.id
				LEFT JOIN security{$suffix}.sec_city f ON a.city=f.code
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('city_allow_name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by start_date desc";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
//			$startrow = ($this->noOfItem != 0) ? ($this->pageNum-1) * $this->noOfItem : 0;
//			$itemcnt = 0;
			foreach ($records as $k=>$record) {
//				if ($k >= $startrow && ($itemcnt <= $this->noOfItem || $this->noOfItem == 0)) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'start_date'=>$record['start_date'],
						'end_date'=>$record['end_date'],
						'employee_code'=>$record['employee_code'],
						'employee_name'=>$record['employee_name'],
						'city_allow_name'=>$record['city_allow_name'],
						'job_key'=>ManageStaffSetForm::getJobStrForKey($record['job_key']),
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['manageStaffSet_c01'] = $this->getCriteria();
		return true;
	}
}
