<?php
class RptRenewal extends ReportData2 {
	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
			'expiry_dt'=>array('label'=>Yii::t('service','Expiry Date'),'width'=>18,'align'=>'C'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>40,'align'=>'L'),
			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
			'amt_install'=>array('label'=>Yii::t('service','Installation Fee'),'width'=>15,'align'=>'C'),
			'salesman'=>array('label'=>Yii::t('service','Resp. Sales'),'width'=>20,'align'=>'L'),
			'status_dt'=>array('label'=>Yii::t('service','New Date'),'width'=>18,'align'=>'C'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
			'cont_info'=>array('label'=>Yii::t('service','Contact'),'width'=>40,'align'=>'L'),
		);	
	}

/*
	public function groups() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>379,'align'=>'L'),
			),
		);
	}
*/
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
		
		$sql = "select
					a.*, d.description as nature, c.description as customer_type
				from 
					swo_service a
					left outer join swo_service b 
						on (a.company_id=b.company_id or a.company_name=b.company_name) and 
						(a.product_id=b.product_id or a.service=b.service or 
						a.product_id=b.b4_product_id or a.service=b.b4_service) and
						(a.status_dt < b.status_dt or 
						(a.status_dt = b.status_dt and a.id < b.id))
					left outer join swo_customer_type c
						on a.cust_type=c.id
					left outer join swo_nature d 
						on a.nature_type=d.id 
				where 
					b.id is null and 
					a.paid_type <> '1' and
					a.ctrt_end_dt is not null and 
					a.city in ({$city_allow})
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
//				$where .= " and datediff((a.sign_dt+interval a.ctrt_period month),'".General::toDate($this->criteria->start_dt)." 00:00:00') <= 60";
				$where .= " and datediff(a.ctrt_end_dt,'".General::toDate($this->criteria->target_dt)." 00:00:00') <= 60";
			if ($where!='') $sql .= $where;	
		}
		$sql .= " order by a.city,a.ctrt_end_dt";
		//echo $sql;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				if ($row['status']!='S' && $row['status']!='T') {
					$temp = array();
                    $temp['city_name'] = General::getCityName($row["city"]);
					$temp['type'] = $row['customer_type'];
					$temp['status_dt'] = General::toDate($row['status_dt']);
					$temp['company_name'] = $row['company_name'];
					$temp['nature'] = $row['nature'];
					$temp['service'] = $row['service'];
					$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/12,2))),2,'.','');
					$temp['amt_year'] = number_format(($row['paid_type']=='1'?0:($row['paid_type']=='M'?$row['amt_paid']*12:$row['amt_paid'])),2,'.','');
					$temp['amt_install'] = number_format($row['amt_install'],2,'.','');
					$temp['salesman'] = $row['salesman'];
					$temp['sign_dt'] = General::toDate($row['sign_dt']);
					$temp['ctrt_period'] = $row['ctrt_period'];
					$temp['expiry_dt'] = General::toDate($row['ctrt_end_dt']);
					$temp['cont_info'] = $row['cont_info'];

					$this->data[] = $temp;
				}
			}
		}
		return true;
	}
}
?>