<?php
class RptRenewal extends ReportData2 {
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>40,'align'=>'L'),
			'amt_install'=>array('label'=>Yii::t('service','Install Amt'),'width'=>15,'align'=>'C'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
		);	
/*
	public function groups() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>379,'align'=>'L'),
			),
		);
	}
*/
	
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		
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
					a.city='$city' 
		";
			if ($where!='') $sql .= $where;	
		echo $sql;
				if ($row['status']!='S' && $row['status']!='T') {
					$temp['status_dt'] = General::toDate($row['status_dt']);
					$temp['service'] = $row['service'];
					$temp['amt_install'] = number_format($row['amt_install'],2,'.','');
					$temp['sign_dt'] = General::toDate($row['sign_dt']);
					$temp['ctrt_period'] = $row['ctrt_period'];
					$temp['expiry_dt'] = General::toDate($row['ctrt_end_dt']);
					$temp['cont_info'] = $row['cont_info'];

					$this->data[] = $temp;
				}
}
?>