<?php

class CheckInStaffForm extends CFormModel
{
    /* User Fields */
    public $search_start_date;//查詢開始日期
    public $search_end_date;//查詢結束日期
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
            array('search_start_date,search_end_date','safe'),
            array('search_start_date,search_end_date','required'),
            array('search_start_date','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        $this->start_date = General::toDate($this->search_start_date);
        $this->end_date = General::toDate($this->search_end_date);
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
            'search_start_date'=>$this->search_start_date,
            'search_end_date'=>$this->search_end_date
        );
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->data = array();
        $city_allow = Yii::app()->user->city_allow();
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $staffs=self::getEmployeeCodeByT($startDate,$endDate);
        //$staffs.=empty($staffs)?"":",";
        //$staffs.="410652,410474,410473,410157";
        $this->u_load_data['u_load_start'] = time();
        //签到签离员工统计
        $uCheckInStaff = CountSearch::getUCheckInStaff($startDate,$endDate,$staffs);
        $this->u_load_data['u_load_end'] = time();
        if(!empty($uCheckInStaff)){
            $data = array();
            foreach ($uCheckInStaff as $staffCode=>$list){
                $city = $list["city"];
                $temp = $this->defMoreCity();
                if(!key_exists($city,$data)){
                    $cityName = General::getCityName($city);
                    $data[$city]=array(
                        "city"=>$city,
                        "city_name"=>empty($cityName)?$city:$cityName,
                        "list"=>array()
                    );
                }
                $temp["city"] = $city;
                $temp["city_name"] = $data[$city]["city_name"];
                $temp["employee_id"] = $staffCode;
                $temp["employee_name"] = self::getEmployeeNameByCode($staffCode);
                foreach ($list as $key=>$item){
                    if(key_exists($key,$temp)){
                        $temp[$key] = $item;
                    }
                }
                $data[$city]["list"][]=$temp;
            }
            $this->data = $data;
        }
        $session = Yii::app()->session;
        $session['checkInStaff_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    public static function getEmployeeNameByCode($staffCode){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("code,name")->from("hr{$suffix}.hr_employee")
            ->where("code=:code",array(":code"=>$staffCode))->order("staff_status desc,table_type asc,id desc")
            ->queryRow();
        return $row?"{$row['name']} ({$row['code']})":$staffCode;
    }

    public static function getEmployeeCodeByT($startDate,$endDate){//获取技术员
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("a.code")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept b","a.position=b.id")
            ->where("b.name like '%技术%' and (
            (a.staff_status=0 and replace(ifnull(a.entry_time,'{$startDate}'),'-', '/')<='{$endDate}') or
            (a.staff_status=-1 and replace(ifnull(a.leave_time,'{$startDate}'),'-', '/')>='{$startDate}')
            )")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[]=$row["code"];
            }
        }
        return implode(",",$list);
    }

    //設置該城市的默認值
    private function defMoreCity(){
        return array(
            "city"=>"",
            "city_name"=>"",
            "employee_id"=>"",
            "employee_name"=>"",
            "check_count"=>0,//签到签离总次数
            "checkIn_abnormal_num"=>0,//异常打卡次数(签到)
            "checkIn_abnormal_ratio"=>0,//打卡异常率(签到)
            "checkOut_abnormal_num"=>0,//异常打卡次数(签离)
            "checkOut_abnormal_ratio"=>0,//打卡异常率(签离)
            "spend_LT_3minute_count"=>0,//数量(工单耗时<5分钟)
            "spend_LT_3minute_ratio"=>0,//占比(工单耗时<5分钟)
            "spend_GT_90minute_count"=>0,//数量(工单耗时>240分钟)
            "spend_GT_90minute_ratio"=>0,//占比(工单耗时>240分钟)
        );
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            if(!empty($list["check_count"])){
                $list["checkIn_abnormal_ratio"] = round($list["checkIn_abnormal_num"]/$list["check_count"],4);
                $list["checkIn_abnormal_ratio"] = ($list["checkIn_abnormal_ratio"]*100)."%";
                $list["checkOut_abnormal_ratio"] = round($list["checkOut_abnormal_num"]/$list["check_count"],4);
                $list["checkOut_abnormal_ratio"] = ($list["checkOut_abnormal_ratio"]*100)."%";
                $list["spend_LT_3minute_ratio"] = round($list["spend_LT_3minute_count"]/$list["check_count"],4);
                $list["spend_LT_3minute_ratio"] = ($list["spend_LT_3minute_ratio"]*100)."%";
                $list["spend_GT_90minute_ratio"] = round($list["spend_GT_90minute_count"]/$list["check_count"],4);
                $list["spend_GT_90minute_ratio"] = ($list["spend_GT_90minute_ratio"]*100)."%";
            }else{
                $list["checkIn_abnormal_ratio"]="";
                $list["checkOut_abnormal_ratio"]="";
                $list["spend_LT_3minute_ratio"]="";
                $list["spend_GT_90minute_ratio"]="";
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
            array("name"=>Yii::t("summary","Technical"),"rowspan"=>2),//技术员
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Check All Total"),"rowspan"=>2),//签到签离总次数
            array("name"=>Yii::t("summary","Check In"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Check Error Num")),//异常打卡次数
                    array("name"=>Yii::t("summary","Check Error Radio")),//打卡异常率
                )
            ),//签到
            array("name"=>Yii::t("summary","Check Out"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Check Error Num")),//异常打卡次数
                    array("name"=>Yii::t("summary","Check Error Radio")),//打卡异常率
                )
            ),//签离
            array("name"=>Yii::t("summary","Check 5 minute"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Number")),//数量
                    array("name"=>Yii::t("summary","Radio")),//占比
                )
            ),//工单耗时<5分钟
            array("name"=>Yii::t("summary","Check 240 minute"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Number")),//数量
                    array("name"=>Yii::t("summary","Radio")),//占比
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
            "employee_name","city_name","check_count",
            "checkIn_abnormal_num","checkIn_abnormal_ratio",
            "checkOut_abnormal_num","checkOut_abnormal_ratio",
            "spend_LT_3minute_count","spend_LT_3minute_ratio",
            "spend_GT_90minute_count","spend_GT_90minute_ratio",
        );
        return $bodyKey;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr,$row){
        $tdClass = "";
        if(strpos($text,'%')!==false){
            $rateNum = floatval($text);
            if($rateNum>=1&&$rateNum<=3){
                $tdClass ="info";
            }elseif ($rateNum>3&&$rateNum<=5){
                $tdClass ="warning";
            }elseif ($rateNum>5){
                $tdClass ="danger";
            }
        }

        return $tdClass;
    }

    //設置百分比顏色
    public static function showExcelNum($text,$keyStr,$row){
        if(strpos($text,'%')!==false){
            $rateNum = floatval($text);
            if($rateNum>=1&&$rateNum<=3){
                $text =array("bg"=>"c4e3f3","text"=>$text);
            }elseif ($rateNum>3&&$rateNum<=5){
                $text =array("bg"=>"fcf8e3","text"=>$text);
            }elseif ($rateNum>5){
                $text =array("bg"=>"ebcccc","text"=>$text);
            }
        }
        return $text;
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
                        $html.="<tr>";
                        foreach ($bodyKey as $keyStr){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$cityList);
                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                            $excelText = self::showExcelNum($text,$keyStr,$cityList);
                            $this->downJsonText["excel"][$regionList['city']]['list'][$cityList['employee_id']][$keyStr]=$excelText;
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["city"]=$regionList["city"];
                    $regionRow["city_name"]=$regionList["city_name"];
                    $regionRow["employee_name"]="统计";
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
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$data);
            //$text = ComparisonForm::showNum($text);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
            $excelText = self::showExcelNum($text,$keyStr,$data);
            $this->downJsonText["excel"][$data['city']]['count'][$keyStr]=$excelText;
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
        $titleName = Yii::t("app","Check In Staff");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setCheckWeekData($excelData);
        $excel->outExcel($titleName);
    }

}