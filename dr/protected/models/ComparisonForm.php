<?php

class ComparisonForm extends CFormModel
{
	/* User Fields */
    public $search_start_date;//查詢開始日期
    public $search_end_date;//查詢結束日期
    public $search_type=3;//查詢類型 1：季度 2：月份 3：天
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $search_quarter;//查詢季度
	public $start_date;
	public $end_date;
    public $month_type;
	public $day_num=0;
	public $comparison_year;
    public $month_start_date;
    public $month_end_date;
    public $last_month_start_date;
    public $last_month_end_date;

    public static $con_list=array("one_gross","one_net","two_gross","two_net","three_gross","three_net");

    public $data=array();

	public $th_sum=2;//所有th的个数

    public $downJsonText='';
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date'),
            'day_num'=>Yii::t('summary','day num'),
            'search_type'=>Yii::t('summary','search type'),
            'search_start_date'=>Yii::t('summary','start date'),
            'search_end_date'=>Yii::t('summary','end date'),
            'search_year'=>Yii::t('summary','search year'),
            'search_quarter'=>Yii::t('summary','search quarter'),
            'search_month'=>Yii::t('summary','search month'),
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('search_type,search_start_date,search_end_date,search_year,search_quarter,search_month','safe'),
            array('search_type','required'),
            array('search_type','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        switch ($this->search_type){
            case 1://1：季度
                if(empty($this->search_year)||empty($this->search_quarter)){
                    $this->addError($attribute, "查询季度不能为空");
                }else{
                    $dateStr = $this->search_year."/".$this->search_quarter."/01";
                    $this->start_date = date("Y/m/01",strtotime($dateStr));
                    $this->end_date = date("Y/m/t",strtotime($dateStr." + 2 month"));
                    $this->month_type = $this->search_quarter;
                }
                break;
            case 2://2：月份
                if(empty($this->search_year)||empty($this->search_month)){
                    $this->addError($attribute, "查询月份不能为空");
                }else{
                    $dateTimer = strtotime($this->search_year."/".$this->search_month."/01");
                    $this->start_date = date("Y/m/01",$dateTimer);
                    $this->end_date = date("Y/m/t",$dateTimer);
                    $i = ceil($this->search_month/3);//向上取整
                    $this->month_type = 3*$i-2;
                }
                break;
            case 3://3：天
                if(empty($this->search_start_date)||empty($this->search_start_date)){
                    $this->addError($attribute, "查询日期不能为空");
                }else{
                    $startYear = date("Y",strtotime($this->search_start_date));
                    $endYear = date("Y",strtotime($this->search_end_date));
                    if($startYear!=$endYear){
                        $this->addError($attribute, "请把开始年份跟结束年份保持一致");
                    }else{
                        $this->search_month = date("n",strtotime($this->search_start_date));
                        $i = ceil($this->search_month/3);//向上取整
                        $this->month_type = 3*$i-2;
                        $this->search_year = $startYear;
                        $this->start_date = $this->search_start_date;
                        $this->end_date = $this->search_end_date;
                    }
                }
                break;
        }
    }

    public function setCriteria($criteria)
    {
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'search_year'=>$this->search_year,
            'search_month'=>$this->search_month,
            'search_type'=>$this->search_type,
            'search_quarter'=>$this->search_quarter,
            'search_start_date'=>$this->search_start_date,
            'search_end_date'=>$this->search_end_date
        );
    }

    public static function setDayNum($startDate,$endDate,&$dayNum){
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $timer = 0;
        if($endDate>=$startDate){
            $timer = ($endDate-$startDate)/86400;
            $timer++;//需要算上起始的一天
        }
        $dayNum = $timer;
    }

    public static function resetNetOrGross($num,$day,$type=3){
        switch ($type){
            case 1://季度
                return $num+$num*2*0.8;
            case 2://月度
                return $num;
            case 3://日期
                $num = ($num*12/365)*$day;
                $num = round($num,2);
                return $num;
        }
        return $type;
    }

    protected function computeDate(){
        $this->start_date = empty($this->start_date)?date("Y/01/01"):$this->start_date;
        $this->end_date = empty($this->end_date)?date("Y/m/t"):$this->end_date;
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));

        $this->last_month_start_date = CountSearch::computeLastMonth($this->start_date);
        $this->last_month_end_date = CountSearch::computeLastMonth($this->end_date);
    }

    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $suffix = Yii::app()->params['envSuffix'];
        $this->computeDate();
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $lastMonthStartDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthStartDate));
        $lastMonthEndDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthEndDate));
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getUServiceMoney($startDate,$endDate,$city_allow);
        //获取U系统的服务单数据(上月)
        $uServiceMoneyLast = CountSearch::getUServiceMoney($monthStartDate,$monthEndDate,$city_allow);
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //获取U系统的產品数据(上一年)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($startDate,$endDate,$city_allow);
        //服务新增（非一次性 和 一次性)(上一年)
        $lastServiceAddForNY = CountSearch::getServiceAddForNY($lastStartDate,$lastEndDate,$city_allow);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($startDate,$endDate,$city_allow);
        //终止服务、暂停服务(上一年)
        $lastServiceForST = CountSearch::getServiceForST($lastStartDate,$lastEndDate,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"R");
        //恢復服务(上一年)
        $lastServiceForR = CountSearch::getServiceForType($lastStartDate,$lastEndDate,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($startDate,$endDate,$city_allow);
        //更改服务(上一年)
        $lastServiceForA = CountSearch::getServiceForA($lastStartDate,$lastStartDate,$city_allow);
        //服务新增（一次性)(上月)
        $monthServiceAddForY = CountSearch::getServiceAddForY($monthStartDate,$monthEndDate,$city_allow);
        //服务新增（一次性)(上月)(上一年)
        $lastMonthServiceAddForY = CountSearch::getServiceAddForY($lastMonthStartDate,$lastMonthEndDate,$city_allow);
        //获取U系统的產品数据(上月)
        $monthUInvMoney = CountSearch::getUInvMoney($monthStartDate,$monthEndDate,$city_allow);
        //获取U系统的產品数据(上月)(上一年)
        $lastMonthUInvMoney = CountSearch::getUInvMoney($lastMonthStartDate,$lastMonthEndDate,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            self::setComparisonConfig($defMoreList,$this->comparison_year,$this->start_date,$city);
            $defMoreList["u_actual_money"]+=key_exists($city,$uServiceMoney)?$uServiceMoney[$city]:0;
            $defMoreList["u_sum"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            $defMoreList["u_actual_money"]+=$defMoreList["u_sum"];//生意额需要加上U系统产品金额
            $defMoreList["u_sum_last"]+=key_exists($city,$lastUInvMoney)?$lastUInvMoney[$city]["sum_money"]:0;
            if(key_exists($city,$serviceAddForNY)){
                $defMoreList["new_sum"]+=$serviceAddForNY[$city]["num_new"];
                $defMoreList["new_sum_n"]+=$serviceAddForNY[$city]["num_new_n"];
            }
            $defMoreList["new_sum_n"]+=$defMoreList["u_sum"];//一次性新增需要加上U系统产品金额
            if(key_exists($city,$lastServiceAddForNY)){
                $defMoreList["new_sum_last"]+=$lastServiceAddForNY[$city]["num_new"];
                $defMoreList["new_sum_n_last"]+=$lastServiceAddForNY[$city]["num_new_n"];
            }
            $defMoreList["new_sum_n_last"]+=$defMoreList["u_sum_last"];//一次性新增需要加上U系统产品金额
            //上月一次性服务+新增（产品）
            $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthServiceAddForY)?-1*$lastMonthServiceAddForY[$city]:0;
            $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthUInvMoney)?-1*$lastMonthUInvMoney[$city]["sum_money"]:0;
            $defMoreList["new_month_n"]+=key_exists($city,$monthServiceAddForY)?-1*$monthServiceAddForY[$city]:0;
            $defMoreList["new_month_n"]+=key_exists($city,$monthUInvMoney)?-1*$monthUInvMoney[$city]["sum_money"]:0;
            //上月生意额
            $defMoreList["last_u_actual"]+=key_exists($city,$uServiceMoneyLast)?$uServiceMoneyLast[$city]:0;
            $defMoreList["last_u_actual"]+=key_exists($city,$monthUInvMoney)?$monthUInvMoney[$city]["sum_money"]:0;
            //暂停、停止
            if(key_exists($city,$serviceForST)){
                $defMoreList["stop_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop"]:0;
                $defMoreList["pause_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_pause"]:0;
                $defMoreList["stopSumOnly"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_month"]:0;
            }
            if(key_exists($city,$lastServiceForST)){
                $defMoreList["stop_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_stop"]:0;
                $defMoreList["pause_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_pause"]:0;
            }
            //恢复
            $defMoreList["resume_sum_last"]+=key_exists($city,$lastServiceForR)?$lastServiceForR[$city]:0;
            $defMoreList["resume_sum"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;
            //更改
            $defMoreList["amend_sum_last"]+=key_exists($city,$lastServiceForA)?$lastServiceForA[$city]:0;
            $defMoreList["amend_sum"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['comparison_c01'] = $this->getCriteria();
        return true;
    }

    //設置滾動生意額及年初生意額
    public static function setComparisonConfig(&$arr,$year,$start_date,$city){
        $monthNum = date("n",strtotime($start_date));
        $year2024 = false;//2024年的年初生意额 = 滚动生意额
        if($year==2024){
            $year2024 = true;
            $monthList = array(
                1=>array("min"=>1,"max"=>3),
                4=>array("min"=>4,"max"=>7),
                7=>array("min"=>8,"max"=>12),
            );
        }else{
            $monthList = array(
                1=>array("min"=>1,"max"=>3),
                4=>array("min"=>4,"max"=>6),
                7=>array("min"=>7,"max"=>9),
                10=>array("min"=>10,"max"=>12),
            );
        }
        $month_type = 1;
        foreach ($monthList as $monthKey=>$monthItem){
            if($monthItem["min"]<=$monthNum&&$monthNum<=$monthItem["max"]){
                $month_type = $monthKey;
                break;
            }
        }
        foreach (self::$con_list as $itemStr){//初始化
            $arr[$itemStr]=0;
            $arr[$itemStr."_rate"]=0;
            $arr["start_".$itemStr]=0;
            $arr["start_".$itemStr."_rate"]=0;
        }
        $rowStart = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryRow();//查询目标金额
        if($rowStart){
            foreach (self::$con_list as $itemStr){//写入年初生意额
                $arr["start_".$itemStr]=empty($rowStart[$itemStr])?0:floatval($rowStart[$itemStr]);
            }
        }
        $setRow = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$year,":month_type"=>$month_type,":city"=>$city)
            )->queryRow();//查询目标金额
        if($setRow){
            foreach (self::$con_list as $itemStr){//写入滚动生意额
                $arr[$itemStr]=empty($setRow[$itemStr])?0:floatval($setRow[$itemStr]);
                if($year2024){//2024年的年初生意额 = 滚动生意额
                    $arr["start_".$itemStr] = $arr[$itemStr];
                }
            }
        }
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "last_u_actual"=>0,//服务生意额(上月)
            "u_actual_money"=>0,//服务生意额
            "u_sum_last"=>0,//U系统金额(上一年)
            "u_sum"=>0,//U系统金额
            "stopSumOnly"=>0,//本月停單金額（月）
            "monthStopRate"=>0,//月停單率
            "comStopRate"=>0,//综合停單率
            "new_sum_last"=>0,//新增(上一年)
            "new_sum"=>0,//新增
            "new_rate"=>0,//新增对比比例

            "new_sum_n_last"=>0,//一次性服务+新增（产品） (上一年)
            "new_sum_n"=>0,//一次性服务+新增（产品）
            "new_n_rate"=>0,//一次性服务+新增（产品）对比比例

            "new_month_n_last"=>0,//上月一次性服务+新增（产品） (上一年)
            "new_month_n"=>0,//上月一次性服务+新增（产品）
            "new_month_rate"=>0,//上月一次性服务+新增（产品）对比比例

            "stop_sum_last"=>0,//终止（上一年）
            "stop_sum"=>0,//终止
            "stop_rate"=>0,//终止对比比例

            "resume_sum_last"=>0,//恢复（上一年）
            "resume_sum"=>0,//恢复
            "resume_rate"=>0,//恢复对比比例

            "pause_sum_last"=>0,//暂停（上一年）
            "pause_sum"=>0,//暂停
            "pause_rate"=>0,//暂停对比比例

            "amend_sum_last"=>0,//更改（上一年）
            "amend_sum"=>0,//更改
            "amend_rate"=>0,//更改对比比例

            "net_sum_last"=>0,//总和（上一年）
            "net_sum"=>0,//总和
            "net_rate"=>0,//总和对比比例
        );
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        $newSum = $list["new_sum"]+$list["new_sum_n"];//所有新增总金额
        //$list["monthStopRate"] = $this->comparisonRate($list["stopSumOnly"],$list["u_actual_money"]);
        //2023年9月改版：月停单率 = (new_sum_n+new_month_n+stop_sum/12)/last_u_actual
        if($bool){
            $list["monthStopRate"] = "-";
            $list["comStopRate"] = "-";
        }else{
            $list["monthStopRate"] = $list["new_sum_n"]+$list["new_month_n"]+round($list["stop_sum"]/12,2);
            $list["monthStopRate"] = $this->comparisonRate($list["monthStopRate"],$list["last_u_actual"]);

            $list["comStopRate"] = $list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"]+$list["amend_sum"];
            $list["comStopRate"]/= 12;//
            $lastSum = $list["new_month_n"]+$list["last_u_actual"];
            $list["comStopRate"] = $this->comparisonRate($list["comStopRate"],$lastSum);
        }
        $list["net_sum"]=0;
        $list["net_sum"]+=$list["new_sum"]+$list["new_sum_n"]+$list["new_month_n"];
        $list["net_sum"]+=$list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"];
        $list["net_sum"]+=$list["amend_sum"];
        $list["net_sum_last"]=0;
        $list["net_sum_last"]+=$list["new_sum_last"]+$list["new_sum_n_last"]+$list["new_month_n_last"];
        $list["net_sum_last"]+=$list["stop_sum_last"]+$list["resume_sum_last"]+$list["pause_sum_last"];
        $list["net_sum_last"]+=$list["amend_sum_last"];
        $list["new_rate"] = $this->nowAndLastRate($list["new_sum"],$list["new_sum_last"],true);
        $list["new_n_rate"] = $this->nowAndLastRate($list["new_sum_n"],$list["new_sum_n_last"],true);
        $list["new_month_rate"] = $this->nowAndLastRate($list["new_month_n"],$list["new_month_n_last"],true);
        $list["stop_rate"] = $this->nowAndLastRate($list["stop_sum"],$list["stop_sum_last"],true);
        $list["resume_rate"] = $this->nowAndLastRate($list["resume_sum"],$list["resume_sum_last"],true);
        $list["pause_rate"] = $this->nowAndLastRate($list["pause_sum"],$list["pause_sum_last"],true);
        $list["amend_rate"] = $this->nowAndLastRate($list["amend_sum"],$list["amend_sum_last"],true);

        $list["net_rate"] = $this->nowAndLastRate($list["net_sum"],$list["net_sum_last"],true);

        if(SummaryForm::targetReadyBase()){
            $list["start_two_gross"] = $bool?$list["start_two_gross"]:ComparisonForm::resetNetOrGross($list["start_two_gross"],$this->day_num,$this->search_type);
            $list["start_two_net"] = $bool?$list["start_two_net"]:ComparisonForm::resetNetOrGross($list["start_two_net"],$this->day_num,$this->search_type);
            $list["start_two_gross_rate"] = $this->comparisonRate($newSum,$list["start_two_gross"]);
            $list["start_two_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_two_net"],"net");
            if(SummaryForm::grossAndNet()){
                $list["two_gross"] = $bool?$list["two_gross"]:ComparisonForm::resetNetOrGross($list["two_gross"],$this->day_num,$this->search_type);
                $list["two_net"] = $bool?$list["two_net"]:ComparisonForm::resetNetOrGross($list["two_net"],$this->day_num,$this->search_type);
                $list["two_gross_rate"] = $this->comparisonRate($newSum,$list["two_gross"]);
                $list["two_net_rate"] = $this->comparisonRate($list["net_sum"],$list["two_net"],"net");
            }
        }
        if(SummaryForm::targetReadyUpside()){
            $list["start_one_gross"] = $bool?$list["start_one_gross"]:ComparisonForm::resetNetOrGross($list["start_one_gross"],$this->day_num,$this->search_type);
            $list["start_one_net"] = $bool?$list["start_one_net"]:ComparisonForm::resetNetOrGross($list["start_one_net"],$this->day_num,$this->search_type);
            $list["start_one_gross_rate"] = $this->comparisonRate($newSum,$list["start_one_gross"]);
            $list["start_one_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_one_net"],"net");

            if(SummaryForm::grossAndNet()){
                $list["one_gross"] = $bool?$list["one_gross"]:ComparisonForm::resetNetOrGross($list["one_gross"],$this->day_num,$this->search_type);
                $list["one_net"] = $bool?$list["one_net"]:ComparisonForm::resetNetOrGross($list["one_net"],$this->day_num,$this->search_type);
                $list["one_gross_rate"] = $this->comparisonRate($newSum,$list["one_gross"]);
                $list["one_net_rate"] = $this->comparisonRate($list["net_sum"],$list["one_net"],"net");
            }
        }
        if(SummaryForm::targetReadyMinimum()){
            $list["start_three_gross"] = $bool?$list["start_three_gross"]:ComparisonForm::resetNetOrGross($list["start_three_gross"],$this->day_num,$this->search_type);
            $list["start_three_net"] = $bool?$list["start_three_net"]:ComparisonForm::resetNetOrGross($list["start_three_net"],$this->day_num,$this->search_type);
            $list["start_three_gross_rate"] = $this->comparisonRate($newSum,$list["start_three_gross"]);
            $list["start_three_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_three_net"],"net");

            if(SummaryForm::grossAndNet()){
                $list["three_gross"] = $bool?$list["three_gross"]:ComparisonForm::resetNetOrGross($list["three_gross"],$this->day_num,$this->search_type);
                $list["three_net"] = $bool?$list["three_net"]:ComparisonForm::resetNetOrGross($list["three_net"],$this->day_num,$this->search_type);
                $list["three_gross_rate"] = $this->comparisonRate($newSum,$list["three_gross"]);
                $list["three_net_rate"] = $this->comparisonRate($list["net_sum"],$list["three_net"],"net");
            }
        }
    }

    public static function nowAndLastRate($nowNum,$lastNum,$bool=false){
        if(empty($lastNum)){
            return 0;
        }else{
            $rate = $nowNum-$lastNum;
            $lastNum = $lastNum<0?$lastNum*-1:$lastNum;
            $rate = $rate/$lastNum;
            $rate = round($rate,3)*100;
            if($bool&&$rate>0){
                $rate=" +".$rate;
            }
            return $rate."%";
        }
    }

    public static function comparisonRate($num,$numLast,$str=''){
        if(empty($numLast)){
            if($str=="net"){
                if($num>0){
                    return Yii::t("summary","completed");
                }else{
                    return Yii::t("summary","incomplete");
                }
            }else{
                return 0;
            }
        }else{
            $rate = ($num/$numLast);
            $rate = round($rate,3)*100;
            return $rate."%";
        }
    }

    public static function showNum($num){
        $pre="";
        if (strpos($num," +")!==false){
            $pre=" +";
            $num = end(explode(" +",$num));
        }
        if (strpos($num,'%')!==false){
            $number = floatval($num);
            $number=sprintf("%.1f",$number)."%";
        }elseif (is_numeric($num)){
            $number = floatval($num);
            $number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $pre.$number;
    }

    //顯示提成表的表格內容
    public function comparisonHtml(){
        $html= '<table id="comparison" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $monthStr = "（{$this->month_start_date} ~ {$this->month_end_date}）";
        $lastMonthStr = "（".date("m/d",strtotime($this->last_month_start_date))." ~ ".date("m/d",strtotime($this->last_month_end_date))."）";
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Actual monthly amount"),"rowspan"=>2),//服务生意额
            array("name"=>Yii::t("summary","YTD New").$monthStr,"background"=>"#f7fd9d",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD新增
            array("name"=>Yii::t("summary","New(single) + New(INV)").$monthStr,"background"=>"#F7FD9D",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//一次性服务+新增（产品）
            array("name"=>Yii::t("summary","Last Month Single + New(INV)").$lastMonthStr,"background"=>"#F7FD9D",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//上月一次性服务+新增产品
            array("name"=>Yii::t("summary","YTD Stop").$monthStr,"exprName"=>$monthStr,"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                    array("name"=>Yii::t("summary","Month Stop Rate")),//月停单率
                    array("name"=>Yii::t("summary","Composite Stop Rate")),//月停单率
                )
            ),//YTD终止
            array("name"=>Yii::t("summary","YTD Resume").$monthStr,"exprName"=>$monthStr,"background"=>"#C5D9F1",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD恢复
            array("name"=>Yii::t("summary","YTD Pause").$monthStr,"exprName"=>$monthStr,"background"=>"#D9D9D9",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD暂停
            array("name"=>Yii::t("summary","YTD Amend").$monthStr,"exprName"=>$monthStr,"background"=>"#EBF1DE",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD更改
            array("name"=>Yii::t("summary","YTD Net").$monthStr,"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD Net
        );
        $colspan=array(
            array("name"=>Yii::t("summary","Start Gross")),//Start Gross
            array("name"=>Yii::t("summary","Start Net")),//Start Net
        );
        if(SummaryForm::grossAndNet()){
            $colspan[]=array("name"=>Yii::t("summary","Gross"));
            $colspan[]=array("name"=>Yii::t("summary","Net"));
        }
        if(SummaryForm::targetReadyUpside()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>$colspan
            );//年金额目标 (upside case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>$colspan
            );//目标完成度 (upside case)
        }
        if(SummaryForm::targetReadyBase()) {
            $topList[] = array("name" => Yii::t("summary", "Annual target (base case)"), "background" => "#DCE6F1",
                "colspan" => $colspan
            );//年金额目标 (base case)
            $topList[] = array("name" => Yii::t("summary", "Goal degree (base case)"), "background" => "#DCE6F1",
                "colspan" => $colspan
            );//目标完成度 (base case)
        }
        if(SummaryForm::targetReadyMinimum()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>$colspan
            );//年金额目标 (minimum case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>$colspan
            );//目标完成度 (minimum case)
        }

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
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=90;
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $moData = key_exists("MO",$this->data)?$this->data["MO"]:array();
            unset($this->data["MO"]);//澳门需要单独处理
            $html.=$this->showServiceHtml($this->data);
            $html.=$this->showServiceHtmlForMO($moData);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name","u_actual_money","new_sum_last","new_sum","new_rate",
            "new_sum_n_last","new_sum_n","new_n_rate",
            "new_month_n_last","new_month_n","new_month_rate",
            "stop_sum_last","stop_sum","stop_rate","monthStopRate","comStopRate",
            "resume_sum_last","resume_sum","resume_rate",
            "pause_sum_last","pause_sum","pause_rate",
            "amend_sum_last","amend_sum","amend_rate",
            "net_sum_last","net_sum","net_rate"
        );
        if(SummaryForm::targetReadyUpside()){
            $bodyKey[]="start_one_gross";
            $bodyKey[]="start_one_net";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="one_gross";
                $bodyKey[]="one_net";
            }
            $bodyKey[]="start_one_gross_rate";
            $bodyKey[]="start_one_net_rate";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="one_gross_rate";
                $bodyKey[]="one_net_rate";
            }
        }
        if(SummaryForm::targetReadyBase()){
            $bodyKey[]="start_two_gross";
            $bodyKey[]="start_two_net";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="two_gross";
                $bodyKey[]="two_net";
            }
            $bodyKey[]="start_two_gross_rate";
            $bodyKey[]="start_two_net_rate";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="two_gross_rate";
                $bodyKey[]="two_net_rate";
            }
        }
        if(SummaryForm::targetReadyMinimum()){
            $bodyKey[]="start_three_gross";
            $bodyKey[]="start_three_net";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="three_gross";
                $bodyKey[]="three_net";
            }
            $bodyKey[]="start_three_gross_rate";
            $bodyKey[]="start_three_net_rate";
            if(SummaryForm::grossAndNet()){
                $bodyKey[]="three_gross_rate";
                $bodyKey[]="three_net_rate";
            }
        }
        return $bodyKey;
    }
    //將城市数据寫入表格(澳门)
    private function showServiceHtmlForMO($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            foreach ($data["list"] as $cityList) {
                $this->resetTdRow($cityList);
                $html="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                    $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                    $text = ComparisonForm::showNum($text);
                    //$inputHide = TbHtml::hiddenField("excel[MO][{$keyStr}]",$text);
                    $this->downJsonText["excel"]['MO'][$keyStr]=$text;
                    $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr){
        $tdClass = "";
        if(strpos($text,'%')!==false){
            if(!in_array($keyStr,array("new_rate","stop_rate","net_rate"))){
                $tdClass =floatval($text)<=60?"text-danger":$tdClass;
            }
            $tdClass =floatval($text)>=100?"text-green":$tdClass;
        }elseif (strpos($keyStr,'net')!==false){ //所有淨增長為0時特殊處理
            if(Yii::t("summary","completed")==$text){
                $tdClass="text-green";
            }elseif (Yii::t("summary","incomplete")==$text){
                $tdClass="text-danger";
            }
        }

        return $tdClass;
    }

    //設置顏色(2023/06/23年继续额外增加)
    public static function setTextColorForKeyStr(&$color,$keyStr,$arr){
        $setArr = array(
            "start_one_gross_rate","start_one_net_rate","one_gross_rate","one_net_rate",
            "start_two_gross_rate","start_two_net_rate","two_gross_rate","two_net_rate",
            "start_three_gross_rate","start_three_net_rate","three_gross_rate","three_net_rate",
        );
        if($color=="text-green"&&in_array($keyStr,$setArr)){
            if(strpos($keyStr,'one_')!==false){
                $str = "one";
            }elseif (strpos($keyStr,'three_')!==false){
                $str = "three";
            }elseif (strpos($keyStr,'two_')!==false){
                $str = "two";
            }else{
                return;
            }
            if($arr["start_{$str}_gross"]==$arr["{$str}_gross"]&&$arr["start_{$str}_gross"]==$arr["{$str}_gross"]){
                $color = "text-orange";
            }
        }
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = ["stopSumOnly"=>0];//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = ["stopSumOnly"=>0];//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $regionRow["stopSumOnly"]+=$cityList["stopSumOnly"];
                        $allRow["stopSumOnly"]+=$cityList["stopSumOnly"];
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
                            if($cityList["add_type"]!=1){ //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                            ComparisonForm::setTextColorForKeyStr($tdClass,$keyStr,$cityList);
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $text = ComparisonForm::showNum($text);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;
                            if($keyStr=="new_sum"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' {$exprData} data-u='{$cityList['u_sum']}'><span>{$text}</span></td>";
                            }elseif($keyStr=="new_sum_last"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' {$exprData} data-u='{$cityList['u_sum_last']}'><span>{$text}</span></td>";
                            }else{
                                $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
                            }
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionList["region"];
                    $regionRow["city_name"]=$regionList["region_name"];
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["region"]="allRow";
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
            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
            $text = ComparisonForm::showNum($text);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
            $this->downJsonText["excel"][$data['region']]['count'][$keyStr]=$text;
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

    //下載
    public function downExcel($excelData){
        if(!is_array($excelData)){
            $excelData = json_decode($excelData,true);
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","Comparison"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Comparison"));
    }

    protected function clickList(){
        return array(
            "new_month_n_last"=>array("title"=>Yii::t("summary","Last Month Single + New(INV)").Yii::t("summary"," (last year)"),"type"=>"ServiceINVMonthNewLast"),
            "new_month_n"=>array("title"=>Yii::t("summary","Last Month Single + New(INV)"),"type"=>"ServiceINVMonthNew"),
            "new_sum_n_last"=>array("title"=>Yii::t("summary","New(single) + New(INV)").Yii::t("summary"," (last year)"),"type"=>"ServiceINVNewLast"),
            "new_sum_n"=>array("title"=>Yii::t("summary","New(single) + New(INV)"),"type"=>"ServiceINVNew"),
            "new_sum_last"=>array("title"=>Yii::t("summary","New(not single)").Yii::t("summary"," (last year)"),"type"=>"ServiceNewLast"),
            "new_sum"=>array("title"=>Yii::t("summary","New(not single)"),"type"=>"ServiceNew"),
            "stop_sum_last"=>array("title"=>Yii::t("summary","YTD Stop").Yii::t("summary"," (last year)"),"type"=>"ServiceStopLast"),
            "stop_sum"=>array("title"=>Yii::t("summary","YTD Stop"),"type"=>"ServiceStop"),
            "resume_sum_last"=>array("title"=>Yii::t("summary","YTD Resume").Yii::t("summary"," (last year)"),"type"=>"ServiceResumeLast"),
            "resume_sum"=>array("title"=>Yii::t("summary","YTD Resume"),"type"=>"ServiceResume"),
            "pause_sum_last"=>array("title"=>Yii::t("summary","YTD Pause").Yii::t("summary"," (last year)"),"type"=>"ServicePauseLast"),
            "pause_sum"=>array("title"=>Yii::t("summary","YTD Pause"),"type"=>"ServicePause"),
            "amend_sum_last"=>array("title"=>Yii::t("summary","YTD Amend").Yii::t("summary"," (last year)"),"type"=>"ServiceAmendLast"),
            "amend_sum"=>array("title"=>Yii::t("summary","YTD Amend"),"type"=>"ServiceAmend"),
        );
    }

    private function tdClick(&$tdClass,$keyStr,$city){
        $expr = " data-city='{$city}'";
        $list = $this->clickList();
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }
}