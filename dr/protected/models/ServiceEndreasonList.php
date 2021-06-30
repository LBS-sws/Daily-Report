<?php

class ServiceEndreasonList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('endreason','Id'),
			'reason'=>Yii::t('endreason','Reason'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$sql1 = "select *
				from swo_service_end_reasons
				where 1=1 ";
		$sql2 = "select count(id)
				from swo_service_end_reasons
				where 1=1 ";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'reason':
					$clause .= General::getSqlConditionClause('reason',$svalue);
					break;
			}
		}
		
		$order = " order by id desc ";
//		if (!empty($this->orderField)) {
//			$order .= " order by ".$this->orderField." ";
//			if ($this->orderType=='D') $order .= "desc ";
//		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
//			$startrow = ($this->noOfItem != 0) ? ($this->pageNum-1) * $this->noOfItem : 0;
//			$itemcnt = 0;
			foreach ($records as $k=>$record) {
//				if ($k >= $startrow && ($itemcnt <= $this->noOfItem || $this->noOfItem == 0)) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'reason'=>$record['reason'],
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['criteria_c08'] = $this->getCriteria();
		return true;
	}
	public function getlist(){
        $sql = "select reason from swo_service_end_reasons order by id desc ";
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        return json_decode( json_encode( $records),true);
    }

}
