<?php
class RptSummarySC extends ReportData2 {
    public $u_load_data=array();//查询时长数组
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
        $lastStartDate = CountSearch::computeLastMonth($startDate);
        $lastEndDate = CountSearch::computeLastMonth($endDate);
        $data = array();
        $city_allow="all";
        if(isset($this->criteria->city)&&!empty($this->criteria->city)){
            //$city_allow = $this->criteria->city;
            $city = $this->criteria->city;
            if(!General::isJSON($city)){
                $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
            }else{
                $city_allow = json_decode($city,true);
                $city_allow = "'".implode("','",$city_allow)."'";
            }
        }
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $this->u_load_data['u_load_start'] = time();
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //获取U系统的產品数据(上月)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        //获取U系统的服务单数据(報表不需要生意額數據)
        //$uServiceMoney = CountSearch::getUServiceMoney($startDate,$endDate,$city_allow);
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
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=self::defMoreCity($city,$cityRow["city_name"]);
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
            $defMoreList["num_stop_none"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop_none"]:0;
            $defMoreList["stop_2024_11"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_stop_none"]:0;
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

    public static function resetData(&$data,$cityRow,$citySet,$defMoreList,$bool=true){
        $notAddList=array("add_type");
        foreach (ComparisonForm::$con_list as $itemStr){
            $notAddList[]=$itemStr;
            $notAddList[]="start_".$itemStr;
        }
        $city = $cityRow["code"];
        $defMoreList["city"]=$city;
        $defMoreList["city_name"]= $cityRow["city_name"];
        $defMoreList["add_type"]= $cityRow["add_type"];
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
                    if($bool){
                        $data[$region]["list"][$city][$key]=$value;
                    }
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
            self::resetData($data,$regionTwo,$citySet,$defMoreList,false);
        }
    }

    public static function defMoreCity($city,$cityName){
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
            "num_stop_none"=>0,//正常终止服务金额
            "stop_2024_11"=>0,//正常终止服务金额
            "net_2024_11"=>0,//净增长
            "num_stop_show"=>0,//暂停后终止服务金额
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