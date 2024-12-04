<?php

/**
 * 合同同比分析、查詢(办事处)
 */
class CountOfficeSearch extends CountSearch{

    //获取服务单月数据 - 办事处
    public static function getUServiceOfficeMoneyOne($startDay,$endDay,$city_allow="",$type=0){
        $list = SystemU::getUServiceOfficeMoney($startDay,$endDay,$city_allow,$type);
        $arr = array();
        if(isset($list["data"])&&is_array($list["data"])){
            foreach ($list["data"] as $city=>$rows){
                if(is_array($rows)){
                    foreach ($rows as $office_id=>$number){
                        $arr[$office_id]=$number;
                    }
                }
            }
        }
        return $arr;
    }

    //获取U系统的產品数据 - 办事处
    public static function getUInvOfficeMoneyOne($startDay,$endDay,$city_allow="",$type=0){
        $list = SystemU::getInvDataOfficeCityAmount($startDay,$endDay,$city_allow,$type);
        $arr = array();
        if(isset($list["data"])&&is_array($list["data"])){
            foreach ($list["data"] as $city=>$rows){
                if(is_array($rows)){
                    foreach ($rows as $row){
                        $arr[$row["office_id"]]=$row;
                    }
                }
            }
        }
        return $arr;
    }

    //服务新增（非一次性 和 一次性) - 办事处
    public static function getServiceOfficeAddForNY($startDay,$endDay,$city_allow="",$sqlEpr=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL.$sqlEpr;
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum({$sum_money}) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
            sum(if(a.paid_type=1 and a.ctrt_period<12,({$sum_money}),0)) as num_new_n,
            sum(if(a.paid_type=1 and a.ctrt_period<12,0,({$sum_money}))) as num_new
            ")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("office_city,a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
                sum(a.amt_paid*a.ctrt_period) as num_new,
                CONCAT(0) as num_new_n")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                //->leftJoin("swo_customer_type_id g","a.cust_type_name=g.id")
                ->where($whereSql)->group("office_city,a.city")->queryAll();//ID服務暫時全部為非一次性服務
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("a.");
            $KARows = Yii::app()->db->createCommand()
                ->select("sum({$sum_money}) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
            sum(if(a.paid_type=1 and a.ctrt_period<12,({$sum_money}),0)) as num_new_n,
            sum(if(a.paid_type=1 and a.ctrt_period<12,0,({$sum_money}))) as num_new
            ")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("office_city,a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!isset($list[$row["city"]][$row["office_city"]])){
                $list[$row["city"]][$row["office_city"]]=array(
                    "num_new"=>0,
                    "num_new_n"=>0,
                );
            }
            $list[$row["city"]][$row["office_city"]]["num_new"]+=$row["num_new"];
            $list[$row["city"]][$row["office_city"]]["num_new_n"]+=$row["num_new_n"];
        }
        return $list;
    }

    //獲取暫停、終止的最後一條記錄(一条服务在一个月内只能存在一条暂停和终止)，特例：暫停→恢復→終止（三個都需要計算）
    public static function getServiceOfficeForST($start_dt,$end_dt,$city_allow,$type="all"){
        $list = array();
        $sum_money = "case b.paid_type when 'M' then b.amt_paid * b.ctrt_period else b.amt_paid end";
        switch ($type){
            case "S"://暫停
                $whereSql="b.status='S'";
                break;
            case "T"://終止
                $whereSql="b.status='T'";
                break;
            default://暫停+終止
                $whereSql="b.status in ('S','T')";
        }
        $whereSql.= " and b.status_dt BETWEEN '{$start_dt}' and '{$end_dt}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and b.city in ({$city_allow})";
        }
        $whereSql.=self::$whereSQL;
        $rows= Yii::app()->db->createCommand()
            ->select("a.id,a.status,a.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            (case b.paid_type
                    when 'M' then b.amt_paid
                    else if(b.ctrt_period='' or b.ctrt_period is null,0,b.amt_paid/b.ctrt_period)
                end
            ) as num_month,
            IF(b.office_id='' or b.office_id is NULL,b.city,b.office_id) as office_city,
            DATE_FORMAT(a.status_dt,'%Y/%m') as month_date")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","b.id=a.service_id")
            ->leftJoin("swo_customer_type f","b.cust_type=f.id")
            ->where($whereSql)
            ->queryAll();
        if($rows){//
            foreach ($rows as $row){
                $city = $row["city"];
                if(!isset($list[$row["city"]][$row["office_city"]])){
                    $list[$row["city"]][$row["office_city"]]=array(
                        "num_pause"=>0,//暫停金額（年金額）
                        "num_stop"=>0,//停單金額（年金額）
                        "num_stop_none"=>0,//停單金額（年金額）(本条终止的前一条、后一条没有暂停、终止)
                        "num_month"=>0,//停單金額（月金額）
                    );
                }
                $nextRow= Yii::app()->db->createCommand()
                    ->select("status")->from("swo_service_contract_no")
                    ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["id"]}' and 
                        status_dt>'{$row['status_dt']}' and 
                        DATE_FORMAT(status_dt,'%Y/%m')='{$row['month_date']}'")
                    ->order("status_dt asc")
                    ->queryRow();//查詢本月的後面一條數據
                if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                    continue;//如果下一條數據是暫停或者終止，則不統計本條數據
                }else{
                    $money = round($row["sum_money"],2);
                    if($row["status"]=="T"){
                        $prevRow= Yii::app()->db->createCommand()
                            ->select("status")->from("swo_service_contract_no")
                            ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["id"]}' and status_dt<='{$row['status_dt']}'")
                            ->order("status_dt desc")
                            ->queryRow();//查詢本条的前面一條數據
                        if($prevRow===false||!in_array($prevRow["status"],array("S","T"))){
                            $list[$row["city"]][$row["office_city"]]["num_stop_none"]+=$money;
                        }
                        $list[$row["city"]][$row["office_city"]]["num_stop"]+=$money;
                        $list[$row["city"]][$row["office_city"]]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                    }else{
                        $list[$row["city"]][$row["office_city"]]["num_pause"]+=$money;
                    }
                }
            }
        }

        if(self::$IDBool){ //ID服務的暫停、終止
            $rows = Yii::app()->db->createCommand()
                ->select("sum(b.amt_paid*b.ctrt_period) as sum_amount,
            IF(b.office_id='' or b.office_id is NULL,b.city,b.office_id) as office_city,
                sum(b.amt_paid) as num_month,b.city,b.status")
                ->from("swo_serviceid b")
                ->leftJoin("swo_customer_type_id f","b.cust_type=f.id")
                ->where($whereSql)->group("office_city,b.city,b.status")->queryAll();//
            if($rows){
                foreach ($rows as $row){
                    if(!isset($list[$row["city"]][$row["office_city"]])){
                        $list[$row["city"]][$row["office_city"]]=array(
                            "num_pause"=>0,//暫停金額（年金額）
                            "num_stop"=>0,//停單金額（年金額）
                            "num_stop_none"=>0,//停單金額（年金額）(本条终止的前一条、后一条没有暂停、终止)
                            "num_month"=>0,//停單金額（月金額）
                        );
                    }
                    $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                    if($row["status"]=="S"){ //暫停
                        $list[$row["city"]][$row["office_city"]]["num_pause"]+= $money;
                    }else{
                        $list[$row["city"]][$row["office_city"]]["num_stop_none"]+=$money;
                        $list[$row["city"]][$row["office_city"]]["num_stop"]+= $money;
                        $list[$row["city"]][$row["office_city"]]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                    }
                }
            }
        }

        if(self::$KABool){ //KA服務的暫停、終止
            $kaSqlPrx = self::getServiceKASQL();
            $KARows= Yii::app()->db->createCommand()
                ->select("a.id,a.status,a.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            (case b.paid_type
                    when 'M' then b.amt_paid
                    else if(b.ctrt_period='' or b.ctrt_period is null,0,b.amt_paid/b.ctrt_period)
                end
            ) as num_month,
            IF(b.office_id='' or b.office_id is NULL,b.city,b.office_id) as office_city,
            DATE_FORMAT(a.status_dt,'%Y/%m') as month_date")
                ->from("swo_service_ka_no a")
                ->leftJoin("swo_service_ka b","b.id=a.service_id")
                ->leftJoin("swo_customer_type f","b.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->queryAll();
            if($KARows){
                foreach ($KARows as $row){
                    $city = $row["city"];
                    if(!isset($list[$row["city"]][$row["office_city"]])){
                        $list[$row["city"]][$row["office_city"]]=array(
                            "num_pause"=>0,//暫停金額（年金額）
                            "num_stop"=>0,//停單金額（年金額）
                            "num_stop_none"=>0,//停單金額（年金額）(本条终止的前一条、后一条没有暂停、终止)
                            "num_month"=>0,//停單金額（月金額）
                        );
                    }
                    $nextRow= Yii::app()->db->createCommand()
                        ->select("status")->from("swo_service_ka_no")
                        ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["id"]}' and 
                        status_dt>'{$row['status_dt']}' and 
                        DATE_FORMAT(status_dt,'%Y/%m')='{$row['month_date']}'")
                        ->order("status_dt asc")
                        ->queryRow();//查詢本月的後面一條數據
                    if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                        continue;//如果下一條數據是暫停或者終止，則不統計本條數據
                    }else{
                        $money = round($row["sum_money"],2);
                        if($row["status"]=="T"){
                            $prevRow= Yii::app()->db->createCommand()
                                ->select("status")->from("swo_service_ka_no")
                                ->where("contract_no='{$row["contract_no"]}' and 
                                id!='{$row["id"]}' and status_dt<='{$row['status_dt']}'")
                                ->order("status_dt desc")
                                ->queryRow();//查詢本条的前面一條數據
                            if($prevRow===false||!in_array($prevRow["status"],array("S","T"))){
                                $list[$row["city"]][$row["office_city"]]["num_stop_none"]+=$money;
                            }
                            $list[$row["city"]][$row["office_city"]]["num_stop"]+=$money;
                            $list[$row["city"]][$row["office_city"]]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                        }else{
                            $list[$row["city"]][$row["office_city"]]["num_pause"]+=$money;
                        }
                    }
                }
            }
        }
        return $list;
    }

    //客户服务查询(根據服務類型)- 办事处
    public static function getServiceOfficeForType($startDate,$endDate,$city_allow="",$type="N"){
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
					) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("office_city,a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
                a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("office_city,a.city")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }

        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("a.");
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("office_city,a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!isset($list[$row["city"]][$row["office_city"]])){
                $list[$row["city"]][$row["office_city"]]=0;
            }
            $list[$row["city"]][$row["office_city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务查询(更改)- 办事处
    public static function getServiceOfficeForA($startDate,$endDate,$city_allow="",$sqlEpr=""){
        $whereSql = "a.status='A' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL.$sqlEpr;
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
					) as b4_sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("office_city,a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
                sum(a.b4_amt_money) as b4_sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("office_city,a.city")->queryAll();
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("a.");
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,sum(case a.b4_paid_type
							when 'M' then a.b4_amt_paid * a.ctrt_period
							else a.b4_amt_paid
						end
					) as b4_sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("office_city,a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!isset($list[$row["city"]][$row["office_city"]])){
                $list[$row["city"]][$row["office_city"]]=0;
            }
            $list[$row["city"]][$row["office_city"]]+=$row["sum_amount"]-$row["b4_sum_amount"];
        }
        return $list;
    }

    //服务新增詳情(長約、短約、一次性服務、餐飲、非餐飲)- 办事处
    public static function getServiceOfficeDetailForAdd($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list = array();
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("sum($sum_money) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
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
            ->group("office_city,a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
            sum(if(a.ctrt_period>=12,a.amt_paid*a.ctrt_period,0)) as num_long,
            sum(if(a.ctrt_period<12,a.amt_paid*a.ctrt_period,0)) as num_short,
            CONCAT(0) as one_service,
            sum(if(g.rpt_cat='A01',a.amt_paid*a.ctrt_period,0)) as num_cate,
            sum(if(g.rpt_cat!='A01',a.amt_paid*a.ctrt_period,0)) as num_not_cate
            ")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->group("office_city,a.city")->queryAll();//ID服務暫時全部為非一次性服務
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("a.");
            $KARows = Yii::app()->db->createCommand()
                ->select("sum($sum_money) as sum_amount,a.city,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
            sum(if(a.ctrt_period>=12,({$sum_money}),0)) as num_long,
            sum(if(a.ctrt_period<12 and a.paid_type!=1,({$sum_money}),0)) as num_short,
            sum(if(a.ctrt_period<12 and a.paid_type=1,({$sum_money}),0)) as one_service,
            sum(if(g.rpt_cat='A01',({$sum_money}),0)) as num_cate,
            sum(if(g.rpt_cat!='A01',({$sum_money}),0)) as num_not_cate
            ")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("office_city,a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!isset($list[$row["city"]][$row["office_city"]])){
                $list[$row["city"]][$row["office_city"]]=array(
                    "sum_amount"=>0,//
                    "num_long"=>0,//长约（>=12月）
                    "num_short"=>0,//短约
                    "one_service"=>0,//一次性服務
                    "num_cate"=>0,//餐饮客户
                    "num_not_cate"=>0,//非餐饮客户
                );
            }
            $list[$row["city"]][$row["office_city"]]["sum_amount"]+=$row["sum_amount"];
            $list[$row["city"]][$row["office_city"]]["num_long"]+=$row["num_long"];
            $list[$row["city"]][$row["office_city"]]["num_short"]+=$row["num_short"];
            $list[$row["city"]][$row["office_city"]]["one_service"]+=$row["one_service"];
            $list[$row["city"]][$row["office_city"]]["num_cate"]+=$row["num_cate"];
            $list[$row["city"]][$row["office_city"]]["num_not_cate"]+=$row["num_not_cate"];
        }
        return $list;
    }

    //服务新增（一次性)- 办事处
    public static function getServiceOfficeAddForY($startDay,$endDay,$city_allow=""){
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
					) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql." and a.paid_type=1 and a.ctrt_period<12")->group("office_city,a.city")->queryAll();
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
        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("a.");
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,
            IF(a.office_id='' or a.office_id is NULL,a.city,a.office_id) as office_city,
					a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx} and a.paid_type=1 and a.ctrt_period<12")->group("office_city,a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!isset($list[$row["city"]][$row["office_city"]])){
                $list[$row["city"]][$row["office_city"]]=0;
            }
            $list[$row["city"]][$row["office_city"]]+=$row["sum_amount"];
        }
        return $list;
    }
}