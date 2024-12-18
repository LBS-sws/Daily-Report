<?php

class InsetCleanForm extends CFormModel
{
	/* User Fields */
    public $start_date;
    public $end_date;

    public $data=array();
    public $month;//查询月份
    public $month_day=0;//查询月份有多少天
    public $day_num=0;//查询天数

	public $th_sum=0;//所有th的个数

    public $downJsonText='';
    public $u_load_data=array();//查询时长数组
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'day_num'=>Yii::t('summary','day num'),
            'start_date'=>Yii::t('summary','start date'),
            'end_date'=>Yii::t('summary','end date')
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,end_date,day_num','safe'),
            array('start_date,end_date','required'),
            array('end_date','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        if(!empty($this->start_date)&&!empty($this->end_date)){
            if(date("Y/m/d",strtotime($this->start_date))>date("Y/m/d",strtotime($this->end_date))){
                $this->addError($attribute, "查询时间异常");
            }
        }
    }

    public function setCriteria($criteria){
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
        );
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();

        $data = array();
        $startDate = $this->start_date;
        $endDate = $this->end_date;

        $forDate = date("Y/m",strtotime($startDate));
        $forEndDate = date("Y/m",strtotime($endDate));
        $cleanSearchModel = new CountCleanSearch();
        $cleanSearchModel->setWhereSql(" and f.rpt_cat='IB'");//虫控
        //一次性服务
        $inServiceY = $cleanSearchModel->getClearServiceAddForYToMonth($startDate,$endDate);
        //恢复服务
        $inServiceR = $cleanSearchModel->getClearServiceForTypeToMonthEx($startDate,$endDate,'',"R");
        //更改
        $inServiceA = $cleanSearchModel->getClearServiceAToMonth($startDate,$endDate,'');
        //暂停
        $inServiceS = $cleanSearchModel->getClearServiceForSTToMonth($startDate,$endDate,'',"S");
        //终止
        $inServiceT = $cleanSearchModel->getClearServiceForSTToMonth($startDate,$endDate,'',"T");
        $cleanSearchModel->setWhereSql(" and f.rpt_cat='IA'");//清洁
        //一次性服务
        $clServiceY = $cleanSearchModel->getClearServiceAddForYToMonth($startDate,$endDate);
        //恢复服务
        $clServiceR = $cleanSearchModel->getClearServiceForTypeToMonthEx($startDate,$endDate,'',"R");
        //更改
        $clServiceA = $cleanSearchModel->getClearServiceAToMonth($startDate,$endDate,'');
        //暂停
        $clServiceS = $cleanSearchModel->getClearServiceForSTToMonth($startDate,$endDate,'',"S");
        //终止
        $clServiceT = $cleanSearchModel->getClearServiceForSTToMonth($startDate,$endDate,'',"T");

        while ($forDate<=$forEndDate){
            $defMoreList=$this->defMoreCity();
            $defMoreList["year_month"]=$forDate;

            $this->pushData($defMoreList,$forDate,$inServiceY,"in_one_service");
            $this->pushData($defMoreList,$forDate,$inServiceR,"in_r_service");
            $this->pushData($defMoreList,$forDate,$inServiceA,"in_a_service");
            $this->pushData($defMoreList,$forDate,$inServiceS,"in_s_service");
            $this->pushData($defMoreList,$forDate,$inServiceT,"in_t_service");

            $this->pushData($defMoreList,$forDate,$clServiceY,"cl_one_service");
            $this->pushData($defMoreList,$forDate,$clServiceR,"cl_r_service");
            $this->pushData($defMoreList,$forDate,$clServiceA,"cl_a_service");
            $this->pushData($defMoreList,$forDate,$clServiceS,"cl_s_service");
            $this->pushData($defMoreList,$forDate,$clServiceT,"cl_t_service");
            $data[]=$defMoreList;
            $forDate = date("Y/m",strtotime("{$forDate}/01 + 1 months"));
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['insetClean_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    private function pushData(&$defMoreList,$thisMonth,$list,$keyStr){
        if(key_exists($thisMonth,$list)){
            $defMoreList[$keyStr]+=$list[$thisMonth];
        }
    }

    private function defMoreCity(){
        return array(
            "year_month"=>0,
            "in_one_service"=>0,//一次性
            "in_u_inv"=>"",//产品
            "in_u_service"=>"",//服务
            "in_s_service"=>0,//终止服务
            "in_r_service"=>0,//恢复服务
            "in_t_service"=>0,//暂停服务
            "in_a_service"=>0,//更改服务
            "in_stop_month_rate"=>"",
            "in_stop_all_rate"=>"",
            "cl_one_service"=>0,//一次性
            "cl_u_inv"=>"",//产品
            "cl_u_service"=>"",//服务
            "cl_s_service"=>0,//终止服务
            "cl_r_service"=>0,//恢复服务
            "cl_t_service"=>0,//暂停服务
            "cl_a_service"=>0,//更改服务
            "cl_stop_month_rate"=>"",
            "cl_stop_all_rate"=>"",
        );
    }

    protected function resetTdRow(&$list,$bool=false){
    }

    //顯示提成表的表格內容
    public function insetCleanHtml(){
        $html= '<table id="insetClean" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $arr = array(
            array(
                "name"=>Yii::t("summary","One Service"),//一次性
            ),
            array(
                "name"=>Yii::t("summary","U INV"),//产品
            ),
            array(
                "name"=>Yii::t("summary","U service"),//服务
            ),
            array(
                "name"=>Yii::t("summary","Terminate service"),//终止服务
            ),
            array(
                "name"=>Yii::t("summary","Resume service"),//恢复服务
            ),
            array(
                "name"=>Yii::t("summary","Suspended service"),//暂停服务
            ),
            array(
                "name"=>Yii::t("summary","Amendment service"),//更改服务
            ),
            array(
                "name"=>Yii::t("summary","Month Stop Rate"),//月停单率
            ),
            array(
                "name"=>Yii::t("summary","Composite Stop Rate"),//综合停单率
            ),
        );
        $topList=array(
            //终止+恢复+暂停+更改金额
            //日期
            array(
                "name"=>"日期",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Year month"))
                )
            ),
            //虫控
            array(
                "name"=>"虫控",
                "background"=>"#f7fd9d",
                "colspan"=>$arr
            ),
            //清洁
            array(
                "name"=>"清洁",
                "background"=>"#fcd5b4",
                "colspan"=>$arr
            )
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
            $width=90;
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
            "year_month",
            "in_one_service",
            "in_u_inv",
            "in_u_service",
            "in_s_service",
            "in_r_service",
            "in_t_service",
            "in_a_service",
            "in_stop_month_rate",
            "in_stop_all_rate",
            "cl_one_service",
            "cl_u_inv",
            "cl_u_service",
            "cl_s_service",
            "cl_r_service",
            "cl_t_service",
            "cl_a_service",
            "cl_stop_month_rate",
            "cl_stop_all_rate",
        );
        return $bodyKey;
    }

    //設置百分比顏色
    private function getTdClassForRow($row){
        $tdClass = "";
        return $tdClass;
    }

    //將城市数据寫入表格
    private function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            foreach ($data as $region=>$cityList){
                $this->resetTdRow($cityList);
                $html.="<tr>";
                foreach ($bodyKey as $keyStr){
                    $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                    $tdClass = $this->getTdClassForRow($cityList);
                    $dataClick="";
                    $this->downJsonText["excel"][$region][$keyStr]=$text;
                    $html.="<td class='{$tdClass}' {$dataClick}><span>{$text}</span></td>";
                }
                $html.="</tr>";
            }
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $this->downJsonText["excel"][$data['city_name']]["count"][]=$text;
            $html.="<td style='font-weight: bold'><span>{$text}</span></td>";
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

    //下載
    public function downExcel($excelData){
        if(!is_array($excelData)){
            $excelData = json_decode($excelData,true);
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new InsetCleanDown();
        $excel->SetHeaderTitle(Yii::t("app","Insect and clean")."（".$this->start_date." ~ ".$this->end_date."）");
        $titleTwo = "月停单率：（“一次性服务+新增（产品）” + “上月一次性服务+新增产品” + 终止金额/12） / 上月服务生意额";
        $titleTwo.= "\n";
        $titleTwo.= "综合停单率：【（终止 + 恢复 + 暂停 + 更改金额） / 12 】/ 【整个上月服务生意额 + （上月一次性服务+新增产品（负数））】* 100%";
        $excel->colTwo=0;
        $excel->SetHeaderString($titleTwo);
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setCleanData($excelData);
        $excel->outExcel(Yii::t("app","Insect and clean"));
    }
}