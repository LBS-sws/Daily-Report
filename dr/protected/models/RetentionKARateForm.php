<?php

class RetentionKARateForm extends CFormModel
{
    /* User Fields */
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $start_date;
    public $end_date;
    public $month_length;
    public $employee_id;

    public $data=array();

    public $th_sum=0;//所有th的个数

    public $downJsonText='';

    protected $class_type="NONE";//类型 NONE:普通  KA:KA

    protected $nowDateKey="";//一次性服务（当前月份主键）
    protected $lastDateKey="";//一次性服务（上月份主键）
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
        if($this->search_month==1){//查询1月时强制搜索去年12月
            $this->start_date = date("Y/m/d",strtotime(($this->search_year-1)."/12/01"));
            $this->end_date = date("Y/m/t",strtotime("{$this->search_year}/{$this->search_month}/01"));
            $this->month_length=2;
        }else{
            $this->start_date = date("Y/m/d",strtotime("{$this->search_year}/01/01"));
            $this->end_date = date("Y/m/t",strtotime("{$this->search_year}/{$this->search_month}/01"));
            $this->month_length=$this->search_month;
        }
        $dateTime = date("Y/m/01",strtotime($this->end_date));
        $this->nowDateKey = "one_".date("Y/m",strtotime($dateTime));
        $this->lastDateKey = "one_".date("Y/m",strtotime($dateTime." - 1 months"));

        $this->employee_id = self::getEmployeeIDForUsername();
    }

    public static function getGroupIDStrForEmployeeID($employee_id){
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

    public static function validAccessForSS($accessStr,$system_id=""){
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

    public static function getEmployeeIDForUsername($username=""){
        $suffix = Yii::app()->params['envSuffix'];
        $username = empty($username)?Yii::app()->user->id:$username;
        $row = Yii::app()->db->createCommand()
            ->select("employee_id")->from("hr{$suffix}.hr_binding")
            ->where("user_id=:uid",array(":uid"=>$username))->queryRow();
        return $row?$row["employee_id"]:null;
    }

    //获取KA所有员工
    public static function getKaManForKaBot($searchYear,$employee_id){
        $maxYear = $searchYear;
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = "sal";
        $city_allow = Yii::app()->user->city_allow();
        $whereSql = "f.a_read_write like '%KA01%'";
        if(self::validAccessForSS('CN15',$systemId)){
            $whereSql.= " and (h.staff_status!=-1 or (h.staff_status=-1 and DATE_FORMAT(h.leave_time,'%Y')>={$maxYear}))";//2023/06/16 改為可以看的所有記錄
        }elseif(self::validAccessForSS('CN19',$systemId)){
            $idSQL = self::getGroupIDStrForEmployeeID($employee_id);
            $whereSql.= " and (h.id in ({$idSQL}) or h.id in ({$idSQL}) or h.city in ({$city_allow}))";
        }else{
            $idSQL = self::getGroupIDStrForEmployeeID($employee_id);
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
        $idArr = array(-1);
        $codeArr = array();
        if(!empty($list)){
            foreach ($list as $row){
                $idArr[]=$row["id"];
                if(!empty($row["code"])){
                    $codeArr[]=$row["code"];
                }
            }
        }
        return array("idList"=>implode(",",$idArr),"codeList"=>implode(",",$codeArr));
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->computeDate();
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $kaManList = self::getKaManForKaBot($this->search_year,$this->employee_id);//KA所有员工
        $kaGroupList = $this->getKASalesGroup();//KA分组

        $salesman_list = $this->getSalesmanStr($kaManList);
        $salesman_str = $salesman_list["idList"];
        $salesman_code = $salesman_list["codeList"];
        $this->u_load_data['u_load_start'] = time();
        //获取销售的工单金额（月为主键）
        $uOneService = SystemU::getUServiceToMonthBySales($startDate,$endDate,$salesman_code);
        $this->u_load_data['u_load_end'] = time();
        //非终止的所有金额(更改、恢复、暂停)
        $ARSListAmt = CountSearch::getServiceARSToMonthAndSales($startDate,$endDate,$salesman_str);
        $stopListAmt = CountSearch::getServiceForTypeToMonthAndSales($startDate,$endDate,$salesman_str,"T");
        $retentionAmt = CountSearch::getRetentionAmtForSales($endDate,$salesman_str);
        $data=array("group"=>array(),"staff"=>array());//排序，分组的员工置顶
        foreach ($kaManList as $row){
            $temp = $this->defMoreCity();
            $ka_id = "".$row["id"];
            $u_code = "".$row["code"];
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
            $temp["per_month_amt"] = key_exists($ka_id,$retentionAmt)?$retentionAmt[$ka_id]:0;
            $this->addTempForList($temp,$stopListAmt,$ka_id,0);
            $this->addTempForList($temp,$ARSListAmt,$ka_id);
            $this->addTempForListForU($temp,$uOneService["data"],$u_code);

            $data[$keyStr][$group_id][$ka_id] = $temp;
        }
        $this->data = $data["group"];
        if(!empty($data["staff"])){
            foreach ($data["staff"] as $key=>$row){
                $this->data[$key]=$row;
            }
        }

        $session = Yii::app()->session;
        $session['retentionKARate_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    protected function addTempForListForU(&$temp,$list,$ka_code,$addBool=1){
        if(key_exists($ka_code,$list)){
            foreach ($list[$ka_code] as $key=>$item){
                $tempKey = "one_".$key;
                if(key_exists($tempKey,$temp)){
                    if(is_numeric($temp[$tempKey])){
                        if($addBool==1){
                            $temp[$tempKey]+= $item;
                        }else{
                            $temp[$tempKey]-= $item;
                        }
                    }else{
                        $temp[$tempKey] = $item;
                    }
                }
            }
        }
    }

    protected function addTempForList(&$temp,$list,$ka_id,$addBool=1){
        if(key_exists($ka_id,$list)){
            foreach ($list[$ka_id] as $key=>$item){
                if(key_exists($key,$temp)){
                    if(is_numeric($temp[$key])){
                        if($addBool==1){
                            $temp[$key]+= $item;
                        }else{
                            $temp[$key]-= $item;
                        }
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
        for ($i=0;$i<$this->month_length;$i++){
            $keyStr = $i==0?date("Y/m",strtotime($this->start_date)):date("Y/m",strtotime($this->start_date." + {$i} months"));
            $arr[$keyStr]=0;//长约
            $arr["one_".$keyStr]=0;//一次性
        }
        $arr["per_ytd_stop_amt"]=0;//YTD终止合同金额
        $arr["ytd_month_length"]=$this->month_length;//YTD月份数
        $arr["per_month_amt"]=0;//月初合同总额
        $arr["per_retention_rate"]="-";//个人保留率

        $arr["group_ytd_stop_amt"]=0;//主管终止合同金额
        $arr["group_month_amt"]=0;//主管月初合同总额
        $arr["group_retention_rate"]="-";//主管保留率
        return $arr;
    }

    protected function resetTdRow(&$list,$type,$bool=false){
        $monthLength = $this->month_length;//YTD月份数
        if($bool){
            $list["ytd_month_length"]= $monthLength;
            if(empty($type)){//长约
                if($list["group_ytd_stop_amt"]<0){
                    $list["group_ytd_stop_amt"]*=-1;
                }
                $list["group_retention_rate"]= $this->computeRetentionRate($list["group_ytd_stop_amt"],$list["group_month_amt"],$monthLength);
            }else{
                $list["group_retention_rate"]= $this->computeOneRate($list["group_month_amt"],$list["group_ytd_stop_amt"]);
            }
        }
        if(empty($type)){//长约
            $list["per_ytd_stop_amt"]=0;
            for ($i=0;$i<$this->month_length;$i++){
                $keyStr = $i==0?date("Y/m",strtotime($this->start_date)):date("Y/m",strtotime($this->start_date." + {$i} months"));
                $list["per_ytd_stop_amt"]+=key_exists($keyStr,$list)?$list[$keyStr]:0;
            }
            if($list["per_ytd_stop_amt"]<0){
                $list["per_ytd_stop_amt"]*=-1;
            }
            $list["per_retention_rate"]= $this->computeRetentionRate($list["per_ytd_stop_amt"],$list["per_month_amt"],$monthLength);
        }else{//一次性
            //"group_ytd_stop_amt"
            //,"group_month_amt"
            $nowAmtKey = $this->nowDateKey;
            $lastAmtKey = $this->lastDateKey;
            $nowAmt = key_exists($nowAmtKey,$list)?$list[$nowAmtKey]:0;
            $lastAmt = key_exists($lastAmtKey,$list)?$list[$lastAmtKey]:0;
            $list["per_retention_rate"] = $this->computeOneRate($lastAmt,$nowAmt);
        }
    }

    protected function computeOneRate($lastAmt,$nowAmt){
        if(empty($nowAmt)){
            return "-";
        }else{
            $retention_rate = $lastAmt-$nowAmt;
            $retention_rate/= $nowAmt;
            $retention_rate = 1-$retention_rate;
            $retention_rate = round($retention_rate,4);
            $retention_rate= ($retention_rate*100)."%";

            return $retention_rate;
        }
    }

    protected function computeRetentionRate($stopAmt,$allAmt,$monthLength){
        if(empty($allAmt)||empty($monthLength)){
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
    public function retentionKARateHtml($type=0){
        $html= "<table id=\"retentionKARate{$type}\" class=\"table table-fixed table-condensed table-bordered table-hover\">";
        $html.=$this->tableTopHtml($type);
        $html.=$this->tableBodyHtml($type);
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr($type=0){
        $topList=array();
        for ($i=0;$i<$this->month_length;$i++){
            $keyStr = $i==0?date("n",strtotime($this->start_date)):date("n",strtotime($this->start_date." + {$i} months"));
            $topList[]=array("name"=>$keyStr.Yii::t("summary"," month"));
        }
        if(empty($type)){//长约
            //YTD综合停单金额
            $topList[]=array("name"=>Yii::t("summary","YTD stop amt"));
            //YTD月份数
            $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","all month length"));
            //个人生效中合同总额
            $topList[]=array("name"=>Yii::t("summary","person ").Yii::t("summary","effect amt"));
            //个人保留率
            $topList[]=array("name"=>Yii::t("summary","person ").Yii::t("summary","retention rate"));
            //主管YTD终止合同金额
            $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","YTD ").Yii::t("summary","stop contract amt"));
            //主管生效中合同总额
            $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","effect amt"));
            //主管保留率
            $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","retention rate"));

            $titleName =Yii::t("summary","Long Service");
        }else{//一次性服务
            //YTD月份数
            $topList[]=array("name"=>Yii::t("summary","YTD ").Yii::t("summary","all month length"));
            //个人保留率
            $topList[]=array("name"=>Yii::t("summary","person ").Yii::t("summary","retention rate"));
            //小组当月服务生意额
            $topList[]=array("name"=>Yii::t("summary","group now month amt"));
            //小组上月服务生意额
            $topList[]=array("name"=>Yii::t("summary","group last month amt"));
            //主管保留率
            $topList[]=array("name"=>Yii::t("summary","manager ").Yii::t("summary","retention rate"));

            $titleName =Yii::t("summary","One Service");
        }
        $headList=array(
            array("name"=>Yii::t("summary","Group Name"),"rowspan"=>2),//组别
            array("name"=>Yii::t("summary","Employee Name"),"rowspan"=>2),//姓名
            array("name"=>$titleName,'colspan'=>$topList),//
        );
        return $headList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml($type=0){
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

    public function tableBodyHtml($type=0){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data,$type);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            if(empty($type)){
                $html.=TbHtml::hiddenField("excel",$this->downJsonText);
            }else{
                $html.=TbHtml::hiddenField("excelTwo",$this->downJsonText);
            }
        }
        return $html;
    }
    //获取td对应的键名
    private function getDataAllKeyStr($type){
        $bodyKey = array(
            "group_name",
            "employee_name",
        );
        for ($i=0;$i<$this->month_length;$i++){
            $keyStr = $i==0?date("Y/m",strtotime($this->start_date)):date("Y/m",strtotime($this->start_date." + {$i} months"));
            if(empty($type)){
                $bodyKey[]=$keyStr;
            }else{
                $bodyKey[]="one_".$keyStr;
            }
        }
        if(empty($type)){
            $bodyKey[]="per_ytd_stop_amt";
            $bodyKey[]="ytd_month_length";
            $bodyKey[]="per_month_amt";
            $bodyKey[]="per_retention_rate";
        }else{
            $bodyKey[]="ytd_month_length";
            $bodyKey[]="per_retention_rate";
        }

        $bodyKey[]="group_ytd_stop_amt";//主管终止合同金额
        $bodyKey[]="group_month_amt";//主管生效中合同总额
        $bodyKey[]="group_retention_rate";//主管保留率
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
    protected function showServiceHtml($data,$type){
        $monthLength = $this->month_length;
        $bodyKey = $this->getDataAllKeyStr($type);
        $html="";
        if(!empty($data)){
            $allRow = ["group_ytd_stop_amt"=>0,"group_month_amt"=>0,"city"=>"","staffID"=>0,"rowspan"=>0];//总计(所有地区)
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
                    $this->resetTdRow($list,$type);
                    $html.="<tr>";
                    foreach ($bodyKey as $keyStr){
                        if(in_array($keyStr,array("group_ytd_stop_amt","group_month_amt","group_retention_rate"))){
                            $this->downJsonText["excel"][$city][$staff_id][$keyStr]=0;
                            $html.=($keyStr=="group_ytd_stop_amt"&&$staff_id==$id)?":groupMoneyHtml:":"";
                            continue;
                        }
                        $text = key_exists($keyStr,$list)?$list[$keyStr]:"0";
                        $bool = empty($type)&&$keyStr=="per_ytd_stop_amt";
                        $bool = $bool||(!empty($type)&&$keyStr==$this->nowDateKey);
                        if($bool){
                            $regionRow["group_ytd_stop_amt"]+=is_numeric($text)?floatval($text):0;
                            $allRow["group_ytd_stop_amt"]+=is_numeric($text)?floatval($text):0;
                        }
                        $bool = empty($type)&&$keyStr=="per_month_amt";
                        $bool = $bool||(!empty($type)&&$keyStr==$this->lastDateKey);
                        if($bool){
                            $regionRow["group_month_amt"]+=is_numeric($text)?floatval($text):0;
                            $allRow["group_month_amt"]+=is_numeric($text)?floatval($text):0;
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
                    if(empty($type)){
                        if($regionRow["group_ytd_stop_amt"]<0){
                            $regionRow["group_ytd_stop_amt"]*=-1;
                        }
                        $regionRow["group_retention_rate"]= $this->computeRetentionRate($regionRow["group_ytd_stop_amt"],$regionRow["group_month_amt"],$monthLength);
                    }else{
                        $regionRow["group_retention_rate"]= $this->computeOneRate($regionRow["group_month_amt"],$regionRow["group_ytd_stop_amt"]);
                    }
                    $groupHtml="<td rowspan='{$rowspan}'>".self::showNum("aaa",$regionRow["group_ytd_stop_amt"])."</td>";
                    $groupHtml.="<td rowspan='{$rowspan}'>".self::showNum("aaa",$regionRow["group_month_amt"])."</td>";
                    $groupHtml.="<td rowspan='{$rowspan}'>".$regionRow["group_retention_rate"]."</td>";
                    $html=str_replace(":groupMoneyHtml:", $groupHtml, $html);
                    $this->downJsonText["excel"][$city][$staff_id]['group_ytd_stop_amt']=array("groupLen"=>$rowspan,"text"=>$regionRow["group_ytd_stop_amt"]);
                    $this->downJsonText["excel"][$city][$staff_id]['group_month_amt']=array("groupLen"=>$rowspan,"text"=>$regionRow["group_month_amt"]);
                    $this->downJsonText["excel"][$city][$staff_id]['group_retention_rate']=array("groupLen"=>$rowspan,"text"=>$regionRow["group_retention_rate"]);
                }
            }
            //所有汇总
            $allRow["group_name"]="";
            $allRow["employee_name"]="Total";
            $allRow["city"]="All";
            $html.=$this->printTableTr($allRow,$bodyKey,$type);
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

    protected function printTableTr($data,$bodyKey,$type){
        $this->resetTdRow($data,$type,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"";
            $tdClass = "";
            $text = self::showNum($keyStr,$text);
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
        $excelDataTwo = array();
        if(key_exists("excelTwo",$_POST)){
            $excelDataTwo = json_decode($_POST["excelTwo"],true);
            $excelDataTwo = key_exists("excel",$excelDataTwo)?$excelDataTwo["excel"]:array();
        }
        $this->computeDate();
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $titleName = Yii::t("app","Retention KA rate");
        $sheetName = Yii::t("summary","Long Service Rate");
        $excel->SetHeaderTitle($sheetName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSheetName($sheetName);
        $excel->setSummaryHeader($headList);
        $excel->setKAData($excelData);
        //第二页
        $headList = $this->getTopArr(1);
        $sheetName = Yii::t("summary","One Service Rate");
        $excel->addSheet($sheetName);
        $excel->SetHeaderTitle($sheetName);
        $excel->outHeader(1);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->setSummaryHeader($headList);
        $excel->setKAData($excelDataTwo);

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