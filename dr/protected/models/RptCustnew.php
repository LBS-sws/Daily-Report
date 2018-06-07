<?php
class RptCustnew extends ReportData2 {	public function fields() {		return array(			'status_dt'=>array('label'=>Yii::t('service','New Date'),'width'=>18,'align'=>'C'),			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>40,'align'=>'L'),			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),			'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
			'amt_install'=>array('label'=>Yii::t('service','Install Amt'),'width'=>15,'align'=>'C'),			'need_install'=>array('label'=>Yii::t('service','Installation'),'width'=>10,'align'=>'C'),
			'salesman'=>array('label'=>Yii::t('service','Salesman'),'width'=>20,'align'=>'L'),			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),			'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
			'cont_info'=>array('label'=>Yii::t('service','Contact'),'width'=>40,'align'=>'L'),
			'first_dt'=>array('label'=>Yii::t('service','First Service Date'),'width'=>18,'align'=>'C'),
			'first_tech'=>array('label'=>Yii::t('service','First Technician'),'width'=>30,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('service','Remarks'),'width'=>40,'align'=>'L'),
			'equip_install_dt'=>array('label'=>Yii::t('service','Install Date'),'width'=>18,'align'=>'C'),
			'diff_ctrt_dt'=>array('label'=>Yii::t('service','Diff. btw Contract Date'),'width'=>15,'align'=>'C'),
			'diff_first_dt'=>array('label'=>Yii::t('service','Diff. btw First Service Date'),'width'=>15,'align'=>'C'),
		);		}
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
		
		$sql = "select a.*, b.description as nature, c.description as customer_type
					from swo_service a
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_customer_type c on a.cust_type=c.id
				where a.status='N' and a.city='".$city."' 
		";		if (isset($this->criteria)) {			$where = '';			if (isset($this->criteria->start_dt))				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";			if (isset($this->criteria->end_dt))				$where .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";			if ($where!='') $sql .= $where;			}		$sql .= " order by c.description, a.status_dt";		$rows = Yii::app()->db->createCommand($sql)->queryAll();		if (count($rows) > 0) {			foreach ($rows as $row) {				$temp = array();				$temp['type'] = $row['customer_type'];
				$temp['status_dt'] = General::toDate($row['status_dt']);				$temp['company_name'] = $row['company_name'];				$temp['nature'] = $row['nature'];
				$temp['service'] = $row['service'];				$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2)))
									,2,'.','');
				$period = empty($row['ctrt_period'])?0:($row['ctrt_period']<12?$row['ctrt_period']:12);				$temp['amt_year'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
										($row['paid_type']=='M'?$row['amt_paid']*$period:$row['amt_paid']))
									,2,'.','');
				$temp['amt_install'] = number_format($row['amt_install'],2,'.','');				$temp['need_install'] = ($row['amt_install']=='Y') ? Yii::t('misc','Yes') : Yii::t('misc','No');
				$temp['salesman'] = $row['salesman'];
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
				$temp['cont_info'] = $row['cont_info'];
				$temp['first_dt'] = General::toDate($row['first_dt']);
				$temp['first_tech'] = $row['first_tech'];				$temp['remarks'] = $row['remarks'];
				$temp['equip_install_dt'] = General::toDate($row['equip_install_dt']);
				$temp['diff_ctrt_dt'] = (empty($temp['equip_install_dt']) || empty($temp['sign_dt'])) ? '' :
					(strtotime($row['equip_install_dt'])-strtotime($row['sign_dt']))/86400;
				$temp['diff_first_dt'] = (empty($temp['sign_dt']) || empty($temp['first_dt'])) ? '' :
					(strtotime($temp['first_dt'])-strtotime($temp['sign_dt']))/86400;

				$this->data[] = $temp;			}		}		return true;	}
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>