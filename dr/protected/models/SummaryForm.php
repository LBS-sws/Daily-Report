<?php

class SummaryForm extends CFormModel
{
	/* User Fields */
	public $start_date;
	public $end_date;
    public $day_num=0;
	public $summary_year;

	public $data=array();

	public $th_sum=2;//所有th的个数

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
	    $startYear = date("Y",strtotime($this->start_date));
	    $endYear = date("Y",strtotime($this->end_date));
	    if($startYear!=$endYear){
            $this->addError($attribute, "请把开始年份跟结束年份保持一致");
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
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date
        );
    }

    public static function getUActualMoney($startDay,$endDay,$city_allow=""){
	    $list = array();
	    $citySql = "";
	    if(!empty($city_allow)){
	        $citySql = " and b.Text in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.Text,a.Fee,a.TermCount")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->order("b.Text")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = $row["Text"];
                $money = empty($row["TermCount"])?0:floatval($row["Fee"])/floatval($row["TermCount"]);
                if(!key_exists($city,$list)){
                    $list[$city]=0;
                }
                $list[$city]+=$money;
            }
        }
        return $list;
    }

    public function retrieveData() {
        $this->summary_year = date("Y",strtotime($this->start_date));
	    $rptModel = new RptSummarySC();
        $criteria = new ReportForm();
        $criteria->start_dt = $this->start_date;
        $criteria->end_dt = $this->end_date;
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $criteria->city = Yii::app()->user->city_allow();
        $rptModel->criteria = $criteria;
        $rptModel->retrieveData();
        $this->data = $rptModel->data;
        $uActualMoneyList = SummaryForm::getUActualMoney($this->start_date,$this->end_date,$criteria->city);
        if($this->data){
            foreach ($this->data as $regionKey=>$regionList){
                if(!empty($regionList["list"])){
                    foreach ($regionList["list"] as $cityKey=>$cityList){
                        $this->data[$regionKey]["list"][$cityKey]["u_actual_money"]=key_exists($cityKey,$uActualMoneyList)?$uActualMoneyList[$cityKey]:0;//实际月金额
                        $this->data[$regionKey]["list"][$cityKey]["num_growth"]=0;//净增长
                        $this->data[$regionKey]["list"][$cityKey]["one_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_net_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_net_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_net_rate"]=0;
                        //放入目标金额
                        $row = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
                            ->where("comparison_year=:year and city=:city",
                                array(":year"=>$this->summary_year,":city"=>$cityKey)
                            )->queryRow();//2023/3/14日，目標金額配置統一調用swo_comparison_set
                        if($row){
                            $this->data[$regionKey]["list"][$cityKey]["one_gross"]=empty($row["one_gross"])?0:floatval($row["one_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["one_net"]=empty($row["one_net"])?0:floatval($row["one_net"]);
                            $this->data[$regionKey]["list"][$cityKey]["two_gross"]=empty($row["two_gross"])?0:floatval($row["two_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["two_net"]=empty($row["two_net"])?0:floatval($row["two_net"]);
                            $this->data[$regionKey]["list"][$cityKey]["three_gross"]=empty($row["three_gross"])?0:floatval($row["three_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["three_net"]=empty($row["three_net"])?0:floatval($row["three_net"]);
                        }
                    }
                }
            }
        }
        $session = Yii::app()->session;
        $session['summary_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
	    $newSum = $list["num_new"]+$list["u_invoice_sum"];//所有新增总金额
        $list["num_growth"] = 0;
	    $list["num_growth"]+=$list["num_new"];
	    $list["num_growth"]+=$list["u_invoice_sum"];
	    $list["num_growth"]+=$list["num_stop"];
	    $list["num_growth"]+=$list["num_restore"];
	    $list["num_growth"]+=$list["num_pause"];
	    $list["num_growth"]+=$list["num_update"];

        $list["two_gross"] = $bool?$list["two_gross"]:ComparisonForm::resetNetOrGross($list["two_gross"],$this->day_num);
        $list["two_net"] = $bool?$list["two_net"]:ComparisonForm::resetNetOrGross($list["two_net"],$this->day_num);
        $list["two_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["two_gross"]);
        $list["two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["two_net"]);
        if(SummaryForm::targetAllReady()){
            $list["one_gross"] = $bool?$list["one_gross"]:ComparisonForm::resetNetOrGross($list["one_gross"],$this->day_num);
            $list["one_net"] = $bool?$list["one_net"]:ComparisonForm::resetNetOrGross($list["one_net"],$this->day_num);
            $list["three_gross"] = $bool?$list["three_gross"]:ComparisonForm::resetNetOrGross($list["three_gross"],$this->day_num);
            $list["three_net"] = $bool?$list["three_net"]:ComparisonForm::resetNetOrGross($list["three_net"],$this->day_num);
            $list["one_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["one_gross"]);
            $list["one_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["one_net"]);
            $list["three_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["three_gross"]);
            $list["three_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["three_net"]);
        }
    }

    //顯示提成表的表格內容
    public function summaryHtml(){
        $html= '<table id="summary" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Actual monthly amount"),"rowspan"=>2),//服务生意额
            array("name"=>Yii::t("summary","Signing status"),"background"=>"#f7fd9d",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","New(service)")),//新增服务
                    array("name"=>Yii::t("summary","New(INV)")),//新增（产品）
                    array("name"=>Yii::t("summary","Terminate service")),//终止服务
                    array("name"=>Yii::t("summary","Resume service")),//恢复服务
                    array("name"=>Yii::t("summary","Suspended service")),//暂停服务
                    array("name"=>Yii::t("summary","Amendment service")),//更改服务
                    array("name"=>Yii::t("summary","Net growth")),//净增长
                )
            ),//签单情况
            array("name"=>Yii::t("summary","New customer(service)"),"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","long month")),//长约（>=12月）
                    array("name"=>Yii::t("summary","short month")),//短约
                    array("name"=>Yii::t("summary","cate service")),//餐饮客户
                    array("name"=>Yii::t("summary","not cate service")),//非餐饮客户
                )
            ),//新增客户（服务）
            array("name"=>Yii::t("summary","New customer(INV)"),"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","cate service")),//餐饮客户
                    array("name"=>Yii::t("summary","not cate service")),//非餐饮客户
                )
            ),//新增客户（产品）
        );
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (upside case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (upside case)
        }
        $topList[]=array("name"=>Yii::t("summary","Annual target (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//年金额目标 (base case)
        $topList[]=array("name"=>Yii::t("summary","Goal degree (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//目标完成度 (base case)
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (minimum case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (minimum case)
        }
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
            if(in_array($i,array(2,4,5,6,7,8))){
                $width=75;
            }elseif($i==9){
                $width=110;
            }elseif(in_array($i,array(1,3,12,14))){
                $width=90;
            }else{
                $width=83;
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
            "city_name","u_actual_money","num_new","u_invoice_sum","num_stop","num_restore","num_pause","num_update",
            "num_growth","num_long","num_short","num_cate","num_not_cate","u_num_cate","u_num_not_cate"
        );
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="one_gross";
            $bodyKey[]="one_net";
            $bodyKey[]="one_gross_rate";
            $bodyKey[]="one_net_rate";
        }
        $bodyKey[]="two_gross";
        $bodyKey[]="two_net";
        $bodyKey[]="two_gross_rate";
        $bodyKey[]="two_net_rate";
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="three_gross";
            $bodyKey[]="three_net";
            $bodyKey[]="three_gross_rate";
            $bodyKey[]="three_net_rate";
        }
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
                    $text = ComparisonForm::showNum($text);
                    $inputHide = TbHtml::hiddenField("excel[MO][]",$text);
                    $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
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
            $allRow = [];//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = [];//地区汇总
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
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                            $text = ComparisonForm::showNum($text);
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);
                            $html.="<td class='{$tdClass}'><span>{$text}</span>{$inputHide}</td>";
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
            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
            $text = ComparisonForm::showNum($text);
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

    public static function targetAllReady(){
        return Yii::app()->user->validFunction('CN15');
    }

    //下載
    public function downExcel($excelData){
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","Summary"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel("Summary");
    }
}