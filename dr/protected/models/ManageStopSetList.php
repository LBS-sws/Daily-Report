<?php

class ManageStopSetList extends CListPageModel
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
			'set_name'=>Yii::t('summary','setting name'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$sql1 = "select *
				from swo_manage_stop_hdr
				where 1=1 
			";
		$sql2 = "select count(id)
				from swo_manage_stop_hdr
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'set_name':
					$clause .= General::getSqlConditionClause('set_name',$svalue);
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
//			$startrow = ($this->noOfItem != 0) ? ($this->pageNum-1) * $this->noOfItem : 0;
//			$itemcnt = 0;
			foreach ($records as $k=>$record) {
//				if ($k >= $startrow && ($itemcnt <= $this->noOfItem || $this->noOfItem == 0)) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'start_date'=>$record['start_date'],
						'set_name'=>$record['set_name'],
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['manageStopSet_c01'] = $this->getCriteria();
		return true;
	}

	public static function getSalesRateList($type="",$bool=false){
	    $list = array(
            0=>Yii::t("code","not participate"),//不参加
	        1=>Yii::t("code","participate"),//参加
        );
	    if($bool){
	        if(key_exists($type,$list)){
	            return $list[$type];
            }else{
	            return $type;
            }
        }else{
	        return $list;
        }
    }
}
