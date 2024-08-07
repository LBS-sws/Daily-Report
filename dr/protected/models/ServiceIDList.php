<?php

class ServiceIDList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
     * 重新提交
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
        );
	}

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city_allow();
        $sql1 = "select a.id,a.service_no,a.service,a.cont_info,a.status_dt,a.status,f.code as company_code,f.name as company_name, b.description as nature_desc, c.description as type_desc, d.name as city_name, 
					docman$suffix.countdoc('SERVICEID',a.id) as no_of_attm   
				from swo_serviceid a 
				inner join security$suffix.sec_city d on a.city=d.code 
				left outer join swo_nature b on a.nature_type=b.id 
				left outer join swo_customer_type_id c on a.cust_type=c.id 
				left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
        $sql2 = "select count(a.id) 
				from swo_serviceid a 
				inner join security$suffix.sec_city d on a.city=d.code 
				left outer join swo_nature b on a.nature_type=b.id 
				left outer join swo_customer_type c on a.cust_type=c.id 
				left outer join swo_company f on a.company_id=f.id 
				where a.city in ($city)  
			";
        $clause = "";
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
                    $clause .= " and (f.name like '%$svalue%' or f.code like '%$svalue%')";
                    break;
                case 'type_desc':
                    $clause .= General::getSqlConditionClause('c.description',$svalue);
                    break;
                case 'nature_desc':
                    $clause .= General::getSqlConditionClause('b.description',$svalue);
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
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'service_no'=>$record['service_no'],
                    'company_name'=>$record['company_code'].$record['company_name'],
                    'nature_desc'=>$record['nature_desc'],
                    'type_desc'=>$record['type_desc'],
                    'service'=>$record['service'],
                    'cont_info'=>$record['cont_info'],
                    'status'=>General::getStatusDesc($record['status']),
                    'status_dt'=>General::toDate($record['status_dt']),
                    'city_name'=>$record['city_name'],
                    'no_of_attm'=>$record['no_of_attm'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['serviceID_c01'] = $this->getCriteria();
        return true;
    }

}
