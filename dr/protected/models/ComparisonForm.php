<?php

class ComparisonForm extends CFormModel
{
	/* User Fields */
	public $start_date;
	public $end_date;
	public $comparison_year;
    public $month_start_date;
    public $month_end_date;

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
        $data = array();
        $suffix = Yii::app()->params['envSuffix'];
        $this->start_date = empty($this->start_date)?date("Y/01/01"):$this->start_date;
        $this->end_date = empty($this->end_date)?date("Y/m/t"):$this->end_date;
        $this->comparison_year = date("Y",strtotime($this->start_date));
        $this->month_start_date = date("m/d",strtotime($this->start_date));
        $this->month_end_date = date("m/d",strtotime($this->end_date));
        $lastStartDate = ($this->comparison_year-1)."/".$this->month_start_date;
        $lastEndDate = ($this->comparison_year-1)."/".$this->month_end_date;
        $where="(a.status_dt BETWEEN '{$this->start_date}' and '{$this->end_date}')";
        $where.="or (a.status_dt BETWEEN '{$lastStartDate}' and '{$lastEndDate}')";
        $rows = Yii::app()->db->createCommand()
            ->select("a.status_dt,a.status,f.rpt_cat,f.single,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.paid_type,a.amt_paid,a.ctrt_period,a.b4_paid_type,a.b4_amt_paid
            ,b.region,b.name as city_name,c.name as region_name")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->leftJoin("security{$suffix}.sec_city b","a.city=b.code")
            ->leftJoin("security{$suffix}.sec_city c","b.region=c.code")
            ->where("a.city not in ('ZY') and a.status in ('N','T') and ({$where})")
            ->order("a.city")
            ->queryAll();
        $cityList = array();
        if($rows){
            foreach ($rows as $row){
                //rpt_cat='INV' and single=1的客户服务是产品，所以需要筛选出去
                if($row["rpt_cat"]==="INV"&&intval($row["single"])===1){
                    continue;
                }
                $row["region"] = RptSummarySC::strUnsetNumber($row["region"]);
                $row["region_name"] = RptSummarySC::strUnsetNumber($row["region_name"]);
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $this->insertDataForRow($row,$data,$cityList);
            }
        }

        $this->insertUData($this->start_date,$this->end_date,$data,$cityList);
        $this->insertUData($lastStartDate,$lastEndDate,$data,$cityList);
        $this->data = $data;
        $session = Yii::app()->session;
        $session['comparison_c01'] = $this->getCriteria();
        return true;
    }

    private function insertUData($startDate,$endDate,&$data,$cityList){
        $year = intval($startDate);//服务的年份
        $json = Invoice::getInvData($startDate,$endDate);
        if($json["message"]==="Success"){
            $jsonData = $json["data"];
            foreach ($jsonData as $row){
                $city = $row["city"];
                $money = is_numeric($row["invoice_amt"])?floatval($row["invoice_amt"]):0;
                if(key_exists($city,$cityList)){
                    $region = $cityList[$city];
                    if($year==$this->comparison_year){
                        $uStr = "u_sum";
                        $newStr = "new_sum";
                    }else{
                        $uStr = "u_sum_last";
                        $newStr = "new_sum_last";
                    }
                    $data[$region]["list"][$city][$uStr]+=$money;
                    $data[$region]["list"][$city][$newStr]+=$money;
                }
            }
        }
    }

    private function insertDataForRow($row,&$data,&$cityList){
	    $year = intval($row["status_dt"]);//服务的年份
        $region = empty($row["region"])?"none":$row["region"];
        $city = empty($row["city"])?"none":$row["city"];
        $region = $city==="MO"?"MO":$region;//澳門地區單獨顯示
        if(!key_exists($region,$data)){
            $data[$region]=array(
                "region"=>$region,
                "region_name"=>$row["region_name"],
                "list"=>array()
            );
        }
        if(!key_exists($city,$data[$region]["list"])){
            $cityList[$city] = $region;//U系统同步使用
            $setRow = Yii::app()->db->createCommand()->select("*")->from("swo_summary_set")
                ->where("summary_year=:year and city=:city",
                    array(":year"=>$this->comparison_year,":city"=>$city)
                )->queryRow();//查询目标金额
            $data[$region]["list"][$city]=array(
                "city"=>$city,
                "city_name"=>$row["city_name"],
                "u_sum_last"=>0,//U系统金额(上一年)
                "u_sum"=>0,//U系统金额
                "new_sum_last"=>0,//新增(上一年)
                "new_sum"=>0,//新增
                "new_rate"=>0,//新增对比比例
                "stop_sum_last"=>0,//终止（上一年）
                "stop_sum"=>0,//终止
                "stop_rate"=>0,//终止对比比例
                "net_sum_last"=>0,//总和（上一年）
                "net_sum"=>0,//总和
                "net_rate"=>0,//总和对比比例
                "one_gross"=>$setRow?$setRow["one_gross"]:0,
                "one_gross_rate"=>0,
                "one_net"=>$setRow?$setRow["one_net"]:0,
                "one_net_rate"=>0,
                "two_gross"=>$setRow?$setRow["two_gross"]:0,
                "two_gross_rate"=>0,
                "two_net"=>$setRow?$setRow["two_net"]:0,
                "two_net_rate"=>0,
                "three_gross"=>$setRow?$setRow["three_gross"]:0,
                "three_gross_rate"=>0,
                "three_net"=>$setRow?$setRow["three_net"]:0,
                "three_net_rate"=>0
            );
        }
        if($row["paid_type"]=="M"){//月金额
            $money = $row["amt_paid"]*$row["ctrt_period"];
        }else{
            $money = $row["amt_paid"];
        }
        if($year==$this->comparison_year){
            $newStr = "new_sum";
            $stopStr = "stop_sum";
            $netStr = "net_sum";
        }else{
            $newStr = "new_sum_last";
            $stopStr = "stop_sum_last";
            $netStr = "net_sum_last";
        }
        switch ($row["status"]) {
            case "N"://新增
                $data[$region]["list"][$city][$newStr] += $money;
                break;
            case "T"://终止
                //$money *= -1;
                $data[$region]["list"][$city][$stopStr] += $money;
                break;
        }
        $data[$region]["list"][$city][$netStr] += $money;
    }

    protected function resetTdRow(&$list){
        $list["new_rate"] = $this->comparisonRate($list["new_sum"],$list["new_sum_last"]);
        $list["stop_rate"] = $this->comparisonRate($list["stop_sum"],$list["stop_sum_last"]);
        $list["net_rate"] = $this->comparisonRate($list["net_sum"],$list["net_sum_last"]);
        $list["two_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["two_gross"]);
        $list["two_net_rate"] = $this->comparisonRate($list["net_sum"],$list["two_net"]);

        if(SummaryForm::targetAllReady()){
            $list["one_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["one_gross"]);
            $list["one_net_rate"] = $this->comparisonRate($list["net_sum"],$list["one_net"]);
            $list["three_gross_rate"] = $this->comparisonRate($list["new_sum"],$list["three_gross"]);
            $list["three_net_rate"] = $this->comparisonRate($list["net_sum"],$list["three_net"]);
        }
    }

    public static function comparisonRate($num,$numLast){
        if(empty($numLast)){
            return 0;
        }else{
            $rate = ($num/$numLast) - 1;
            $rate = round($rate,3)*100;
            return $rate."%";
        }
    }

    //顯示提成表的表格內容
    public function comparisonHtml(){
        $html= '<table id="comparison" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }


    //顯示提成表的表格內容（表頭）
    private function tableTopHtml(){
        $monthStr = "（{$this->month_start_date} ~ {$this->month_end_date}）";
        $topList=array(
            array("name"=>"City","rowspan"=>2),//城市
            array("name"=>Yii::t("summary","YTD New").$monthStr,"background"=>"rgb(247,253,157)",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD新增
            array("name"=>Yii::t("summary","YTD Stop").$monthStr,"exprName"=>$monthStr,"background"=>"rgb(252,213,180)",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD终止
            array("name"=>Yii::t("summary","YTD Net").$monthStr,"background"=>"rgb(242,220,219)",
                "colspan"=>array(
                    array("name"=>$this->comparison_year-1),//对比年份
                    array("name"=>$this->comparison_year),//查询年份
                    array("name"=>Yii::t("summary","YoY change")),//YoY change
                )
            ),//YTD Net
        );
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (upside case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (upside case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (upside case)
        }
        $topList[]=array("name"=>Yii::t("summary","Annual target (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//年金额目标 (base case)
        $topList[]=array("name"=>Yii::t("summary","Goal degree (base case)"),"background"=>"#DCE6F1",
            "colspan"=>array(
                array("name"=>Yii::t("summary","Gross")),//Gross
                array("name"=>Yii::t("summary","Net")),//Net
            )
        );//目标完成度 (base case)
        if(SummaryForm::targetAllReady()){
            $topList[]=array("name"=>Yii::t("summary","Annual target (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//年金额目标 (minimum case)
            $topList[]=array("name"=>Yii::t("summary","Goal degree (minimum case)"),"background"=>"#FDE9D9",
                "colspan"=>array(
                    array("name"=>Yii::t("summary","Gross")),//Gross
                    array("name"=>Yii::t("summary","Net")),//Net
                )
            );//目标完成度 (minimum case)
        }
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
            $trOne.=" >".$clickName."</th>";
            if(!empty($colList)){
                foreach ($colList as $col){
                    $this->th_sum++;
                    $trTwo.="<th>".$col["name"]."</th>";
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
            if(in_array($i,array(3,6,9,18,19,20,21))){
                $width=90;
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
            "city_name","new_sum_last","new_sum","new_rate","stop_sum_last","stop_sum","stop_rate",
            "net_sum_last","net_sum","net_rate"
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
                            if($keyStr=="new_sum"){//调试U系统同步数据
                                $html.="<td data-u='{$cityList['u_sum']}'>{$text}</td>";
                            }else{
                                $html.="<td>{$text}</td>";
                            }
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["city_name"]=$regionList["region_name"];
                    $html.=$this->printTableTr($regionRow,$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["city_name"]=Yii::t("summary","all total");
            $html.=$this->printTableTr($allRow,$bodyKey);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $html.="<td><b>{$text}</b></td>";
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