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
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $where="not(f.rpt_cat='INV' and f.single=1) and a.status_dt BETWEEN '{$this->start_date}' and '{$this->end_date}'";
        $selectSql = "a.city,sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * a.ctrt_period
							else a.amt_paid
						end
					) as sum_amount";
        $serviceRows = Yii::app()->db->createCommand()
            ->select($selectSql)
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where("a.city in ({$city_allow}) and a.city not in ('ZY') and a.status='N' and {$where}")
            ->group("a.city")
            ->queryAll();
        //所有需要計算的客戶服務(ID客戶服務)
        $serviceRowsID = Yii::app()->db->createCommand()
            ->select("a.city,sum(amt_money) as sum_amount")
            ->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_id f","a.cust_type=f.id")
            ->where("a.city in ({$city_allow}) and a.city not in ('ZY') and a.status='N' and {$where}")
            ->group("a.city")
            ->queryAll();
        $serviceRows = $serviceRows?$serviceRows:array();
        $serviceRowsID = $serviceRowsID?$serviceRowsID:array();
        $rows = array_merge($serviceRows,$serviceRowsID);
        $uList = $this->getUData($this->start_date,$this->end_date);
        $staffList = $this->getStaffListForCity($city_allow);
        $lineList = LifelineForm::getLifeLineList($city_allow,$this->end_date);
        if(!empty($rows)){
            foreach ($rows as $row){
                $city = $row["city"];
                if(key_exists($city,$staffList)){
                    $region_name = $staffList[$city]["region_name"];
                    $city_name = $staffList[$city]["city_name"];
                    $staff_num = $staffList[$city]["staff_num"];
                }else{
                    $region_name = "none";
                    $city_name = "none";
                    $staff_num = 0;
                }
                if(!key_exists($region_name,$data)){
                    $data[$region_name]=array();
                }
                if(!key_exists($city,$data[$region_name])){
                    $data[$region_name][$city] = array(
                        "city"=>$city,
                        "amt_sum"=>key_exists($city,$uList)?$uList[$city]:0,
                        "life_num"=>key_exists($city,$lineList)?$lineList[$city]:0,
                        "staff_num"=>$staff_num,
                        "city_name"=>$city_name,
                        "region_name"=>$region_name,
                    );
                }
                $data[$region_name][$city]["amt_sum"]+=round($row["sum_amount"],2);
            }
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['salesAverage_c01'] = $this->getCriteria();
        return true;
    }

    private function getStaffListForCity($city_allow){
        $list = array();
        $staffList = SalesAnalysisForm::getSalesForHr($city_allow,$this->end_date);
        $cityList = SalesAnalysisForm::getCityListAndRegion($city_allow);
        if($staffList){
            foreach ($staffList as $row){
                $city = $row["city"];
                if(!key_exists($city,$list)){
                    $list[$city]=array(
                        "staff_num"=>0,
                        "city"=>$city,
                        "city_name"=>key_exists($city,$cityList)?$cityList[$city]["city_name"]:"",
                        "region_name"=>key_exists($city,$cityList)?$cityList[$city]["region_name"]:""
                    );
                }
                $list[$city]["staff_num"]++;
            }
        }
        return $list;
    }

    private function getUData($startDate,$endDate){
        $list=array();
        $json = Invoice::getInvData($startDate,$endDate);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = $row["city"];
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                $list[$city]+=$money;
            }
        }
        return $list;
    }

    protected function resetTdRow(&$list,$bool=false){
        $list["amt_average"]=empty($list["staff_num"])?0:round($list["amt_sum"]/$list["staff_num"]);
        $list["amt_auto"]=round(($list["amt_average"]/$this->day_num)*$this->month_day);
        if($bool){
            $list["life_num"]="-";
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
    private function tableTopHtml(){
        $topList = self::getTopArr();
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
            }else{
                $this->th_sum++;
            }
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr>";
        if(!empty($trTwo)){
            $html.="<tr>{$trTwo}</tr>";
        }
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
            "life_num",
        );

        return $bodyKey;
    }

    //設置百分比顏色
    private function getTdClassForRow($row){
        $tdClass = "";
        if($row["life_num"]>$row["amt_auto"]){
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
                if(!empty($regionList)) {
                    $regionRow = array('city_num'=>0);//地区汇总
                    foreach ($regionList as $cityList) {
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
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = $this->getTdClassForRow($cityList);
                            $this->downJsonText["excel"][$cityList['region_name']][$cityList['city']][$keyStr]=$text;
                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
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
        $excel->outExcel("SalesAverage");
    }
}