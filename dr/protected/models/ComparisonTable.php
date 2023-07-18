<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class ComparisonTable extends ComparisonForm {
    public $city_allow;

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $city = key_exists("city",$_GET)?$_GET["city"]:0;
        $cityList = CitySetForm::getCityAllowForCity($city);
        $this->city_allow = "'".implode("','",$cityList)."'";
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
        $invRows = SummaryTable::getUInvList($this->start_date,$this->end_date,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //一次性+U系统产品（上一年）
    private function ServiceINVNewLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $invRows = SummaryTable::getUInvList($lastStartDate,$lastEndDate,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($lastStartDate,$lastEndDate,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //一次性+U系统产品(上月)（本年）
    private function ServiceINVMonthNew(){
        parent::computeDate();
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $invRows = SummaryTable::getUInvList($monthStartDate,$monthEndDate,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($monthStartDate,$monthEndDate,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //一次性+U系统产品(上月)（上一年）
    private function ServiceINVMonthNewLast(){
        parent::computeDate();
        $monthStartDate = $this->last_month_start_date;
        $monthEndDate = $this->last_month_end_date;
        $lastMonthStartDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthStartDate));
        $lastMonthEndDate = ($this->comparison_year-1)."/".date("m/d",strtotime($monthEndDate));
        $invRows = SummaryTable::getUInvList($lastMonthStartDate,$lastMonthEndDate,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($lastMonthStartDate,$lastMonthEndDate,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //YTD新增（本年）
    private function ServiceNew(){
        $rows = SummaryTable::getServiceRowsForAdd($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //YTD新增（上一年）
    private function ServiceNewLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRowsForAdd($lastStartDate,$lastEndDate,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //更改服务（本年）
    private function ServiceAmend(){
        $rows = SummaryTable::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"A");
        return SummaryTable::getTableForRowsTwo($rows,$this->city_allow);
    }

    //更改服务（上一年）
    private function ServiceAmendLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRows($lastStartDate,$lastEndDate,$this->city_allow,"A");
        return SummaryTable::getTableForRowsTwo($rows,$this->city_allow);
    }

    //暂停服务（本年）
    private function ServicePause(){
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$this->start_date,$this->end_date,"S");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //暂停服务（上一年）
    private function ServicePauseLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$lastStartDate,$lastEndDate,"S");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //恢复服务（本年）
    private function ServiceResume(){
        $rows = SummaryTable::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"R");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //恢复服务（上一年）
    private function ServiceResumeLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = SummaryTable::getServiceRows($lastStartDate,$lastEndDate,$this->city_allow,"R");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //终止服务（本年）
    private function ServiceStop(){
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$this->start_date,$this->end_date,"T");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //终止服务（上一年）
    private function ServiceStopLast(){
        parent::computeDate();
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$lastStartDate,$lastEndDate,"T");
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }
}