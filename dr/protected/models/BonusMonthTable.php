<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class BonusMonthTable extends BonusMonthForm {
    public $city_allow;

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $city = key_exists("city",$_GET)?$_GET["city"]:0;
        $cityList = CitySetForm::getCityAllowForCity($city);
        if(CountSearch::getSystem()==2){
            if(in_array("MY",$cityList)){
                $cityList = array_merge($cityList,array("SL","KL","JB"));
            }
        }
        $this->city_allow = "'".implode("','",$cityList)."'";
        $this->start_date = key_exists("startDate",$_GET)?$_GET["startDate"]:"";
        $this->end_date = key_exists("endDate",$_GET)?$_GET["endDate"]:"";
        $clickList = parent::getClickTdList();
        $type = key_exists("type",$_GET)?$_GET["type"]:"";
        if(key_exists($type,$clickList)){
            $fun = $clickList[$type]["fun"];
            return $this->$fun();
        }else{
            return "<p>数据异常，请刷新重试</p>";
        }
    }

    //新增服务
    private function ServiceNew(){
        $rows = SummaryTable::getServiceRowsForAdd($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //更改服务(只含新增)
    private function ServiceAmendmentAdd(){
        $rows = SummaryTable::getServiceRowsForAD($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRowsTwo($rows,$this->city_allow);
    }

    //一次性
    private function ServiceOne(){
        $rows = SummaryTable::getOneServiceRows($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //新增服务(非地区管理员)
    private function CityServiceNew(){
        $chargeSql = CountSearch::getCityChargeSql($this->city_allow);
        $rows = SummaryTable::getServiceRowsForAdd($this->start_date,$this->end_date,$this->city_allow,$chargeSql);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }

    //更改服务(只含新增)(非地区管理员)
    private function CityServiceAmendmentAdd(){
        $chargeSql = CountSearch::getCityChargeSql($this->city_allow);
        $rows = SummaryTable::getServiceRowsForAD($this->start_date,$this->end_date,$this->city_allow,$chargeSql);
        return SummaryTable::getTableForRowsTwo($rows,$this->city_allow);
    }

    //一次性(非地区管理员)
    private function CityServiceOne(){
        $chargeSql = CountSearch::getCityChargeSql($this->city_allow);
        $rows = SummaryTable::getOneServiceRows($this->start_date,$this->end_date,$this->city_allow,$chargeSql);
        return SummaryTable::getTableForRows($rows,$this->city_allow);
    }
}