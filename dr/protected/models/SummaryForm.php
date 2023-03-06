<?php

class SummaryForm extends CFormModel
{
	/* User Fields */
	public $start_date;
	public $end_date;
	public $summary_year;

	public $data=array();

	public $th_sum=1;//所有th的个数

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date'),
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
        $this->summary_year = date("Y",strtotime($this->start_date));
	    $rptModel = new RptSummarySC();
        $criteria = new ReportForm();
        $criteria->start_dt = $this->start_date;
        $criteria->end_dt = $this->end_date;
        $rptModel->criteria = $criteria;
        $rptModel->retrieveData();
        $this->data = $rptModel->data;
        if($this->data){
            foreach ($this->data as $regionKey=>$regionList){
                if(!empty($regionList["list"])){
                    foreach ($regionList["list"] as $cityKey=>$cityList){
                        $this->data[$regionKey]["list"][$cityKey]["num_growth"]=0;//净增长
                        $this->data[$regionKey]["list"][$cityKey]["one_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["one_net_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["two_net_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_gross"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_gross_rate"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_net"]=0;
                        $this->data[$regionKey]["list"][$cityKey]["three_net_rate"]=0;
                        //放入目标金额
                        $row = Yii::app()->db->createCommand()->select("*")->from("swo_summary_set")
                            ->where("summary_year=:year and city=:city",
                                array(":year"=>$this->summary_year,":city"=>$cityKey)
                            )->queryRow();
                        if($row){
                            $this->data[$regionKey]["list"][$cityKey]["one_gross"]=empty($row["one_gross"])?0:floatval($row["one_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["one_net"]=empty($row["one_net"])?0:floatval($row["one_net"]);
                            $this->data[$regionKey]["list"][$cityKey]["two_gross"]=empty($row["two_gross"])?0:floatval($row["two_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["two_net"]=empty($row["two_net"])?0:floatval($row["two_net"]);
                            $this->data[$regionKey]["list"][$cityKey]["three_gross"]=empty($row["three_gross"])?0:floatval($row["three_gross"]);
                            $this->data[$regionKey]["list"][$cityKey]["three_net"]=empty($row["three_net"])?0:floatval($row["three_net"]);
                        }
                    }
                }
            }
        }
        $session = Yii::app()->session;
        $session['summary_c01'] = $this->getCriteria();
        return true;
    }

    protected function resetTdRow(&$list){
	    $newSum = $list["num_new"]+$list["u_invoice_sum"];//所有新增总金额
	    $list["num_growth"]+=$list["num_new"];
	    $list["num_growth"]+=$list["u_invoice_sum"];
	    $list["num_growth"]+=$list["num_stop"];
	    $list["num_growth"]+=$list["num_restore"];
	    $list["num_growth"]+=$list["num_pause"];
	    $list["num_growth"]+=$list["num_update"];

        $list["two_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["two_gross"]);
        $list["two_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["two_net"]);
        if(SummaryForm::targetAllReady()){
            $list["one_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["one_gross"]);
            $list["one_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["one_net"]);
            $list["three_gross_rate"] = ComparisonForm::comparisonRate($newSum,$list["three_gross"]);
            $list["three_net_rate"] = ComparisonForm::comparisonRate($list["num_growth"],$list["three_net"]);
        }
    }

    //顯示提成表的表格內容
    public function summaryHtml(){
        $html= '<table id="summary" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }


    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
        $topList=array(
            array("name"=>"City","rowspan"=>2),//城市
            array("name"=>"Signing status","background"=>"rgb(247,253,157)",
                "colspan"=>array(
                    array("name"=>"New(service)"),//新增服务
                    array("name"=>"New(INV)"),//新增（产品）
                    array("name"=>"Terminate service"),//终止服务
                    array("name"=>"Resume service"),//恢复服务
                    array("name"=>"Suspended service"),//暂停服务
                    array("name"=>"Amendment service"),//更改服务
                    array("name"=>"Net growth"),//净增长
                )
            ),//签单情况
            array("name"=>"New customer(service)","background"=>"rgb(252,213,180)",
                "colspan"=>array(
                    array("name"=>"long month"),//长约（>=12月）
                    array("name"=>"short month"),//短约
                    array("name"=>"cate service"),//餐饮客户
                    array("name"=>"not cate service"),//非餐饮客户
                )
            ),//新增客户（服务）
            array("name"=>"New customer(INV)","background"=>"rgb(242,220,219)",
                "colspan"=>array(
                    array("name"=>"cate service"),//餐饮客户
                    array("name"=>"not cate service"),//非餐饮客户
                )
            ),//新增客户（产品）
        );
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>"Annual target (upside case)","background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>"Gross"),//Gross
                    array("name"=>"Net"),//Net
                )
            );//年金额目标 (upside case)
            $topList[]=array("name"=>"Goal degree (upside case)","background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>"Gross"),//Gross
                    array("name"=>"Net"),//Net
                )
            );//目标完成度 (upside case)
        }
        $topList[]=array("name"=>"Annual target (base case)","background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>"Gross"),//Gross
                array("name"=>"Net"),//Net
            )
        );//年金额目标 (base case)
        $topList[]=array("name"=>"Goal degree (base case)","background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>"Gross"),//Gross
                array("name"=>"Net"),//Net
            )
        );//目标完成度 (base case)
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>"Annual target (minimum case)","background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>"Gross"),//Gross
                    array("name"=>"Net"),//Net
                )
            );//年金额目标 (minimum case)
            $topList[]=array("name"=>"Goal degree (minimum case)","background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>"Gross"),//Gross
                    array("name"=>"Net"),//Net
                )
            );//目标完成度 (minimum case)
        }
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=Yii::t("summary",$list["name"]);
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
            $trOne.=" >".$clickName."</th>";
            if(!empty($colList)){
                foreach ($colList as $col){
                    $this->th_sum++;
                    $trTwo.="<th>".Yii::t("summary",$col["name"])."</th>";
                }
            }
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr><tr>{$trTwo}</tr>";
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            if(in_array($i,array(1,3,4,5,6,7))){
                $width=75;
            }elseif($i==8){
                $width=110;
            }elseif(in_array($i,array(2,11,13))){
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
            $moData = key_exists("MO",$this->data)?$this->data["MO"]:array();
            unset($this->data["MO"]);//澳门需要单独处理
            $html.=$this->showServiceHtml($this->data);
            $html.=$this->showServiceHtmlForMO($moData);
            $html.="</tbody>";
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name","num_new","u_invoice_sum","num_stop","num_restore","num_pause","num_update",
            "num_growth","num_long","num_short","num_cate","num_not_cate","u_num_cate","u_num_not_cate"
        );
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="one_gross";
            $bodyKey[]="one_net";
            $bodyKey[]="one_gross_rate";
            $bodyKey[]="one_net_rate";
        }
        $bodyKey[]="two_gross";
        $bodyKey[]="two_net";
        $bodyKey[]="two_gross_rate";
        $bodyKey[]="two_net_rate";
        if(SummaryForm::targetAllReady()){
            $bodyKey[]="three_gross";
            $bodyKey[]="three_net";
            $bodyKey[]="three_gross_rate";
            $bodyKey[]="three_net_rate";
        }
        return $bodyKey;
    }
    //將城市数据寫入表格(澳门)
    private function showServiceHtmlForMO($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            foreach ($data["list"] as $cityList) {
                $this->resetTdRow($cityList);
                $html="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $html.="<td>{$text}</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }
    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = [];//总计(所有地区)
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = [];//地区汇总
                    foreach ($regionList["list"] as $cityList) {
                        $this->resetTdRow($cityList);
                        $html.="<tr>";
                        foreach ($bodyKey as $keyStr){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            if(!key_exists($keyStr,$allRow)){
                                $allRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $html.="<td>{$text}</td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["city_name"]=$regionList["region_name"];
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey);
            $html.="<tr><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data);
        $html="<tr class='click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $html.="<td><b>{$text}</b></td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function tableFooterHtml(){
        $html="<tfoot>";
        $html.="<tr><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        $html.="</tfoot>";
        return $html;
    }

    public static function targetAllReady(){
        return Yii::app()->user->validFunction('CN15');
    }
}