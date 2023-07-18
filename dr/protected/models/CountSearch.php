<?php

/**
 * 合同同比分析、查詢
 */
class CountSearch{

    private static $whereSQL=" and not(f.rpt_cat='INV' and f.single=1)";
    private static $IDBool=true;//是否需要ID服務的查詢

    //獲取暫停、終止的最後一條記錄(一条服务在一个月内只能存在一条暂停和终止)
    public static function getServiceForST($start_dt,$end_dt,$city_allow=""){
        $where = "";
        $where .= " and ser.status_dt BETWEEN '{$start_dt}' and '{$end_dt}'";
        $where .= " and ser.status in ('S','T')";
        $where .= str_ireplace("f.", "ser_type.", self::$whereSQL);
        if(!empty($city_allow)&&$city_allow!="all"){
            $where .= " and "."ser.city in ({$city_allow})";
        }
        $list=array();
        $sqlText= Yii::app()->db->createCommand()
            ->select("ser.company_id,ser.cust_type,ser_no.contract_no,MAX(ser.id) AS id,MAX(ser.status_dt) AS status_dt")
            ->from("swo_service ser")
            ->leftJoin("swo_customer_type ser_type","ser.cust_type=ser_type.id")
            ->leftJoin("swo_service_contract_no ser_no","ser.id=ser_no.service_id")
            ->where("ser.city not in ('ZY') {$where}")
            ->group("ser.company_id,DATE_FORMAT(ser.status_dt,'%Y/%m'),ser.cust_type,ser_no.contract_no")->getText();
        $where = str_ireplace("ser.", "a.", $where);
        $where = str_ireplace("ser_type.", "f.", $where);
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,
					sum(case a.paid_type
							when 'M' then a.amt_paid
							else if(a.ctrt_period='' or a.ctrt_period is null,0,a.amt_paid/a.ctrt_period)
						end
					) as num_month,a.city,a.status")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("({$sqlText}) b","a.company_id = b.company_id AND a.cust_type = b.cust_type AND IFNULL(b.contract_no,'sb')=IFNULL(n.contract_no,'sb')")
            ->where("a.id>0 {$where} AND (a.status_dt>b.status_dt or (a.status_dt=b.status_dt and a.id=b.id))")
            ->group("a.city,a.status")
            ->queryAll(); //客戶服務的暫停、終止需要特殊處理

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,sum(a.amt_paid) as num_month,a.city,a.status")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where("a.id>0 {$where}")->group("a.city,a.status")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["city"],$list)){
                    $list[$row["city"]]=array(
                        "num_pause"=>0,//暫停金額（年金額）
                        "num_stop"=>0,//停單金額（年金額）
                        "num_month"=>0,//停單金額（月金額）
                    );
                }
                if($row["status"]=="S"){ //暫停
                    $list[$row["city"]]["num_pause"]+= $row["sum_amount"];
                }else{
                    $list[$row["city"]]["num_stop"]+= $row["sum_amount"];
                    $list[$row["city"]]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                }
            }
        }
        return $list;
    }

    //客户服务查询(根據服務類型)
    public static function getServiceForType($startDate,$endDate,$city_allow="",$type="N"){
        $whereSql = "a.status='{$type}' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list=array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务查询(更改)
    public static function getServiceForA($startDate,$endDate,$city_allow=""){
        $whereSql = "a.status='A' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list=array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,sum(case a.b4_paid_type
							when 'M' then a.b4_amt_paid * a.ctrt_period
							else a.b4_amt_paid
						end
					) as b4_sum_amount,a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,sum(a.b4_amt_money) as b4_sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city")->queryAll();
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"]-$row["b4_sum_amount"];
        }
        return $list;
    }

    //服务新增詳情(長約、短約、一次性服務、餐飲、非餐飲)
    public static function getServiceDetailForAdd($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list = array();
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("sum($sum_money) as sum_amount,a.city,
            sum(if(a.ctrt_period>=12,({$sum_money}),0)) as num_long,
            sum(if(a.ctrt_period<12 and a.paid_type!=1,({$sum_money}),0)) as num_short,
            sum(if(a.ctrt_period<12 and a.paid_type=1,({$sum_money}),0)) as one_service,
            sum(if(g.rpt_cat='A01',({$sum_money}),0)) as num_cate,
            sum(if(g.rpt_cat!='A01',({$sum_money}),0)) as num_not_cate
            ")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)
            ->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city,
            sum(if(a.ctrt_period>=12,a.amt_paid*a.ctrt_period,0)) as num_long,
            sum(if(a.ctrt_period<12,a.amt_paid*a.ctrt_period,0)) as num_short,
            CONCAT(0) as one_service,
            sum(if(g.rpt_cat='A01',a.amt_paid*a.ctrt_period,0)) as num_cate,
            sum(if(g.rpt_cat!='A01',a.amt_paid*a.ctrt_period,0)) as num_not_cate
            ")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->group("a.city")->queryAll();//ID服務暫時全部為非一次性服務
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=array(
                    "sum_amount"=>0,//
                    "num_long"=>0,//长约（>=12月）
                    "num_short"=>0,//短约
                    "one_service"=>0,//一次性服務
                    "num_cate"=>0,//餐饮客户
                    "num_not_cate"=>0,//非餐饮客户
                );
            }
            $list[$row["city"]]["sum_amount"]+=$row["sum_amount"];
            $list[$row["city"]]["num_long"]+=$row["num_long"];
            $list[$row["city"]]["num_short"]+=$row["num_short"];
            $list[$row["city"]]["one_service"]+=$row["one_service"];
            $list[$row["city"]]["num_cate"]+=$row["num_cate"];
            $list[$row["city"]]["num_not_cate"]+=$row["num_not_cate"];
        }
        return $list;
    }

    //服务新增（非一次性 和 一次性)
    public static function getServiceAddForNY($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum({$sum_money}) as sum_amount,a.city,
            sum(if(a.paid_type=1 and a.ctrt_period<12,({$sum_money}),0)) as num_new_n,
            sum(if(a.paid_type=1 and a.ctrt_period<12,0,({$sum_money}))) as num_new
            ")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city,
                sum(a.amt_paid*a.ctrt_period) as num_new,
                CONCAT(0) as num_new_n")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                //->leftJoin("swo_customer_type_id g","a.cust_type_name=g.id")
                ->where($whereSql)->group("a.city")->queryAll();//ID服務暫時全部為非一次性服務
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=array(
                    "num_new"=>0,
                    "num_new_n"=>0,
                );
            }
            $list[$row["city"]]["num_new"]+=$row["num_new"];
            $list[$row["city"]]["num_new_n"]+=$row["num_new_n"];
        }
        return $list;
    }

    //服务新增（非一次性)
    public static function getServiceAddForN($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql." and not (a.paid_type=1 and a.ctrt_period<12)")->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                //->leftJoin("swo_customer_type_id g","a.cust_type_name=g.id")
                ->where($whereSql)->group("a.city")->queryAll();//ID服務暫時全部為非一次性服務
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //服务新增（一次性)
    public static function getServiceAddForY($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql." and a.paid_type=1 and a.ctrt_period<12")->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            /* ID服務暫時全部為非一次性服務
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_customer_type_id g","a.cust_type_name=g.id")
                ->where($whereSql." and g.single=1")->group("a.city")->queryAll();
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
            */
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //获取生意額的数据(U系統服務生意額 + U系統產品金額)
    public static function getUActualMoney($startDay,$endDay,$city_allow=""){
        $uServiceList = self::getUServiceMoney($startDay,$endDay,$city_allow);
        $uData = self::getUInvMoney($startDay,$endDay);
        foreach ($uData as $city=>$row){
            if(!key_exists($city,$uServiceList)){
                $uServiceList[$city]=0;
            }
            $uServiceList[$city]+=$row["sum_money"];
        }
        return $uServiceList;
    }

    //获取U系统的服务单数据
    public static function getUServiceMoney($startDay,$endDay,$city_allow=""){
        $list = array();
        $citySql = "";
        if(!empty($city_allow)&&$city_allow!="all"){
            $citySql = " and b.Text in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.Text,sum(
                    if(a.TermCount=0,0,a.Fee/a.TermCount)
					) as sum_amount")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->group("b.Text")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = self::resetCity($row["Text"]);
                $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$money;
            }
        }
        return $list;
    }

    //获取U系统的產品数据
    public static function getUInvMoney($startDay,$endDay,$city_allow=""){
        $city = "";
        if(!empty($city_allow)&&$city_allow!="all"){
            $city = $city_allow;
        }
        $json = Invoice::getInvData($startDay,$endDay,$city);
        $list = array();
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = self::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(!key_exists($city,$list)){
                    $list[$city]=array(
                        "sum_money"=>0,
                        "u_num_cate"=>0,
                        "u_num_not_cate"=>0
                    );
                }
                $list[$city]["sum_money"]+=$money;
                if($row["customer_type"]==="餐饮类"){
                    $list[$city]["u_num_cate"]+=$money;
                }else{
                    $list[$city]["u_num_not_cate"]+=$money;
                }
            }
        }
        return $list;
    }

    //轉換U系統的城市（國際版專用）
    public static function resetCity($city){
        switch($city){
            case "KL":
                return "MY";
            case "SL":
                return "MY";
        }
        return $city;
    }
}