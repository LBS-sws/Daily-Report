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

        $rows = CountSearch::getTechnicianDetail($startDay,$endDay,$city_allow);
        $staffStrList = array("staff01","staff02","staff03");
        $list = array();
        $userList = $this->getUserList($city_allow,$endDay);
        $city_name = "";
        $oldCity = "";
		if ($rows) {
			foreach ($rows as $row) {
                $city = SummaryForm::resetCity($row["city_code"]);
                if($oldCity!==$city){
                    $city_name = General::getCityName($city);
                    $oldCity = $city;
                }
                $money = empty($row["term_count"])?0:(floatval($row["fee"])+floatval($row["add_first"]))/floatval($row["term_count"]);

                $staffCount = 1;
                $staffCount+= empty($row["staff02"])?0:1;
                $staffCount+= empty($row["staff03"])?0:1;
                $money = $money/$staffCount;//如果多人，需要平分金額
                $money = round($money,2);
                foreach ($staffStrList as $staffStr){
                    $staff = $row[$staffStr];//员工编号
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
