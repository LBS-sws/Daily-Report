<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class SummaryTable extends SummaryForm{
    private static $whereSQL=" and not(f.rpt_cat='INV' and f.single=1)";
    private static $IDBool=true;//是否需要ID服務的查詢
    private static $KABool=true;//是否需要KA服務的查詢

    private static $system=0;//0:大陸 1:台灣 2:國際

    public $city_allow;

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $city = key_exists("city",$_GET)?$_GET["city"]:0;
        $cityList = CitySetForm::getCityAllowForCity($city);
        if(CountSearch::getSystem()==2){
            if(in_array("MY",$cityList)){
                $cityList = array_merge($cityList,array("SL","KL","JB"));
            }
        }
        $this->city_allow = "'".implode("','",$cityList)."'";
        $this->start_date = key_exists("startDate",$_GET)?$_GET["startDate"]:"";
        $this->end_date = key_exists("endDate",$_GET)?$_GET["endDate"]:"";
        $clickList = parent::clickList();
        $clickList = array_column($clickList,"type");
        $type = key_exists("type",$_GET)?$_GET["type"]:"";
        if(in_array($type,$clickList)){
            return $this->$type();
        }else{
            return "<p>数据异常，请刷新重试</p>";
        }
    }

    //餐饮 新增（产品）
    private function ServiceINVCate(){
        $invRows = SummaryTable::getUInvListForType($this->start_date,$this->end_date,$this->city_allow,$type="cate");
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        return $invTable["html"];
    }

    //非餐饮 新增（产品）
    private function ServiceINVCateNot(){
        $invRows = SummaryTable::getUInvListForType($this->start_date,$this->end_date,$this->city_allow,$type="not");
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        return $invTable["html"];
    }

    //一次性服务+新增（产品）
    private function ServiceINVNew(){
        $invRows = SummaryTable::getUInvList($this->start_date,$this->end_date,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($this->start_date,$this->end_date,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //新增（产品）(上个月)
    private function ServiceINVLast(){
        $monthStartDate = CountSearch::computeLastMonth($this->start_date);
        $monthEndDate = CountSearch::computeLastMonth($this->end_date);
        $invRows = SummaryTable::getUInvList($monthStartDate,$monthEndDate,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        return $invTable["html"];
    }

    //一次性服务+新增（产品）(上个月)
    private function ServiceINVMonthNew(){
        $monthStartDate = CountSearch::computeLastMonth($this->start_date);
        $monthEndDate = CountSearch::computeLastMonth($this->end_date);
        $invRows = SummaryTable::getUInvList($monthStartDate,$monthEndDate,$this->city_allow);
        $invTable = SummaryTable::getTableForInv($invRows,$this->city_allow);
        $rows = SummaryTable::getOneServiceRows($monthStartDate,$monthEndDate,$this->city_allow);
        return SummaryTable::getTableForRows($rows,$this->city_allow,$invTable);
    }

    //餐饮（客户服务）
    private function ServiceCate(){
        $rows = self::getServiceForCate($this->start_date,$this->end_date,$this->city_allow,"cate");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //非餐饮（客户服务）
    private function ServiceCateNot(){
        $rows = self::getServiceForCate($this->start_date,$this->end_date,$this->city_allow,"not");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //长约
    private function ServiceLong(){
        $rows = self::getServiceForMonthType($this->start_date,$this->end_date,$this->city_allow,"long");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //短约
    private function ServiceShort(){
        $rows = self::getServiceForMonthType($this->start_date,$this->end_date,$this->city_allow,"short");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //一次性
    private function ServiceOne(){
        $rows = self::getOneServiceRows($this->start_date,$this->end_date,$this->city_allow);
        return self::getTableForRows($rows,$this->city_allow);
    }

    //一次性(上个月)
    private function ServiceOneLast(){
        //上月的開始及結束時間
        $start_date = CountSearch::computeLastMonth($this->start_date);
        $end_date = CountSearch::computeLastMonth($this->end_date);
        $rows = self::getOneServiceRows($start_date,$end_date,$this->city_allow);
        return self::getTableForRows($rows,$this->city_allow);
    }

    //新增服务
    private function ServiceNew(){
        $rows = self::getServiceRowsForAdd($this->start_date,$this->end_date,$this->city_allow);
        return self::getTableForRows($rows,$this->city_allow);
    }

    //更改服务
    private function ServiceAmendment(){
        $rows = self::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"A");
        return self::getTableForRowsTwo($rows,$this->city_allow);
    }

    //暂停服务
    private function ServiceSuspended(){
        $rows = self::getServiceSTForType($this->start_date,$this->end_date,$this->city_allow,"S");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //恢复服务
    private function ServiceRenewal(){
        $rows = self::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"R");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //终止服务
    private function ServiceStop(){
        $rows = self::getServiceSTListForType($this->start_date,$this->end_date,$this->city_allow,"T");
        $listGood = self::getTableListForRowsEx($rows["goodList"],$this->city_allow,"终止");
        $listNot = self::getTableListForRowsEx($rows["notList"],$this->city_allow,"终止");
        return self::getTableForTab($listGood,$listNot);
    }

    public static function getTableForTab($listGood,$listNot){
        $tabs = array();
        $tabs[] = array(
            'label'=>"正常的终止服务",
            'content'=>"<p>&nbsp;</p>".$listGood["html"],
            'active'=>false,
        );
        $tabs[] = array(
            'label'=>"暂停后的终止服务",
            'content'=>"<p>&nbsp;</p>".$listNot["html"],
            'active'=>false,
        );
        $html = "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr><th>类型</th><th>数量</th><th>金额</th></tr><tbody>";
        $html.="<tr>";
        $html.="<td class='text-right'>"."正常的终止服务"."</td>";
        $html.="<td>".$listGood["count"]."</td>";
        $html.="<td>".$listGood["amt"]."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td class='text-right'>"."暂停后的终止服务"."</td>";
        $html.="<td>".$listNot["count"]."</td>";
        $html.="<td>".$listNot["amt"]."</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td class='text-right'><b>"."汇总："."</b></td>";
        $html.="<td><b>".($listGood["count"]+$listNot["count"])."</b></td>";
        $html.="<td><b>".($listGood["amt"]+$listNot["amt"])."</b></td>";
        $html.="</tr>";
        $html.="</tbody>";
        $html.="</table>";
        $tabs[] = array(
            'label'=>"汇总",
            'content'=>"<p>&nbsp;</p>".$html,
            'active'=>true,
        );
        return TbHtml::tabbable("tabs", $tabs, array());
    }

    public static function getTableForRows($rows,$city_allow,$invTable=array(),$week_day=1,$month_day=0){
        $companyList = GetNameToId::getCompanyList($city_allow);
        $html="";
        if(!empty($invTable)){
            $html.=$invTable["html"];
            $html.="<p>&nbsp;</p>";
        }
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('summary','menu name')."</th>";//菜單名稱
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='100px'>".Yii::t('service','Resp. Sales')."</th>";//客户编号及名称
        $html.="<th>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='120px'>".Yii::t('service','Paid Amt')."</th>";//服务金额
        $html.="<th width='80px'>".Yii::t('customer','Contract Period')."</th>";//合同年限(月)
        $html.="<th width='100px'>".Yii::t('summary','all money')."</th>";//合同总金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        if($rows){
            $sum = 0;
            $count=0;
            $html.="<tbody>";
            $city="";
            $cityName = "";
            foreach ($rows as $row){
                $count++;
                if($city!=$row["city"]){
                    $cityName= General::getCityName($row["city"]);
                    $city = $row["city"];
                }
                switch ($row["sql_type_name"]){
                    case "D":
                        $menuStr = Yii::t("app","Customer Service ID");//菜單名稱
                        $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                        break;
                    case "KA":
                        $menuStr = Yii::t("app","Customer Service KA");//菜單名稱
                        $link = self::drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$row['id']));
                        break;
                    default:
                        $menuStr = Yii::t("app","Customer Service");//菜單名稱
                        $link = self::drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$row['id']));
                }
                $companyName = key_exists($row["company_id"],$companyList)?$companyList[$row["company_id"]]["codeAndName"]:$row["company_id"];
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;

                if($row["paid_type"]=="M") {//月金额
                    $row["sum_amount"] = $row["amt_paid"]*$row["ctrt_period"];
                }else{
                    $row["sum_amount"] = $row["amt_paid"];
                }
                $row["sum_amount"]=round($row["sum_amount"],2);
                $sum+=$row["sum_amount"];
                $html.="<tr data-id='{$row["id"]}'>";
                $html.="<td>".$menuStr."</td>";
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["salesman"]."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            if(!empty($invTable)){
                $html.="<tr><td colspan='10'>&nbsp;</td></tr>";
                $count+=$invTable["count"];
                $sum+=$invTable["sum"];
                $html.="<tr>";
                $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
                $html.="<td colspan='2'>".$count."</td>";
                $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
                $html.="<td colspan='2'>".$sum."</td>";
                $html.="</tr>";
            }
            if(!empty($month_day)){
                $html.="<tr>";
                $monthAmt = $sum/$week_day*$month_day;
                $monthAmt = round($monthAmt,2);
                $sumTxt = "全月预估金额：({$sum}÷{$week_day}) × {$month_day} = {$monthAmt}";
                $html.="<td colspan='10' class='text-right'>".$sumTxt."</td>";
                $html.="<td>&nbsp;</td>";
                $html.="</tr>";
            }
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='10'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return $html;
    }

    public static function getTableListForRows($rows,$city_allow){
        $companyList = GetNameToId::getCompanyList($city_allow);
        $html="";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('summary','menu name')."</th>";//菜單名稱
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='100px'>".Yii::t('service','Resp. Sales')."</th>";//客户编号及名称
        $html.="<th>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='120px'>".Yii::t('service','Paid Amt')."</th>";//服务金额
        $html.="<th width='80px'>".Yii::t('customer','Contract Period')."</th>";//合同年限(月)
        $html.="<th width='100px'>".Yii::t('summary','all money')."</th>";//合同总金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $sum = 0;
        $count=0;
        if($rows){
            $html.="<tbody>";
            $city="";
            $cityName = "";
            foreach ($rows as $row){
                $count++;
                if($city!=$row["city"]){
                    $cityName= General::getCityName($row["city"]);
                    $city = $row["city"];
                }
                switch ($row["sql_type_name"]){
                    case "D":
                        $menuStr = Yii::t("app","Customer Service ID");//菜單名稱
                        $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                        break;
                    case "KA":
                        $menuStr = Yii::t("app","Customer Service KA");//菜單名稱
                        $link = self::drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$row['id']));
                        break;
                    default:
                        $menuStr = Yii::t("app","Customer Service");//菜單名稱
                        $link = self::drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$row['id']));
                }
                $companyName = key_exists($row["company_id"],$companyList)?$companyList[$row["company_id"]]["codeAndName"]:$row["company_id"];
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;

                if($row["paid_type"]=="M") {//月金额
                    $row["sum_amount"] = $row["amt_paid"]*$row["ctrt_period"];
                }else{
                    $row["sum_amount"] = $row["amt_paid"];
                }
                $row["sum_amount"]=round($row["sum_amount"],2);
                $sum+=$row["sum_amount"];
                $html.="<tr data-id='{$row["id"]}'>";
                $html.="<td>".$menuStr."</td>";
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["salesman"]."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            if(!empty($invTable)){
                $html.="<tr><td colspan='10'>&nbsp;</td></tr>";
                $count+=$invTable["count"];
                $sum+=$invTable["sum"];
                $html.="<tr>";
                $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
                $html.="<td colspan='2'>".$count."</td>";
                $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
                $html.="<td colspan='2'>".$sum."</td>";
                $html.="</tr>";
            }
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='10'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return array("html"=>$html,"amt"=>$sum,"count"=>$count);
    }

    public static function getTableListForRowsEx($rows,$city_allow,$typeStr=""){
        $companyList = GetNameToId::getCompanyList($city_allow);
        $html="";
        $html.= "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('summary','menu name')."</th>";//菜單名稱
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".$typeStr.Yii::t('summary','search day')."</th>";//日期
        if(!empty($typeStr)){
            $html.="<th width='90px'>".Yii::t('service','Sign Date')."</th>";//签约日期
        }
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='100px'>".Yii::t('service','Resp. Sales')."</th>";//客户编号及名称
        $html.="<th>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='120px'>".Yii::t('service','Paid Amt')."</th>";//服务金额
        $html.="<th width='80px'>".Yii::t('customer','Contract Period')."</th>";//合同年限(月)
        $html.="<th width='100px'>".Yii::t('summary','all money')."</th>";//合同总金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        $sum = 0;
        $count=0;
        if($rows){
            $html.="<tbody>";
            $city="";
            $cityName = "";
            foreach ($rows as $row){
                $count++;
                if($city!=$row["city"]){
                    $cityName= General::getCityName($row["city"]);
                    $city = $row["city"];
                }
                switch ($row["sql_type_name"]){
                    case "D":
                        $menuStr = Yii::t("app","Customer Service ID");//菜單名稱
                        $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                        break;
                    case "KA":
                        $menuStr = Yii::t("app","Customer Service KA");//菜單名稱
                        $link = self::drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$row['id']));
                        break;
                    default:
                        $menuStr = Yii::t("app","Customer Service");//菜單名稱
                        $link = self::drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$row['id']));
                }
                $companyName = key_exists($row["company_id"],$companyList)?$companyList[$row["company_id"]]["codeAndName"]:$row["company_id"];
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;

                if($row["paid_type"]=="M") {//月金额
                    $row["sum_amount"] = $row["amt_paid"]*$row["ctrt_period"];
                }else{
                    $row["sum_amount"] = $row["amt_paid"];
                }
                $row["sum_amount"]=round($row["sum_amount"],2);
                $sum+=$row["sum_amount"];
                $html.="<tr data-id='{$row["id"]}'>";
                $html.="<td>".$menuStr."</td>";
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                if(!empty($typeStr)){
                    $html.="<td>".General::toDate($row["sign_dt"])."</td>";
                }
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["salesman"]."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            if(!empty($invTable)){
                $html.="<tr><td colspan='10'>&nbsp;</td></tr>";
                $count+=$invTable["count"];
                $sum+=$invTable["sum"];
                $html.="<tr>";
                $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
                $html.="<td colspan='2'>".$count."</td>";
                $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
                $html.="<td colspan='2'>".$sum."</td>";
                $html.="</tr>";
            }
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='10'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return array("html"=>$html,"amt"=>$sum,"count"=>$count);
    }

    public static function getTableForInv($rows,$city_allow){
        $html = "<table class='table table-bordered table-striped table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='100px'>".Yii::t('summary','INV code')."</th>";//产品编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='100px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th width='100px'>".Yii::t('summary','Customer Code')."</th>";//客户编号
        $html.="<th width='100px'>".Yii::t('summary','Customer Type')."</th>";//客户类别
        $html.="<th width='100px'>".Yii::t('summary','INV Amt')."</th>";//产品金额
        $html.="</tr></thead>";
        $sum = 0;
        $count=0;
        if($rows){
            $html.="<tbody>";
            $city="";
            $cityName = "";
            foreach ($rows as $row){
                $count++;
                if($city!=$row["city"]){
                    $cityName= General::getCityName($row["city"]);
                    $city = $row["city"];
                }
                $row["sum_amount"]=round($row["invoice_amt"],2);
                $sum+=$row["sum_amount"];
                $html.="<tr>";
                $html.="<td>".$row["invoice_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["invoice_dt"])."</td>";
                $html.="<td>".$row["customer_code"]."</td>";
                $html.="<td>".$row["customer_type"]."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='2' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td>".$sum."</td>";
            $html.="</tr>";
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='6'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return array("html"=>$html,"count"=>$count,"sum"=>$sum);
    }

    public static function getTableForRowsTwo($rows,$city_allow){
        $companyList = GetNameToId::getCompanyList($city_allow);

        $html = "<table class='table table-bordered table-striped table-condensed table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('summary','menu name')."</th>";//菜單名稱
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='100px'>".Yii::t('service','Resp. Sales')."</th>";//业务员
        $html.="<th width='80px'>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='80px'>".Yii::t('customer','Contract Period')."</th>";//合同年限(月)
        $html.="<th width='100px'>".Yii::t('service','Paid Amt').Yii::t('summary','(Before)')."</th>";//服务金额(更改前)
        $html.="<th width='100px'>".Yii::t('service','Paid Amt').Yii::t('summary','(After)')."</th>";//服务金额(更改後)
        $html.="<th width='100px'>".Yii::t('summary','Difference')."</th>";//變更金额
        $html.="<th width='1px'></th>";
        $html.="</tr></thead>";
        if($rows){
            $sum = 0;
            $count=0;
            $city="";
            $cityName = "";
            $html.="<tbody>";
            foreach ($rows as $row){
                $count++;
                if($city!=$row["city"]){
                    $cityName= General::getCityName($row["city"]);
                    $city = $row["city"];
                }
                switch ($row["sql_type_name"]){
                    case "D":
                        $menuStr = Yii::t("app","Customer Service ID");//菜單名稱
                        $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                        break;
                    case "KA":
                        $menuStr = Yii::t("app","Customer Service KA");//菜單名稱
                        $link = self::drawEditButton('A13', 'serviceKA/edit', 'serviceKA/view', array('index'=>$row['id']));
                        break;
                    default:
                        $menuStr = Yii::t("app","Customer Service");//菜單名稱
                        $link = self::drawEditButton('A02', 'service/edit', 'service/view', array('index'=>$row['id']));
                }
                $companyName = key_exists($row["company_id"],$companyList)?$companyList[$row["company_id"]]["codeAndName"]:$row["company_id"];
                $row["b4_amt_paid"] = is_numeric($row["b4_amt_paid"])?floatval($row["b4_amt_paid"]):0;
                $row["amt_paid"] = is_numeric($row["amt_paid"])?floatval($row["amt_paid"]):0;
                $row["ctrt_period"] = is_numeric($row["ctrt_period"])?floatval($row["ctrt_period"]):0;

                if($row["paid_type"]=="M") {//月金额
                    $row["sum_amount"] = $row["amt_paid"]*$row["ctrt_period"];
                }else{
                    $row["sum_amount"] = $row["amt_paid"];
                }
                if($row["b4_paid_type"]=="M") {//月金额
                    $row["b4_sum_amount"] = $row["b4_amt_paid"]*$row["ctrt_period"];
                }else{
                    $row["b4_sum_amount"] = $row["b4_amt_paid"];
                }
                $row["sum_amount"]=round($row["sum_amount"],2);
                $row["b4_sum_amount"]=round($row["b4_sum_amount"],2);
                $row["sum_amount"]-=$row["b4_sum_amount"];
                $sum+=$row["sum_amount"];
                $html.="<tr data-id='{$row["id"]}'>";
                $html.="<td>".$menuStr."</td>";
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["salesman"]."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td>".$row["b4_amt_paid"]."(".GetNameToId::getPaidTypeForId($row["b4_paid_type"]).") "."</td>";
                $html.="<td>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='11'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return $html;
    }

    //客户服务查询(新增非一次性)
    public static function getServiceRowsForAdd($startDate,$endDate,$city_allow,$sqlExpr=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL.$sqlExpr;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql." and not (a.paid_type=1 and a.ctrt_period<12)")->order("a.city,a.status_dt desc")->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx} and not (a.paid_type=1 and a.ctrt_period<12)")->order("a.city,a.status_dt desc")->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //客户服务查询
    public static function getServiceRows($startDate,$endDate,$city_allow,$type){
        $whereSql = "a.status='{$type}' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx}")->order("a.city,a.status_dt desc")->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //客户服务查询(更改增加)
    public static function getServiceRowsForAD($startDate,$endDate,$city_allow,$sqlExpr=""){
        $whereSql = "a.status='A' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL.$sqlExpr;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where("(case a.paid_type
							when 'M' then IFNULL(a.amt_paid,0) * a.ctrt_period
							else IFNULL(a.amt_paid,0)
						end
					) > (case a.b4_paid_type
							when 'M' then IFNULL(a.b4_amt_paid,0) * a.ctrt_period
							else IFNULL(a.b4_amt_paid,0)
						end
					) and ".$whereSql)->order("a.city,a.status_dt desc")->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where("(IFNULL(a.amt_paid,0)*a.ctrt_period)>a.b4_amt_money and ".$whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where("(case a.paid_type
							when 'M' then IFNULL(a.amt_paid,0) * a.ctrt_period
							else IFNULL(a.amt_paid,0)
						end
					) > (case a.b4_paid_type
							when 'M' then IFNULL(a.b4_amt_paid,0) * a.ctrt_period
							else IFNULL(a.b4_amt_paid,0)
						end
					) and ".$whereSql." and {$kaSqlPrx}")->order("a.city,a.status_dt desc")->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //客户服务查询(暫停、終止)
    public static function getServiceSTForType($startDate,$endDate,$city_allow,$type){
        $whereSql = "a.status='{$type}' and a.status in ('S','T') and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql." and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
        if($queryIARows){
            foreach ($queryIARows as $key=>$row){
                $month_date = date_format(date_create($row['status_dt']),"Y/m");
                if($row['month_date']<=CountSearch::$stop_new_dt){ //2024年12月后改版
                    $next_end_dt=$month_date."/31";//修改下一条查询逻辑
                }else{
                    $next_end_dt=$endDate;//修改下一条查询逻辑
                }
                $nextRow= Yii::app()->db->createCommand()
                    ->select("status")->from("swo_service_contract_no")
                    ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt BETWEEN '{$row['status_dt']}' and '{$next_end_dt}'")
                    ->order("status_dt asc")
                    ->queryRow();//查詢本月的後面一條數據
                if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                    unset($queryIARows[$key]);
                }
            }
        }else{
            $queryIARows = array();
        }

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx} and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
            if($queryKARows){
                foreach ($queryKARows as $key=>$row){
                    $month_date = date_format(date_create($row['status_dt']),"Y/m");
                    if($row['month_date']<=CountSearch::$stop_new_dt){ //2024年12月后改版
                        $next_end_dt=$month_date."/31";//修改下一条查询逻辑
                    }else{
                        $next_end_dt=$endDate;//修改下一条查询逻辑
                    }
                    $nextRow= Yii::app()->db->createCommand()
                        ->select("status")->from("swo_service_ka_no")
                        ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt BETWEEN '{$row['status_dt']}' and '{$next_end_dt}'")
                        ->order("status_dt asc")
                        ->queryRow();//查詢本月的後面一條數據
                    if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                        unset($queryKARows[$key]);
                    }
                }
            }else{
                $queryKARows = array();
            }
            //$queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }


    //客户服务查询(暫停、終止)
    public static function getServiceSTListForType($startDate,$endDate,$city_allow,$type){
        $whereSql = "a.status='{$type}' and a.status in ('S','T') and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.sign_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql." and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
        $returnList = array("goodList"=>array(),"notList"=>array());
        if($queryIARows){
            foreach ($queryIARows as $key=>$row){
                $month_date = date_format(date_create($row['status_dt']),"Y/m");
                if($month_date<=CountSearch::$stop_new_dt){ //2024年12月后改版
                    $next_end_dt=$month_date."/31";//修改下一条查询逻辑
                }else{
                    $next_end_dt=$endDate;//修改下一条查询逻辑
                }
                $nextRow= Yii::app()->db->createCommand()
                    ->select("status")->from("swo_service_contract_no")
                    ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt BETWEEN '{$row['status_dt']}' and '{$next_end_dt}'")
                    ->order("status_dt asc")
                    ->queryRow();//查詢本月的後面一條數據
                if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                    continue;
                }else{
                    $prevRow= Yii::app()->db->createCommand()
                        ->select("status,DATE_FORMAT(status_dt,'%Y/%m') as month_date")->from("swo_service_contract_no")
                        ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and status_dt<='{$row['status_dt']}'")
                        ->order("status_dt desc")
                        ->queryRow();//查詢本月的前面一條數據
                    if($prevRow&&in_array($prevRow["status"],array("S","T"))){
                        if($month_date>CountSearch::$stop_new_dt&&$prevRow['month_date']==$month_date){
                            $returnList["goodList"][] = $row;
                        }else{
                            $returnList["notList"][] = $row;
                        }
                    }else{
                        $returnList["goodList"][] = $row;
                    }
                }
            }
        }

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
            $returnList["goodList"] = array_merge($returnList["goodList"],$queryIDRows);
        }

        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx} and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
            if($queryKARows){
                foreach ($queryKARows as $key=>$row){
                    $month_date = date_format(date_create($row['status_dt']),"Y/m");
                    if($month_date<=CountSearch::$stop_new_dt){ //2024年12月后改版
                        $next_end_dt=$month_date."/31";//修改下一条查询逻辑
                    }else{
                        $next_end_dt=$endDate;//修改下一条查询逻辑
                    }
                    $nextRow= Yii::app()->db->createCommand()
                        ->select("status")->from("swo_service_ka_no")
                        ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt BETWEEN '{$row['status_dt']}' and '{$next_end_dt}'")
                        ->order("status_dt asc")
                        ->queryRow();//查詢本月的後面一條數據
                    if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                        continue;
                    }else{
                        $prevRow= Yii::app()->db->createCommand()
                            ->select("status,DATE_FORMAT(status_dt,'%Y/%m') as month_date")->from("swo_service_ka_no")
                            ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and status_dt<='{$row['status_dt']}'")
                            ->order("status_dt desc")
                            ->queryRow();//查詢本月的前面一條數據
                        if($prevRow&&in_array($prevRow["status"],array("S","T"))){
                            if($month_date>CountSearch::$stop_new_dt&&$prevRow['month_date']==$month_date){
                                $returnList["goodList"][] = $row;
                            }else{
                                $returnList["notList"][] = $row;
                            }
                        }else{
                            $returnList["goodList"][] = $row;
                        }
                    }
                }
            }
        }
        return $returnList;
    }

    //暂停超过2个月的服务
    public static function getServicePauseForTwoMonth($date,$city_allow){
        $startDate = date("Y/m/d",strtotime($date." - 4 months"));//由于数据量过大所以只查4个月内
        $endDate = date("Y/m/d",strtotime($date." - 2 months"));
        $whereSql = "a.status='S' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql." and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
        if($queryIARows){
            foreach ($queryIARows as $key=>$row){
                $month_date = date("Y/m",strtotime($row['status_dt']));
                $nextRow= Yii::app()->db->createCommand()
                    ->select("status")->from("swo_service_contract_no")
                    ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt>'{$row['status_dt']}' and 
                        DATE_FORMAT(status_dt,'%Y/%m')='{$month_date}'")
                    ->order("status_dt asc")
                    ->queryRow();//查詢本月的後面一條數據
                if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                    unset($queryIARows[$key]);
                }
            }
        }else{
            $queryIARows = array();
        }

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.id as no_id,n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx} and n.id is not null")->order("a.city,a.status_dt desc")->queryAll();
            if($queryKARows){
                foreach ($queryKARows as $key=>$row){
                    $month_date = date("Y/m",strtotime($row['status_dt']));
                    $nextRow= Yii::app()->db->createCommand()
                        ->select("status")->from("swo_service_ka_no")
                        ->where("contract_no='{$row["contract_no"]}' and 
                        id!='{$row["no_id"]}' and 
                        status_dt>'{$row['status_dt']}' and 
                        DATE_FORMAT(status_dt,'%Y/%m')='{$month_date}'")
                        ->order("status_dt asc")
                        ->queryRow();//查詢本月的後面一條數據
                    if($nextRow&&in_array($nextRow["status"],array("S","T"))){
                        unset($queryKARows[$key]);
                    }
                }
            }else{
                $queryKARows = array();
            }
            //$queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //一次性查询
    public static function getOneServiceRows($startDay,$endDay,$city_allow="",$sqlExpr=""){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= self::$whereSQL.$sqlExpr;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql." and a.paid_type=1 and a.ctrt_period<12")->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();
        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx} and a.paid_type=1 and a.ctrt_period<12")
                ->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return $queryIARows;
    }

    //长约、短约查询
    public static function getServiceForMonthType($startDay,$endDay,$city_allow="",$type="long"){
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        if($type=="long"){
            $whereSqlIA= " and a.ctrt_period>=12";
            $whereSqlID= " and a.ctrt_period>=12";
        }else{
            $whereSqlIA= " and a.ctrt_period<12 and a.paid_type!=1";
            $whereSqlID= " and a.ctrt_period<12";
        }
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql.$whereSqlIA)->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql.$whereSqlID)->order("a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }

        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql.$whereSqlIA." and {$kaSqlPrx}")->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //餐饮、非餐饮查询
    public static function getServiceForCate($startDay,$endDay,$city_allow="",$type="cate"){
        //cate==A01
        $whereSql = "a.status='N' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        if($type=="cate"){ //餐饮
            $whereSql.= " and g.rpt_cat='A01' ";
        }else{
            $whereSql.= " and (g.rpt_cat!='A01' or g.rpt_cat is null) ";
        }
        $whereSql .= self::$whereSQL;
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        if(self::$IDBool){
            $queryIDRows = Yii::app()->db->createCommand()
                ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
                ->from("swo_serviceid a")
                ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
            $queryIDRows = $queryIDRows?$queryIDRows:array();
        }else{
            $queryIDRows=array();
        }

        if(self::$KABool){
            $kaSqlPrx = CountSearch::getServiceKASQL("a.");
            $queryKARows = Yii::app()->db->createCommand()
                ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('KA') as sql_type_name")
                ->from("swo_service_ka a")
                ->leftJoin("swo_service_ka_no n","a.id=n.service_id")
                ->leftJoin("swo_customer_type f","a.cust_type=f.id")
                ->leftJoin("swo_nature g","a.nature_type=g.id")
                ->where($whereSql." and {$kaSqlPrx}")->queryAll();
            $queryKARows = $queryKARows?$queryKARows:array();
            $queryIARows = array_merge($queryIARows,$queryKARows);
        }
        return array_merge($queryIARows,$queryIDRows);
    }

    //U系统的产品（台湾专用）
    public static function getUInvTWList($startDay,$endDay,$city_allow=""){
        //cate==A01
        $whereSql = "a.status='N' and f.rpt_cat='INV' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->queryAll();
        return $queryIARows?$queryIARows:array();
    }

    //U系统的产品
    public static function getUInvList($startDay,$endDay,$city_allow=""){
        if(self::$system==0){//2024年1月29日年大陆版使用了新的U系统
            return SearchForCurlU::getCurlInvDetail($startDay,$endDay,$city_allow);
        }
        if(self::$system===1){//台灣版的產品為lbs的inv新增
            return self::getUInvTWList($startDay,$endDay,$city_allow);
        }
        $list = array();
        $json = Invoice::getInvData($startDay,$endDay,$city_allow);
        if($json["message"]==="Success"){
            $list = $json["data"];
        }
        return $list;
    }

    //U系统的产品(餐饮、非餐饮)（台湾专用）
    public static function getUInvTWListForType($startDay,$endDay,$city_allow="",$type=""){
        //cate==A01
        $whereSql = "a.status='N' and f.rpt_cat='INV' and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        if($type=="cate"){ //餐饮
            $whereSql.= " and g.rpt_cat='A01' ";
        }else{
            $whereSql.= " and (g.rpt_cat!='A01' or g.rpt_cat is null) ";
        }
        $selectSql = "a.id,a.status,a.status_dt,a.salesman,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->queryAll();
        return $queryIARows?$queryIARows:array();
    }

    //U系统的产品
    public static function getUInvListForType($startDay,$endDay,$city_allow="",$type="not"){
        if(self::$system===1){//台灣版的產品為lbs的inv新增
            return self::getUInvTWListForType($startDay,$endDay,$city_allow,$type);
        }
        $list = array();
        $Catering = self::$system===2?"Catering":"餐饮类";
        if(self::$system==0){//2024年1月29日年大陆版使用了新的U系统
            $json = SystemU::getInvDataDetail($startDay,$endDay,$city_allow);
        }else{
            $json = Invoice::getInvData($startDay,$endDay,$city_allow);
        }
        //$json = Invoice::getInvData($startDay,$endDay,$city_allow);
        if($json["message"]==="Success"){
            foreach ($json["data"] as $row){
                if($type==="cate"&&$row["customer_type"]===$Catering){
                    $list[]=$row;
                }
                if ($type==="not"&&$row["customer_type"]!==$Catering){
                    $list[]=$row;
                }
            }
        }
        return $list;
    }
}