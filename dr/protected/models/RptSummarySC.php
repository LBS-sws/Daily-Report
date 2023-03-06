<?php
class RptSummarySC extends ReportData2 {
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $where = '';
        if(!isset($this->criteria->start_dt)){
            $this->criteria->start_dt = date("Y/m/01");
        }
        if(!isset($this->criteria->end_dt)){
            $this->criteria->end_dt = date("Y/m/31");
        }
        $this->criteria->start_dt = General::toDate($this->criteria->start_dt);
        $this->criteria->end_dt = General::toDate($this->criteria->end_dt);
        $where .= " and "."a.status_dt>='{$this->criteria->start_dt} 00:00:00'";
        $where .= " and "."a.status_dt<='{$this->criteria->end_dt} 23:59:59'";

        $rows = Yii::app()->db->createCommand()
            ->select("a.status,f.rpt_cat,f.single,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.paid_type,a.amt_paid,a.ctrt_period,a.b4_paid_type,a.b4_amt_paid
            ,b.region,b.name as city_name,c.name as region_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->leftJoin("security{$suffix}.sec_city c","b.region=c.code")
            ->where("a.city not in ('ZY') {$where}")
            ->order("a.city")
            ->queryAll();
        $data = array();
        $cityList = array();
		if($rows){
            foreach ($rows as $row) {
                //rpt_cat='INV' and single=1的客户服务是产品，所以需要筛选出去
                if($row["rpt_cat"]==="INV"&&intval($row["single"])===1){
                    continue;
                }
                $row["region"] = $this->strUnsetNumber($row["region"]);
                $row["region_name"] = $this->strUnsetNumber($row["region_name"]);
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $region = empty($row["region"])?"none":$row["region"];
                $city = empty($row["city"])?"none":$row["city"];
                $region = $city==="MO"?"MO":$region;//澳門地區單獨顯示
                if(!key_exists($region,$data)){
                    $data[$region]=array(
                        "region"=>$region,
                        "region_name"=>$row["region_name"],
                        "list"=>array()
                    );
                }
                if(!key_exists($city,$data[$region]["list"])){
                    $cityList[$city] = $region;
                    $data[$region]["list"][$city]=array(
                        "city"=>$city,
                        "city_name"=>$row["city_name"],
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
                        $data[$region]["list"][$city]["num_new"]+=$money;
                        $data[$region]["list"][$city]["num_long"]+=$row["ctrt_period"]>=12?$money:0;
                        $data[$region]["list"][$city]["num_short"]+=$row["ctrt_period"]<12?$money:0;
                        $data[$region]["list"][$city]["num_cate"]+=$row["nature_rpt_cat"]=="A01"?$money:0;
                        $data[$region]["list"][$city]["num_not_cate"]+=$row["nature_rpt_cat"]!="A01"?$money:0;
                        break;
                    case "A"://更改
                        $data[$region]["list"][$city]["num_update"]+=($money-$b4_money);
                        break;
                    case "S"://暂停
                        $money*=-1;
                        $data[$region]["list"][$city]["num_pause"]+=$money;
                        break;
                    case "R"://恢复
                        $data[$region]["list"][$city]["num_restore"]+=$money;
                        break;
                    case "T"://终止
                        $money*=-1;
                        $data[$region]["list"][$city]["num_stop"]+=$money;
                        break;
                }

            }
        }

        //獲取U系統的數據
        $this->getUData($data,$cityList);

        $this->data = $data;
		return true;
	}

    //獲取U系統的數據
	protected function getUData(&$data,$cityList){
        $json = Invoice::getInvData($this->criteria->start_dt,$this->criteria->end_dt);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = $row["city"];
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(key_exists($city,$cityList)){
                    $region = $cityList[$city];
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

	protected function strUnsetNumber($str){
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