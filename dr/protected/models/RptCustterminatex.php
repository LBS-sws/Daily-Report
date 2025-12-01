<?php
class RptCustterminatex extends ReportData2 {
	public function fields() {
		return array(
			'lud'=>array('label'=>Yii::t('service','Entry Date'),'width'=>18,'align'=>'C'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
			'contact_name'=>array('label'=>Yii::t('customer','Contact Name'),'width'=>30,'align'=>'L'),
			'contact_phone'=>array('label'=>Yii::t('customer','Contact Phone'),'width'=>20,'align'=>'L'),
			'address'=>array('label'=>Yii::t('customer','Address'),'width'=>40,'align'=>'L'),
			'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
			'service'=>array('label'=>Yii::t('service','Service'),'width'=>30,'align'=>'L'),
			'reason'=>array('label'=>Yii::t('service','Reason'),'width'=>30,'align'=>'L'),
			'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
			'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
			'salesman'=>array('label'=>Yii::t('service','Resp. Sales'),'width'=>20,'align'=>'L'),
            'othersalesman'=>array('label'=>Yii::t('service','OtherSalesman'),'width'=>20,'align'=>'L'),
            'technician'=>array('label'=>Yii::t('service','Resp. Tech.'),'width'=>20,'align'=>'L'),
			'status_dt'=>array('label'=>Yii::t('service','Terminate Date'),'width'=>22,'align'=>'C'),
			'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
			'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
			'org_equip_qty'=>array('label'=>Yii::t('service','Org. Equip. Qty'),'width'=>18,'align'=>'C'),
			'rtn_equip_qty'=>array('label'=>Yii::t('service','Return Equip. Qty'),'width'=>18,'align'=>'C'),
			'diff_equip_qty'=>array('label'=>Yii::t('service','Diff. Qty'),'width'=>18,'align'=>'C'),
			'remarks2'=>array('label'=>Yii::t('service','Remarks'),'width'=>30,'align'=>'L'),
		);
	}

	public function groups() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>294,'align'=>'L'),
			),
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*, b.description as nature, c.description as customer_type, d.cont_name, d.cont_phone, d.address
					from swo_service a
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_customer_type c on a.cust_type=c.id
					left outer join swo_company d on a.company_id=d.id
				where a.status='T' and a.city='".$city."' 
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
				$contact_name = is_null($row['cont_name']) ? '' : $row['cont_name'];
				$contact_phone = is_null($row['cont_phone']) ? '' : $row['cont_phone'];
				$address = is_null($row['address']) ? '' : $row['address'];
var_dump($contact_name);
var_dump($contact_phone);
var_dump($address);
				if (empty($contact_name) && empty($contact_phone) && empty($address)) {
echo "Get ...\n";
					$company_name = $row['company_name'];
					$sql1 = "select * from swo_company where :company_name regexp code and city='$city' limit 1";
					$command=Yii::app()->db->createCommand($sql1);
					$command->bindParam(':company_name',$company_name,PDO::PARAM_STR);
					$rec = $command->queryRow();
var_dump($rec);
					if ($rec!==false) {
						$contact_name = $rec['cont_name'];
						$contact_phone = $rec['cont_phone'];
						$address = $rec['address'];
					}
				}

				$temp = array();
				$temp['type'] = $row['customer_type'];
				$temp['status_dt'] = General::toDate($row['status_dt']);
				$temp['company_name'] = $row['company_name'];
				$temp['contact_name'] = $contact_name;
				$temp['contact_phone'] = $contact_phone;
				$temp['address'] = $address;
				$temp['nature'] = $row['nature'];
				$temp['service'] = $row['service'];
				$temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:12),2))),2,'.','');
				$temp['amt_year'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:($row['paid_type']=='M'?$row['amt_paid']*($row['ctrt_period']<12&&!empty($row['ctrt_period'])?$row['ctrt_period']:12):$row['amt_paid'])),2,'.','');
				$temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
                $temp['technician'] = $row['technician'];
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
				$temp['reason'] = $row['reason'];
				$temp['lud'] = General::toDate($row['lcd']);
				$temp['org_equip_qty'] = $row['org_equip_qty'];
				$temp['rtn_equip_qty'] = $row['rtn_equip_qty'];
				$temp['diff_equip_qty'] = $row['rtn_equip_qty'] - $row['org_equip_qty'];
				$temp['remarks2'] = $row['remarks2'];
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
