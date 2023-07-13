<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/7/12 0012
 * Time: 15:52
 */
class SummaryTable extends SummaryForm{
    public $city_allow;

    //顯示表格內的數據來源
    public function ajaxDetailForHtml(){
        $city = key_exists("city",$_GET)?$_GET["city"]:0;
        $this->city_allow = CitySetForm::getCityAllowForCity($city);
        $this->start_date = key_exists("startDate",$_GET)?$_GET["startDate"]:"";
        $this->end_date = key_exists("endDate",$_GET)?$_GET["endDate"]:"";
        $type = key_exists("type",$_GET)?$_GET["type"]:"";

        return $this->$type();
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
        $start_date = date("Y/m/d",strtotime($this->start_date." - 1 month"));
        $end_date = date("Y/m/d",strtotime($this->end_date." - 1 month"));
        $rows = self::getOneServiceRows($start_date,$end_date,$this->city_allow);
        return self::getTableForRows($rows,$this->city_allow);
    }

    //新增服务
    private function ServiceNew(){
        $rows = self::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"N");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //更改服务
    private function ServiceAmendment(){
        $rows = self::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"A");
        return self::getTableForRowsTwo($rows,$this->city_allow);
    }

    //暂停服务
    private function ServiceSuspended(){
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$this->start_date,$this->end_date,"S");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //恢复服务
    private function ServiceRenewal(){
        $rows = self::getServiceRows($this->start_date,$this->end_date,$this->city_allow,"R");
        return self::getTableForRows($rows,$this->city_allow);
    }

    //终止服务
    private function ServiceStop(){
        $rows = RptSummarySC::getSRTRowsAll($this->city_allow,$this->start_date,$this->end_date,"T");
        return self::getTableForRows($rows,$this->city_allow);
    }

    public static function getTableForRows($rows,$city_allow){
        $companyList = GetNameToId::getCompanyList($city_allow);

        $html = "<table class='table table-bordered table-striped table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
        $html.="<th width='80px'>".Yii::t('service','Customer Type')."</th>";//客户类别
        $html.="<th width='120px'>".Yii::t('service','Paid Amt')."</th>";//服务金额
        $html.="<th width='80px'>".Yii::t('customer','Contract Period')."</th>";//合同年限(月)
        $html.="<th width='100px'>".Yii::t('service','all money')."</th>";//合同总金额
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
                if($row["sql_type_name"]=="D"){//ID服务
                    $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                }else{
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
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td class='text-right'>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td class='text-right'>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='2' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='3' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='9'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return $html;
    }

    public static function getTableForRowsTwo($rows,$city_allow){
        $companyList = GetNameToId::getCompanyList($city_allow);

        $html = "<table class='table table-bordered table-striped table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='90px'>".Yii::t('service','Contract No')."</th>";//合同编号
        $html.="<th width='90px'>".Yii::t('summary','City')."</th>";//城市
        $html.="<th width='90px'>".Yii::t('summary','search day')."</th>";//日期
        $html.="<th>".Yii::t('service','Customer')."</th>";//客户编号及名称
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
                if($row["sql_type_name"]=="D"){//ID服务
                    $link = self::drawEditButton('A11', 'serviceID/edit', 'serviceID/view', array('index'=>$row['id']));
                }else{
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
                $html.="<td>".$row["contract_no"]."</td>";
                $html.="<td>".$cityName."</td>";
                $html.="<td>".General::toDate($row["status_dt"])."</td>";
                $html.="<td>".$companyName."</td>";
                $html.="<td>".$row["cust_type_name"]."</td>";
                $html.="<td>".$row["ctrt_period"]."</td>";
                $html.="<td class='text-right'>".$row["b4_amt_paid"]."(".GetNameToId::getPaidTypeForId($row["b4_paid_type"]).") "."</td>";
                $html.="<td class='text-right'>".$row["amt_paid"]."(".GetNameToId::getPaidTypeForId($row["paid_type"]).") "."</td>";
                $html.="<td class='text-right'>".$row["sum_amount"]."</td>";
                $html.="<td>{$link}</td>";
                $html.="</tr>";
            }
            $html.="</tbody><tfoot>";
            $html.="<tr>";
            $html.="<td colspan='2' class='text-right'>".Yii::t("summary","total count:")."</td>";
            $html.="<td colspan='2'>".$count."</td>";
            $html.="<td colspan='4' class='text-right'>".Yii::t("summary","total amt:")."</td>";
            $html.="<td colspan='2'>".$sum."</td>";
            $html.="</tr>";
            $html.="</tfoot>";
        }else{
            $html.="<tbody><tr><td colspan='10'>".Yii::t("summary","none data")."</td></tr></tbody>";
        }
        $html.="</table>";
        return $html;
    }

    //客户服务查询
    public function getServiceRows($startDate,$endDate,$city_allow,$type){
        $whereSql = "a.status='{$type}' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.city in ({$city_allow})";
        $whereSql .= " and not(f.rpt_cat='INV' and f.single=1)";
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        $queryIDRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swo_serviceid a")
            ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
        $queryIDRows = $queryIDRows?$queryIDRows:array();
        return array_merge($queryIARows,$queryIDRows);
    }

    //一次性查询
    public function getOneServiceRows($startDay,$endDay,$city_allow=""){
        $whereSql = "a.status='N' and a.paid_type=1 and a.status_dt BETWEEN '{$startDay}' and '{$endDay}'";
        if(!empty($city_allow)){
            $whereSql.= " and a.city in ({$city_allow})";
        }
        $whereSql .= " and not(f.rpt_cat='INV' and f.single=1)";
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $rows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->queryAll();
        return $rows;
    }

    //长约、短约查询
    public function getServiceForMonthType($startDay,$endDay,$city_allow="",$type="long"){
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
        $whereSql .= " and not(f.rpt_cat='INV' and f.single=1)";
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql.$whereSqlIA)->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();

        $queryIDRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swo_serviceid a")
            ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql.$whereSqlID)->order("a.status_dt desc")->queryAll();
        $queryIDRows = $queryIDRows?$queryIDRows:array();
        return array_merge($queryIARows,$queryIDRows);
    }

    //餐饮、非餐饮查询
    public function getServiceForCate($startDay,$endDay,$city_allow="",$type="cate"){
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
        $whereSql .= " and not(f.rpt_cat='INV' and f.single=1)";
        $selectSql = "a.id,a.status,a.status_dt,a.company_id,f.rpt_cat,a.city,g.rpt_cat as nature_rpt_cat,a.nature_type,a.amt_paid,a.ctrt_period,a.b4_amt_paid,
            f.description as cust_type_name";
        $queryIARows = Yii::app()->db->createCommand()
            ->select("{$selectSql},n.contract_no,a.paid_type,a.b4_paid_type,CONCAT('A') as sql_type_name")
            ->from("swo_service a")
            ->leftJoin("swo_service_contract_no n","a.id=n.service_id")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->queryAll();
        $queryIARows = $queryIARows?$queryIARows:array();
        $queryIDRows = Yii::app()->db->createCommand()
            ->select("{$selectSql},CONCAT('ID服务') as contract_no,CONCAT('M') as paid_type,CONCAT('M') as b4_paid_type,CONCAT('D') as sql_type_name")
            ->from("swo_serviceid a")
            ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
            ->leftJoin("swo_nature g","a.nature_type=g.id")
            ->where($whereSql)->order("a.city,a.status_dt desc")->queryAll();
        $queryIDRows = $queryIDRows?$queryIDRows:array();
        return array_merge($queryIARows,$queryIDRows);
    }
}