<?php
class RptCustnew extends ReportData2 {
	public function fields() {
		return array(
            'id'=>array('label'=>"LBS系统ID",'width'=>12,'align'=>'L'),
			'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
			'office_name'=>array('label'=>"归属",'width'=>12,'align'=>'C'),
			'status_dt'=>array('label'=>Yii::t('service','New Date'),'width'=>18,'align'=>'C'),
            'company_code'=>array('label'=>"客户编号",'width'=>20,'align'=>'L'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
            'group_code'=>array('label'=>"KA/集团编号",'width'=>18,'align'=>'L'),
            'group_name'=>array('label'=>"KA/集团名称",'width'=>18,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'cust_type_name_two'=>array('label'=>Yii::t('service','Layer 2'),'width'=>20,'align'=>'L'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>40,'align'=>'L'),
			'prepay_month'=>array('label'=>Yii::t('service','Prepay Month'),'width'=>10,'align'=>'C'),
			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
			'amt_install'=>array('label'=>Yii::t('service','Installation Fee'),'width'=>15,'align'=>'C'),
			'need_install'=>array('label'=>Yii::t('service','Installation'),'width'=>10,'align'=>'C'),
			'salesman'=>array('label'=>Yii::t('service','Resp. Sales'),'width'=>20,'align'=>'L'),
            'othersalesman'=>array('label'=>Yii::t('service','OtherSalesman'),'width'=>20,'align'=>'L'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
			'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
			'cont_info'=>array('label'=>Yii::t('service','Contact'),'width'=>40,'align'=>'L'),
			'first_dt'=>array('label'=>Yii::t('service','First Service Date'),'width'=>18,'align'=>'C'),
			'first_tech'=>array('label'=>Yii::t('service','First Service Tech.'),'width'=>30,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('service','Remarks'),'width'=>40,'align'=>'L'),
			'equip_install_dt'=>array('label'=>Yii::t('service','Installation Date'),'width'=>18,'align'=>'C'),
			'diff_ctrt_dt'=>array('label'=>Yii::t('service','Diff. btw Contract Date'),'width'=>15,'align'=>'C'),
			'diff_first_dt'=>array('label'=>Yii::t('service','Diff. btw First Service Date'),'width'=>15,'align'=>'C'),
		);	
	}

	public function groups() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>397,'align'=>'L'),
			),
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
		
		$sql = "select a.*, b.description as nature,f.code as com_code,f.name as com_name,f.group_id as group_code,f.group_name, c.description as customer_type, d.cust_type_name as cust_type_name_two 
					from swo_service a
					left outer join swo_nature b on a.nature_type=b.id 
        			left outer join swo_company f on a.company_id=f.id 
					left outer join swo_customer_type c on a.cust_type=c.id
					left outer join swo_customer_type_twoname d on d.id=a.cust_type_name
				where a.status='N' and a.city in ({$city_allow}) 
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			if ($where!='') $sql .= $where;	
		}
		$orderSql = " order by a.city,c.description, a.status_dt";
		$sql .= $orderSql;
		$rowsIA = Yii::app()->db->createCommand($sql)->queryAll();
		$rowsIA = $rowsIA?$rowsIA:array();
		
		// 查询KA客户
		$sql = "select a.*, b.description as nature,f.code as com_code,f.name as com_name,f.group_id as group_code,f.group_name, c.description as customer_type, d.cust_type_name as cust_type_name_two 
					from swo_service_ka a
					left outer join swo_nature b on a.nature_type=b.id 
        				left outer join swo_company f on a.company_id=f.id 
					left outer join swo_customer_type c on a.cust_type=c.id
					left outer join swo_customer_type_twoname d on d.id=a.cust_type_name
				where a.status='N' and a.city in ({$city_allow}) 
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.status_dt<='" .General::toDate($this->criteria->end_dt)." 23:59:59'";
			if ($where!='') $sql .= $where;	
		}
		$sql .= $orderSql;
		$rowsKA = Yii::app()->db->createCommand($sql)->queryAll();
		$rowsKA = $rowsKA?$rowsKA:array();
		
		// 合并普通客户和KA客户数据
		$rows = array_merge($rowsIA, $rowsKA);
		$officeList = array();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $row["office_id"] = empty($row["office_id"])?0:$row["office_id"];
				if(!key_exists($row["office_id"],$officeList)){
                    $officeList[$row["office_id"]] = GetNameToId::getOfficeNameForID($row['office_id']);
				}
				$temp = array();
                $temp['id'] = $row['id'];
				$temp['city_name'] = General::getCityName($row["city"]);
				$temp['office_name'] = $officeList[$row["office_id"]];
				$temp['type'] = $row['customer_type'];
				$temp['status_dt'] = General::toDate($row['status_dt']);
                $temp['company_code'] = $row['com_code'];
                $temp['company_name'] = empty($row['com_name'])?$row['company_name']:$row['com_name'];
                $temp['group_code'] = $row['group_code'];
                $temp['group_name'] = $row['group_name'];
				$temp['nature'] = $row['nature'];
				$temp['cust_type_name_two'] = $row['cust_type_name_two'];
				$temp['service'] = $row['service'];
				$temp['prepay_month'] = $row['prepay_month'];
				$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2)))
									,2,'.','');
				$period = empty($row['ctrt_period'])?0:($row['ctrt_period']<12?$row['ctrt_period']:12);
				$temp['amt_year'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']*$period:$row['amt_paid']))
									,2,'.','');
				$temp['amt_install'] = number_format($row['amt_install'],2,'.','');
				$temp['need_install'] = ($row['need_install']=='Y') ? Yii::t('misc','Yes') : Yii::t('misc','No');
				$temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
				$temp['cont_info'] = $row['cont_info'];
				$temp['first_dt'] = General::toDate($row['first_dt']);
				$temp['first_tech'] = $row['first_tech'];
				$temp['remarks'] = $row['remarks2'];
				$temp['equip_install_dt'] = General::toDate($row['equip_install_dt']);
				$temp['diff_ctrt_dt'] = (empty($temp['equip_install_dt']) || empty($temp['status_dt'])) ? '' :
					(strtotime($row['equip_install_dt'])-strtotime($row['status_dt']))/86400;
				$temp['diff_first_dt'] = (empty($temp['status_dt']) || empty($temp['first_dt'])) ? '' :
					(strtotime($temp['first_dt'])-strtotime($temp['status_dt']))/86400;

				$this->data[] = $temp;
			}
		}
		return true;
	}
	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityNameForList($this->criteria->city) : '';
		return parent::getReportName();
	}
}
?>
