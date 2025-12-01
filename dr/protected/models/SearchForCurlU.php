<?php

/**
 * curl查询U系统
 * User: Administrator
 * Date: 2024/1/29 0029
 * Time: 10:54
 */
class SearchForCurlU
{

    //周人效
    public static function getUCheckWeekStaff($startDay,$endDay,$city_allow=""){
        $list = SystemU::getUCheckWeekStaff($startDay,$endDay,$city_allow,false);
        return isset($list["data"])?$list["data"]:array();
    }

    //签到签离月统计
    public static function getUCheckInMonth($startDay,$endDay,$city_allow=""){
        $list = SystemU::getUCheckInMonth($startDay,$endDay,$city_allow,false);
        return isset($list["data"])?$list["data"]:array();
    }

    //签到签离周统计
    public static function getUCheckInWeek($startDay,$endDay,$city_allow=""){
        $list = SystemU::getUCheckInWeek($startDay,$endDay,$city_allow,false);
        return isset($list["data"])?$list["data"]:array();
    }

    //签到签离员工统计
    public static function getUCheckInStaff($startDay,$endDay,$staffs=""){
        $list = SystemU::getUCheckInStaff($startDay,$endDay,$staffs,false);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的服务单数据(发包方、承接方、资质方)
    public static function getUServiceMoneyV3($startDay,$endDay,$city_allow="",$type=0){
        $list = SystemU::getUServiceMoneyV3($startDay,$endDay,$city_allow,false,$type);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的服务单数据(城市分类)
    public static function getCurlServiceForCity($startDay,$endDay,$city_allow="",$type=0){
        $list = SystemU::getUServiceMoney($startDay,$endDay,$city_allow,false,$type);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的服务单数据(销售)
    public static function getCurlServiceForSales($startDay,$endDay,$salesCode="",$type=0){
        $list = SystemU::getUServiceMoneyBySales($startDay,$endDay,$salesCode,false,$type);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的服务单数据(城市的月份分类)
    public static function getCurlServiceForMonth($startDay,$endDay,$city_allow="",$type=0){
        $list = SystemU::getUServiceMoneyToMonth($startDay,$endDay,$city_allow,false,$type);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的服务单数据(城市的周一分类)
    public static function getCurlServiceForWeek($startDay,$endDay,$city_allow="",$type=1){
        $list = SystemU::getUServiceMoneyToWeek($startDay,$endDay,$city_allow,false,$type);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的技术员金额（技术员已分离）
    public static function getCurlTechnicianMoney($startDay,$endDay,$city_allow=""){
        $list = SystemU::getTechnicianMoney($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取派单系统的做单提成
    public static function getCurlSalaryMoney($startDay,$endDay,$city_allow=""){
        $list = SystemU::getSalaryMoney($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(账单详情)
    public static function getCurlInvDetail($startDay,$endDay,$city_allow=""){
        $list = SystemU::getInvDataDetail($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(城市分类)
    public static function getCurlInvForCity($startDay,$endDay,$city_allow=""){
        $list = SystemU::getInvDataCityAmount($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(账单详情)(销售)
    public static function getCurlInvDetailForSales($startDay,$endDay,$salesCode=""){
        $list = SystemU::getInvDataSalesDetail($startDay,$endDay,$salesCode);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(销售)
    public static function getCurlInvForCitySales($startDay,$endDay,$salesCode=""){
        $list = SystemU::getInvDataSalesAmount($startDay,$endDay,$salesCode);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(城市的月份分类)
    public static function getCurlInvForMonth($startDay,$endDay,$city_allow=""){
        $list = SystemU::getInvDataCityMonth($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的INV数据(城市的周一分类)
    public static function getCurlInvForWeek($startDay,$endDay,$city_allow=""){
        $list = SystemU::getInvDataCityWeek($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }

    //获取U系统的技术员金额列表（需要分开多个技术员）
    public static function getCurlTechnicianDetail($startDay,$endDay,$city_allow=""){
        $list = SystemU::getTechnicianDetail($startDay,$endDay,$city_allow);
        return isset($list["data"])?$list["data"]:array();
    }
}