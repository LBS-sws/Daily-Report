<?php

class SummaryForm extends CFormModel
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
	public $summary_year;

	public $last_month_start;
	public $last_month_end;

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
        //上月的開始及結束時間
        $this->last_month_start = date("Y/m/d",strtotime($this->start_date." - 1 month"));
        $this->last_month_end = date("Y/m/d",strtotime($this->end_date." - 1 month"));
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
	
	//轉換U系統的城市（國際版專用）
	public static function resetCity($city){
		switch($city){
			case "KL":
				return "MY";
			case "SL":
				return "MY";
		}
		return $city;
	}

    //获取某個時間段的一次性服務金額
    public static function getOneServiceList($startDay,$endDay,$city_allow="",$citySetList=array()){
        $list = array();
        $whereSql = "status='N' and paid_type=1 and status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and city in ({$city_allow})";
        }
        $queryIA = Yii::app()->db->createCommand()
            ->select("sum(amt_paid) as sum_paid,city")
            ->from("swo_service")
            ->where($whereSql);
        $rows = $queryIA->group("city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = $row["city"];
                $money = empty($row["sum_paid"])?0:round($row["sum_paid"],2);
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$money;
                if(key_exists($city,$citySetList)&&$citySetList[$city]["add_type"]==1){//城市配置（叠加)
                    $city=$citySetList[$city]["region_code"];
                    if(!key_exists($city,$list)){
                        $list[$city]=0;
                    }
                    $list[$city]+=$money;
                }
            }
        }
        return $list;
    }

    //获取某個時間段的產品服務金額
    public static function getUData($startDay,$endDay,$citySetList=array()){
        $json = Invoice::getInvData($startDay,$endDay);
        $list = array();
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = SummaryForm::resetCity($row["city"]);
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$money;
                if(key_exists($city,$citySetList)&&$citySetList[$city]["add_type"]==1){//城市配置（叠加)
                    $city=$citySetList[$city]["region_code"];
                    if(!key_exists($city,$list)){
                        $list[$city]=0;
                    }
                    $list[$city]+=$money;
                }
            }
        }
        return $list;
    }

    //获取U系统的服务单数据
    public static function getUActualMoney($startDay,$endDay,$city_allow="",$citySetList=array()){
	    $list = array();
	    $citySql = "";
	    if(!empty($city_allow)){
	        $citySql = " and b.Text in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("b.Text,sum(
                    if(a.TermCount=0,0,a.Fee/a.TermCount)
					) as sum_amount")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->group("b.Text")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = SummaryForm::resetCity($row["Text"]);
                $money = empty($row["sum_amount"])?0:round($row["sum_amount"],2);
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$money;
                if(key_exists($city,$citySetList)&&$citySetList[$city]["add_type"]==1){//城市配置（叠加)
                    $city=$citySetList[$city]["region_code"];
                    if(!key_exists($city,$list)){
                        $list[$city]=0;
                    }
                    $list[$city]+=$money;
                }
            }
        }
        return $list;
    }

    public function retrieveData() {
        $this->summary_year = date("Y",strtotime($this->start_date));
	    $rptModel = new RptSummarySC();
        $criteria = new ReportForm();
        $criteria->start_dt = $this->start_date;
        $criteria->end_dt = $this->end_date;
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $criteria->city = $city_allow;
        $rptModel->criteria = $criteria;
        $rptModel->retrieveData();
        $this->data = $rptModel->data;
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $uActualMoneyList = SummaryForm::getUActualMoney($this->start_date,$this->end_date,$city_allow,$citySetList);
        $lastOneServiceList = self::getOneServiceList($this->last_month_start,$this->last_month_end,$city_allow,$citySetList);
        $lastUDataList = self::getUData($this->last_month_start,$this->last_month_end,$citySetList);
        if($this->data){
            $strSelect = implode(",",$this->con_list);

            foreach ($this->data as $regionKey=>$regionList){
                if(!empty($regionList["list"])){
                    foreach ($regionList["list"] as $cityKey=>$cityList){
                        $this->data[$regionKey]["list"][$cityKey]["last_one_service"]+=key_exists($cityKey,$lastOneServiceList)?$lastOneServiceList[$cityKey]:0;//实际月金额
                        $this->data[$regionKey]["list"][$cityKey]["last_u_invoice_sum"]+=key_exists($cityKey,$lastUDataList)?$lastUDataList[$cityKey]:0;//实际月金额
                        $this->data[$regionKey]["list"][$cityKey]["u_actual_money"]+=key_exists($cityKey,$uActualMoneyList)?$uActualMoneyList[$cityKey]:0;//实际月金额
                        $this->data[$regionKey]["list"][$cityKey]["u_actual_money"]+=$this->data[$regionKey]["list"][$cityKey]["u_invoice_sum"];//服务生意额需要加上产品金额
                        $this->data[$regionKey]["list"][$cityKey]["num_growth"]=0;//净增长
                        foreach ($this->con_list as $itemStr){//初始化
                            $this->data[$regionKey]["list"][$cityKey][$itemStr]=0;
                            $this->data[$regionKey]["list"][$cityKey][$itemStr."_rate"]=0;
                            $this->data[$regionKey]["list"][$cityKey]["start_".$itemStr]=0;
                            $this->data[$regionKey]["list"][$cityKey]["start_".$itemStr."_rate"]=0;
                        }
                        //放入目标金额
                        $rowStart = Yii::app()->db->createCommand()->select($strSelect)->from("swo_comparison_set")
                            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                                array(":year"=>$this->summary_year,":month_type"=>1,":city"=>$cityKey)
                            )->queryRow();//年初生意额目标
                        if($rowStart){
                            foreach ($this->con_list as $itemStr){//写入年初生意额
                                $this->data[$regionKey]["list"][$cityKey]["start_".$itemStr]=empty($rowStart[$itemStr])?0:floatval($rowStart[$itemStr]);
                            }
                        }
                        $row = Yii::app()->db->createCommand()->select($strSelect)->from("swo_comparison_set")
                            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                                array(":year"=>$this->summary_year,":month_type"=>$this->month_type,":city"=>$cityKey)
                            )->queryRow();//滚动生意额目标
                        if($row){
                            foreach ($this->con_list as $itemStr){//写入滚动生意额
                                $this->data[$regionKey]["list"][$cityKey][$itemStr]=empty($row[$itemStr])?0:floatval($row[$itemStr]);
                            }
                        }
                    }
                }
            }
        }

        $session = Yii::app()->session;
        $session['summary_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
	    $newSum = $list["num_new"]+$list["u_invoice_sum"];//所有新增总金额
        $list["num_growth"] = 0;
	    $list["num_growth"]+=$list["num_new"];
	    $list["num_growth"]+=$list["u_invoice_sum"];
	    $list["num_growth"]+=$list["num_stop"];
	    $list["num_growth"]+=$list["num_restore"];
	    $list["num_growth"]+=$list["num_pause"];
	    $list["num_growth"]+=$list["num_update"];

        if(SummaryForm::targetReadyBase()){
            $list["start_two_gross"] = $bool?$list["start_two_gross"]:ComparisonForm::resetNetOrGross($list["start_two_gross"],$this->day_num,$this->search_type);
            $list["start_two_net"] = $bool?$list["start_two_net"]:ComparisonForm::resetNetOrGross($list["start_two_net"],$this->day_num,$this->search_type);
            $list["start_two_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["start_two_gross"]);
            $list["start_two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["start_two_net"],"net");
            if(SummaryForm::grossAndNet()){
                $list["two_gross"] = $bool?$list["two_gross"]:ComparisonForm::resetNetOrGross($list["two_gross"],$this->day_num,$this->search_type);
                $list["two_net"] = $bool?$list["two_net"]:ComparisonForm::resetNetOrGross($list["two_net"],$this->day_num,$this->search_type);
                $list["two_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["two_gross"]);
                $list["two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["two_net"],"net");
            }
        }
        if(SummaryForm::targetReadyUpside()){
            $list["start_one_gross"] = $bool?$list["start_one_gross"]:ComparisonForm::resetNetOrGross($list["start_one_gross"],$this->day_num,$this->search_type);
            $list["start_one_net"] = $bool?$list["start_one_net"]:ComparisonForm::resetNetOrGross($list["start_one_net"],$this->day_num,$this->search_type);
            $list["start_one_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["start_one_gross"]);
            $list["start_one_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["start_one_net"],"net");
            if(SummaryForm::grossAndNet()){
                $list["one_gross"] = $bool?$list["one_gross"]:ComparisonForm::resetNetOrGross($list["one_gross"],$this->day_num,$this->search_type);
                $list["one_net"] = $bool?$list["one_net"]:ComparisonForm::resetNetOrGross($list["one_net"],$this->day_num,$this->search_type);
                $list["one_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["one_gross"]);
                $list["one_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["one_net"],"net");
            }
        }
        if(SummaryForm::targetReadyMinimum()){
            $list["start_three_gross"] = $bool?$list["start_three_gross"]:ComparisonForm::resetNetOrGross($list["start_three_gross"],$this->day_num,$this->search_type);
            $list["start_three_net"] = $bool?$list["start_three_net"]:ComparisonForm::resetNetOrGross($list["start_three_net"],$this->day_num,$this->search_type);
            $list["start_three_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["start_three_gross"]);
            $list["start_three_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["start_three_net"],"net");
            if(SummaryForm::grossAndNet()){
                $list["three_gross"] = $bool?$list["three_gross"]:ComparisonForm::resetNetOrGross($list["three_gross"],$this->day_num,$this->search_type);
                $list["three_net"] = $bool?$list["three_net"]:ComparisonForm::resetNetOrGross($list["three_net"],$this->day_num,$this->search_type);
                $list["three_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["three_gross"]);
                $list["three_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["three_net"],"net");
            }
        }
    }

    //顯示提成表的表格內容
    public function summaryHtml(){
        $html= '<table id="summary" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Actual monthly amount"),"rowspan"=>2),//服务生意额
            array("name"=>Yii::t("summary","Signing status"),"background"=>"#f7fd9d",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","New(service)")),//新增服务
                    array("name"=>Yii::t("summary","New(INV)")),//新增（产品）
                    array("name"=>Yii::t("summary","Terminate service")),//终止服务
                    array("name"=>Yii::t("summary","Resume service")),//恢复服务
                    array("name"=>Yii::t("summary","Suspended service")),//暂停服务
                    array("name"=>Yii::t("summary","Amendment service")),//更改服务
                    array("name"=>Yii::t("summary","Net growth")),//净增长
                )
            ),//签单情况
            array("name"=>Yii::t("summary","New customer(service)"),"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","long month")),//长约（>=12月）
                    array("name"=>Yii::t("summary","short month")),//短约
                    array("name"=>Yii::t("summary","one service")),//一次性服务
                    array("name"=>Yii::t("summary","cate service")),//餐饮客户
                    array("name"=>Yii::t("summary","not cate service")),//非餐饮客户
                )
            ),//新增客户（服务）
            array("name"=>Yii::t("summary","New customer(INV)"),"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","cate service")),//餐饮客户
                    array("name"=>Yii::t("summary","not cate service")),//非餐饮客户
                )
            ),//新增客户（产品）
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

        $topList[]=array("name"=>Yii::t("summary","added last month"),"background"=>"#f7fd9d",
            "colspan"=>array(
                array("name"=>Yii::t("summary","one service")),//一次性服务
                array("name"=>Yii::t("summary","New(INV)")),//新增（产品）
            )
        );//上月新增
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
            if(in_array($i,array(2,4,5,6,7,8))){
                $width=75;
            }elseif($i==9){
                $width=110;
            }elseif(in_array($i,array(1,3,12,14))){
                $width=90;
            }else{
                $width=83;
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
            "city_name","u_actual_money","num_new","u_invoice_sum","num_stop","num_restore","num_pause","num_update",
            "num_growth","num_long","num_short","one_service","num_cate","num_not_cate","u_num_cate","u_num_not_cate"
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
        $bodyKey[]="last_one_service";
        $bodyKey[]="last_u_invoice_sum";
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
                    $text = ComparisonForm::showNum($text);
                    $inputHide = TbHtml::hiddenField("excel[MO][{$keyStr}]",$text);
                    $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                    $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                    ComparisonForm::setTextColorForKeyStr($tdClass,$keyStr,$cityList);
                    $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span>{$inputHide}</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }
    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = [];//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = [];//地区汇总
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
                            if($cityList["add_type"]!=1) { //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                            ComparisonForm::setTextColorForKeyStr($tdClass,$keyStr,$cityList);
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $text = ComparisonForm::showNum($text);
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span>{$inputHide}</td>";
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

    public static function targetAllReady(){
        return Yii::app()->user->validFunction('CN15');
    }

    public static function targetReadyUpside(){
        return Yii::app()->user->validFunction('CN23');
    }

    public static function targetReadyBase(){
        return Yii::app()->user->validFunction('CN21');
    }

    public static function targetReadyMinimum(){
        return Yii::app()->user->validFunction('CN22');
    }

    public static function grossAndNet(){
        return Yii::app()->user->validFunction('CN20');
    }

    //下載
    public function downExcel($excelData){
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","Summary"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Summary"));
    }

    private function tdClick(&$tdClass,$keyStr,$city){
        $expr = " data-city='{$city}'";
        $list = array(
            "num_cate"=>array("title"=>Yii::t("summary","cate service"),"type"=>"ServiceCate"),
            "num_not_cate"=>array("title"=>Yii::t("summary","not cate service"),"type"=>"ServiceCateNot"),
            "num_long"=>array("title"=>Yii::t("summary","long month"),"type"=>"ServiceLong"),
            "num_short"=>array("title"=>Yii::t("summary","short month"),"type"=>"ServiceShort"),
            "one_service"=>array("title"=>Yii::t("summary","one service"),"type"=>"ServiceOne"),
            "last_one_service"=>array("title"=>Yii::t("summary","one service")."(".Yii::t("summary","added last month").")","type"=>"ServiceOneLast"),
            "num_update"=>array("title"=>Yii::t("summary","Amendment service"),"type"=>"ServiceAmendment"),
            "num_new"=>array("title"=>Yii::t("summary","New(service)"),"type"=>"ServiceNew"),
            "num_pause"=>array("title"=>Yii::t("summary","Suspended service"),"type"=>"ServiceSuspended"),
            "num_restore"=>array("title"=>Yii::t("summary","Resume service"),"type"=>"ServiceRenewal"),
            "num_stop"=>array("title"=>Yii::t("summary","Terminate service"),"type"=>"ServiceStop"),
        );
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }

    public static function drawEditButton($access, $writeurl, $readurl, $param) {
        $rw = Yii::app()->user->validRWFunction($access);
        $url = $rw ? $writeurl : $readurl;
        $icon = $rw ? "glyphicon glyphicon-pencil" : "glyphicon glyphicon-eye-open";
        $lnk=Yii::app()->createUrl($url,$param);

        return "<a href=\"$lnk\" target='_blank'><span class=\"$icon\"></span></a>";
    }
}