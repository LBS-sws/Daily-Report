<?php

class SalesAnalysisForm extends CFormModel
{
	/* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
    public $last_year;

    public $start_date;
    public $end_date;

    public $data=array();
    public $twoDate=array();
    public $threeDate=array();
    public $fourDate=array();

	public $th_sum=0;//所有th的个数

    public $downJsonText='';
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'search_date'=>Yii::t('summary','search date'),
            'week_start'=>Yii::t('summary','now week'),
            'last_week_start'=>Yii::t('summary','last week'),
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('search_date','safe'),
            array('search_date','required'),
            array('search_date','validateDate'),
        );
    }

    public function validateDate($attribute="", $params="") {
        if(!empty($this->search_date)) {
            $timer = strtotime($this->search_date);
            $this->search_year = date("Y", $timer);
            $this->search_month = date("n", $timer);

            $this->start_date = $this->search_year . "/01/01";
            $this->end_date = date("Y/m/d", $timer);

            $this->month_day = date("d", $timer);
            $this->last_year = $this->search_year - 1;
        }
    }

    public function setCriteria($criteria){
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'search_date'=>$this->search_date
        );
    }

    //获取销售拜访的金额字段
    public static function getVisitAmtToSQL() {
        return array("svc_A7","svc_B6","svc_C7","svc_D6","svc_H6","svc_E7","svc_F4","svc_G3");
    }

    //获取什么是签单的查询字符串
    public static function getDealString($field) {
        $suffix = Yii::app()->params['envSuffix'];
        $rtn = '';
        $sql = "select id from sales{$suffix}.sal_visit_obj where rpt_type='DEAL'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $rtn .= ($rtn=='' ? '' : ' or ').$field." like '%\"".$row['id']."\"%'";
        }
        return ($rtn=='' ? "$field='0'" : $rtn);
    }

    public static function getCityListAndRegion($city_allow){
        return CitySetForm::getCitySetList($city_allow);
    }

    //暂时不使用
    public static function getSalesForHrNew($city_allow,$endDate="",$startDate=""){
        $endDate = empty($endDate)?date("Y/m/d"):$endDate;
        $suffix = Yii::app()->params['envSuffix'];
        $endDate = empty($endDate)?date("Y/m/d"):date("Y/m/d",strtotime($endDate));
        $startDate = empty($startDate)?date("Y/01/01",strtotime($endDate)):$startDate;
        $list=array('staff'=>array(),'user'=>array());
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.code,a.name,a.city,d.user_id,a.entry_time,a.staff_status,a.leave_time,a.office_id,g.name as office_name,
            dept.name as dept_name,
            CONCAT('') as city_name,CONCAT('') as region,CONCAT('') as region_name")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr{$suffix}.hr_office g","a.office_id=g.id")
            ->leftJoin("hr{$suffix}.hr_dept dept","a.position=dept.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$endDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/31')>='{$startDate}')
             ) AND a.city in ({$city_allow})"
            )->order("a.city desc,a.office_id asc,a.id asc")->queryAll();
        return $rows;
    }

    public static function getSalesForHr($city_allow,$endDate="",$startDate=""){
        $endDate = empty($endDate)?date("Y/m/d"):$endDate;
        $suffix = Yii::app()->params['envSuffix'];
        $endDate = empty($endDate)?date("Y/m/d"):date("Y/m/d",strtotime($endDate));
        $startDate = empty($startDate)?$endDate:$startDate;
        $list=array('staff'=>array(),'user'=>array());
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.code,a.name,a.city,d.user_id,a.entry_time,a.staff_status,a.leave_time,a.office_id,g.name as office_name,
            dept.name as dept_name,
            CONCAT('') as city_name,CONCAT('') as region,CONCAT('') as region_name")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr{$suffix}.hr_office g","a.office_id=g.id")
            ->leftJoin("hr{$suffix}.hr_dept dept","a.position=dept.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$startDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/31')>='{$endDate}')
             ) AND a.city in ({$city_allow})"
            )->order("a.city desc,a.office_id asc,a.id asc")->queryAll();
        return $rows;
    }

    protected function groupFTEForStaffAndData($city_allow){
        if($this->search_year==2023){
            $startMonth = 3;
        }else{
            $startMonth = 1;
        }
        $data=array();
        //老销售
        $data["list"][0]=array();
        $data["list"][0]["title"]=Yii::t("summary","old now sales");
        for($j=$startMonth;$j<=$this->search_month;$j++){
            $key = $j<10?"0".$j:$j;
            $key = $this->search_year."/".$key;
            $data["list"][0][$key]=0;//人数
            $data["list"][0]["rate_".$key]=0;//对比
        }
        //新入职销售
        for($i=$startMonth;$i<=$this->search_month;$i++){
            $data["list"][$i]=array();
            $data["list"][$i]["title"]=$i.Yii::t("summary"," month now sales");
            for($j=$startMonth;$j<=$this->search_month;$j++){
                $key = $j<10?"0".$j:$j;
                $key = $this->search_year."/".$key;
                $data["list"][$i][$key]=0;//人数
                $data["list"][$i]["rate_".$key]=0;//对比
            }
        }

        //计算销售人数
        $key = $startMonth;
        $key = $key<10?"0".$key:$key;
        $endDate = $this->search_year."/".$key."/01";
        $startDate = $this->search_date;
        $staffRows = self::getSalesForHr($city_allow,$endDate,$startDate);
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $leave_timer = $staffRow["staff_status"]==-1?date("Y/m",strtotime($staffRow["leave_time"])):"2222/22";
                $entry_timer = date("Y/m",strtotime($staffRow["entry_time"]));

                for($i=$startMonth;$i<=$this->search_month;$i++){
                    $key = $i<10?"0".$i:$i;
                    $key = $this->search_year."/".$key;
                    if($leave_timer<$key||$entry_timer>$key){//已離職 或者 未入职
                        continue;//跳出循環
                    }
                    if($entry_timer<$this->search_year."/0{$startMonth}"){
                        $month = 0;//老员工
                    }else{
                        $month = date("n",strtotime($staffRow["entry_time"]));//某月新入职员工
                    }
                    $data["list"][$month][$key]++;
                }
            }
        }
        return $data;
    }

    //前一年的平均值
    protected function getLastYearData($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $amtSql = self::getVisitAmtToSQL();
        $dealSQL = self::getDealString("b.visit_obj");//签单的sql
        $amtSql = implode("','",$amtSql);
        //注意：本城市
        $rows = Yii::app()->db->createCommand()
            ->select("b.username,sum(a.field_value) as last_amt")
            ->from("sales{$suffix}.sal_visit_info a")
            ->leftJoin("sales{$suffix}.sal_visit b","a.visit_id=b.id")
            ->where("b.city in ({$city_allow}) and ({$dealSQL}) and a.field_id in ('{$amtSql}') and 
            DATE_FORMAT(b.visit_dt,'%Y')='{$this->last_year}'"
            )->group("b.username,DATE_FORMAT(b.visit_dt,'%Y/%m')")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["username"],$list)){
                    $list[$row["username"]]=array("last_amt"=>0,"count_month"=>0);
                }
                $list[$row["username"]]["last_amt"]+=round($row["last_amt"],2);
                $list[$row["username"]]["count_month"]++;
            }
        }
        return $list;
    }

    //本年度的数据
    public static function getNowYearData($start_date,$end_date,$city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $amtSql = self::getVisitAmtToSQL();
        $dealSQL = self::getDealString("b.visit_obj");//签单的sql
        $amtSql = implode("','",$amtSql);
        //注意：本城市
        $rows = Yii::app()->db->createCommand()
            ->select("b.username,sum(a.field_value) as now_amt,DATE_FORMAT(b.visit_dt,'%Y/%m') as month")
            ->from("sales{$suffix}.sal_visit_info a")
            ->leftJoin("sales{$suffix}.sal_visit b","a.visit_id=b.id")
            ->where("b.city in ({$city_allow}) and ({$dealSQL}) and a.field_id in ('{$amtSql}') and 
            b.visit_dt BETWEEN '{$start_date}' and '{$end_date}'"
            )->group("b.username,DATE_FORMAT(b.visit_dt,'%Y/%m')")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["username"]][$row["month"]] = round($row["now_amt"],2);
            }
        }
        return $list;
    }

    protected function setStaffRowForNowData(&$staffRow,$nowData){
        $username = $staffRow["user_id"];
        $timer = strtotime($staffRow["entry_time"]);
        $leaveDate = $staffRow["staff_status"]==-1?date("Y/m",strtotime($staffRow["leave_time"])):"9999/12";
        $entry_ym = date("Y/m",$timer);
        $entry_ym_prev = date("Y/m",strtotime("{$entry_ym}/01 - 1 month"));
        $lastSum = 0;
        $lastCount = 0;
        $nowSum = 0;
        $nowCount = 0;
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        for($i=0;$i<=24;$i++){
            $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
            if($dateTimer<=strtotime($this->end_date)){
                $yearMonth = date("Y/m",$dateTimer);
                $value=0;
                if(key_exists($username,$nowData)){
                    $value = key_exists($yearMonth,$nowData[$username])?$nowData[$username][$yearMonth]:0;
                }
                if($entry_ym>$yearMonth){//未入职显示空
                    if($entry_ym_prev==$yearMonth) {//入职前一个月
                        $value="；";
                    }else{
                        $value="";
                    }
                }elseif ($entry_ym==$yearMonth&&empty($value)){//入职当月且没有签单金额
                    $value="-";
                }
                if($leaveDate<$yearMonth){//已离职
                    $value="";
                }

                if($value!==""&&is_numeric($value)){//有效数据
                    if(date("Y",$dateTimer)==date("Y",strtotime($this->end_date))){
                        $nowCount++;
                        $nowSum+=$value;
                    }else{
                        $lastCount++;
                        $lastSum+=$value;
                    }
                }
                $staffRow[$yearMonth]=$value;
            }else{
                break;
            }
        }
        $staffRow["now_average"]=empty($nowCount)?0:round($nowSum/$nowCount);
        $staffRow["last_average"]=empty($lastCount)?0:round($lastSum/$lastCount);
    }

    protected function groupAreaForStaffAndData($staffRows,$cityList,$nowData){
        $startYear = date("Y",strtotime($this->start_date));
        $startYear-=1;
        if($startYear==2023){
            $nowStaffDate = "{$startYear}/03/01";
        }else{
            $nowStaffDate = "{$startYear}/01/01";
        }
        $data = array();
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $entry_timer = strtotime($staffRow["entry_time"]);
                $city = $staffRow['city'];
                $region = key_exists($city,$cityList)?$cityList[$city]["region_name"]:"none";
                if(!key_exists($region,$data)){
                    $monthDataList=array(
                        0=>array("name"=>Yii::t("summary","now sales"),"fte_num"=>0,"region"=>$region,'user_name'=>array())
                    );
                    for($i=0;$i<=24;$i++){
                        $dateTimer = empty($i)?strtotime($nowStaffDate):strtotime($nowStaffDate." + {$i} months");
                        $yearMonth = date("Y/m",$dateTimer);
                        if($dateTimer<=strtotime($this->end_date)){
                            $monthDataList[$yearMonth]=array(
                                "name"=>date("Y年m",$dateTimer).Yii::t("summary"," month now sales"),"fte_num"=>0,"region"=>$region,'user_name'=>array()
                            );
                        }else{
                            break;
                        }
                    }
                    $data[$region]=array(
                        "region"=>$region,
                        "region_name"=>key_exists($city,$cityList)?$cityList[$city]["region_name"]:"none",
                        "list"=>$monthDataList,
                    );
                }
                if($entry_timer<strtotime($nowStaffDate)){
                    $month = 0;//老员工
                }else{
                    $month = date("Y/m",$entry_timer);//某月新入职员工
                    if(!key_exists($month,$data[$region]["list"])){
                        $data[$region]["list"][$month]=array(
                            "name"=>$month.Yii::t("summary"," month now sales"),"fte_num"=>0,"region"=>$region,'user_name'=>array()
                        );
                    }
                }
                $data[$region]["list"][$month]["fte_num"]++;
                $data[$region]["list"][$month]["user_name"][]=$staffRow["user_id"];
                $this->setMonthAmt($data[$region]["list"][$month],$nowData,$staffRow["user_id"]);
            }
        }
        return $data;
    }

    //将员工的金额汇总
    protected function setMonthAmt(&$data,$nowData,$username){
        $list = array();
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        $sum=0;
        $count=0;
        if(key_exists($username,$nowData)){
            $list = $nowData[$username];
        }
        for($i=0;$i<=24;$i++){
            $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
            if($dateTimer<=strtotime($this->end_date)){
                $key = date("Y/m",$dateTimer);
                if(!key_exists($key,$data)){
                    $data[$key]=0;
                }
                $data[$key]+=key_exists($key,$list)?$list[$key]:0;
                $sum+=$data[$key];
                $count++;
            }else{
                break;
            }
        }
        $data["now_average"]=empty($count)?0:round($sum/$count);
    }

    protected function groupCityForStaffAndData($staffRows,$cityList,$nowData,$lifelineList){
        $yearMonth = $this->search_year."/".($this->search_month < 10 ? "0{$this->search_month}" : $this->search_month);
        $data = array();
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $entry_time = date("Y/m",strtotime($staffRow["entry_time"]));
                $city = $staffRow['city'];
                $city_name = key_exists($city,$cityList)?$cityList[$city]["city_name"]:"none";
                if(!key_exists($city,$data)){
                    $data[$city]=array(
                        "city"=>$city,
                        "city_name"=>$city_name,
                        "list"=>array("city_name"=>$city_name,"now_num"=>0,"max_num"=>0,"min_num"=>0,"new_num"=>0),
                    );
                }
                $data[$city]["list"]["now_num"]++;//在职人数
                if($entry_time<$yearMonth){//在职人数
                    //达标人数
                    $amt_sum = key_exists($staffRow["user_id"],$nowData)?$nowData[$staffRow["user_id"]]:array();
                    $amt_sum = key_exists($yearMonth,$amt_sum)?$amt_sum[$yearMonth]:0;
                    $tar_num = key_exists($city,$lifelineList)?$lifelineList[$city]:80000;
                    if($amt_sum>$tar_num){
                        $data[$city]["list"]["max_num"]++;
                    }else{
                        $data[$city]["list"]["min_num"]++;
                    }
                }else if($entry_time==$yearMonth){//新招人数
                    $data[$city]["list"]["new_num"]++;
                }
            }
        }
        return $data;
    }

    public static function getCitySetForCityAllow($city_allow){
        $rows = Yii::app()->db->createCommand()
            ->select("code")->from("swo_city_set")
            ->where("code in ({$city_allow}) or region_code in ({$city_allow})")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = "'{$row["code"]}'";
                if (strpos($city_allow,$city)===false){
                    $city_allow.=",".$city;
                }
            }
        }
        return $city_allow;
    }

    public function retrieveData($city="") {
        $data = array();
        if(empty($city)){
            $city_allow = Yii::app()->user->city_allow();
            $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        }else{
            $city_allow = "'{$city}'";
        }
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        $lifelineList = LifelineForm::getLifeLineList($city_allow,$this->search_date);//生命线
        $staffRows = $this->getSalesForHr($city_allow,$this->search_date);//员工信息
        //$lastData = $this->getLastYearData($city_allow);//前一年的平均值
        $nowData = $this->getNowYearData($this->start_date,$this->end_date,$city_allow);//本年度的数据
        $twoYearData = $this->getNowYearData($twoYear,$this->end_date,$city_allow);//两年内的数据
        $cityList = self::getCityListAndRegion($city_allow);//城市信息
        $this->twoDate = $this->groupAreaForStaffAndData($staffRows,$cityList,$twoYearData);
        $this->threeDate = $this->groupCityForStaffAndData($staffRows,$cityList,$nowData,$lifelineList);
        $this->fourDate = $this->groupFTEForStaffAndData($city_allow);
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $username = $staffRow["user_id"];
                $city = $staffRow["city"];
                $name_label = $staffRow["name"]." ({$staffRow["code"]})";
                $name_label.= empty($staffRow["staff_status"])?"":"（已离职）";
                $staffRow["employee_name"] = $name_label;
                $staffRow["office_name"] = empty($staffRow["office_id"])?Yii::t("summary","local office"):$staffRow["office_name"];
                if(key_exists($city,$cityList)){
                    $staffRow["region"] = $cityList[$city]["region"];
                    $staffRow["city_name"] = $cityList[$city]["city_name"];
                    $staffRow["region_name"] = $cityList[$city]["region_name"];
                }
/*                if(key_exists($username,$lastData)){//上一年平均生意额
                    $staffRow["last_average"] = round($lastData[$username]["last_amt"]/$lastData[$username]["count_month"]);
                }*/
                //生命线
                $staffRow["life_num"] = LifelineForm::getLineValueForC_O($lifelineList,$city,$staffRow["office_id"]);
                $this->setStaffRowForNowData($staffRow,$twoYearData);
                $region = empty($staffRow["region_name"])?"null":$staffRow["region_name"];
                if (!key_exists($region,$data)){
                    $data[$region]=array("list"=>array(),"region_code"=>$region,"region_name"=>$staffRow["region_name"]);
                }
                $data[$region]["list"][]=$staffRow;
            }
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['salesAnalysis_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            $sum=0;
            $count=0;
            for ($i=0;$i<=12;$i++) {
                $yearMonth = empty($i)?$this->search_year."/01":date("Y/m",strtotime($this->search_year."/01/01 + {$i} months"));
                if($yearMonth<=date("Y/m",strtotime($this->end_date))){
                    $sum+=$list[$yearMonth];
                    $count++;
                }else{
                    break;
                }
            }
            $list["last_average"]="-";
            $list["life_num"]="-";
            $list["now_average"]=empty($count)?0:round($sum/$count);
            //计算上一个年度
            $sum=0;
            $count=0;
            for ($i=0;$i<=12;$i++) {
                $yearMonth = empty($i)?($this->search_year-1)."/01":date("Y/m",strtotime(($this->search_year-1)."/01/01 + {$i} months"));
                if($yearMonth<=date("Y/m",strtotime(($this->search_year-1)."/12/01"))){
                    $sum+=$list[$yearMonth];
                    $count++;
                }else{
                    break;
                }
            }
            $list["last_average"]=empty($count)?0:round($sum/$count);
        }
    }

    //顯示提成表的表格內容
    public function salesAnalysisHtml(){
        $html= '<table id="salesAnalysis" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    public function getTopArr(){
        $lastMonthArr = array();
        for($i=1;$i<=12;$i++){
            $lastMonthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"));
        $topList=array(
            array("name"=>Yii::t("summary","Employee Name"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//姓名
            array("name"=>Yii::t("summary","dept name"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//职位
            array("name"=>Yii::t("summary","City"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//城市
            array("name"=>Yii::t("summary","staff office"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//辦事處
            array("name"=>Yii::t("summary","Reference"),"background"=>"#000000","color"=>"#FFFFFF",
                "colspan"=>array(
                    array("name"=>$this->last_year.Yii::t("summary"," year average")),//参考平均值
                    array("name"=>$this->search_year.Yii::t("summary"," life num")),//参考生命线
                )
            ),//参考
            array("name"=>($this->search_year-1).Yii::t("summary"," year sales"),"background"=>"#00B0F0","color"=>"#FFFFFF",
                "colspan"=>$lastMonthArr
            ),//销售金额
            array("name"=>$this->search_year.Yii::t("summary"," year sales"),"background"=>"#00B0F0","color"=>"#FFFFFF",
                "colspan"=>$monthArr
            )//销售金额
        );

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $this->th_sum = 0;
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=$list["name"];
            $colList=key_exists("colspan",$list)?$list['colspan']:array();
            $style = "";
            $colNum=0;
            if(key_exists("background",$list)){
                $style.="background:{$list["background"]};";
            }
            if(key_exists("color",$list)){
                $style.="color:{$list["color"]};";
            }
            if(!empty($colList)){
                foreach ($colList as $col){
                    $colNum++;
                    $trTwo.="<th style='{$style}'><span>".$col["name"]."</span></th>";
                    $this->th_sum++;
                }
            }else{
                $this->th_sum++;
            }
            $colNum = empty($colNum)?1:$colNum;
            $trOne.="<th style='{$style}' colspan='{$colNum}'";
            if($colNum>1){
                $trOne.=" class='click-th'";
            }
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr><tr>{$trTwo}</tr>";
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            if($i==0){
                $width=115;
            }else{
                $width=70;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    protected function getDataAllKeyStr(){
        $bodyKey = array(
            "employee_name",
            "dept_name",
            "city_name",
            "office_name",
            "last_average",
            "life_num"
        );
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        for($i=0;$i<=11;$i++){
            $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
            $yearMonth = date("Y/m",$dateTimer);
            $bodyKey[]=$yearMonth;
        }
        for($i=0;$i<=12;$i++){
            $dateTimer = empty($i)?strtotime($this->search_year."/01/01"):strtotime($this->search_year."/01/01 + {$i} months");
            if($dateTimer<=strtotime($this->end_date)){
                $yearMonth = date("Y/m",$dateTimer);
                $bodyKey[]=$yearMonth;
            }else{
                break;
            }
        }
        $bodyKey[]="now_average";
        return $bodyKey;
    }

    //設置顏色
    public static function getTextColorForKeyStr($text,$keyStr,$num){
        $tdClass = "";
        if($text!==""&&(strpos($keyStr,'/')!==false||$keyStr!="now_average")&&is_numeric($text)){
            if($text>=$num){
                $tdClass="text-green";
            }else{
                $tdClass="text-danger";
            }
        }

        return $tdClass;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = array();//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array();//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $this->resetTdRow($cityList);
                        $html.="<tr>";
                        foreach ($bodyKey as $keyStr){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            if(!key_exists($keyStr,$allRow)){
                                $allRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$cityList['life_num']);
                            $this->downJsonText[$cityList['region']][$cityList['id']][$keyStr]=array("text"=>$text,"tdClass"=>$tdClass);
                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionList["region_code"];
                    $regionRow["city_name"]=$regionList["region_name"];
                    $regionRow["employee_name"]="";
                    $regionRow["dept_name"]="";
                    $regionRow["office_name"]="-";
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["employee_name"]="";
            $allRow["dept_name"]="";
            $allRow["region"]="allRow";
            $allRow["office_name"]="-";
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
            $this->downJsonText[$data['region']]["count"][$keyStr]=$text;
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function tableFooterHtml(){
        $html="<tfoot>";
        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        $html.="</tfoot>";
        return $html;
    }

    public function setAttrAll($model){
        $this->search_date = $model->search_date;
        $this->search_year = $model->search_year;
        $this->search_month = $model->search_month;
        $this->month_day = $model->month_day;
        $this->last_year = $model->last_year;
        $this->start_date = $model->start_date;
        $this->end_date = $model->end_date;
    }

    protected function getValueForArr($arr){
        if(is_array($arr)){
            return $arr["text"];
        }else{
            return $arr;
        }
    }

    protected function getTopArrNot(){
        $topList=array(
            array("name"=>Yii::t("summary","Month"),"rowspan"=>2),//月份
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Employee Name"),"rowspan"=>2),//销售名字
            array("name"=>Yii::t("summary","dept name"),"rowspan"=>2),//销售职位
            array("name"=>Yii::t("summary","staff office"),"rowspan"=>2),//办事处
            array("name"=>Yii::t("summary","life num"),"rowspan"=>2),//生命线
            array("name"=>Yii::t("summary","Not money"),"rowspan"=>2),//不达标金额
        );
        return $topList;
    }

    //下載(所有未达标数据)
    public function downExcelNot($excelData){
        $this->validateDate("","");
        $monthList=array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $monthList[]=$this->search_year."/{$month}";
        }
        $headList = $this->getTopArrNot();
        //所有数据
        $data = key_exists("one",$excelData)?json_decode($excelData["one"],true):array();
        $attr = array();
        foreach ($data as $regionKey=>$region) {
            foreach ($region as $keyStr => $list) {
                if($keyStr=="count"){ //合计不统计
                    continue;
                }
                $temp = array(
                    "month_num"=>"",//月份
                    "city_name"=>$this->getValueForArr($list["city_name"]),//城市
                    "employee_name"=>$this->getValueForArr($list["employee_name"]),//销售名字
                    "dept_name"=>$this->getValueForArr($list["dept_name"]),//销售职位
                    "office_name"=>$this->getValueForArr($list["office_name"]),//办事处
                    "life_num"=>$this->getValueForArr($list["life_num"]),//生命线
                    "money"=>"",//不达标金额
                );
                foreach ($monthList as $monthKey){
                    if(isset($list[$monthKey]["tdClass"])&&$list[$monthKey]["tdClass"]=="text-danger"){
                        $salesTemp = $temp;
                        $salesTemp["month_num"]=$monthKey;
                        $salesTemp["money"]=$this->getValueForArr($list[$monthKey]);
                        $attr[]=$salesTemp;
                    }
                }
            }
        }
        $excel = new DownSummary();
        $excel->colTwo=7;
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Staff Not")."（{$this->search_date}）");
        $titleTwo = date("Y/m/01/",strtotime($this->end_date))." ~ ".$this->end_date;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setUServiceData($attr);
        $excel->outExcel(Yii::t("summary","Capacity Staff Not"));
    }

    //下載
    public function downExcel($excelData){
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $twoModel = new SalesAnalysisAreaForm();
        $twoModel->setAttrAll($this);
        $threeModel = new SalesAnalysisCityForm();
        $threeModel->setAttrAll($this);
        $fourModel = new SalesAnalysisFTEForm();
        $fourModel->setAttrAll($this);
        $excel = new DownSummary();
        $excel->colTwo=4;
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Staff")."（{$this->search_date}）");
        $titleTwo = date("Y/m/01/",strtotime($this->end_date))." ~ ".$this->end_date;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        //第一页
        $excel->setSheetName(Yii::t("summary","Capacity Staff"));
        $excel->setSummaryHeader($headList,true);
        $data = key_exists("one",$excelData)?json_decode($excelData["one"],true):array();
        $excel->setSalesAnalysisData($data);
        //第二页
        $excel->colTwo=2;
        $sheetName = Yii::t("summary","Capacity Area");
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName."（{$this->search_date}）");
        $excel->outHeader(1);
        $data = key_exists("two",$excelData)?json_decode($excelData["two"],true):array();
        $excel->setSalesAreaData($data,$twoModel->getTopArr());
        //第三页
        $sheetName = Yii::t("summary","Capacity City");
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName."（{$this->search_date}）");
        $excel->outHeader(2);
        $excel->setUServiceHeader($threeModel->getTopArr());
        $data = key_exists("three",$excelData)?json_decode($excelData["three"],true):array();
        $excel->setUServiceData($data);
        //第四页
        $sheetName = Yii::t("summary","Capacity FTE");
        $excel->colTwo=1;
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName."（{$this->search_date}）");
        $excel->outHeader(3);
        $excel->setSummaryHeader($fourModel->getTopArr());
        $data = key_exists("four",$excelData)?json_decode($excelData["four"],true):array();
        $excel->setSummaryData($data);
        $excel->outExcel(Yii::t("app","Sales Analysis"));
    }

    public static function getMenuList($active=1){
        return array(
            1=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/view'),
                "label"=>Yii::t("summary","Capacity Staff"),
                "active"=>$active==1
            ),
            2=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/area'),
                "label"=>Yii::t("summary","Capacity Area"),
                "active"=>$active==2
            ),
            3=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/city'),
                "label"=>Yii::t("summary","Capacity City"),
                "active"=>$active==3
            ),
            4=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/fte'),
                "label"=>Yii::t("summary","Capacity FTE"),
                "active"=>$active==4
            ),
        );
    }
}