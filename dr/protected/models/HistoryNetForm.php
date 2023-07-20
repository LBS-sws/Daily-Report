<?php

class HistoryNetForm extends CFormModel
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
            $this->week_start = HistoryAddForm::getDateDiffForMonth($timer,6,$this->search_month,false);
            $this->week_day = HistoryAddForm::getDateDiffForDay($this->week_start,$this->week_end);

            $this->last_week_end = HistoryAddForm::getDateDiffForMonth($this->week_start,1,$this->search_month);
            $this->last_week_start = HistoryAddForm::getDateDiffForMonth($this->last_week_end,6,$this->search_month,false);
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
        $lastEndDate = $this->last_end_date;
        $weekStartDate = date("Y/m/d",$this->week_start);
        $weekEndDate = date("Y/m/d",$this->week_end);
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);

        //服务新增(本年)
        $serviceN = CountSearch::getServiceForTypeToMonth($endDate,$city_allow,"N");
        //服务新增(上一年)
        $lastServiceN = CountSearch::getServiceForTypeToMonth($lastEndDate,$city_allow,"N");
        //服务恢復(本年)
        $serviceR = CountSearch::getServiceForTypeToMonth($endDate,$city_allow,"R");
        //服务恢復(上一年)
        $lastServiceR = CountSearch::getServiceForTypeToMonth($lastEndDate,$city_allow,"R");
        //服务更改(本年)
        $serviceA = CountSearch::getServiceAToMonth($endDate,$city_allow);
        //服务更改(上一年)
        $lastServiceA = CountSearch::getServiceAToMonth($lastEndDate,$city_allow);
        //服务暫停、終止(本年)
        $serviceST = CountSearch::getServiceForSTToMonth($endDate,$city_allow);
        //服务暫停、終止(上一年)
        $lastServiceST = CountSearch::getServiceForSTToMonth($lastEndDate,$city_allow);
        //服务新增（一次性)(本年)
        $serviceAddForY = CountSearch::getServiceAddForYToMonth($endDate,$city_allow);
        //服务新增（一次性)(上一年)
        $lastServiceAddForY = CountSearch::getServiceAddForYToMonth($lastEndDate,$city_allow);
        //获取U系统的產品数据(本年)
        $uInvMoney = CountSearch::getUInvMoneyToMonth($endDate,$city_allow);
        //获取U系统的產品数据(上一年)
        $lastUInvMoney = CountSearch::getUInvMoneyToMonth($lastEndDate,$city_allow);
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getUServiceMoneyToMonth($endDate,$city_allow);
        //本週數據
        $serviceWeek = CountSearch::getServiceForAll($weekStartDate,$weekEndDate);
        //上週數據
        $lastServiceWeek = CountSearch::getServiceForAll($lastWeekStartDate,$lastWeekEndDate);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $this->addListForCity($defMoreList,$city,$serviceN);
            $this->addListForCity($defMoreList,$city,$lastServiceN);
            $this->addListForCity($defMoreList,$city,$serviceR);
            $this->addListForCity($defMoreList,$city,$lastServiceR);
            $this->addListForCity($defMoreList,$city,$serviceA);
            $this->addListForCity($defMoreList,$city,$lastServiceA);
            $this->addListForCity($defMoreList,$city,$serviceST);
            $this->addListForCity($defMoreList,$city,$lastServiceST);
            $this->addListForCity($defMoreList,$city,$serviceAddForY,1);
            $this->addListForCity($defMoreList,$city,$lastServiceAddForY,1);
            $this->addListForCity($defMoreList,$city,$uInvMoney,2);
            $this->addListForCity($defMoreList,$city,$lastUInvMoney,2);
            $this->addListForCity($defMoreList,$city,$uServiceMoney,3);

            $defMoreList["now_week"]+=key_exists($city,$serviceWeek)?$serviceWeek[$city]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?$lastServiceWeek[$city]:0;

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['historyNet_c01'] = $this->getCriteria();
        return true;
    }

    private function addListForCity(&$data,$city,$list,$type=""){
        if(key_exists($city,$list)){
            foreach ($list[$city] as $key=>$value){
                $dateStr = $key;
                switch ($type){
                    case 1://上月的一次性服務
                        $dateStr.="/01";
                        $dateStr = date("Y/m",strtotime($dateStr." + 1 months"));
                        $value*=-1;
                        break;
                    case 2://產品服務及上月的產品服務
                        //需要把本月的數據在下一個月減掉 且 本月也需要增加
                        $nextStr = "{$dateStr}/01";
                        $nextStr = date("Y/m",strtotime($nextStr." + 1 months"));
                        if(key_exists($nextStr,$data)){
                            $data[$nextStr]+=$value*-1;
                        }
                        //生意額需要加上U系統的產品數據
                        $uDateStr="u_".$dateStr;
                        $data[$uDateStr]+=$value;
                        break;
                    case 3://U系統的服務單
                        $dateStr ="u_".$dateStr;
                        break;
                }
                if(key_exists($dateStr,$data)){
                    $data[$dateStr]+=$value;
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
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//产品金额
            $dateStrTwo = $this->last_year."/{$month}";//产品金额
            $dateStrThree = "u_".$this->search_year."/{$month}";//生意额
            $arr[$dateStrOne]=0;
            $arr[$dateStrOne."_u"]=$arr[$dateStrOne];//U系統的產品金额
            $arr[$dateStrTwo]=0;
            $arr[$dateStrTwo."_u"]=$arr[$dateStrTwo];//U系統的產品金额
            //U系统的服務生意额
            $arr[$dateStrThree]=0;
        }
        $arr["now_average"]=0;//本年平均
        $arr["last_average"]=0;//上一年平均
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["growth"]="";//加速增长
        $arr["start_two_net"]=0;//年初目标
        $arr["two_net"]=0;//滚动目标
        $arr["start_result"]="";//达成目标(年初)
        $arr["result"]="";//达成目标(滚动)
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        if(!$bool){
            $list["now_week"]=($list["now_week"]/$this->week_day)*$this->month_day;
            $list["now_week"]=HistoryAddForm::historyNumber($list["now_week"]);
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=HistoryAddForm::historyNumber($list["last_week"]);
        }
        $list["start_two_net"]=HistoryAddForm::historyNumber($list["start_two_net"],$bool);
        $list["two_net"]=HistoryAddForm::historyNumber($list["two_net"],$bool);
        $list["now_average"]=0;
        $list["last_average"]=0;
        $list["u_average"]=0;
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"]);
        $list["start_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_net"]);
        $list["result"]=HistoryAddForm::comYes($list["now_week"],$list["two_net"]);
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $lastStr = $this->last_year."/{$month}";
            $uStr = "u_".$this->search_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$lastStr] = key_exists($lastStr,$list)?$list[$lastStr]:0;
            $list[$uStr] = key_exists($uStr,$list)?$list[$uStr]:0;
            $list[$nowStr] = HistoryAddForm::historyNumber($list[$nowStr],$bool);
            $list[$lastStr] = HistoryAddForm::historyNumber($list[$lastStr],$bool);
            $list[$uStr] = HistoryAddForm::historyNumber($list[$uStr],$bool);
            $list["now_average"]+=$list[$nowStr];
            $list["last_average"]+=$list[$lastStr];
            $list["u_average"]+=$list[$uStr];
        }
        $list["now_average"]=round($list["now_average"]/$this->search_month,2);
        $list["last_average"]=round($list["last_average"]/$this->search_month,2);
        $list["u_average"]=round($list["u_average"]/$this->search_month,2);
    }

    //顯示提成表的表格內容
    public function historyNetHtml(){
        $html= '<table id="historyNet" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"));
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>$this->last_year,"background"=>"#f7fd9d",
                "colspan"=>$monthArr
            ),//上一年
            array("name"=>$this->search_year,"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            ),//本年
            array("name"=>$this->search_year.Yii::t("summary","Actual monthly amount"),"background"=>"#FDE9D9",
                "colspan"=>$monthArr
            )//生意额
        );

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week")),//本周
                array("name"=>Yii::t("summary","last week")),//上周
                array("name"=>Yii::t("summary","growth")),//加速增长
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","Target contrast"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Target")),//年初目标
                array("name"=>Yii::t("summary","Start Target result")),//达成目标
                array("name"=>Yii::t("summary","Roll Target")),//滚动目标
                array("name"=>Yii::t("summary","Roll Target result")),//达成目标
            )
        );//目标对比

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
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
            "city_name"
        );
        $dateTwoList = array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $bodyKey[]=$this->last_year."/{$month}";
            $dateTwoList[]=$this->search_year."/{$month}";
            $dateThreeList[]="u_".$this->search_year."/{$month}";
        }
        $bodyKey[]="last_average";
        $dateTwoList[]="now_average";
        $dateThreeList[]="u_average";
        $bodyKey=array_merge($bodyKey,$dateTwoList,$dateThreeList);

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_two_net";
        $bodyKey[]="start_result";
        $bodyKey[]="two_net";
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
                    $inputHide = TbHtml::hiddenField("excel[MO][]",$text);
                    $html.="<td class='{$tdClass}'><span>{$text}</span>{$inputHide}</td>";
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
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);
                            if(strpos($keyStr,'/')!==false&&strpos($keyStr,'u_')===false){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' data-u='{$cityList[$keyStr."_u"]}'><span>{$text}</span>{$inputHide}</td>";
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
            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
            $inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][]",$text);
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
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("app","History Net")."（{$this->search_date}）");
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
        $excel->outExcel(Yii::t("app","History Net"));
    }
}