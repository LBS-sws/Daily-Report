<?php

class BonusMonthForm extends CFormModel
{
	/* User Fields */
	public $search_year;
	public $search_month;
	public $start_date;
	public $end_date;

	public $data=array();

	public $th_sum=0;//所有th的个数

    public $downJsonText='';
    public $u_load_data=array();//查询时长数组
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
            'search_year'=>Yii::t('summary','search year'),
            'search_month'=>Yii::t('summary','search month'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('search_year,search_month','safe'),
			array('search_year,search_month','required'),
            array('search_month','validateDate'),
		);
	}

    public function validateDate($attribute, $params) {
	    $dateStr = $this->search_year."/".$this->search_month."/01";
	    $this->start_date = date_format(date_create($dateStr),"Y/m/01");
	    $this->end_date = date_format(date_create($dateStr),"Y/m/t");
	    if($this->start_date>date_format(date_create(),"Y/m/01")){
            $this->addError($attribute, "查询时间不能大于".date_format(date_create(),"Y年n月"));
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
            'search_month'=>$this->search_month
        );
    }

    public function retrieveData($city_allow="") {
        $this->u_load_data['load_start'] = time();
        $data = array();
        $city_allow = empty($city_allow)?Yii::app()->user->city_allow():$city_allow;
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $monthStartDate = CountSearch::computeLastMonth($this->start_date);
        $monthEndDate = CountSearch::computeLastMonth($this->end_date);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $chargeSql = CountSearch::getCityChargeSql($city_allow);

        $this->u_load_data['u_load_start'] = time();
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //获取U系统的產品数据(上月)
        $monthUInvMoney = CountSearch::getUInvMoney($monthStartDate,$monthEndDate,$city_allow);
        //获取U系统的服务单数据(上月)
        $uServiceMoneyLast = CountSearch::getUServiceMoney($monthStartDate,$monthEndDate,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        //更改服务(只算增加)
        $serviceForAD = CountSearch::getServiceForAD($startDate,$endDate,$city_allow);
        //更改服务(只算增加)(非地区管理员)
        $serviceForADC = CountSearch::getServiceForAD($startDate,$endDate,$city_allow,$chargeSql);
        //服务新增（非一次性 和 一次性)(非地区管理员)
        $serviceAddForNYC = CountSearch::getServiceAddForNY($startDate,$endDate,$city_allow,$chargeSql);
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($startDate,$endDate,$city_allow);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($startDate,$endDate,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($startDate,$endDate,$city_allow);
        //服务新增（一次性)(上月)
        $monthServiceAddForY = CountSearch::getServiceAddForY($monthStartDate,$monthEndDate,$city_allow);

        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->start_date,$city);

            if(key_exists($city,$serviceAddForNY)){
                $defMoreList["new_sum"]+=$serviceAddForNY[$city]["num_new"];
                $defMoreList["one_service"]+=$serviceAddForNY[$city]["num_new_n"];
                $defMoreList["u_sum"]+=$serviceAddForNY[$city]["num_new_n"];
            }
            $defMoreList["u_sum"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;

            $defMoreList["new_sum_n"]+=$defMoreList["u_sum"];//一次性新增需要加上U系统产品金额
            //上月一次性服务+新增（产品）
            $defMoreList["new_month_n"]+=key_exists($city,$monthServiceAddForY)?-1*$monthServiceAddForY[$city]:0;
            $defMoreList["new_month_n"]+=key_exists($city,$monthUInvMoney)?-1*$monthUInvMoney[$city]["sum_money"]:0;
            //上月生意额
            $defMoreList["last_u_actual"]+=key_exists($city,$uServiceMoneyLast)?$uServiceMoneyLast[$city]:0;
            $defMoreList["last_u_actual"]+=key_exists($city,$monthUInvMoney)?$monthUInvMoney[$city]["sum_money"]:0;
            //暂停、停止
            if(key_exists($city,$serviceForST)){
                $defMoreList["stop_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop"]:0;
                $defMoreList["pause_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_pause"]:0;
                $defMoreList["stop_sum_none"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11"] = -1*$defMoreList["stop_sum"]-$defMoreList["stop_2024_11"];
                //$defMoreList["stopSumOnly"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_month"]:0;
            }
            //恢复
            $defMoreList["resume_sum"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;
            //更改
            $defMoreList["amend_sum"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;
            //更改服务(只算增加)
            $defMoreList["num_update_add"]+=key_exists($city,$serviceForAD)?$serviceForAD[$city]:0;
            //更改服务(只算增加)(非地区管理员)
            $defMoreList["city_update_add"]+=key_exists($city,$serviceForADC)?$serviceForADC[$city]:0;
            //新增一次性、非一次性服務(排除城市负责人的金额)
            if(key_exists($city,$serviceAddForNYC)){
                $defMoreList["city_num_new"]+=$serviceAddForNYC[$city]["num_new"];
                $defMoreList["city_one_service"]+=$serviceAddForNYC[$city]["num_new_n"];
            }

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        if(Yii::app()->getComponent('user')!==null){
            $session = Yii::app()->session;
            $session['bonusMonth_c01'] = $this->getCriteria();
        }
        $this->u_load_data['load_end'] = time();
        return true;
    }

    protected function addTempForList(&$temp,$list,$ka_id){
        if(key_exists($ka_id,$list)){
            foreach ($list[$ka_id] as $key=>$item){
                if(key_exists($key,$temp)){
                    $temp[$key] = $item;
                }
            }
        }
    }

    protected function defMoreCity($city,$city_name){
        return array(
            "city"=>$city,
            "city_name"=>$city_name,
            "two_net"=>"",
            "comStopRate"=>0,//综合停單率
            "new_sum"=>0,//新增（非一次性）
            "num_update_add"=>0,//更改服务(只含新增)
            "num_growth"=>0,//净增长
            "one_service"=>0,//一次性服務
            "city_employee_name"=>0,//城市负责人（人事系统的员工）
            "city_employee_dept"=>0,//城市负责人职位
            "city_num_new"=>0,//新增（非一次性）(排除城市负责人的金额)
            "city_update_add"=>0,//更改服务(只含新增)(排除城市负责人的金额)
            "city_one_service"=>0,//一次性服務(排除城市负责人的金额)

            "new_month_n"=>0,//上月一次性服务+新增（产品）
            "last_u_actual"=>0,//服务生意额(上月)
            "u_sum"=>0,//U系统金额
            "new_sum_n"=>0,//一次性服务+新增（产品）
            "stop_sum"=>0,//终止
            "stop_sum_none"=>0,//终止(本条终止的前一条、后一条没有暂停、终止)
            "stop_2024_11"=>0,//终止(2024年12月份改版)
            "resume_sum"=>0,//恢复
            "pause_sum"=>0,//暂停
            "amend_sum"=>0,//更改
        );
    }

    protected function resetTdRow(&$list,$bool=false){
        //$newSum = $list["new_sum"]+$list["new_sum_n"];//所有新增总金额
        $list["city_employee_name"] = "-";
        $list["city_employee_dept"] = "-";
        if($bool){
            //$list["comStopRate"] = "-";
        }else{
            $cityList = CountSearch::getCityChargeList("'{$list["city"]}'");
            if($cityList){
                $cityList = $cityList[0];
                $employeeId = CountSearch::getEmployeeIDForUsername($cityList["incharge"]);
                $employeeList = CountSearch::getEmployeeListForID($employeeId);
                if(!empty($employeeList)){
                    $list["city_employee_name"] = $employeeList["name"]." ({$employeeList["code"]})";
                    $list["city_employee_dept"] = CountSearch::getDeptNameForDeptId($employeeList["position"]);
                }
            }
            $list["num_growth"]=0;
            $list["num_growth"]+=$list["new_sum"]+$list["new_sum_n"]+$list["new_month_n"];
            $list["num_growth"]+=$list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"];
            $list["num_growth"]+=$list["amend_sum"];
            if(date_format(date_create($this->end_date),'Y/m')>CountSearch::$stop_new_dt){
                $list["num_growth"]=$list["num_growth"]+$list["stop_2024_11"];
            }
        }

        $list["comStopRate"] = $list["stop_sum_none"]+$list["resume_sum"]+$list["pause_sum"]+$list["amend_sum"];
        $list["comStopRate"]/= 12;//
        $lastSum = $list["new_month_n"]+$list["last_u_actual"];
        $list["comStopRate"] = ComparisonForm::comparisonRate($list["comStopRate"],$lastSum);

        $list["two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["two_net"],"net");
    }

    //顯示提成表的表格內容
    public function bonusMonthHtml(){
        $html= '<table id="bonusMonth" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $num_growth = Yii::t('summary',"Net growth");
        if(date_format(date_create($this->end_date),'Y/m')>CountSearch::$stop_new_dt){
            $num_growth = Yii::t("summary","net 2024 11");
        }
        $topList=array(
            array("name"=>Yii::t('summary',"City"),"rowspan"=>3),//城市
            array("name"=>Yii::t('summary',"data from summary"),"background"=>"#f7fd9d",//来源系统-合同分析查询
                "colspan"=>array(
                    array(
                        "name"=>Yii::t('summary',"Signing status"),//签单情况
                        "colspan"=>array(
                            array("name"=>Yii::t('summary',"New(not single)")),//新增服务(除一次性服务)
                            array("name"=>Yii::t('summary',"Update(for add)")),//更改服务（只含新增）
                            array("name"=>$num_growth),//净增长
                        )
                    ),
                    array(
                        "name"=>Yii::t('summary',"New customer(service)"),//新增客户（服务）
                        "colspan"=>array(
                            array("name"=>Yii::t('summary',"one service")),//一次性服务
                        )
                    ),
                    array(
                        "name"=>Yii::t('summary',"Goal degree (base case)"),//目标完成度(base case)
                        "colspan"=>array(
                            array("name"=>Yii::t('summary',"Start Net")),//年初净增长
                        )
                    ),
                )
            ),//来源系统-合同分析查询
            array("name"=>Yii::t('summary',"data from comparison"),"background"=>"#fcd5b4",//来源系统-合同同比分析
                "colspan"=>array(
                    array(
                        "name"=>Yii::t('summary',"YTD Stop"),//YTD终止
                        "colspan"=>array(
                            array("name"=>Yii::t('summary',"Composite Stop Rate")),//综合停单率
                        )
                    )
                )
            ),//来源系统-合同同比分析
            array("name"=>Yii::t('summary',"city charge"),"background"=>"#f2dcdb",//城市负责人
                "colspan"=>array(
                    array(
                        "name"=>Yii::t('summary',"city charge detail"),//地区主管需要剔除个人销售业绩后取数
                        "colspan"=>array(
                            array("name"=>Yii::t('summary',"employee name")),//姓名
                            array("name"=>Yii::t('summary',"dept name")),//职位
                            array("name"=>Yii::t('summary',"New(not single)")),//新增服务(除一次性服务)
                            array("name"=>Yii::t('summary',"Update(for add)")),//更改服务（只含新增）
                            array("name"=>Yii::t('summary',"one service")),//一次性服务
                        )
                    )
                )
            ),//城市负责人
        );
        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $trThree="";
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
                    $threeCol=key_exists("colspan",$col)?$col['colspan']:array();
                    if(!empty($threeCol)){
                        foreach ($threeCol as $three){
                            $this->th_sum++;
                            $trThree.="<th style='{$style}'><span>".$three["name"]."</span></th>";

                        }
                    }else{
                        $this->th_sum++;
                    }
                    $threeColNum=count($threeCol);
                    $colNum+=$threeColNum;
                    $threeColNum = empty($threeColNum)?1:$threeColNum;
                    //$this->th_sum++;

                    if(key_exists("rowspan",$col)){
                        $trTwo.="<th colspan='{$threeColNum}' rowspan='{$col["rowspan"]}' style='{$style}'><span>".$col["name"]."</span></th>";
                    }else{
                        $trTwo.="<th colspan='{$threeColNum}' style='{$style}'><span>".$col["name"]."</span></th>";
                    }
                }
            }
            $colNum = empty($colNum)?1:$colNum;
            $trOne.="<th style='{$style}' colspan='{$colNum}'";
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $this->th_sum++;
        $html.="<tr>{$trOne}</tr><tr>{$trTwo}</tr><tr>{$trThree}</tr>";
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<=$this->th_sum;$i++){
            if($i==0){
                $width=70;
            }elseif ($i==6){
                $width=130;
            }elseif ($i==7){
                $width=110;
            }else{
                $width=90;
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
            $moData = key_exists("MO",$this->data)?$this->data["MO"]:array();
            unset($this->data["MO"]);//澳门需要单独处理
            $html.=$this->showServiceHtml($this->data);
            $html.=$this->showServiceHtmlForMO($moData);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
        }
        return $html;
    }
    //获取td对应的键名
    protected function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name","new_sum","num_update_add","num_growth","one_service","two_net_rate",
            "comStopRate","city_employee_name","city_employee_dept","city_num_new","city_update_add",
            "city_one_service"
        );
        return $bodyKey;
    }

    public static function showNum($num){
        $pre="";
        if (strpos($num," +")!==false){
            $pre=" +";
            $num = end(explode(" +",$num));
        }
        if (is_numeric($num)){
            $number = floatval($num);
            //$number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $pre.$number;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $clickTdList = $this->getClickTdList();
        $keyStrExp = array("two_net","stop_2024_11","stop_sum_none","resume_sum","pause_sum","amend_sum","new_month_n","last_u_actual");
        $html="";
        if(!empty($data)){
            $allRow = [];//总计(所有地区)
            foreach ($data as $regionList){
                $regionRow = [];//地区汇总
                foreach ($regionList["list"] as $cityList){
                    foreach ($keyStrExp as $keyExpItm){
                        $regionRow[$keyExpItm]=key_exists($keyExpItm,$regionRow)?$regionRow[$keyExpItm]:0;
                        $allRow[$keyExpItm]=key_exists($keyExpItm,$allRow)?$allRow[$keyExpItm]:0;
                        $regionRow[$keyExpItm]+=key_exists($keyExpItm,$cityList)?$cityList[$keyExpItm]:0;
                        if($cityList["add_type"]!=1){ //疊加的城市不需要重複統計
                            $allRow[$keyExpItm]+=key_exists($keyExpItm,$cityList)?$cityList[$keyExpItm]:0;
                        }
                    }
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
                        $text = ComparisonForm::showNum($text);
                        $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;
                        $class = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                        $title="";
                        if(key_exists($keyStr,$clickTdList)){
                            $class.=" td_detail";
                            $title=$clickTdList[$keyStr]["title"];
                        }
                        $html.="<td class='{$class}' data-title='{$title}' data-type='{$keyStr}' data-city='{$cityList['city']}'>";
                        $html.="<span>{$text}</span></td>";
                        $html.="</td>";
                    }
                    $html.="</tr>";
                }
                //地区汇总
                $regionRow["region"]=$regionList["region"];
                $regionRow["city"]=$regionList["region"];
                $regionRow["city_name"]=$regionList["region_name"];
                $html.=$this->printTableTr($regionRow,$bodyKey);
                $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            }
            //所有汇总
            $allRow["region"]="allRow";
            $allRow["city"]=$allRow["region"];
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }
    //將城市数据寫入表格(澳门)
    private function showServiceHtmlForMO($data){
        $bodyKey = $this->getDataAllKeyStr();
        $clickTdList = $this->getClickTdList();
        $html="";
        if(!empty($data)){
            foreach ($data["list"] as $cityList) {
                $this->resetTdRow($cityList);
                $html="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $text = ComparisonForm::showNum($text);
                    //$inputHide = TbHtml::hiddenField("excel[MO][{$keyStr}]",$text);
                    $this->downJsonText["excel"]['MO'][$keyStr]=$text;
                    $class = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                    $title="";
                    if(key_exists($keyStr,$clickTdList)){
                        $class.=" td_detail";
                        $title=$clickTdList[$keyStr]["title"];
                    }
                    $html.="<td class='{$class}' data-title='{$title}' data-type='{$keyStr}' data-city='{$cityList['city']}'>";
                    $html.="<span>{$text}</span></td>";
                    $html.="</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            //$tdClass = "";
            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
            $text = ComparisonForm::showNum($text);
            $this->downJsonText["excel"][$data['city']]["count"][]=$text;
            $html.="<td class='tr-end click-tr {$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
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
            $excelData = empty($excelData)?array():$excelData;
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("app","Bonus Month"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $this->tableTopHtml();//检查表头总共有多少个th
        $excel->th_num = $this->th_sum;
        $excel->setBonusHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Bonus Month"));
    }

    //获取年份
    public static function getYearList(){
        $year = date("Y");
        $list = array();
        for ($i=$year-4;$i<=$year+1;$i++){
            if($i>2022){
                $list[$i] = $i.Yii::t('summary'," Year");
            }
        }
        return $list;
    }
    //获取月份
    public static function getMonthList(){
        $list = array();
        for ($i=1;$i<=12;$i++){
            $list[$i] = $i.Yii::t('summary'," month");
        }
        return $list;
    }

    //需要顯示表格詳情的欄位
    protected function getClickTdList(){
        $strCharge = Yii::t('summary',"city charge");
        return array(
            //新增服务
            "new_sum"=>array("title"=>Yii::t("summary","New(not single)"),"fun"=>"ServiceNew"),
            //更改服务（只含新增）
            "num_update_add"=>array("title"=>Yii::t("summary","Update(for add)"),"fun"=>"ServiceAmendmentAdd"),
            //一次性服务
            "one_service"=>array("title"=>Yii::t("summary","one service"),"fun"=>"ServiceOne"),
            //新增服务(城市负责人)
            "city_num_new"=>array("title"=>$strCharge." - ".Yii::t("summary","New(not single)"),"fun"=>"CityServiceNew"),
            //更改服务（只含新增）(城市负责人)
            "city_update_add"=>array("title"=>$strCharge." - ".Yii::t("summary","Update(for add)"),"fun"=>"CityServiceAmendmentAdd"),
            //一次性服务(城市负责人)
            "city_one_service"=>array("title"=>$strCharge." - ".Yii::t("summary","one service"),"fun"=>"CityServiceOne"),
        );
    }
}