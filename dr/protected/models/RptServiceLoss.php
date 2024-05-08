<?php
class RptServiceLoss extends ReportData2 {
    public $dataList=array(
        "sumRate"=>array(),//占比
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
            );
        }
        $this->dataList["sumRate"][$city]["year_amt"]+=trim($row["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_pause"]+=trim($row["pause_amt"]);
        $this->dataList["sumRate"][$city]["sum_stop"]+=trim($row["stop_amt"]);
        $this->dataList["sumRate"][$city]["sum_all"]=$this->dataList["sumRate"][$city]["sum_pause"];
        $this->dataList["sumRate"][$city]["sum_all"]+=$this->dataList["sumRate"][$city]["sum_pause"];

        $this->dataList["sumRate"][$city]["sum_pause_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_pause"],$this->dataList["sumRate"][$city]["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_stop_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_stop"],$this->dataList["sumRate"][$city]["year_amt"]);
        $this->dataList["sumRate"][$city]["sum_all_rate"]=$this->numRate($this->dataList["sumRate"][$city]["sum_all"],$this->dataList["sumRate"][$city]["year_amt"]);
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
        $diff_month = round($diff_seconds / (60 * 60 * 24 * 30));
        return $diff_month;
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
                    'sum_stop'=>array('label'=>Yii::t('service','pause amount'),'width'=>18,'align'=>'C'),//终止金额
                    'sum_stop_rate'=>array('label'=>Yii::t('service','rate'),'width'=>18,'align'=>'C'),//百分比
                    'sum_all'=>array('label'=>Yii::t('service','pause + stop'),'width'=>20,'align'=>'C'),//暂停+终止金额
                    'sum_all_rate'=>array('label'=>Yii::t('service','rate'),'width'=>18,'align'=>'C'),//百分比
                ),
            )
        );
        return $sheetList;
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
                $row['pause_amt'] = 0;
                $row['stop_amt'] = 0;
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
                    ->select("b.status,b.status_dt,b.ctrt_period,b.paid_type,b.amt_paid")
                    ->from("swo_service_contract_no a")
                    ->leftJoin("swo_service b","a.service_id=b.id")
                    ->where("a.contract_no=:code and DATE_FORMAT(b.status_dt,'%Y')='{$year}' and b.status in ('S','T')",
                        array(":code"=>$row["contract_no"])
                    )->order("b.status_dt desc")->queryRow();
                if($stopRow){
                    $stopRow['ctrt_period'] = is_numeric($stopRow['ctrt_period'])?floatval($stopRow['ctrt_period']):0;
                    $stopRow['amt_paid'] = is_numeric($stopRow['amt_paid'])?floatval($stopRow['amt_paid']):0;
                    $stopRow['stop_year_amt'] = $stopRow['paid_type']=="M"?$stopRow['amt_paid']*$stopRow['ctrt_period']:$stopRow['amt_paid'];
                    $temp = array();
                    $temp['city_name'] = $row['city_name'];
                    $temp['customer'] = $row['company_name'];
                    $temp['nature'] = $row['nature_type'];
                    $temp['month_amt'] = " ".$row['month_amt'];
                    $temp['ctrt_period'] = " ".$row['ctrt_period'];
                    $temp['year_amt'] = $row['year_amt'];
                    $temp['new_date'] = " ".$row['status_dt'];
                    $temp['contract_len'] = $this->dateDiff($row['status_dt'],$stopRow['status_dt']);
                    $temp['pause_date'] = " ";
                    $temp['stop_date'] = " ";
                    if($stopRow["status"]=="S"){//暂停
                        $temp['pause_date'] = " ".$stopRow['status_dt'];
                        $row['pause_amt'] = $stopRow['stop_year_amt'];
                    }else{//终止
                        $temp['stop_date'] = " ".$stopRow['status_dt'];
                        $row['stop_amt'] = $stopRow['stop_year_amt'];
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