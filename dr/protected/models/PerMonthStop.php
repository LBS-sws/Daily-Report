<?php

class PerMonthStop extends PerMonth
{
    public function retrieveData() {
        $data = array();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = SalesAnalysisForm::getCitySetForCityAllow($city_allow);
        $citySetList = CitySetForm::getCitySetList($city_allow);
        $endDate = $this->end_date;
        $yearMonth = date("Y/m",strtotime($endDate));
        $monthStartDate = date("Y/m/01",strtotime($this->end_date));//本月的第一天
        $lastMonthStart = CountSearch::computeLastMonth($monthStartDate);//查询时间的上月（减法使用）
        $lastMonthEnd = CountSearch::computeLastMonth($endDate);//查询时间的上月（减法使用）
        $lastYearStart = $this->last_year."/01/01";
        $lastYearEnd = $this->last_year."/12/31";
        $lastYearStartU = ($this->last_year-1)."/12/01";//上一年的前一个月
        $lastYearEndU = $this->last_year."/11/30";//上一年的前一个月
        $lastWeekStartDate = date("Y/m/d",$this->last_week_start);
        $lastWeekEndDate = date("Y/m/d",$this->last_week_end);
        $lastWeekStartDateU = date("Y/m/d",strtotime($lastWeekStartDate." - 1 month"));//上月的上周
        $lastWeekEndDateU = date("Y/m/d",strtotime($lastWeekEndDate." - 1 month"));//上月的上周
        //停止金额 = 合同同比分析里的 “ 终止服务 ” +  “ 上月一次性服务+新增产品 ”

        //终止服务(本年)
        $serviceT = CountSearch::getServiceForSTToMonth($endDate,$city_allow,"T");
        //一次性服务(本年)
        $monthServiceAddForY = CountSearch::getServiceAddForYToMonth($endDate,$city_allow);
        //产品(本年)
        $uInvMoney = CountSearch::getUInvMoneyToMonth($endDate,$city_allow);
        //新增产品（本年上月）
        $subServiceInv = CountSearch::getUInvMoney($lastMonthStart,$lastMonthEnd,$city_allow);
        //一次性服务（本年上月）
        $subServiceY = CountSearch::getServiceAddForY($lastMonthStart,$lastMonthEnd,$city_allow);

        //终止服务(上一年)
        $lastServiceT = CountSearch::getServiceForST($lastYearStart,$lastYearEnd,$city_allow,"T");
        //一次性服务(上一年)
        $lastMonthServiceAddForY = CountSearch::getServiceAddForY($lastYearStartU,$lastYearEndU,$city_allow);
        //产品(上一年)
        $lastUInvMoney = CountSearch::getUInvMoney($lastYearStartU,$lastYearEndU,$city_allow);

        //终止服务(上週)
        $lastServiceWeek = CountSearch::getServiceForST($lastWeekStartDate,$lastWeekEndDate,$city_allow,"T");
        //一次性服务(上月上週)
        $lastMonthServiceAddForYWeek = CountSearch::getServiceAddForY($lastWeekStartDateU,$lastWeekEndDateU,$city_allow);
        //产品(上月上週)
        $lastUInvMoneyWeek = CountSearch::getUInvMoney($lastWeekStartDateU,$lastWeekEndDateU,$city_allow);
        foreach ($citySetList as $cityRow){
            $city = $cityRow["code"];
            $defMoreList=$this->defMoreCity($city,$cityRow["city_name"]);
            $defMoreList["add_type"] = $cityRow["add_type"];
            ComparisonForm::setComparisonConfig($defMoreList,$this->search_year,$this->month_type,$city);

            $this->addListForCity($defMoreList,$city,$serviceT);
            $this->addListForCity($defMoreList,$city,$monthServiceAddForY,1,$yearMonth);
            $this->addListForCity($defMoreList,$city,$uInvMoney,1,$yearMonth);
            $defMoreList[$yearMonth]+=key_exists($city,$subServiceInv)?-1*$subServiceInv[$city]["sum_money"]:0;
            $defMoreList[$yearMonth]+=key_exists($city,$subServiceY)?-1*$subServiceY[$city]:0;

            $defMoreList["last_average"]+=key_exists($city,$lastServiceT)?-1*$lastServiceT[$city]["num_stop"]:0;
            $defMoreList["last_average"]+=key_exists($city,$lastUInvMoney)?-1*$lastUInvMoney[$city]["sum_money"]:0;
            $defMoreList["last_average"]+=key_exists($city,$lastMonthServiceAddForY)?-1*$lastMonthServiceAddForY[$city]:0;

            $defMoreList["last_week"]+=key_exists($city,$lastServiceWeek)?-1*$lastServiceWeek[$city]["num_stop"]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastUInvMoneyWeek)?-1*$lastUInvMoneyWeek[$city]["sum_money"]:0;
            $defMoreList["last_week"]+=key_exists($city,$lastMonthServiceAddForYWeek)?-1*$lastMonthServiceAddForYWeek[$city]["sum_money"]:0;

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
            $list["last_week"]=($list["last_week"]/$this->last_week_day)*$this->month_day;
            $list["last_week"]=parent::perMonthNumber($list["last_week"]);
        }
        if(!$bool){
            $list["start_one_gross"]*=-1;
            $list["start_two_gross"]*=-1;
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
        $list["now_week"]=($lastNum/$this->week_day)*$this->month_day;
        $list["now_week"] = parent::perMonthNumber($list["now_week"]);
        $list["last_average"] = parent::perMonthNumber($list["last_average"]);
        $list["growth"]=HistoryAddForm::comYes($list["now_week"],$list["last_week"],true);
        $list["start_two_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_two_gross"],true);
        $list["start_one_result"]=HistoryAddForm::comYes($list["now_week"],$list["start_one_gross"],true);

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

        $topList[]=array("name"=>$this->search_month.Yii::t("summary"," month estimate"),"background"=>"#f2dcdb",
            "colspan"=>array(
                array("name"=>Yii::t("summary","now week")),//本周
                array("name"=>Yii::t("summary","last week")),//上周
                array("name"=>Yii::t("summary","stop growth")),//加速增长
            )
        );//本月預估

        $topList[]=array("name"=>Yii::t("summary","Target contrast"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Start Target").Yii::t("summary","(base case)")),//年初目标(base)
                array("name"=>Yii::t("summary","Start Target result").Yii::t("summary","(base case)")),//达成目标(base)
                array("name"=>Yii::t("summary","Start Target").Yii::t("summary","(upside case)")),//年初目标(upside)
                array("name"=>Yii::t("summary","Start Target result").Yii::t("summary","(upside case)")),//达成目标(upside)
            )
        );//目标对比

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

        $bodyKey[]="now_week";
        $bodyKey[]="last_week";
        $bodyKey[]="growth";
        $bodyKey[]="start_one_gross";//(Upside)
        $bodyKey[]="start_one_result";//(Upside)
        $bodyKey[]="start_two_gross";//(Base)
        $bodyKey[]="start_two_result";//(Base)

        return $bodyKey;
    }

    //下載
    public function downExcel($excelData){
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("summary","Per Month Stop")."（{$this->search_date}）");
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
        $excel->outExcel(Yii::t("summary","Per Month Stop"));
    }
}