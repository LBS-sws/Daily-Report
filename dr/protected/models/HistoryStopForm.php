<?php

class HistoryStopForm extends CFormModel
{
	/* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
    public $last_year;
    public $last_start_date;
    public $last_end_date;
    public $start_date;
    public $end_date;
    public $week_start;
    public $week_end;
    public $week_day;
    public $last_week_start;
    public $last_week_end;
    public $last_week_day;
    public $month_type;

    public $data=array();

	public $th_sum=1;//所有th的个数

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'search_date'=>Yii::t('summary','search date'),
            'week_start'=>Yii::t('summary','now week'),
            'last_week_start'=>Yii::t('summary','last week'),
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
            $this->search_month = date("n",$timer);
            $i = ceil($this->search_month/3);//向上取整
            $this->month_type = 3*$i-2;
            $this->start_date = $this->search_year."/01/01";
            $this->end_date = date("Y/m/t",$timer);
            $this->month_day = date("t",$timer);
            $this->last_year = $this->search_year-1;
            $this->last_start_date = $this->last_year."/01/01";
            $this->last_end_date = date("Y/m/t",strtotime($this->last_year."/{$this->search_month}/01"));

            $this->week_end = $timer;
            $this->week_start = HistoryAddForm::getDateDiffForMonth($timer,6,$this->search_month,false);
            $this->week_day = HistoryAddForm::getDateDiffForDay($this->week_start,$this->week_end);

            $this->last_week_end = HistoryAddForm::getDateDiffForMonth($this->week_start,1,$this->search_month);
            $this->last_week_start = HistoryAddForm::getDateDiffForMonth($this->last_week_end,6,$this->search_month,false);
            $this->last_week_day = HistoryAddForm::getDateDiffForDay($this->last_week_start,$this->last_week_end);
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
        $suffix = Yii::app()->params['envSuffix'];

        $where="(a.status_dt BETWEEN '{$this->start_date}' and '{$this->end_date}')";
        $where.="or (a.status_dt BETWEEN '{$this->last_start_date}' and '{$this->last_end_date}')";
        $rows = Yii::app()->db->createCommand()
            ->select("a.status_dt,a.status,f.rpt_cat,f.single,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.paid_type,a.amt_paid,a.ctrt_period,a.b4_paid_type,a.b4_amt_paid
            ,b.region,b.name as city_name,c.name as region_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->leftJoin("security{$suffix}.sec_city c","b.region=c.code")
            ->where("a.city in ({$city_allow}) and a.city not in ('ZY') and a.status='T' and ({$where})")
            ->order("a.city")
            ->queryAll();
        $uList = array();
        //$this->insertUData($this->start_date,$this->end_date,$uList);
        //$this->insertUData($this->last_start_date,$this->last_end_date,$uList);
        if($rows){
            foreach ($rows as $row){
                //rpt_cat='INV' and single=1的客户服务是产品，所以需要筛选出去
                if($row["rpt_cat"]==="INV"&&intval($row["single"])===1){
                    continue;
                }
                $row["region"] = RptSummarySC::strUnsetNumber($row["region"]);
                $row["region_name"] = RptSummarySC::strUnsetNumber($row["region_name"]);
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $this->insertDataForRow($row,$data,$uList);
            }
        }
        //$this->defaultRowForCity($data,$cityList,$uList);//填充默認城市（無數據的城市需要顯示0）
        $this->data = $data;
        $session = Yii::app()->session;
        $session['historyStop_c01'] = $this->getCriteria();
        return true;
    }

    //填充默認城市
    private function defaultRowForCity(&$data,&$cityList,&$uActualMoneyList){
        $city_allow = Yii::app()->user->city_allow();
        $notCity = ComparisonSetList::notCitySqlStr();
        $notCity = explode("','",$notCity);
        $hasCity = array_keys($cityList);
        $notCity = array_merge($hasCity,$notCity);
        $suffix = Yii::app()->params['envSuffix'];
        $where=" and b.code not in (SELECT f.region FROM security{$suffix}.sec_city f WHERE f.region is not NULL and f.region!='' GROUP BY f.region)";
        if(!empty($notCity)){
            $notCity = implode("','",$notCity);
            $where.=" and b.code not in ('{$notCity}')";
        }
        //where
        $rows = Yii::app()->db->createCommand()
            ->select("b.code,b.region,b.name as city_name,c.name as region_name")
            ->from("security{$suffix}.sec_city b")
            ->leftJoin("security{$suffix}.sec_city c","b.region=c.code")
            ->where("b.code in ({$city_allow}) {$where}")
            ->order("b.code")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $row["region"] = RptSummarySC::strUnsetNumber($row["region"]);
                $row["region_name"] = RptSummarySC::strUnsetNumber($row["region_name"]);
                $city = $row["code"];
                $region = $row["region"];
                $region = $city==="MO"?"MO":$region;//澳門地區單獨顯示
                if(!key_exists($region,$data)){
                    $data[$region]=array(
                        "region"=>$region,
                        "region_name"=>$row["region_name"],
                        "list"=>array()
                    );
                }
                $cityList[$row["code"]]=$row["region"];//U系统同步使用
                $arr = $this->defMoreCity($row["code"],$row["city_name"],$row["region"],$uActualMoneyList);
                $data[$region]["list"][$city]=$arr;
            }
        }
    }

    private function insertUData($startDate,$endDate,&$uList){
        //$year = intval($startDate);//服务的年份
        $json = Invoice::getInvData($startDate,$endDate);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = $row["city"];
                $date = date("Y/m",strtotime($row["invoice_dt"]));
                if(!key_exists($city,$uList)){
                    $uList[$city]=array();
                }
                if(!key_exists($date,$uList[$city])){
                    $uList[$city][$date]=0;
                }
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                $uList[$city][$date]+=$money;
            }
        }
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name,$region,$uList){
        $cityList[$city] = $region;//U系统同步使用
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";
            $dateStrTwo = $this->last_year."/{$month}";
            $arr[$dateStrOne]=key_exists($city,$uList)&&key_exists($dateStrOne,$uList[$city])?$uList[$city][$dateStrOne]:0;
            $arr[$dateStrOne."_u"]=$arr[$dateStrOne];
            $arr[$dateStrTwo]=key_exists($city,$uList)&&key_exists($dateStrTwo,$uList[$city])?$uList[$city][$dateStrTwo]:0;
            $arr[$dateStrTwo."_u"]=$arr[$dateStrTwo];
        }
        $arr["now_average"]=0;//本年平均
        $arr["last_average"]=0;//上一年平均
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["growth"]="";//加速增长
        $arr["start_two_gross"]=0;//年初目标
        $arr["two_gross"]=0;//滚动目标
        $arr["start_result"]="";//达成目标(年初)
        $arr["result"]="";//达成目标(滚动)
        $rowStart = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>$this->search_year,":city"=>$city)
            )->queryRow();//查询目标金额
        if($rowStart){//年初
            $rowStart["two_gross"]=empty($rowStart["two_gross"])?0:floatval($rowStart["two_gross"]);
            $rowStart["two_net"]=empty($rowStart["two_net"])?0:floatval($rowStart["two_net"]);
            $arr["start_two_gross"]=$rowStart["two_gross"]-$rowStart["two_net"];
        }
        $setRow = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$this->search_year,":month_type"=>$this->month_type,":city"=>$city)
            )->queryRow();//查询目标金额
        if($setRow){//滚动
            $setRow["two_gross"]=empty($setRow["two_gross"])?0:floatval($setRow["two_gross"]);
            $setRow["two_net"]=empty($setRow["two_net"])?0:floatval($setRow["two_net"]);
            $arr["two_gross"]=$setRow["two_gross"]-$setRow["two_net"];
        }
        return $arr;
    }

    private function insertDataForRow($row,&$data,&$uList){
        $timer = strtotime($row["status_dt"]);
        $dateStr = date("Y/m",$timer);
        $region = empty($row["region"])?"none":$row["region"];
        $city = empty($row["city"])?"none":$row["city"];
        $region = $city==="MO"?"MO":$region;//澳門地區單獨顯示
        if(!key_exists($region,$data)){
            $data[$region]=array(
                "region"=>$region,
                "region_name"=>$row["region_name"],
                "list"=>array()
            );
        }
        if(!key_exists($city,$data[$region]["list"])){//設置該城市的默認值
            $arr = $this->defMoreCity($city,$row["city_name"],$region,$uList);
            $data[$region]["list"][$city]=$arr;
        }
        if($row["paid_type"]=="M"){//月金额
            $money = $row["amt_paid"]*$row["ctrt_period"];
        }else{
            $money = $row["amt_paid"];
        }
        $data[$region]["list"][$city][$dateStr] += $money;
        if($timer>=$this->week_start&&$timer<=$this->week_end){//本周
            $data[$region]["list"][$city]["now_week"] += $money;
        }
        if($timer>=$this->last_week_start&&$timer<=$this->last_week_end){//上周
            $data[$region]["list"][$city]["last_week"] += $money;
        }
    }

    protected function resetTdRow(&$list,$bool=false){
        if(!$bool){
            $list["now_week"]=($list["now_week"]/$this->week_day)*$this->month_day;
            $list["now_week"]=HistoryAddForm::historyNumber($list["now_week"]);
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=HistoryAddForm::historyNumber($list["last_week"]);
        }
        $list["start_two_gross"]=HistoryAddForm::historyNumber($list["start_two_gross"]);
        $list["two_gross"]=HistoryAddForm::historyNumber($list["two_gross"]);
        $list["now_average"]=0;
        $list["last_average"]=0;
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"]);
        $list["start_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_gross"]);
        $list["result"]=HistoryAddForm::comYes($list["now_week"],$list["two_gross"]);
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $lastStr = $this->last_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$lastStr] = key_exists($lastStr,$list)?$list[$lastStr]:0;
            $list["now_average"]+=$list[$nowStr];
            $list["last_average"]+=$list[$lastStr];
            $list[$nowStr] = HistoryAddForm::historyNumber($list[$nowStr]);
            $list[$lastStr] = HistoryAddForm::historyNumber($list[$lastStr]);
        }
        $list["now_average"]=$list["now_average"]/$this->search_month;
        $list["last_average"]=$list["last_average"]/$this->search_month;
        $list["now_average"] = HistoryAddForm::historyNumber($list["now_average"]);
        $list["last_average"] = HistoryAddForm::historyNumber($list["last_average"]);
    }

    //顯示提成表的表格內容
    public function historyStopHtml(){
        $html= '<table id="historyStop" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"));
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>$this->last_year,"background"=>"#f7fd9d",
                "colspan"=>$monthArr
            ),//上一年
            array("name"=>$this->search_year,"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            )//本年
        );

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week")),//本周
                array("name"=>Yii::t("summary","last week")),//上周
                array("name"=>Yii::t("summary","growth")),//加速增长
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","Target contrast"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Target")),//年初目标
                array("name"=>Yii::t("summary","Start Target result")),//达成目标
                array("name"=>Yii::t("summary","Roll Target")),//滚动目标
                array("name"=>Yii::t("summary","Roll Target result")),//达成目标
            )
        );//目标对比

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
        $topList = $this->getTopArr();
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
            $width=70;
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
            "city_name"
        );
        $dateTwoList = array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $bodyKey[]=$this->last_year."/{$month}";
            $dateTwoList[]=$this->search_year."/{$month}";
        }
        $bodyKey[]="last_average";
        $dateTwoList[]="now_average";
        $bodyKey=array_merge($bodyKey,$dateTwoList);

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_two_gross";
        $bodyKey[]="start_result";
        $bodyKey[]="two_gross";
        $bodyKey[]="result";

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
                    $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
                    $inputHide = TbHtml::hiddenField("excel[MO][]",$text);
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
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);
                            if(strpos($keyStr,'/')!==false){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' data-u='{$cityList[$keyStr."_u"]}'><span>{$text}</span>{$inputHide}</td>";
                            }else{
                                $html.="<td class='{$tdClass}'><span>{$text}</span>{$inputHide}</td>";
                            }
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
            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
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

    //下載
    public function downExcel($excelData){
        $this->search_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","HistoryStop"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel("HistoryStop");
    }
}