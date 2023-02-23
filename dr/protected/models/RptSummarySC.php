<?php
class RptSummarySC extends ReportData2 {
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $where = '';
		if (isset($this->criteria)) {
			if (isset($this->criteria->start_dt))
				$where .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
		}
		if(empty($where)){
		    $where = "and a.id<0";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.status,a.city,a.nature_type,a.paid_type,a.amt_paid,a.ctrt_period,a.b4_paid_type,a.b4_amt_paid
            ,b.region,b.name as city_name,c.name as region_name")
            ->from("swo_service a")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->leftJoin("security{$suffix}.sec_city c","b.region=c.code")
            ->where("a.id>0 {$where}")
            ->order("a.city")
            ->queryAll();
        $data = array();
		if($rows){
            foreach ($rows as $row) {
                $row["region"] = $this->strUnsetNumber($row["region"]);
                $row["region_name"] = $this->strUnsetNumber($row["region_name"]);
                $row["nature_type"] = is_numeric($row["nature_type"])?intval($row["nature_type"]):0;
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $region = empty($row["region"])?"none":$row["region"];
                $city = empty($row["city"])?"none":$row["city"];
                if(!key_exists($region,$data)){
                    $data[$region]=array(
                        "region"=>$region,
                        "region_name"=>$row["region_name"],
                        "list"=>array()
                    );
                }
                if(!key_exists($city,$data[$region]["list"])){
                    $data[$region]["list"][$city]=array(
                        "city"=>$city,
                        "city_name"=>$row["city_name"],
                        "num_new"=>0,//新增
                        "num_stop"=>0,//终止服务
                        "num_restore"=>0,//恢复服务
                        "num_pause"=>0,//暂停服务
                        "num_update"=>0,//更改服务
                        "num_growth"=>0,//净增长
                        "num_long"=>0,//长约（>=12月）
                        "num_short"=>0,//短约
                        "num_cate"=>0,//餐饮客户
                        "num_not_cate"=>0,//非餐饮客户
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
                        $data[$region]["list"][$city]["num_cate"]+=$row["nature_type"]==2?$money:0;
                        $data[$region]["list"][$city]["num_not_cate"]+=$row["nature_type"]!=2?$money:0;
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
        $this->data = $data;
		return true;
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