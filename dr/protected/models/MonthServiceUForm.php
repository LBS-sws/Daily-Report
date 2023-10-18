<?php

class MonthServiceUForm extends CFormModel
{
	/* User Fields */
    public $start_date;
    public $end_date;
    public $search_date;//查询日期
    public $search_year;//查询年份
    public $search_month;//查询月份
    public $day_num;//本月查询天数
    public $month_num;//本月总天数
    public $end_key;//
    public $last_end_key;//

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
            $this->start_date = ($this->search_year-1)."/01/01";
            $this->end_date = date("Y/m/d",$timer);
            $this->search_month = date("n",$timer);
            $this->day_num = date("j",$timer);
            $this->month_num = date("t",$timer);

            $this->end_key = date("Y/m",$timer);
            $this->last_end_key = date("Y/m",strtotime($this->search_date." - 1 months"));
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
            'search_date'=>$this->search_date,
        );
    }

    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $monthStart = date("Y/m/01",strtotime($this->end_date));
        $monthEnd = $this->end_date;
        $lastStartDate = CountSearch::computeLastMonth($monthStart);//上月开始时间
        $lastEndDate = CountSearch::computeLastMonth($monthEnd);//上月结束时间
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($monthStart,$monthEnd,$city_allow);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($monthStart,$monthEnd,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($monthStart,$monthEnd,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($monthStart,$monthEnd,$city_allow);

        //本月产品
        $uInvMoney = CountSearch::getUInvMoney($monthStart,$monthEnd,$city_allow);
        //服务新增（一次性)(上月)
        $lastServiceAddForNY = CountSearch::getServiceAddForY($lastStartDate,$lastEndDate,$city_allow);
        //获取U系统的產品数据(上月)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);

        //获取U系统的產品数据
        $uInvMoneyMonth = CountSearch::getUInvMoneyToMonthEx($this->start_date,$this->end_date,$city_allow);
        //获取U系统的服务数据
        $uServiceMoneyMonth = CountSearch::getUServiceMoneyToMonthEx($this->start_date,$this->end_date,$city_allow);

        $tempList=$this->defMoreCity();
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$tempList;
            $defMoreList["city"]=$city;
            $defMoreList["city_name"]=$cityRow["city_name"];
            $defMoreList["add_type"] = $cityRow["add_type"];
            $defMoreList["service_add"]=key_exists($city,$serviceAddForNY)?$serviceAddForNY[$city]["num_new"]:0;
            $defMoreList["service_update"]=key_exists($city,$serviceForA)?$serviceForA[$city]:0;
            $defMoreList["service_stop"]=key_exists($city,$serviceForST)?(-1)*$serviceForST[$city]["num_stop"]:0;
            $defMoreList["service_pause"]=key_exists($city,$serviceForST)?(-1)*$serviceForST[$city]["num_pause"]:0;
            $defMoreList["service_recover"]=key_exists($city,$serviceForR)?$serviceForR[$city]:0;

            $defMoreList["add_u_now"]=key_exists($city,$serviceAddForNY)?$serviceAddForNY[$city]["num_new_n"]:0;
            $defMoreList["add_u_now"]=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            $defMoreList["add_u_last"]=key_exists($city,$lastServiceAddForNY)?(-1)*$lastServiceAddForNY[$city]:0;
            $defMoreList["add_u_last"]=key_exists($city,$lastUInvMoney)?(-1)*$lastUInvMoney[$city]["sum_money"]:0;

            $this->addListForCity($defMoreList,$city,$uInvMoneyMonth);
            $this->addListForCity($defMoreList,$city,$uServiceMoneyMonth);

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['monthServiceU_c01'] = $this->getCriteria();
        return true;
    }

    protected function addListForCity(&$data,$city,$list){
        if(key_exists($city,$list)){
            foreach ($list[$city] as $key=>$value){
                $dateStr = $key;
                if(key_exists($dateStr,$data)){
                    $data[$dateStr]+=$value;
                }
            }
        }
    }

    private function defMoreCity(){
        $arr = array(
            "city"=>"",
            "city_name"=>"",
            "service_add"=>0,//服务新增（不包含一次性）
            "service_update"=>0,//服务更改
            "service_stop"=>0,//服务终止
            "service_pause"=>0,//服务暂停
            "service_recover"=>0,//服务恢复
            "add_u_now"=>0,//本月一次性+产品
            "add_u_last"=>0,//上月一次性+产品
        );
        for ($i=0;$i<=24;$i++){
            $dateStr = date("Y/m",strtotime($this->start_date." + {$i} months"));
            if ($dateStr<=$this->end_key){
                $arr[$dateStr]=0;
            }else{
                break;
            }
        }
        $arr[$this->end_key."_EA"]=0;
        $arr[$this->end_key."_EB"]=0;
        for ($i=1;$i<$this->search_month;$i++){
            $arr["rate_".$i]=0;
        }
        $arr["rate_".$this->search_month."_EA"]=0;
        $arr["rate_".$this->search_month."_EB"]=0;

        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        $list[$this->end_key."_EA"]=$list[$this->end_key]/$this->day_num*$this->month_num;
        $list[$this->end_key."_EA"] = round($list[$this->end_key."_EA"],2);

        if(!$bool){
            //EB = (新增+更改+终止+暂停+恢复)/12 + ((本月一次性+产品)+(上月一次性+产品))
            $list[$this->end_key."_EB"] = $list[$this->last_end_key];
            $list[$this->end_key."_EB"]+=$list["service_add"];
            $list[$this->end_key."_EB"]+=$list["service_update"];
            $list[$this->end_key."_EB"]+=$list["service_stop"];
            $list[$this->end_key."_EB"]+=$list["service_pause"];
            $list[$this->end_key."_EB"]+=$list["service_recover"];
            $list[$this->end_key."_EB"]/=12;
            $list[$this->end_key."_EB"]+=$list["add_u_now"];
            $list[$this->end_key."_EB"]+=$list["add_u_last"];
            $list[$this->end_key."_EB"] = round($list[$this->end_key."_EB"],2);
        }
        for ($i=1;$i<$this->search_month;$i++){
            $key = $i<10?"0{$i}":$i;
            $lastStr = ($this->search_year-1)."/".$key;
            $nowStr = $this->search_year."/".$key;
            $list["rate_".$i]=$this->computeRate($list[$lastStr],$list[$nowStr]);
        }
        $key = $i<10?"0{$i}":$i;
        $lastStr = ($this->search_year-1)."/".$key;
        $nowStr = $this->end_key."_EA";
        $list["rate_".$this->search_month."_EA"]=$this->computeRate($list[$lastStr],$list[$nowStr]);
        $i--;
        $i=$i==0?1:$i;
        $key = $i<10?"0{$i}":$i;
        $lastStr = ($this->search_year-1)."/".$key;
        $nowStr = $this->end_key."_EB";
        $list["rate_".$this->search_month."_EB"]=$this->computeRate($list[$lastStr],$list[$nowStr]);
    }

    protected function computeRate($lastStr,$nowStr){
        if(!empty($lastStr)){
            $rate = round($nowStr/$lastStr,3);
            $rate = ($rate-1)*100;
            $rate.="%";
        }else{
            $rate = "";
        }
        return $rate;
    }

    //顯示提成表的表格內容
    public function monthServiceUHtml(){
        $html= '<table id="monthServiceU" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $monthLast = array();//上一年的列表
        $monthNow = array();//本年列表
        $rate = array();//对比列表
        for ($i=1;$i<=12;$i++){
            $monthLast[]=array("name"=>$i.Yii::t("summary"," month"));
            if($i<=$this->search_month){
                $monthNow[]=array("name"=>$i.Yii::t("summary"," month"));
            }
            if($i<$this->search_month){
                $rate[]=array("name"=>$i.Yii::t("summary"," month"));
            }
        }
        $rate[]=array("name"=>$this->search_month.Yii::t("summary"," month E(A)"));
        $rate[]=array("name"=>$this->search_month.Yii::t("summary"," month E(B)"));

        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>($this->search_year-1).Yii::t("summary"," Year"),"background"=>"#f7fd9d",
                "colspan"=>$monthLast
            ),//上一年
            array("name"=>$this->search_year.Yii::t("summary"," Year"),"background"=>"#fcd5b4",
                "colspan"=>$monthNow
            ),//本年
            array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>$this->search_month.Yii::t("summary"," month E(A)")),
                    array("name"=>$this->search_month.Yii::t("summary"," month E(B)")),
                )
            ),//本月预估
            array("name"=>Yii::t("summary","YoY growth"),"background"=>"#f7fd9d",
                "colspan"=>$rate
            ),//YoY growth
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
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=90;
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
            "city_name",
        );
        for ($i=0;$i<=24;$i++){
            $dateStr = date("Y/m",strtotime($this->start_date." + {$i} months"));
            if ($dateStr<=$this->end_key){
                $bodyKey[]=$dateStr;
            }else{
                break;
            }
        }
        $bodyKey[]=$this->end_key."_EA";
        $bodyKey[]=$this->end_key."_EB";
        for ($i=1;$i<$this->search_month;$i++){
            $bodyKey[]="rate_".$i;
        }
        $bodyKey[]="rate_".$this->search_month."_EA";
        $bodyKey[]="rate_".$this->search_month."_EB";

        return $bodyKey;
    }

    //設置百分比顏色
    private function getTdClassForRow($row){
        $tdClass = "";
        return $tdClass;
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
                    $tdClass = "";
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
                            $tdClass = "";
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

    protected function printTableTr($data,$bodyKey,$count=1){
        $this->resetTdRow($data,true,$count);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = "";
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
        $excel->SetHeaderTitle(Yii::t("app","Month Service U")."（{$this->search_date}）");
        $titleTwo = $this->start_date." ~ ".$this->end_date."\r\n";
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Month Service U"));
    }
}