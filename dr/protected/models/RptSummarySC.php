<?php
class RptSummarySC extends ReportData2 {
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        if(!isset($this->criteria->start_dt)){
            $this->criteria->start_dt = date("Y/m/01");
        }
        if(!isset($this->criteria->end_dt)){
            $this->criteria->end_dt = date("Y/m/31");
        }
        $this->criteria->start_dt = General::toDate($this->criteria->start_dt);
        $this->criteria->end_dt = General::toDate($this->criteria->end_dt);
        $data = array();
        $city_allow="all";
        if(isset($this->criteria->city)&&!empty($this->criteria->city)){
            $city_allow = $this->criteria->city;
        }
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $serviceList = $this->getServiceData($citySetList,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $region = $cityRow["region_code"];
            if(!key_exists($region,$data)){
                $data[$region]=array(
                    "region"=>$region,
                    "region_name"=>$cityRow["region_name"],
                    "list"=>array()
                );
            }
            if(key_exists($city,$serviceList)){
                $arr=$serviceList[$city];
            }else{
                $arr=$this->defMoreCity($city,$cityRow["city_name"]);
            }
            $arr["add_type"] = $cityRow["add_type"];
            $data[$region]["list"][$city]=$arr;
        }
        //獲取U系統的數據
        $this->getUData($data,$citySetList);

        $this->data = $data;
		return true;
	}

	private function defMoreCity($city,$cityName){
	    return array(
            "city"=>$city,
            "city_name"=>$cityName,
            "u_actual_money"=>0,//实际月金额
            "num_new"=>0,//新增
            "u_invoice_sum"=>0,//新增(U系统同步数据)
            "num_stop"=>0,//终止服务
            "num_restore"=>0,//恢复服务
            "num_pause"=>0,//暂停服务
            "num_update"=>0,//更改服务
            "num_growth"=>0,//净增长
            "num_long"=>0,//长约（>=12月）
            "num_short"=>0,//短约
            "num_cate"=>0,//餐饮客户
            "num_not_cate"=>0,//非餐饮客户
            "u_num_cate"=>0,//餐饮客户(U系统同步数据)
            "u_num_not_cate"=>0,//非餐饮客户(U系统同步数据)
        );
    }

    private function getServiceData($citySetList,$city_allow){
	    $data = array();
        $suffix = Yii::app()->params['envSuffix'];
        $where = '';
        $where .= " and "."a.status_dt>='{$this->criteria->start_dt} 00:00:00'";
        $where .= " and "."a.status_dt<='{$this->criteria->end_dt} 23:59:59'";
        $where .= " and not(f.rpt_cat='INV' and f.single=1)";
        if(!empty($city_allow)&&$city_allow!="all"){
            $where .= " and "."a.city in ({$city_allow})";
        }
        $selectSql = "a.status,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid
            ";
        $serviceRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("a.city not in ('ZY') and a.status in ('N','A') {$where}")
            ->order("a.city")
            ->queryAll(); //客戶服務的暫停、恢復、終止需要特殊處理
        $SRTRows = self::getSRTRows($city_allow,$this->criteria->start_dt,$this->criteria->end_dt);
        //所有需要計算的客戶服務(ID客戶服務)
        $serviceRowsID = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("a.city not in ('ZY') {$where}")
            ->order("a.city")
            ->queryAll();
        $serviceRows = $serviceRows?$serviceRows:array();
        $serviceRowsID = $serviceRowsID?$serviceRowsID:array();
        $rows = array_merge($serviceRows,$SRTRows,$serviceRowsID);
        if(!empty($rows)){
            foreach ($rows as $row) {
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $city = empty($row["city"])?"none":$row["city"];
                $citySet = CitySetForm::getListForCityCode($city,$citySetList);

                if(!key_exists($city,$data)){
                    $data[$city]=$this->defMoreCity($city,$citySet["city_name"]);
                }
                if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                    if(!key_exists($citySet["region_code"],$data)){
                        $data[$citySet["region_code"]]=$this->defMoreCity($citySet["region_code"],$citySet["region_name"]);
                    }
                }
                if($row["paid_type"]=="M"){//月金额
                    $money = $row["amt_paid"]*$row["ctrt_period"];
                }else{
                    $money = $row["amt_paid"];
                }
                if($row["b4_paid_type"]=="M"){//月金额(变更前)
                    $b4_money = $row["b4_amt_paid"]*$row["ctrt_period"];
                }else{
                    $b4_money = $row["b4_amt_paid"];
                }
                switch ($row["status"]){
                    case "N"://新增
                        $data[$city]["num_new"]+=$money;
                        $data[$city]["num_long"]+=$row["ctrt_period"]>=12?$money:0;
                        $data[$city]["num_short"]+=$row["ctrt_period"]<12?$money:0;
                        $data[$city]["num_cate"]+=$row["nature_rpt_cat"]=="A01"?$money:0;
                        $data[$city]["num_not_cate"]+=$row["nature_rpt_cat"]!="A01"?$money:0;
                        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                            $data[$citySet["region_code"]]["num_new"]+=$money;
                            $data[$citySet["region_code"]]["num_long"]+=$row["ctrt_period"]>=12?$money:0;
                            $data[$citySet["region_code"]]["num_short"]+=$row["ctrt_period"]<12?$money:0;
                            $data[$citySet["region_code"]]["num_cate"]+=$row["nature_rpt_cat"]=="A01"?$money:0;
                            $data[$citySet["region_code"]]["num_not_cate"]+=$row["nature_rpt_cat"]!="A01"?$money:0;
                        }
                        break;
                    case "A"://更改
                        $data[$city]["num_update"]+=($money-$b4_money);
                        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                            $data[$citySet["region_code"]]["num_update"]+=($money-$b4_money);
                        }
                        break;
                    case "S"://暂停
                        $money*=-1;
                        $data[$city]["num_pause"]+=$money;
                        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                            $data[$citySet["region_code"]]["num_pause"]+=$money;
                        }
                        break;
                    case "R"://恢复
                        $data[$city]["num_restore"]+=$money;
                        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                            $data[$citySet["region_code"]]["num_restore"]+=$money;
                        }
                        break;
                    case "T"://终止
                        $money*=-1;
                        $data[$city]["num_stop"]+=$money;
                        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                            $data[$citySet["region_code"]]["num_stop"]+=$money;
                        }
                        break;
                }

            }
        }

        return $data;
    }

    //獲取暫停、恢復、終止的最後一條記錄(包含ID服务)
    public static function getSRTRowsAll($city_allow,$start_dt,$end_dt,$type=""){
        $rows = self::getSRTRows($city_allow,$start_dt,$end_dt,$type);//所有需要計算的客戶服務
        $serviceRowsID = self::getSRTRowsForID($city_allow,$start_dt,$end_dt,$type);//所有需要計算的客戶服務(ID客戶服務)

        return array_merge($rows,$serviceRowsID);
    }

    //獲取暫停、恢復、終止的最後一條記錄
    public static function getSRTRows($city_allow,$start_dt,$end_dt,$type=""){
        $where = "";
        $where .= " and ser.status_dt>='{$start_dt} 00:00:00'";
        $where .= " and ser.status_dt<='{$end_dt} 23:59:59'";
        $where .= " and ser.status in ('S','R','T')";
        $where .= " and not(ser_type.rpt_cat='INV' and ser_type.single=1)";
        if(!empty($city_allow)&&$city_allow!="all"){
            $where .= " and "."ser.city in ({$city_allow})";
        }
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $sqlText= Yii::app()->db->createCommand()
            ->select("ser.company_id,ser.cust_type,ser_no.contract_no,MAX(ser.id) AS id,MAX(ser.status_dt) AS status_dt")
            ->from("swo_service ser")
            ->leftJoin("swo_customer_type ser_type","ser.cust_type=ser_type.id")
            ->leftJoin("swo_service_contract_no ser_no","ser.id=ser_no.service_id")
            ->where("ser.city not in ('ZY') {$where}")
            ->group("ser.company_id,ser.cust_type,ser_no.contract_no")->getText();
        $where = str_ireplace("ser.", "a.", $where);
        $where = str_ireplace("ser_type.", "f.", $where);
        if(!empty($type)&&in_array($type,array("S","R","T"))){
            $where .= " and a.status='{$type}'";
        }
        $serviceRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->leftJoin("({$sqlText}) b","a.company_id = b.company_id AND a.cust_type = b.cust_type AND IFNULL(b.contract_no,'sb')=IFNULL(n.contract_no,'sb')")
            ->where("a.id>0 {$where} AND (a.status_dt>b.status_dt or (a.status_dt=b.status_dt and a.id=b.id))")
            ->order("a.city")
            ->queryAll(); //客戶服務的暫停、恢復、終止需要特殊處理
        return $serviceRows?$serviceRows:array();
    }

    //獲取暫停、恢復、終止的記錄(ID)
    public static function getSRTRowsForID($city_allow,$start_dt,$end_dt,$type=""){
        $where = "";
        $where .= " and a.status_dt>='{$start_dt} 00:00:00'";
        $where .= " and a.status_dt<='{$end_dt} 23:59:59'";
        switch ($type){
            case "S"://暫停
                $where .= " and a.status='S' ";
                break;
            case "R"://恢复
                $where .= " and a.status='R' ";
                break;
            case "T"://終止
                $where .= " and a.status='T' ";
                break;
            default:
                $where .= " and a.status in ('S','R','T')";
        }
        $where .= " and not(f.rpt_cat='INV' and f.single=1)";
        if(!empty($city_allow)&&$city_allow!="all"){
            $where .= " and "."a.city in ({$city_allow})";
        }
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $serviceRowsID = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swo_serviceid a")
            ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("a.city not in ('ZY') {$where}")
            ->order("a.city")
            ->queryAll();
        return $serviceRowsID?$serviceRowsID:array();
    }

    //Invoice表未同步，無法使用
    public function insertUData(&$data,$cityList){
        $startDate = $this->criteria->start_dt;
        $endDate = $this->criteria->end_dt;
        $suffix = Yii::app()->params['envSuffix'];
        $where = "";
        if(isset($this->criteria->city)&&!empty($this->criteria->city)){
            $where .= " and b.Text in ({$this->criteria->city})";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("x.InvoiceAmount,b.Text AS City,g.Text AS CusType")
            ->from("service{$suffix}.invoice x")
            ->leftJoin("service{$suffix}.officecity a", "x.City = a.City")
            ->leftJoin("service{$suffix}.enums b", "a.Office = b.EnumID AND b.EnumType = 8")
            ->leftJoin("service{$suffix}.customercompany c", "x.CustomerID = c.CustomerID")
            ->leftJoin("service{$suffix}.enums g", "(c.CustomerType - MOD (c.CustomerType, 100)) = g.EnumID AND g.EnumType = 4 ")
            ->where("x.status>0 and x.InvoiceDate BETWEEN '{$startDate}' AND '{$endDate}'AND SUBSTRING(x.InvoiceNumber, 3, 3) = 'INV' {$where}")
            ->order("x.InvoiceDate,x.CustomerID,x.InvoiceNumber")
            ->queryAll();
        if ($rows) {
            foreach ($rows as $row){
                $city = SummaryForm::resetCity($row["Text"]);
                $money = is_numeric($row["InvoiceAmount"])?floatval($row["InvoiceAmount"]):0;
                if(key_exists($city,$cityList)){
                    $region = $cityList[$city];
                    $data[$region]["list"][$city]["u_invoice_sum"]+=$money;
                    if($row["CusType"]==="餐饮类"){
                        $data[$region]["list"][$city]["u_num_cate"]+=$money;
                    }else{
                        $data[$region]["list"][$city]["u_num_not_cate"]+=$money;
                    }
                }
            }
        }
    }

    //獲取U系統的數據
	protected function getUData(&$data,$cityList){
        $json = Invoice::getInvData($this->criteria->start_dt,$this->criteria->end_dt);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = SummaryForm::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(key_exists($city,$cityList)){
                    $region = $cityList[$city]["region_code"];
                    $data[$region]["list"][$city]["u_invoice_sum"]+=$money;
                    if($row["customer_type"]==="餐饮类"){
                        $data[$region]["list"][$city]["u_num_cate"]+=$money;
                    }else{
                        $data[$region]["list"][$city]["u_num_not_cate"]+=$money;
                    }

                    if($cityList[$city]["add_type"]==1){//叠加(城市配置的叠加)
                        $city = $cityList[$city]["region_code"];
                        $region = "";
                        if(key_exists($city,$cityList)){
                            $region = $cityList[$city]["region_code"];
                        }
                        if(key_exists($region,$data)){
                            $data[$region]["list"][$city]["u_invoice_sum"]+=$money;
                            if($row["customer_type"]==="餐饮类"){
                                $data[$region]["list"][$city]["u_num_cate"]+=$money;
                            }else{
                                $data[$region]["list"][$city]["u_num_not_cate"]+=$money;
                            }
                        }
                    }
                }
            }
        }
    }

	public static function strUnsetNumber($str){
	    if(!empty($str)){
            $arr = str_split($str,1);
            foreach ($arr as $key=>$value){
                if(is_numeric($value)){
                    unset($arr[$key]);
                }
            }
            return implode("",$arr);
        }else{
	        return "none";
        }
    }
}
?>