<?php

class CustomerEnqList extends CListPageModel
{
	public $company_code;
	public $company_name;
	public $company_status;
	public $city_list;
	public $company_type_list;
	public $show;

	
	public function attributeLabels()
	{
		return array(	
			'company_code'=>Yii::t('customer','Customer Code'),
			'company_name'=>Yii::t('customer','Customer Name'),
			'company_type_list'=>Yii::t('customer','Customer Type'),
			'company_status'=>Yii::t('customer','Status'),
			'full_name'=>Yii::t('customer','Full Name'),
			'cont_name'=>Yii::t('customer','Contact Name'),
			'cont_phone'=>Yii::t('customer','Contact Phone'),
			'city_name'=>Yii::t('misc','City'),
			'city_list'=>Yii::t('misc','City'),
			'status_dt'=>Yii::t('customer','Date'),
			'status'=>Yii::t('customer','Status'),
			'cust_type_desc'=>Yii::t('customer','Type'),
			'product_desc'=>Yii::t('customer','Product'),
			'first_dt'=>Yii::t('customer','First Date'),
			'amt_paid'=>Yii::t('customer','Amount'),
		);
	}
	
	public function rules()
	{	$rtn1 = parent::rules();
		$rtn2 =  array(
			array('company_code, company_name, company_type_list, company_status, city_list, show','safe',),
			);
		return array_merge($rtn1, $rtn2);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
//		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, c.name as city_name, b.status, b.type_list  
				from swo_company a
				inner join security$suffix.sec_city c on a.city=c.code
				left outer join swo_company_status b on a.id=b.id
				where 1=1
			";
		$sql2 = "select count(a.id)
				from swo_company a
				inner join security$suffix.sec_city c on a.city=c.code
				left outer join swo_company_status b on a.id=b.id
				where 1=1
			";
		$clause = "";
		if (!empty($this->company_code)) {
			$svalue = str_replace("'","\'",$this->company_code);
			$clause .= (empty($clause) ? '' : ' and ')."a.code like '%$svalue%'";
		}
		if (!empty($this->company_name)) {
			$svalue = str_replace("'","\'",$this->company_name);
			$clause .= (empty($clause) ? '' : ' and ')."a.name like '%$svalue%'";
		}
		if (!empty($this->company_status)) {
			switch($this->company_status) {
				case 'A':
					$clause .= (empty($clause) ? '' : ' and ')."b.status='A'";
					break;
				case 'T':
					$clause .= (empty($clause) ? '' : ' and ')."b.status='T'";
					break;
				case 'U':
					$clause .= (empty($clause) ? '' : ' and ')."(b.status='U' or b.status is null)";
					break;
			}
		}
		if (!empty($this->company_type_list)) {
			$svalue = '';
			foreach ($this->company_type_list as $item) {
				$svalue .= ($svalue=='' ? '' : ' or ')."position('/$item/' in b.type_list)>0";
			}
			$clause .= (empty($clause) ? '' : ' and ')."($svalue)";
		}
		if (!empty($this->city_list)) {
			$svalue = '';
			foreach ($this->city_list as $item) {
				$svalue .= ($svalue=='' ? '' : ',')."'$item'";
			}
			$clause .= (empty($clause) ? '' : ' and ').'a.city in ('.$svalue.')';
		}
		if ($clause!='') $clause = ' and ('.$clause.')'; 
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
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
				$detail = $this->getServiceList($record['id'], $record['code'], $record['name'], $record['city']);
				$this->attr[] = array(
					'company_id'=>$record['id'],
					'company_code'=>$record['code'],
					'company_name'=>$record['name'],
					'full_name'=>$record['full_name'],
					'cont_name'=>$record['cont_name'],
					'cont_phone'=>$record['cont_phone'],
					'city_name'=>$record['city_name'],
					'company_status'=>$this->statusDesc($record['status']),
					'detail'=>$detail,
				);
			}
		}
		$session = Yii::app()->session;
		$session[$this->criteriaName()] = $this->getCriteria();
		return true;
	}

	protected function getServiceList($id, $code, $name, $city) {
		$rtn = array();
		$name = str_replace("'","\'",$name);
		$sql = "select a.*, c.description as cust_type_desc, d.description as product_desc   
				from swo_service a
				left outer join swo_service b on a.company_name=b.company_name 
					and a.status_dt < b.status_dt and a.cust_type=b.cust_type
				left outer join swo_customer_type c on a.cust_type=c.id 
				left outer join swo_product d on a.product_id=d.id 
				where b.id is null and a.city='$city'
				and (a.company_id=$id or a.company_name like concat('$code',' %') 
				or a.company_name like concat('%','$name'));
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$rtn[] = array(
							'status_dt'=>General::toDate($row['status_dt']),
							'status'=>($row['status']=='T' ? $this->statusDesc('T') : $this->statusDesc('A')),
							'service'=>$row['service'],
							'first_dt'=>General::toDate($row['first_dt']),
							'amt_paid'=>$row['amt_paid'],
							'cust_type_desc'=>$row['cust_type_desc'],
							'product_desc'=>$row['product_desc'],
							'paid_type'=>($row['paid_type']=='M' ? Yii::t('service','Monthly')
											: ($row['paid_type']=='Y' ? Yii::t('service','Yearly')
												: ($row['paid_type']=='1' ? Yii::t('service','One time') : ''))
									),
						);
			}
		} 
		return $rtn;
	}
	
	public function getCriteria() {
		$rtn1 = parent::getCriteria();
		$rtn2 = array(
					'company_code'=>$this->company_code,
					'company_name'=>$this->company_name,
					'company_status'=>$this->company_status,
					'city_list'=>$this->city_list,
					'company_type_list'=>$this->company_type_list,
				);
		return array_merge($rtn1, $rtn2);
	}
	
	public function statusDesc($invalue) {
		switch($invalue) {
			case 'A': return Yii::t('customer','Active'); break;
			case 'T': return Yii::t('customer','Terminated'); break;
			default: return Yii::t('customer','Unknown');
		};
	}
}
