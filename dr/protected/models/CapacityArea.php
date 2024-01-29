<?php

class CapacityArea extends CFormModel
{
    /* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
    public $day_num;//本月的天數
    public $last_month;//上一个月
    public $last_year;
    public $last_start_date;
    public $last_end_date;
    public $start_date;
    public $end_date;
    public $week_start;
    public $week_end;
    public $week_day;
    public $last_week_start;
    public $last_week_end;
    public $last_week_day;
    public $month_type;

    public $data=array();
    public $dt_list=array();

    public $th_sum=1;//所有th的个数
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

    public function validateDate($attribute, $params) {
        if(!empty($this->search_date)){
            $timer = strtotime($this->search_date);
            $this->search_year = date("Y",$timer);
            $this->search_month = date("n",$timer);
            $i = ceil($this->search_month/3);//向上取整
            $this->month_type = 3*$i-2;
            $this->start_date = $this->search_year."/01/01";
            $this->end_date = date("Y/m/d",$timer);
            $this->month_day = date("t",$timer);
            $this->day_num = date("j",$timer);
            $this->last_month = date("Y/m",strtotime("{$this->search_year}/{$this->search_month}/01 - 1 months"));
            $this->last_year = $this->search_year-1;
            $this->last_start_date = $this->last_year."/01/01";
            $this->last_end_date = date("Y/m/t",strtotime($this->last_year."/{$this->search_month}/01"));

            $this->week_end = $timer;
            $weekStart = HistoryAddForm::getDateDiffForMonth($timer,6,$this->search_month,false);
            //2023-09-07修改本周的逻辑：本月1号至查询日期为本周
            $this->week_start = strtotime("{$this->search_year}/{$this->search_month}/01");
            $this->week_day = HistoryAddForm::getDateDiffForDay($this->week_start,$this->week_end);

            $this->last_week_end = HistoryAddForm::getDateDiffForMonth($weekStart,1,$this->search_month);
            $this->last_week_start = strtotime("1999/01/01")==$this->last_week_end?$this->last_week_end:$this->week_start;
            $this->last_week_day = HistoryAddForm::getDateDiffForDay($this->last_week_start,$this->last_week_end);

            $i=0;
            do{
                $nowDate = date("Y/m",strtotime($this->start_date." + {$i} months"));
                if($nowDate."/01">$this->end_date){
                    $i=-1;
                }else{
                    $this->dt_list[]=$nowDate;
                }
                $i++;
            }while($i>0);
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
        $endDate = $this->end_date;
        $lastYearStart = $this->last_year."/01/01";
        $lastYearEnd = $this->last_year."/12/31";
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        //新增金额 = 合同同比分析里的 “ 新增(除一次性服务）” +  “ 一次性服务+新增（产品） ”

        //服务新增(本年)
        $serviceN = CountSearch::getServiceForTypeToMonth($endDate,$city_allow,"N");
        //获取U系统的產品数据(本年)
        $uInvMoney = CountSearch::getUInvMoneyToMonth($endDate,$city_allow);
        //服务新增(上週)
        $lastServiceWeek = CountSearch::getServiceForType($lastWeekStartDate,$lastWeekEndDate,$city_allow,"N");
        //获取U系统的產品数据(上週)
        $lastUInvMoneyWeek = CountSearch::getUInvMoney($lastWeekStartDate,$lastWeekEndDate,$city_allow);

        //销售人员
        $salesList = SalesMonthCountForm::getSalesForHr($city_allow,$endDate);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            //ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $this->addListForCity($defMoreList,$city,$serviceN);
            $this->addListForCity($defMoreList,$city,$uInvMoney);
            $this->addListForCity($defMoreList,$city,$salesList,"arrSales");

            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?$lastServiceWeek[$city]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastUInvMoneyWeek)?$lastUInvMoneyWeek[$city]["sum_money"]:0;

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['capacity_c01'] = $this->getCriteria();
        return true;
    }

    protected function addListForCity(&$data,$city,$list,$str=""){
        if(key_exists($city,$list)){
            if($str==="arrSales"){
                foreach ($list[$city] as $row){
                    $entry_time = date("Y/m",strtotime($row["entry_time"]));
                    $leave_time = $row['staff_status']==-1?date("Y/m",strtotime($row["leave_time"])):"2222/12";

                    foreach ($this->dt_list as $key){
                        if($entry_time<=$key&&$key<=$leave_time){
                            $data["sales_".$key]++;
                        }
                    }
                }
            }else{
                foreach ($list[$city] as $key=>$value){
                    $dateStr = $key;
                    if(key_exists($dateStr,$data)){
                        $data[$dateStr]+=$value;
                    }
                }
            }
        }
    }

    //設置該城市的默認值
    protected function defMoreCity($city,$city_name){
        $endMonth = date("Y/m",strtotime($this->search_date));
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//产品金额
            $arr[$dateStrOne]=0;
            $arr["sales_".$dateStrOne]=0;//销售人数
        }
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["mtd"]=0;//MTD
        $arr["month_money"]=0;//整月预估
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        $endMonth = date("Y/m",strtotime($this->search_date));
        if(!$bool){
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=PerMonth::perMonthNumber($list["last_week"]);
        }
        $lastNum = 0;
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$nowStr] = PerMonth::perMonthNumber($list[$nowStr]);

            if($this->search_month==$i&&$this->day_num!=$this->month_day){ //如果查询的不是整月，则查询上月
                $lastStr = "sales_".$this->last_month;
                if(!key_exists("sales_".$nowStr,$list)){//1月查询或许有问题
                    $list["sales_".$nowStr]=0;
                }
                $list["sales_".$nowStr] = key_exists($lastStr,$list)?$list[$lastStr]:$list["sales_".$nowStr];
            }
            $lastNum = $list[$nowStr];
        }
        $list["now_week"]=($lastNum/$this->week_day)*$this->month_day;
        $list["now_week"] = PerMonth::perMonthNumber($list["now_week"]);

        $list["mtd"]=empty($list["sales_".$endMonth])?0:$list[$endMonth]/$list["sales_".$endMonth];
        $list["mtd"]=round($list["mtd"]);
        $list["month_money"]=empty($list["sales_".$endMonth])?0:$list["now_week"]/$list["sales_".$endMonth];
        $list["month_money"]=round($list["month_money"]);
    }

    //顯示提成表的表格內容
    public function capacityAreaHtml(){
        $html= '<table id="capacityArea" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $monthArr = array();
        $monthArrTwo = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
            if($this->search_month!=$i||$this->day_num==$this->month_day){ //如果查询的不是整月，则查询上月
                $monthArrTwo[]=array("name"=>$i.Yii::t("summary","Month"));
            }
        }
        if(empty($monthArrTwo)){//查询1月有异常
            $monthArrTwo[]=array("name"=>"none");
        }
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>$this->search_year,"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            ),//本年
        );

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week")),//本周
                array("name"=>Yii::t("summary","last week")),//上周
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","sales num"),"background"=>"#FDE9D9",
            "colspan"=>$monthArrTwo
        );//销售人数

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," Month Capacity"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","MTD")),//MTD
                array("name"=>Yii::t("summary","month forecast")),//整月预估
            )
        );//销售人数

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

    public static function capacityAreaNumber($number){
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
            "city_name"
        );
        $arrTwo=array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $bodyKey[]=$this->search_year."/{$month}";
            if($this->search_month!=$i||$this->day_num==$this->month_day){ //如果查询的不是整月，则查询上月
                $arrTwo[]="sales_".$this->search_year."/{$month}";
            }
        }
        if(empty($arrTwo)){//查询1月有异常
            $arrTwo[]="sales_".$this->last_month;
        }

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey=array_merge($bodyKey,$arrTwo);
        $bodyKey[]="mtd";
        $bodyKey[]="month_money";

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
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Area Count")."（{$this->search_date}）");
        $titleTwo = $this->start_date." ~ ".$this->end_date."\r\n";
        $titleTwo.="本周:".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." ({$this->week_day})\r\n";
        $titleTwo.="上周:";
        if($this->last_week_end===strtotime("1999/01/01")){
            $titleTwo.="无";
        }else{
            $titleTwo.=date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." ({$this->last_week_day})";
        }
        $excel->SetHeaderString($titleTwo);

        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("summary","Capacity Area Count"));
    }
}