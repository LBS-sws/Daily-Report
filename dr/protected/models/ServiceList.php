<?php

class ServiceList extends CListPageModel
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
            'contract_no'=>Yii::t('service','Contract No'),
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

    public function getFilterFieldList() {

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
                case 'contract_no':
                    $clause .= "and a.id in (select no.service_id from swo_service_ka_no no where no.contract_no like '%{$svalue}%' )";;
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
    }
    public function searchColumns() {
        $search = array(
            'city_name'=>"d.name",
            'company_name'=>"CONCAT(f.code,f.name)",
            'type_desc'=>"c.description",
            'nature_desc'=>"CONCAT(b.description,g.name)",
            'service'=>'a.service',
            'cont_info'=>'a.cont_info',
            'contract_no'=>"(select no.contract_no from swo_service_contract_no no where no.service_id=a.id)",
            'status'=>"(select case a.status when 'N' then '".General::getStatusDesc('N')."' 
							when 'S' then '".General::getStatusDesc('S')."' 
							when 'R' then '".General::getStatusDesc('R')."' 
							when 'A' then '".General::getStatusDesc('A')."' 
							when 'T' then '".General::getStatusDesc('T')."' 
							when 'C' then '".General::getStatusDesc('C')."' 
						end) ",
        );
        return $search;
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*,f.code as com_code,f.name as com_name,g.name as nature_two_name, b.description as nature_desc, c.description as type_desc, d.name as city_name, 
					docman$suffix.countdoc('SERVICE',a.id) as no_of_attm   
				from swo_service a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$sql2 = "select count(a.id)
				from swo_service a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$clause = "";
		switch ($this->office_type) {
			case 'office_one'://本部
				$clause .= " and a.office_id is null ";
				break;
			case 'office_two'://办事处
				$clause .= " and a.office_id is not null ";
				break;
		}
        $static = $this->staticSearchColumns();
        $columns = $this->searchColumns();
        if (!empty($this->searchField) && (!empty($this->searchValue) || in_array($this->searchField, $static) || $this->isAdvancedSearch())) {
            if ($this->isAdvancedSearch()) {
                $clause = $this->buildSQLCriteria();
            } elseif (in_array($this->searchField, $static)) {
                $clause .= 'and '.$columns[$this->searchField];
            } else {
                $svalue = str_replace("'","\'",$this->searchValue);
                $clause .= General::getSqlConditionClause($columns[$this->searchField],$svalue);
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
					'type_desc'=>$record['type_desc'],
					'service'=>$record['service'],
					'cont_info'=>$record['cont_info'],
					'status'=>General::getStatusDesc($record['status']),
					'status_dt'=>General::toDate($record['status_dt']),
					'city_name'=>$record['city_name'],
					'office_name'=>GetNameToId::getOfficeNameForID($record['office_id']),
					'no_of_attm'=>$record['no_of_attm'],
                    'cross_bool'=>$record['status']=="N"&&self::validateCross($record['id']),
				);
			}
		}
		$session = Yii::app()->session;
		$session[$this->criteriaName()] = $this->getCriteria();
		return true;
	}

	protected function retrieveExcelDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*,f.code as com_code,f.name as com_name,g.name as nature_two_name, b.description as nature_desc, c.description as type_desc, d.name as city_name, 
					docman$suffix.countdoc('SERVICE',a.id) as no_of_attm   
				from swo_service a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$sql2 = "select count(a.id)
				from swo_service a inner join security$suffix.sec_city d on a.city=d.code 
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_nature_type g on a.nature_type_two=g.id 
					left outer join swo_customer_type c on a.cust_type=c.id 
                    left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
		$clause = "";
		switch ($this->office_type) {
			case 'office_one'://本部
				$clause .= " and a.office_id is null ";
				break;
			case 'office_two'://办事处
				$clause .= " and a.office_id is not null ";
				break;
		}
        $static = $this->staticSearchColumns();
        $columns = $this->searchColumns();
        if (!empty($this->searchField) && (!empty($this->searchValue) || in_array($this->searchField, $static) || $this->isAdvancedSearch())) {
            if ($this->isAdvancedSearch()) {
                $clause = $this->buildSQLCriteria();
            } elseif (in_array($this->searchField, $static)) {
                $clause .= 'and '.$columns[$this->searchField];
            } else {
                $svalue = str_replace("'","\'",$this->searchValue);
                $clause .= General::getSqlConditionClause($columns[$this->searchField],$svalue);
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
                    'city_name'=>$record['city_name'],
                    'office_name'=>GetNameToId::getOfficeNameForID($record['office_id']),
					'company_name'=>empty($record['com_code'])?$record['company_name']:$record['com_code'].$record['com_name'],
					'type_desc'=>$record['type_desc'],
                    'nature_desc'=>$nature_desc,
					'service'=>$record['service'],
					'cont_info'=>$record['cont_info'],
					'status'=>General::getStatusDesc($record['status']),
					'status_dt'=>General::toDate($record['status_dt']),
					'no_of_attm'=>$record['no_of_attm']>0?"归档":"",
				);
			}
		}
		return true;
	}

	public function downloadExcel(){
        $this->noOfItem=0;
        $this->pageNum=1;
	    $this->retrieveExcelDataByPage(1);
        $headList = array(
            array("name"=>"LBS系统ID","background"=>"#f7fd9d"),//區域
            array("name"=>"地区","background"=>"#f7fd9d"),//區域
            array("name"=>"归属","background"=>"#f7fd9d"),//區域
            array("name"=>"客户编号及名称","background"=>"#f7fd9d"),//區域
            array("name"=>"客户类别","background"=>"#f7fd9d"),//區域
            array("name"=>"性质","background"=>"#f7fd9d"),//區域
            array("name"=>"服务內容","background"=>"#f7fd9d"),//區域
            array("name"=>"客户联系/电话","background"=>"#f7fd9d"),//區域
            array("name"=>"记录类别","background"=>"#f7fd9d"),//區域
            array("name"=>"记录日期","background"=>"#f7fd9d"),//區域
            array("name"=>"是否归档","background"=>"#f7fd9d"),//區域
        );
        $excel = new DownSummary();
        $titleName = "客户服务";
        $objName="时间范围：".self::getDateRangeStr($this->dateRangeValue);
        if(!empty($this->searchField)&&!empty($this->searchValue)){
            $objName.= "\n";
            $objName.= $this->getAttributeLabel($this->searchField);
            $objName.= "：".$this->searchValue;
        }
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($objName);
        $excel->init();
        $excel->setUServiceHeader($headList);
        $excel->setUServiceData($this->attr);
        $excel->outExcel($titleName);
    }

    public static function getDateRangeStr($dateRangeValue){
	    if (empty($dateRangeValue)){
	        return "全部";
        }else{
	        return "{$dateRangeValue}个月内";
        }
    }

    //驗證该服务是否交叉派单
    private function validateCross($service_id) {
        $row = Yii::app()->db->createCommand()->select("id")->from("swo_cross")
            ->where("table_type=0 and service_id=:id and status_type not in (2,5,6)",array(":id"=>$service_id))
            ->queryRow();
        return $row?false:true;//如果存在，则不允许批量交叉派单
    }
}
