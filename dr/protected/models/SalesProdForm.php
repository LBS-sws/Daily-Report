<?php

class SalesProdForm extends CFormModel
{
	/* User Fields */
    public $start_date;
    public $end_date;

    public $td_list=array();//员工职位名称

    public $data=array();
    public $twoDate=array();
    public $threeDate=array();

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

    public function validateDate($attribute="", $params="") {
        $this->start_date = date("Y/m/d", strtotime($this->start_date));
        $this->end_date = date("Y/m/d", strtotime($this->end_date));
        if($this->start_date>$this->end_date) {
            $this->addError($attribute, "开始时间不能大于结束时间");
        }else{
            $city_allow = Yii::app()->user->city_allow();
            $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
            $this->td_list = $this->getSalesForDept($city_allow,$this->start_date,$this->end_date);
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
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date
        );
    }

    public function getSalesForDept($city_allow,$startDate="",$endDate=""){
        $list = array();
        $suffix = Yii::app()->params['envSuffix'];
        $startDate = empty($startDate)?date("Y/m/01"):date("Y/m/d",strtotime($endDate));
        $endDate = empty($endDate)?date("Y/m/d"):date("Y/m/d",strtotime($endDate));
        $rows = Yii::app()->db->createCommand()
            ->select("dept.name as dept_name")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr{$suffix}.hr_dept dept","a.position=dept.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$endDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/%d')>='{$startDate}')
             ) AND a.city in ({$city_allow})"
            )->group("dept.name")->order("dept.name desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[]=$row["dept_name"];
            }
        }
        return $list;
    }

    private function getSalesDeptCountForCity($city_allow){
        $list = array();
        $suffix = Yii::app()->params['envSuffix'];
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $selectSql = "";
        foreach ($this->td_list as $key=>$item){
            if(!empty($item)){
                $deptStr="dept.name='{$item}'";
            }else{
                $deptStr="(dept.name is null or dept.name='')";
            }
            $selectSql.=",sum(if({$deptStr},1,0)) as num_{$key}";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.city,sum(1) as num_total{$selectSql}")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr{$suffix}.hr_dept dept","a.position=dept.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$endDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/%d')>='{$startDate}')
             ) AND a.city in ({$city_allow})"
            )->group("a.city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["city"]]=$row;
            }
        }
        return $list;
    }

    protected function getSalesDeptAmtForCity($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $amtSql = SalesAnalysisForm::getVisitAmtToSQL();
        $dealSQL = SalesAnalysisForm::getDealString("b.visit_obj");//签单的sql
        $amtSql = implode("','",$amtSql);
        $selectSql = "";
        foreach ($this->td_list as $key=>$item){
            if(!empty($item)){
                $deptStr="dept.name='{$item}'";
            }else{
                $deptStr="(dept.name is null or dept.name='')";
            }
            $selectSql.=",sum(if({$deptStr},a.field_value,0)) as amt_{$key}";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("b.city,sum(a.field_value) as amt_total{$selectSql}")
            ->from("sales{$suffix}.sal_visit_info a")
            ->leftJoin("sales{$suffix}.sal_visit b","a.visit_id=b.id")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=b.username")
            ->leftJoin("hr{$suffix}.hr_employee f","d.employee_id=f.id")
            ->leftJoin("hr{$suffix}.hr_dept dept","f.position=dept.id")
            ->where("b.city in ({$city_allow}) and ({$dealSQL}) and a.field_id in ('{$amtSql}') and 
            b.visit_dt BETWEEN '{$this->start_date}' and '{$this->end_date}'"
            )->group("b.city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["city"]]=$row;
            }
        }
        return $list;
    }

    public function retrieveData($city="") {
        $this->u_load_data['load_start'] = time();

        $this->u_load_data['u_load_start'] = time();

        $this->u_load_data['u_load_end'] = time();
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);

        $salesDeptCount = $this->getSalesDeptCountForCity($city_allow);
        $salesDeptAmt = $this->getSalesDeptAmtForCity($city_allow);

        $temp = $this->defMoreCity();
        foreach ($citySetList as $cityRow) {
            $city = $cityRow["code"];
            $defMoreList = $temp;
            $defMoreList["city"] = $city;
            $defMoreList["city_name"] = $cityRow["city_name"];
            $defMoreList["add_type"] = $cityRow["add_type"];

            $this->addListForCity($defMoreList,$city,$salesDeptCount);
            $this->addListForCity($defMoreList,$city,$salesDeptAmt);

            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        $session = Yii::app()->session;
        $session['salesProd_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
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

    //設置該城市的默認值
    private function defMoreCity(){
        $arr=array(
            "city"=>"",
            "city_name"=>"",
            "num_total"=>0,//人数总数 (该总数在mysql内计算)
            "amt_total"=>0,//金额总数 (该总数在mysql内计算)
        );
        foreach ($this->td_list as $key=>$item){
            $arr["num_".$key]=0;//人数
            $arr["amt_".$key]=0;//金额
            $arr["rate_".$key]=0;//生产率
        }
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool===false){
            foreach ($this->td_list as $key=>$item){
                //$list["num_".$key];//人数
                //$list["amt_".$key];//金额
                if(empty($list["num_".$key])){
                    $list["rate_".$key]="";
                }else{
                    $list["rate_".$key]=round($list["amt_".$key]/$list["num_".$key]);
                }
            }
        }
    }

    //顯示提成表的表格內容
    public function salesProdHtml($type="num"){
        $html= '<table id="salesProd" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml($type);
        $html.=$this->tableBodyHtml($type);
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    public function getTopArr($type="num"){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//城市
        );
        foreach ($this->td_list as $item){
            $topList[]=array("name"=>$item,"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF");
        }
        if($type!='rate'){
            $topList[]=array("name"=>Yii::t("summary","all total"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF");
        }

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml($type){
        $this->th_sum = 0;
        $topList = self::getTopArr($type);
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
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=90;
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml($type){
        $html="";
        if(!empty($this->data)){
            //$this->downJsonText=array();
            $html.="<tbody>";
            $data = $this->data;
            $moData = key_exists("MO",$data)?$data["MO"]:array();
            unset($data["MO"]);//澳门需要单独处理
            $html.=$this->showServiceHtml($data,$type);
            $html.=$this->showServiceHtmlForMO($moData,$type);
            $html.="</tbody>";
            //$this->downJsonText=json_encode($this->downJsonText);
            //$html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    protected function getDataAllKeyStr($type=""){
        $bodyKey = array(
            "city_name",
        );
        foreach ($this->td_list as $key=>$item){
            $bodyKey[]=$type."_".$key;
        }
        if($type!='rate'){
            $bodyKey[]=$type."_total";
        }
        return $bodyKey;
    }
    //將城市数据寫入表格(澳门)
    protected function showServiceHtmlForMO($data,$type=""){
        $bodyKey = $this->getDataAllKeyStr($type);
        $html="";
        if(!empty($data)){
            foreach ($data["list"] as $cityList) {
                $this->resetTdRow($cityList);
                $html="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $tdClass = "";
                    $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                    $text = self::showNum($text);
                    $this->downJsonText["excel"]['MO'][$keyStr]=$text;
                    $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data,$type=""){
        $bodyKey = $this->getDataAllKeyStr($type);
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
                            if($cityList["add_type"]!=1){ //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = "";
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $text = self::showNum($text);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;

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

    public static function showNum($num){
        return $num;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = "";
            $text = self::showNum($text);
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
        $this->validateDate("","");
        $excel = new DownSummary();
        //第一页
        $excel->SetHeaderTitle(Yii::t("summary","Sales productivity num"));
        $titleTwo = $this->start_date." ~ ".$this->end_date;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $headList = $this->getTopArr("num");
        $keyList = $this->getDataAllKeyStr("num");
        $excel->setSheetName(Yii::t("summary","Sales productivity num"));
        $excel->setUServiceHeader($headList);
        $excel->setSalesProdData($excelData,$keyList);
        //第二页
        $headList = $this->getTopArr("amt");
        $keyList = $this->getDataAllKeyStr("amt");
        $sheetName = Yii::t("summary","Sales productivity amt");
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName);
        $excel->outHeader(1);
        $excel->SetHeaderString($titleTwo);
        $excel->setUServiceHeader($headList);
        $excel->setSalesProdData($excelData,$keyList);
        //第三页
        $headList = $this->getTopArr("rate");
        $keyList = $this->getDataAllKeyStr("rate");
        $sheetName = Yii::t("summary","Sales productivity rate");
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName);
        $excel->outHeader(2);
        $excel->SetHeaderString($titleTwo);
        $excel->setUServiceHeader($headList);
        $excel->setSalesProdData($excelData,$keyList);
        $excel->outExcel(Yii::t("app","Sales productivity"));
    }

    protected function clickList(){
        return array(
            "new_month_n_last"=>array("title"=>Yii::t("summary","Last Month Single + New(INV)").Yii::t("summary"," (last year)"),"type"=>"ServiceINVMonthNewLast"),
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
}