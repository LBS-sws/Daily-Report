<?php

class FollowupList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'entry_dt'=>Yii::t('followup','Date'),
			'type'=>Yii::t('followup','Type'),
			'company_name'=>Yii::t('followup','Customer'),
			'resp_staff'=>Yii::t('followup','Resp. Sales'),
			'resp_tech'=>Yii::t('followup','Technician'),
			'content'=>Yii::t('followup','Content'),
			'cont_info'=>Yii::t('followup','Contact'),
			'pest_type_name'=>Yii::t('followup','Pest Type'),
			'city_name'=>Yii::t('misc','City'),
			'fp_comment'=>Yii::t('followup','Comment'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN01') ? "" : "and a.lcu='$user'";
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name 
				from swo_followup a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city) $allcond
			";
		$sql2 = "select count(a.id)
				from swo_followup a, security$suffix.sec_city b
				where a.city=b.code and a.city in ($city) $allcond 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'type':
					$clause .= General::getSqlConditionClause('a.type',$svalue);
					break;
				case 'pest_type_name':
					$clause .= General::getSqlConditionClause('a.pest_type_name',$svalue);
					break;
				case 'company_name':
					$clause .= General::getSqlConditionClause('a.company_name',$svalue);
					break;
				case 'resp_staff':
					$clause .= General::getSqlConditionClause('a.resp_staff',$svalue);
					break;
				case 'resp_tech':
					$clause .= General::getSqlConditionClause('a.resp_tech',$svalue);
					break;
				case 'cont_info':
					$clause .= General::getSqlConditionClause('a.cont_info',$svalue);
					break;
				case 'content':
					$clause .= General::getSqlConditionClause('a.content',$svalue);
					break;
				case 'fp_comment':
					$clause .= General::getSqlConditionClause('a.fp_comment',$svalue);
					break;
			}
		}
		$clause .= $this->getDateRangeCondition('a.entry_dt');
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order ="order by a.entry_dt desc";
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
					'resp_staff'=>$record['resp_staff'],
					'resp_tech'=>$record['resp_tech'],
					'content'=>$record['content'],
					'type'=>$record['type'],
					'pest_type_name'=>$record['pest_type_name'],
					'cont_info'=>$record['cont_info'],
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a03'] = $this->getCriteria();
		return true;
	}

}
