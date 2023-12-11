<?php

class PestTypeList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'pest_name'=>Yii::t('followup','Pest Type Name'),
			'z_index'=>Yii::t('followup','z_index'),
			'display_num'=>Yii::t('followup','display'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select * 
				from swo_pest_type 
				where 1=1 
			";
		$sql2 = "select count(id)
				from swo_pest_type 
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'pest_name':
					$clause .= General::getSqlConditionClause('pest_name',$svalue);
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
						'pest_name'=>$record['pest_name'],
						'z_index'=>$record['z_index'],
                        'display_num'=>$record['display_num']==1?Yii::t('followup',"show"):Yii::t('followup',"none"),
					);
			}
		}
		$session = Yii::app()->session;
		$session['pestType_c01'] = $this->getCriteria();
		return true;
	}

}
