<?php

class OutsourceForm extends CFormModel
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
	public $outsource_year;
    public $month_start_date;
    public $month_end_date;
    public $last_month_start_date;
    public $last_month_end_date;

    public static $con_list=array("one_gross","one_net","two_gross","two_net","three_gross","three_net");

    public $data=array();
    public $dataTwo=array();
    public $outCity=array();//含有数据的外包城市

	public $th_sum=2;//所有th的个数

    public $downJsonText='';

    protected $class_type="NONE";//类型 NONE:普通  KA:KA
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

    protected function computeDate(){
        $this->start_date = empty($this->start_date)?date("Y/01/01"):$this->start_date;
        $this->end_date = empty($this->end_date)?date("Y/m/t"):$this->end_date;
        $this->outsource_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));

        $this->last_month_start_date = CountSearch::computeLastMonth($this->start_date);
        $this->last_month_end_date = CountSearch::computeLastMonth($this->end_date);
    }

    protected function getOutStaffList($city_allow){
        //外聘、业务承揽、外包商
        $nullDate = "2000/01/01";
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.city,a.code,a.name,a.staff_status,b.name as position_name")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept b","a.position=b.id")
            ->where("a.table_type in (3,4,5) and a.city in ({$city_allow}) and
             replace(ifnull(a.entry_time,'{$nullDate}'),'-', '/')<='{$this->end_date}' and
             (
            a.staff_status!=-1 or
            (replace(ifnull(a.leave_time,'{$nullDate}'),'-', '/')>='{$this->start_date}' and a.staff_status=-1)
            )")
            ->order("a.city asc,a.position asc,a.id asc")
            ->queryAll();
        $staffList=array();
        $staffCodeList=array();
        if($rows){
            foreach ($rows as $row){
                $cityCode = "".$row["city"];
                if(!in_array($cityCode,$this->outCity)){
                    $this->outCity[]=$cityCode;
                }
                $staffCode = "".$row["code"];
                $row["name"] = $row["name"]."({$staffCode})";
                if($row["staff_status"]==-1){
                    $row["name"].=" - 已离职";
                }
                if(!key_exists($staffCode,$staffList)){
                    $staffCodeList[] = $staffCode;
                }
                $staffList[$staffCode] = $row;
            }
        }
        if(empty($staffCodeList)){//如果为空，强制查询某个员工
            $staffCodeList = array(0);
        }
        return array("staffList"=>$staffList,"staffCodeList"=>$staffCodeList);
    }

    public static function getEndRegionList($city,$citySetList,$num=0){
        $num++;
        $regionCode = "";
        $regionName = "";
        if(key_exists($city,$citySetList)){
            if($citySetList[$city]["add_type"]==1&&$num<5){//叠加,最多循环5次
                return self::getEndRegionList($citySetList[$city]["region_code"],$citySetList,$num);
            }else{
                $regionCode = $citySetList[$city]["region_code"];
                $regionName = $citySetList[$city]["region_name"];
            }
        }
        return array("region_code"=>$regionCode,"region_name"=>$regionName);
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $data = array();
        $dataTwo = array();
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $this->computeDate();
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        //获取外包员工
        $staffAllList = self::getOutStaffList($city_allow);
        $staffCodeStr = implode(",",$staffAllList["staffCodeList"]);
        $uServiceType = $this->search_type==3?1:0;//当日期查询时，根据日期查询

        $this->u_load_data['u_load_start'] = time();
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getUServiceMoney($startDate,$endDate,$city_allow,$uServiceType);
        //获取外包员工的服务金额(详情金额)
        $outStaffMoney = CountSearch::getOutsourceServiceMoney($startDate,$endDate,$staffCodeStr,$city_allow,$uServiceType);
        //获取地区的外包金额(总金额)
        $outsourceMoney=CountSearch::getOutsourceCountMoney($startDate,$endDate,$staffCodeStr,$city_allow,$uServiceType);
        $this->u_load_data['u_load_end'] = time();

        $endRegionList = array();//城市最终归属的区域

        foreach ($staffAllList["staffList"] as $staffList){ //外包员工表格
            $staffCode = "".$staffList["code"];
            $cityCode = "".$staffList["city"];
            if(!key_exists($cityCode,$endRegionList)){
                $endRegionList[$cityCode] = self::getEndRegionList($cityCode,$citySetList);
            }
            $regionList = $endRegionList[$cityCode];
            $defStaffList=$this->defMoreStaff($staffCode,$staffList["name"]);
            $defStaffList["city"]=$cityCode;
            $defStaffList["city_name"]=key_exists($cityCode,$citySetList)?$citySetList[$cityCode]["city_name"]:$cityCode;
            $defStaffList["region_code"]=key_exists($cityCode,$citySetList)?$citySetList[$cityCode]["region_code"]:"none";
            $defStaffList["region_name"]=key_exists($cityCode,$citySetList)?$citySetList[$cityCode]["region_name"]:"";
            $defStaffList["position_name"]=$staffList["position_name"];
            $money = 0;
            if(key_exists($staffCode,$outStaffMoney)&&!empty($outStaffMoney[$staffCode])){
                foreach ($outStaffMoney[$staffCode] as $staffCity=>$staffCityMoney){
                    if($staffCity==$cityCode){
                        $money = $staffCityMoney===false?0:$staffCityMoney;
                        continue;
                    }else{
                        if($staffCityMoney!==false){
                            if(!in_array($staffCity,$this->outCity)){
                                $this->outCity[]=$staffCity;
                            }
                            $exprDef = $defStaffList;
                            $exprDef["city"] = $staffCity;
                            $exprDef["city_name"]=key_exists($staffCity,$citySetList)?$citySetList[$staffCity]["city_name"]:$staffCity;
                            $exprDef["region_code"]=key_exists($staffCity,$citySetList)?$citySetList[$staffCity]["region_code"]:"";
                            $exprDef["region_name"]=key_exists($staffCity,$citySetList)?$citySetList[$staffCity]["region_name"]:"";
                            $exprDef["service_money"]=$staffCityMoney;
                            if(!key_exists($staffCity,$endRegionList)){
                                $endRegionList[$staffCity] = self::getEndRegionList($staffCity,$citySetList);
                            }
                            $this->pushDataTwoForArr($dataTwo,$exprDef,$endRegionList[$staffCity]);
                        }
                    }
                }
            }
            $defStaffList["service_money"]=$money;
            $this->pushDataTwoForArr($dataTwo,$defStaffList,$regionList);
        }


        foreach ($citySetList as $cityRow){ //外包城市表格
            $city = $cityRow["code"];
            if(in_array($city,$this->outCity)){
                $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
                $defMoreList["add_type"] = $cityRow["add_type"];

                $defMoreList["u_actual_money"]+=key_exists($city,$uServiceMoney)?$uServiceMoney[$city]:0;
                $defMoreList["outsource_money"]+=key_exists($city,$outsourceMoney)?$outsourceMoney[$city]:0;

                RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
            }
        }

        $this->data = $data;
        $this->dataTwo = $dataTwo;
        $session = Yii::app()->session;
        $session['outsource_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    private function pushDataTwoForArr(&$dataTwo,$arr,$regionList){
        if(!key_exists($regionList["region_code"],$dataTwo)){
            $dataTwo[$regionList["region_code"]]=array(
                "region"=>$regionList["region_code"],
                "region_name"=>$regionList["region_name"],
                "list"=>array(),
            );
        }
        $dataTwo[$regionList["region_code"]]["list"][]=$arr;
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_actual_money"=>0,//实际服务金额(不包含产品金额)
            "outsource_money"=>0,//外包服务总金额
            "outsource_rate"=>0,//外包比例
            "outsource_cost"=>0,//外包成本
            "outsource_cost_rate"=>0,//外包成本/外包服务总金额%
        );
        return $arr;
    }

    //設置员工的默認值
    private function defMoreStaff($staffCode,$staffName){
        $arr=array(
            "add_type"=>0,
            "staff_code"=>$staffCode,
            "staff_name"=>$staffName,
            "city"=>"",
            "city_name"=>"",
            "region_code"=>"",
            "region_name"=>"",
            "position_name"=>0,//职位
            "service_money"=>0,//服务金额
        );
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$type=1){
        //"city_name","u_actual_money","outsource_money","outsource_rate","outsource_cost","outsource_cost_rate"
        if($type==1){
            $list["outsource_rate"] = self::comparisonRate($list["outsource_money"],$list["u_actual_money"]);
            $list["outsource_cost"] = "";
            $list["outsource_cost_rate"] = "";
        }else{
            if($bool){
                $list["region_name"] = "汇总：".$list["city_name"];
                $list["city_name"] = "";
                $list["position_name"] = "";
                $list["staff_name"] = "";
                $list["average"] = empty($list["countNum"])?0:$list["service_money"]/$list["countNum"];
                $list["average"] = round($list["average"],2);
            }
        }
    }

    //顯示提成表的表格內容
    public function outsourceHtml($type=1){
        $html= "<table id=\"outsource_{$type}\" class=\"table table-fixed table-condensed table-bordered table-hover\">";
        $html.=$this->tableTopHtml($type);
        $html.=$this->tableBodyHtml($type);
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Actual service amount"),"rowspan"=>2),//实际服务金额
            array("name"=>Yii::t("summary","Outsource amount"),"rowspan"=>2),//外包服务总金额
            array("name"=>Yii::t("summary","Outsource rate"),"rowspan"=>2),//外包比例
            array("name"=>Yii::t("summary","Outsource cost amount"),"rowspan"=>2),//外包成本
            array("name"=>Yii::t("summary","Outsource cost rate"),"rowspan"=>2),//外包成本/外包服务总金额%
        );

        return $topList;
    }

    private function getTopArrTwo(){
        $topList=array(
            array("name"=>Yii::t("summary","Area"),"rowspan"=>2),//区域
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Staff Name"),"rowspan"=>2),//员工
            array("name"=>Yii::t("summary","dept name"),"rowspan"=>2),//职位
            array("name"=>Yii::t("summary","Paid Amt"),"rowspan"=>2),//服务金额
        );

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml($type=1){
        $this->th_sum = 0;
        if($type==1){
            $topList = self::getTopArr();
        }else{
            $topList = self::getTopArrTwo();
        }
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
            if($i==6){
                $width=110;
            }else{
                $width=90;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml($type=1){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            if($type==1){
                $keyStr = "oneData";
                $html.=$this->showServiceHtml($this->data,$type);
            }else{
                $keyStr = "twoData";
                $html.=$this->showServiceHtml($this->dataTwo,$type);
            }
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel[{$keyStr}]",$this->downJsonText);
        }
        return $html;
    }
    //获取td对应的键名
    private function getDataAllKeyStr($type){
        if($type==1){
            $bodyKey = array(
                "city_name","u_actual_money","outsource_money","outsource_rate","outsource_cost","outsource_cost_rate"
            );
        }else{
            $bodyKey = array(
                "region_name","city_name","staff_name","position_name","service_money"
            );
        }
        return $bodyKey;
    }

    public static function comparisonRate($num,$numLast){
        $num = is_numeric($num)?floatval($num):0;
        $numLast = is_numeric($numLast)?floatval($numLast):0;
        if(empty($numLast)){
            return "";
        }else{
            $rate = ($num/$numLast);
            $rate = round($rate,3)*100;
            return $rate."%";
        }
    }
    //設置百分比顏色
    public static function showNum($keyStr,$num){
        if (strpos($num,'%')!==false){
            $number = floatval($num);
            $number=sprintf("%.1f",$number)."%";
        }elseif (is_numeric($num)){
            $number = floatval($num);
            $number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $number;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr){
        $tdClass = "";

        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data,$type){
        $bodyKey = $this->getDataAllKeyStr($type);
        $html="";
        if(!empty($data)){
            $allRow = array("countNum"=>0);//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array("countNum"=>0);//地区汇总
                    foreach ($regionList["list"] as $tdStr=>$cityList) {
                        $allRow["countNum"]++;
                        $regionRow["countNum"]++;
                        $this->resetTdRow($cityList,false,$type);
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
                            $tdClass = OutsourceForm::getTextColorForKeyStr($text,$keyStr);
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $text = OutsourceForm::showNum($keyStr,$text);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$tdStr][$keyStr]=$text;

                            $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionList["region"];
                    $regionRow["city_name"]=$regionList["region_name"];
                    $html.=$this->printTableTr($regionRow,$bodyKey,$type);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["region"]="allRow";
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey,$type);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey,$type){
        $this->resetTdRow($data,true,$type);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = OutsourceForm::getTextColorForKeyStr($text,$keyStr);
            $text = OutsourceForm::showNum($keyStr,$text);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
            $this->downJsonText["excel"][$data['region']]['count'][$keyStr]=$text;
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
        }
        $html.="</tr>";
        if($type!=1){
            $html.="<tr class='tr-end click-tr'>";
            foreach ($bodyKey as $keyStr){
                $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
                $text = $keyStr=="region_name"?"平均值：":$text;
                $text = $keyStr=="service_money"?$data["average"]:$text;
                $tdClass = OutsourceForm::getTextColorForKeyStr($text,$keyStr);
                $text = OutsourceForm::showNum($keyStr,$text);
                //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
                $this->downJsonText["excel"][$data['region']]['average'][$keyStr]=$text;
                $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
            }
            $html.="</tr>";
        }
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
        $oneData = key_exists("oneData",$excelData)?$excelData["oneData"]:array();
        $twoData = key_exists("twoData",$excelData)?$excelData["twoData"]:array();
        if(!is_array($oneData)){
            $oneData = json_decode($oneData,true);
            $oneData = key_exists("excel",$oneData)?$oneData["excel"]:array();
        }
        if(!is_array($twoData)){
            $twoData = json_decode($twoData,true);
            $twoData = key_exists("excel",$twoData)?$twoData["excel"]:array();
        }
        $this->validateDate("","");
        $this->outsource_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        $headList = $this->getTopArr();
        $headListTwo = $this->getTopArrTwo();
        $excel = new DownSummary();
        $titleName = Yii::t("app","Outsource");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->colTwo=6;
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($oneData,false);
        $excel->setSheetName($titleName);
        $titleName = Yii::t("summary","Outsource productivity");
        $excel->addSheet($titleName);
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->outHeader(1);
        $excel->setSummaryHeader($headListTwo);
        $excel->setSummaryData($twoData,false);
        $excel->outExcel($titleName);
    }

    protected function clickList(){
        return array(
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

    public static function getSelectType(){
        $arr = array();
        $arr[1]=Yii::t("summary","search quarter");//季度
        $arr[2]=Yii::t("summary","search month");//月度
        $arr[3]=Yii::t("summary","search day");//日期
        return $arr;
    }
}