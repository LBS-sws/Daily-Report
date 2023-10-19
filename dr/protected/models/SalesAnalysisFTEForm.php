<?php

class SalesAnalysisFTEForm extends SalesAnalysisForm
{

    public function retrieveData() {
        $city_allow = Yii::app()->user->city_allow();
        $staffRows = $this->getSalesForHr($city_allow,$this->search_date);//员工信息
        $nowData = $this->getNowYearData($this->start_date,$this->end_date,$city_allow);//本年度的数据
        $cityList = self::getCityListAndRegion($city_allow);//城市信息
        $this->data = $this->groupAreaForStaffAndData($staffRows,$cityList,$nowData);

        $session = Yii::app()->session;
        $session['salesAnalysis_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($this->search_year==2023){
            $startMonth = 3;
        }else{
            $startMonth = 1;
        }
        for($i=$startMonth;$i<=$this->search_month;$i++){
            $nowMonth = $i;
            $nowMonth = $nowMonth>=10?$nowMonth:"0{$nowMonth}";
            $nowMonth = $this->search_year."/{$nowMonth}";
            $keyStr = $nowMonth;
            $nowMonth = key_exists($nowMonth,$list)?$list[$nowMonth]:0;
            $lastMonth =$i-1;
            $lastMonth = $lastMonth>=10?$lastMonth:"0{$lastMonth}";
            $lastMonth = $this->search_year."/{$lastMonth}";
            $lastMonth = key_exists($lastMonth,$list)?$list[$lastMonth]:0;
            $list["rate_".$keyStr]=self::computeRate($lastMonth,$nowMonth);
        }
        //$data["list"][$month][$number]
    }

    public static function computeRate($lastStr,$nowStr,$num=0){
        if(!empty($lastStr)){
            $rate = round($nowStr/$lastStr,3);
            $rate = ($rate-$num)*100;
            $rate.="%";
        }else{
            $rate = "";
        }
        return $rate;
    }

    public function getTopArr(){
        if($this->search_year==2023){
            $startMonth = 3;
        }else{
            $startMonth = 1;
        }
        $monthArr = array();
        for($i=$startMonth;$i<=$this->search_month;$i++){
            $monthArr[]=array("name"=>$i.Yii::t("summary","Month"));
        }
        $topList=array(
            array("name"=>Yii::t("summary","all city"),"rowspan"=>2),//区域
            array("name"=>Yii::t("summary","sales num"),"background"=>"#00B0F0","color"=>"#FFFFFF",
                "colspan"=>$monthArr
            ),//销售人数
            array("name"=>Yii::t("summary","MoM retention rate"),"background"=>"#C5D9F1","color"=>"#FFFFFF",
                "colspan"=>$monthArr
            ),//MoM retention rate
        );
        return $topList;
    }

    //顯示提成表的表格內容
    public function salesAnalysisHtml(){
        $html= '<table id="salesAnalysisArea" class="table table-fixed table-condensed table-bordered table-hover">';
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.=$this->tableTopHtml();
            $html.=$this->tableBodyHtml();
            $this->downJsonText=json_encode($this->downJsonText);
        }
        $html.="</table>";
        return $html;
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
    protected function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            if($i==0){
                $width=150;
            }else{
                $width=70;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $data = $this->data;
            $data = key_exists("list",$data)?$data["list"]:array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($data);
            $html.="</tbody>";
        }
        return $html;
    }

    //获取td对应的键名
    protected function getDataAllKeyStr(){
        if($this->search_year==2023){
            $startMonth = 3;
        }else{
            $startMonth = 1;
        }
        $bodyKey = array(
            "title"
        );
        $arrRate=array();
        for($i=$startMonth;$i<=$this->search_month;$i++){
            $month = $i>=10?$i:"0{$i}";
            $keyStr = $this->search_year."/{$month}";
            $bodyKey[]=$keyStr;
            $arrRate[]="rate_".$keyStr;
        }
        $bodyKey=array_merge($bodyKey,$arrRate);
        return $bodyKey;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $all = array("title"=>Yii::t("summary","all total"));
            foreach ($data as $monthStr=>$cityList){
                $this->resetTdRow($cityList);
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    if(!key_exists($keyStr,$all)){
                        $all[$keyStr]=0;
                    }
                    if(is_numeric($text)){
                        $all[$keyStr]+=$text;
                    }
                    $this->downJsonText["all"]["list"][$monthStr][$keyStr]=$text;
                    $html.="<td><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
            $this->resetTdRow($all);
            $html.="<tr style='font-weight: bold;'>";
            //合计
            foreach ($bodyKey as $keyStr){
                $text = key_exists($keyStr,$all)?$all[$keyStr]:"0";
                $this->downJsonText["all"]["count"][$keyStr]=$text;
                $html.="<td><span>{$text}</span></td>";
            }
            $html.="</tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
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
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=1;
        $excel->SetHeaderTitle(Yii::t("summary","Capacity Staff")."（{$this->search_date}）");
        $titleTwo = $this->start_date." ~ ".$this->end_date;
        //"\r\n"
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel("SalesAnalysis");
    }
}