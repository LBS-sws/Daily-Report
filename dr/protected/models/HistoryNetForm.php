<?php

class HistoryNetForm extends CFormModel
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
        $citySetList = CitySetForm::getCitySetList();
        $serviceList = $this->getServiceData($citySetList);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $region = $cityRow["region_code"];
            if(!key_exists($region,$data)){
                $data[$region]=array(
                    "region"=>$region,
                    "region_name"=>$cityRow["region_name"],
                    "list"=>array()
                );
            }
            if(key_exists($city,$serviceList)){
                $arr=$serviceList[$city];
            }else{
                $arr=$this->defMoreCity($city,$cityRow["city_name"]);
            }
            $arr["add_type"] = $cityRow["add_type"];
            $data[$region]["list"][$city]=$arr;
        }

        $this->insertUData($this->start_date,$this->end_date,$data,$citySetList);
        $this->insertUData($this->last_start_date,$this->last_end_date,$data,$citySetList);
        $this->insertUActualMoney($this->start_date,$this->end_date,$data,$citySetList,$city_allow);
        $this->data = $data;
        $session = Yii::app()->session;
        $session['historyNet_c01'] = $this->getCriteria();
        return true;
    }

    private function getServiceData($citySetList){
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];

        $where="(a.status_dt BETWEEN '{$this->start_date}' and '{$this->end_date}')";
        $where.="or (a.status_dt BETWEEN '{$this->last_start_date}' and '{$this->last_end_date}')";

        $selectSql = "a.status_dt,a.status,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid
            ";
        $serviceRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("not(f.rpt_cat='INV' and f.single=1) and a.city in ({$city_allow}) and a.city not in ('ZY') and a.status in ('N','T') and ({$where})")
            ->order("a.city")
            ->queryAll();
        //所有需要計算的客戶服務(ID客戶服務)
        $serviceRowsID = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("not(f.rpt_cat='INV' and f.single=1) and a.city in ({$city_allow}) and a.city not in ('ZY') and a.status in ('N','T') and ({$where})")
            ->order("a.city")
            ->queryAll();
        $serviceRows = $serviceRows?$serviceRows:array();
        $serviceRowsID = $serviceRowsID?$serviceRowsID:array();
        $rows = array_merge($serviceRows,$serviceRowsID);

        if($rows){
            foreach ($rows as $row){
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $this->insertDataForRow($row,$data,$citySetList);
            }
        }
        return $data;
    }
    //获取U系统的服务单数据
    private function insertUActualMoney($startDay,$endDay,&$data,$citySetList,$city_allow=""){
        $list = array();
        $citySql = "";
        if(!empty($city_allow)){
            $citySql = " and b.Text in ({$city_allow})";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.Text,a.JobDate,a.Fee,a.TermCount")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.officecity f","a.City = f.City")
            ->leftJoin("service{$suffix}.enums b","f.Office = b.EnumID and b.EnumType=8")
            ->where("a.Status=3 and a.JobDate BETWEEN '{$startDay}' AND '{$endDay}' {$citySql}")
            ->order("b.Text")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $city = $row["Text"];
                $date = date("Y/m",strtotime($row["JobDate"]));
                $money = empty($row["TermCount"])?0:floatval($row["Fee"])/floatval($row["TermCount"]);

                if(key_exists($city,$citySetList)) {
                    $region = $citySetList[$city]["region_code"];
                    $data[$region]["list"][$city]["u_{$date}"] += $money;

                    if ($citySetList[$city]["add_type"] == 1) {//叠加(城市配置的叠加)
                        $city = $citySetList[$city]["region_code"];
                        $region = "";
                        if(key_exists($city,$citySetList)){
                            $region = $citySetList[$city]["region_code"];
                        }
                        if(key_exists($region,$data)){
                            $data[$region]["list"][$city]["u_{$date}"] += $money;
                        }
                    }
                }
            }
        }
        return $list;
    }

    private function insertUData($startDate,$endDate,&$data,$citySetList){
        $year = intval($startDate);//服务的年份
        $json = Invoice::getInvData($startDate,$endDate);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = $row["city"];
                $date = date("Y/m",strtotime($row["invoice_dt"]));
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(key_exists($city,$citySetList)){
                    $region = $citySetList[$city]["region_code"];
                    $data[$region]["list"][$city][$date]+=$money;
                    $data[$region]["list"][$city]["{$date}_u"]+=$money;
                    if($year == $this->search_year){//生意额需要加上产品金额
                        $data[$region]["list"][$city]["u_{$date}"]+=$money;
                    }

                    if($citySetList[$city]["add_type"]==1){//叠加(城市配置的叠加)
                        $city = $citySetList[$city]["region_code"];
                        $region = "";
                        if(key_exists($city,$citySetList)){
                            $region = $citySetList[$city]["region_code"];
                        }
                        if(key_exists($region,$data)){
                            $data[$region]["list"][$city][$date]+=$money;
                            $data[$region]["list"][$city]["{$date}_u"]+=$money;
                            if($year == $this->search_year){//生意额需要加上产品金额
                                $data[$region]["list"][$city]["u_{$date}"]+=$money;
                            }
                        }
                    }
                }
            }
        }
    }

    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//产品金额
            $dateStrTwo = $this->last_year."/{$month}";//产品金额
            $dateStrThree = "u_".$this->search_year."/{$month}";//生意额
            $arr[$dateStrOne]=0;
            $arr[$dateStrOne."_u"]=$arr[$dateStrOne];
            $arr[$dateStrTwo]=0;
            $arr[$dateStrTwo."_u"]=$arr[$dateStrTwo];
            //U系统的生意额
            $arr[$dateStrThree]=0;
        }
        $arr["now_average"]=0;//本年平均
        $arr["last_average"]=0;//上一年平均
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["growth"]="";//加速增长
        $arr["start_two_net"]=0;//年初目标
        $arr["two_net"]=0;//滚动目标
        $arr["start_result"]="";//达成目标(年初)
        $arr["result"]="";//达成目标(滚动)
        $rowStart = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=1 and city=:city",
                array(":year"=>$this->search_year,":city"=>$city)
            )->queryRow();//查询目标金额
        if($rowStart){//年初
            $arr["start_two_net"]=empty($rowStart["two_net"])?0:floatval($rowStart["two_net"]);
        }
        $setRow = Yii::app()->db->createCommand()->select("*")->from("swo_comparison_set")
            ->where("comparison_year=:year and month_type=:month_type and city=:city",
                array(":year"=>$this->search_year,":month_type"=>$this->month_type,":city"=>$city)
            )->queryRow();//查询目标金额
        if($setRow){//滚动
            $arr["two_net"]=empty($setRow["two_net"])?0:floatval($setRow["two_net"]);
        }
        return $arr;
    }

    private function insertDataForRow($row,&$data,$citySetList){
        $timer = strtotime($row["status_dt"]);
        $dateStr = date("Y/m",$timer);
        $city = empty($row["city"])?"none":$row["city"];
        $citySet = CitySetForm::getListForCityCode($city,$citySetList);
        if(!key_exists($city,$data)){//設置該城市的默認值
            $arr = $this->defMoreCity($city,$citySet["city_name"]);
            $data[$city]=$arr;
        }
        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
            if(!key_exists($citySet["region_code"],$data)){
                $data[$citySet["region_code"]]=$this->defMoreCity($citySet["region_code"],$citySet["region_name"]);
            }
        }
        if($row["paid_type"]=="M"){//月金额
            $money = $row["amt_paid"]*$row["ctrt_period"];
        }else{
            $money = $row["amt_paid"];
        }
        switch ($row["status"]){
            case "N"://新增
                break;
            case "T"://终止
                $money*=-1;
                break;
        }
        $data[$city][$dateStr] += $money;
        if($citySet["add_type"]==1){//叠加(城市配置的叠加)
            $data[$citySet["region_code"]][$dateStr] += $money;
        }
        if($timer>=$this->week_start&&$timer<=$this->week_end){//本周
            $data[$city]["now_week"] += $money;
            if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                $data[$citySet["region_code"]]["now_week"] += $money;
            }
        }
        if($timer>=$this->last_week_start&&$timer<=$this->last_week_end){//上周
            $data[$city]["last_week"] += $money;
            if($citySet["add_type"]==1){//叠加(城市配置的叠加)
                $data[$citySet["region_code"]]["last_week"] += $money;
            }
        }
    }

    protected function resetTdRow(&$list,$bool=false){
        if(!$bool){
            $list["now_week"]=($list["now_week"]/$this->week_day)*$this->month_day;
            $list["now_week"]=HistoryAddForm::historyNumber($list["now_week"]);
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=HistoryAddForm::historyNumber($list["last_week"]);
        }
        $list["start_two_net"]=HistoryAddForm::historyNumber($list["start_two_net"],$bool);
        $list["two_net"]=HistoryAddForm::historyNumber($list["two_net"],$bool);
        $list["now_average"]=0;
        $list["last_average"]=0;
        $list["u_average"]=0;
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"]);
        $list["start_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_net"]);
        $list["result"]=HistoryAddForm::comYes($list["now_week"],$list["two_net"]);
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $lastStr = $this->last_year."/{$month}";
            $uStr = "u_".$this->search_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$lastStr] = key_exists($lastStr,$list)?$list[$lastStr]:0;
            $list[$uStr] = key_exists($uStr,$list)?$list[$uStr]:0;
            $list[$nowStr] = HistoryAddForm::historyNumber($list[$nowStr],$bool);
            $list[$lastStr] = HistoryAddForm::historyNumber($list[$lastStr],$bool);
            $list[$uStr] = HistoryAddForm::historyNumber($list[$uStr],$bool);
            $list["now_average"]+=$list[$nowStr];
            $list["last_average"]+=$list[$lastStr];
            $list["u_average"]+=$list[$uStr];
        }
        $list["now_average"]=round($list["now_average"]/$this->search_month,2);
        $list["last_average"]=round($list["last_average"]/$this->search_month,2);
        $list["u_average"]=round($list["u_average"]/$this->search_month,2);
    }

    //顯示提成表的表格內容
    public function historyNetHtml(){
        $html= '<table id="historyNet" class="table table-fixed table-condensed table-bordered table-hover">';
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
            ),//本年
            array("name"=>$this->search_year.Yii::t("summary","Actual monthly amount"),"background"=>"#FDE9D9",
                "colspan"=>$monthArr
            )//生意额
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
            if($i==0){
                $width=90;
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
            "city_name"
        );
        $dateTwoList = array();
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $bodyKey[]=$this->last_year."/{$month}";
            $dateTwoList[]=$this->search_year."/{$month}";
            $dateThreeList[]="u_".$this->search_year."/{$month}";
        }
        $bodyKey[]="last_average";
        $dateTwoList[]="now_average";
        $dateThreeList[]="u_average";
        $bodyKey=array_merge($bodyKey,$dateTwoList,$dateThreeList);

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_two_net";
        $bodyKey[]="start_result";
        $bodyKey[]="two_net";
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
                            if($cityList["add_type"]!=1) { //疊加的城市不需要重複統計
                                $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            }
                            $tdClass = HistoryAddForm::getTextColorForKeyStr($text,$keyStr);
                            $inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][]",$text);
                            if(strpos($keyStr,'/')!==false&&strpos($keyStr,'u_')===false){//调试U系统同步数据
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
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("app","History Stop")."（{$this->search_date}）");
        $titleTwo = $this->start_date." ~ ".$this->end_date."\r\n";
        $titleTwo.="本周:".date("Y/m/d",$this->week_start)." ~ ".date("Y/m/d",$this->week_end)." ({$this->week_day})\r\n";
        $titleTwo.="上周:";
        if($this->last_week_end===strtotime("1999/01/01")){
            $titleTwo.="无";
        }else{
            $titleTwo.=date("Y/m/d",$this->last_week_start)." ~ ".date("Y/m/d",$this->last_week_end)." ({$this->last_week_day})";
        }
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel("HistoryNet");
    }
}