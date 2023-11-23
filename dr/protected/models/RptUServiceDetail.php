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
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $city = $this->criteria->city;
        $city_allow = self::getCityAllow($city);
        $startDay = isset($this->criteria->start_dt)?date("Y/m/d",strtotime($this->criteria->start_dt)):date("Y/m/d");
        $endDay = isset($this->criteria->end_dt)?date("Y/m/d",strtotime($this->criteria->end_dt)):date("Y/m/d");
        $citySql = " and b.Text in ({$city_allow})";
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.Text,a.Addr,h.Text as area_name,g.Text as city_name,
            a.CustomerName,a.CustomerID,a.ContractNumber,
            a.JobDate,a.StartTime,a.FinishDate,a.FinishTime,
            a.Fee,a.TermCount,Staff01,Staff02,Staff03")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->leftJoin("service{$suffix}.enums h","a.District = h.EnumID and h.EnumType=1")
            ->leftJoin("service{$suffix}.enums g","a.City = g.EnumID and g.EnumType=1")
            ->where("a.Status=3 and b.Text not in ('ZY') and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->order("b.Text,a.JobDate desc")
            ->queryAll();
        $staffStrList = array("Staff01","Staff02","Staff03");
        $list = array();
        $userList = $this->getUserList($city_allow,$endDay);
        $city_name = "";
        $oldCity = "";
		if ($rows) {
			foreach ($rows as $row) {
                $city = SummaryForm::resetCity($row["Text"]);
                if($oldCity!==$city){
                    $city_name = General::getCityName($city);
                    $oldCity = $city;
                }
                $money = empty($row["TermCount"])?0:floatval($row["Fee"])/floatval($row["TermCount"]);

                $staffCount = 1;
                $staffCount+= empty($row["Staff02"])?0:1;
                $staffCount+= empty($row["Staff03"])?0:1;
                $money = $money/$staffCount;//如果多人，需要平分金額
                $money = round($money,2);
                foreach ($staffStrList as $staffStr){
                    $staff = $row[$staffStr];//员工编号
                    $user = self::getUserListForCode($staff,$userList);
                    $username = $user["name"]." ({$user["code"]})".($user["staff_status"]==-1?Yii::t("summary"," - Leave"):"");
                    if(!empty($staff)){
                        $list[]=array(
                            "city"=>$city_name,//LBS城市
                            "job_date"=>$row["JobDate"],//工作日期（U系统）
                            "contract_code"=>$row["ContractNumber"],//合约编号（U系统）
                            "customer_code"=>$row["CustomerID"],//客户编号（U系统）
                            "customer_name"=>$row["CustomerName"],//客户名称（U系统）
                            "username"=>$username,//员工(人事系統)
                            "dept_name"=>$user["dept_name"],//职位（人事系统）
                            "city_name"=>$row["city_name"],//市（U系统）
                            "district"=>$row["area_name"],//区（U系统）
                            "address"=>$row["Addr"],//地址（U系统）
                            "start_date"=>$row["JobDate"]." ".$row["StartTime"],//（U系统）
                            "end_date"=>$row["FinishDate"]." ".$row["FinishTime"],//（U系统）
                            "amt"=>$money,//服务金额
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

	public static function getUserList($city_allow,$endDate){
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
        $city_allow = City::model()->getDescendantList($city);
        $cstr = $city;
        $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
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
