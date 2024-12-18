<?php

/**
 * 清洁灭虫查询
 */
class CountCleanSearch extends CountSearch{
    public $cleanWhere=" and not(f.rpt_cat='INV' and f.single=1)";

    public function setWhereSql($sql){
        $this->cleanWhere = $sql;
    }

    //服务新增（一次性)（月為鍵名)
    public function getClearServiceAddForYToMonth($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= $this->cleanWhere;
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql." and a.paid_type=1 and a.ctrt_period<12")
            ->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
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
					) as sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx} and a.paid_type=1 and a.ctrt_period<12")
                ->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            $thisMonth = $row["month_dt"];
            if(!key_exists($thisMonth,$list)){
                $list[$thisMonth]=0;
            }
            $list[$thisMonth]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务查询(根據服務類型)（月為鍵名)
    public function getClearServiceForTypeToMonthEx($startDate,$endDate,$city_allow="",$type="N"){
        $whereSql = "a.status='{$type}' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= $this->cleanWhere;
        $list=array();
        $rows = Yii::app()->db->createCommand()
            ->select("sum(case a.paid_type
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();//
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
					) as sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            $thisMonth = $row["month_dt"];
            if(!key_exists($thisMonth,$list)){
                $list[$thisMonth]=0;
            }
            $list[$thisMonth]+=$row["sum_amount"];
        }
        return $list;
    }

    //客户服务查询(更改)（月為鍵名)
    public function getClearServiceAToMonth($startDate,$endDate,$city_allow=""){
        $whereSql = "a.status='A' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        if(!empty($city_allow)&&$city_allow!="all"){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= $this->cleanWhere;
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
					) as b4_sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
        $rows = $rows?$rows:array();

        if(self::$IDBool){
            $IDRows = Yii::app()->db->createCommand()
                ->select("sum(a.amt_paid*a.ctrt_period) as sum_amount,
                sum(a.b4_amt_money) as b4_sum_amount,
                DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->where($whereSql)->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
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
					) as b4_sum_amount,DATE_FORMAT(a.status_dt,'%Y/%m') as month_dt")
                ->from("swo_service_ka a")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
                ->group("DATE_FORMAT(a.status_dt,'%Y/%m')")->queryAll();
            $KARows = $KARows?$KARows:array();
            $rows = array_merge($rows,$KARows);
        }
        foreach ($rows as $row){
            $thisMonth = $row["month_dt"];
            if(!key_exists($thisMonth,$list)){
                $list[$thisMonth]=0;
            }
            $list[$thisMonth]+=$row["sum_amount"]-$row["b4_sum_amount"];
        }
        return $list;
    }

    //獲取暫停、終止（月為鍵名)
    public function getClearServiceForSTToMonth($start_dt,$end_dt,$city_allow,$type="all"){
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
        $whereSql .= $this->cleanWhere;
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
                    $thisMonth = $row['month_date'];
                    if(!key_exists($thisMonth,$list)){
                        $list[$thisMonth]=0;
                    }
                    $money = empty($row["sum_money"])?0:round($row["sum_money"],2);
                    $list[$thisMonth]+=$money*-1;
                }
            }
        }
        if(self::$KABool){
            $kaSqlPrx = self::getServiceKASQL("b.");
            $KARows= Yii::app()->db->createCommand()
                ->select("a.id,b.status,b.status_dt,a.contract_no,a.service_id,
            b.city,({$sum_money}) as sum_money,
            DATE_FORMAT(b.status_dt,'%Y/%m') as month_date")
                ->from("swo_service_ka_no a")
                ->leftJoin("swo_service_ka b","b.id=a.service_id")
                ->leftJoin("swo_customer_type f","b.cust_type=f.id")
                ->where($whereSql." and {$kaSqlPrx}")
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
                        $thisMonth = $row['month_date'];
                        if(!key_exists($thisMonth,$list)){
                            $list[$thisMonth]=0;
                        }
                        $money = empty($row["sum_money"])?0:round($row["sum_money"],2);
                        $list[$thisMonth]+=$money*-1;
                    }
                }
            }
        }

        if(self::$IDBool){ //ID服務的暫停、終止
            $rows = Yii::app()->db->createCommand()
                ->select("sum(b.amt_paid*b.ctrt_period) as sum_amount,
                DATE_FORMAT(b.status_dt,'%Y/%m') as month_date")
                ->from("swo_serviceid b")
                ->leftJoin("swo_customer_type_id f","b.cust_type=f.id")
                ->where($whereSql)->group("DATE_FORMAT(b.status_dt,'%Y/%m')")->queryAll();//
            if($rows){
                foreach ($rows as $row){
                    $thisMonth = $row['month_date'];
                    if(!key_exists($thisMonth,$list)){
                        $list[$thisMonth]=0;
                    }
                    $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                    $list[$thisMonth]+= $money*-1;
                }
            }
        }
        return $list;
    }
}