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
        $startDate = $this->criteria->start_dt;
        $endDate = $this->criteria->end_dt;
        $lastStartDate = date("Y/m/d",strtotime("{$startDate} - 1 months"));
        $lastEndDate = date("Y/m/d",strtotime("{$endDate} - 1 months"));
        $data = array();
        $city_allow="all";
        if(isset($this->criteria->city)&&!empty($this->criteria->city)){
            $city_allow = $this->criteria->city;
        }
        $citySetList = CitySetForm::getCitySetList($city_allow);

        //获取U系统的服务单数据(報表不需要生意額數據)
        //$uServiceMoney = CountSearch::getUServiceMoney($startDate,$endDate,$city_allow);
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($startDate,$endDate,$city_allow);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($startDate,$endDate,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($startDate,$endDate,$city_allow);
        //新增服務的詳情
        $serviceDetailForAdd = CountSearch::getServiceDetailForAdd($startDate,$endDate,$city_allow);
        //服务新增（一次性)(上月)
        $lastServiceAddForNY = CountSearch::getServiceAddForY($lastStartDate,$lastEndDate,$city_allow);
        //获取U系统的產品数据(上月)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            //$defMoreList["u_actual_money"]+=key_exists($city,$uServiceMoney)?$uServiceMoney[$city]:0;
            $defMoreList["u_invoice_num"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            $defMoreList["u_num_cate"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["u_num_cate"]:0;
            $defMoreList["u_num_not_cate"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["u_num_not_cate"]:0;
            $defMoreList["num_new"]+=key_exists($city,$serviceAddForNY)?$serviceAddForNY[$city]["num_new"]:0;
            $defMoreList["num_new_n"]+=key_exists($city,$serviceAddForNY)?$serviceAddForNY[$city]["num_new_n"]:0;
            $defMoreList["u_invoice_sum"]+=$defMoreList["num_new_n"];
            $defMoreList["u_invoice_sum"]+=$defMoreList["u_invoice_num"];
            $defMoreList["num_pause"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_pause"]:0;
            $defMoreList["num_stop"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop"]:0;
            $defMoreList["num_restore"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;
            $defMoreList["num_update"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;
            if(key_exists($city,$serviceDetailForAdd)){
                $defMoreList["num_long"]+=$serviceDetailForAdd[$city]["num_long"];
                $defMoreList["num_short"]+=$serviceDetailForAdd[$city]["num_short"];
                $defMoreList["one_service"]+=$serviceDetailForAdd[$city]["one_service"];
                $defMoreList["num_cate"]+=$serviceDetailForAdd[$city]["num_cate"];
                $defMoreList["num_not_cate"]+=$serviceDetailForAdd[$city]["num_not_cate"];
            }
            $defMoreList["last_u_invoice_sum"]+=key_exists($city,$lastUInvMoney)?$lastUInvMoney[$city]["sum_money"]:0;
            $defMoreList["last_one_service"]+=key_exists($city,$lastServiceAddForNY)?$lastServiceAddForNY[$city]:0;
            $defMoreList["last_month_sum"]+=-1*($defMoreList["last_one_service"]+$defMoreList["last_u_invoice_sum"]);

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        return true;
    }

    public static function resetData(&$data,$cityRow,$citySet,$defMoreList){
        $notAddList=array("add_type");
        foreach (ComparisonForm::$con_list as $itemStr){
            $notAddList[]=$itemStr;
            $notAddList[]="start_".$itemStr;
        }
        $city = $cityRow["code"];
        $defMoreList["city"]=$city;
        $defMoreList["city_name"]= $cityRow["city_name"];
        $region = $cityRow["region_code"];
        if(!key_exists($region,$data)){
            $data[$region]=array(
                "region"=>$region,
                "region_name"=>$cityRow["region_name"],
                "list"=>array()
            );
        }
        if(key_exists($city,$data[$region]["list"])){
            foreach ($defMoreList as $key=>$value){
                if(in_array($key,$notAddList)){
                    $data[$region]["list"][$city][$key]=$value;
                }elseif (is_numeric($value)){
                    $data[$region]["list"][$city][$key]+=$value;
                }else{
                    $data[$region]["list"][$city][$key]=$value;
                }
            }
        }else{
            $data[$region]["list"][$city]=$defMoreList;
        }

        if($cityRow["add_type"]==1&&key_exists($region,$citySet)){//叠加(城市配置的叠加)
            $regionTwo = $citySet[$region];
            self::resetData($data,$regionTwo,$citySet,$defMoreList);
        }
    }

    private function defMoreCity($city,$cityName){
        return array(
            "city"=>$city,
            "city_name"=>$cityName,
            "u_actual_money"=>0,//实际月金额
            "num_new"=>0,//新增（非一次性）
            "num_new_n"=>0,//新增（一次性）
            "u_invoice_sum"=>0,//新增(U系统同步数据 + LBS一次性服務)
            "u_invoice_num"=>0,//新增(U系统同步数据)
            "last_month_sum"=>0,//上月一次性服务+新增产品
            "num_stop"=>0,//终止服务
            "num_restore"=>0,//恢复服务
            "num_pause"=>0,//暂停服务
            "num_update"=>0,//更改服务
            "num_growth"=>0,//净增长
            "num_long"=>0,//长约（>=12月）
            "num_short"=>0,//短约
            "one_service"=>0,//一次性服務
            "num_cate"=>0,//餐饮客户
            "num_not_cate"=>0,//非餐饮客户
            "u_num_cate"=>0,//餐饮客户(U系统同步数据)
            "u_num_not_cate"=>0,//非餐饮客户(U系统同步数据)
            "last_one_service"=>0,//一次性服務（上月）
            "last_u_invoice_sum"=>0,//U系统同步数据（上個月）
        );
    }

    //獲取暫停、終止的最後一條記錄(包含ID服务)
    public static function getSRTRowsAll($city_allow,$start_dt,$end_dt,$type=""){
        $rows = self::getSRTRows($city_allow,$start_dt,$end_dt,$type);//所有需要計算的客戶服務
        $serviceRowsID = self::getSRTRowsForID($city_allow,$start_dt,$end_dt,$type);//所有需要計算的客戶服務(ID客戶服務)

        return array_merge($rows,$serviceRowsID);
    }

    //獲取暫停、終止的最後一條記錄(一条服务在一个月内只能存在一条暂停和终止)
    public static function getSRTRows($city_allow,$start_dt,$end_dt,$type=""){
        $where = "";
        $where .= " and ser.status_dt>='{$start_dt} 00:00:00'";
        $where .= " and ser.status_dt<='{$end_dt} 23:59:59'";
        $where .= " and ser.status in ('S','T')";
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
            ->group("ser.company_id,DATE_FORMAT(ser.status_dt,'%Y/%m'),ser.cust_type,ser_no.contract_no")->getText();
        $where = str_ireplace("ser.", "a.", $where);
        $where = str_ireplace("ser_type.", "f.", $where);
        if(!empty($type)&&in_array($type,array("S","T"))){
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
            ->order("a.city,a.status_dt desc")
            ->queryAll(); //客戶服務的暫停、終止需要特殊處理
        return $serviceRows?$serviceRows:array();
    }

    //獲取暫停、終止的記錄(ID)
    public static function getSRTRowsForID($city_allow,$start_dt,$end_dt,$type=""){
        $where = "";
        $where .= " and a.status_dt>='{$start_dt} 00:00:00'";
        $where .= " and a.status_dt<='{$end_dt} 23:59:59'";
        switch ($type){
            case "S"://暫停
                $where .= " and a.status='S' ";
                break;
            case "T"://終止
                $where .= " and a.status='T' ";
                break;
            default:
                $where .= " and a.status in ('S','T')";
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
            ->order("a.city,a.status_dt desc")
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