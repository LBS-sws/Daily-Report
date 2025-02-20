<?php

class RetentionRateForm extends CFormModel
{
	/* User Fields */
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $search_month_start;//查詢月份(开始)
    public $search_month_end;//查詢月份(结束)
	public $start_date;
	public $end_date;

    public $data=array();

	public $th_sum=0;//所有th的个数

    public $downJsonText='';

    protected $class_type="NONE";//类型 NONE:普通  KA:KA
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
            array('search_year','safe'),
            array('search_year','required'),
        );
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
            'search_year'=>$this->search_year
        );
    }

    protected function computeDate(){
        $search_year = date("Y");
        $search_month = date("n");
        if($search_year == $this->search_year){
            $this->search_month = $search_month - 1;
        }else{
            $this->search_month = 12;
        }
        if($this->search_month<=0){
            $this->search_month = 12;
            $this->search_year--;
        }
        $this->search_month_start = 1;
        $this->search_month_end = $this->search_month;

        $this->start_date = date("Y/m/d",strtotime("{$this->search_year}/{$this->search_month_start}/01"));
        $this->end_date = date("Y/m/t",strtotime("{$this->search_year}/{$this->search_month_end}/01"));
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $this->computeDate();
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        $stopListAmt = CountSearch::getServiceForTypeToMonthEx($startDate,$endDate,$city_allow,"T");
        $retentionAmt = CountSearch::getRetentionAmt($endDate,$city_allow);
        $this->u_load_data['u_load_start'] = time();
        $this->u_load_data['u_load_end'] = time();

        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"]=$cityRow["add_type"];

            if(key_exists($city,$stopListAmt)){
                foreach ($stopListAmt[$city] as $keyStr=>$amt){
                    if(key_exists($keyStr,$defMoreList)){
                        $defMoreList[$keyStr]+=$amt;
                    }
                }
            }
            if(key_exists($city,$retentionAmt)){
                $defMoreList["all_month_amt"]+=$retentionAmt[$city];
            }
            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['retentionRate_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $arr[$keyStr]=0;
        }
        $arr["ytd_stop_amt"]=0;//YTD终止合同金额
        $arr["ytd_month_length"]=0;//YTD月份数
        $arr["all_month_amt"]=0;//月初合同总额
        $arr["retention_rate"]="-";//保留率
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        $monthLength = $this->search_month_end-$this->search_month_start+1;
        $list["ytd_month_length"]="{$monthLength}个月";
        $list["ytd_stop_amt"]=0;
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $list["ytd_stop_amt"]+=key_exists($keyStr,$list)?$list[$keyStr]:0;
        }
        if(!empty($list["all_month_amt"])){
            $list["retention_rate"] = ($list["ytd_stop_amt"]/$monthLength)*12;
            $list["retention_rate"]/= $list["all_month_amt"];
            $list["retention_rate"] = 1-$list["retention_rate"];
            $list["retention_rate"] = round($list["retention_rate"],4);
            $list["retention_rate"]= ($list["retention_rate"]*100)."%";
        }else{
            $list["retention_rate"]='-';
        }
    }

    //顯示提成表的表格內容
    public function retentionRateHtml(){
        $html= "<table id=\"retentionRate\" class=\"table table-fixed table-condensed table-bordered table-hover\">";
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $topList[]=array("name"=>$i.Yii::t("summary"," month"),"rowspan"=>2);
        }
        //YTD终止合同金额
        $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","stop contract amt"),"rowspan"=>2);
        //YTD月份数
        $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","all month length"),"rowspan"=>2);
        //月初合同总额
        $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","all month amt"),"rowspan"=>2);
        //保留率
        $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","retention rate"),"rowspan"=>2);

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
            "city_name"
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $bodyKey[]=$keyStr;
        }
        $bodyKey[]="ytd_stop_amt";
        $bodyKey[]="ytd_month_length";
        $bodyKey[]="all_month_amt";
        $bodyKey[]="retention_rate";
        return $bodyKey;
    }

    public static function comparisonRate($num,$numLast){
        $num = is_numeric($num)?floatval($num):0;
        $numLast = is_numeric($numLast)?floatval($numLast):0;
        if(empty($numLast)){
            return "";
        }else{
            $rate = ($num/$numLast);
            $rate = round($rate,3)*100;
            return $rate."%";
        }
    }
    //設置百分比顏色
    public static function showNum($keyStr,$num){
        if (strpos($num,'%')!==false){
            $number = floatval($num);
            $number=sprintf("%.1f",$number)."%";
        }elseif (is_numeric($num)){
            $number = floatval($num);
            $number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $number;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr($text,$keyStr){
        $tdClass = "";

        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = array("countNum"=>0);//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array("countNum"=>0);//地区汇总
                    foreach ($regionList["list"] as $tdStr=>$cityList) {
                        $allRow["countNum"]++;
                        $regionRow["countNum"]++;
                        $this->resetTdRow($cityList,false);
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
                            if($cityList["add_type"]!=1){ //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr);
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $text = self::showNum($keyStr,$text);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$tdStr][$keyStr]=$text;

                            $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
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
            $tdClass = self::getTextColorForKeyStr($text,$keyStr);
            $text = self::showNum($keyStr,$text);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][{$keyStr}]",$text);
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
        $this->computeDate();
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $titleName = Yii::t("app","Retention rate");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->colTwo=6;
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData,false);
        $excel->outExcel($titleName);
    }

    protected function clickList(){
        return array(
        );
    }

    private function tdClick(&$tdClass,$keyStr,$city){
        $expr = " data-city='{$city}'";
        $list = $this->clickList();
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }

    public static function getRetentionYear(){
        $search_year = date("Y");
        $search_month = date("n");
        $arr = array();
        $minYear = 2025;
        $maxYear = $search_month>=2?$search_year:$search_year-1;
        for($i=$minYear;$i<=$maxYear;$i++){
            $arr[$i] = $i.Yii::t("summary"," Year");
        }

        return $arr;
    }
}