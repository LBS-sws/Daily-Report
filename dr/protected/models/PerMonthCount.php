<?php

class PerMonthCount extends CFormModel
{
    /* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
    public $day_num=0;//查詢天數
    public $day_rate;//时间流失

    public $month_type;
    public $start_date;
    public $end_date;

    public $data=array();

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
            'day_num'=>Yii::t('summary','day num'),
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

    public function validateDate($attribute, $params) {
        if(!empty($this->search_date)){
            $timer = strtotime($this->search_date);
            $this->search_year = date("Y",$timer);
            $this->search_month = date("n",$timer);
            $i = ceil($this->search_month/3);//向上取整
            $this->month_type = 3*$i-2;

            $this->start_date = date("Y/m/01",$timer);
            $this->end_date = date("Y/m/d",$timer);
            $this->month_day = date("t",$timer);

            ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);

            $this->day_rate = ($this->day_num/$this->month_day)*100;
            $this->day_rate = round($this->day_rate)."%";
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

    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $monthStartDate = date("Y/m/01",strtotime($this->end_date));//本月的第一天
        $lastMonthStart = CountSearch::computeLastMonth($monthStartDate);//查询时间的上月（减法使用）
        $lastMonthEnd = CountSearch::computeLastMonth($endDate);//查询时间的上月（减法使用）

        //服务新增
        $serviceN = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"N");
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //终止服务（num_stop）、暂停服务（num_pause）
        $serviceST = CountSearch::getServiceForST($startDate,$endDate,$city_allow);
        //恢复服务(上一年)
        $serviceR = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"R");
        //更改服务(上一年)
        $serviceA = CountSearch::getServiceForA($startDate,$endDate,$city_allow);

        //新增产品（本年上月）
        $subServiceInv = CountSearch::getUInvMoney($lastMonthStart,$lastMonthEnd,$city_allow);
        //一次性服务（本年上月）
        $subServiceY = CountSearch::getServiceAddForY($lastMonthStart,$lastMonthEnd,$city_allow);

        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $defMoreList["num_add"]+=key_exists($city,$serviceN)?$serviceN[$city]:0;
            $defMoreList["num_add"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;

            $defMoreList["num_stop"]+=key_exists($city,$serviceST)?-1*$serviceST[$city]["num_stop"]:0;
            $defMoreList["num_stop"]+=key_exists($city,$subServiceInv)?-1*$subServiceInv[$city]["sum_money"]:0;
            $defMoreList["num_stop"]+=key_exists($city,$subServiceY)?-1*$subServiceY[$city]:0;

            $defMoreList["num_recover"]+=key_exists($city,$serviceST)?-1*$serviceST[$city]["num_pause"]:0;
            $defMoreList["num_recover"]+=key_exists($city,$serviceR)?$serviceR[$city]:0;
            $defMoreList["num_recover"]+=key_exists($city,$serviceA)?$serviceA[$city]:0;


            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['perMonth_c01'] = $this->getCriteria();
        return true;
    }

    //設置該城市的默認值
    protected function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "num_add"=>0,//新增
            "num_stop"=>0,//停止
            "num_recover"=>0,//净恢复
            "num_net"=>0,//净增长
            "start_two_gross"=>0,//年初滚动新生意
            "start_two_net"=>0,//年初滚动净增长
            "start_two_gross_rate"=>0,//年初滚动新生意目标
            "start_two_net_rate"=>0,//年初滚动净增长目标
            "day_rate"=>$this->day_rate,//时间流失
        );
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        /* 不需要计算天数
        $list["start_two_gross"] = $bool?$list["start_two_gross"]:ComparisonForm::resetNetOrGross($list["start_two_gross"],$this->day_num);
        $list["start_two_net"] = $bool?$list["start_two_net"]:ComparisonForm::resetNetOrGross($list["start_two_net"],$this->day_num);
        */
        if($bool){
            $list["day_rate"]=$this->day_rate;
        }
        $list["num_net"]=$list["num_add"]+$list["num_stop"]+$list["num_recover"];
        $list["start_two_gross_rate"] = ComparisonForm::comparisonRate($list["num_add"],$list["start_two_gross"]);
        $list["start_two_net_rate"] = ComparisonForm::comparisonRate($list["num_net"],$list["start_two_net"]);
    }

    //顯示提成表的表格內容
    public function perMonthCountHtml(){
        $html= '<table id="perMonthCount" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $dayName = $this->start_date." ~ ".$this->end_date;
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>$dayName,"background"=>"#f7fd9d",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","num add"),"background"=>"#f7fd9d"),
                    array("name"=>Yii::t("summary","num stop"),"background"=>"#f7fd9d"),
                    array("name"=>Yii::t("summary","num recover"),"background"=>"#f7fd9d"),
                    array("name"=>Yii::t("summary","num net"),"background"=>"#f7fd9d"),
                )
            ),//时间
            array("name"=>Yii::t("summary","monthly target"),"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","num add"),"background"=>"#fcd5b4"),
                    array("name"=>Yii::t("summary","num net"),"background"=>"#fcd5b4"),
                )
            ),//每月目标
            array("name"=>Yii::t("summary","monthly rate"),"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","num add"),"background"=>"#f2dcdb"),
                    array("name"=>Yii::t("summary","num net"),"background"=>"#f2dcdb"),
                )
            ),//目标达成
            array("name"=>Yii::t("summary","Per Month Count"),"rowspan"=>2),//时间流失
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
            $width=85;
            if($i==0){
                $width=90;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public static function perMonthCountNumber($number){
        return round($number,2);
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
    protected function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name",
            "num_add",
            "num_stop",
            "num_recover",
            "num_net",
            "start_two_gross",
            "start_two_net",
            "start_two_gross_rate",
            "start_two_net_rate",
            "day_rate"
        );
        return $bodyKey;
    }
    //將城市数据寫入表格(澳门)
    protected function showServiceHtmlForMO($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            foreach ($data["list"] as $cityList) {
                $this->resetTdRow($cityList);
                $html="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $tdClass = self::getTextColorForKeyStr($text,$keyStr);
                    //$inputHide = TbHtml::hiddenField("excel[MO][]",$text);
                    $this->downJsonText["excel"]['MO'][$keyStr]=$text;
                    $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = array('count'=>0);//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array();//地区汇总
                    $regionCount = count($regionList["list"]);
                    foreach ($regionList["list"] as $cityList) {
                        if($cityList["add_type"]!=1) { //疊加的城市不需要重複統計
                            $allRow['count']++;//叠加的城市数量
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
                            if($cityList["add_type"]!=1) { //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;

                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionList["region"];
                    $regionRow["city_name"]=$regionList["region_name"];
                    $html.=$this->printTableTr($regionRow,$bodyKey,$regionCount);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["region"]="allRow";
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey,$allRow['count']);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr){
        $tdClass = "";
        /*
        if(strpos($text,'%')!==false){
            if(!in_array($keyStr,array("new_rate","stop_rate","net_rate"))){
                $tdClass =floatval($text)<=60?"text-danger":$tdClass;
            }
            $tdClass =floatval($text)>=100?"text-green":$tdClass;
        }
        */
        return $tdClass;
    }

    protected function printTableTr($data,$bodyKey,$count=1){
        $this->resetTdRow($data,true,$count);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = self::getTextColorForKeyStr($text,$keyStr);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][]",$text);
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
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("summary","Per Month Count")."（{$this->search_date}）");

        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("summary","Per Month Count"));
    }
}