<?php

class ManageMonthBonusForm extends CFormModel
{
	/* User Fields */
	public $search_year;
	public $search_month;
	public $start_date;
	public $end_date;
	public $status_type;//1:已固定
	public $update_user;//操作用户
	public $update_date;//操作时间

	public $data=array();
	public $bonusData=array();
	public $cityForStaffID=array();//城市转员工id

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
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date'),
            'search_year'=>Yii::t('summary','search year'),
            'search_month'=>Yii::t('summary','search month'),
            'status_type'=>Yii::t('misc','Status'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('search_year,search_month','safe'),
			array('search_year,search_month','required'),
            array('search_month','validateDate'),
		);
	}

    public function validateDate($attribute, $params) {
	    $dateStr = $this->search_year."/".$this->search_month."/01";
	    $this->start_date = date_format(date_create($dateStr),"Y/m/01");
	    $this->end_date = date_format(date_create($dateStr),"Y/m/t");
	    if($this->start_date>date_format(date_create(),"Y/m/01")){
            $this->addError($attribute, "查询时间不能大于".date_format(date_create(),"Y年n月"));
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
            'search_year'=>$this->search_year,
            'search_month'=>$this->search_month
        );
    }

    private function resetBonusData($data){
	    $list =array();
	    foreach ($data as $topList){
	        if(!empty($topList["list"])){
	            foreach ($topList["list"] as $row){
	                $city = $row["city"];
                    $this->resetBonusRow($row);
	                $list[$city] = $row;
                }
            }
        }
	    return $list;
    }

    private function resetBonusRow(&$list){
        $list["num_growth"]=0;
        $list["num_growth"]+=$list["new_sum"]+$list["new_sum_n"]+$list["new_month_n"];
        $list["num_growth"]+=$list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"];
        $list["num_growth"]+=$list["amend_sum"];
        if(date_format(date_create($this->end_date),'Y/m')>CountSearch::$stop_new_dt){
            $list["num_growth"]=$list["num_growth"]+$list["stop_2024_11"];
        }

        $list["comStopRate"] = $list["stop_sum_none"]+$list["resume_sum"]+$list["pause_sum"]+$list["amend_sum"];
        $list["comStopRate"]/= 12;//
        $lastSum = $list["new_month_n"]+$list["last_u_actual"];
        $list["comStopRate_top"] = $list["comStopRate"];
        $list["comStopRate_bottom"] = $lastSum;
        $list["comStopRate"] = ComparisonForm::comparisonRate($list["comStopRate"],$lastSum);

        //$list["two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["two_net"],"net");
        $list["month_net_amt"] = $list["num_growth"];
        $list["city_month_new_amt"]=$list["city_num_new"]+$list["city_update_add"]+$list["city_one_service"];
        $list["month_new_amt"]=$list["new_sum"]+$list["num_update_add"]+$list["one_service"];
        //$list["month_net_tar"] = self::comparisonNetRate($list["num_growth"],$list["two_net"]);
    }

    public function saveCache(){
        $suffix = Yii::app()->params['envSuffix'];
        $update_user = Yii::app()->getComponent('user')===null?"admin":Yii::app()->user->id;
        $update_date = date_format(date_create(),"Y/m/d H:i:s");
        $cityList = array("11");
        $cityRows = Yii::app()->db->createCommand()->select("code")->from("security{$suffix}.sec_city")
            ->where("1=1")->queryAll();
        if($cityRows){
            foreach ($cityRows as $cityRow){
                $cityList[]=$cityRow["code"];
            }
        }
        $city_allow = "'".implode("','",$cityList)."'";
        $this->setModelData($city_allow);
        $cacheRow = Yii::app()->db->createCommand()->select("id")->from("swo_manage_cache")
            ->where("year_no=:year and month_no=:month",array(
                ":year"=>$this->search_year,
                ":month"=>$this->search_month,
            ))->queryRow();
        if($cacheRow){
            Yii::app()->db->createCommand()->update("swo_manage_cache",array(
                "dataJson"=>json_encode($this->data),
                "status_type"=>1,
                "update_user"=>$update_user,
                "update_date"=>$update_date,
            ),"id=".$cacheRow["id"]);
        }else{
            Yii::app()->db->createCommand()->insert("swo_manage_cache",array(
                "year_no"=>$this->search_year,
                "month_no"=>$this->search_month,
                "dataJson"=>json_encode($this->data),
                "status_type"=>1,
                "update_user"=>$update_user,
                "update_date"=>$update_date,
            ));
        }

        return $this->sendBsData();
    }

    private function sendBsData(){
        $saveArr= array("bool"=>true,"message"=>"");
        $bsCurlModel = new BsCurlModel();
        $bsCurlModel->sendData = $this->getCurlData();
        $curlData = $bsCurlModel->sendBsCurl();
        if($curlData["code"]!=200){//curl异常，不继续执行
            $bsCurlModel->logError($curlData);
            $saveArr["bool"]=false;
            $saveArr["message"]=$curlData["message"];
        }
        return $saveArr;
    }

    private function getCurlData(){
        $suffix = Yii::app()->params['envSuffix'];
        $startDate = date("Y/m/01",strtotime("{$this->search_year}-{$this->search_month}-01"));
        $stopDate = date("Y/m/t",strtotime($startDate));
        $models = array();
        if(!empty($this->data)){
            foreach ($this->data as $row){
                if(!empty($row["employee_id"])){
                    $employee_id = "".$row["employee_id"];
                    if(!key_exists($employee_id,$models)){
                        $staffRow = Yii::app()->db->createCommand()->select("bs_staff_id")->from("hr{$suffix}.hr_employee")
                            ->where("id=:id",array(":id"=>$employee_id))->queryRow();
                        $models[$employee_id]=array(
                            "staffId"=>$staffRow["bs_staff_id"],
                            "itemName"=>6,//城市总&副总监提成
                            "startDate"=>$startDate,
                            "stopDate"=>$stopDate,
                            "numericVal"=>0,
                        );
                    }
                    $models[$employee_id]["numericVal"]+=empty($row["goal_all_bonus"])?0:floatval($row["goal_all_bonus"]);
                }
            }
        }

        return array(
            "presetSalarySubsetCode"=>"PresetSalarySubset1",
            "models"=>array_values($models)
        );
    }

    private function setModelData($city_allow){
        $this->data = array();
        $city_allow = empty($city_allow)?Yii::app()->user->city_allow():$city_allow;
        $staffSetList = ManageStaffSetForm::getStaffListForCityAllow($city_allow,$this->search_year,$this->search_month);
        $staffLists = $staffSetList["staffRow"];
        $bonusModel = new BonusMonthForm();
        $bonusModel->search_year = $this->search_year;
        $bonusModel->search_month = $this->search_month;
        $bonusModel->start_date = $this->start_date;
        $bonusModel->end_date = $this->end_date;
        $bonusModel->retrieveData($city_allow);
        $bonusData = $bonusModel->data;
        $this->u_load_data = $bonusModel->u_load_data;
        $this->bonusData = $this->resetBonusData($bonusData);
        foreach ($staffLists as $staffList){
            $setID = $staffList["id"];
            $cityList = explode(",",$staffList["city_allow"]);
            if(count($cityList)==1){
                $city = $cityList[0];
                if(!key_exists($city,$this->cityForStaffID)){
                    $this->cityForStaffID[$city] = $setID;
                }
            }
            $defMoreList=$this->defMoreCity($cityList,$staffList["city_allow_name"]);
            $this->addDefListForArr($defMoreList,$staffList);
            $employName = "{$staffList["employee_name"]} ({$staffList["employee_code"]})";
            $defMoreList["employee_name"] = empty($staffList["employee_id"])?"-":$employName;
            $defMoreList["year_month"] = $this->search_year."/".$this->search_month;

            $this->resetTdRow($defMoreList);
            $this->data[$setID] = $defMoreList;
        }
    }

    public function retrieveData($city_allow="",$refresh=false) {
        $this->u_load_data['load_start'] = time();
        $city_allow = empty($city_allow)?Yii::app()->user->city_allow():$city_allow;
        $row = Yii::app()->db->createCommand()->select("*")->from("swo_manage_cache")
            ->where("status_type=1 and year_no=:year and month_no=:month",array(
                ":year"=>$this->search_year,
                ":month"=>$this->search_month,
            ))->queryRow();
        if($refresh===false&&$row){//查询固定数据
            $this->status_type = $row["status_type"];
            $this->update_date = $row["update_date"];
            $this->update_user = $row["update_user"];
            $this->data = array();
            $data = json_decode($row["dataJson"],true);
            if(!empty($data)&&is_array($data)){
                foreach ($data as $row){
                    $okBool = true;
                    $cityList = $row["city_list"];
                    if(!empty($cityList)){
                        foreach ($cityList as $city){
                            if (strpos($city_allow,"'{$city}'")===false){
                                $okBool = false;
                                break;
                            }
                        }
                    }
                    if($okBool){
                        $setID = "".$row["id"];
                        $this->data[$setID] = $row;
                    }
                }
            }
        }else{//实时刷新
            $this->setModelData($city_allow);
        }

        $session = Yii::app()->session;
        $session['manageMonthBonus_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    protected function addDefListForArr(&$temp,$list){
	    foreach ($list as $key=>$value){
	        if(key_exists($key,$temp)){
	            $temp[$key] = $value;
            }
        }
    }

    protected function defMoreCity($city_list,$city_allow_name){
        return array(
            "id"=>"",//配置的id(主键)
            "year_month"=>"",
            "city"=>"",
            "city_list"=>$city_list,
            "city_name"=>$city_allow_name,
            "employee_id"=>"",
            "employee_name"=>"",
            "dept_name"=>"",//职位
            "month_new_amt"=>0,//当月新生意额
            "month_net_amt"=>0,//当月净增长额
            "comStopRate"=>0,//综合停單率
            //"month_net_tar"=>"",//当月净增长目标达成
            "sign_tar"=>"",//新签金额达标
            "royalty_rate"=>"",//提成率
            "stop_rate_coe"=>"",//停单率调节系数
            "month_royalty"=>"",//月新生意额提成
            "person_royalty"=>"",//个人提成
            //"goal_ach_bonus"=>"",//目标达成奖奖金
            "goal_all_bonus"=>"",//合计月度提成奖金
            "end_bonus"=>"",//对应副总监/高级总经理管理提成

            "employee_code"=>"",//职位类型
            "job_key"=>0,//职位类型
            "team_rate"=>0,//团队提成率
            "person_type"=>1,//个人提成金额类型
            "person_money"=>0,//个人提成金额
            "condition_money"=>0,//新签金额不低于本金额
            "max_bonus"=>0,//最大目标金额
        );
    }

    protected function resetTdRow(&$list){
        $list["month_new_amt"]=0;
        $list["city_month_new_amt"]=0;
        $list["comStopRate_top"]=0;
        $list["comStopRate_bottom"]=0;
        $list["month_net_amt"]=0;
        $list["two_net"]=0;
        foreach ($list["city_list"] as $city){
            if(key_exists($city,$this->bonusData)){
                $list["month_new_amt"]+=$this->bonusData[$city]["month_new_amt"];
                $list["city_month_new_amt"]+=$this->bonusData[$city]["city_month_new_amt"];
                $list["comStopRate_top"]+=$this->bonusData[$city]["comStopRate_top"];
                $list["comStopRate_bottom"]+=$this->bonusData[$city]["comStopRate_bottom"];
                $list["month_net_amt"]+=$this->bonusData[$city]["month_net_amt"];
                $list["two_net"]+=$this->bonusData[$city]["two_net"];
            }
        }
        if($list["job_key"]==5){//地区主管需要剔除个人部分
            $list["month_new_amt"] = $list["city_month_new_amt"];
        }
        $list["comStopRate"] = ComparisonForm::comparisonRate($list["comStopRate_top"],$list["comStopRate_bottom"]);
        //当月净增长目标达成
        //$list["month_net_tar"] = self::comparisonNetRate($list["month_net_amt"],$list["two_net"]);
        //停单调解系数
        $list["stop_rate_coe"] = $this->getStopRateCoeForStopRate($list["comStopRate"]);
        $list["person_royalty"]=$list["person_money"];//个人提成
        if($list["condition_money"]<=$list["month_new_amt"]){
            $list["sign_tar"]=Yii::t("summary","fit in");
            $list["royalty_rate"]=floatval($list["team_rate"])."%";//提成率
        }else{
            $list["sign_tar"]=Yii::t("summary","fit out");
            $list["royalty_rate"]="0%";//提成率
        }
        //月新生意额提成
        $list["month_royalty"]=floatval($list["royalty_rate"])*floatval($list["stop_rate_coe"])*floatval($list["month_new_amt"])*0.01;
        $list["month_royalty"] = round($list["month_royalty"],2);
        //目标达成奖奖金
        /*
        if($list["month_net_amt"]>=$list["two_net"]){
            $list["goal_ach_bonus"]=floatval($list["month_net_amt"])*0.01;
            $list["goal_ach_bonus"] = $list["goal_ach_bonus"]<$list["max_bonus"]?round($list["goal_ach_bonus"],2):$list["max_bonus"];
        }else{
            $list["goal_ach_bonus"]=0;
        }
        */
        switch ($list["job_key"]){
            case 1://副总监
            case 2://高级总经理
                $list["comStopRate"]="";
                $list["month_net_amt"]="";
                //$list["month_net_tar"]="";
                $list["stop_rate_coe"]="";
                //$list["goal_ach_bonus"]="";
                $list["month_royalty"]=0;
                foreach ($list["city_list"] as $city){
                    if(key_exists($city,$this->cityForStaffID)){
                        $setID = "".$this->cityForStaffID[$city];
                        $this->data[$setID]["end_bonus"] = floatval($this->data[$setID]["month_new_amt"])*floatval($this->data[$setID]["stop_rate_coe"])*floatval($list["royalty_rate"])*0.01;
                        $this->data[$setID]["end_bonus"] = ComparisonForm::showNum($this->data[$setID]["end_bonus"]);
                        //$this->downJsonText["excel"][$setID]["end_bonus"]=$this->data[$setID]["end_bonus"];
                        $list["month_royalty"]+=$this->data[$setID]["end_bonus"];
                    }
                }
                break;
            case 3://一线城市总经理
                break;
            case 4://非一线城市总经理
                break;
            case 5://地区主管
                $list["person_royalty"]=$this->getSalesPersonMoney($list);//个人提成
                break;
            case 6://副总监（一线）
                break;
        }
        //目标达成奖奖金
        $list["goal_all_bonus"] = 0;
        //$list["goal_all_bonus"] = empty($list["goal_ach_bonus"])?0:$list["goal_ach_bonus"];
        $list["goal_all_bonus"]+= empty($list["person_royalty"])?0:$list["person_royalty"];
        $list["goal_all_bonus"]+= empty($list["month_royalty"])?0:$list["month_royalty"];
    }

    private function getStopRateCoeForStopRate($comStopRate){
        $stop_rate = floatval($comStopRate);
        $startDate = date("Y-m-01",strtotime("{$this->search_year}-{$this->search_month}-01"));
        $hrRow = Yii::app()->db->createCommand()->select("id")
            ->from("swo_manage_stop_hdr")
            ->where("start_date<='{$startDate}'")
            ->order("start_date desc")
            ->queryRow();
        $coefficient=0;
        if($hrRow){
            $rows = Yii::app()->db->createCommand()->select("operator,stop_rate,coefficient")
                ->from("swo_manage_stop_hdl")
                ->where("hdr_id=:id",array(":id"=>$hrRow["id"]))
                ->order("stop_rate asc")
                ->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if($row["operator"]=="LT"){
                        if($stop_rate<=$row["stop_rate"]){
                            $coefficient = floatval($row["coefficient"]);
                            break;
                        }
                    }else{
                        if($stop_rate>$row["stop_rate"]){
                            $coefficient = floatval($row["coefficient"]);
                        }
                    }
                }
            }
        }
        return $coefficient;
    }

    private function getSalesPersonMoney($list){
        $suffix = Yii::app()->params['envSuffix'];
        $countSql = $this->getCountMoneySql();
        $row = Yii::app()->db->createCommand()->select("a.id{$countSql}")
            ->from("account$suffix.acc_service_comm_hdr a")
            ->leftJoin("account$suffix.acc_service_comm_dtl f","f.hdr_id=a.id")
            ->where("a.year_no={$this->search_year} and a.month_no={$this->search_month} and a.employee_code='{$list["employee_code"]}'")
            ->queryRow();
        if($row){
            return isset($row["moneys"])?$row["moneys"]:0;
        }else{
            return 0;
        }
    }

    private function getCountMoneySql(){
        $sql = "";
        $addList = array("IFNULL(f.supplement_money,0)");
        $arrList = array(
            'service_reward'=>array('value'=>'service_reward','name'=>''),
            'point'=>array('value'=>'point','name'=>''),
            'new_calc'=>array('value'=>'new_calc','name'=>''),
            'new_amount'=>array('value'=>'new_amount','name'=>'','amount'=>true),
            'edit_amount'=>array('value'=>'edit_amount','name'=>'','amount'=>true),
            'install_amount'=>array('value'=>'install_amount','name'=>'','amount'=>true),
            'end_amount'=>array('value'=>'end_amount','name'=>'','amount'=>true),
            'performance_amount'=>array('value'=>'performance_amount','name'=>'','amount'=>true),
            'new_money'=>array('value'=>'new_money','name'=>''),
            'edit_money'=>array('value'=>'edit_money','name'=>''),
            'install_money'=>array('value'=>'install_money','name'=>''),
            'out_money'=>array('value'=>'out_money','name'=>''),
            'performanceedit_amount'=>array('value'=>'performanceedit_amount','name'=>'','amount'=>true),
            'performanceedit_money'=>array('value'=>'performanceedit_money','name'=>''),
            'performanceend_amount'=>array('value'=>'performanceend_amount','name'=>'','amount'=>true),
            'renewal_amount'=>array('value'=>'renewal_amount','name'=>'','amount'=>true),
            'renewal_money'=>array('value'=>'renewal_money','name'=>''),
            'renewalend_amount'=>array('value'=>'renewalend_amount','name'=>'','amount'=>true),
            'product_amount'=>array('value'=>'product_amount','name'=>'','amount'=>true),
            'supplement_money'=>array('value'=>'supplement_money','name'=>''),
        );
        foreach ($arrList as $item){
            if(key_exists("amount",$item)&&$item["amount"]){
                $addList[]="IFNULL(f.{$item['value']},0)";
            }
        }
        if(!empty($addList)){
            $sql= ",(".implode("+",$addList).") as moneys";
        }
        return $sql;
    }

    public static function comparisonNetRate($num,$numLast){
        if(empty($numLast)){
            if($num>0){
                return Yii::t("summary","Yes");
            }else{
                return Yii::t("summary","No");
            }
        }else{
            if($num>=$numLast){
                return Yii::t("summary","Yes");
            }else{
                return Yii::t("summary","No");
            }
        }
    }

    //顯示提成表的表格內容
    public function manageMonthBonusHtml(){
        $html= '<table id="manageMonthBonus" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $topList=array(
            array("name"=>Yii::t('summary',"Year month"),"background"=>"#00B0F0","color"=>"#ffffff"),//年月
            array("name"=>Yii::t('summary',"Employee Name"),"background"=>"#00B0F0","color"=>"#ffffff"),//员工
            array("name"=>Yii::t('summary',"bonus city"),"background"=>"#00B0F0","color"=>"#ffffff"),//奖金城市
            array("name"=>Yii::t('summary',"bonus position"),"background"=>"#00B0F0","color"=>"#ffffff"),//奖金职位
            array("name"=>Yii::t('summary',"monthly amount"),"background"=>"#00B0F0","color"=>"#ffffff"),//当月新生意额
            array("name"=>Yii::t('summary',"Composite Stop Rate"),"background"=>"#00B0F0","color"=>"#ffffff"),//综合停单率
            array("name"=>Yii::t('summary',"monthly net amount"),"background"=>"#00B0F0","color"=>"#ffffff"),//当月净增长额
            //array("name"=>Yii::t('summary',"monthly net target"),"background"=>"#00B0F0","color"=>"#ffffff"),//当月净增长目标达成
            array("name"=>Yii::t('summary',"new sign target"),"background"=>"#808080","color"=>"#ffffff"),//提成发放条件：新签金额达标
            array("name"=>Yii::t('summary',"royalty rate"),"background"=>"#808080","color"=>"#ffffff"),//提成率
            array("name"=>Yii::t('summary',"stop rate coefficient"),"background"=>"#808080","color"=>"#ffffff"),//停单率调节系数
            array("name"=>Yii::t('summary',"monthly royalty"),"background"=>"#808080","color"=>"#ffffff"),//月新生意额提成
            array("name"=>Yii::t('summary',"person royalty"),"background"=>"#808080","color"=>"#ffffff"),//个人提成(仅地区主管)
            //array("name"=>Yii::t('summary',"goal achievement bonus"),"background"=>"#808080","color"=>"#ffffff"),//目标达成奖奖金
            array("name"=>Yii::t('summary',"goal all bonus"),"background"=>"#808080","color"=>"#ffffff"),//合计月度提成奖金
            array("name"=>Yii::t('summary',"end bonus"),"background"=>"#44546A","color"=>"#ffffff"),//对应副总监/高级总经理管理提成
        );
        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $this->th_sum=0;
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $trThree="";
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
                    $threeCol=key_exists("colspan",$col)?$col['colspan']:array();
                    if(!empty($threeCol)){
                        foreach ($threeCol as $three){
                            $this->th_sum++;
                            $trThree.="<th style='{$style}'><span>".$three["name"]."</span></th>";

                        }
                    }else{
                        $this->th_sum++;
                    }
                    $threeColNum=count($threeCol);
                    $colNum+=$threeColNum;
                    $threeColNum = empty($threeColNum)?1:$threeColNum;
                    //$this->th_sum++;

                    if(key_exists("rowspan",$col)){
                        $trTwo.="<th colspan='{$threeColNum}' rowspan='{$col["rowspan"]}' style='{$style}'><span>".$col["name"]."</span></th>";
                    }else{
                        $trTwo.="<th colspan='{$threeColNum}' style='{$style}'><span>".$col["name"]."</span></th>";
                    }
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
        $html.="<tr>{$trOne}</tr>";//<tr>{$trTwo}</tr><tr>{$trThree}</tr>
        if(!empty($trTwo)){
            $html.="<tr>{$trTwo}</tr>";
        }
        if(!empty($trThree)){
            $html.="<tr>{$trThree}</tr>";
        }
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            if($i==0){
                $width=70;
            }elseif ($i==2){
                $width=130;
            }elseif ($i==3){
                $width=110;
            }else{
                $width=90;
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
            "year_month","employee_name","city_name","dept_name","month_new_amt","comStopRate",
            "month_net_amt","sign_tar","royalty_rate","stop_rate_coe","month_royalty",
            "person_royalty","goal_all_bonus","end_bonus"
        );
        return $bodyKey;
    }

    public static function showNum($num){
        $pre="";
        if (strpos($num," +")!==false){
            $pre=" +";
            $num = end(explode(" +",$num));
        }
        if (is_numeric($num)){
            $number = floatval($num);
            //$number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $pre.$number;
    }

    //將城市数据寫入表格
    protected function showServiceHtml(&$data){
        $bodyKey = $this->getDataAllKeyStr();
        $clickTdList = $this->getClickTdList();
        $html="";
        if(!empty($data)){
            foreach ($data as &$cityList){
                //$this->resetTdRow($cityList);
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $text = $text===""?"":ComparisonForm::showNum($text);
                    $this->downJsonText["excel"][$cityList['id']][$keyStr]=$text;
                    $class = "";
                    $title="";
                    if(key_exists($keyStr,$clickTdList)){
                        $class.=" td_detail";
                        $title=$clickTdList[$keyStr]["title"];
                    }
                    $html.="<td class='{$class}' data-title='{$title}' data-id='{$cityList["id"]}' data-type='{$keyStr}' data-city='{$cityList['city']}'>";
                    $html.="<span>{$text}</span></td>";
                    $html.="</td>";
                }
                $html.="</tr>";
            }
        }
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
            $excelData = empty($excelData)?array():$excelData;
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("app","Management Month Bonus"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $this->tableTopHtml();//检查表头总共有多少个th
        $excel->th_num = $this->th_sum;
        $excel->setUServiceHeader($headList);
        $excel->setUServiceData($excelData);
        $excel->outExcel(Yii::t("app","Bonus Month"));
    }

    //获取年份
    public static function getYearList(){
        $year = date("Y");
        $list = array();
        for ($i=$year-4;$i<=$year+1;$i++){
            if($i>2022){
                $list[$i] = $i.Yii::t('summary'," Year");
            }
        }
        return $list;
    }
    //获取月份
    public static function getMonthList(){
        $list = array();
        for ($i=1;$i<=12;$i++){
            $list[$i] = $i.Yii::t('summary'," month");
        }
        return $list;
    }

    //需要顯示表格詳情的欄位
    protected function getClickTdList(){
        return array();
    }
}