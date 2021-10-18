<?php
class RptCustomerID extends ReportData2 {
//	private $customerType = "N";
	protected $customerType = "N";		// 為應付日报表总汇增加ID服务内容 - Percy

	public function fields() {
        //N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
		$list = array();
		switch ($this->customerType){
            case 'N':
            	$dt_name = 'New Date';
                $list = array(
                	"status_dt","company_name","cust_type","nature","pieces","service","pay_week",
                	"prepay_month","amt_paid","ctrt_period","amt_money","need_install","amt_install","sign_dt",
                	"ctrt_end_dt","equip_install_dt","freq","salesman","othersalesman","technician","cont_info",
                	"remarks2","remarks","back_date","back_money","put_month","out_month"
				);
            	break;
            case 'C':
            	$dt_name = 'Renew Date';
                $list = array(
                    "status_dt","company_name","cust_type","nature","pieces","service","pay_week",
                    "amt_paid","ctrt_period","amt_money","need_install","amt_install","sign_dt",
                    "ctrt_end_dt","equip_install_dt","freq","salesman","othersalesman","technician","cont_info",
                    "remarks2","remarks","back_date","back_money","put_month","out_month"
                );
            	break;
            case 'A':
            	$dt_name = 'Amend Date';
                $list = array(
                    "status_dt","company_name","cust_type","nature",
					"amt_paid"=>"b4_amt_paid","amt_money"=>"b4_amt_money","pieces"=>"b4_pieces","cust_type_end"=>"b4_cust_type_end",
					"amt_paid","amt_money","pieces","cust_type_end",
                    "amt_paid_diff","amt_year",
					"need_install","amt_install","sign_dt","ctrt_period",
                    "ctrt_end_dt","equip_install_dt","freq","salesman","othersalesman","technician","cont_info",
                    "remarks2","remarks","back_date","back_money","put_month","out_month"
                );
            	break;
            case 'S':
            	$dt_name = 'Suspend Date';
                $list = array(
                    "status_dt","company_name","cust_type","nature","pieces","service","pay_week",
                    "amt_paid","ctrt_period","amt_money","need_install","amt_install","sign_dt",
                    "ctrt_end_dt","equip_install_dt","salesman","othersalesman","technician","cont_info",
                    "remarks2","remarks"
                );
            	break;
            case 'R':
            	$dt_name = 'Resume Date';
                $list = array(
                    "status_dt","company_name","cust_type","nature","pieces","service","pay_week",
                    "amt_paid","ctrt_period","amt_money","need_install","amt_install","sign_dt",
                    "ctrt_end_dt","equip_install_dt","salesman","othersalesman","technician","cont_info",
                    "remarks2","remarks"
                );
            	break;
            case 'T':
            	$dt_name = 'Terminate Date';
                $list = array(
                    "status_dt","company_name","cust_type","nature","pieces","service","pay_week",
                    "amt_paid","ctrt_period","amt_money","all_number","surplus",
					"need_install","amt_install","sign_dt",
                    "ctrt_end_dt","equip_install_dt","salesman","othersalesman","technician","reason","cont_info",
                    "remarks2","remarks"
                );
            	break;
			default:
                $dt_name = 'New Date';
		}
        $arr=array();
        $arr['status_dt'] = array('label'=>Yii::t('service',$dt_name),'width'=>18,'align'=>'C');
        //客户编号及名称
        $arr['company_name'] = array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L');
        //客户类别
        $arr['cust_type'] = array('label'=>Yii::t('service','Customer Type'),'width'=>40,'align'=>'L');
        //性质
        $arr['nature'] = array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L');
        //机器数量
        $arr['pieces'] = array('label'=>Yii::t('service','machine number'),'width'=>15,'align'=>'L');
        //机器型号
        $arr['cust_type_end'] = array('label'=>Yii::t('service','Customer type end'),'width'=>15,'align'=>'L');
        //服务內容
        $arr['service'] = array('label'=>Yii::t('service','Service'),'width'=>31,'align'=>'L');
        //付款周期
        $arr['pay_week'] = array('label'=>Yii::t('service','pay week'),'width'=>20,'align'=>'L');
        //预付月数
        $arr['prepay_month'] = array('label' => Yii::t('service', 'Prepay Month'), 'width' => 15, 'align' => 'L');
        //月金额
        $arr['amt_paid'] = array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C');
        //月金额
        $arr['amt_paid_diff'] = array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C');
        //年金额
        $arr['amt_year'] = array('label'=>Yii::t('service','Yearly Amt'),'width'=>15,'align'=>'C');
        //合同年限(月)
        $arr['ctrt_period'] = array('label' => Yii::t('service', 'Contract Period'), 'width' => 15, 'align' => 'C');
        //合同总金额
        $arr['amt_money'] = array('label'=>Yii::t('service','all money'),'width'=>15,'align'=>'C');
        //实际发放月数
        $arr['all_number'] = array('label'=>Yii::t('service','put month'),'width'=>15,'align'=>'C');
        //剩余月数
        $arr['surplus'] = array('label'=>Yii::t('service','surplus month'),'width'=>15,'align'=>'C');
        //是否收取押金
        $arr['need_install'] = array('label'=>Yii::t('service','Whether to charge deposit'),'width'=>20,'align'=>'C');
        //机器押金
        $arr['amt_install'] = array('label'=>Yii::t('service','deposit machine'),'width'=>15,'align'=>'C');
        //合同开始日期
        $arr['sign_dt'] = array('label'=>Yii::t('service','Contract Start Date'),'width'=>20,'align'=>'C');
        //合同终止日期
        $arr['ctrt_end_dt'] = array('label'=>Yii::t('service','Contract End Date'),'width'=>20,'align'=>'C');
        //签约日期
        $arr['equip_install_dt'] = array('label'=>Yii::t('service','Sign Date'),'width'=>20,'align'=>'C');
        //服务次数
        $arr['freq']=array('label'=>Yii::t('service','Frequency'),'width'=>15,'align'=>'C');
		//业务员
        $arr['salesman'] = array('label'=>Yii::t('service','Resp. Sales'),'width'=>20,'align'=>'L');
        //被跨区业务员
        $arr['othersalesman'] = array('label'=>Yii::t('service','OtherSalesman'),'width'=>20,'align'=>'L');
        //负责技术员
        $arr['technician'] = array('label'=>Yii::t('service','Resp. Tech.'),'width'=>20,'align'=>'L');
        //客户联系/电话
        $arr['cont_info'] = array('label'=>Yii::t('service','Contact'),'width'=>40,'align'=>'L');
        //备注
        $arr['remarks2'] = array('label'=>Yii::t('service','Remarks'),'width'=>40,'align'=>'L');
        //跨区明细
        $arr['remarks'] = array('label'=>Yii::t('service','Cross Area Remarks'),'width'=>40,'align'=>'L');
        //終止原因
        $arr['reason'] = array('label'=>Yii::t('service','Stop Remark'),'width'=>40,'align'=>'L');
        //回款日期
        $arr['back_date'] = array('label'=>Yii::t('service','back date'),'width'=>15,'align'=>'C');
        //回款金额
        $arr['back_money'] = array('label'=>Yii::t('service','back money'),'width'=>15,'align'=>'C');
        //实际发放月数
        $arr['put_month'] = array('label'=>Yii::t('service','put month'),'width'=>15,'align'=>'C');
        //剩余发放月数
        $arr['out_month'] = array('label'=>Yii::t('service','out month'),'width'=>15,'align'=>'C');

        $returnList = array();
        if(empty($list)){
        	$returnList = $arr;
		}else{
        	foreach ($list as $key=>$value){
        		if(is_numeric($key)){
        			$returnList[$value] = $arr[$value];
				}else{
        			$returnList[$value] = $arr[$key];
				}
			}
		}
        return $returnList;
	}

    public function header_structure() {
        //N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
        if($this->customerType=="A") {
            return array(
                "status_dt","company_name","cust_type","nature",
                array(
                    'label'=>Yii::t('service','Before'),
                    'child'=>array(
                        'b4_amt_paid',
                        'b4_amt_money',
                        'b4_pieces',
                        'b4_cust_type_end',
                    ),
                ),
                array(
                    'label'=>Yii::t('service','After'),
                    'child'=>array(
                        'amt_paid',
                        'amt_money',
                        'pieces',
                        'cust_type_end',
                    )
                ),
                array(
                    'label'=>Yii::t('service','Difference'),
                    'child'=>array(
                        'amt_paid_diff',
                        'amt_year',
                    )
                ),
                "need_install","amt_install","sign_dt","ctrt_period",
                "ctrt_end_dt","equip_install_dt","freq","salesman","othersalesman","technician","cont_info",
                "remarks2","remarks","back_date","back_money","put_month","out_month"
            );
        }else{
        	return array();
		}
    }

    public function report_structure() {
        $list = array();
        //N:新增 C:續約 A:更改 S:暫停 R:恢復 T:終止
		/* 因為表格內換行，所以不執行
        switch ($this->customerType) {
            case 'N':
                $list = array(
                    "status_dt", "company_name", "cust_type", "nature", "pieces", "service", "pay_week",
                    "prepay_month", "amt_paid", "ctrt_period", "amt_money", "need_install", "amt_install", "sign_dt",
                    "ctrt_end_dt", "equip_install_dt", "freq", "salesman", "othersalesman", "technician", "cont_info",
                    "remarks2", "remarks", array("back_date", "back_money", "put_month", "out_month")
                );
                break;
            case 'C':
                $list = array(
                    "status_dt", "company_name", "cust_type", "nature", "pieces", "service", "pay_week",
                    "amt_paid", "ctrt_period", "amt_money", "need_install", "amt_install", "sign_dt",
                    "ctrt_end_dt", "equip_install_dt", "freq", "salesman", "othersalesman", "technician", "cont_info",
                    "remarks2", "remarks", array("back_date", "back_money", "put_month", "out_month")
                );
                break;
            case 'A':
                $list = array(
                    "status_dt", "company_name", "cust_type", "nature",
                    "amt_paid" => "b4_amt_paid", "amt_money" => "b4_amt_money", "pieces" => "b4_pieces", "cust_type_end" => "b4_cust_type_end",
                    "amt_paid", "amt_money", "pieces", "cust_type_end",
                    "need_install", "amt_install", "sign_dt", "ctrt_period",
                    "ctrt_end_dt", "equip_install_dt", "freq", "salesman", "othersalesman", "technician", "cont_info",
                    "remarks2", "remarks", array("back_date", "back_money", "put_month", "out_month")
                );
                break;
			default:
				$list = array();
        }
		*/
        return $list;
    }
/*
	public function line_group() {
		return array(
			array(
				'type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>397,'align'=>'L'),
			),
		);
	}
	*/
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		if ($this->criteria->type!='?') { // 為應付日报表总汇增加ID服务内容 - Percy
			$type = $this->criteria->type;
			$type = in_array($type,array("N","C","S","R","A","T"))?$type:"N";
			$this->customerType = $type;
		}
		
		$sql = "select a.*,
			j.code as product_code,j.description as product_name,
			g.code as com_code,g.name as com_name,
			h.description as pay_week_name, b.description as nature,
			 c.description as customer_type
					from swo_serviceid a
					left outer join swo_nature b on a.nature_type=b.id 
					left outer join swo_customer_type_id c on a.cust_type=c.id
					left outer join swo_company g on a.company_id=g.id 
					left outer join swo_payweek h on a.pay_week=h.id 
					left outer join swo_product j on a.product_id=j.id 
				where a.status='$type' and a.city='".$city."' 
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
                $this->resetRow($row);
				$temp = array();
				$temp['status_dt'] = General::toDate($row['status_dt']);
				$temp['company_name'] = $row['com_code'].$row['com_name'];
				$temp['cust_type'] = $row['cust_type'];
				$temp['nature'] = $row['nature'];
				$temp['pieces'] = $row['pieces'];
				$temp['all_number'] = $row['all_number'];
				$temp['reason'] = $row['reason'];
				$temp['surplus'] = $row['surplus']."天";
				$temp['service'] = $row['product_code']." ".$row['product_name'];
				$temp['pay_week'] = $row['pay_week_name'];
				$temp['prepay_month'] = $row['prepay_month'];
				$temp['amt_paid'] = $row['amt_paid'];
				$temp['ctrt_period'] = $row['ctrt_period'];
				$temp['amt_money'] = $row['amt_money'];
				$temp['b4_amt_paid'] = $row['b4_amt_paid'];
				$temp['b4_amt_money'] = $row['b4_amt_money'];
				$temp['b4_pieces'] = $row['b4_pieces'];
				$temp['b4_cust_type_end'] = $row['b4_cust_type_end'];
				$temp['amt_paid_diff'] = $row['amt_paid']-$row['b4_amt_paid'];
				$temp['amt_year'] = $temp['amt_paid_diff']*12;
				$temp['cust_type_end'] = $row['cust_type_end'];
                $temp['need_install'] = ($row['need_install']=='Y') ? Yii::t('misc','Yes') : Yii::t('misc','No');
                $temp['amt_install'] = $row['amt_install'];
                $temp['sign_dt'] = General::toDate($row['sign_dt']);
                $temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
                $temp['equip_install_dt'] = General::toDate($row['equip_install_dt']);
                $temp['freq'] = $row['freq'];
                $temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
                $temp['technician'] = $row['technician'];
                $temp['cont_info'] = $row['cont_info'];
                $temp['remarks2'] = $row['remarks2'];
                $temp['remarks'] = $row['remarks'];
                $temp['detail']=$row['detail'];
                $temp['back_date'] = implode("\n",$row['back_date']);
                $temp['back_money'] = implode("\n",$row['back_money']);
                $temp['put_month'] = implode("\n",$row['put_month']);
                $temp['out_month'] = implode("\n",$row['out_month']);
				$this->data[] = $temp;
			}
		}
		return true;
	}

	private function resetRow(&$row){
        $row['salesman'] = General::getEmployeeCodeAndNameForID($row['salesman_id']);
        $row['othersalesman'] = General::getEmployeeCodeAndNameForID($row['othersalesman_id']);
        $row['technician'] = General::getEmployeeCodeAndNameForID($row['technician_id']);
        $row['b4_cust_type_end'] = CustomertypeIDForm::getCustTypeInfoNameForId($row['b4_cust_type_end']);
        $row['cust_type_end'] = CustomertypeIDForm::getCustTypeInfoNameForId($row['cust_type_end']);
        $list=array($row['customer_type']);
        if(!empty($row["cust_type_name"])){
            $list[] = CustomertypeIDForm::getCustTypeInfoNameForId($row["cust_type_name"]);
		}
        if(!empty($row["cust_type_three"])){
            $list[] = CustomertypeIDForm::getCustTypeInfoNameForId($row["cust_type_three"]);
		}
        if(!empty($row["cust_type_four"])){
            $list[] = CustomertypeIDForm::getCustTypeInfoNameForId($row["cust_type_four"]);
		}
        $row['cust_type'] = implode("/",$list);
        $row['detail'] = array();
        $row['back_date'] = array();
        $row['back_money'] = array();
        $row['put_month'] = array();
        $row['out_month'] = array();
        $details = Yii::app()->db->createCommand()->select("back_date,back_money,put_month,out_month")->from("swo_serviceid_info")
            ->where("serviceID_id=:id",array(":id"=>$row["id"]))->order("back_date asc")->queryAll();
        if($details){
            $row['detail'] = $details;
        	foreach ($details as $detail){
                $row['back_date'][] = $detail["back_date"];
                $row['back_money'][] = $detail["back_money"];
                $row['put_month'][] = $detail["put_month"];
                $row['out_month'][] = $detail["out_month"];
			}
		}
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>
