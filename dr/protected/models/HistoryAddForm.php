<?php

class HistoryAddForm extends CFormModel
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

    public $u_load_data=array();//查询时长数组
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
            $this->end_date = date("Y/m/t",$timer);
            $this->month_day = date("t",$timer);
            $this->last_year = $this->search_year-1;
            $this->last_start_date = $this->last_year."/01/01";
            $this->last_end_date = date("Y/m/t",strtotime($this->last_year."/{$this->search_month}/01"));

            $this->week_end = $timer;
            $this->week_start = self::getDateDiffForMonth($timer,6,$this->search_month,false);
            $this->week_day = self::getDateDiffForDay($this->week_start,$this->week_end);

            $this->last_week_end = self::getDateDiffForMonth($this->week_start,1,$this->search_month);
            $this->last_week_start = self::getDateDiffForMonth($this->last_week_end,6,$this->search_month,false);
            $this->last_week_day = self::getDateDiffForDay($this->last_week_start,$this->last_week_end);
        }
    }

    public static function getDateDiffForDay($startDate,$endDate){
        return ($endDate-$startDate)/(60*60*24)+1;
    }

    public static function getDateDiffForMonth($dateTimer,$day,$month,$bool=true){
        $date = date("Y/m/d",$dateTimer);
        $timer = strtotime("{$date} - {$day} day");
        $diffMonth = date("n",$timer);
        if($diffMonth===$month){
            $returnTime = $timer;
        }else{
            $year = intval($date);
            $returnTime = strtotime("{$year}/{$month}/01");
        }
        return $bool&&$returnTime===$dateTimer?strtotime("1999/01/01"):$returnTime;
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
        $this->u_load_data['load_start'] = time();
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);

        $this->u_load_data['u_load_start'] = time();
        //获取U系统的產品数据
        $nowInvList = CountSearch::getUInvMoneyToMonthEx($this->start_date,$this->end_date,$city_allow);
        //获取U系统的產品数据(上一年)
        $lastInvList = CountSearch::getUInvMoneyToMonthEx($this->last_start_date,$this->last_end_date,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        //服务新增(IA、IB、IC、OTHER)
        $nowDetailServiceList = CountSearch::getServiceForTypeAndTwoToMonth($this->end_date,$city_allow);
        //服务新增
        $nowServiceList = CountSearch::getServiceForTypeToMonth($this->end_date,$city_allow);
        //服务新增(上一年)
        $lastServiceList = CountSearch::getServiceForTypeToMonth($this->last_end_date,$city_allow);
        //服务新增(IA、IB、IC、OTHER)
        $lastDetailServiceList = CountSearch::getServiceForTypeAndTwoToMonth($this->last_end_date,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->start_date,$city);

            $this->addListForCity($defMoreList,$city,$nowDetailServiceList);
            $this->addListForCity($defMoreList,$city,$nowServiceList);
            $this->addListForCity($defMoreList,$city,$nowInvList,"U");
            $this->addListForCity($defMoreList,$city,$lastServiceList);
            $this->addListForCity($defMoreList,$city,$lastDetailServiceList);
            $this->addListForCity($defMoreList,$city,$lastInvList,"U");
            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['historyAdd_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    protected function addListForCity(&$data,$city,$list,$str=""){
        if(key_exists($city,$list)){
            foreach ($list[$city] as $key=>$value){
                $dateStr = $key;
                if(key_exists($dateStr,$data)){
                    $data[$dateStr]+=$value;
                }
                if($str==="U"&&key_exists($dateStr."_u",$data)){
                    $data[$dateStr."_u"]+=$value;
                }
                if($str==="U"&&key_exists($dateStr."_OTHER",$data)){
                    $data[$dateStr."_OTHER"]+=$value;
                }
            }
        }
    }
    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";
            $dateStrTwo = $this->last_year."/{$month}";
            $arr[$dateStrOne]=0;
            $arr[$dateStrOne."_IA"]=0;
            $arr[$dateStrOne."_IB"]=0;
            $arr[$dateStrOne."_IC"]=0;
            $arr[$dateStrOne."_OTHER"]=0;
            $arr[$dateStrOne."_u"]=$arr[$dateStrOne];
            $arr[$dateStrTwo]=0;
            $arr[$dateStrTwo."_IA"]=0;
            $arr[$dateStrTwo."_IB"]=0;
            $arr[$dateStrTwo."_IC"]=0;
            $arr[$dateStrTwo."_OTHER"]=0;
            $arr[$dateStrTwo."_u"]=$arr[$dateStrTwo];
        }
        $arr["now_average"]=0;//本年平均
        $arr["last_average"]=0;//上一年平均
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["growth"]="";//加速增长
        $arr["start_two_gross"]=0;//年初目标
        $arr["two_gross"]=0;//滚动目标
        $arr["start_result"]="";//达成目标(年初)
        $arr["result"]="";//达成目标(滚动)
        return $arr;
    }

    public static function comYes($startNum,$endNum,$bool=false){
        $startNum = floatval($startNum);
        $endNum = floatval($endNum);
        $bool = $bool||!empty($startNum);
        if($bool&&$startNum>=$endNum){
            return Yii::t("summary","Yes");
        }else{
            return "";
        }
    }

    public static function historyNumber($number,$bool=false){
        return round($number,2);
        if(!$bool){//2023-5-8不需要除以1000
            $number = is_numeric($number)?floatval($number):0;
            $number = round($number/1000);
        }
        return $number;
    }

    protected function resetTdRow(&$list,$bool=false){
        if(!$bool){
            $list["now_week"]=($list["now_week"]/$this->week_day)*$this->month_day;
            $list["now_week"]=self::historyNumber($list["now_week"]);
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=self::historyNumber($list["last_week"]);
        }
        $list["start_two_gross"]=self::historyNumber($list["start_two_gross"],$bool);
        $list["two_gross"]=self::historyNumber($list["two_gross"],$bool);
        $list["now_average"]=0;
        $list["last_average"]=0;
        $list["growth"]=self::comYes($list["now_week"],$list["last_week"]);
        $list["start_result"]=self::comYes($list["now_week"],$list["start_two_gross"]);
        $list["result"]=self::comYes($list["now_week"],$list["two_gross"]);
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $lastStr = $this->last_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$lastStr] = key_exists($lastStr,$list)?$list[$lastStr]:0;
            $list[$nowStr] = self::historyNumber($list[$nowStr],$bool);
            $list[$lastStr] = self::historyNumber($list[$lastStr],$bool);
            $list["now_average"]+=$list[$nowStr];
            $list["last_average"]+=$list[$lastStr];
        }
        $list["now_average"]=round($list["now_average"]/$this->search_month,2);
        $list["last_average"]=round($list["last_average"]/$this->search_month,2);
    }

    //顯示提成表的表格內容
    public function historyAddHtml(){
        $html= '<table id="historyAdd" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"),
                "colspan"=>array(
                    array("name"=>"IA"),//年初目标
                    array("name"=>"IB"),//达成目标
                    array("name"=>"IC"),//滚动目标
                    array("name"=>"其它"),//达成目标
                    array("name"=>"合计"),//达成目标
                )
            );
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"),"rowspan"=>2);
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>3),//城市
            array("name"=>$this->last_year,"background"=>"#f7fd9d",
                "colspan"=>$monthArr
            ),//上一年
            array("name"=>$this->search_year,"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            )//本年
        );

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week"),"rowspan"=>2),//本周
                array("name"=>Yii::t("summary","last week"),"rowspan"=>2),//上周
                array("name"=>Yii::t("summary","growth"),"rowspan"=>2),//加速增长
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","Target contrast"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Target"),"rowspan"=>2),//年初目标
                array("name"=>Yii::t("summary","Start Target result"),"rowspan"=>2),//达成目标
                array("name"=>Yii::t("summary","Roll Target"),"rowspan"=>2),//滚动目标
                array("name"=>Yii::t("summary","Roll Target result"),"rowspan"=>2),//达成目标
            )
        );//目标对比

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
                            $colNum++;
                            $this->th_sum++;
                            $trThree.="<th style='{$style}'><span>".$three["name"]."</span></th>";

                        }
                    }else{
                        $colNum++;
                        $this->th_sum++;
                    }
                    $threeColNum=count($threeCol);
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
            $trOne.="<th style='{$style}' colspan='{$colNum}' class='click-th'";
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
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=70;
            if($i==0){
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
            $html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name"
        );
        $dateTwoList = array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $bodyKey[]=$this->last_year."/{$month}"."_IA";
            $bodyKey[]=$this->last_year."/{$month}"."_IB";
            $bodyKey[]=$this->last_year."/{$month}"."_IC";
            $bodyKey[]=$this->last_year."/{$month}"."_OTHER";
            $bodyKey[]=$this->last_year."/{$month}";
            $dateTwoList[]=$this->search_year."/{$month}"."_IA";
            $dateTwoList[]=$this->search_year."/{$month}"."_IB";
            $dateTwoList[]=$this->search_year."/{$month}"."_IC";
            $dateTwoList[]=$this->search_year."/{$month}"."_OTHER";
            $dateTwoList[]=$this->search_year."/{$month}";
        }
        $bodyKey[]="last_average";
        $dateTwoList[]="now_average";
        $bodyKey=array_merge($bodyKey,$dateTwoList);

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_two_gross";
        $bodyKey[]="start_result";
        $bodyKey[]="two_gross";
        $bodyKey[]="result";

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
                    $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
                    //$inputHide = TbHtml::hiddenField("excel[MO][]",$text);
                    $this->downJsonText["excel"]['MO'][$keyStr]=$text;
                    $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
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
        }

        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
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
                            if($cityList["add_type"]!=1) { //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);

                            $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);

                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
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
            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
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
        $excel->colTwo=0;
        $excel->SetHeaderTitle(Yii::t("app","History Add")."（{$this->search_date}）");
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
        $excel->setHistoryAddHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","History Add"));
    }
}