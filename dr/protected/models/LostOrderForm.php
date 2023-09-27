<?php

class LostOrderForm extends CFormModel
{
    /* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
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
        $yearMonth = date("Y/m",strtotime($endDate));
        $monthStartDate = date("Y/m/01",strtotime($this->end_date));//本月的第一天
        $lastMonthStart = CountSearch::computeLastMonth($monthStartDate);//查询时间的上月（减法使用）
        $lastMonthEnd = CountSearch::computeLastMonth($endDate);//查询时间的上月（减法使用）
        //停止金额 = 合同同比分析里的 “ 终止服务 ” +  “ 上月一次性服务+新增产品 ”

        //获取U系统的服务数据
        $uServiceMoney = CountSearch::getUServiceMoneyToMonth($endDate,$city_allow,true);

        //终止服务(本年)
        $serviceT = CountSearch::getServiceForSTToMonth($endDate,$city_allow,"T");
        //一次性服务(本年)
        $monthServiceAddForY = CountSearch::getServiceAddForYToMonth($endDate,$city_allow);
        //产品(本年)
        $uInvMoney = CountSearch::getUInvMoneyToMonth($endDate,$city_allow);
        //新增产品（本年上月）
        $subServiceInv = CountSearch::getUInvMoney($lastMonthStart,$lastMonthEnd,$city_allow);
        //一次性服务（本年上月）
        $subServiceY = CountSearch::getServiceAddForY($lastMonthStart,$lastMonthEnd,$city_allow);

        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $this->addListForCity($defMoreList,$city,$serviceT);
            $this->addListForCity($defMoreList,$city,$monthServiceAddForY,1,$yearMonth);
            $this->addListForCity($defMoreList,$city,$uInvMoney,2,$yearMonth);
            $this->addListForCity($defMoreList,$city,$uServiceMoney,3,$yearMonth);
            $defMoreList[$yearMonth]+=key_exists($city,$subServiceInv)?-1*$subServiceInv[$city]["sum_money"]:0;
            $defMoreList[$yearMonth]+=key_exists($city,$subServiceY)?-1*$subServiceY[$city]:0;

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        return true;
    }

    protected function addListForCity(&$data,$city,$list,$type="",$endDate="1900/01"){
        if(key_exists($city,$list)){
            foreach ($list[$city] as $key=>$value){
                $dateStr = $key;
                switch ($type){
                    case 1://上月的一次性服務
                        $dateStr.="/01";
                        $dateStr = date("Y/m",strtotime($dateStr." + 1 months"));
                        //查询的月份不需要减少，需要特别处理
                        if($dateStr!=$endDate){
                            $value*=-1;
                        }else{
                            $value=0;
                        }
                        break;
                    case 2://產品服務及上月的產品服務
                        //生意額需要加上U系統的產品數據
                        $uDateStr="u_".$dateStr;
                        if(key_exists($uDateStr,$data)){
                            $data[$uDateStr]+=$value;
                        }
                        $dateStr.="/01";
                        $dateStr = date("Y/m",strtotime($dateStr." + 1 months"));
                        //查询的月份不需要减少，需要特别处理
                        if($dateStr!=$endDate){
                            $value*=-1;
                        }else{
                            $value=0;
                        }
                        break;
                    case 3://U系統的服務單
                        $dateStr ="u_".$dateStr;
                        break;
                    case 4://终止、暂停
                        $value*=-1;
                        break;
                }
                if(key_exists($dateStr,$data)){
                    $data[$dateStr]+=$value;
                }
            }
        }
    }

    //設置該城市的默認值
    protected function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
            "u_{$this->last_year}/12"=>0,//服务生意额上一年12月
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//产品金额
            $arr[$dateStrOne]=0;
            $arr['rate_'.$dateStrOne]=0;
            if($i!=$this->search_month){
                $arr['u_'.$dateStrOne]=0;
            }
        }
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        if(!$bool){
            for($i=1;$i<=$this->search_month;$i++){ //停单金额需要除以12
                $month = $i>=10?10:"0{$i}";
                $dateStrOne = $this->search_year."/{$month}";//停单金额
                $list[$dateStrOne]/=12;
                $list[$dateStrOne]=round($list[$dateStrOne]);
            }
        }
        for($i=1;$i<=$this->search_month;$i++){ //计算停单比率
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//停单金额
            if($i==1){
                $dateStrTwo = $this->last_year."/12";//服务金额
            }else{
                $uMonth = $i-1;
                $uMonth = $uMonth>=10?10:"0{$uMonth}";
                $dateStrTwo = $this->search_year."/{$uMonth}";//服务金额
            }
            $list["rate_".$dateStrOne]=empty($list["u_".$dateStrTwo])?0:$list[$dateStrOne]/$list["u_".$dateStrTwo];
            $list["rate_".$dateStrOne]=round($list["rate_".$dateStrOne],3);
            $list["rate_".$dateStrOne]=empty($list["rate_".$dateStrOne])?0:($list["rate_".$dateStrOne]*100)."%";
        }
    }

    //顯示提成表的表格內容
    public function lostOrderHtml(){
        $html= '<table id="lostOrder" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $monthArr = array();
        $uMonth = array(array("name"=>"12".Yii::t("summary","Month")));
        for($i=1;$i<=$this->search_month;$i++){
            if($i!=$this->search_month){
                $uMonth[]=array("name"=>$i.Yii::t("summary","Month"));
            }
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","monthly Actual amount"),"background"=>"#f7fd9d",
                "colspan"=>$uMonth
            ),//每月服务生意额
            array("name"=>Yii::t("summary","monthly stop amount"),"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            ),//每月终止客户
            array("name"=>Yii::t("summary","lost order rate"),"background"=>"#f2dcdb",
                "colspan"=>$monthArr
            ),//每月丢单率
        );

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $topList = $this->getTopArr();
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
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=75;
            if($i==0){
                $width=90;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public static function lostOrderNumber($number){
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
            "u_{$this->last_year}/12"
        );
        $nowArr = array();
        $rateArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            if($i!=$this->search_month){
                $bodyKey[]="u_".$this->search_year."/{$month}";
            }
            $nowArr[]=$this->search_year."/{$month}";
            $rateArr[]="rate_".$this->search_year."/{$month}";
        }
        $bodyKey = array_merge($bodyKey,$nowArr);
        $bodyKey = array_merge($bodyKey,$rateArr);

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
        $excel->SetHeaderTitle(Yii::t("app","Lost orders rate")."（{$this->search_date}）");

        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Lost orders rate"));
    }
}