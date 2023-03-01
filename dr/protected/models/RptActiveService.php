<?php
class RptActiveService extends ReportData2 {
	public function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('user','City'),'width'=>18,'align'=>'C'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'customer_type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>15,'align'=>'L'),
			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
			'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
		);	
	}

	public function retrieveData() {
		$suffix = Yii::app()->params['envSuffix'];
		$targetDate = isset($this->criteria) && isset($this->criteria->target_dt)
			? General::toDate($this->criteria->target_dt)." 23:59:59"
			: date('Y-m-d')." 23:59:59";
		$sql = "select 
				z3.name as city_name, 
				y.company_name, 
				z1.description as nature, 
				z2.description as customer_type, 
				y.amt_paid, 
				y.paid_type, 
				y.ctrt_period, 
				y.sign_dt, 
				y.ctrt_end_dt
			from (
				select a.city, b.code, a.cust_type, max(a.status_dt) as max_dt
				from swo_service a, swo_company b
				where a.city=b.city and substring(a.company_name, 1, char_length(b.code))=b.code
				and a.status_dt <= '$targetDate'
				group by a.city, b.code, a.cust_type
			) x
			inner join swo_service y on y.city=x.city and substring(y.company_name, 1, char_length(x.code))=x.code and x.max_dt=y.status_dt
			left outer join swo_nature z1 on y.nature_type=z1.id 
			left outer join swo_customer_type z2 on y.cust_type=z2.id
			left outer join security$suffix.sec_city z3 on y.city=z3.code
			where y.status not in ('T','S')
		";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['city_name'] = $row['city_name'];
				$temp['customer_type'] = $row['customer_type'];
				$temp['company_name'] = $row['company_name'];
				$temp['nature'] = $row['nature'];
				$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2)))
									,2,'.','');
				$period = empty($row['ctrt_period'])?0:($row['ctrt_period']<12?$row['ctrt_period']:12);
				$temp['amt_year'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']*$period:$row['amt_paid']))
									,2,'.','');
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);

				$this->data[] = $temp;
			}
		}
		return true;
	}

}
?>
