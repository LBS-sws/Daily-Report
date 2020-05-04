<?php

class AnnounceList extends CListPageModel {
	
	public function attributeLabels() {
		return array(	
			'name'=>Yii::t('code','Description'),
			'start_dt'=>Yii::t('code','Start Date'),
			'end_dt'=>Yii::t('code','End Date'),
			'priority'=>Yii::t('code','Priority'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1) {
        $suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select *
				from announcement$suffix.ann_announce
				where 1=1 ";
		$sql2 = "select count(id)
				from announcement$suffix.ann_announce
				where 1=1 ";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'start_dt':
					$clause .= General::getSqlConditionClause('start_dt',$svalue);
					break;
				case 'end_dt':
					$clause .= General::getSqlConditionClause('end_dt',$svalue);
					break;
				case 'priority':
					$clause .= General::getSqlConditionClause('priority',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else {
			$order .= " order by id desc ";
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
					'name'=>$record['name'],
					'start_dt'=>General::toDate($record['start_dt']),
					'end_dt'=>General::toDate($record['end_dt']),
					'priority'=>$record['priority'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_d05'] = $this->getCriteria();
		return true;
	}

}
