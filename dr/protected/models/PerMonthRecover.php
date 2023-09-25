<?php

class PerMonthRecover extends PerMonth
{
    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $endDate = $this->end_date;
        $lastYearStart = $this->last_year."/01/01";
        $lastYearEnd = $this->last_year."/12/31";
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        //净恢复金额 = 合同同比分析里的 “ 恢复服务 ”  + “ 暂停服务 ” + “ 更改服务 ”

        //恢复服务(本年)
        $serviceR = CountSearch::getServiceForTypeToMonth($endDate,$city_allow,"R");
        //暂停服务(本年)
        $serviceS = CountSearch::getServiceForSTToMonth($endDate,$city_allow,"S");
        //更改服务(本年)
        $serviceA = CountSearch::getServiceAToMonth($endDate,$city_allow);

        //恢复服务(上一年)
        $lastServiceR = CountSearch::getServiceForType($lastYearStart,$lastYearEnd,$city_allow,"R");
        //暂停服务(上一年)
        $lastServiceS = CountSearch::getServiceForST($lastYearStart,$lastYearEnd,$city_allow,"S");
        //更改服务(上一年)
        $lastServiceA = CountSearch::getServiceForA($lastYearStart,$lastYearEnd,$city_allow);
/* 2023-09-20说不需要预估了
        //恢复服务(上週)
        $serviceRWeek = CountSearch::getServiceForType($lastWeekStartDate,$lastWeekEndDate,$city_allow,"R");
        //暂停服务(上週)
        $serviceSWeek = CountSearch::getServiceForST($lastWeekStartDate,$lastWeekEndDate,$city_allow,"S");
        //更改服务(上週)
        $serviceAWeek = CountSearch::getServiceForA($lastWeekStartDate,$lastWeekEndDate,$city_allow);
*/
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $this->addListForCity($defMoreList,$city,$serviceR);
            $this->addListForCity($defMoreList,$city,$serviceS);
            $this->addListForCity($defMoreList,$city,$serviceA);

            $defMoreList["last_average"]+=key_exists($city,$lastServiceR)?$lastServiceR[$city]:0;
            $defMoreList["last_average"]+=key_exists($city,$lastServiceS)?-1*$lastServiceS[$city]["num_pause"]:0;
            $defMoreList["last_average"]+=key_exists($city,$lastServiceA)?$lastServiceA[$city]:0;
            /* 2023-09-20说不需要预估了
            $defMoreList["last_week"]+=key_exists($city,$serviceRWeek)?$serviceRWeek[$city]:0;
            $defMoreList["last_week"]+=key_exists($city,$serviceSWeek)?-1*$serviceSWeek[$city]["num_pause"]:0;
            $defMoreList["last_week"]+=key_exists($city,$serviceAWeek)?$serviceAWeek[$city]:0;
            */
            RptSummarySC::resetData($data,$cityRow,$citySetList,$defMoreList);
        }
        $this->data = $data;
        return true;
    }

    //設置該城市的默認值
    protected function defMoreCity($city,$city_name){
        $arr=array(
            "city"=>$city,
            "city_name"=>$city_name,
            "u_sum"=>0,//U系统金额
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $dateStrOne = $this->search_year."/{$month}";//产品金额
            $arr[$dateStrOne]=0;
        }
        $arr["last_average"]=0;//上一年平均
        $arr["now_week"]=0;//本周
        $arr["last_week"]=0;//上周
        $arr["growth"]="";//加速增长
        $arr["start_one_gross"]=0;//年初目标(upside)
        $arr["start_one_result"]="";//达成目标(upside)
        $arr["start_two_gross"]=0;//年初目标(base)
        $arr["start_two_result"]="";//达成目标(base)
        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        if(!$bool){
            /*2023-09-20说不需要预估了
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=parent::perMonthNumber($list["last_week"]);
            */
        }
        if(!$bool){
            $list["last_average"]=round($list["last_average"]/12,2);
        }else{
            $list["last_average"]=empty($count)?0:round($list["last_average"]/$count,2);
        }
        $lastNum = 0;
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $nowStr = $this->search_year."/{$month}";
            $list[$nowStr] = key_exists($nowStr,$list)?$list[$nowStr]:0;
            $list[$nowStr] = parent::perMonthNumber($list[$nowStr]);
            $lastNum = $list[$nowStr];
        }
        $list["last_average"] = parent::perMonthNumber($list["last_average"]);
        /*2023-09-20说不需要预估了
        $list["now_week"]=($lastNum/$this->week_day)*$this->month_day;
        $list["now_week"] = parent::perMonthNumber($list["now_week"]);
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"]);
        $list["start_two_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_gross"]);
        $list["start_one_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_one_gross"]);
        */
    }

    protected function getTopArr(){
        $monthArr = array();
        for($i=1;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>$this->last_year,"background"=>"#f7fd9d",
                "colspan"=>array(
                    array(
                        "name"=>Yii::t("summary","Average")//平均
                    )
                )
            ),//上一年(平均)
            array("name"=>$this->search_year,"background"=>"#fcd5b4",
                "colspan"=>$monthArr
            ),//本年
        );
/*
        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week")),//本周
                array("name"=>Yii::t("summary","last week")),//上周
                array("name"=>Yii::t("summary","growth")),//加速增长
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","Target contrast"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Target").Yii::t("summary","(base)")),//年初目标(base)
                array("name"=>Yii::t("summary","Start Target result").Yii::t("summary","(base)")),//达成目标(base)
                array("name"=>Yii::t("summary","Start Target").Yii::t("summary","(upside)")),//年初目标(upside)
                array("name"=>Yii::t("summary","Start Target result").Yii::t("summary","(upside)")),//达成目标(upside)
            )
        );//目标对比
*/
        return $topList;
    }

    //获取td对应的键名
    protected function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name",
            "last_average"
        );
        for($i=1;$i<=$this->search_month;$i++){
            $month = $i>=10?10:"0{$i}";
            $bodyKey[]=$this->search_year."/{$month}";
        }
/*
        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_one_gross";//(Upside)
        $bodyKey[]="start_one_result";//(Upside)
        $bodyKey[]="start_two_gross";//(Base)
        $bodyKey[]="start_two_result";//(Base)
*/
        return $bodyKey;
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
        $excel->SetHeaderTitle(Yii::t("summary","Per Month Recover")."（{$this->search_date}）");
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
        $excel->outExcel(Yii::t("summary","Per Month Recover"));
    }
}