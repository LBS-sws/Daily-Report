<?php

class CheckStaffWeekForm extends CFormModel
{
    /* User Fields */
    public $start_date;
    public $end_date;
    public $condition;//筛选条件
    public $seniority_min=3;//年资（最小）
    public $seniority_max=9999;//年资（最大）
    public $staff_type=0;//员工类型 0：全部 1：专职 2：其他
    public $city;
    public $city_desc="全部";
    public $city_allow;

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
            'city'=>Yii::t('app','City'),
            'condition'=>Yii::t('summary','screening condition'),
            'seniority_min'=>Yii::t('summary','seniority（month）'),
            'staff_type'=>Yii::t('summary','Staff Type'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,end_date,city,staff_type,condition,seniority_min,seniority_max','safe'),
            array('start_date,end_date','required'),
            array('start_date','validateDate'),
            array('city','validateCity'),
        );
    }

    public function validateCity($attribute, $params) {
        if(empty($this->city)){
            $city_allow = Yii::app()->user->city_allow();
        }else{
            $city_allow = explode("~",$this->city);
            $city_allow = implode("','",$city_allow);
            $city_allow = "'{$city_allow}'";
        }
        $this->city_allow = $city_allow;
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
            'start_date'=>$this->start_date,
            'condition'=>$this->condition,
            'seniority_min'=>$this->seniority_min,
            'seniority_max'=>$this->seniority_max,
            'city_desc'=>$this->city_desc,
            'staff_type'=>$this->staff_type,
            'city'=>$this->city
        );
    }

    public function getStaffListByCode($staffCode,$endDate){
        $list = array("code"=>$staffCode,"name"=>"异常员工","table_type"=>1,"table_name"=>"否","entry_month"=>0,"level_type"=>3,"city"=>"none","dept_name"=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()
            ->select("a.code,a.table_type,a.staff_status,a.entry_time,g.name as dept_name,a.name,a.city,
            g.level_type,f.ka_bool")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept g","a.position = g.id")
            ->leftJoin("security{$suffix}.sec_city f","f.code = a.city")
            //需要评核类型：技术员 并且 参与评分差异
            ->where("a.code=:code",array(":code"=>$staffCode))
            ->order("a.staff_status desc,a.table_type asc,a.entry_time desc")
            ->queryRow();
        if($row){
            //1:技术员 2：技术主管 3：其它
            $list["level_type"]=empty($row["level_type"])?3:$row["level_type"];
            //员工在KA城市且是技术主管，强制转换成KA技术主管
            $list["level_type"]= $row["ka_bool"]==1&&$list["level_type"]==2?5:$list["level_type"];
            $entryMonth = strtotime($endDate)-strtotime($row["entry_time"]);
            $entryMonth/=24*60*60*30;
            $entryMonth = round($entryMonth);
            //在职月份
            $list["entry_month"] = $entryMonth;
            $list["city"] = $row["city"];
            $list["dept_name"] = $row["dept_name"];
            $list["name"] = $row["name"];
            $list["table_type"] = $row["table_type"]==1?1:3;
            $list["table_name"] = $row["table_type"]==1?"否":"是";//是否外包
        }
        return $list;
    }

    protected function resetUCheckStaffWeek($uCheckStaffWeek){
        $list = array();
        $conditionList = empty($this->condition)?array(1,2,3,4,5):$this->condition;
        if(!empty($uCheckStaffWeek)){
            foreach ($uCheckStaffWeek as $staffCode=>$row){
                $staffList = $this->getStaffListByCode($staffCode,$this->end_date);
                if(empty($this->staff_type)||$this->staff_type==$staffList["table_type"]){
                    $bool = true;//允许
                }else{
                    $bool=false;
                }
                if($bool&&$staffList["entry_month"]>=$this->seniority_min&&$staffList["entry_month"]<=$this->seniority_max){
                    $bool = true;
                }else{
                    $bool = false;
                }
                if($bool&&in_array($staffList["level_type"],$conditionList)){
                    $bool = true;
                }else{
                    $bool = false;
                }
                if(!$bool){
                    continue;//没达到条件，不统计
                }
                $cityCode = $staffList["city"];
                if(!key_exists($cityCode,$list)){
                    $list[$cityCode]=array();
                }
                $staffList["money"]=$row["money"];//工单总金额
                $staffList["days"]=$row["days"];//有效做单天数
                $staffList["jobCount"]=$row["jobCount"];//有效工单数
                $staffList["jobNumRate"]=empty($row["days"])?0:round($row["jobCount"]/$row["days"],4);//平均每日单数
                $list[$cityCode][]=$staffList;
            }
        }

        return $list;
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->data = array();
        $city_allow = $this->city_allow;
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $city_allow = array_keys($citySetList);
        $city_allow = implode(",",$city_allow);
        $this->u_load_data['u_load_start'] = time();
        //签到签离统计
        $uCheckStaffWeek = CountSearch::getUCheckWeekStaff($startDate,$endDate,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        $uList = $this->resetUCheckStaffWeek($uCheckStaffWeek);//需要把派单系统回传的数据添加城市及员工信息
        $citySetList[]=array("region_code"=>"none","region_name"=>"异常区域","code"=>"none","city_name"=>"异常城市");
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
                $temp = $this->defMoreCity();
                $temp["city"]=$cityCode;
                $temp["city_name"]=$cityRow["city_name"];
                if(isset($uList[$cityCode])){
                    foreach ($uList[$cityCode] as $uCityRow){
                        $uCityRow["city_name"]=$cityRow["city_name"];
                        $temp["staff_list"][] = $uCityRow;
                    }
                }
                if($cityCode=="none"&&count($temp["staff_list"])<1){
                    //异常为空则不需要显示
                }else{
                    $data[$regionCode]["list"][$cityCode]=$temp;
                }
            }
            $this->data = $data;
        }
        $session = Yii::app()->session;
        $session['checkStaffWeek_c01'] = $this->getCriteria();
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
        return array(
            "city"=>"",
            "city_name"=>"",
            "head_amt"=>"",//头部平均周人效
            "head_num"=>"",//头部平均工单数
            "week_amt"=>"",//平均周人效
            "week_num"=>"",//平均每日工单数
            "staff_list"=>array(),//城市内员工
        );
    }

    protected function resetTdRow(&$list,$bool=false){
        $moneyList = $list["staff_list"];
        $jobRateList = $list["staff_list"];
        // 根据工单总金额排序
        array_multisort(array_column($moneyList, 'money'), SORT_DESC, $moneyList);
        // 根据平均每日单数排序
        array_multisort(array_column($jobRateList, 'jobNumRate'), SORT_DESC, $jobRateList);
        $maxCount = count($list["staff_list"]);
        $maxCount = $maxCount>5?5:$maxCount;
        $head_amt=0;//前5工单金额总金额(总金额排序)
        $head_num_top=0;//前5技术员工单总数(每日单数排序)
        $head_num_bottom=0;//前5总有效工作天数(每日单数排序)
        for ($i=0;$i<$maxCount;$i++){//前五名
            $head_amt+=$moneyList[$i]["money"];
            $head_num_top+=$jobRateList[$i]["jobCount"];
            $head_num_bottom+=$jobRateList[$i]["days"];
        }
        $list["head_amt"] = round($head_amt/5,2);
        $list["head_num"] = empty($head_num_bottom)?0:round($head_num_top/$head_num_bottom,2);
        $week_amt_top=0;//该城市工单总金额
        $week_amt_bottom=0;//该城市总技术员人数
        $week_num_top=0;//该城市总工单数
        $week_num_bottom=0;//该城市总有效工作天数
        foreach ($list["staff_list"] as $staffRow){
            $week_amt_top+=$staffRow["money"];//该城市工单总金额
            $week_num_top+=$staffRow["jobCount"];//该城市总工单数
            if($staffRow["table_type"]==1){//专职,外包人员不计算
                $week_amt_bottom++;//该城市总技术员人数
                $week_num_bottom+=$staffRow["days"];//该城市总有效工作天数
            }
        }
        $list["week_amt"] = empty($week_amt_bottom)?0:round($week_amt_top/$week_amt_bottom,2);
        $list["week_num"] = empty($week_num_bottom)?0:round($week_num_top/$week_num_bottom,2);
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
            array("name"=>Yii::t("summary","head money"),
                "colspan"=>array(
                    array("name"=>Yii::t("summary","head amt")),//头部平均周人效
                    array("name"=>Yii::t("summary","head num")),//头部平均工单数
                )
            ),//财富效应
            array("name"=>Yii::t("summary","week amt"),"rowspan"=>2),//平均周人效
            array("name"=>Yii::t("summary","week num"),"rowspan"=>2),//平均每日工单数
        );
        return $topList;
    }

    protected function getStaffTopArr(){
        $topList=array(
            array("name"=>"员工编号","rowspan"=>2),//
            array("name"=>"员工名称","rowspan"=>2),//
            array("name"=>"员工城市","rowspan"=>2),//
            array("name"=>"是否外包","rowspan"=>2),//
            array("name"=>"职位名称","rowspan"=>2),//
            array("name"=>"年资(月)","rowspan"=>2),//
            array("name"=>"工单总金额","rowspan"=>2),//
            array("name"=>"有效做单天数","rowspan"=>2),//
            array("name"=>"有效工单数","rowspan"=>2),//
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
            "head_amt"=>array("show"=>true,"addType"=>false),
            "head_num"=>array("show"=>true,"addType"=>false),
            "week_amt"=>array("show"=>true,"addType"=>false),
            "week_num"=>array("show"=>true,"addType"=>false),
            "staff_list"=>array("show"=>false,"addType"=>3),
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
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            if($keyRow["addType"]===3){//数组
                                $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:array();
                                if(!key_exists("staffExcel",$this->downJsonText)){
                                    $this->downJsonText["staffExcel"]=array();
                                }
                                $this->downJsonText["staffExcel"]=array_merge($this->downJsonText["staffExcel"],$text);
                                if(!key_exists($keyStr,$regionRow)){
                                    $regionRow[$keyStr]=array();
                                }
                                $regionRow[$keyStr]=array_merge($regionRow[$keyStr],$text);
                            }else{
                                if(!key_exists($keyStr,$regionRow)){
                                    $regionRow[$keyStr]=0;
                                }
                                $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            if(!$keyRow["show"]){//不显示
                                continue;
                            }
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$cityList);
                            $this->downJsonText["excel"][$regionList['regionCode']]['list'][$cityList['city']][$keyStr]=$text;

                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    if($regionList["regionCode"]!="none"){
                        //地区汇总
                        $regionRow["city"]=$regionList["regionCode"];
                        $regionRow["city_name"]=$regionList["regionName"];
                        $html.=$this->printTableTr($regionRow,$bodyKey);
                        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                    }
                }
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr,$row){
        $tdClass = "";
        if(strpos($text,'%')!==false){
            if(!in_array($keyStr,array("new_rate","stop_rate","net_rate"))){
                $tdClass =floatval($text)<=60?"text-danger":$tdClass;
            }
            $tdClass =floatval($text)>=100?"text-green":$tdClass;
        }elseif (strpos($keyStr,'net')!==false){ //所有淨增長為0時特殊處理
            if(Yii::t("summary","completed")==$text){
                $tdClass="text-green";
            }elseif (Yii::t("summary","incomplete")==$text){
                $tdClass="text-danger";
            }
        }

        return $tdClass;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr=>$keyRow){
            if(!$keyRow["show"]){//不显示
                continue;
            }
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$data);

            $this->downJsonText["excel"][$data['city']]['count'][$keyStr]=$text;
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
            $staffData = key_exists("staffExcel",$excelData)?$excelData["staffExcel"]:array();
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $titleName = Yii::t("app","Check Week Staff");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setCheckWeekData($excelData);
        $excel->setSheetName($titleName);
        if(!empty($staffData)){
            $excel->addSheet("员工详情");
            $headList = $this->getStaffTopArr();
            $excel->SetHeaderTitle("员工详情");
            $excel->outHeader(1);
            $excel->setSummaryHeader($headList);
            $keyArr=array(
                "code","name","city_name","table_name","dept_name","entry_month","money","days","jobCount"
            );
            $excel->setCheckWeekStaffData($staffData,$keyArr);
        }
        $excel->outExcel($titleName);
    }

}