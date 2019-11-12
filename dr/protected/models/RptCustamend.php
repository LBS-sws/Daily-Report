<?php
class RptCustamend extends ReportData2 {
	public function fields() {
		return array(
			'status_dt'=>array('label'=>Yii::t('service','New Date'),'width'=>18,'align'=>'C'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'b4_service'=>array('label'=>Yii::t('service','Service'),'width'=>30,'align'=>'L'),
			'b4_amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>30,'align'=>'L'),
			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'amt_install'=>array('label'=>Yii::t('service','Install Amt'),'width'=>15,'align'=>'C'),
			'diff_amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'diff_amt_year'=>array('label'=>Yii::t('service','Yearly Amt'),'width'=>15,'align'=>'C'),
			'need_install'=>array('label'=>Yii::t('service','Installation'),'width'=>10,'align'=>'C'),
			'salesman'=>array('label'=>Yii::t('service','Salesman'),'width'=>20,'align'=>'L'),
            'othersalesman'=>array('label'=>Yii::t('service','OtherSalesman'),'width'=>20,'align'=>'L'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
			'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
			'first_dt'=>array('label'=>Yii::t('service','First Service Date'),'width'=>18,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('service','Remarks'),'width'=>30,'align'=>'L'),
		);
	}

	public function header_structure() {
		return array(
			'status_dt',
			'company_name',
			'nature',
			array(
				'label'=>Yii::t('service','Before'),
				'child'=>array(
					'b4_service',
					'b4_amt_month',
				),
			),
			array(
				'label'=>Yii::t('service','After'),
				'child'=>array(
					'service',
					'amt_month',
					'amt_install',
				)
			),
			array(
				'label'=>Yii::t('service','Difference'),
				'child'=>array(
					'diff_amt_month',
					'diff_amt_year',
				),
			),
			'need_install',
			'salesman',
			'sign_dt',
			'ctrt_period',
			'ctrt_end_dt',
			'first_dt',
			'remarks'
		);
	}

	public function groups() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>321,'align'=>'L'),
			),
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*, b.description as nature, c.description as customer_type
					from swo_service a
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_customer_type c on a.cust_type=c.id
				where a.status='A' and a.city='".$city."' 
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			if ($where!='') $sql .= $where;	
		}
		$sql .= " order by c.description, a.status_dt";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['type'] = $row['customer_type'];
				$temp['status_dt'] = General::toDate($row['status_dt']);
				$temp['company_name'] = $row['company_name'];
				$temp['nature'] = $row['nature'];
				$temp['b4_service'] = $row['b4_service'];
				$temp['b4_amt_month'] = number_format(($row['b4_paid_type']=='1'?$row['b4_amt_paid']:($row['b4_paid_type']=='M'?$row['b4_amt_paid']:round($row['b4_amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2))),2,'.','');
				$temp['service'] = $row['service'];
				$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2))),2,'.','');
				$temp['amt_install'] = $row['amt_install'];
				$temp['need_install'] = ($row['need_install']=='Y') ? Yii::t('misc','Yes') : Yii::t('misc','No');
				$temp['diff_amt_month'] = number_format(($temp['amt_month']-$temp['b4_amt_month']),2,'.','');
//				$temp['diff_amt_year'] = number_format((($temp['amt_month']-$temp['b4_amt_month'])*($row['paid_type']=='1'?1:($row['paid_type']=='M'?($row['ctrt_period']<12?$row['ctrt_period']:12):$row['ctrt_period']))),2,'.','');
				$temp['diff_amt_year'] = number_format((
					($row['amt_paid']*($row['paid_type']=='M'?($row['ctrt_period']<12?$row['ctrt_period']:12):1))
					-($row['b4_amt_paid']*($row['b4_paid_type']=='M'?($row['ctrt_period']<12?$row['ctrt_period']:12):1))
					),2,'.','');
				$temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
				$temp['first_dt'] = General::toDate($row['first_dt']);
				$temp['remarks'] = $row['remarks'];

				$this->data[] = $temp;
			}
		}
		return true;
	}
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>
