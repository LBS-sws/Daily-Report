<?php

class SalesAnalysisAreaForm extends SalesAnalysisForm
{

    public function retrieveData() {
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        $city_allow = Yii::app()->user->city_allow();
        $staffRows = $this->getSalesForHr($city_allow,$this->search_date);//员工信息
        $nowData = $this->getNowYearData($twoYear,$this->end_date,$city_allow);//两年度的数据
        $cityList = self::getCityListAndRegion($city_allow);//城市信息
        $this->data = $this->groupAreaForStaffAndData($staffRows,$cityList,$nowData);

        $session = Yii::app()->session;
        $session['salesAnalysis_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            $twoYear = $this->search_year;
            $twoYear = ($twoYear-1)."/01/01";
            $sum=0;
            $count=0;
            for($i=0;$i<=24;$i++){
                $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
                if($dateTimer<=strtotime($this->end_date)){
                    $yearMonth = date("Y/m",$dateTimer);
                    $sum+=$list[$yearMonth];
                    $count++;
                }else{
                    break;
                }
            }
            $list["now_average"]=empty($count)?0:round($sum/$count);
        }
    }

    public function getTopArr(){
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        $area_name=key_exists("region_name",$this->data)?$this->data["region_name"]:"{city}";
        $monthArr = array();

        for($i=0;$i<=24;$i++){
            $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
            if($dateTimer<=strtotime($this->end_date)){
                $monthArr[]=array("name"=>date("Y年m月",$dateTimer));
            }else{
                break;
            }
        }
        $monthArr[]=array("name"=>Yii::t("summary","Average"));
        $topList=array(
            array("name"=>$area_name,"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//区域
            array("name"=>Yii::t("summary","FTE"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//销售人数
            array("name"=>Yii::t("summary","Productivity"),"background"=>"#00B0F0","color"=>"#FFFFFF",
                "colspan"=>$monthArr
            ),//生产力
        );
        return $topList;
    }

    //顯示提成表的表格內容
    public function salesAnalysisHtml(){
        $html= '<table id="salesAnalysisArea" class="table table-fixed table-condensed table-bordered table-hover">';
        if(!empty($this->data)){
            $data = $this->data;
            $this->downJsonText=array();
            foreach ($data as $city_data){
                $this->data = $city_data;
                $html.=$this->tableTopHtml();
                $html.=$this->tableBodyHtml();
                //$html.=$this->tableFooterHtml();
            }
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
                $width=170;
            }else{
                $width=80;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data["list"]);
            $html.="</tbody>";
        }
        return $html;
    }

    //获取td对应的键名
    protected function getDataAllKeyStr(){
        $twoYear = $this->search_year;
        $twoYear = ($twoYear-1)."/01/01";
        $bodyKey = array(
            "name",
            "fte_num"
        );

        for($i=0;$i<=24;$i++){
            $dateTimer = empty($i)?strtotime($twoYear):strtotime($twoYear." + {$i} months");
            if($dateTimer<=strtotime($this->end_date)){
                $bodyKey[]=date("Y/m",$dateTimer);
            }else{
                break;
            }
        }
        $bodyKey[]="now_average";
        return $bodyKey;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            //ksort($data,1);//根据键名从小到大排序
            foreach ($data as $monthStr=>$cityList){
                $this->resetTdRow($cityList);
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $this->downJsonText[$cityList['region']][$monthStr][$keyStr]=$text;
                    $html.="<td><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
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