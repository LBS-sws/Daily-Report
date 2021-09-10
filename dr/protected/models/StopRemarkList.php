<?php

class StopRemarkList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
            'remark'=>Yii::t('service','Stop Remark'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$sql1 = "select *
				from swo_stop_remark
				where 1=1 ";
		$sql2 = "select count(id)
				from swo_stop_remark
				where 1=1 ";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'remark':
					$clause .= General::getSqlConditionClause('remark',$svalue);
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

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'remark'=>$record['remark'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['stopRemark_c01'] = $this->getCriteria();
		return true;
	}

}
