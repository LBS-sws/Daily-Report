<?php
class RptKaRetentionMonth extends RptKaRetention {
    public $month;

    public function __construct($month){
        $this->month = is_numeric($month)?intval($month):1;
    }

    public function fields() {
        return array(
            'id'=>array('label'=>"LBS系统ID",'width'=>12,'align'=>'C'),
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
            'office_name'=>array('label'=>"归属",'width'=>12,'align'=>'C'),
            'table_class'=>array('label'=>"菜单名称",'width'=>12,'align'=>'C'),
            'lcd'=>array('label'=>"输入日期",'width'=>18,'align'=>'C'),
            'company_code'=>array('label'=>"客户编号",'width'=>12,'align'=>'C'),
            'company_code_pre'=>array('label'=>"客户编号(含尾缀)",'width'=>12,'align'=>'C'),
            'company_name'=>array('label'=>"客户名称",'width'=>30,'align'=>'C'),
            'cust_type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>12,'align'=>'C'),
            'nature_type'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
            'service'=>array('label'=>Yii::t('service','Service'),'width'=>30,'align'=>'L'),
            'reason'=>array('label'=>"变动原因",'width'=>30,'align'=>'L'),
            'tracking'=>array('label'=>"跟踪因素详情",'width'=>30,'align'=>'L'),
            'month_amt'=>array('label'=>"月金额",'width'=>12,'align'=>'C'),
            'year_amt'=>array('label'=>"年金额",'width'=>12,'align'=>'C'),
            'all_number'=>array('label'=>"服务总次数",'width'=>18,'align'=>'C'),
            'surplus'=>array('label'=>"剩余次数",'width'=>12,'align'=>'C'),
            'salesman'=>array('label'=>"业务员",'width'=>20,'align'=>'C'),
            'othersalesman'=>array('label'=>"被跨区业务员",'width'=>20,'align'=>'C'),
            'technician'=>array('label'=>"负责技术员",'width'=>20,'align'=>'C'),
            'status_dt'=>array('label'=>"终止日期",'width'=>20,'align'=>'C'),
            'sign_dt'=>array('label'=>"签约日期",'width'=>12,'align'=>'C'),
            'ctrt_period'=>array('label'=>"合同年限(月)",'width'=>12,'align'=>'C'),
            'ctrt_end_dt'=>array('label'=>"合同终止日期",'width'=>12,'align'=>'C'),
            'cont_info'=>array('label'=>"客户联系/电话",'width'=>20,'align'=>'C'),
            'first_dt'=>array('label'=>"首次日期",'width'=>12,'align'=>'C'),
            'first_tech'=>array('label'=>"首次技术员",'width'=>20,'align'=>'C'),
            'remarks2'=>array('label'=>"备注",'width'=>30,'align'=>'C'),
        );
    }

    public function retrieveData() {
//		$city = Yii::app()->user->city();
        $this->chain_num = json_decode($this->criteria->chain_num,true);
        $sales_sql_str = implode(",",$this->chain_num);

        $startDate = date("Y/m/01",strtotime("{$this->criteria->year}/{$this->month}/01"));
        $endDate = date("Y/m/t",strtotime("{$this->criteria->year}/{$this->month}/01"));
        $whereSql = "a.status='T' and a.status_dt BETWEEN '{$startDate}' and '{$endDate}'";
        $whereSql.= " and a.salesman_id in ({$sales_sql_str}) and (a.reason!='【系统自动触发】:合同已到期' or a.reason is null)";
        $whereSql.= CountSearch::$whereSQL;

        $rows = Yii::app()->db->createCommand()
            ->select("a.*,f.description,CONCAT('IA') as table_class")
            ->from("swo_service a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)
            ->order("a.city,a.salesman_id")
            ->queryAll();
        $rows = $rows?$rows:array();
        $IDRows = Yii::app()->db->createCommand()
            ->select("a.*,f.description,CONCAT('ID') as table_class")
            ->from("swo_serviceid a")
            ->leftJoin("swo_customer_type_id f","a.cust_type=f.id")
            ->where($whereSql)
            ->order("a.city,a.salesman_id")
            ->queryAll();//
        $IDRows = $IDRows?$IDRows:array();
        $rows = array_merge($rows,$IDRows);
        $KARows = Yii::app()->db->createCommand()
            ->select("a.*,f.description,CONCAT('KA') as table_class")
            ->from("swo_service_ka a")
            ->leftJoin("swo_customer_type f","a.cust_type=f.id")
            ->where($whereSql)
            ->order("a.city,a.salesman_id")
            ->queryAll();
        $KARows = $KARows?$KARows:array();
        $rows = array_merge($rows,$KARows);

        $officeList =array();
        $companyList =array();
        $cityList =array();
        $natureOneList =array();
        $natureTwoList =array();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $temp = array();
                if(!key_exists($row["office_id"],$officeList)){
                    $officeList[$row["office_id"]] = GetNameToId::getOfficeNameForID($row['office_id']);
                }
                $temp["office_name"] = $officeList[$row["office_id"]];

                if(!key_exists($row["city"],$cityList)){
                    $cityList[$row["city"]] = self::getCityNameForCity($row["city"]);
                }
                $temp["city_name"] = $cityList[$row["city"]];

                if(!key_exists($row["nature_type"],$natureOneList)){
                    $natureOneList[$row["nature_type"]] = GetNameToId::getNatureOneNameForId($row["nature_type"]);
                }
                $temp["nature_type"] = $natureOneList[$row["nature_type"]];

                if(!key_exists($row["nature_type_two"],$natureTwoList)){
                    $natureTwoList[$row["nature_type_two"]] = GetNameToId::getNatureOneNameForId($row["nature_type_two"]);
                }
                $temp["nature_two"] = $natureTwoList[$row["nature_type_two"]];

                if(!key_exists($row["company_id"],$companyList)){
                    $companyList[$row["company_id"]] = self::getCompanyListForID($row["company_id"]);
                }
                $temp["company_code"] = $companyList[$row["company_id"]]["code"];
                $temp["company_code_pre"] = $companyList[$row["company_id"]]["code"]."-".$row["city"];
                $temp["company_name"] = $companyList[$row["company_id"]]["name"];
                switch ($row["table_class"]){
                    case "IA":
                        $temp["table_class"]="客户服务";
                        break;
                    case "ID":
                        $temp["table_class"]="ID客户服务";
                        break;
                    default:
                        $temp["table_class"]="KA客户服务";

                }

                $temp['id'] = $row["id"];
                $temp["cust_type"] = $row["description"];
                $temp["lcd"] = empty($row["lcd"])?"":General::toMyDate($row["lcd"]);
                $temp["status_dt"] = empty($row["status_dt"])?"":General::toMyDate($row["status_dt"]);
                $temp["sign_dt"] = empty($row["sign_dt"])?"":General::toMyDate($row["sign_dt"]);
                $temp["ctrt_end_dt"] = empty($row["ctrt_end_dt"])?"":General::toMyDate($row["ctrt_end_dt"]);
                $temp["first_dt"] = empty($row["first_dt"])?"":General::toMyDate($row["first_dt"]);
                $temp["service"] = $row["service"];
                $temp["prepay_month"] = $row["prepay_month"];
                $temp["amt_install"] = $row["amt_install"];
                $temp["salesman"] = $row["salesman"];
                $temp["othersalesman"] = $row["othersalesman"];
                $temp["technician"] = $row["technician"];
                $temp["all_number"] = $row["all_number"];
                $temp["surplus"] = $row["surplus"];
                $temp["reason"] = $row["reason"];
                $temp["tracking"] = $row["tracking"];
                $temp["ctrt_period"] = empty($row["ctrt_period"])?0:floatval($row["ctrt_period"]);
                $temp["cont_info"] = $row["cont_info"];
                $temp["first_tech"] = $row["first_tech"];
                $temp["remarks2"] = $row["remarks2"];
                $temp["need_install"] = GetNameToId::getNeedInstallForId($row["need_install"]);

                if($row["paid_type"]=="M"){
                    $month_amt = empty($row["amt_paid"])?0:floatval($row["amt_paid"]);
                    $year_amt = $month_amt*$temp["ctrt_period"];
                }else{
                    $year_amt = empty($row["amt_paid"])?0:floatval($row["amt_paid"]);
                    $month_amt = empty($temp["ctrt_period"])?0:($year_amt/$temp["ctrt_period"]);
                    $month_amt = round($month_amt,2);
                }

                $temp["month_amt"] = $month_amt;
                $temp["year_amt"] = $year_amt;
                $this->data[] = $temp;
            }
        }
        return true;
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityNameForList($this->criteria->city) : '';
        return parent::getReportName();
    }
}
?>