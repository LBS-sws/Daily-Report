<?php

/**
 * 合同同比分析、查詢
 */
class CountSearch{

    private static $whereSQL=" and not(f.rpt_cat='INV' and f.single=1)";
    private static $IDBool=true;//是否需要ID服務的查詢
    private static $KABool=true;//是否需要KA服務的查詢

    private static $system=0;//0:大陸 1:台灣 2:國際

    public static function getSystem(){
        return self::$system;
    }

    //獲取暫停、終止的最後一條記錄(一条服务在一个月内只能存在一条暂停和终止)，特例：暫停→恢復→終止（三個都需要計算）
    public static function getServiceForST($start_dt,$end_dt,$city_allow,$type="all"){
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
            DATE_FORMAT(a.status_dt,'%Y/%m') as month_date")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","b.id=a.service_id")
            ->leftJoin("swo_customer_type f","b.cust_type=f.id")
            ->where($whereSql)
            ->queryAll();
        if($rows){//
            foreach ($rows as $row){
                $city = $row["city"];
                if(!key_exists($city,$list)){
                    $list[$city]=array(
                        "num_pause"=>0,//暫停金額（年金額）
                        "num_stop"=>0,//停單金額（年金額）
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
                        $list[$city]["num_stop"]+=$money;
                        $list[$city]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                    }else{
                        $list[$city]["num_pause"]+=$money;
                    }
                }
            }
        }

        if(self::$IDBool){ //ID服務的暫停、終止
            $rows = Yii::app()->db->createCommand()
                ->select("sum(b.amt_paid*b.ctrt_period) as sum_amount,sum(b.amt_paid) as num_month,b.city,b.status")
                ->from("swo_serviceid b")
                ->leftJoin("swo_customer_type_id f","b.cust_type=f.id")
                ->where($whereSql)->group("b.city,b.status")->queryAll();//
            if($rows){
                foreach ($rows as $row){
                    if(!key_exists($row["city"],$list)){
                        $list[$row["city"]]=array(
                            "num_pause"=>0,//暫停金額（年金額）
                            "num_stop"=>0,//停單金額（年金額）
                            "num_month"=>0,//停單金額（月金額）
                        );
                    }
                    $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                    if($row["status"]=="S"){ //暫停
                        $list[$row["city"]]["num_pause"]+= $money;
                    }else{
                        $list[$row["city"]]["num_stop"]+= $money;
                        $list[$row["city"]]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                    }
                }
            }
        }

        if(self::$KABool){ //KA服務的暫停、終止
            $KARows= Yii::app()->db->createCommand()
                ->select("a.id,a.status,a.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            (case b.paid_type
                    when 'M' then b.amt_paid
                    else if(b.ctrt_period='' or b.ctrt_period is null,0,b.amt_paid/b.ctrt_period)
                end
            ) as num_month,
            DATE_FORMAT(a.status_dt,'%Y/%m') as month_date")
                ->from("swo_service_ka_no a")
                ->leftJoin("swo_service_ka b","b.id=a.service_id")
                ->leftJoin("swo_customer_type f","b.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->queryAll();
            if($KARows){
                foreach ($KARows as $row){
                    $city = $row["city"];
                    if(!key_exists($city,$list)){
                        $list[$city]=array(
                            "num_pause"=>0,//暫停金額（年金額）
                            "num_stop"=>0,//停單金額（年金額）
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
                            $list[$city]["num_stop"]+=$money;
                            $list[$city]["num_month"]+= empty($row["num_month"])?0:round($row["num_month"],2);
                        }else{
                            $list[$city]["num_pause"]+=$money;
                        }
                    }
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

        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务的匯總（新增+恢復+更改-暫停-終止)
    public static function getServiceForAll($startDate,$endDate,$city_allow=""){
        $whereSql = "a.status in ('N','S','A','R','T') and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list=array();
        $sumAmtSql = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $b4_sumAmtSql = "case a.b4_paid_type when 'M' then a.b4_amt_paid * a.ctrt_period else a.b4_amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("
            sum(
                if(a.status in ('N','R'),($sumAmtSql),
                    if(a.status='A',($sumAmtSql)-($b4_sumAmtSql),-1*($sumAmtSql))
                )
            ) as sum_amount,a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(
                        if(a.status in ('N','R'),(a.amt_paid*a.ctrt_period),
                            if(a.status='A',(a.amt_paid*a.ctrt_period)-(a.b4_amt_paid*a.ctrt_period),-1*(a.amt_paid*a.ctrt_period))
                        )
                    ) as sum_amount,a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }

        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("
            sum(
                if(a.status in ('N','R'),($sumAmtSql),
                    if(a.status='A',($sumAmtSql)-($b4_sumAmtSql),-1*($sumAmtSql))
                )
            ) as sum_amount,a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=0;
            }
            $list[$row["city"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务各个状态的汇总
    public static function getServiceCountForStatus($startDate,$endDate,$city_allow=""){
        $whereSql = "a.status in ('N','S','A','R','T') and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL;
        $list=array();
        $sumAmtSql = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $b4_sumAmtSql = "case a.b4_paid_type when 'M' then a.b4_amt_paid * a.ctrt_period else a.b4_amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("
            sum(if(a.status='N',($sumAmtSql),0)) as add_sum,
            sum(if(a.status='S',-1*($sumAmtSql),0)) as pause_sum,
            sum(if(a.status='A',($sumAmtSql)-($b4_sumAmtSql),0)) as update_sum,
            sum(if(a.status='R',($sumAmtSql),0)) as renew_sum,
            sum(if(a.status='T',-1*($sumAmtSql),0)) as stop_sum,
            a.city")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("
                sum(if(a.status='N',(a.amt_paid*a.ctrt_period),0)) as add_sum,
                sum(if(a.status='S',-1*(a.amt_paid*a.ctrt_period),0)) as pause_sum,
                sum(if(a.status='A',(a.amt_paid*a.ctrt_period)-(a.b4_amt_paid*a.ctrt_period),0)) as update_sum,
                sum(if(a.status='R',(a.amt_paid*a.ctrt_period),0)) as renew_sum,
                sum(if(a.status='T',-1*(a.amt_paid*a.ctrt_period),0)) as stop_sum,
                a.city")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }

        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("
                sum(if(a.status='N',($sumAmtSql),0)) as add_sum,
                sum(if(a.status='S',-1*($sumAmtSql),0)) as pause_sum,
                sum(if(a.status='A',($sumAmtSql)-($b4_sumAmtSql),0)) as update_sum,
                sum(if(a.status='R',($sumAmtSql),0)) as renew_sum,
                sum(if(a.status='T',-1*($sumAmtSql),0)) as stop_sum,
                a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=array(
                    "add_sum"=>0,
                    "pause_sum"=>0,
                    "update_sum"=>0,
                    "renew_sum"=>0,
                    "stop_sum"=>0,
                    "net_sum"=>0,
                );
            }
            $list[$row["city"]]["add_sum"]+=empty($row["add_sum"])?0:$row["add_sum"];
            $list[$row["city"]]["pause_sum"]+=empty($row["pause_sum"])?0:$row["pause_sum"];
            $list[$row["city"]]["update_sum"]+=empty($row["update_sum"])?0:$row["update_sum"];
            $list[$row["city"]]["renew_sum"]+=empty($row["renew_sum"])?0:$row["renew_sum"];
            $list[$row["city"]]["stop_sum"]+=empty($row["stop_sum"])?0:$row["stop_sum"];
            $list[$row["city"]]["net_sum"] =$list[$row["city"]]["add_sum"];//净增长
            $list[$row["city"]]["net_sum"]+=$list[$row["city"]]["pause_sum"];
            $list[$row["city"]]["net_sum"]+=$list[$row["city"]]["update_sum"];
            $list[$row["city"]]["net_sum"]+=$list[$row["city"]]["renew_sum"];
            $list[$row["city"]]["net_sum"]+=$list[$row["city"]]["stop_sum"];
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
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,sum(case a.b4_paid_type
							when 'M' then a.b4_amt_paid * a.ctrt_period
							else a.b4_amt_paid
						end
					) as b4_sum_amount,a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
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
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum($sum_money) as sum_amount,a.city,
            sum(if(a.ctrt_period>=12,({$sum_money}),0)) as num_long,
            sum(if(a.ctrt_period<12 and a.paid_type!=1,({$sum_money}),0)) as num_short,
            sum(if(a.ctrt_period<12 and a.paid_type=1,({$sum_money}),0)) as one_service,
            sum(if(g.rpt_cat='A01',({$sum_money}),0)) as num_cate,
            sum(if(g.rpt_cat!='A01',({$sum_money}),0)) as num_not_cate
            ")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
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
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum({$sum_money}) as sum_amount,a.city,
            sum(if(a.paid_type=1 and a.ctrt_period<12,({$sum_money}),0)) as num_new_n,
            sum(if(a.paid_type=1 and a.ctrt_period<12,0,({$sum_money}))) as num_new
            ")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
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
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024' and not (a.paid_type=1 and a.ctrt_period<12)")->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
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
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024' and a.paid_type=1 and a.ctrt_period<12")->group("a.city")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
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
        $textSql = "b.Text";
        if(self::$system==2){//國際版
            $textSql = "IF(b.Text='KL' or b.Text='SL','MY',b.Text)";
        }
        if(!empty($city_allow)&&$city_allow!="all"){
            $citySql = " and {$textSql} in ({$city_allow})";
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

    //服务新增INV(餐飲、非餐飲)(台灣版專用)
    public static function getServiceTWForAdd($startDay,$endDay,$city_allow=""){
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql = "a.status='N' and f.rpt_cat='INV' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $list = array();
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("sum($sum_money) as sum_amount,a.city,
            sum(if(g.rpt_cat='A01',({$sum_money}),0)) as num_cate,
            sum(if(g.rpt_cat!='A01',({$sum_money}),0)) as num_not_cate
            ")
            ->from("swoper{$suffix}.swo_service a")
            ->leftJoin("swoper{$suffix}.swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swoper{$suffix}.swo_nature g","a.nature_type=g.id")
            ->where($whereSql)
            ->group("a.city")->queryAll();
        $rows = $rows?$rows:array();

        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=array(
                    "sum_money"=>0,
                    "u_num_cate"=>0,//餐饮客户
                    "u_num_not_cate"=>0//非餐饮客户
                );
            }
            $list[$row["city"]]["sum_money"]+=$row["sum_amount"];
            $list[$row["city"]]["u_num_cate"]+=$row["num_cate"];
            $list[$row["city"]]["u_num_not_cate"]+=$row["num_not_cate"];
        }
        return $list;
    }

    //获取U系统的產品数据
    public static function getUInvMoney($startDay,$endDay,$city_allow=""){
        if(self::$system===1){//台灣版的產品為lbs的inv新增
            return self::getServiceTWForAdd($startDay,$endDay,$city_allow);
        }
        $city = "";
        if(!empty($city_allow)&&$city_allow!="all"){
            $city = $city_allow;
        }
        if(self::$system===2&&!empty($city)&&strpos($city,"'MY'")!==false){//國際版
            $city.=",'KL','SL'";
        }
        $json = Invoice::getInvData($startDay,$endDay,$city);
        $list = array();
        $Catering = self::$system===2?"Catering":"餐饮类";
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
                if($row["customer_type"]===$Catering){
                    $list[$city]["u_num_cate"]+=$money;
                }else{
                    $list[$city]["u_num_not_cate"]+=$money;
                }
            }
        }
        return $list;
    }

    //获取U系统的服务单数据（月為鍵名)
    public static function getUServiceMoneyToMonth($endDay,$city_allow="",$lastBool=false){
        $year = date("Y",strtotime($endDay));
        $startDay =$year."/01/01";
        if($lastBool){//多查询一个月
            $startDay =($year-1)."/12/01";
        }
        $maxMonth = date("n",strtotime($endDay));
        $monthList = array();
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
        $list = array();
        $citySql = "";
        $textSql = "b.Text";
        if(self::$system==2){//國際版
            $textSql = "IF(b.Text='KL' or b.Text='SL','MY',b.Text)";
        }
        if(!empty($city_allow)&&$city_allow!="all"){
            $citySql = " and {$textSql} in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.Text,sum(
                    if(a.TermCount=0,0,a.Fee/a.TermCount)
					) as sum_amount,DATE_FORMAT(a.JobDate,'%Y/%m') as month_dt")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->group("b.Text,DATE_FORMAT(a.JobDate,'%Y/%m')")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = self::resetCity($row["Text"]);
                $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                if(!key_exists($city,$list)){
                    $list[$city]=$monthList;
                }
                $list[$city][$row["month_dt"]]+=$money;
            }
        }
        return $list;
    }

    //客户服务查询(更改)（月為鍵名)
    public static function getServiceAToMonth($endDate,$city_allow=""){
        $year = date("Y",strtotime($endDate));
        $startDate =$year."/01/01";
        $maxMonth = date("n",strtotime($endDate));
        $monthList = array();
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
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
					) as b4_sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,
                sum(a.b4_amt_money) as b4_sum_amount,
                a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,sum(case a.b4_paid_type
							when 'M' then a.b4_amt_paid * a.ctrt_period
							else a.b4_amt_paid
						end
					) as b4_sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=$monthList;
            }
            $list[$row["city"]][$row["month_dt"]]+=$row["sum_amount"]-$row["b4_sum_amount"];
        }
        return $list;
    }

    //客户服务查询(根據服務類型)（月為鍵名)
    public static function getServiceForTypeToMonth($endDate,$city_allow="",$type="N"){
        $year = date("Y",strtotime($endDate));
        $startDate =$year."/01/01";
        $maxMonth = date("n",strtotime($endDate));
        $monthList = array();
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
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
					) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();//
            $IDRows = $IDRows?$IDRows:array();
            $rows = array_merge($rows,$IDRows);
        }
        if(self::$KABool){
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024'")
                ->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=$monthList;
            }
            $list[$row["city"]][$row["month_dt"]]+=$row["sum_amount"];
        }
        return $list;
    }

    //獲取暫停、終止（月為鍵名)
    public static function getServiceForSTToMonth($end_dt,$city_allow,$type="all"){
        $year = date("Y",strtotime($end_dt));
        $start_dt =$year."/01/01";
        $maxMonth = date("n",strtotime($end_dt));
        $monthList = array();
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
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
            ->select("a.id,b.status,b.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            DATE_FORMAT(b.status_dt,'%Y/%m') as month_date")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","b.id=a.service_id")
            ->leftJoin("swo_customer_type f","b.cust_type=f.id")
            ->where($whereSql)
            ->queryAll();
        if($rows){//
            foreach ($rows as $row){
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
                    $city = $row["city"];
                    if(!key_exists($city,$list)){
                        $list[$city]=$monthList;
                    }
                    $money = empty($row["sum_money"])?0:round($row["sum_money"],2);
                    $list[$city][$row['month_date']]+=$money*-1;
                }
            }
        }
        if(self::$KABool){
            $KARows= Yii::app()->db->createCommand()
                ->select("a.id,b.status,b.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            DATE_FORMAT(b.status_dt,'%Y/%m') as month_date")
                ->from("swo_service_ka_no a")
                ->leftJoin("swo_service_ka b","b.id=a.service_id")
                ->leftJoin("swo_customer_type f","b.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(b.status_dt,'%Y')<'2024'")
                ->queryAll();
            if($KARows){//
                foreach ($KARows as $row){
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
                        $city = $row["city"];
                        if(!key_exists($city,$list)){
                            $list[$city]=$monthList;
                        }
                        $money = empty($row["sum_money"])?0:round($row["sum_money"],2);
                        $list[$city][$row['month_date']]+=$money*-1;
                    }
                }
            }
        }

        if(self::$IDBool){ //ID服務的暫停、終止
            $rows = Yii::app()->db->createCommand()
                ->select("sum(b.amt_paid*b.ctrt_period) as sum_amount,b.city,
                DATE_FORMAT(b.status_dt,'%Y/%m') as month_date")
                ->from("swo_serviceid b")
                ->leftJoin("swo_customer_type_id f","b.cust_type=f.id")
                ->where($whereSql)->group("b.city,DATE_FORMAT(b.status_dt,'%Y/%m')")->queryAll();//
            if($rows){
                foreach ($rows as $row){
                    if(!key_exists($row["city"],$list)){
                        $list[$row["city"]]=$monthList;
                    }
                    $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                    $list[$row["city"]][$row["month_date"]]+= $money*-1;
                }
            }
        }
        return $list;
    }

    //服务新增（一次性)（月為鍵名)
    public static function getServiceAddForYToMonth($endDay,$city_allow=""){
        $year = date("Y",strtotime($endDay));
        $maxMonth = date("n",strtotime($endDay));
        $startDay =($year-1)."/12/01";
        $monthList = array();
        $monthList[($year-1)."/12"]=0;
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
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
					) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql." and a.paid_type=1 and a.ctrt_period<12")
            ->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
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
            $KARows = Yii::app()->db->createCommand()
                ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and DATE_FORMAT(a.status_dt,'%Y')<'2024' and a.paid_type=1 and a.ctrt_period<12")
                ->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=$monthList;
            }
            $thisMonth = $row["month_dt"];
            $list[$row["city"]][$thisMonth]+=$row["sum_amount"];
        }
        return $list;
    }


    //获取U系统的服务单数据(周為鍵名)
    public static function getUServiceMoneyForWeek($startDay,$endDay,$city_allow=""){
        $list = array();
        $citySql = "";
        $textSql = "b.Text";
        if(self::$system==2){//國際版
            $textSql = "IF(b.Text='KL' or b.Text='SL','MY',b.Text)";
        }
        if(!empty($city_allow)&&$city_allow!="all"){
            $citySql = " and {$textSql} in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.Text,a.JobDate,sum(
                    if(a.TermCount=0,0,a.Fee/a.TermCount)
					) as sum_amount")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->group("b.Text,a.JobDate")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = self::resetCity($row["Text"]);
                $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                if(!key_exists($city,$list)){
                    $list[$city]=array();
                }
                $dateKay = self::getWeekStart($row["JobDate"]);
                if(!key_exists($dateKay,$list[$city])){
                    $list[$city][$dateKay]=0;
                }
                $list[$city][$dateKay]+=$money;
            }
        }
        return $list;
    }

    //服务新增INV(餐飲、非餐飲)(台灣版專用)(周為鍵名)
    public static function getServiceTWForWeekAdd($startDay,$endDay,$city_allow=""){
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql = "a.status='N' and f.rpt_cat='INV' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $list = array();
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("sum($sum_money) as sum_amount,a.city,a.status_dt")
            ->from("swoper{$suffix}.swo_service a")
            ->leftJoin("swoper{$suffix}.swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swoper{$suffix}.swo_nature g","a.nature_type=g.id")
            ->where($whereSql)
            ->group("a.city,a.status_dt")->queryAll();
        $rows = $rows?$rows:array();

        foreach ($rows as $row){
            $city = $row["city"];
            if(!key_exists($city,$list)){
                $list[$city]=array();
            }
            $dateKay = self::getWeekStart($row["status_dt"]);
            if(!key_exists($dateKay,$list[$city])){
                $list[$city][$dateKay]=0;
            }
            $list[$city][$dateKay]+=$row["sum_amount"];
        }
        return $list;
    }

    //获取U系统的產品数据（周為鍵名)
    public static function getUInvMoneyForWeek($startDay,$endDay,$city_allow=""){
        if(self::$system===1){//台灣版的產品為lbs的inv新增
            return self::getServiceTWForWeekAdd($startDay,$endDay,$city_allow);
        }
        $city = "";
        if(!empty($city_allow)&&$city_allow!="all"){
            $city = $city_allow;
        }
        if(self::$system===2&&!empty($city)&&strpos($city,"'MY'")!==false){//國際版
            $city.=",'KL','SL'";
        }
        $json = Invoice::getInvData($startDay,$endDay,$city);
        $list = array();
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = self::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(!key_exists($city,$list)){
                    $list[$city]=array();
                }
                $dateKay = self::getWeekStart($row["invoice_dt"]);
                if(!key_exists($dateKay,$list[$city])){
                    $list[$city][$dateKay]=0;
                }
                $list[$city][$dateKay]+=$money;
            }
        }
        return $list;
    }

    //服务新增INV產品数据(台灣版專用)
    public static function getUInvTWMoneyToMonth($endDay,$city_allow=""){
        $suffix = Yii::app()->params['envSuffix'];
        $year = date("Y",strtotime($endDay));
        $maxMonth = date("n",strtotime($endDay));
        $startDay =($year-1)."/12/01";
        $monthList = array();
        $monthList[($year-1)."/12"]=0;
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
        $whereSql = "a.status='N' and f.rpt_cat='INV' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $list = array();
        $sum_money = "case a.paid_type when 'M' then a.amt_paid * a.ctrt_period else a.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("sum({$sum_money}) as sum_amount,a.city,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swoper{$suffix}.swo_service a")
            ->leftJoin("swoper{$suffix}.swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)
            ->group("a.city,DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
        $rows = $rows?$rows:array();

        foreach ($rows as $row){
            if(!key_exists($row["city"],$list)){
                $list[$row["city"]]=$monthList;
            }
            $thisMonth = $row["month_dt"];
            $list[$row["city"]][$thisMonth]+=$row["sum_amount"];
        }
        return $list;
    }

    //获取U系统的產品数据（月為鍵名)
    public static function getUInvMoneyToMonth($endDay,$city_allow=""){
        if(self::$system===1){//台灣版的產品為lbs的inv新增
            return self::getUInvTWMoneyToMonth($endDay,$city_allow);
        }
        $city = "";
        if(!empty($city_allow)&&$city_allow!="all"){
            $city = $city_allow;
        }
        if(self::$system===2&&!empty($city)&&strpos($city,"'MY'")!==false){//國際版
            $city.=",'KL','SL'";
        }
        $year = date("Y",strtotime($endDay));
        $maxMonth = date("n",strtotime($endDay));
        $startDay =($year-1)."/12/01";
        $json = Invoice::getInvData($startDay,$endDay,$city);
        $monthList = array();
        $monthList[($year-1)."/12"]=0;
        for ($i=1;$i<=$maxMonth;$i++){
            $month = $i<10?"0".$i:$i;
            $monthList["{$year}/{$month}"]=0;
        }
        $list = array();
        $count = 0;
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = self::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                $thisMonth = date("Y/m",strtotime($row["invoice_dt"]));
                if($thisMonth=="2023/05"){
                    $count++;
                }
                if(!key_exists($city,$list)){
                    $list[$city]=$monthList;
                }
                $list[$city][$thisMonth]+=$money;
            }
        }
        return $list;
    }

    public static function computeLastMonth($date,$diffMonth=1){
        $lastDate = date("Y/m/d",strtotime($date." - {$diffMonth} month"));
        $maxDay = date("t",strtotime($date));
        $thisDay = date("d",strtotime($date));
        $lastDay = date("d",strtotime($lastDate));
        if($maxDay==$thisDay){
            if($lastDay<$thisDay){ //大月份转小月份
                $lastDate = date("Y/m/01",strtotime($lastDate));
                $lastDate = date("Y/m/d",strtotime($lastDate." - 1 day"));
            }elseif($lastDay==$thisDay){ //小月份转大月份
                $lastDate = date("Y/m/t",strtotime($lastDate));
            }elseif($lastDay>$thisDay){ //本情况不可能发生
                //$lastDate = date("Y/m/t",strtotime($lastDate));
            }
        }else{
            if($thisDay!=$lastDay){
                $lastDate = date("Y/m/01",strtotime($lastDate));
                $lastDate = date("Y/m/d",strtotime($lastDate." - 1 day"));
            }
        }

        return $lastDate;
    }

    //轉換U系統的城市（國際版專用）
    public static function resetCity($city){
        if(self::$system===2){
            switch($city){
                case "KL":
                    return "MY";
                case "SL":
                    return "MY";
            }
        }
        return $city;
    }

    //获取本周的起始天数（周六为起始天）
    public static function getWeekStart($date){
        $date = date("Y/m/d",strtotime($date));
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date("w",strtotime($date));
        //一周：周六至周五
        $weekStart = date("Y/m/d",strtotime($date." - ".($w==6?0:$w+1)." day"));
        return $weekStart;
    }
}