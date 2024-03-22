<?php

class ServiceKAList extends CListPageModel
{
	public $office_type="all";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'service_no'=>Yii::t('service','service no'),
			'company_name'=>Yii::t('service','Customer'),
			'type_desc'=>Yii::t('service','Customer Type'),
			'nature_desc'=>Yii::t('service','Nature'),
			'service'=>Yii::t('service','Service'),
			'cont_info'=>Yii::t('service','Contact'),
			'status'=>Yii::t('service','Record Type'),
			'status_dt'=>Yii::t('service','Record Date'),
			'city_name'=>Yii::t('misc','City'),
			'office_name'=>"归属",
		);
	}

	public function rules()
	{
		return array(
			array('office_type,attr, pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
		);
	}
	
	public function getCriteria() {
		return array(
			'office_type'=>$this->office_type,
			'searchField'=>$this->searchField,
			'searchValue'=>$this->searchValue,
			'orderField'=>$this->orderField,
			'orderType'=>$this->orderType,
			'noOfItem'=>$this->noOfItem,
			'pageNum'=>$this->pageNum,
			'filter'=>$this->filter,
            'city'=>$this->city,
			'dateRangeValue'=>$this->dateRangeValue,
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*,f.code as com_code,f.name as com_name,g.name as nature_two_name, b.description as nature_desc, c.description as type_desc, d.name as city_name, 
					docman$suffix.countdoc('SERVICEKA',a.id) as no_of_attm   
				from swo_service_ka a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$sql2 = "select count(a.id)
				from swo_service_ka a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$clause = "";
		switch ($this->office_type) {
			case 'office_one'://本部
				$clause .= " and a.office_id is null";
				break;
			case 'office_two'://办事处
				$clause .= " and a.office_id is not null";
				break;
		}
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'service_no':
					$clause .= General::getSqlConditionClause('a.service_no',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
				case 'company_name':
					$clause .= " and (f.code like '%$svalue%' or f.name like '%$svalue%' or a.company_name like '%$svalue%')";
					break;
				case 'type_desc':
					$clause .= General::getSqlConditionClause('c.description',$svalue);
					break;
				case 'nature_desc':
					$clause .= " and (b.description like '%{$svalue}%' or g.name like '%{$svalue}%')";
					break;
				case 'service':
					$clause .= General::getSqlConditionClause('a.service',$svalue);
					break;
				case 'cont_info':
					$clause .= General::getSqlConditionClause('a.cont_info',$svalue);
					break;
				case 'status':
					$field = "(select case a.status when 'N' then '".General::getStatusDesc('N')."' 
							when 'S' then '".General::getStatusDesc('S')."' 
							when 'R' then '".General::getStatusDesc('R')."' 
							when 'A' then '".General::getStatusDesc('A')."' 
							when 'T' then '".General::getStatusDesc('T')."' 
							when 'C' then '".General::getStatusDesc('C')."' 
						end) ";
					$clause .= General::getSqlConditionClause($field, $svalue);
					break;
			}
		}
		$clause .= $this->getDateRangeCondition('a.status_dt');
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order ="order by status_dt desc";
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
			    $nature_desc = $record['nature_desc'];
			    $nature_desc.= empty($record['nature_two_name'])?"":"({$record['nature_two_name']})";
				$this->attr[] = array(
					'id'=>$record['id'],
					'company_name'=>empty($record['com_code'])?$record['company_name']:$record['com_code'].$record['com_name'],
					'nature_desc'=>$nature_desc,
					'service_no'=>$record['service_no'],
					'type_desc'=>$record['type_desc'],
					'service'=>$record['service'],
					'cont_info'=>$record['cont_info'],
					'status'=>General::getStatusDesc($record['status']),
					'status_dt'=>General::toDate($record['status_dt']),
					'city_name'=>$record['city_name'],
					'office_name'=>GetNameToId::getOfficeNameForID($record['office_id']),
					'no_of_attm'=>$record['no_of_attm'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['serviceKA_01'] = $this->getCriteria();
		return true;
	}

}
