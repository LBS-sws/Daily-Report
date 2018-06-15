<?php

class QcList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'entry_dt'=>Yii::t('qc','Date'),
			'company_name'=>Yii::t('qc','Customer'),
			'job_staff'=>Yii::t('qc','Staff-Job'),
			'team'=>Yii::t('qc','Team'),
			'qc_dt'=>Yii::t('qc','QC Date'),
			'qc_staff'=>Yii::t('qc','Staff-QC'),
			'city_name'=>Yii::t('misc','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN02') ? "" : "and a.lcu='$user'";
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, b.name as city_name,
				docman$suffix.countdoc('QC',a.id) as no_of_attm   
				from swo_qc a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) $allcond 
			";
		$sql2 = "select count(a.id)
				from swo_qc a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) $allcond 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'company_name':
					$clause .= General::getSqlConditionClause('a.company_name',$svalue);
					break;
				case 'team':
					$clause .= General::getSqlConditionClause('a.team',$svalue);
					break;
				case 'job_staff':
					$clause .= General::getSqlConditionClause('a.job_staff',$svalue);
					break;
				case 'qc_staff':
					$clause .= General::getSqlConditionClause('a.qc_staff',$svalue);
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


        $sqls="select a.id from swo_qc a
left outer join swo_qc_info b on a.id=b.qc_id and b.field_id='sign_cust'
left outer join swo_qc_info c on a.id=c.qc_id and c.field_id='sign_qc'
left outer join swo_qc_info d on a.id=d.qc_id and d.field_id='sign_cust'
where b.field_blob='' or c.field_blob='' or d.field_blob='' and a.id=''";
        $nook = Yii::app()->db->createCommand($sqls)->queryAll();

        $arr = array();
        if(count($nook) > 0){
            foreach ($nook as $nooks) {
                $arr[] = $nooks['id'];
/*                $this->attrs[] = array(
                    'ids'=>$nooks['id'],
                );*/
            }
        }

		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $bool = in_array($record['id'],$arr);
				$this->attr[] = array(
					'id'=>$record['id'],
					'entry_dt'=>General::toDate($record['entry_dt']),
					'company_name'=>$record['company_name'],
					'team'=>$record['team'],
					'job_staff'=>$record['job_staff'],
					'qc_dt'=>General::toDate($record['qc_dt']),
					'qc_staff'=>$record['qc_staff'],
					'city_name'=>$record['city_name'],
					'no_of_attm'=>$record['no_of_attm'],
					'bool'=>$bool,
				);
			}
		}
//        $this->attr=array_merge_recursive($this->attr,$this->attrs);
		$session = Yii::app()->session;
		$session['criteria_a06'] = $this->getCriteria();
		return true;
	}

}
