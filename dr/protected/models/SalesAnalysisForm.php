<?php

class SalesAnalysisForm extends CFormModel
{
	/* User Fields */
    public $search_date;//查詢日期
    public $search_year;//查詢年份
    public $search_month;//查詢月份

    public $month_day;//本月的天數
    public $last_year;

    public $start_date;
    public $end_date;

    public $data=array();
    public $twoDate=array();
    public $threeDate=array();

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

    public function validateDate($attribute="", $params="") {
        if(!empty($this->search_date)) {
            $timer = strtotime($this->search_date);
            $this->search_year = date("Y", $timer);
            $this->search_month = date("n", $timer);

            $this->start_date = $this->search_year . "/01/01";
            $this->end_date = date("Y/m/d", $timer);

            $this->month_day = date("d", $timer);
            $this->last_year = $this->search_year - 1;
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

    //获取销售拜访的金额字段
    public static function getVisitAmtToSQL() {
        return array("svc_A7","svc_B6","svc_C7","svc_D6","svc_H6","svc_E7","svc_F4","svc_G3");
    }

    //获取什么是签单的查询字符串
    public static function getDealString($field) {
        $suffix = Yii::app()->params['envSuffix'];
        $rtn = '';
        $sql = "select id from sales{$suffix}.sal_visit_obj where rpt_type='DEAL'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $rtn .= ($rtn=='' ? '' : ' or ').$field." like '%\"".$row['id']."\"%'";
        }
        return ($rtn=='' ? "$field='0'" : $rtn);
    }

    public static function getCityListAndRegion($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $cityList=array();
        $cityRows = Yii::app()->db->createCommand()
            ->select("a.code,a.region,a.name as city_name,d.name as region_name")
            ->from("security{$suffix}.sec_city a")
            ->leftJoin("security{$suffix}.sec_city d","a.region=d.code")
            ->where("a.code in ({$city_allow})")->queryAll();
        if($cityRows){
            foreach ($cityRows as $cityRow){
                $cityRow["region_name"] = RptSummarySC::strUnsetNumber($cityRow["region_name"]);
                $cityList[$cityRow["code"]]=$cityRow;
            }
        }
        return $cityList;
    }

    protected function getSalesForHr($city_allow,$endDate=""){
        $endDate = empty($endDate)?$this->search_date:$endDate;
        $suffix = Yii::app()->params['envSuffix'];
        $endDate = empty($endDate)?date("Y/m/d"):date("Y/m/d",strtotime($endDate));
        $list=array('staff'=>array(),'user'=>array());
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.code,a.name,a.city,d.user_id,a.entry_time,
            CONCAT('') as city_name,CONCAT('') as region,CONCAT('') as region_name")
            ->from("security{$suffix}.sec_user_access f")
            ->leftJoin("hr{$suffix}.hr_binding d","d.user_id=f.username")
            ->leftJoin("hr{$suffix}.hr_employee a","d.employee_id=a.id")
            ->where("f.system_id='sal' and f.a_read_write like '%HK01%' and date_format(a.entry_time,'%Y/%m/%d')<='{$endDate}' and (
                (a.staff_status = 0)
                or
                (a.staff_status=-1 and date_format(a.leave_time,'%Y/%m/31')>='{$endDate}')
             ) AND a.city in ({$city_allow})"
            )->order("a.city desc")->queryAll();
        return $rows;
    }

    //前一年的平均值
    protected function getLastYearData($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $amtSql = self::getVisitAmtToSQL();
        $dealSQL = self::getDealString("b.visit_obj");//签单的sql
        $amtSql = implode("','",$amtSql);
        //注意：本城市
        $rows = Yii::app()->db->createCommand()
            ->select("b.username,sum(a.field_value) as last_amt")
            ->from("sales{$suffix}.sal_visit_info a")
            ->leftJoin("sales{$suffix}.sal_visit b","a.visit_id=b.id")
            ->where("b.city in ({$city_allow}) and ({$dealSQL}) and a.field_id in ('{$amtSql}') and 
            DATE_FORMAT(b.visit_dt,'%Y')='{$this->last_year}'"
            )->group("b.username,DATE_FORMAT(b.visit_dt,'%Y/%m')")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["username"],$list)){
                    $list[$row["username"]]=array("last_amt"=>0,"count_month"=>0);
                }
                $list[$row["username"]]["last_amt"]+=$row["last_amt"];
                $list[$row["username"]]["count_month"]++;
            }
        }
        return $list;
    }

    //本年度的数据
    protected function getNowYearData($city_allow){
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $amtSql = self::getVisitAmtToSQL();
        $dealSQL = self::getDealString("b.visit_obj");//签单的sql
        $amtSql = implode("','",$amtSql);
        //注意：本城市
        $rows = Yii::app()->db->createCommand()
            ->select("b.username,sum(a.field_value) as now_amt,DATE_FORMAT(b.visit_dt,'%Y/%m') as month")
            ->from("sales{$suffix}.sal_visit_info a")
            ->leftJoin("sales{$suffix}.sal_visit b","a.visit_id=b.id")
            ->where("b.city in ({$city_allow}) and ({$dealSQL}) and a.field_id in ('{$amtSql}') and 
            b.visit_dt BETWEEN '{$this->start_date}' and '{$this->end_date}'"
            )->group("b.username,DATE_FORMAT(b.visit_dt,'%Y/%m')")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["username"]][$row["month"]] = $row["now_amt"];
            }
        }
        return $list;
    }

    protected function setStaffRowForNowData(&$staffRow,$nowData){
        $username = $staffRow["user_id"];
        $timer = strtotime($staffRow["entry_time"]);
        $entry_year = date("Y",$timer);
        $entry_month = date("n",$timer);
        $sum = 0;
        $count = 0;
        for ($i=1;$i<=$this->search_month;$i++){
            $yearMonth = $this->search_year."/".($i<10?"0{$i}":$i);
            $value=0;
            if(key_exists($username,$nowData)){
                $value = key_exists($yearMonth,$nowData[$username])?$nowData[$username][$yearMonth]:0;
            }
            if($entry_year==$this->search_year){
                if($entry_month>$i){//未入职显示空
                    $value="";
                }elseif ($entry_month==$i&&empty($value)){//入职当月且没有签单金额
                    $value="-";
                }
            }
            if($value!==""&&is_numeric($value)){//有效数据
                $sum+=$value;
                $count++;
            }
            $staffRow[$yearMonth]=$value;
        }
        $staffRow["now_average"]=empty($count)?0:round($sum/$count);
    }

    protected function groupAreaForStaffAndData($staffRows,$cityList,$nowData){
        $data = array();
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $entry_timer = strtotime($staffRow["entry_time"]);
                $city = $staffRow['city'];
                $region = key_exists($city,$cityList)?$cityList[$city]["region_name"]:"none";
                if(!key_exists($region,$data)){
                    $data[$region]=array(
                        "region"=>$region,
                        "region_name"=>key_exists($city,$cityList)?$cityList[$city]["region_name"]:"none",
                        "list"=>array(
                            "now_sales"=>array("name"=>Yii::t("summary","now sales"),"fte_num"=>0,"region"=>$region,'user_name'=>array())
                        ),
                    );
                }
                $nowStaffDate = $this->search_year==2023?"{$this->search_year}/03/01":"{$this->search_year}/01/01";
                if($entry_timer<strtotime($nowStaffDate)){
                    $month = "sales";//老员工
                }else{
                    $month = date("n",$entry_timer);//某月新入职员工
                    if(!key_exists("now_{$month}",$data[$region]["list"])){
                        $data[$region]["list"]["now_{$month}"]=array(
                            "name"=>$month.Yii::t("summary"," month now sales"),"fte_num"=>0,"region"=>$region,'user_name'=>array()
                        );
                    }
                }
                $data[$region]["list"]["now_{$month}"]["fte_num"]++;
                $data[$region]["list"]["now_{$month}"]["user_name"][]=$staffRow["user_id"];
                $this->setMonthAmt($data[$region]["list"]["now_{$month}"],$nowData,$staffRow["user_id"]);
            }
        }
        return $data;
    }

    //将员工的金额汇总
    protected function setMonthAmt(&$data,$nowData,$username){
        $list = array();
        $sum=0;
        if(key_exists($username,$nowData)){
            $list = $nowData[$username];
        }
        for($i=1;$i<=$this->search_month;$i++){
            $key = $this->search_year."/".($i<10?"0{$i}":$i);
            if(!key_exists($key,$data)){
                $data[$key]=0;
            }
            $data[$key]+=key_exists($key,$list)?$list[$key]:0;
            $sum+=$data[$key];
        }
        $data["now_average"]=round($sum/$this->search_month);
    }

    protected function groupCityForStaffAndData($staffRows,$cityList,$nowData,$lifelineList){
        $yearMonth = $this->search_year."/".($this->search_month < 10 ? "0{$this->search_month}" : $this->search_month);
        $data = array();
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $entry_time = date("Y/m",strtotime($staffRow["entry_time"]));
                $city = $staffRow['city'];
                $city_name = key_exists($city,$cityList)?$cityList[$city]["city_name"]:"none";
                if(!key_exists($city,$data)){
                    $data[$city]=array(
                        "city"=>$city,
                        "city_name"=>$city_name,
                        "list"=>array("city_name"=>$city_name,"now_num"=>0,"max_num"=>0,"min_num"=>0,"new_num"=>0),
                    );
                }
                $data[$city]["list"]["now_num"]++;//在职人数
                if($entry_time<$yearMonth){//在职人数
                    //达标人数
                    $amt_sum = key_exists($staffRow["user_id"],$nowData)?$nowData[$staffRow["user_id"]]:array();
                    $amt_sum = key_exists($yearMonth,$amt_sum)?$amt_sum[$yearMonth]:0;
                    $tar_num = key_exists($city,$lifelineList)?$lifelineList[$city]:80000;
                    if($amt_sum>$tar_num){
                        $data[$city]["list"]["max_num"]++;
                    }else{
                        $data[$city]["list"]["min_num"]++;
                    }
                }else if($entry_time==$yearMonth){//新招人数
                    $data[$city]["list"]["new_num"]++;
                }
            }
        }
        return $data;
    }

    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $lifelineList = LifelineForm::getLifeLineList($city_allow,$this->search_date);//生命线
        $staffRows = $this->getSalesForHr($city_allow);//员工信息
        $lastData = $this->getLastYearData($city_allow);//前一年的平均值
        $nowData = $this->getNowYearData($city_allow);//本年度的数据
        $cityList = self::getCityListAndRegion($city_allow);//城市信息
        $this->twoDate = $this->groupAreaForStaffAndData($staffRows,$cityList,$nowData);
        $this->threeDate = $this->groupCityForStaffAndData($staffRows,$cityList,$nowData,$lifelineList);
        if($staffRows){
            foreach ($staffRows as $staffRow){
                $username = $staffRow["user_id"];
                $city = $staffRow["city"];
                $name_label = $staffRow["name"]." ({$staffRow["code"]})";
                $name_label.= empty($staffRow["staff_status"])?"":"（已离职）";
                $staffRow["employee_name"] = $name_label;
                if(key_exists($city,$cityList)){
                    $staffRow["region"] = $cityList[$city]["region"];
                    $staffRow["city_name"] = $cityList[$city]["city_name"];
                    $staffRow["region_name"] = $cityList[$city]["region_name"];
                }
                if(key_exists($username,$lastData)){//上一年平均生意额
                    $staffRow["last_average"] = round($lastData[$username]["last_amt"]/$lastData[$username]["count_month"]);
                }
                if(key_exists($city,$lifelineList)){//生命线
                    $staffRow["life_num"] = $lifelineList[$city];
                }else{
                    $staffRow["life_num"] = 80000;
                }
                $this->setStaffRowForNowData($staffRow,$nowData);
                $region = empty($staffRow["region"])?"null":$staffRow["region"];
                if (!key_exists($region,$data)){
                    $data[$region]=array("list"=>array(),"region_code"=>$region,"region_name"=>$staffRow["region_name"]);
                }
                $data[$region]["list"][]=$staffRow;
            }
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['salesAnalysis_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            $sum=0;
            for ($i=1;$i<=$this->search_month;$i++) {
                $yearMonth = $this->search_year . "/" . ($i < 10 ? "0{$i}" : $i);
                $sum+=$list[$yearMonth];
            }
            $list["last_average"]="-";
            $list["life_num"]="-";
            $list["now_average"]=round($sum/$this->search_month);
        }
    }

    //顯示提成表的表格內容
    public function salesAnalysisHtml(){
        $html= '<table id="salesAnalysis" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"));
        $topList=array(
            array("name"=>Yii::t("summary","Employee Name"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//姓名
            array("name"=>Yii::t("summary","City"),"rowspan"=>2,"background"=>"#000000","color"=>"#FFFFFF"),//城市
            array("name"=>Yii::t("summary","Reference"),"background"=>"#000000","color"=>"#FFFFFF",
                "colspan"=>array(
                    array("name"=>$this->last_year.Yii::t("summary"," year average")),//参考平均值
                    array("name"=>$this->search_year.Yii::t("summary"," life num")),//参考生命线
                )
            ),//参考
            array("name"=>$this->search_year.Yii::t("summary"," year sales"),"background"=>"#00B0F0","color"=>"#FFFFFF",
                "colspan"=>$monthArr
            )//销售金额
        );

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
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
            if($i==0){
                $width=115;
            }else{
                $width=70;
            }
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
    protected function getDataAllKeyStr(){
        $bodyKey = array(
            "employee_name",
            "city_name",
            "last_average",
            "life_num"
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $bodyKey[]=$this->search_year."/{$month}";
        }
        $bodyKey[]="now_average";
        return $bodyKey;
    }

    //設置顏色
    public static function getTextColorForKeyStr($text,$keyStr,$num){
        $tdClass = "";
        if($text!==""&&strpos($keyStr,'/')!==false&&is_numeric($text)){
            if($text>=$num){
                $tdClass="text-green";
            }else{
                $tdClass="text-danger";
            }
        }

        return $tdClass;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
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
                            $tdClass = self::getTextColorForKeyStr($text,$keyStr,$cityList['life_num']);
                            $this->downJsonText["excel"][$cityList['region']][$cityList['id']][$keyStr]["value"]=$text;
                            $this->downJsonText["excel"][$cityList['region']][$cityList['id']][$keyStr]["color"]=$tdClass;
                            $html.="<td class='{$tdClass}'><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionList["region_code"];
                    $regionRow["city_name"]=$regionList["region_name"];
                    $regionRow["employee_name"]="";
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["employee_name"]="";
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
            $this->downJsonText["excel"][$data['region']]["count"][$keyStr]["value"]=$text;
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
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Staff")."（{$this->search_date}）");
        $titleTwo = $this->start_date." ~ ".$this->end_date;
        //"\r\n"
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel("SalesAnalysis");
    }

    public static function getMenuList($active=1){
        return array(
            1=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/view'),
                "label"=>Yii::t("summary","Capacity Staff"),
                "active"=>$active==1
            ),
            2=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/area'),
                "label"=>Yii::t("summary","Capacity Area"),
                "active"=>$active==2
            ),
            3=>array(
                "url"=>Yii::app()->createUrl('salesAnalysis/city'),
                "label"=>Yii::t("summary","Capacity City"),
                "active"=>$active==3
            ),
        );
    }
}