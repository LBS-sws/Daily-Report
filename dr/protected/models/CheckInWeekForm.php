<?php

class CheckInWeekForm extends CFormModel
{
    /* User Fields */
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
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,end_date','safe'),
            array('start_date,end_date','required'),
            array('start_date','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        $this->start_date = General::toDate($this->start_date);
        $this->end_date = General::toDate($this->end_date);
        if($this->end_date<$this->start_date){
            $this->addError($attribute, "查询时间异常");
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
            'end_date'=>$this->end_date,
            'start_date'=>$this->start_date
        );
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->data = array();
        $city_allow = Yii::app()->user->city_allow();
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $city_allow = array_keys($citySetList);
        $city_allow = implode(",",$city_allow);
        $this->u_load_data['u_load_start'] = time();
        //签到签离统计
        $uCheckInWeek = CountSearch::getUCheckInWeek($startDate,$endDate,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        if($citySetList){
            $data = array();
            foreach ($citySetList as $cityRow){
                $regionCode = $cityRow["region_code"];
                $cityCode = $cityRow["code"];
                if(!key_exists($regionCode,$data)){
                    $data[$regionCode]=array(
                        "regionCode"=>$regionCode,
                        "regionName"=>$cityRow["region_name"],
                        "list"=>array(),
                    );
                }
                if(isset($uCheckInWeek[$cityCode])){
                    foreach ($uCheckInWeek[$cityCode] as $officeID=>$uCityRow){
                        $temp = $this->defMoreCity();
                        $this->addTempByList($temp,$uCityRow);
                        if($officeID==$cityCode){
                            $temp["city"] = $cityCode;
                            $temp["city_name"] =  $cityRow["city_name"];
                            $data[$regionCode]["list"][$cityCode]=$temp;
                        }else{
                            $temp["city"] = $officeID;
                            $temp["city_name"] =  self::getOfficeNameByUID($officeID);
                            if(!isset($data[$regionCode]["list"][$cityCode])){
                                $data[$regionCode]["list"][$cityCode]["list"]=array();
                            }
                            $data[$regionCode]["list"][$cityCode]["list"][]=$temp;
                        }
                    }
                }
            }
            $this->data = $data;
        }
        $session = Yii::app()->session;
        $session['checkInWeek_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    public function addTempByList(&$temp,$uLists){
        foreach ($uLists as $key=>$lists){
            foreach ($lists as $itemKey=>$itemVlaue){
                $uKey = $key."_".$itemKey;
                if(key_exists($uKey,$temp)){
                    $temp[$uKey] = $itemVlaue;
                }
            }
        }
    }

    public static function getOfficeNameByUID($u_office_id){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("hr{$suffix}.hr_office")
            ->where("u_id=:u_id",array(":u_id"=>$u_office_id))
            ->queryRow();
        return $row?$row['name']:$u_office_id;
    }

    //設置該城市的默認值
    private function defMoreCity(){
        $moreList = array(
            "check_count"=>0,//签到签离总次数
            "checkIn_forgot_num"=>0,//忘记打卡次数(签到)
            "checkIn_forgot_ratio"=>"",//忘记打卡异常率(签到)
            "checkIn_address_num"=>0,//地址错误次数(签到)
            "checkIn_address_ratio"=>"",//地址错误占比(签到)
            "checkIn_positioning_num"=>0,//定位错误次数(签到)
            "checkIn_positioning_ratio"=>"",//定位错误占比(签到)
            "checkOut_forgot_num"=>0,//忘记打卡次数(签离)
            "checkOut_forgot_ratio"=>"",//忘记打卡异常率(签离)
            "checkOut_address_num"=>0,//地址错误次数(签离)
            "checkOut_address_ratio"=>"",//地址错误占比(签离)
            "checkOut_positioning_num"=>0,//定位错误次数(签离)
            "checkOut_positioning_ratio"=>"",//定位错误占比(签离)
            "spend_LT_5minute_count"=>0,//数量(工单耗时<5分钟)
            "spend_LT_5minute_ratio"=>"",//占比(工单耗时<5分钟)
            "spend_GT_240minute_count"=>0,//数量(工单耗时>240分钟)
            "spend_GT_240minute_ratio"=>"",//占比(工单耗时>240分钟)
        );
        $list = array(
            "city"=>"",
            "city_name"=>"",
        );
        foreach ($moreList as $key=>$value){
            $list["week_".$key]=$value;
            $list["lastWeek_".$key]=$value;
        }
        return $list;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            $ratioList = array(
                "checkIn_forgot_ratio"=>"checkIn_forgot_num",
                "checkIn_address_ratio"=>"checkIn_address_num",
                "checkIn_positioning_ratio"=>"checkIn_positioning_num",
                "checkOut_forgot_ratio"=>"checkOut_forgot_num",
                "checkOut_address_ratio"=>"checkOut_address_num",
                "checkOut_positioning_ratio"=>"checkOut_positioning_num",
                "spend_LT_5minute_ratio"=>"spend_LT_5minute_count",
                "spend_GT_240minute_ratio"=>"spend_GT_240minute_count",
            );
            foreach ($ratioList as $key=>$item){
                $list["week_".$key] = 0;
                $list["lastWeek_".$key] = 0;
                if(!empty($list["week_check_count"])){
                    $list["week_".$key] = round($list["week_".$item]/$list["week_check_count"],4);
                    $list["week_".$key] = ($list["week_".$key]*100)."%";
                }
                if(!empty($list["lastWeek_check_count"])){
                    $list["lastWeek_".$key] = round($list["lastWeek_".$item]/$list["lastWeek_check_count"],4);
                    $list["lastWeek_".$key] = ($list["lastWeek_".$key]*100)."%";
                }
            }
        }
    }

    //顯示提成表的表格內容
    public function comparisonHtml(){
        $html= '<table id="comparison" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Check All Total"),"rowspan"=>2),//签到签离总次数
            array("name"=>Yii::t("summary","Check In"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Check Error Forgot Num")),//忘记打卡次数
                    array("name"=>Yii::t("summary","Check Error Forgot Radio")),//忘记打卡次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                    array("name"=>Yii::t("summary","Check Error Address Num")),//地址错误次数
                    array("name"=>Yii::t("summary","Check Error Address Radio")),//地址错误次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                    array("name"=>Yii::t("summary","Check Error Position Num")),//定位错误次数
                    array("name"=>Yii::t("summary","Check Error Position Radio")),//定位错误次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                )
            ),//签到
            array("name"=>Yii::t("summary","Check Out"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Check Error Forgot Num")),//忘记打卡次数
                    array("name"=>Yii::t("summary","Check Error Forgot Radio")),//忘记打卡次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                    array("name"=>Yii::t("summary","Check Error Address Num")),//地址错误次数
                    array("name"=>Yii::t("summary","Check Error Address Radio")),//地址错误次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                    array("name"=>Yii::t("summary","Check Error Position Num")),//定位错误次数
                    array("name"=>Yii::t("summary","Check Error Position Radio")),//定位错误次数异常率
                    array("name"=>Yii::t("summary","Last Week")),//上周
                )
            ),//签离
            array("name"=>Yii::t("summary","Check 5 minute"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Number")),//数量
                    array("name"=>Yii::t("summary","Radio")),//占比
                    array("name"=>Yii::t("summary","Last Week")),//上周
                )
            ),//工单耗时<5分钟
            array("name"=>Yii::t("summary","Check 240 minute"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Number")),//数量
                    array("name"=>Yii::t("summary","Radio")),//占比
                    array("name"=>Yii::t("summary","Last Week")),//上周
                )
            ),//工单耗时>240分钟
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
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city"=>array("show"=>false,"addType"=>false),
            "city_name"=>array("show"=>true,"addType"=>false),
            "week_check_count"=>array("show"=>true,"addType"=>true),
            "lastWeek_check_count"=>array("show"=>false,"addType"=>true),
            "week_checkIn_forgot_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkIn_forgot_num"=>array("show"=>false,"addType"=>true),
            "week_checkIn_forgot_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkIn_forgot_ratio"=>array("show"=>true,"addType"=>false),
            "week_checkIn_address_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkIn_address_num"=>array("show"=>false,"addType"=>true),
            "week_checkIn_address_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkIn_address_ratio"=>array("show"=>true,"addType"=>false),
            "week_checkIn_positioning_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkIn_positioning_num"=>array("show"=>false,"addType"=>true),
            "week_checkIn_positioning_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkIn_positioning_ratio"=>array("show"=>true,"addType"=>false),
            "week_checkOut_forgot_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkOut_forgot_num"=>array("show"=>false,"addType"=>true),
            "week_checkOut_forgot_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkOut_forgot_ratio"=>array("show"=>true,"addType"=>false),
            "week_checkOut_address_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkOut_address_num"=>array("show"=>false,"addType"=>true),
            "week_checkOut_address_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkOut_address_ratio"=>array("show"=>true,"addType"=>false),
            "week_checkOut_positioning_num"=>array("show"=>true,"addType"=>true),
            "lastWeek_checkOut_positioning_num"=>array("show"=>false,"addType"=>true),
            "week_checkOut_positioning_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_checkOut_positioning_ratio"=>array("show"=>true,"addType"=>false),
            "week_spend_LT_5minute_count"=>array("show"=>true,"addType"=>true),
            "lastWeek_spend_LT_5minute_count"=>array("show"=>false,"addType"=>true),
            "week_spend_LT_5minute_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_spend_LT_5minute_ratio"=>array("show"=>true,"addType"=>false),
            "week_spend_GT_240minute_count"=>array("show"=>true,"addType"=>true),
            "lastWeek_spend_GT_240minute_count"=>array("show"=>false,"addType"=>true),
            "week_spend_GT_240minute_ratio"=>array("show"=>true,"addType"=>false),
            "lastWeek_spend_GT_240minute_ratio"=>array("show"=>true,"addType"=>false),
        );
        return $bodyKey;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            //last_u_all
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array();//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $this->resetTdRow($cityList);
                        $html.="<tr data-city='{$cityList['city']}'>";
                        foreach ($bodyKey as $keyStr=>$keyRow){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            if(!$keyRow["show"]){//不显示
                                continue;
                            }
                            $tdClass = CheckInStaffForm::getTextColorForKeyStr($text,$keyStr,$cityList);

                            $excelText = CheckInStaffForm::showExcelNum($text,$keyStr,$cityList);
                            $this->downJsonText["excel"][$regionList['regionCode']]['list'][$cityList['city']][$keyStr]=$excelText;

                            if($keyStr=="city_name"){
                                $tdClass.= " changeOffice";
                                $text = "<i class='fa fa-minus'></i>".$text;
                            }
                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                        }
                        $html.="</tr>";

                        if(isset($cityList["list"])){//办事处
                            foreach ($cityList["list"] as $officeList){
                                $this->resetTdRow($officeList);
                                $html.="<tr class='office-city-tr' data-city='{$cityList['city']}' data-type='hide' data-office='{$officeList['city']}'>";
                                foreach ($bodyKey as $keyStr=>$keyRow){
                                    $text = key_exists($keyStr,$officeList)?$officeList[$keyStr]:"0";
                                    if(!$keyRow["show"]){//不显示
                                        continue;
                                    }
                                    $tdClass = CheckInStaffForm::getTextColorForKeyStr($text,$keyStr,$officeList);

                                    $excelText = CheckInStaffForm::showExcelNum($text,$keyStr,$officeList);
                                    $this->downJsonText["excel"][$regionList['regionCode']]['list'][$officeList['city']][$keyStr]=$excelText;

                                    $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                                }
                                $html.="</tr>";
                            }
                        }
                    }
                    //地区汇总
                    $regionRow["city"]=$regionList["regionCode"];
                    $regionRow["city_name"]=$regionList["regionName"];
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr=>$keyRow){
            if(!$keyRow["show"]){//不显示
                continue;
            }
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = CheckInStaffForm::getTextColorForKeyStr($text,$keyStr,$data);

            $excelText = CheckInStaffForm::showExcelNum($text,$keyStr,$data);
            $this->downJsonText["excel"][$data['city']]['count'][$keyStr]=$excelText;
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
        $titleName = Yii::t("app","Check In Week");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setCheckWeekData($excelData);
        $excel->outExcel($titleName);
    }

}