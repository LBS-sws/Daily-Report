<?php

class UServiceForm extends CFormModel
{
	/* User Fields */
    public $search_start_date;//查詢開始日期
    public $search_end_date;//查詢結束日期
    public $search_type=3;//查詢類型 1：季度 2：月份 3：天
    public $search_year;//查詢年份
    public $search_month;//查詢月份
    public $search_quarter;//查詢季度
	public $start_date;//查詢開始日期
	public $end_date;//查詢結束日期
	public $condition;//筛选条件
	public $seniority_min=6;//年资（最小）
	public $seniority_max=9999;//年资（最大）
    public $month_type;
    public $city;
    public $city_desc="全部";
    public $city_allow;

	public $data=array();

	public $th_sum=0;//所有th的个数

    public $downJsonText='';

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
            'search_type'=>Yii::t('summary','search type'),
            'search_start_date'=>Yii::t('summary','start date'),
            'search_end_date'=>Yii::t('summary','end date'),
            'search_year'=>Yii::t('summary','search year'),
            'search_quarter'=>Yii::t('summary','search quarter'),
            'search_month'=>Yii::t('summary','search month'),
            'city'=>Yii::t('app','City'),
            'condition'=>Yii::t('summary','screening condition'),
            'seniority_min'=>Yii::t('summary','seniority（month）'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('condition,seniority_min,seniority_max,search_type,city,city_desc,search_start_date,search_end_date,search_year,search_quarter,search_month','safe'),
            array('search_type','required'),
            array('search_type','validateDate'),
            array('city','validateCity'),
		);
	}

    public function validateCity($attribute, $params) {
	    if(empty($this->city)){
            $city_allow = Yii::app()->user->city_allow();
        }else{
	        $city_allow = explode("~",$this->city);
            $city_allow = implode("','",$city_allow);
            $city_allow = "'{$city_allow}'";
        }
        $this->city_allow = $city_allow;
    }

    public function validateDate($attribute, $params) {
        switch ($this->search_type){
            case 1://1：季度
                if(empty($this->search_year)||empty($this->search_quarter)){
                    $this->addError($attribute, "查询季度不能为空");
                }else{
                    $dateStr = $this->search_year."/".$this->search_quarter."/01";
                    $this->start_date = date("Y/m/01",strtotime($dateStr));
                    $this->end_date = date("Y/m/t",strtotime($dateStr." + 2 month"));
                    $this->month_type = $this->search_quarter;
                }
                break;
            case 2://2：月份
                if(empty($this->search_year)||empty($this->search_month)){
                    $this->addError($attribute, "查询月份不能为空");
                }else{
                    $dateTimer = strtotime($this->search_year."/".$this->search_month."/01");
                    $this->start_date = date("Y/m/01",$dateTimer);
                    $this->end_date = date("Y/m/t",$dateTimer);
                    $i = ceil($this->search_month/3);//向上取整
                    $this->month_type = 3*$i-2;
                }
                break;
            case 3://3：天
                if(empty($this->search_start_date)||empty($this->search_start_date)){
                    $this->addError($attribute, "查询日期不能为空");
                }else{
                    $startYear = date("Y",strtotime($this->search_start_date));
                    $endYear = date("Y",strtotime($this->search_end_date));
                    if($startYear!=$endYear){
                        $this->addError($attribute, "请把开始年份跟结束年份保持一致");
                    }else{
                        $this->search_month = date("n",strtotime($this->search_start_date));
                        $i = ceil($this->search_month/3);//向上取整
                        $this->month_type = 3*$i-2;
                        $this->search_year = $startYear;
                        $this->start_date = $this->search_start_date;
                        $this->end_date = $this->search_end_date;
                    }
                }
                break;
        }
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
            'search_year'=>$this->search_year,
            'search_month'=>$this->search_month,
            'search_type'=>$this->search_type,
            'search_quarter'=>$this->search_quarter,
            'search_start_date'=>$this->search_start_date,
            'search_end_date'=>$this->search_end_date,
            'condition'=>$this->condition,
            'seniority_min'=>$this->seniority_min,
            'seniority_max'=>$this->seniority_max,
            'city_desc'=>$this->city_desc,
            'city'=>$this->city
        );
    }

    public function retrieveData() {
	    $rptModel = new RptUService();
	    $rptModel->condition = $this->condition;
	    $rptModel->seniority_min = $this->seniority_min;
	    $rptModel->seniority_max = $this->seniority_max;
        $criteria = new ReportForm();
        $criteria->start_dt = $this->start_date;
        $criteria->end_dt = $this->end_date;
        $criteria->city = $this->city_allow;
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
            array("name"=>Yii::t("summary","Area"),"background"=>"#f7fd9d"),//區域
            array("name"=>Yii::t("summary","City"),"background"=>"#fcd5b4"),//城市
            array("name"=>Yii::t("summary","Staff Name"),"background"=>"#f2dcdb"),//员工
            array("name"=>Yii::t("summary","dept name"),"background"=>"#FDE9D9"),//职位
            array("name"=>Yii::t("summary","entry month"),"background"=>"#DCE6F1"),//入职月数
            array("name"=>Yii::t("summary","Paid Amt"),"background"=>"#d1e2fb"),//服务金额
        );
        return $topList;
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
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "area","u_city_name","name","dept_name","entry_month","amt"
        );
        return $bodyKey;
    }
    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $RegionKey = array("region","entry_month","amt");
        $html="";
        if(!empty($data)){
            $city = "none";
            $regionRow = array("staff_num"=>0);//地区汇总
            foreach ($data as $staffCode=>$row) {
                if($city==="none"||$row["city_code"]!=$city){//地區匯總
                    if($city!="none"){
                        $html.=$this->printTableTr($regionRow,$RegionKey);
                        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                    }
                    $city = $row["city_code"];
                    $regionRow = array("staff_num"=>0);
                    $regionRow["region"]=Yii::t("summary","Count：").$row["u_city_name"];
                }
                $regionRow["staff_num"]++;
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    if(!key_exists($keyStr,$regionRow)){
                        $regionRow[$keyStr]=0;
                    }
                    $text = key_exists($keyStr,$row)?$row[$keyStr]:"0";
                    $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                    $text = self::showNum($text,$keyStr);
                    $this->downJsonText["excel"][$staffCode][]=$text;
                    $html.="<td><span>{$text}</span></td>";
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

    public function showNum($num,$str=""){
        if($str=="amt"){
            $number = floatval($num);
            $number=sprintf("%.2f",$number);
        }else{
            $number = $num;
        }
        return $number;
    }

    protected function printTableTr($data,$bodyKey){
        $html="<tr class='tr-end click-tr'>";
        $html.="<td colspan='4' style='font-weight: bold'>".$data["region"]."</td>";
        $html.="<td style='font-weight: bold'>".$data["entry_month"]."</td>";
        $html.="<td style='font-weight: bold'>".self::showNum($data["amt"],"amt")."</td>";
        $html.="</tr>";
        $this->downJsonText["excel"]["count_{$data['region']}"]=array(
            "region"=>$data["region"],
            "entry_month"=>$data["entry_month"],
            "amt"=>$data["amt"],
        );
        $data["region"] = Yii::t("summary","average：");
        $data["month_average"] = round($data["entry_month"]/$data["staff_num"]);
        $data["amt_average"] = self::showNum(($data["amt"]/$data["staff_num"]),"amt");
        $html.="<tr class='tr-end'>";
        $html.="<td colspan='4' style='font-weight: bold'>".$data["region"]."</td>";
        $html.="<td style='font-weight: bold;color:red;'>".$data["month_average"]."</td>";
        $html.="<td style='font-weight: bold;color:red;'>".$data["amt_average"]."</td>";
        $html.="</tr>";
        $this->downJsonText["excel"]["average_{$data['region']}"]=array(
            "region"=>$data["region"],
            "entry_month"=>$data["month_average"],
            "amt"=>$data["amt_average"],
        );
        $html.="</tr>";
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
            $excelData = empty($excelData)?array():$excelData;
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->SetHeaderTitle(Yii::t("app","U Service Amount"));
        $excel->SetHeaderString($this->start_date." ~ ".$this->end_date);
        $excel->init();
        $excel->setUServiceHeader($headList);
        $excel->setUServiceData($excelData);
        $excel->outExcel(Yii::t("app","U Service Amount"));
    }

    public static function getCityList(){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("security{$suffix}.sec_city")
            ->where("code in ({$city_allow})")
            ->order("name")
            ->queryAll();
        $list = array();
        if($rows){
            foreach ($rows as $row){
                $list[$row["code"]] =$row["name"];
            }
        }
        return $list;
    }

    public static function getConditionList(){
        return array(
            1=>Yii::t("summary","Technician level"),
            2=>Yii::t("summary","Technical supervisor"),
            3=>Yii::t("summary","Other personnel"),
            4=>Yii::t("summary","KA Technician"),
        );
    }

    public static function getSelectType(){
        $arr = array();
        if(Yii::app()->user->validFunction('CN18')){
            $arr[1]=Yii::t("summary","search quarter");//季度
        }
        if(Yii::app()->user->validFunction('CN19')){
            $arr[2]=Yii::t("summary","search month");//月度
        }
        $arr[3]=Yii::t("summary","search day");//日期
        return $arr;
    }
}