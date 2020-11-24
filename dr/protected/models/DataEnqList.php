<?php

class DataEnqList extends CListPageModel
{
	public $city_list;
	public $year_from;
	public $year_to;
	public $month_from;
	public $month_to;
	public $data_type;
	
	public $data_list = array();
	
	public function init() {
		$now = strtotime(date("Y-m-d"));
		$lastm = strtotime("-1 month", $now);
		
		$this->year_from = date("Y", $now);
		$this->month_from = date("m", '1');
		
		$this->year_to = date("Y", $lastm);
		$this->month_to = date("m", $lastm);

		$this->data_list = array(
							'DataTurnoverGrowth'=>array(
													'name'=>Yii::t('report','Turnover Growth'),
												),
						);
	}

	public function attributeLabels()
	{
		$label_1 = array(	
			'data_type'=>Yii::t('misc','Type'),
			'city_list'=>Yii::t('misc','City'),
			'year_from'=>Yii::t('misc','Year From'),
			'year_to'=>Yii::t('misc','Year To'),
			'month_from'=>Yii::t('misc','Month From'),
			'month_to'=>Yii::t('misc','Month To'),
		);
		$label_2 = empty($this->data) ? array() : $this->data->attributeLabels();
		return array_merge($label_1, $label_2);
	}
	
	public function rules() {	
		return array(
				array('year_from, year_to, month_from, month_to, data_type','required',),
				array('city_list','safe',),
			);
	}

	public function retrieveData() {
		$start_dt = $this->year_from.'-'.$this->month_from.'-1';
		$tmp_dt = strtotime($this->year_to.'-'.$this->month_to.'-1');
		$end_dt = date("Y-m-d", strtotime('-1 day',strtotime("+1 month", $tmp_dt)));
		
		$modelname = $this->data_type;
		$model = new $modelname;
		
		$suffix = Yii::app()->params['envSuffix'];
//		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.*, c.name as city_name, b.status 
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
				and (a.company_id=$id or a.company_name like concat('$code','%') 
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
				);
		return array_merge($rtn1, $rtn2);
	}
	
	public function getDataTypeList() {
		$rtn = array();
		foreach ($this->data_list as $key=>$items) {
			$rtn[$key] = $items['name'];
		}
		return $rtn;
	}
}
