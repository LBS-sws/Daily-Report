<?php

class UServiceForm extends CFormModel
{
	/* User Fields */
	public $start_date;//查詢開始日期
	public $end_date;//查詢結束日期

	public $data=array();

	public $th_sum=0;//所有th的个数

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'start_date'=>Yii::t("summary",'start date'),
            'end_date'=>Yii::t("summary",'end date'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('start_date,end_date','safe'),
			array('start_date,end_date','required'),
		);
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
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date
        );
    }

    public function retrieveData() {
	    $rptModel = new RptUService();
        $criteria = new ReportForm();
        $criteria->start_dt = $this->start_date;
        $criteria->end_dt = $this->end_date;
        $criteria->city = Yii::app()->user->city_allow();
        $rptModel->criteria = $criteria;
        $rptModel->retrieveData();
        $this->data = $rptModel->data;

        $session = Yii::app()->session;
        $session['uService_c01'] = $this->getCriteria();
        return true;
    }

    //顯示提成表的表格內容
    public function uServiceHtml(){
        $html= '<table id="summary" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            array("name"=>Yii::t("summary","Area")),//區域
            array("name"=>Yii::t("summary","City")),//城市
            array("name"=>Yii::t("summary","Staff Name")),//员工
            array("name"=>Yii::t("summary","Paid Amt")),//服务金额
        );
        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
        $topList = self::getTopArr();
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=$list["name"];
            $colList=key_exists("colspan",$list)?$list['colspan']:array();
            $trOne.="<th";
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("colspan",$list)){
                $colNum=count($colList);
                $trOne.=" colspan='{$colNum}' class='click-th'";
            }
            if(key_exists("background",$list)){
                $trOne.=" style='background:{$list["background"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
            if(!empty($colList)){
                foreach ($colList as $col){
                    $this->th_sum++;
                    $trTwo.="<th><span>".$col["name"]."</span></th>";
                }
            }else{
                $this->th_sum++;
            }
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr>";
        if(!empty($trTwo)){
            $html.="<tr>{$trTwo}</tr>";
        }
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            if(in_array($i,array(2,4,5,6,7,8))){
                $width=75;
            }elseif($i==9){
                $width=110;
            }elseif(in_array($i,array(1,3,12,14))){
                $width=90;
            }else{
                $width=83;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "area","city_name","name","amt"
        );
        return $bodyKey;
    }
    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $RegionKey = $this->getDataAllKeyStr();
        unset($RegionKey[0]);
        unset($RegionKey[1]);
        unset($RegionKey[2]);
        $RegionKey = array_merge(array("region"),$RegionKey);
        $html="";
        if(!empty($data)){
            $city = "none";
            $regionRow = [];//地区汇总
            foreach ($data as $staffCode=>$row) {
                if($city==="none"||$row["city_code"]!=$city){//地區匯總
                    if($city!="none"){
                        $html.=$this->printTableTr($regionRow,$RegionKey);
                        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                    }
                    $city = $row["city_code"];
                    $regionRow=[];
                    $regionRow["region"]=Yii::t("summary","Count：").$row["city_name"];
                }
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    if(!key_exists($keyStr,$regionRow)){
                        $regionRow[$keyStr]=0;
                    }
                    $text = key_exists($keyStr,$row)?$row[$keyStr]:"0";
                    $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                    $text = ComparisonForm::showNum($text);
                    $inputHide = TbHtml::hiddenField("excel[{$staffCode}][]",$text);
                    $html.="<td><span>{$text}</span>{$inputHide}</td>";
                }
                $html.="</tr>";
            }
            if($city!="none"){//地區匯總
                $html.=$this->printTableTr($regionRow,$RegionKey);
                $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            }
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $key=>$keyStr){
            $colSpan = $key==0?3:1;
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = ComparisonForm::getTextColorForKeyStr($text,$keyStr);
            $text = ComparisonForm::showNum($text);
            $inputHide = TbHtml::hiddenField("excel[{$data['region']}][]",$text);
            $html.="<td class='{$tdClass}' colspan='$colSpan' style='font-weight: bold'><span>{$text}</span>{$inputHide}</td>";
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
}