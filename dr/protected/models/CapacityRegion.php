<?php

class CapacityRegion extends CFormModel
{
    /* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $last_month;
    public $last_year;//上一年
    public $month_day;//本月的天數
    public $day_num=0;//本月查詢天數

    public $month_type;
    public $start_date;
    public $end_date;

    public $data=array();
    public $dt_list=array();

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
            $this->last_year = $this->search_year-1;
            $this->search_month = date("n",$timer);
            $this->last_month = date("Y/m",strtotime("{$this->search_year}/{$this->search_month}/01 - 1 months"));

            $this->start_date = $this->last_year."/01/01";
            $this->end_date = date("Y/m/d",$timer);
            $this->month_day = date("t",$timer);
            $this->day_num = date("j",$timer);
            $this->dt_list = array();
            $this->th_sum = 1;

            $i=0;
            do{
                $nowDate = date("Y/m",strtotime($this->start_date." + {$i} months"));
                if($nowDate."/01">$this->end_date){
                    $i=-1;
                }else{
                    $this->dt_list[]=$nowDate;
                    $this->th_sum++;
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
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        //服务新增(本年)
        $serviceN = CountSearch::getServiceForTypeToMonthEx($startDate,$endDate,$city_allow,"N");
        //获取U系统的產品数据(本年)
        $uInvMoney = CountSearch::getUInvMoneyToMonthEx($startDate,$endDate,$city_allow);
        //销售人员
        $salesList = SalesMonthCountForm::getSalesForHr($city_allow,$endDate);

        $defMoreArr= $this->defMoreArr();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $region = $cityRow["region"];
            $region_name = $cityRow["region_name"];
            if(!key_exists($region,$data)){
                $data[$region]["name"]=$region_name;
                $data[$region]["list"]=$defMoreArr;
            }
            $this->addListForCity($data[$region],$city,$serviceN,"arrMoney");
            $this->addListForCity($data[$region],$city,$uInvMoney,"arrMoney");
            $this->addListForCity($data[$region],$city,$salesList,"arrSales");

            if($cityRow["add_type"]==1&&key_exists($region,$citySetList)){//叠加(城市配置的叠加)
                $regionTwo = $citySetList[$region]["region"];
                if(!key_exists($regionTwo,$data)){
                    $data[$regionTwo]["name"]=$citySetList[$region]["region_name"];
                    $data[$regionTwo]["list"]=$defMoreArr;
                }
                $this->addListForCity($data[$regionTwo],$city,$serviceN,"arrMoney");
                $this->addListForCity($data[$regionTwo],$city,$uInvMoney,"arrMoney");
                $this->addListForCity($data[$regionTwo],$city,$salesList,"arrSales");
            }
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['capacity_c01'] = $this->getCriteria();
        return true;
    }

    //設置默認值
    protected function addListForCity(&$data,$city,$rows,$str){
        if(key_exists($city,$rows)){
            if($str==="arrMoney"){
                foreach ($rows[$city] as $key=>$value){
                    if(key_exists($key,$data["list"][$str])){
                        $data["list"][$str][$key]+=$value;
                    }
                }
            }else{
                foreach ($rows[$city] as $row){
                    $entry_time = date("Y/m",strtotime($row["entry_time"]));
                    $leave_time = $row['staff_status']==-1?date("Y/m",strtotime($row["leave_time"])):"2222/12";

                    foreach ($data["list"]["arrKey"] as $key){
                        if($entry_time<=$key&&$key<=$leave_time){
                            $data["list"]["arrSales"][$key]++;
                        }
                    }
                }
            }
        }
    }

    //設置默認值
    protected function defMoreArr(){
        $arrKey=array();
        $arrMoney=array();
        foreach ($this->dt_list as $item){
            $arrKey[$item]=$item;
            $arrMoney[$item]=0;
        }
        $arr=array(
            "arrKey"=>$arrKey,
            "arrMoney"=>$arrMoney,
            "arrSales"=>$arrMoney,
            "arrCapacity"=>$arrMoney,
        );
        return $arr;
    }

    protected function resetTdRow(&$list,$regionName){
        $endMonth = date("Y/m",strtotime($this->search_date));
        if($this->day_num!=$this->month_day&&key_exists($this->last_month,$list["arrSales"])){ //如果查询的不是整月，则查询上月
            $list["arrSales"][$endMonth] = $list["arrSales"][$this->last_month];
        }
        foreach ($list["arrKey"] as $key){
            $capacity = empty($list["arrSales"][$key])?0:$list["arrMoney"][$key]/$list["arrSales"][$key];
            $capacity = round($capacity);
            $list["arrCapacity"][$key] = $capacity;
        }
        array_unshift($list["arrKey"],$regionName);
        array_unshift($list["arrMoney"],"新增金额");
        array_unshift($list["arrSales"],"销售人数");
        array_unshift($list["arrCapacity"],"产能");
    }

    //顯示提成表的表格內容
    public function capacityRegionHtml(){
        $html= '<table id="capacityRegion" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        return array();
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $html="<thead>";
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
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

    public static function capacityRegionNumber($number){
        return round($number,2);
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $html="";
        if(!empty($data)){
            foreach ($data as $region=>$regionList){
                if(!empty($regionList["list"])) {
                    $regionName = $regionList["name"];//
                    $cityList = $regionList["list"];//
                    $this->resetTdRow($cityList,$regionName);
                    $html.=$this->printTableTr($region,$cityList,"arrKey",true);
                    $html.=$this->printTableTr($region,$cityList,"arrMoney");
                    $html.=$this->printTableTr($region,$cityList,"arrSales");
                    $html.=$this->printTableTr($region,$cityList,"arrCapacity");
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
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

    protected function printTableTr($region,$data,$keyStr,$bool=false){
        $data = $data[$keyStr];
        $html="<tr class='tr-end'>";
        foreach ($data as $text){
            $tdClass = $bool?"td-title":"";
            $this->downJsonText["excel"][$region][$keyStr][]=$text;
            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
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
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->th_num = $this->th_sum;
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Region Count")."（{$this->search_date}）");

        $excel->init();
        $excel->setCapacityData($excelData);
        $excel->outExcel(Yii::t("summary","Capacity Region Count"));
    }
}