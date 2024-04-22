<?php
class RptSupplier extends ReportData2 {
	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
			'code'=>array('label'=>Yii::t('supplier','Code'),'width'=>22,'align'=>'C'),//供应商编号
			'name'=>array('label'=>Yii::t('supplier','Name'),'width'=>40,'align'=>'L'),//名称
			'full_name'=>array('label'=>Yii::t('supplier','Full Name'),'width'=>40,'align'=>'L'),//全称
			'tax_reg_no'=>array('label'=>Yii::t('code','Taxpayer No.'),'width'=>22,'align'=>'L'),//纳税人登记号
			'cont_name'=>array('label'=>Yii::t('supplier','Contact Name'),'width'=>30,'align'=>'L'),//供应商联系人
			'cont_phone'=>array('label'=>Yii::t('supplier','Contact Phone'),'width'=>30,'align'=>'L'),//供应商电话
			'address'=>array('label'=>Yii::t('supplier','Address'),'width'=>40,'align'=>'L'),//供应商地址
			'bank'=>array('label'=>Yii::t('supplier','Bank'),'width'=>30,'align'=>'L'),//付款账户
			'acct_no'=>array('label'=>Yii::t('supplier','Account No'),'width'=>30,'align'=>'L'),//账户号码
		);
	}

	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
		$city = $this->criteria->city;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
        $sql = "select a.*,b.name as city_name from swo_supplier a 
                LEFT JOIN security{$suffix}.sec_city b ON a.city = b.code
                where a.city in ({$city_allow})";
		$sql .= " order by a.city,a.id";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['city_name'] = $row['city_name'];
				$temp['code'] = $row['code'];
				$temp['name'] = $row['name'];
				$temp['full_name'] = $row['full_name'];
				$temp['tax_reg_no'] = " ".$row['tax_reg_no'];
				$temp['cont_name'] = $row['cont_name'];
				$temp['cont_phone'] = " ".$row['cont_phone'];
				$temp['address'] = $row['address'];
				$temp['bank'] = " ".$row['bank'];
				$temp['acct_no'] = " ".$row['acct_no'];
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