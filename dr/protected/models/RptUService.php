<?php
class RptUService extends ReportData2 {
	public function fields() {
		return array(
			'area'=>array('label'=>Yii::t('report','Area'),'width'=>18,'align'=>'L'),
			'u_city_name'=>array('label'=>Yii::t('report','City'),'width'=>18,'align'=>'L'),
			'name'=>array('label'=>Yii::t('staff','Name'),'width'=>30,'align'=>'L'),
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
        $userList = self::getUserList($city_allow);
        $cityList = self::getCityList($city_allow);
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
					$user = self::getUserListForCode($staff,$userList);
					$uCity = self::getCityListForCode($city,$cityList);
                    if(!empty($staff)){
                        if(!key_exists($staff,$list)){
                            $list[$staff]=array(
                                "area"=>$uCity["region_name"],//区域(U系统)
                                "u_city"=>$city,//城市(U系统)
                                "u_city_name"=>$uCity["city_name"],//城市(U系统)
                                "city"=>$user["city"],//城市(LBS系统)
                                "name"=>$user["name"]." ({$user["code"]})",//员工名称
                                "amt"=>0,//服务金额
                            );
                        }
                        $list[$staff]["amt"]+=$money;
                    }
                }
			}
		}
        $this->data = $list;
		return true;
	}

    public static function getUserListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("code"=>$code,"name"=>"","city"=>"","city_name"=>"","region_name"=>"");
		}
	}

    public static function getCityListForCode($code,$list){
		if(key_exists($code,$list)){
			return $list[$code];
		}else{
			return array("code"=>$code,"city_name"=>"","region_name"=>"");
		}
	}

	public static function getUserList($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.code,a.name,a.city,b.name as city_name,f.name as region_name")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("security{$suffix}.sec_city b","a.city = b.code")
            ->leftJoin("security{$suffix}.sec_city f","b.region = f.code")
            ->where("a.city in ({$city_allow})")
            ->order("a.city")
            ->queryAll();
        $list = array();
        if($rows){
        	foreach ($rows as $row){
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
