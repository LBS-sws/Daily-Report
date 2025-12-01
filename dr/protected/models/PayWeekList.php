<?php

class PayWeekList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'code'=>Yii::t('code','Code'),
			'description'=>Yii::t('code','Description'),
			'u_id'=>"派单系统id",
			'z_display'=>"是否显示",
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$sql1 = "select *
				from swo_payweek
				where 1=1 
			";
		$sql2 = "select count(code)
				from swo_payweek
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('code',$svalue);
					break;
				case 'description':
					$clause .= General::getSqlConditionClause('description', $svalue);
					break;
				case 'u_id':
					$clause .= General::getSqlConditionClause('u_id', $svalue);
					break;
				case 'z_display':
					$clause .= General::getSqlConditionClause('z_display', $svalue);
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
						'code'=>$record['code'],
						'description'=>$record['description'],
						'z_display'=>$record['z_display']==1?"显示":"隐藏",
						'u_id'=>$record['u_id'],
					);
//					$itemcnt++;
//				}
			}
		}
		$session = Yii::app()->session;
		$session['criteria_c09'] = $this->getCriteria();
		return true;
	}

}
