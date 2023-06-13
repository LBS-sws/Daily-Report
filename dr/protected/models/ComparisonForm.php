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

    private $con_list=array("one_gross","one_net","two_gross","two_net","three_gross","three_net");

    public $data=array();

	public $th_sum=2;//所有th的个数

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

    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $this->start_date = empty($this->start_date)?date("Y/01/01"):$this->start_date;
        $this->end_date = empty($this->end_date)?date("Y/m/t"):$this->end_date;
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $citySetList = CitySetForm::getCitySetList();
        $uActualMoneyList = SummaryForm::getUActualMoney($this->start_date,$this->end_date,$city_allow,$citySetList);
        $serviceList = $this->getServiceData($citySetList,$uActualMoneyList);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $region = $cityRow["region_code"];
            if(!key_exists($region,$data)){
                $data[$region]=array(
                    "region"=>$region,
                    "region_name"=>$cityRow["region_name"],
                    "list"=>array()
                );
            }
            if(key_exists($city,$serviceList)){
                $arr = $serviceList[$city];
            }else{
                $arr = $this->defMoreCity($city,$cityRow["city_name"],$uActualMoneyList);
            }
            $arr["add_type"]=$cityRow["add_type"];
            $data[$region]["list"][$city]=$arr;
        }
        //$this->defaultRowForCity($data,$cityList,$uActualMoneyList);//填充默認城市（無數據的城市需要顯示0）

        $this->insertUData($this->start_date,$this->end_date,$data,$citySetList);
        $this->insertUData($lastStartDate,$lastEndDate,$data,$citySetList);
        $this->data = $data;
        $session = Yii::app()->session;
        $session['comparison_c01'] = $this->getCriteria();
        return true;
    }

    private function getServiceData($citySetList,$uActualMoneyList){
        $data=array();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];

        $where="(a.status_dt BETWEEN '{$this->start_date}' and '{$this->end_date}')";
        $where.="or (a.status_dt BETWEEN '{$lastStartDate}' and '{$lastEndDate}')";
        $selectSql = "a.status_dt,a.status,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid
            ";
        $serviceRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("not(f.rpt_cat='INV' and f.single=1) and a.city in ({$city_allow}) and a.city not in ('ZY') and a.status in ('N','T') and ({$where})")
            ->order("a.city")
            ->queryAll();
        //所有需要計算的客戶服務(ID客戶服務)
        $serviceRowsID = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("not(f.rpt_cat='INV' and f.single=1) and a.city in ({$city_allow}) and a.city not in ('ZY') and a.status in ('N','T') and ({$where})")
            ->order("a.city")
            ->queryAll();
        $serviceRows = $serviceRows?$serviceRows:array();
        $serviceRowsID = $serviceRowsID?$serviceRowsID:array();
        $rows = array_merge($serviceRows,$serviceRowsID);
        if(!empty($rows)){
            foreach ($rows as $row){
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $this->insertDataForRow($row,$data,$citySetList,$uActualMoneyList);
            }
        }
        return $data;
    }

    private function insertUData($startDate,$endDate,&$data,$cityList){
        $year = intval($startDate);//服务的年份
        $json = Invoice::getInvData($startDate,$endDate);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = SummaryForm::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(key_exists($city,$cityList)){
                    $region = $cityList[$city]["region_code"];
                    if($year==$this->comparison_year){
                        $data[$region]["list"][$city]["u_actual_money"]+=$money;//服务生意额需要加上产品金额
                        $uStr = "u_sum";
                        $newStr = "new_sum";
                        $netStr = "net_sum";
                    }else{
                        $uStr = "u_sum_last";
                        $newStr = "new_sum_last";
                        $netStr = "net_sum_last";
                    }
                    $data[$region]["list"][$city][$uStr]+=$money;
                    $data[$region]["list"][$city][$newStr]+=$money;
                    $data[$region]["list"][$city][$netStr]+=$money;

                    if($cityList[$city]["add_type"]==1){//叠加(城市配置的叠加)
                        $city = $cityList[$city]["region_code"];
                        $region = "";
                        if(key_exists($city,$cityList)){
                            $region = $cityList[$city]["region_code"];
                        }
                        if(key_exists($region,$data)){
                            if($year==$this->comparison_year){
                                $data[$region]["list"][$city]["u_actual_money"]+=$money;//服务生意额需要加上产品金额
                            }
                            $data[$region]["list"][$city][$uStr]+=$money;
                            $data[$region]["list"][$city][$newStr]+=$money;
                            $data[$region]["list"][$city][$netStr]+=$money;
                        }
                    }
                }
            }
        }
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name,$uActualMoneyList){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_actual_money"=>key_exists($city,$uActualMoneyList)?$uActualMoneyList[$city]:0,//服务生意额
            "u_sum_last"=>0,//U系统金额(上一年)
            "u_sum"=>0,//U系统金额
            "stopSumOnly"=>0,//本月停單金額（月）
            "monthStopRate"=>0,//月停單率
            "new_sum_last"=>0,//新增(上一年)
            "new_sum"=>0,//新增
            "new_rate"=>0,//新增对比比例
            "stop_sum_last"=>0,//终止（上一年）
            "stop_sum"=>0,//终止
            "stop_rate"=>0,//终止对比比例
            "net_sum_last"=>0,//总和（上一年）
            "net_sum"=>0,//总和
            "net_rate"=>0,//总和对比比例
        );
        foreach ($this->con_list as $itemStr){//初始化
            $arr[$itemStr]=0;
            $arr[$itemStr."_rate"]=0;
            $arr["start_".$itemStr]=0;
            $arr["start_".$itemStr."_rate"]=0;
        }
        $rowStart = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>$this->comparison_year,":city"=>$city)
            )->queryRow();//查询目标金额
        if($rowStart){
            foreach ($this->con_list as $itemStr){//写入年初生意额
                $arr["start_".$itemStr]=empty($rowStart[$itemStr])?0:floatval($rowStart[$itemStr]);
            }
        }
        $setRow = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$this->comparison_year,":month_type"=>$this->month_type,":city"=>$city)
            )->queryRow();//查询目标金额
        if($setRow){
            foreach ($this->con_list as $itemStr){//写入滚动生意额
                $arr[$itemStr]=empty($setRow[$itemStr])?0:floatval($setRow[$itemStr]);
            }
        }
        return $arr;
    }

    private function insertDataForRow($row,&$data,$citySetList,$uActualMoneyList){
	    $year = intval($row["status_dt"]);//服务的年份
        $city = empty($row["city"])?"none":$row["city"];
        $citySet = CitySetForm::getListForCityCode($city,$citySetList);
        if(!key_exists($city,$data)){//設置該城市的默認值
            $arr = $this->defMoreCity($city,$citySet["city_name"],$uActualMoneyList);
            $data[$city]=$arr;
        }
        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
            if(!key_exists($citySet["region_code"],$data)){
                $data[$citySet["region_code"]]=$this->defMoreCity($citySet["region_code"],$citySet["region_name"],$uActualMoneyList);
            }
        }
        if($row["paid_type"]=="M"){//月金额
            $money = $row["amt_paid"]*$row["ctrt_period"];
            $monthMoney = $row["amt_paid"];//月金額
        }else{
            $money = $row["amt_paid"];
            $monthMoney = empty($row["ctrt_period"])?0:$row["amt_paid"]/$row["ctrt_period"];
            $monthMoney = round($monthMoney,2);
        }
        if($year==$this->comparison_year){
            $newStr = "new_sum";
            $stopStr = "stop_sum";
            $netStr = "net_sum";
        }else{
            $newStr = "new_sum_last";
            $stopStr = "stop_sum_last";
            $netStr = "net_sum_last";
        }
        switch ($row["status"]) {
            case "N"://新增
                $data[$city][$newStr] += $money;
                if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                    $data[$citySet["region_code"]][$newStr] += $money;
                }
                break;
            case "T"://终止
                if($this->comparison_year==$year){
                    $data[$city]["stopSumOnly"] += $monthMoney;
                    if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                        $data[$citySet["region_code"]]["stopSumOnly"] += $monthMoney;
                    }
                }
                $money *= -1;
                $data[$city][$stopStr] += $money;
                if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                    $data[$citySet["region_code"]][$stopStr] += $money;
                }
                break;
        }
        $data[$city][$netStr] += $money;
        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
            $data[$citySet["region_code"]][$netStr] += $money;
        }
    }

    protected function resetTdRow(&$list,$bool=false){
        $list["monthStopRate"] = $this->comparisonRate($list["stopSumOnly"],$list["u_actual_money"]);
        $list["start_two_gross"] = $bool?$list["start_two_gross"]:ComparisonForm::resetNetOrGross($list["start_two_gross"],$this->day_num,$this->search_type);
        $list["start_two_net"] = $bool?$list["start_two_net"]:ComparisonForm::resetNetOrGross($list["start_two_net"],$this->day_num,$this->search_type);
        $list["start_two_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["start_two_gross"]);
        $list["start_two_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_two_net"],"net");

        $list["two_gross"] = $bool?$list["two_gross"]:ComparisonForm::resetNetOrGross($list["two_gross"],$this->day_num,$this->search_type);
        $list["two_net"] = $bool?$list["two_net"]:ComparisonForm::resetNetOrGross($list["two_net"],$this->day_num,$this->search_type);
        $list["new_rate"] = $this->nowAndLastRate($list["new_sum"],$list["new_sum_last"],true);
        $list["stop_rate"] = $this->nowAndLastRate($list["stop_sum"],$list["stop_sum_last"],true);
        $list["net_rate"] = $this->nowAndLastRate($list["net_sum"],$list["net_sum_last"],true);
        $list["two_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["two_gross"]);
        $list["two_net_rate"] = $this->comparisonRate($list["net_sum"],$list["two_net"],"net");

        if(SummaryForm::targetAllReady()){
            $list["start_one_gross"] = $bool?$list["start_one_gross"]:ComparisonForm::resetNetOrGross($list["start_one_gross"],$this->day_num,$this->search_type);
            $list["start_one_net"] = $bool?$list["start_one_net"]:ComparisonForm::resetNetOrGross($list["start_one_net"],$this->day_num,$this->search_type);
            $list["start_three_gross"] = $bool?$list["start_three_gross"]:ComparisonForm::resetNetOrGross($list["start_three_gross"],$this->day_num,$this->search_type);
            $list["start_three_net"] = $bool?$list["start_three_net"]:ComparisonForm::resetNetOrGross($list["start_three_net"],$this->day_num,$this->search_type);
            $list["start_one_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["start_one_gross"]);
            $list["start_one_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_one_net"],"net");
            $list["start_three_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["start_three_gross"]);
            $list["start_three_net_rate"] = $this->comparisonRate($list["net_sum"],$list["start_three_net"],"net");

            $list["one_gross"] = $bool?$list["one_gross"]:ComparisonForm::resetNetOrGross($list["one_gross"],$this->day_num,$this->search_type);
            $list["one_net"] = $bool?$list["one_net"]:ComparisonForm::resetNetOrGross($list["one_net"],$this->day_num,$this->search_type);
            $list["three_gross"] = $bool?$list["three_gross"]:ComparisonForm::resetNetOrGross($list["three_gross"],$this->day_num,$this->search_type);
            $list["three_net"] = $bool?$list["three_net"]:ComparisonForm::resetNetOrGross($list["three_net"],$this->day_num,$this->search_type);
            $list["one_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["one_gross"]);
            $list["one_net_rate"] = $this->comparisonRate($list["net_sum"],$list["one_net"],"net");
            $list["three_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["three_gross"]);
            $list["three_net_rate"] = $this->comparisonRate($list["net_sum"],$list["three_net"],"net");
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
            array("name"=>Yii::t("summary","YTD Stop").$monthStr,"exprName"=>$monthStr,"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                    array("name"=>Yii::t("summary","Month Stop Rate")),//月停单率
                )
            ),//YTD终止
            array("name"=>Yii::t("summary","YTD Net").$monthStr,"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD Net
        );
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                    array("name"=>Yii::t("summary","Start Net")),//Start Net
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (upside case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                    array("name"=>Yii::t("summary","Start Net")),//Start Net
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (upside case)
        }
        $topList[]=array("name"=>Yii::t("summary","Annual target (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                array("name"=>Yii::t("summary","Start Net")),//Start Net
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//年金额目标 (base case)
        $topList[]=array("name"=>Yii::t("summary","Goal degree (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                array("name"=>Yii::t("summary","Start Net")),//Start Net
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//目标完成度 (base case)
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                    array("name"=>Yii::t("summary","Start Net")),//Start Net
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (minimum case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Start Gross")),//Start Gross
                    array("name"=>Yii::t("summary","Start Net")),//Start Net
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (minimum case)
        }

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=$list["name"];
            $colList=key_exists("colspan",$list)?$list['colspan']:array();
            $trOne.="<th";
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("colspan",$list)){
                $colNum=count($colList);
                $trOne.=" colspan='{$colNum}' class='click-th'";
            }
            if(key_exists("background",$list)){
                $trOne.=" style='background:{$list["background"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
            if(!empty($colList)){
                foreach ($colList as $col){
                    $this->th_sum++;
                    $trTwo.="<th><span>".$col["name"]."</span></th>";
                }
            }
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
            if(in_array($i,array(3,6,9,18,19,20,21))){
                $width=90;
            }else{
                $width=80;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $html.="<tbody>";
            $moData = key_exists("MO",$this->data)?$this->data["MO"]:array();
            unset($this->data["MO"]);//澳门需要单独处理
            $html.=$this->showServiceHtml($this->data);
            $html.=$this->showServiceHtmlForMO($moData);
            $html.="</tbody>";
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name","u_actual_money","new_sum_last","new_sum","new_rate","stop_sum_last","stop_sum","stop_rate","monthStopRate",
            "net_sum_last","net_sum","net_rate"
        );
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="start_one_gross";
            $bodyKey[]="start_one_net";
            $bodyKey[]="one_gross";
            $bodyKey[]="one_net";
            $bodyKey[]="start_one_gross_rate";
            $bodyKey[]="start_one_net_rate";
            $bodyKey[]="one_gross_rate";
            $bodyKey[]="one_net_rate";
        }
        $bodyKey[]="start_two_gross";
        $bodyKey[]="start_two_net";
        $bodyKey[]="two_gross";
        $bodyKey[]="two_net";
        $bodyKey[]="start_two_gross_rate";
        $bodyKey[]="start_two_net_rate";
        $bodyKey[]="two_gross_rate";
        $bodyKey[]="two_net_rate";
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="start_three_gross";
            $bodyKey[]="start_three_net";
            $bodyKey[]="three_gross";
            $bodyKey[]="three_net";
            $bodyKey[]="start_three_gross_rate";
            $bodyKey[]="start_three_net_rate";
            $bodyKey[]="three_gross_rate";
            $bodyKey[]="three_net_rate";
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
                    $text = ComparisonForm::showNum($text);
                    $inputHide = TbHtml::hiddenField("excel[MO][{$keyStr}]",$text);
                    $html.="<td class='{$tdClass}'><span>{$text}</span>{$inputHide}</td>";
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
                            $text = ComparisonForm::showNum($text);
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            if($keyStr=="new_sum"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' data-u='{$cityList['u_sum']}'><span>{$text}</span>{$inputHide}</td>";
                            }elseif($keyStr=="new_sum_last"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' data-u='{$cityList['u_sum_last']}'><span>{$text}</span>{$inputHide}</td>";
                            }else{
                                $html.="<td class='{$tdClass}'><span>{$text}</span>{$inputHide}</td>";
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
            $inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span>{$inputHide}</td>";
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
}