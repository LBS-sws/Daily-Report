<?php

class SalesAverageForm extends CFormModel
{
	/* User Fields */
    public $start_date;
    public $end_date;

    public $data=array();
    public $month;//查询月份
    public $month_day=0;//查询月份有多少天
    public $day_num=0;//查询天数

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
            'day_num'=>Yii::t('summary','day num'),
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date')
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,end_date,day_num','safe'),
            array('start_date,end_date','required'),
            array('end_date','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        if(!empty($this->start_date)&&!empty($this->end_date)){
            if(date("Y/m",strtotime($this->start_date))!=date("Y/m",strtotime($this->end_date))){
                $this->addError($attribute, "不允许跨月查询");
            }else{
                $timer = strtotime($this->end_date);
                $this->month = date("n",$timer);
                $this->month_day = date("t",$timer);
                ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
            }
        }
    }

    public static function getDateDiffForDay($startDate,$endDate){
        return ($endDate-$startDate)/(60*60*24)+1;
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
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'day_num'=>$this->day_num,
        );
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();

        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);

        $lineList = LifelineForm::getLifeLineList($city_allow,$this->end_date);
        $staffList = $this->getStaffCountForCity($city_allow);

        $this->u_load_data['u_load_start'] = time();
        $uList = CountSearch::getUInvMoney($this->start_date,$this->end_date,$city_allow);
        $this->u_load_data['u_load_end'] = time();
        $serviceList = CountSearch::getServiceForType($this->start_date,$this->end_date,$city_allow);

        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);

            //$defMoreList["life_num"]=key_exists($city,$lineList)?$lineList[$city]:80000;
            $defMoreList["staff_num"]+=key_exists($city,$staffList)?$staffList[$city]:0;
            $defMoreList["amt_sum"]+=key_exists($city,$uList)?$uList[$city]["sum_money"]:0;
            $defMoreList["amt_sum"]+=key_exists($city,$serviceList)?$serviceList[$city]:0;
            $defMoreList["one_gross"]=key_exists($city,$lineList)?$lineList[$city]:80000;
            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['salesAverage_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    private function defMoreCity($city,$city_name){
        return array(
            "city"=>$city,
            "amt_sum"=>0,
            //"life_num"=>0,
            "one_gross"=>0,
            "staff_num"=>0,
            "city_name"=>$city_name,
            "region_name"=>"none",
        );
    }

    private function getStaffCountForCity($city_allow){
        $list = array();
        $endDate = $this->end_date;
        $suffix = Yii::app()->params['envSuffix'];
        $endDate = empty($endDate)?date("Y/m/d"):date("Y/m/d",strtotime($endDate));
        $rows = Yii::app()->db->createCommand()
            ->select("a.city,count(a.id) as staff_count")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$endDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/31')>='{$endDate}')
             ) AND a.city in ({$city_allow})"
            )->group("a.city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = $row["city"];
                $staff_count=empty($row["staff_count"])?0:$row["staff_count"];
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$staff_count;
            }
        }
        return $list;
    }

    protected function resetTdRow(&$list,$bool=false){
        $list["amt_average"]=empty($list["staff_num"])?0:round($list["amt_sum"]/$list["staff_num"]);
        $list["amt_auto"]=round(($list["amt_average"]/$this->day_num)*$this->month_day);
        if($bool){
            //$list["life_num"]="-";
            $list["one_gross"]="-";
        }
    }

    //顯示提成表的表格內容
    public function salesAverageHtml(){
        $html= '<table id="salesAverage" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $dateStr = $this->month.Yii::t("summary"," month").date("j",strtotime($this->start_date));
        $dateStr.=" ~ ".date("j",strtotime($this->end_date)).Yii::t("summary"," day");
        $topList=array(
            //城市
            array("name"=>Yii::t("summary","City")),
            //新增金额
            array("name"=>Yii::t("summary","New Amt"),"background"=>"#f7fd9d"),
            //销售人数
            array("name"=>Yii::t("summary","sales num"),"background"=>"#fcd5b4"),
            //查询时间段
            array("name"=>$dateStr,"background"=>"#f2dcdb"),
            //预计全月
            array("name"=>$this->month.Yii::t("summary"," month expected full month"),"background"=>"#DCE6F1"),
            //每月生命线
            array("name"=>Yii::t("summary","life num"),"background"=>"#FDE9D9"),
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
            $width=70;
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
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name",
            "amt_sum",
            "staff_num",
            "amt_average",
            "amt_auto",
            //"life_num",
            "one_gross",
        );

        return $bodyKey;
    }

    //設置百分比顏色
    private function getTdClassForRow($row){
        $tdClass = "";
/*        if($row["life_num"]>$row["amt_auto"]){
            $tdClass="danger";
        }*/
        if($row["one_gross"]>$row["amt_auto"]){
            $tdClass="danger";
        }
        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = array('city_num'=>0);//总计(所有地区)
            foreach ($data as $region=>$regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array('city_num'=>0);//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $city = $cityList["city"];
                        $allRow["city_num"]++;
                        $regionRow["city_num"]++;
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
                            $tdClass = $this->getTdClassForRow($cityList);
                            $dataClick="";
                            if ($keyStr=="staff_num"){
                                $tdClass.=" show_staff";
                                $dataClick=" data-city='{$city}' ";
                            }
                            $this->downJsonText["excel"][$cityList['region_name']][$cityList['city']][$keyStr]=$text;
                            $html.="<td class='{$tdClass}' {$dataClick}><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["city_name"]=$region;
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
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
            $this->downJsonText["excel"][$data['city_name']]["count"][]=$text;
            $html.="<td style='font-weight: bold'><span>{$text}</span></td>";
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
        $excel->SetHeaderTitle(Yii::t("app","Average office")."（".$this->start_date." ~ ".$this->end_date."）");
        $titleTwo = Yii::t("summary","day num").":".$this->day_num." ".Yii::t("summary","day");
        $excel->colTwo=2;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setUServiceHeader($headList);
        $excel->setSalesAnalysisData($excelData);
        $excel->outExcel(Yii::t("app","Average office"));
    }
}