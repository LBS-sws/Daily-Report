<?php
class RptUService extends ReportData2 {
    public $condition="";//筛选条件
    public $seniority_min=0;//年资（最小）
    public $seniority_max=9999;//年资（最大）

	public function fields() {
		return array(
			'area'=>array('label'=>Yii::t('report','Area'),'width'=>18,'align'=>'L'),
			'u_city_name'=>array('label'=>Yii::t('report','City'),'width'=>18,'align'=>'L'),
			'name'=>array('label'=>Yii::t('staff','Name'),'width'=>30,'align'=>'L'),
			'dept_name'=>array('label'=>Yii::t('summary','dept name'),'width'=>30,'align'=>'L'),
			'entry_month'=>array('label'=>Yii::t('summary','entry month'),'width'=>30,'align'=>'L'),
			'amt'=>array('label'=>Yii::t('service','Paid Amt'),'width'=>18,'align'=>'L'),
		);
	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
        $city_allow = $this->criteria->city;
        $startDay = isset($this->criteria->start_dt)?date("Y/m/d",strtotime($this->criteria->start_dt)):date("Y/m/d");
        $endDay = isset($this->criteria->end_dt)?date("Y/m/d",strtotime($this->criteria->end_dt)):date("Y/m/d");
        $citySql = " and b.Text in ({$city_allow})";
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.Text,a.Fee,a.TermCount,Staff01,Staff02,Staff03")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and b.Text not in ('ZY') and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->order("b.Text")
            ->queryAll();
        $UStaffCodeList=array();
        $staffStrList = array("Staff01","Staff02","Staff03");
        $list = array();
		if ($rows) {
			foreach ($rows as $row) {
                $city = $row["Text"];
                $money = empty($row["TermCount"])?0:floatval($row["Fee"])/floatval($row["TermCount"]);

                $staffCount = 1;
                $staffCount+= empty($row["Staff02"])?0:1;
                $staffCount+= empty($row["Staff03"])?0:1;
                $money = $money/$staffCount;//如果多人，需要平分金額
                $money = round($money,2);
                foreach ($staffStrList as $staffStr){
                    $staff = $row[$staffStr];//员工编号
                    if(!empty($staff)){
                        if(!key_exists($staff,$list)){
                            $UStaffCodeList[$staff]="'{$staff}'";
                            $list[$staff]=array(
                                "city_code"=>$city,//城市编号
                                "staff"=>$staff,//员工
                                "area"=>"",//区域(U系统)
                                "u_city"=>$city,//城市(U系统)
                                "u_city_name"=>"",//城市(U系统)
                                "city"=>"",//城市(LBS系统)
                                "name"=>"",//员工名称
                                "dept_name"=>"",//员工名称
                                "entry_month"=>"",//员工名称
                                "amt"=>0,//服务金额
                            );
                        }
                        $list[$staff]["amt"]+=$money;
                    }
                }
			}
		}
        $userList = $this->getUserList($UStaffCodeList,$endDay);
        $cityList = self::getCityList($city_allow);
        $conditionList = empty($this->condition)?array(1,2,3):array($this->condition);
		foreach ($list as &$item){//由于数据太多，尝试优化
            $user = self::getUserListForCode($item["staff"],$userList);
            $entryMonth = empty($user["entry_month"])?0:$user["entry_month"];
            //年资范围
            $bool =$entryMonth>=$this->seniority_min&&$entryMonth<=$this->seniority_max;
            if(in_array($user["level_type"],$conditionList)&&$bool){ //职位且年资范围
                $uCity = self::getCityListForCode($item["city_code"],$cityList);
                $item["area"] = $uCity["region_name"];
                $item["u_city_name"] = $uCity["city_name"];
                $item["city"] = $user["city"];
                $item["dept_name"] = $user["dept_name"];
                $item["entry_month"] = $user["entry_month"];
                $item["name"] = $user["name"]." ({$user["code"]})".($user["staff_status"]==-1?Yii::t("summary"," - Leave"):"");
            }else{
                unset($list[$item["staff"]]);
            }
        }
        $this->data = $list;
		return true;
	}

    public static function getUserListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("level_type"=>3,"staff_status"=>0,"code"=>$code,"name"=>"","city"=>"","dept_name"=>"","entry_month"=>"");
		}
	}

    public static function getCityListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("code"=>$code,"city_name"=>"","region_name"=>"");
		}
	}

	public function getUserList($UStaffCodeList,$endDate){
        $suffix = Yii::app()->params['envSuffix'];
        if(!empty($UStaffCodeList)){
            $codeStr = implode(",",$UStaffCodeList);
            $whereSql = "a.code in ({$codeStr})";
        }else{
            $whereSql = "a.code=0";
        }
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
                //1:技术员 2：技术主管 3：其它
                $row["level_type"]=empty($row["level_type"])?3:$row["level_type"];
        	    $entryMonth = strtotime($endDate)-strtotime($row["entry_time"]);
                $entryMonth/=24*60*60*30;
                $entryMonth = round($entryMonth);
                //在职月份
                $row["entry_month"] = $entryMonth;
                $list[$row['code']]=$row;
			}
		}
        return $list;
	}

	public static function getCityList($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.code,b.name as city_name,f.name as region_name")
            ->from("security{$suffix}.sec_city b")
            ->leftJoin("security{$suffix}.sec_city f","b.region = f.code")
            ->where("b.code in ({$city_allow})")
            ->order("b.code")
            ->queryAll();
        $list = array();
        if($rows){
        	foreach ($rows as $row){
                $list[$row['code']]=$row;
			}
		}
        return $list;
	}

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName();
	}
}
?>
