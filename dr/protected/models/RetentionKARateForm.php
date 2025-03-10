<?php

class RetentionKARateForm extends CFormModel
{
	/* User Fields */
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $search_month_start;//查詢月份(开始)
    public $search_month_end;//查詢月份(结束)
	public $start_date;
	public $end_date;
	public $employee_id;

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

    private function getGroupIDStrForEmployeeID($employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $employee_id = empty($employee_id)||!is_numeric($employee_id)?0:$employee_id;
        $list = array($employee_id);
        $bossRow = Yii::app()->db->createCommand()->select("a.id")
            ->from("hr{$suffix}.hr_group_staff a")
            ->leftJoin("hr{$suffix}.hr_group b","a.group_id=b.id")
            ->where("a.employee_id=:employee_id and b.group_code='KALIST'",array(":employee_id"=>$employee_id))
            ->queryRow();
        if($bossRow){//该员工有分组
            $infoRows = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")
                ->from("hr{$suffix}.hr_group_branch a")
                ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
                ->where("a.group_staff_id=:group_staff_id",array(":group_staff_id"=>$bossRow["id"]))
                ->queryAll();
            if($infoRows){//该员工有管辖员工
                foreach ($infoRows as $infoRow){
                    $list[] = $infoRow["id"];
                }
            }
        }
        return "'".implode("','",$list)."'";
    }

    private function validAccessForSS($accessStr,$system_id=""){
        $system_id = empty($system_id)?$systemId = Yii::app()->params['systemId']:$system_id;
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()
            ->select("username")
            ->from("security{$suffix}.sec_user_access")
            ->where("username=:uid and system_id=:system_id and a_control like '%{$accessStr}%'",array(
                ":uid"=>Yii::app()->user->id,
                ":system_id"=>$system_id,
            ))->queryRow();
        return $row?true:false;
    }

    //获取KA所有员工
    protected function getKaManForKaBot(){
        $maxYear = $this->search_year;
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = "sal";
        $city_allow = Yii::app()->user->city_allow();
        $whereSql = "f.a_read_write like '%KA01%'";
        if($this->validAccessForSS('CN15',$systemId)){
            $whereSql.= " and (h.staff_status!=-1 or (h.staff_status=-1 and DATE_FORMAT(h.leave_time,'%Y')>={$maxYear}))";//2023/06/16 改為可以看的所有記錄
        }elseif($this->validAccessForSS('CN19',$systemId)){
            $idSQL = $this->getGroupIDStrForEmployeeID($this->employee_id);
            $whereSql.= " and (h.id in ({$idSQL}) or h.id in ({$idSQL}) or h.city in ({$city_allow}))";
        }else{
            $idSQL = $this->getGroupIDStrForEmployeeID($this->employee_id);
            $whereSql.= " and h.id in ({$idSQL})";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("h.id,h.code,h.name,h.city,h.entry_time,h.table_type")
            ->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee h","a.employee_id=h.id")
            ->leftJoin("security{$suffix}.sec_user_access f","f.username=a.user_id and f.system_id='{$systemId}'")
            ->where($whereSql)
            ->group("h.id,h.code,h.name,h.city,h.entry_time,h.table_type")
            ->order("h.table_type asc,h.city,h.id")
            ->queryAll();
        return $rows?$rows:array();
    }

    public function getKASalesGroup(){
        $suffix = Yii::app()->params['envSuffix'];
        $groupList = array();
        $rows = Yii::app()->db->createCommand()->select("a.employee_id,a.group_id,b.group_name")
            ->from("hr{$suffix}.hr_group_staff a")
            ->leftJoin("hr{$suffix}.hr_group b","a.group_id=b.id")
            ->where("b.group_code='KAGROUP'")
            ->order("a.group_id asc")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $groupList[$row["employee_id"]] = $row;
            }
        }
        return $groupList;
    }

    private function getSalesmanStr($list){
        $arr = array();
        if(!empty($list)){
            foreach ($list as $row){
                $arr[]=$row["id"];
            }
        }
        return "-1,".implode(",",$arr);
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->computeDate();
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $kaManList = $this->getKaManForKaBot();//KA所有员工
        $kaGroupList = $this->getKASalesGroup();//KA分组

        $salesman_str = $this->getSalesmanStr($kaManList);
        $stopListAmt = CountSearch::getServiceForTypeToMonthAndSales($startDate,$endDate,$salesman_str,"T");
        $retentionAmt = CountSearch::getRetentionAmtForSales($endDate,$salesman_str);
        $data=array("group"=>array(),"staff"=>array());//排序，分组的员工置顶
        foreach ($kaManList as $row){
            $temp = $this->defMoreCity();
            $ka_id = "".$row["id"];
            $city = $row["city"];
            if(key_exists($ka_id,$kaGroupList)){
                $keyStr = "group";
                $group_id = $kaGroupList[$ka_id]["group_id"];
                $temp["group_name"] = $kaGroupList[$ka_id]["group_name"];
            }else{
                $keyStr = "staff";
                $group_id = $city."_".$ka_id;
                $temp["group_name"] = "独立组";
            }
            $temp["city"] = $city;
            $temp["employee_id"] = $ka_id;
            $temp["entry_date"] = General::toDate($row["entry_time"]);
            $temp["employee_name"] = $row["name"]." ({$row["code"]})";
            //$this->addTempForList($temp,$listVQS,$ka_id);
            if(key_exists($ka_id,$retentionAmt)){//有月初生意额的员工才会统计
                $temp["per_month_amt"] = $retentionAmt[$ka_id];
                $this->addTempForList($temp,$stopListAmt,$ka_id);

                $data[$keyStr][$group_id][$ka_id] = $temp;
            }
        }
        $this->data = $data["group"];
        if(!empty($data["staff"])){
            foreach ($data["staff"] as $key=>$row){
                $this->data[$key]=$row;
            }
        }
        $this->u_load_data['u_load_start'] = time();
        $this->u_load_data['u_load_end'] = time();

        $session = Yii::app()->session;
        $session['retentionKARate_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    protected function addTempForList(&$temp,$list,$ka_id){
        if(key_exists($ka_id,$list)){
            foreach ($list[$ka_id] as $key=>$item){
                if(key_exists($key,$temp)){
                    if(is_numeric($temp[$key])){
                        $temp[$key]+= $item;
                    }else{
                        $temp[$key] = $item;
                    }
                }
            }
        }
    }

    //設置該城市的默認值
    private function defMoreCity(){
        $arr=array(
            "city"=>"",
            "employee_id"=>"",
            "kam_name"=>"",
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $arr[$keyStr]=0;
        }
        $arr["per_ytd_stop_amt"]=0;//YTD终止合同金额
        $arr["ytd_month_length"]=$this->search_month_end-$this->search_month_start+1;//YTD月份数
        $arr["per_month_amt"]=0;//月初合同总额
        $arr["per_retention_rate"]="-";//个人保留率

        $arr["group_ytd_stop_amt"]=0;//主管终止合同金额
        $arr["group_month_amt"]=0;//主管月初合同总额
        $arr["group_retention_rate"]="-";//主管保留率
        $arr["ka_ytd_stop_amt"]=0;//KA团队终止合同金额
        $arr["ka_month_amt"]=0;//KA团队月初合同总额
        $arr["ka_retention_rate"]="-";//KA团队保留率
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        $monthLength = $list["ytd_month_length"];
        $list["per_ytd_stop_amt"]=0;
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $list["per_ytd_stop_amt"]+=key_exists($keyStr,$list)?$list[$keyStr]:0;
        }

        $list["per_retention_rate"]= $this->computeRetentionRate($list["per_ytd_stop_amt"],$list["per_month_amt"],$monthLength);
    }

    protected function computeRetentionRate($stopAmt,$allAmt,$monthLength){
        if(empty($allAmt)){
            return "-";
        }else{
            $retention_rate = ($stopAmt/$monthLength)*12;
            $retention_rate/= $allAmt;
            $retention_rate = 1-$retention_rate;
            $retention_rate = round($retention_rate,4);
            $retention_rate= ($retention_rate*100)."%";

            return $retention_rate;
        }
    }

    //顯示提成表的表格內容
    public function retentionKARateHtml(){
        $html= "<table id=\"retentionKARate\" class=\"table table-fixed table-condensed table-bordered table-hover\">";
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","Group Name")),//组别
            array("name"=>Yii::t("summary","Employee Name")),//姓名
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $topList[]=array("name"=>$i.Yii::t("summary"," month"));
        }
        //YTD终止合同金额
        $topList[]=array("name"=>Yii::t("summary","person ").Yii::t("summary","YTD ").Yii::t("summary","stop contract amt"));
        //YTD月份数
        $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","all month length"));
        //月初合同总额
        $topList[]=array("name"=>Yii::t("summary","all month amt"));
        //个人保留率
        $topList[]=array("name"=>Yii::t("summary","person ").Yii::t("summary","retention rate"));
        //主管YTD终止合同金额
        $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","YTD ").Yii::t("summary","stop contract amt"));
        //主管保留率
        $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","retention rate"));
        //KA团队YTD 终止合同金额
        $topList[]=array("name"=>Yii::t("summary","KA team ").Yii::t("summary","YTD ").Yii::t("summary","stop contract amt"));
        //KA团队保留率
        $topList[]=array("name"=>Yii::t("summary","KA team ").Yii::t("summary","retention rate"));

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
            "group_name",
            "employee_name",
        );
        for ($i=$this->search_month_start;$i<=$this->search_month_end;$i++){
            $month = $i<10?"0{$i}":$i;
            $keyStr = $this->search_year."/".$month;
            $bodyKey[]=$keyStr;
        }
        $bodyKey[]="per_ytd_stop_amt";
        $bodyKey[]="ytd_month_length";
        $bodyKey[]="per_month_amt";
        $bodyKey[]="per_retention_rate";

        $bodyKey[]="group_ytd_stop_amt";//主管终止合同金额
        $bodyKey[]="group_retention_rate";//主管保留率
        $bodyKey[]="ka_ytd_stop_amt";//KA团队终止合同金额
        $bodyKey[]="ka_retention_rate";//KA团队保留率
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
        if($keyStr=="ytd_month_length"){
            return $num;
        }
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
    protected function showServiceHtml($data){
        $monthLength = $this->search_month_end-$this->search_month_start+1;
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = ["ka_ytd_stop_amt"=>0,"ka_month_amt"=>0,"city"=>"","staffID"=>0,"rowspan"=>0];//总计(所有地区)
            foreach ($data as $city=>$row){
                $currentRow = $row;
                $staff_id=array_shift($currentRow)["employee_id"];
                $rowspan = count($row);
                if(empty($allRow["city"])){
                    $allRow["city"] = $city;
                    $allRow["staffID"] = $staff_id;
                }
                $allRow["rowspan"]+= $rowspan;
                $regionRow = ["group_ytd_stop_amt"=>0,"group_month_amt"=>0];//分组汇总
                foreach ($row as $list){
                    $id = $list["employee_id"];
                    $this->resetTdRow($list);
                    $html.="<tr>";
                    foreach ($bodyKey as $keyStr){
                        if(in_array($keyStr,array("group_ytd_stop_amt","group_retention_rate","ka_ytd_stop_amt","ka_retention_rate"))){
                            $this->downJsonText["excel"][$city][$staff_id][$keyStr]=0;
                            $html.=($keyStr=="group_ytd_stop_amt"&&$staff_id==$id)?":groupMoneyHtml:":"";
                            continue;
                        }
                        $text = key_exists($keyStr,$list)?$list[$keyStr]:"0";
                        if($keyStr=="per_ytd_stop_amt"){
                            $regionRow["group_ytd_stop_amt"]+=is_numeric($text)?floatval($text):0;
                            $allRow["ka_ytd_stop_amt"]+=is_numeric($text)?floatval($text):0;
                        }
                        if($keyStr=="per_month_amt"){
                            $regionRow["group_month_amt"]+=is_numeric($text)?floatval($text):0;
                            $allRow["ka_month_amt"]+=is_numeric($text)?floatval($text):0;
                        }
                        if(!key_exists($keyStr,$allRow)){
                            $allRow[$keyStr]=0;
                        }
                        $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                        $text = self::showNum($keyStr,$text);
                        $this->downJsonText["excel"][$city][$id][]=$text;
                        $class="";
                        $title="";
                        $html.="<td class='{$class}' data-title='{$title}' data-type='{$keyStr}' data-employee_id='{$list['employee_id']}'>";
                        $html.="<span>{$text}</span></td>";
                        $html.="</td>";
                    }
                    $html.="</tr>";
                }

                if(strpos($html,":groupMoneyHtml:")!==false){
                    $regionRow["group_retention_rate"]= $this->computeRetentionRate($regionRow["group_ytd_stop_amt"],$regionRow["group_month_amt"],$monthLength);
                    $groupHtml="<td rowspan='{$rowspan}'>".$regionRow["group_ytd_stop_amt"]."</td>";
                    $groupHtml.="<td rowspan='{$rowspan}'>".$regionRow["group_retention_rate"]."</td>";
                    $groupHtml.=$allRow["staffID"]==$staff_id?":kaMoneyHtml:":"";
                    $html=str_replace(":groupMoneyHtml:", $groupHtml, $html);
                    $this->downJsonText["excel"][$city][$staff_id]['group_ytd_stop_amt']=array("groupLen"=>$rowspan,"text"=>$regionRow["group_ytd_stop_amt"]);
                    $this->downJsonText["excel"][$city][$staff_id]['group_retention_rate']=array("groupLen"=>$rowspan,"text"=>$regionRow["group_retention_rate"]);
                }
            }
            //所有汇总
            if(strpos($html,":kaMoneyHtml:")!==false){
                $rowspan = $allRow["rowspan"];
                $city = $allRow["city"];
                $staff_id = $allRow["staffID"];
                $allRow["ka_retention_rate"]= $this->computeRetentionRate($allRow["ka_ytd_stop_amt"],$allRow["ka_month_amt"],$monthLength);
                $groupHtml="<td rowspan='{$rowspan}'>".$allRow["ka_ytd_stop_amt"]."</td>";
                $groupHtml.="<td rowspan='{$rowspan}'>".$allRow["ka_retention_rate"]."</td>";
                $html=str_replace(":kaMoneyHtml:", $groupHtml, $html);
                $this->downJsonText["excel"][$city][$staff_id]['ka_ytd_stop_amt']=array("groupLen"=>$rowspan,"text"=>$allRow["ka_ytd_stop_amt"]);
                $this->downJsonText["excel"][$city][$staff_id]['ka_retention_rate']=array("groupLen"=>$rowspan,"text"=>$allRow["ka_retention_rate"]);
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    public static function getRateForNumber($number){
        $rate = "";
        if(is_numeric($number)){
            $rate = $number*100;
            $rate = round($rate);
            $rate = "".$rate."%";
        }
        return $rate;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = "";
            $text = self::showNum($text,$keyStr);
            $this->downJsonText["excel"][$data['city']]["count"][]=$text;
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
        $titleName = Yii::t("app","Retention KA rate");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setUServiceHeader($headList);
        $excel->setKAData($excelData);
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