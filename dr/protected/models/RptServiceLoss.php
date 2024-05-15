<?php
class RptServiceLoss extends ReportData2 {
    public $dataList=array(
        "sumRate"=>array(),//占比
        "sumDetail"=>array(),//暂停或终止-新增明细
    );

    protected function addDataList($city,$row){
        if(!key_exists($city,$this->dataList["sumRate"])){
            $this->dataList["sumRate"][$city]=array(
                "city_name"=>$row["city_name"],
                "year_amt"=>0,//2023年新增金额
                "sum_pause"=>0,
                "sum_pause_rate"=>0,
                "sum_stop"=>0,
                "sum_stop_rate"=>0,
                "sum_all"=>0,
                "sum_all_rate"=>0,
                "re_sign_amt"=>0,
                "re_sign_rate"=>0,
            );
        }
        $this->dataList["sumRate"][$city]["year_amt"]+=trim($row["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_pause"]+=trim($row["pause_amt"]);
        $this->dataList["sumRate"][$city]["sum_stop"]+=trim($row["stop_amt"]);
        $this->dataList["sumRate"][$city]["re_sign_amt"]+=trim($row["re_sign_amt"]);
        $this->dataList["sumRate"][$city]["sum_all"]=$this->dataList["sumRate"][$city]["sum_pause"];
        $this->dataList["sumRate"][$city]["sum_all"]+=$this->dataList["sumRate"][$city]["sum_stop"];

        $this->dataList["sumRate"][$city]["sum_pause_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_pause"],$this->dataList["sumRate"][$city]["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_stop_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_stop"],$this->dataList["sumRate"][$city]["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_all_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_all"],$this->dataList["sumRate"][$city]["year_amt"]);
        $this->dataList["sumRate"][$city]["re_sign_rate"]=$this->numRate($this->dataList["sumRate"][$city]["re_sign_amt"],$this->dataList["sumRate"][$city]["year_amt"]);
    }

    protected function numRate($num,$sum){
        if(empty($sum)){
            return "";
        }else{
            $rate = ($num/$sum)*100;
            $rate = round($rate,2)."%";
            return $rate;
        }
    }

    protected function dateDiff($startDate,$endDate){
        $datetime1 = date_create($startDate);
        $datetime2 = date_create($endDate);

        $timestamp1 = $datetime1->getTimestamp();
        $timestamp2 = $datetime2->getTimestamp();

        $diff_seconds = $timestamp2 - $timestamp1;
        $diff_day = round($diff_seconds / (60 * 60 * 24));
        $diff_month = round($diff_seconds / (60 * 60 * 24 * 30));
        return array("diff_day"=>$diff_day,"diff_month"=>$diff_month);
    }

    public function getSheetExpr(){//额外的sheet
        $year = $this->criteria->year;
        $sheetList=array(
            array(//占比sheet
                "header_title"=>"占比",
                "data"=>$this->dataList["sumRate"],
                "line_def"=>array(//页头
                    'city_name'=>array('label'=>Yii::t('app','City'),'width'=>13,'align'=>'C'),//城市
                    'year_amt'=>array('label'=>$year.Yii::t('service',' year new amount'),'width'=>20,'align'=>'C'),//2023年新增金额
                    'sum_pause'=>array('label'=>Yii::t('service','pause amount'),'width'=>18,'align'=>'C'),//暂停金额
                    'sum_pause_rate'=>array('label'=>Yii::t('service','rate'),'width'=>18,'align'=>'C'),//百分比
                    'sum_stop'=>array('label'=>Yii::t('service','stop amount'),'width'=>18,'align'=>'C'),//终止金额
                    'sum_stop_rate'=>array('label'=>Yii::t('service','rate'),'width'=>18,'align'=>'C'),//百分比
                    'sum_all'=>array('label'=>Yii::t('service','pause + stop'),'width'=>20,'align'=>'C'),//暂停+终止金额
                    'sum_all_rate'=>array('label'=>Yii::t('service','rate'),'width'=>18,'align'=>'C'),//百分比
                    're_sign_amt'=>array('label'=>Yii::t('report','Re signing amount'),'width'=>22,'align'=>'C'),//重签金额（暂停或终止3个月后新增的单）
                    're_sign_rate'=>array('label'=>Yii::t('service','rate'),'width'=>22,'align'=>'C'),//百分比
                ),
            ),
            array(//暂停或终止-新增明细sheet
                "header_title"=>"暂停或终止-新增明细",
                "data"=>$this->dataList["sumDetail"],
                "line_def"=>array(//页头
                    'city_name'=>array('label'=>Yii::t('app','City'),'width'=>13,'align'=>'C'),//城市
                    'status'=>array('label'=>Yii::t('service','Record Type'),'width'=>20,'align'=>'C'),//记录类别
                    'status_dt'=>array('label'=>Yii::t('report','Date'),'width'=>20,'align'=>'C'),//日期
                    'customer'=>array('label'=>Yii::t('service','Customer'),'width'=>18,'align'=>'C'),//客户编号及名称
                    'cust_type_name'=>array('label'=>Yii::t('service','Customer Type'),'width'=>18,'align'=>'C'),//客户类别
                    'nature'=>array('label'=>Yii::t('service','Nature'),'width'=>18,'align'=>'C'),//性质
                    'reason'=>array('label'=>Yii::t('service','Reason'),'width'=>18,'align'=>'C'),//变动原因
                    'month_amt'=>array('label'=>Yii::t('service','Monthly'),'width'=>20,'align'=>'C'),//月金额
                    'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>18,'align'=>'C'),//合同年限(月)
                    'year_amt'=>array('label'=>Yii::t('service','Yearly'),'width'=>18,'align'=>'C'),//年金额
                ),
            ),
        );
        return $sheetList;
    }

    protected function addDataListForDetail($addRow,$stopRow){
        $this->dataList["sumDetail"][] = array(
            "city_name"=>$addRow["city_name"],
            "status"=>$stopRow["status_desc"],
            "status_dt"=>$stopRow["status_dt"],
            "customer"=>$stopRow["company_name"],
            "cust_type_name"=>$stopRow["cust_type_name"],
            "nature"=>$stopRow["nature_name"],
            "reason"=>$stopRow["reason"],
            "month_amt"=>$stopRow["month_amt"],
            "ctrt_period"=>$stopRow["ctrt_period"],
            "year_amt"=>$stopRow["year_amt"],
        );
        $this->dataList["sumDetail"][] = array(
            "city_name"=>$addRow["city_name"],
            "status"=>$addRow["status_desc"],
            "status_dt"=>$addRow["status_dt"],
            "customer"=>$addRow["company_name"],
            "cust_type_name"=>$addRow["cust_type_name"],
            "nature"=>$addRow["nature_name"],
            "reason"=>"",
            "month_amt"=>$addRow["month_amt"],
            "ctrt_period"=>$addRow["ctrt_period"],
            "year_amt"=>$addRow["year_amt"],
        );
    }

	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>10,'align'=>'C'),//城市
			'customer'=>array('label'=>Yii::t('service','Customer'),'width'=>31,'align'=>'C'),//客户编号及名称
			'nature'=>array('label'=>Yii::t('service','Nature'),'width'=>13,'align'=>'C'),//性质
			'month_amt'=>array('label'=>Yii::t('service','Monthly'),'width'=>13,'align'=>'C'),//月金额
			'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>15,'align'=>'C'),//合同年限(月)
			'year_amt'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),//年金额
			'new_date'=>array('label'=>Yii::t('service','New Date'),'width'=>15,'align'=>'C'),//新增日期
			'pause_date'=>array('label'=>Yii::t('service','Suspend Date'),'width'=>15,'align'=>'C'),//暂停日期
			'stop_date'=>array('label'=>Yii::t('service','Terminate Date'),'width'=>15,'align'=>'C'),//终止日期
			'contract_len'=>array('label'=>Yii::t('service','contract length'),'width'=>18,'align'=>'C'),//合同执行月数
		);
	}

	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
		$city = $this->criteria->city;
		$year = $this->criteria->year;
        $year = is_numeric($year)?intval($year):"2000";
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
        $inTypeList = Yii::app()->db->createCommand()->select("GROUP_CONCAT(id) as id_list")
            ->from("swo_customer_type")
            ->where("rpt_cat!='INV' and single!=1")->queryRow();
        $inTypeList = $inTypeList?$inTypeList["id_list"]:"0";//排除INV及一次性服务
        $sql = "select a.*,b.name as city_name,f.contract_no from swo_service a 
                LEFT JOIN security{$suffix}.sec_city b ON a.city = b.code
                LEFT JOIN swo_service_contract_no f ON a.id = f.service_id
                where a.city in ({$city_allow}) AND DATE_FORMAT(a.status_dt,'%Y')='{$year}' 
                AND a.status='N' AND a.cust_type in ({$inTypeList}) AND a.paid_type in ('M','Y')";
		$sql .= " order by a.city,a.status_dt,a.id";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
			    $city = $row["city"];
                $row['status_dt'] = General::toMyDate($row['status_dt']);
                $row['pause_amt'] = 0;
                $row['stop_amt'] = 0;
                $row['re_sign_amt'] = 0;
                $row['ctrt_period'] = is_numeric($row['ctrt_period'])?floatval($row['ctrt_period']):0;
			    $amt_paid = is_numeric($row['amt_paid'])?floatval($row['amt_paid']):0;
			    if($row["paid_type"]=="M"){
                    $row['month_amt'] = $amt_paid;
                    $row['year_amt'] = $amt_paid*$row['ctrt_period'];
                }else{
                    $month_amt = empty($row['ctrt_period'])?0:$amt_paid/$row['ctrt_period'];
                    $row['month_amt'] = round($month_amt,2);
                    $row['year_amt'] = $amt_paid;
                }
			    //pause_amt,stop_amt
                $stopRow = Yii::app()->db->createCommand()
                    ->select("b.*")
                    ->from("swo_service_contract_no a")
                    ->leftJoin("swo_service b","a.service_id=b.id")
                    ->where("a.contract_no=:code and DATE_FORMAT(b.status_dt,'%Y')='{$year}' and b.status in ('S','T')",
                        array(":code"=>$row["contract_no"])
                    )->order("b.status_dt desc")->queryRow();
                if($stopRow){
                    $row['nature_name'] = GetNameToId::getNatureOneNameForId($row['nature_type']);
                    $stopRow['status_dt'] = General::toMyDate($stopRow['status_dt']);
                    $stopRow['ctrt_period'] = is_numeric($stopRow['ctrt_period'])?floatval($stopRow['ctrt_period']):0;
                    $stopRow['amt_paid'] = is_numeric($stopRow['amt_paid'])?floatval($stopRow['amt_paid']):0;
                    $amt_paid = $stopRow['amt_paid'];
                    if($stopRow["paid_type"]=="M"){
                        $stopRow['month_amt'] = $amt_paid;
                        $stopRow['year_amt'] = $amt_paid*$stopRow['ctrt_period'];
                    }else{
                        $month_amt = empty($stopRow['ctrt_period'])?0:$amt_paid/$stopRow['ctrt_period'];
                        $stopRow['month_amt'] = round($month_amt,2);
                        $stopRow['year_amt'] = $amt_paid;
                    }
                    $temp = array();
                    $temp['city_name'] = $row['city_name'];
                    $temp['customer'] = $row['company_name'];
                    //$temp['nature'] = $row['nature_type'];
                    $temp['nature'] = $row['nature_name'];
                    $temp['month_amt'] = " ".$row['month_amt'];
                    $temp['ctrt_period'] = " ".$row['ctrt_period'];
                    $temp['year_amt'] = $row['year_amt'];
                    $temp['new_date'] = " ".$row['status_dt'];
                    $diffList = $this->dateDiff($row['status_dt'],$stopRow['status_dt']);
                    $temp['contract_len'] = $diffList["diff_month"];
                    $temp['pause_date'] = " ";
                    $temp['stop_date'] = " ";
                    if($stopRow["status"]=="S"){//暂停
                        $temp['pause_date'] = " ".$stopRow['status_dt'];
                        $row['pause_amt'] = $stopRow['year_amt'];
                    }else{//终止
                        $temp['stop_date'] = " ".$stopRow['status_dt'];
                        $row['stop_amt'] = $stopRow['year_amt'];
                    }
                    if($diffList['diff_day']<-90){
                        $row['re_sign_amt'] = $row['year_amt'];
                        $row["status_desc"]=GetNameToId::getServiceStatusForKey($row["status"]);
                        $stopRow["status_desc"]=GetNameToId::getServiceStatusForKey($stopRow["status"]);
                        $row["cust_type_name"]=GetNameToId::getCustOneNameForId($row["cust_type"]);
                        $stopRow["cust_type_name"]=GetNameToId::getCustOneNameForId($stopRow["cust_type"]);
                        $stopRow['nature_name'] = GetNameToId::getNatureOneNameForId($stopRow['nature_type']);
                        $this->addDataListForDetail($row,$stopRow);
                    }
                    $this->data[] = $temp;
                }
                $this->addDataList($city,$row);
			}
		}
		return true;
	}

	public function getSelectString() {
        $rtn = '';
        if (isset($this->criteria)) {
            if ($this->fieldExist('year')) {
                $rtn = Yii::t('report','Year').': ';
                $rtn .= $this->criteria->year;
            }
            if ($this->fieldExist('city')&&!empty($this->criteria->city)) {
                $rtn.= empty($rtn)?"":" ；";
                $rtn.= Yii::t('report','City').': ';
                $rtn.= General::getCityNameForList($this->criteria->city);
            }
        }
        return $rtn;
	}

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityNameForList($this->criteria->city) : '';
		return parent::getReportName();
	}
}
?>