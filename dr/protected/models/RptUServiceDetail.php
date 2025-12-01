<?php
class RptUServiceDetail extends ReportData2 {
    public $condition="";//筛选条件
    public $seniority_min=0;//年资（最小）
    public $seniority_max=9999;//年资（最大）

	public function fields() {
		return array(
			'city'=>array('label'=>Yii::t('summary','city name'),'width'=>18,'align'=>'L'),
			'job_date'=>array('label'=>Yii::t('summary','job date'),'width'=>18,'align'=>'L'),
			'contract_code'=>array('label'=>Yii::t('summary','Contract Code'),'width'=>18,'align'=>'L'),
			'customer_code'=>array('label'=>Yii::t('summary','U Customer Code'),'width'=>18,'align'=>'L'),
			'customer_name'=>array('label'=>Yii::t('summary','Customer Name'),'width'=>30,'align'=>'L'),
			'username'=>array('label'=>Yii::t('summary','Technical'),'width'=>22,'align'=>'L'),
			'dept_name'=>array('label'=>Yii::t('summary','dept name'),'width'=>18,'align'=>'L'),
			'city_name'=>array('label'=>Yii::t('summary','city'),'width'=>18,'align'=>'L'),
			'district'=>array('label'=>Yii::t('summary','district'),'width'=>18,'align'=>'L'),

			'address'=>array('label'=>Yii::t('summary','address'),'width'=>30,'align'=>'L'),
			'start_date'=>array('label'=>Yii::t('summary','start date'),'width'=>30,'align'=>'L'),
			'end_date'=>array('label'=>Yii::t('summary','end date'),'width'=>30,'align'=>'L'),
			'amt'=>array('label'=>Yii::t('summary','Paid Amt'),'width'=>18,'align'=>'R'),
			'unit_price'=>array('label'=>"工单金额(unit_price)",'width'=>18,'align'=>'R'),
			'fee'=>array('label'=>"费用(fee)",'width'=>18,'align'=>'R'),
			'add_first'=>array('label'=>"首次加做金额(add_first)",'width'=>18,'align'=>'R'),
			'revise_fee'=>array('label'=>"调整金额(revise_fee)",'width'=>18,'align'=>'R'),
			'term_count'=>array('label'=>"期数(term_count)",'width'=>18,'align'=>'R'),
			'staffListStr'=>array('label'=>"参与员工",'width'=>18,'align'=>'R'),
			'staffCount'=>array('label'=>"参与员工人数",'width'=>18,'align'=>'R'),
			'staff_ratio'=>array('label'=>"分配比例",'width'=>18,'align'=>'R'),
			'staff_ratio_one'=>array('label'=>"分配比例(该员工)",'width'=>18,'align'=>'R'),
			'amt_lbs'=>array('label'=>"LBS计算金额",'width'=>18,'align'=>'R'),
			'amt_bool'=>array('label'=>"服务金额是否等于LBS计算金额",'width'=>20,'align'=>'C'),
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $city = $this->criteria->city;
        $city_allow = self::getCityAllow($city);
        $startDay = isset($this->criteria->start_dt)?date("Y/m/d",strtotime($this->criteria->start_dt)):date("Y/m/d");
        $endDay = isset($this->criteria->end_dt)?date("Y/m/d",strtotime($this->criteria->end_dt)):date("Y/m/d");

        $rows = CountSearch::getTechnicianDetail($startDay,$endDay,$city_allow);
        $staffStrList = array("staff01","staff02","staff03");
        $list = array();
        $userList = $this->getUserList($city_allow,$endDay);
        $city_name = "";
        $oldCity = "";
		if ($rows) {
			foreach ($rows as $row) {
                $staff_ratio = key_exists("staff_ratio",$row)?$row["staff_ratio"]:array();//分配比例
                $staffRatio = array();
                if(!empty($staff_ratio)){
                    $staffRatioStr="";
                    foreach ($staff_ratio as $ratioRow){
                        $staffRatioStr.=empty($staffRatioStr)?"":",";
                        $staffRatioStr.=$ratioRow["code"].":".$ratioRow["ratio"];
                        $staffRatio[$ratioRow["code"]] = $ratioRow["ratio"];
                    }
                }else{
                    $staffRatioStr="";
                }
                $city = SummaryForm::resetCity($row["city_code"]);
                if($oldCity!==$city){
                    $city_name = General::getCityName($city);
                    $oldCity = $city;
                }
                $amt_lbs_sum = empty($row["term_count"])?0:(floatval($row["fee"])+floatval($row["add_first"]))/floatval($row["term_count"]);
                if(isset($row["unit_price"])&&($row["unit_price"]!==''||$row["unit_price"]!==null)){
                    $countMoney = $row["unit_price"];//派单系统可以随意设置金额
                }else{
                    $countMoney = $amt_lbs_sum;
                }
                //revise_fee
                if(key_exists("revise_fee",$row)){//调整服务单金额(2024-04-03)
                    $amt_lbs_sum+=floatval($row["revise_fee"]);
                    $countMoney+=floatval($row["revise_fee"]);
                }
                if(key_exists("staff_arr",$row)){//新版U系统有多个员工
                    $staffCount = count($row["staff_arr"]);
                    $staffStrList = $row["staff_arr"];
                }else{
                    $staffCount = 1;
                    $staffCount+= empty($row["staff02"])?0:1;
                    $staffCount+= empty($row["staff03"])?0:1;
                }
                $money = $countMoney/$staffCount;//如果多人，需要平分金額
                $money = round($money,2);
                $amt_lbs = $amt_lbs_sum/$staffCount;//如果多人，需要平分金額
                $amt_lbs = round($amt_lbs,2);
                foreach ($staffStrList as $staffStr){
                    $staff = key_exists("staff_arr",$row)?$staffStr:$row[$staffStr];//员工编号
                    if(!empty($staffRatio)&&key_exists($staff,$staffRatio)){//自定义分配比例
                        $staffRatioOne =$staffRatio[$staff];
                        $money = empty($staffRatioOne)?0:$countMoney*$staffRatioOne;//自定义分配比例
                        $money = round($money,2);
                        $amt_lbs = $amt_lbs_sum/$staffCount;//自定义分配比例
                        $amt_lbs = round($amt_lbs,2);
                    }else{
                        $staffRatioOne = "";
                    }
                    $user = self::getUserListForCode($staff,$userList);
                    $username = $user["name"]." ({$user["code"]})".($user["staff_status"]==-1?Yii::t("summary"," - Leave"):"");
                    if(!empty($staff)){
                        $list[]=array(
                            "city"=>$city_name,//LBS城市
                            "job_date"=>$row["job_date"],//工作日期（U系统）
                            "contract_code"=>$row["contract_code"],//合约编号（U系统）
                            "customer_code"=>$row["customer_code"],//客户编号（U系统）
                            "customer_name"=>$row["customer_name"],//客户名称（U系统）
                            "username"=>$username,//员工(人事系統)
                            "dept_name"=>$user["dept_name"],//职位（人事系统）
                            "city_name"=>$row["city_name"],//市（U系统）
                            "district"=>$row["district"],//区（U系统）
                            "address"=>$row["address"],//地址（U系统）
                            "start_date"=>$row["start_date"],//（U系统）
                            "end_date"=>$row["end_date"],//（U系统）
                            "amt"=>$money,//服务金额
                            "fee"=>$row["fee"],//费用
                            "unit_price"=>$row["unit_price"],//工单金额
                            "add_first"=>$row["add_first"],//首次加做金额
                            "revise_fee"=>$row["revise_fee"],//调整金额
                            "term_count"=>$row["term_count"],//期数
                            "staffListStr"=>implode(",",$staffStrList),//参与员工
                            "staffCount"=>$staffCount,//参与员工人数
                            "staff_ratio"=>$staffRatioStr,//分配比例
                            "staff_ratio_one"=>$staffRatioOne,//分配比例
                            "amt_lbs"=>$amt_lbs,//LBS计算金额
                            "amt_bool"=>$money==$amt_lbs?"是":"否",//服务金额是否等于LBS计算金额
                        );
                    }
                }
			}
		}
        $this->data = $list;
		return true;
	}

    public function getUserListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("level_type"=>3,"staff_status"=>0,"code"=>$code,"name"=>"","dept_name"=>"","entry_month"=>"");
		}
	}

	public function getUserList($city_allow,$endDate){
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql = "a.city in ({$city_allow})";
        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.staff_status,a.entry_time,g.name as dept_name,a.name,a.city,
            g.level_type")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept g","a.position = g.id")
            //需要评核类型：技术员 并且 参与评分差异
            ->where($whereSql)
            ->order("a.city")
            ->queryAll();
        $list = array();
        if($rows){
        	foreach ($rows as $row){
                if (CountSearch::getSystem()==1){ //台湾版使用旧编号
                    $staffCode = $row['code_old'];
                }else{
                    $staffCode = $row['code'];
                }
                $list[$staffCode]=$row;
			}
		}
        return $list;
	}

	public static function getCityAllow($city){
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
        if (CountSearch::getSystem()==2&&strpos($city_allow,'MY')!==false){ //國際版的MY需要轉換成KL，SL
            $city_allow.=",'KL','SL'";
        }
        return $city_allow;
	}

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName();
	}
}
?>
