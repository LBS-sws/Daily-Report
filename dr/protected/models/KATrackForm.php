<?php

class KATrackForm extends CFormModel
{
    /* User Fields */
    public $search_start_date;//查詢開始日期
    public $search_end_date;//查詢結束日期
    public $search_type=3;//查詢類型 1：季度 2：月份 3：天
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $search_month_end;//查詢月份(结束)
    public $search_quarter;//查詢季度
    public $start_date;
    public $end_date;
    public $month_type;
    public $day_num=0;
    public $comparison_year;
    public $month_start_date;
    public $month_end_date;
    public $last_month_start_date;
    public $last_month_end_date;

    public $data=array();

    public $th_sum=2;//所有th的个数

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
            array('search_type,search_start_date,search_end_date,search_year,search_quarter,search_month,search_month_end','safe'),
            array('search_type','required'),
            array('search_type','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        switch ($this->search_type){
            case 1://1：季度
                if(empty($this->search_year)||empty($this->search_quarter)){
                    $this->addError($attribute, "查询季度不能为空");
                }else{
                    $dateStr = $this->search_year."/".$this->search_quarter."/01";
                    $this->start_date = date("Y/m/01",strtotime($dateStr));
                    $this->end_date = date("Y/m/t",strtotime($dateStr." + 2 month"));
                    $this->month_type = $this->search_quarter;
                }
                break;
            case 2://2：月份
                if(empty($this->search_year)||empty($this->search_month)){
                    $this->addError($attribute, "查询月份不能为空");
                }else{
                    $dateTimer = strtotime($this->search_year."/".$this->search_month."/01");
                    $this->start_date = date("Y/m/01",$dateTimer);
                    $dateTimer = strtotime($this->search_year."/".$this->search_month_end."/01");
                    $this->end_date = date("Y/m/t",$dateTimer);
                    $i = ceil($this->search_month/3);//向上取整
                    $this->month_type = 3*$i-2;
                }
                break;
            case 3://3：天
                if(empty($this->search_start_date)||empty($this->search_start_date)){
                    $this->addError($attribute, "查询日期不能为空");
                }else{
                    $startYear = date("Y",strtotime($this->search_start_date));
                    $endYear = date("Y",strtotime($this->search_end_date));
                    if($startYear!=$endYear){
                        $this->addError($attribute, "请把开始年份跟结束年份保持一致");
                    }else{
                        $this->search_month = date("n",strtotime($this->search_start_date));
                        $i = ceil($this->search_month/3);//向上取整
                        $this->month_type = 3*$i-2;
                        $this->search_year = $startYear;
                        $this->start_date = $this->search_start_date;
                        $this->end_date = $this->search_end_date;
                    }
                }
                break;
        }
        if($this->end_date<$this->start_date){
            $this->addError($attribute, "查询时间异常");
        }
        $boolDate = CountSearch::$stop_new_dt."/01";
        $boolDate = date("Y/m/01",strtotime($boolDate." + 1 month"));
        if($this->start_date<$boolDate&&$this->end_date>=$boolDate){
            $this->addError($attribute, Yii::t("summary","index_note_3"));
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
            'search_month'=>$this->search_month,
            'search_month_end'=>$this->search_month_end,
            'search_type'=>$this->search_type,
            'search_quarter'=>$this->search_quarter,
            'search_start_date'=>$this->search_start_date,
            'search_end_date'=>$this->search_end_date
        );
    }

    public static function setDayNum($startDate,$endDate,&$dayNum){
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $timer = 0;
        if($endDate>=$startDate){
            $timer = ($endDate-$startDate)/86400;
            $timer++;//需要算上起始的一天
        }
        $dayNum = $timer;
    }

    public static function resetNetOrGross($num,$day,$type=3){
        switch ($type){
            case 1://季度
                return $num+$num*2*0.8;
            case 2://月度
                return $num;
            case 3://日期
                $num = ($num*12/365)*$day;
                $num = round($num,2);
                return $num;
        }
        return $type;
    }

    protected function computeDate(){
        $this->start_date = empty($this->start_date)?date("Y/01/01"):$this->start_date;
        $this->end_date = empty($this->end_date)?date("Y/m/t"):$this->end_date;
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));

        $this->last_month_start_date = CountSearch::computeLastMonth($this->start_date);
        $this->last_month_end_date = CountSearch::computeLastMonth($this->end_date);
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->u_load_data['u_load_start'] = 0;
        $this->u_load_data['u_load_end'] = 0;
        $this->computeDate();
        ComparisonForm::setDayNum($this->start_date,$this->end_date,$this->day_num);
        $this->data = array();
        $this->retrieveDataForSales();
        if(Yii::app()->user->validFunction('CN33')){//拥有区域查询权限
            $this->retrieveDataForCity();
        }
        $session = Yii::app()->session;
        $session['kATrack_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    //获取KA所有员工
    public static function getKaManForKaBot($searchDate,$employee_id){
        $maxDate = date("Y-m-d",strtotime($searchDate));
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = "sal";
        $city_allow = Yii::app()->user->city_allow();
        $whereSql = "f.a_read_write like '%KA01%'";
        if(RetentionKARateForm::validAccessForSS('CN15',$systemId)){
            $whereSql.= " and (h.staff_status!=-1 or (h.staff_status=-1 and DATE_FORMAT(h.leave_time,'%Y-%m-%d')>='{$maxDate}'))";
        }elseif(RetentionKARateForm::validAccessForSS('CN19',$systemId)){
            $idSQL = RetentionKARateForm::getGroupIDStrForEmployeeID($employee_id);
            $whereSql.= " and (h.id in ({$idSQL}) or h.id in ({$idSQL}) or h.city in ({$city_allow}))";
        }else{
            $idSQL = RetentionKARateForm::getGroupIDStrForEmployeeID($employee_id);
            $whereSql.= " and h.id in ({$idSQL})";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("h.id,h.code,h.name,h.city,h.entry_time,h.table_type")
            ->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee h","a.employee_id=h.id")
            ->leftJoin("security{$suffix}.sec_user_access f","f.username=a.user_id and f.system_id='{$systemId}'")
            ->where("h.id=3875 or ({$whereSql})")//固定增加赵志宏
            ->group("h.id,h.code,h.name,h.city,h.entry_time,h.table_type")
            ->order("h.table_type asc,h.city,h.id")
            ->queryAll();
        $rows = $rows?$rows:array();
        if(!empty($rows)){
            //排序开始
            $kaGroupList = self::getKASalesGroup();
            $arr= array("group"=>array(),"staff"=>array());//排序
            foreach ($rows as $row){
                $ka_id = $row["id"];
                if(key_exists($ka_id,$kaGroupList)){
                    $keyStr = "group";
                    $temp["group_name"] = $kaGroupList[$ka_id]["group_name"];
                }else{
                    $keyStr = "staff";
                }
                $arr[$keyStr][]=$row;
            }
            $rows = array_merge($arr["group"],$arr["staff"]);
        }

        return $rows;
    }

    public static function getKASalesGroup(){
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
        $id_arr = array(-1);
        $code_arr = array("-1");
        if(!empty($list)){
            foreach ($list as $row){
                $id_arr[]=empty($row["id"])?0:$row["id"];
                $code_arr[]=$row["code"];
            }
        }
        return array("id_str"=>implode(",",$id_arr),"code_arr"=>$code_arr,"code_str"=>implode(",",$code_arr));
    }

    protected function retrieveDataForSales() {
        $data = array();
        $city_allow = '';
        $employee_id = RetentionKARateForm::getEmployeeIDForUsername();
        $kaManList = self::getKaManForKaBot($this->end_date,$employee_id);
        $salesman_str_list = $this->getSalesmanStr($kaManList);
        $sales_id_str = $salesman_str_list["id_str"];
        $sales_code_str = $salesman_str_list["code_str"];
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $lastMonthStartDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthStartDate));
        $lastMonthEndDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthEndDate));
        $allMonthStartDate = date("Y/m/01",strtotime($this->start_date));
        $allMonthStartDate = date("Y/m/01",strtotime($allMonthStartDate." - 1 months"));
        $allMonthEndDate = date("Y/m/01",strtotime($this->end_date));
        $allMonthEndDate = date("Y/m/t",strtotime($allMonthEndDate." - 1 months"));
        $uServiceType = $this->search_type==3?1:0;//当日期查询时，根据日期查询
        $u_load_start = time();
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getCurlServiceForSales($startDate,$endDate,$sales_code_str,$uServiceType);
        //获取U系统的服务单数据(上月)
        $uServiceMoneyLast = CountSearch::getCurlServiceForSales($monthStartDate,$monthEndDate,$sales_code_str,$uServiceType);
        //获取U系统的服务单数据(上月)(整月)
        $uServiceMoneyAllLast = CountSearch::getCurlServiceForSales($allMonthStartDate,$allMonthEndDate,$sales_code_str);
        //获取U系统的產品数据(上月)(整月)
        $monthUInvAllMoney = CountSearch::getCurlInvForCitySales($allMonthStartDate,$allMonthEndDate,$sales_code_str);
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getCurlInvForCitySales($startDate,$endDate,$sales_code_str);
        //获取U系统的產品数据(上一年)
        $lastUInvMoney = CountSearch::getCurlInvForCitySales($lastStartDate,$lastEndDate,$sales_code_str);
        //获取U系统的產品数据(上月)
        $monthUInvMoney = CountSearch::getCurlInvForCitySales($monthStartDate,$monthEndDate,$sales_code_str);
        //获取U系统的產品数据(上月)(上一年)
        $lastMonthUInvMoney = CountSearch::getCurlInvForCitySales($lastMonthStartDate,$lastMonthEndDate,$sales_code_str);
        $u_load_end = time();
        $this->u_load_data['u_load_end']+= $u_load_end-$u_load_start;
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForSalesNY($startDate,$endDate,$sales_id_str);
        //服务新增（非一次性 和 一次性)(上一年)
        $lastServiceAddForNY = CountSearch::getServiceAddForSalesNY($lastStartDate,$lastEndDate,$sales_id_str);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForSalesST($startDate,$endDate,$sales_id_str);
        //终止服务、暂停服务(上一年)
        $lastServiceForST = CountSearch::getServiceForSalesST($lastStartDate,$lastEndDate,$sales_id_str);
        //恢復服务
        $serviceForR = CountSearch::getServiceForSalesType($startDate,$endDate,$sales_id_str,"R");
        //恢復服务(上一年)
        $lastServiceForR = CountSearch::getServiceForSalesType($lastStartDate,$lastEndDate,$sales_id_str,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForSalesA($startDate,$endDate,$sales_id_str);
        //更改服务(上一年)
        $lastServiceForA = CountSearch::getServiceForSalesA($lastStartDate,$lastEndDate,$sales_id_str);
        //服务新增（一次性)(上月)
        $monthServiceAddForY = CountSearch::getServiceAddForSalesY($monthStartDate,$monthEndDate,$sales_id_str);
        //服务新增（一次性)(上月)(上一年)
        $lastMonthServiceAddForY = CountSearch::getServiceAddForSalesY($lastMonthStartDate,$lastMonthEndDate,$sales_id_str);

        if(!empty($kaManList)){
            $region_name = "KA销售";
            if(!key_exists($region_name,$data)){
                $data[$region_name]=array(
                    "list"=>array(),
                    "region"=>$region_name,
                    "region_code"=>$region_name,
                    "region_name"=>$region_name,
                );
            }
            foreach ($kaManList as $cityRow){
                $city = $cityRow["id"];
                $staffCode = $cityRow["code"];
                $cityRow["name"] = $cityRow["name"]." ({$cityRow["code"]})";
                $defMoreList=$this->defMoreCity($city,$cityRow["name"]);
                $defMoreList["search"]=2;//员工查询
                $defMoreList["u_actual_money"]+=key_exists($staffCode,$uServiceMoney)?$uServiceMoney[$staffCode]:0;
                $defMoreList["u_sum"]+=key_exists($staffCode,$uInvMoney)?$uInvMoney[$staffCode]["sum_money"]:0;
                $defMoreList["u_actual_money"]+=$defMoreList["u_sum"];//生意额需要加上U系统产品金额
                $defMoreList["u_sum_last"]+=key_exists($staffCode,$lastUInvMoney)?$lastUInvMoney[$staffCode]["sum_money"]:0;
                if(key_exists($city,$serviceAddForNY)){
                    $defMoreList["new_sum"]+=$serviceAddForNY[$city]["num_new"];
                    $defMoreList["new_sum_n"]+=$serviceAddForNY[$city]["num_new_n"];
                }
                $defMoreList["new_sum_n"]+=$defMoreList["u_sum"];//一次性新增需要加上U系统产品金额
                if(key_exists($city,$lastServiceAddForNY)){
                    $defMoreList["new_sum_last"]+=$lastServiceAddForNY[$city]["num_new"];
                    $defMoreList["new_sum_n_last"]+=$lastServiceAddForNY[$city]["num_new_n"];
                }
                $defMoreList["new_sum_n_last"]+=$defMoreList["u_sum_last"];//一次性新增需要加上U系统产品金额
                //上月一次性服务+新增（产品）
                $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthServiceAddForY)?-1*$lastMonthServiceAddForY[$city]:0;
                $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthUInvMoney)?-1*$lastMonthUInvMoney[$city]["sum_money"]:0;
                $defMoreList["new_month_n"]+=key_exists($city,$monthServiceAddForY)?-1*$monthServiceAddForY[$city]:0;
                $defMoreList["new_month_n"]+=key_exists($city,$monthUInvMoney)?-1*$monthUInvMoney[$city]["sum_money"]:0;
                //上月生意额
                $defMoreList["last_u_actual"]+=key_exists($city,$uServiceMoneyLast)?$uServiceMoneyLast[$city]:0;
                $defMoreList["last_u_actual"]+=key_exists($city,$monthUInvMoney)?$monthUInvMoney[$city]["sum_money"]:0;
                //上月生意额(整月)
                $defMoreList["last_u_all"]+=key_exists($city,$uServiceMoneyAllLast)?$uServiceMoneyAllLast[$city]:0;
                $defMoreList["last_u_all"]+=key_exists($city,$monthUInvAllMoney)?$monthUInvAllMoney[$city]["sum_money"]:0;
                //暂停、停止
                if(key_exists($city,$serviceForST)){
                    $defMoreList["stop_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop"]:0;
                    $defMoreList["pause_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_pause"]:0;
                    $defMoreList["stop_sum_none"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop_none"]:0;
                    $defMoreList["stop_2024_11"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_stop_none"]:0;
                    $defMoreList["stop_2024_11"] = -1*$defMoreList["stop_sum"]-$defMoreList["stop_2024_11"];
                    $defMoreList["stopSumOnly"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_month"]:0;
                }
                if(key_exists($city,$lastServiceForST)){
                    $defMoreList["stop_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_stop"]:0;
                    $defMoreList["stop_sum_none_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_stop_none"]:0;
                    $defMoreList["stop_2024_11_last"]+=key_exists($city,$lastServiceForST)?$lastServiceForST[$city]["num_stop_none"]:0;
                    $defMoreList["stop_2024_11_last"] = -1*$defMoreList["stop_sum_last"]-$defMoreList["stop_2024_11_last"];
                    $defMoreList["pause_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_pause"]:0;
                }
                //恢复
                $defMoreList["resume_sum_last"]+=key_exists($city,$lastServiceForR)?$lastServiceForR[$city]:0;
                $defMoreList["resume_sum"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;
                //更改
                $defMoreList["amend_sum_last"]+=key_exists($city,$lastServiceForA)?$lastServiceForA[$city]:0;
                $defMoreList["amend_sum"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;
                $data[$region_name]["list"][]=$defMoreList;
            }
        }

        $this->data = array_merge($this->data,$data);
        return true;
    }

    protected function retrieveDataForCity() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $citySetList = CityTrackForm::getCityTrackList($city_allow);
        $city_code_list = empty($citySetList)?array(-1):array_keys($citySetList);
        $city_allow = "'".implode("','",$city_code_list)."'";
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $lastMonthStartDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthStartDate));
        $lastMonthEndDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthEndDate));
        $allMonthStartDate = date("Y/m/01",strtotime($this->start_date));
        $allMonthStartDate = date("Y/m/01",strtotime($allMonthStartDate." - 1 months"));
        $allMonthEndDate = date("Y/m/01",strtotime($this->end_date));
        $allMonthEndDate = date("Y/m/t",strtotime($allMonthEndDate." - 1 months"));
        $uServiceType = $this->search_type==3?1:0;//当日期查询时，根据日期查询
        $u_load_start = time();
        //获取U系统的服务单数据
        $uServiceMoney = CountSearch::getUServiceMoney($startDate,$endDate,$city_allow,$uServiceType);
        //获取U系统的服务单数据(上月)
        $uServiceMoneyLast = CountSearch::getUServiceMoney($monthStartDate,$monthEndDate,$city_allow,$uServiceType);
        //获取U系统的服务单数据(上月)(整月)
        $uServiceMoneyAllLast = CountSearch::getUServiceMoney($allMonthStartDate,$allMonthEndDate,$city_allow);
        //获取U系统的產品数据(上月)(整月)
        $monthUInvAllMoney = CountSearch::getUInvMoney($allMonthStartDate,$allMonthEndDate,$city_allow);
        //获取U系统的產品数据
        $uInvMoney = CountSearch::getUInvMoney($startDate,$endDate,$city_allow);
        //获取U系统的產品数据(上一年)
        $lastUInvMoney = CountSearch::getUInvMoney($lastStartDate,$lastEndDate,$city_allow);
        //获取U系统的產品数据(上月)
        $monthUInvMoney = CountSearch::getUInvMoney($monthStartDate,$monthEndDate,$city_allow);
        //获取U系统的產品数据(上月)(上一年)
        $lastMonthUInvMoney = CountSearch::getUInvMoney($lastMonthStartDate,$lastMonthEndDate,$city_allow);
        $u_load_end = time();
        $this->u_load_data['u_load_end']+= $u_load_end-$u_load_start;
        //服务新增（非一次性 和 一次性)
        $serviceAddForNY = CountSearch::getServiceAddForNY($startDate,$endDate,$city_allow);
        //服务新增（非一次性 和 一次性)(上一年)
        $lastServiceAddForNY = CountSearch::getServiceAddForNY($lastStartDate,$lastEndDate,$city_allow);
        //终止服务、暂停服务
        $serviceForST = CountSearch::getServiceForST($startDate,$endDate,$city_allow);
        //终止服务、暂停服务(上一年)
        $lastServiceForST = CountSearch::getServiceForST($lastStartDate,$lastEndDate,$city_allow);
        //恢復服务
        $serviceForR = CountSearch::getServiceForType($startDate,$endDate,$city_allow,"R");
        //恢復服务(上一年)
        $lastServiceForR = CountSearch::getServiceForType($lastStartDate,$lastEndDate,$city_allow,"R");
        //更改服务
        $serviceForA = CountSearch::getServiceForA($startDate,$endDate,$city_allow);
        //更改服务(上一年)
        $lastServiceForA = CountSearch::getServiceForA($lastStartDate,$lastEndDate,$city_allow);
        //服务新增（一次性)(上月)
        $monthServiceAddForY = CountSearch::getServiceAddForY($monthStartDate,$monthEndDate,$city_allow);
        //服务新增（一次性)(上月)(上一年)
        $lastMonthServiceAddForY = CountSearch::getServiceAddForY($lastMonthStartDate,$lastMonthEndDate,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $region_name = $cityRow["end_name"];
            if(!key_exists($region_name,$data)){
                $data[$region_name]=array(
                    "list"=>array(),
                    "region"=>$region_name,
                    "region_code"=>$region_name,
                    "region_name"=>$region_name,
                );
            }
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["search"]=1;//城市查询
            $defMoreList["u_actual_money"]+=key_exists($city,$uServiceMoney)?$uServiceMoney[$city]:0;
            $defMoreList["u_sum"]+=key_exists($city,$uInvMoney)?$uInvMoney[$city]["sum_money"]:0;
            $defMoreList["u_actual_money"]+=$defMoreList["u_sum"];//生意额需要加上U系统产品金额
            $defMoreList["u_sum_last"]+=key_exists($city,$lastUInvMoney)?$lastUInvMoney[$city]["sum_money"]:0;
            if(key_exists($city,$serviceAddForNY)){
                $defMoreList["new_sum"]+=$serviceAddForNY[$city]["num_new"];
                $defMoreList["new_sum_n"]+=$serviceAddForNY[$city]["num_new_n"];
            }
            $defMoreList["new_sum_n"]+=$defMoreList["u_sum"];//一次性新增需要加上U系统产品金额
            if(key_exists($city,$lastServiceAddForNY)){
                $defMoreList["new_sum_last"]+=$lastServiceAddForNY[$city]["num_new"];
                $defMoreList["new_sum_n_last"]+=$lastServiceAddForNY[$city]["num_new_n"];
            }
            $defMoreList["new_sum_n_last"]+=$defMoreList["u_sum_last"];//一次性新增需要加上U系统产品金额
            //上月一次性服务+新增（产品）
            $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthServiceAddForY)?-1*$lastMonthServiceAddForY[$city]:0;
            $defMoreList["new_month_n_last"]+=key_exists($city,$lastMonthUInvMoney)?-1*$lastMonthUInvMoney[$city]["sum_money"]:0;
            $defMoreList["new_month_n"]+=key_exists($city,$monthServiceAddForY)?-1*$monthServiceAddForY[$city]:0;
            $defMoreList["new_month_n"]+=key_exists($city,$monthUInvMoney)?-1*$monthUInvMoney[$city]["sum_money"]:0;
            //上月生意额
            $defMoreList["last_u_actual"]+=key_exists($city,$uServiceMoneyLast)?$uServiceMoneyLast[$city]:0;
            $defMoreList["last_u_actual"]+=key_exists($city,$monthUInvMoney)?$monthUInvMoney[$city]["sum_money"]:0;
            //上月生意额(整月)
            $defMoreList["last_u_all"]+=key_exists($city,$uServiceMoneyAllLast)?$uServiceMoneyAllLast[$city]:0;
            $defMoreList["last_u_all"]+=key_exists($city,$monthUInvAllMoney)?$monthUInvAllMoney[$city]["sum_money"]:0;
            //暂停、停止
            if(key_exists($city,$serviceForST)){
                $defMoreList["stop_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop"]:0;
                $defMoreList["pause_sum"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_pause"]:0;
                $defMoreList["stop_sum_none"]+=key_exists($city,$serviceForST)?-1*$serviceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11"] = -1*$defMoreList["stop_sum"]-$defMoreList["stop_2024_11"];
                $defMoreList["stopSumOnly"]+=key_exists($city,$serviceForST)?$serviceForST[$city]["num_month"]:0;
            }
            if(key_exists($city,$lastServiceForST)){
                $defMoreList["stop_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_stop"]:0;
                $defMoreList["stop_sum_none_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11_last"]+=key_exists($city,$lastServiceForST)?$lastServiceForST[$city]["num_stop_none"]:0;
                $defMoreList["stop_2024_11_last"] = -1*$defMoreList["stop_sum_last"]-$defMoreList["stop_2024_11_last"];
                $defMoreList["pause_sum_last"]+=key_exists($city,$lastServiceForST)?-1*$lastServiceForST[$city]["num_pause"]:0;
            }
            //恢复
            $defMoreList["resume_sum_last"]+=key_exists($city,$lastServiceForR)?$lastServiceForR[$city]:0;
            $defMoreList["resume_sum"]+=key_exists($city,$serviceForR)?$serviceForR[$city]:0;
            //更改
            $defMoreList["amend_sum_last"]+=key_exists($city,$lastServiceForA)?$lastServiceForA[$city]:0;
            $defMoreList["amend_sum"]+=key_exists($city,$serviceForA)?$serviceForA[$city]:0;

            $data[$region_name]["list"][]=$defMoreList;
        }

        $this->data = array_merge($this->data,$data);
        return true;
    }


    //設置該城市的默認值
    private function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "last_u_actual"=>0,//服务生意额(上月)
            "last_u_all"=>0,//服务生意额(上月)(整月)
            "u_actual_money"=>0,//服务生意额
            "u_sum_last"=>0,//U系统金额(上一年)
            "u_sum"=>0,//U系统金额
            "stopSumOnly"=>0,//本月停單金額（月）
            "monthStopRate"=>0,//月停單率
            "comStopRate"=>0,//综合停單率
            "new_sum_last"=>0,//新增(上一年)
            "new_sum"=>0,//新增
            "new_rate"=>0,//新增对比比例

            "new_sum_n_last"=>0,//一次性服务+新增（产品） (上一年)
            "new_sum_n"=>0,//一次性服务+新增（产品）
            "new_n_rate"=>0,//一次性服务+新增（产品）对比比例

            "new_month_n_last"=>0,//上月一次性服务+新增（产品） (上一年)
            "new_month_n"=>0,//上月一次性服务+新增（产品）
            "new_month_rate"=>0,//上月一次性服务+新增（产品）对比比例

            "stop_sum_last"=>0,//终止（上一年）
            "stop_sum"=>0,//终止
            "stop_sum_none"=>0,//终止(本条终止的前一条、后一条没有暂停、终止)
            "stop_2024_11"=>0,//终止(2024年12月份改版)
            "stop_2024_11_last"=>0,//终止（上一年）(2024年12月份改版)
            "stop_sum_none_last"=>0,//终止（上一年）(本条终止的前一条、后一条没有暂停、终止)
            "stop_rate"=>0,//终止对比比例

            "resume_sum_last"=>0,//恢复（上一年）
            "resume_sum"=>0,//恢复
            "resume_rate"=>0,//恢复对比比例

            "pause_sum_last"=>0,//暂停（上一年）
            "pause_sum"=>0,//暂停
            "pause_rate"=>0,//暂停对比比例

            "amend_sum_last"=>0,//更改（上一年）
            "amend_sum"=>0,//更改
            "amend_rate"=>0,//更改对比比例

            "lbs_new_amt_last"=>0,//利比斯（上一年）
            "lbs_new_amt"=>0,//利比斯
            "lbs_new_amt_rate"=>0,//利比斯对比比例

            "net_sum_last"=>0,//总和（上一年）
            "net_sum"=>0,//总和
            "net_rate"=>0,//总和对比比例
        );
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false){
        $newSum = $list["new_sum"]+$list["new_sum_n"];//所有新增总金额
        //$list["monthStopRate"] = $this->comparisonRate($list["stopSumOnly"],$list["u_actual_money"]);
        //2023年9月改版：月停单率 = (new_sum_n+new_month_n+stop_sum/12)/last_u_actual
        if($bool){
            $list["monthStopRate"] = "-";
        }else{
            $list["monthStopRate"] = $list["new_sum_n"]+$list["new_month_n"]+round($list["stop_sum"]/12,2);
            $list["monthStopRate"] = ComparisonForm::comparisonRate($list["monthStopRate"],$list["last_u_actual"]);
        }
        $list["comStopRate"] = $list["stop_sum_none"]+$list["resume_sum"]+$list["pause_sum"]+$list["amend_sum"];
        $list["comStopRate"]/= 12;//stop_sum_none,last_u_all
        $lastSum = $list["new_month_n"]+$list["last_u_all"];
        $list["comStopRate"] = ComparisonForm::comparisonRate($list["comStopRate"],$lastSum);
        $list["net_sum"]=0;
        $list["net_sum"]+=$list["new_sum"]+$list["new_sum_n"]+$list["new_month_n"];
        //$list["net_sum"]+=$list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"];
        $list["net_sum"]+=$list["stop_sum"]+$list["resume_sum"]+$list["pause_sum"];
        $list["net_sum"]+=$list["amend_sum"];
        if(date_format(date_create($this->end_date),'Y/m')>CountSearch::$stop_new_dt){
            $list["not_net_sum"]=$list["net_sum"]+$list["stop_2024_11"];
        }else{
            $list["not_net_sum"]=0;
        }
        $list["net_sum_last"]=0;
        $list["net_sum_last"]+=$list["new_sum_last"]+$list["new_sum_n_last"]+$list["new_month_n_last"];
        $list["net_sum_last"]+=$list["stop_sum_last"]+$list["resume_sum_last"]+$list["pause_sum_last"];
        $list["net_sum_last"]+=$list["amend_sum_last"];
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        if($lastEndDate>CountSearch::$stop_new_dt){
            $list["not_net_sum_last"]=$list["net_sum_last"]+$list["stop_2024_11_last"];
        }else{
            $list["not_net_sum_last"]=0;
        }
        $list["new_rate"] = ComparisonForm::nowAndLastRate($list["new_sum"],$list["new_sum_last"],true);
        $list["new_n_rate"] = ComparisonForm::nowAndLastRate($list["new_sum_n"],$list["new_sum_n_last"],true);
        $list["new_month_rate"] = ComparisonForm::nowAndLastRate($list["new_month_n"],$list["new_month_n_last"],true);
        $list["stop_rate"] = ComparisonForm::nowAndLastRate($list["stop_sum"],$list["stop_sum_last"],true);
        $list["resume_rate"] = ComparisonForm::nowAndLastRate($list["resume_sum"],$list["resume_sum_last"],true);
        $list["pause_rate"] = ComparisonForm::nowAndLastRate($list["pause_sum"],$list["pause_sum_last"],true);
        $list["amend_rate"] = ComparisonForm::nowAndLastRate($list["amend_sum"],$list["amend_sum_last"],true);

        $list["net_rate"] = ComparisonForm::nowAndLastRate($list["net_sum"],$list["net_sum_last"],true);
        $list["not_net_rate"] = ComparisonForm::nowAndLastRate($list["not_net_sum"],$list["not_net_sum_last"],true);

    }

    //顯示提成表的表格內容
    public function comparisonHtml(){
        $html= '<table id="comparison" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    protected function getTopArr(){
        $monthStr = "（{$this->month_start_date} ~ {$this->month_end_date}）";
        $lastMonthStr = "（".date("m/d",strtotime($this->last_month_start_date))." ~ ".date("m/d",strtotime($this->last_month_end_date))."）";
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>Yii::t("summary","Actual monthly amount"),"rowspan"=>2),//服务生意额
            array("name"=>Yii::t("summary","YTD New").$monthStr,"background"=>"#f7fd9d",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD新增
            array("name"=>Yii::t("summary","New(single) + New(INV)").$monthStr,"background"=>"#F7FD9D",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//一次性服务+新增（产品）
            array("name"=>Yii::t("summary","Last Month Single + New(INV)").$lastMonthStr,"background"=>"#F7FD9D",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//上月一次性服务+新增产品
            array("name"=>Yii::t("summary","YTD Stop").$monthStr,"exprName"=>$monthStr,"background"=>"#fcd5b4",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                    array("name"=>Yii::t("summary","Month Stop Rate")),//月停单率
                    array("name"=>Yii::t("summary","Composite Stop Rate")),//月停单率
                )
            ),//YTD终止
            array("name"=>Yii::t("summary","YTD Resume").$monthStr,"exprName"=>$monthStr,"background"=>"#C5D9F1",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD恢复
            array("name"=>Yii::t("summary","YTD Pause").$monthStr,"exprName"=>$monthStr,"background"=>"#D9D9D9",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD暂停
            array("name"=>Yii::t("summary","YTD Amend").$monthStr,"exprName"=>$monthStr,"background"=>"#EBF1DE",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD更改
            array("name"=>Yii::t("summary","YTD Net").$monthStr,"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD Net
            array("name"=>Yii::t("summary","YTD Not Net").$monthStr,"background"=>"#f2dcdb",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD Net
        );
        $topList[]=array("name"=>Yii::t("summary","stop sum none"),"background"=>"#fcd5b4",
            "colspan"=>array(
                array("name"=>$this->comparison_year),//查询年份
            )
        );//计算停单率的终止金额

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
        $html.="<tr>{$trOne}</tr><tr>{$trTwo}</tr>";
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
            "city_name","u_actual_money","new_sum_last","new_sum","new_rate",
            "new_sum_n_last","new_sum_n","new_n_rate",
            "new_month_n_last","new_month_n","new_month_rate",
            "stop_sum_last","stop_sum","stop_rate","monthStopRate","comStopRate",
            "resume_sum_last","resume_sum","resume_rate",
            "pause_sum_last","pause_sum","pause_rate",
            "amend_sum_last","amend_sum","amend_rate",
            "net_sum_last","net_sum","net_rate",
            "not_net_sum_last","not_net_sum","not_net_rate",
        );
        $bodyKey[]="stop_sum_none";
        return $bodyKey;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            //last_u_all
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = ["stopSumOnly"=>0,"last_u_all"=>0,"stop_sum_none_last"=>0,"stop_2024_11"=>0,"stop_2024_11_last"=>0];//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $regionRow["stopSumOnly"]+=$cityList["stopSumOnly"];
                        $regionRow["last_u_all"]+=$cityList["last_u_all"];
                        $regionRow["stop_sum_none_last"]+=$cityList["stop_sum_none_last"];
                        $this->resetTdRow($cityList);
                        //last_u_all
                        $html.="<tr data-stopSumNone='{$cityList['stop_sum_none']}' data-lastUAll='{$cityList['last_u_all']}'>";
                        foreach ($bodyKey as $keyStr){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
                            ComparisonForm::setTextColorForKeyStr($tdClass,$keyStr,$cityList);
                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"],$cityList['search']);//点击后弹窗详细内容
                            $text = ComparisonForm::showNum($text);
                            //$inputHide = TbHtml::hiddenField("excel[{$regionList['region']}][list][{$cityList['city']}][{$keyStr}]",$text);
                            $this->downJsonText["excel"][$regionList['region']]['list'][$cityList['city']][$keyStr]=$text;

                            if($keyStr=="new_sum"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' {$exprData} data-u='{$cityList['u_sum']}'><span>{$text}</span></td>";
                            }elseif($keyStr=="new_sum_last"){//调试U系统同步数据
                                $html.="<td class='{$tdClass}' {$exprData} data-u='{$cityList['u_sum_last']}'><span>{$text}</span></td>";
                            }else{
                                $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
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
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $titleName = Yii::t("app","KA Tracking");
        $excel->SetHeaderTitle($titleName);
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel($titleName);
    }

    protected function clickList(){
        return array(
            "new_month_n_last"=>array("title"=>Yii::t("summary","Last Month Single + New(INV)").Yii::t("summary"," (last year)"),"type"=>"ServiceINVMonthNewLast"),
            "new_month_n"=>array("title"=>Yii::t("summary","Last Month Single + New(INV)"),"type"=>"ServiceINVMonthNew"),
            "new_sum_n_last"=>array("title"=>Yii::t("summary","New(single) + New(INV)").Yii::t("summary"," (last year)"),"type"=>"ServiceINVNewLast"),
            "new_sum_n"=>array("title"=>Yii::t("summary","New(single) + New(INV)"),"type"=>"ServiceINVNew"),
            "new_sum_last"=>array("title"=>Yii::t("summary","New(not single)").Yii::t("summary"," (last year)"),"type"=>"ServiceNewLast"),
            "new_sum"=>array("title"=>Yii::t("summary","New(not single)"),"type"=>"ServiceNew"),
            "stop_sum_last"=>array("title"=>Yii::t("summary","YTD Stop").Yii::t("summary"," (last year)"),"type"=>"ServiceStopLast"),
            "stop_sum"=>array("title"=>Yii::t("summary","YTD Stop"),"type"=>"ServiceStop"),
            "resume_sum_last"=>array("title"=>Yii::t("summary","YTD Resume").Yii::t("summary"," (last year)"),"type"=>"ServiceResumeLast"),
            "resume_sum"=>array("title"=>Yii::t("summary","YTD Resume"),"type"=>"ServiceResume"),
            "pause_sum_last"=>array("title"=>Yii::t("summary","YTD Pause").Yii::t("summary"," (last year)"),"type"=>"ServicePauseLast"),
            "pause_sum"=>array("title"=>Yii::t("summary","YTD Pause"),"type"=>"ServicePause"),
            "amend_sum_last"=>array("title"=>Yii::t("summary","YTD Amend").Yii::t("summary"," (last year)"),"type"=>"ServiceAmendLast"),
            "amend_sum"=>array("title"=>Yii::t("summary","YTD Amend"),"type"=>"ServiceAmend"),
        );
    }

    private function tdClick(&$tdClass,$keyStr,$city,$search_type=1){
        $expr = " data-city='{$city}' data-search='{$search_type}'";
        $list = $this->clickList();
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }

}