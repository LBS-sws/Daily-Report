<?php

class SalesAnalysisCityForm extends SalesAnalysisForm
{
    public function retrieveData() {
        $city_allow = Yii::app()->user->city_allow();
        $lifelineList = LifelineForm::getLifeLineList($city_allow,$this->search_date);//生命线
        $staffRows = $this->getSalesForHr($city_allow,$this->search_date);//员工信息
        $nowData = $this->getNowYearData($city_allow);//本年度的数据
        $cityList = self::getCityListAndRegion($city_allow);//城市信息
        $this->data = $this->groupCityForStaffAndData($staffRows,$cityList,$nowData,$lifelineList);

        $session = Yii::app()->session;
        $session['salesAnalysis_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list,$bool=false){
        if($bool){
            $sum=0;
            for ($i=1;$i<=$this->search_month;$i++) {
                $yearMonth = $this->search_year . "/" . ($i < 10 ? "0{$i}" : $i);
                $sum+=$list[$yearMonth];
            }
            $list["now_average"]=round($sum/$this->search_month);
        }
    }

    public function getTopArr(){
        $topList=array(
            //月份
            array("name"=>$this->search_month.Yii::t("summary"," month"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//区域
            //在职人数
            array("name"=>Yii::t("summary","number staff"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//销售人数
            //超出目标
            array("name"=>Yii::t("summary","exceed target"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//销售人数
            //低于目标
            array("name"=>Yii::t("summary","below target"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//销售人数
            //4月新招
            array("name"=>$this->search_month.Yii::t("summary"," month now"),"rowspan"=>2,"background"=>"#00B0F0","color"=>"#FFFFFF"),//销售人数

        );
        return $topList;
    }

    //顯示提成表的表格內容
    public function salesAnalysisHtml(){
        $html= '<table id="salesAnalysisArea" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
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
            "city_name",
            "now_num",
            "max_num",
            "min_num",
            "new_num"
        );
        return $bodyKey;
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            foreach ($data as $regionList){
                $cityList = $regionList["list"];
                $this->resetTdRow($cityList);
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $this->downJsonText[$cityList['city_name']][$keyStr]=$text;
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