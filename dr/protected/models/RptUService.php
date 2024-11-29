<?php
class RptUService extends ReportData2 {
    public $condition="";//筛选条件
    public $seniority_min=0;//年资（最小）
    public $seniority_max=9999;//年资（最大）
    public $staff_type=0;//员工类型 0：全部 1：专职 2：其他

    public $u_load_data=array();//查询时长数组
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
        $city = $this->criteria->city;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $startDay = isset($this->criteria->start_dt)?date("Y/m/d",strtotime($this->criteria->start_dt)):date("Y/m/d");
        $endDay = isset($this->criteria->end_dt)?date("Y/m/d",strtotime($this->criteria->end_dt)):date("Y/m/d");
        $this->data=array();
        $list = array();
        //$rows = CountSearch::getTechnicianMoney($startDay,$endDay,$city_allow);
        $this->u_load_data['u_load_start'] = time();
        $rows = CountSearch::getTechnicianMoney($startDay,$endDay);//由于派单系统不做城市判断，所以查询所有城市
        $this->u_load_data['u_load_end'] = time();
        $UStaffCodeList = array_column($rows,"staff");
        $userList = $this->getUserList($UStaffCodeList,$endDay);
        $cityList = self::getCityList($city_allow);
        $conditionList = empty($this->condition)?array(1,2,3,4,5):$this->condition;
        $conditionList = is_array($conditionList)?$conditionList:array($conditionList);
		foreach ($rows as $item){//由于数据太多，尝试优化
            $staff_code = isset($item["staff"])?$item["staff"]:"none";
            $user = self::getUserListForCode($staff_code,$userList);
            $staff_type = key_exists("table_type",$user)?$user["table_type"]:1;
            $lbs_city = key_exists("city",$user)?$user["city"]:"none";
            $u_city = isset($item["city_code"])?$item["city_code"]:"none";
            $u_city = SummaryForm::resetCity($u_city);
            if (strpos($city_allow,"'{$lbs_city}'")===false){
                continue;//由于派单系统不做城市判断，所以查询所有城市,由LBS删除多余城市
            }
            $bool = false;
            if(empty($this->staff_type)){//全部
                $bool = true;//允许
            }else if($this->staff_type==1&&$staff_type==1){//专职
                $bool = true;//允许
            }else if($this->staff_type==3&&$staff_type!=1){//其它
                $bool = true;//允许
            }
            if(!$bool){
                continue;
            }
            $amt = isset($item["amt"])&&is_numeric($item["amt"])?floatval($item["amt"]):0;
            $temp = array(
                "city_code"=>$lbs_city,//城市编号
                "staff"=>$staff_code,//员工
                "area"=>"",//区域(U系统)
                "u_city"=>$u_city,//城市(U系统)
                "u_city_name"=>"",//城市(U系统)
                "city"=>"",//城市(LBS系统)
                "name"=>"",//员工名称
                "dept_name"=>"",//员工名称
                "entry_month"=>"",//员工名称
                "amt"=>$amt,//服务金额
                "staff_type"=>self::getStaffTypeStrForType($staff_type,true),//员工类型
            );
            $cityNameList = self::getCityListForCode($lbs_city,$cityList);
            //员工在KA城市且是技术主管，强制转换成KA技术主管
            $user["level_type"]= $cityNameList["ka_bool"]==1&&$user["level_type"]==2?5:$user["level_type"];
            $entryMonth = empty($user["entry_month"])?0:$user["entry_month"];
            //年资范围
            $bool =$entryMonth>=$this->seniority_min&&$entryMonth<=$this->seniority_max;
            if(in_array($user["level_type"],$conditionList)&&$bool){ //职位且年资范围
                if(!key_exists($lbs_city,$list)){
                    $list[$lbs_city]=array();
                }
                $temp["area"] = $cityNameList["region_name"];
                $temp["u_city_name"] = $cityNameList["city_name"];
                $temp["city"] = $user["city"];
                $temp["dept_name"] = $user["dept_name"];
                $temp["entry_month"] = $user["entry_month"];
                $temp["name"] = $user["name"]." ({$user["code"]})".($user["staff_status"]==-1?Yii::t("summary"," - Leave"):"");

                $list[$lbs_city][$staff_code] = $temp;
            }
        }


        if(!empty($list)){
            foreach ($list as $row){
                if(!empty($row)){
                    foreach ($row as $key=>$item){
                        $this->data["".$key] = $item;
                    }
                }
            }
        }
        //$this->data = $list;
		return true;
	}

    public static function getUserListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("level_type"=>3,"table_type"=>1,"staff_status"=>0,"code"=>$code,"name"=>"","city"=>"","dept_name"=>"","entry_month"=>"");
		}
	}

    public static function getCityListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("code"=>$code,"ka_bool"=>0,"city_name"=>"","region_name"=>"");
		}
	}
    public static function getStaffTypeStrForType($type=1,$bool=false){
        $list = array(
            1=>"专职",
            2=>"兼职",
            3=>"外聘",
            4=>"业务承揽",
            5=>"外包商",
            6=>"临时账号"
        );
        if($bool){
            $type = "".$type;
            if(key_exists($type,$list)){
                return $list[$type];
            }else{
                return $type;
            }
        }else{
            return $list;
        }
	}

	public function getUserList($UStaffCodeList,$endDate){
        $suffix = Yii::app()->params['envSuffix'];
        if(!empty($UStaffCodeList)){
            $codeStr = implode("','",$UStaffCodeList);
            $whereSql = "a.code in ('{$codeStr}')";
        }else{
            $whereSql = "a.code=0";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.table_type,a.staff_status,a.entry_time,g.name as dept_name,a.name,a.city,
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
        $rows = Yii::app()->db->createCommand()
            ->select("b.code,b.ka_bool,b.name as city_name,f.name as region_name")
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
