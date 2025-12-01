<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class KATrackTable extends ComparisonForm {
    public $city_allow;
    public $search=1;//1:城市查询，2:员工查询
    public $sales_id=0;//

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $city = key_exists("city",$_GET)?$_GET["city"]:0;
        $this->search = key_exists("search",$_GET)?$_GET["search"]:1;
        if($this->search ==1){//城市查询
            $cityList = CitySetForm::getCityAllowForCity($city);
            if(CountSearch::getSystem()==2){
                if(in_array("MY",$cityList)){
                    $cityList = array_merge($cityList,array("SL","KL","JB"));
                }
            }
            $this->city_allow = "'".implode("','",$cityList)."'";
        }else{
            $this->sales_id = is_numeric($city)?$city:0;
        }
        $this->start_date = key_exists("startDate",$_GET)?$_GET["startDate"]:"";
        $this->end_date = key_exists("endDate",$_GET)?$_GET["endDate"]:"";
        $clickList = parent::clickList();
        $clickList = array_column($clickList,"type");
        $type = key_exists("type",$_GET)?$_GET["type"]:"";
        if(in_array($type,$clickList)){
            return $this->$type();
        }else{
            return "<p>数据异常，请刷新重试</p>";
        }
    }

    //一次性+U系统产品（本年）
    private function ServiceINVNew(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
            $invRows = SummaryTable::getUInvList($this->start_date,$this->end_date,$this->city_allow);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
            $salesCode= self::getEmployeeCodeByID($this->sales_id);

            $invRows = SearchForCurlU::getCurlInvDetailForSales($this->start_date,$this->end_date,$salesCode);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }
        $rows = SummaryTable::getOneServiceRows($this->start_date,$this->end_date,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow,$invTable);
    }

    //一次性+U系统产品（上一年）
    private function ServiceINVNewLast(){
        $city_allow = $this->city_allow;
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        if($this->search==1){//城市查询
            $sqlExpr = "";
            $invRows = SummaryTable::getUInvList($lastStartDate,$lastEndDate,$this->city_allow);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
            $salesCode= self::getEmployeeCodeByID($this->sales_id);

            $invRows = SearchForCurlU::getCurlInvDetailForSales($this->start_date,$this->end_date,$salesCode);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }
        $rows = SummaryTable::getOneServiceRows($lastStartDate,$lastEndDate,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow,$invTable);
    }

    //一次性+U系统产品(上月)（本年）
    private function ServiceINVMonthNew(){
        $city_allow = $this->city_allow;
        parent::computeDate();
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        if($this->search==1){//城市查询
            $sqlExpr = "";
            $invRows = SummaryTable::getUInvList($monthStartDate,$monthEndDate,$this->city_allow);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
            $salesCode= self::getEmployeeCodeByID($this->sales_id);

            $invRows = SearchForCurlU::getCurlInvDetailForSales($this->start_date,$this->end_date,$salesCode);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }
        $rows = SummaryTable::getOneServiceRows($monthStartDate,$monthEndDate,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow,$invTable);
    }

    //一次性+U系统产品(上月)（上一年）
    private function ServiceINVMonthNewLast(){
        $city_allow = $this->city_allow;
        parent::computeDate();
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $lastMonthStartDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthStartDate));
        $lastMonthEndDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthEndDate));
        if($this->search==1){//城市查询
            $sqlExpr = "";
            $invRows = SummaryTable::getUInvList($lastMonthStartDate,$lastMonthEndDate,$this->city_allow);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
            $salesCode= self::getEmployeeCodeByID($this->sales_id);

            $invRows = SearchForCurlU::getCurlInvDetailForSales($this->start_date,$this->end_date,$salesCode);
            $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        }
        $rows = SummaryTable::getOneServiceRows($lastMonthStartDate,$lastMonthEndDate,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow,$invTable);
    }

    //YTD新增（本年）
    private function ServiceNew(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        $rows = SummaryTable::getServiceRowsForAdd($this->start_date,$this->end_date,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //YTD新增（上一年）
    private function ServiceNewLast(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRowsForAdd($lastStartDate,$lastEndDate,$city_allow,$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //更改服务（本年）
    private function ServiceAmend(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        $rows = SummaryTable::getServiceRows($this->start_date,$this->end_date,$city_allow,"A",$sqlExpr);
        return SummaryTable::getTableForRowsTwo($rows,$city_allow);
    }

    //更改服务（上一年）
    private function ServiceAmendLast(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRows($lastStartDate,$lastEndDate,$city_allow,"A",$sqlExpr);
        return SummaryTable::getTableForRowsTwo($rows,$city_allow);
    }

    //暂停服务（本年）
    private function ServicePause(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        $rows = SummaryTable::getServiceSTForType($this->start_date,$this->end_date,$city_allow,"S",$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //暂停服务（上一年）
    private function ServicePauseLast(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceSTForType($lastStartDate,$lastEndDate,$city_allow,"S",$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //恢复服务（本年）
    private function ServiceResume(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        $rows = SummaryTable::getServiceRows($this->start_date,$this->end_date,$city_allow,"R",$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //恢复服务（上一年）
    private function ServiceResumeLast(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRows($lastStartDate,$lastEndDate,$city_allow,"R",$sqlExpr);
        return SummaryTable::getTableForRows($rows,$city_allow);
    }

    //终止服务（本年）
    private function ServiceStop(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        $rows = SummaryTable::getServiceSTListForType($this->start_date,$this->end_date,$city_allow,"T",$sqlExpr);
        $listGood = SummaryTable::getTableListForRowsEx($rows["goodList"],$this->city_allow,"终止");
        $listNot = SummaryTable::getTableListForRowsEx($rows["notList"],$this->city_allow,"终止");
        return SummaryTable::getTableForTab($listGood,$listNot);
    }

    //终止服务（上一年）
    private function ServiceStopLast(){
        $city_allow = $this->city_allow;
        if($this->search==1){//城市查询
            $sqlExpr = "";
        }else{
            $city_allow="";
            $sqlExpr= " and a.salesman_id='{$this->sales_id}' ";
        }
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceSTListForType($lastStartDate,$lastEndDate,$city_allow,"T",$sqlExpr);
        $listGood = SummaryTable::getTableListForRowsEx($rows["goodList"],$city_allow,"终止");
        $listNot = SummaryTable::getTableListForRowsEx($rows["notList"],$city_allow,"终止");
        return SummaryTable::getTableForTab($listGood,$listNot);
    }

    public static function getEmployeeCodeByID($employee_id){
        $suffix = Yii::app()->params['envSuffix'];//区分正式版、测试版
        $row = Yii::app()->db->createCommand()
            ->select("code")->from("hr{$suffix}.hr_employee")
            ->where("id=:id",array(":id"=>$employee_id))->queryRow();
        return $row?$row["code"]:$employee_id;
    }
}