<?php
class RptCustterall extends ReportData2 {
	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
		    //狀態
			'status_desc'=>array('label'=>Yii::t('service','Record Type'),'width'=>12,'align'=>'C'),
			//日期
            'status_dt'=>array('label'=>Yii::t('customer','Date'),'width'=>14,'align'=>'C'),
            //客户编号及名称
            'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>26,'align'=>'C'),
            //客户类别
            'type'=>array('label'=>Yii::t('customer','Customer Type'),'width'=>14,'align'=>'C'),
            //客户联系
            'contact_name'=>array('label'=>Yii::t('customer','Contact Name'),'width'=>14,'align'=>'C'),
            //客户电话
            'contact_phone'=>array('label'=>Yii::t('customer','Contact Phone'),'width'=>14,'align'=>'C'),
            //客户地址
            'address'=>array('label'=>Yii::t('customer','Address'),'width'=>34,'align'=>'C'),
            //性质
            'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>10,'align'=>'C'),
            //服务內容
            'service'=>array('label'=>Yii::t('service','Service'),'width'=>25,'align'=>'C'),
            //变动原因
            'reason'=>array('label'=>Yii::t('service','Reason'),'width'=>25,'align'=>'C'),
            //月金额 paid_type,ctrt_period
            'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>12,'align'=>'C'),
            //年金额
            'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>12,'align'=>'C'),
            //业务员
            'salesman'=>array('label'=>Yii::t('service','Resp. Sales'),'width'=>17,'align'=>'C'),
            //被跨区业务员
            'othersalesman'=>array('label'=>Yii::t('service','OtherSalesman'),'width'=>17,'align'=>'C'),
            //负责技术员
            'technician'=>array('label'=>Yii::t('service','Resp. Tech.'),'width'=>17,'align'=>'C'),
            //签约日期
            'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>15,'align'=>'C'),
            //合同年限(月)
            'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>15,'align'=>'C'),
            //合同终止日期
            'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>15,'align'=>'C'),
        );
	}
	
	public function retrieveData() {
	    $this->data=array();
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
        $city_allow = City::model()->getDescendantList($city);
        $city_allow .= (empty($city_allow)) ? "'$city'" : ",'$city'";
		$sql = "select a.*, b.description as nature, c.description as customer_type, d.cont_name, d.cont_phone, d.address
					from swo_service a
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_customer_type c on a.cust_type=c.id
					left outer join swo_company d on a.company_id=d.id
				where a.status in ('T','S','R') and a.city in ({$city_allow})
		";
		if (isset($this->criteria)) {
			$where = '';
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
			if ($where!='') $sql .= $where;	
		}
		$sql .= " order by a.city,d.id asc,a.status_dt asc";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$contact_name = $row['cont_name'];
				$contact_phone = $row['cont_phone'];
				$address = $row['address'];
				if (empty($row['cont_name']) && empty($row['cont_phone']) && empty($row['address'])) {
					$company_name = $row['company_name'];
					$sql1 = "select * from swo_company where :company_name regexp code and city='$city' limit 1";
					$command=Yii::app()->db->createCommand($sql1);
					$command->bindParam(':company_name',$company_name,PDO::PARAM_STR);
					$rec = $command->queryRow();
/*
					$sql1 = "select * from swo_company where '$company_name' regexp code and city='$city' limit 1";
					$rec = Yii::app()->db->createCommand($sql1)->queryRow();
*/
					if ($rec!==false) {
						$contact_name = $rec['cont_name'];
						$contact_phone = $rec['cont_phone'];
						$address = $rec['address'];
					}
				}

				$temp = array();
                $temp['city_name'] = General::getCityName($row["city"]);
				$temp['status_desc'] = self::statusDesc($row['status']);
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
                $temp['all_number'] = $row['all_number'];
                $temp['surplus'] = $row['surplus'];
				$temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
                $temp['technician'] = $row['technician'];
				$temp['sign_dt'] = General::toDate($row['sign_dt']);
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
				$temp['reason'] = $row['reason'];
				$temp['lud'] = General::toDate($row['lcd']);
				$this->data[] = $temp;
			}
		}
		return true;
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
        $city_name = parent::getReportName().$city_name;

        $city_name=str_replace("/", "_", $city_name);
        return $city_name;
	}

	private function statusDesc($status){
	    switch ($status){
            case "T"://終止
                return Yii::t("service","Terminate");
            case "S"://暫停
                return Yii::t("service","Suspend");
            case "R"://恢復
                return Yii::t("service","Resume");
            default:
                return Yii::t("service","none");
        }
    }
}
?>