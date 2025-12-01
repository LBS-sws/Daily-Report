<?php
class RptKaRetention extends ReportData2 {
    public $chain_num;//KA销售
    public function fields() {
        return array(
            'id'=>array('label'=>"LBS系统ID",'width'=>12,'align'=>'C'),
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
            'office_name'=>array('label'=>"归属",'width'=>12,'align'=>'C'),
            'table_class'=>array('label'=>"菜单名称",'width'=>12,'align'=>'C'),
            'status_dt'=>array('label'=>"新增日期",'width'=>18,'align'=>'C'),
            'company_code'=>array('label'=>"客户编号",'width'=>12,'align'=>'C'),
            'company_code_pre'=>array('label'=>"客户编号(含尾缀)",'width'=>12,'align'=>'C'),
            'company_name'=>array('label'=>"客户名称",'width'=>30,'align'=>'C'),
            'cust_type'=>array('label'=>Yii::t('service','Customer Type'),'width'=>12,'align'=>'C'),
            'nature_type'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
            'nature_two'=>array('label'=>"二级栏位",'width'=>12,'align'=>'C'),
            'service'=>array('label'=>Yii::t('service','Service'),'width'=>30,'align'=>'L'),
            'prepay_month'=>array('label'=>"预付月数",'width'=>12,'align'=>'C'),
            'month_amt'=>array('label'=>"月金额",'width'=>12,'align'=>'C'),
            'year_amt'=>array('label'=>"年金额",'width'=>12,'align'=>'C'),
            'amt_install'=>array('label'=>"装机金额",'width'=>12,'align'=>'C'),
            'need_install'=>array('label'=>"需安装",'width'=>12,'align'=>'C'),
            'salesman'=>array('label'=>"业务员",'width'=>20,'align'=>'C'),
            'othersalesman'=>array('label'=>"被跨区业务员",'width'=>20,'align'=>'C'),
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

        $endDate = date("Y/m/t",strtotime("{$this->criteria->year}/{$this->criteria->month}/01"));
        $sql = "
            select b.*,'IA' as table_class from swo_service_contract_no a
            JOIN (
              select contract_no,max(id) as max_id from swo_service_contract_no WHERE status_dt<='{$endDate}' GROUP BY contract_no
            ) f ON a.id=f.max_id and a.contract_no=f.contract_no 
            LEFT JOIN swo_service b ON a.service_id=b.id
            WHERE a.status!='T' AND f.max_id is NOT null AND !(b.paid_type=1 AND b.ctrt_period<12) AND b.salesman_id in ({$sales_sql_str}) ORDER BY b.city,b.status_dt,b.salesman_id
        ";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $rows = $rows?$rows:array();
        $kaSql = "
            select b.*,'KA' as table_class from swo_service_ka_no a
            JOIN (
              select contract_no,max(id) as max_id from swo_service_ka_no WHERE status_dt<='{$endDate}' GROUP BY contract_no
            ) f ON a.id=f.max_id and a.contract_no=f.contract_no 
            LEFT JOIN swo_service_ka b ON a.service_id=b.id
            WHERE a.status!='T' AND f.max_id is NOT null AND !(b.paid_type=1 AND b.ctrt_period<12) AND b.salesman_id in ({$sales_sql_str}) ORDER BY b.city,b.status_dt,b.salesman_id
        ";
        $kaRow = Yii::app()->db->createCommand($kaSql)->queryAll();
        $kaRow = $kaRow?$kaRow:array();
        $rows = array_merge($rows,$kaRow);

        $officeList =array();
        $companyList =array();
        $cityList =array();
        $custTypeList =array();
        $natureOneList =array();
        $natureTwoList =array();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row["id"];
                if(!key_exists($row["office_id"],$officeList)){
                    $officeList[$row["office_id"]] = GetNameToId::getOfficeNameForID($row['office_id']);
                }
                $temp["office_name"] = $officeList[$row["office_id"]];

                if(!key_exists($row["city"],$cityList)){
                    $cityList[$row["city"]] = self::getCityNameForCity($row["city"]);
                }
                $temp["city_name"] = $cityList[$row["city"]];

                if(!key_exists($row["cust_type"],$custTypeList)){
                    $custTypeList[$row["cust_type"]] = GetNameToId::getCustOneNameForId($row["cust_type"]);
                }
                $temp["cust_type"] = $custTypeList[$row["cust_type"]];

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
                    default:
                        $temp["table_class"]="KA客户服务";

                }
                $temp["status_dt"] = empty($row["status_dt"])?"":General::toMyDate($row["status_dt"]);
                $temp["sign_dt"] = empty($row["sign_dt"])?"":General::toMyDate($row["sign_dt"]);
                $temp["ctrt_end_dt"] = empty($row["ctrt_end_dt"])?"":General::toMyDate($row["ctrt_end_dt"]);
                $temp["first_dt"] = empty($row["first_dt"])?"":General::toMyDate($row["first_dt"]);
                $temp["service"] = $row["service"];
                $temp["prepay_month"] = $row["prepay_month"];
                $temp["amt_install"] = $row["amt_install"];
                $temp["salesman"] = $row["salesman"];
                $temp["othersalesman"] = $row["othersalesman"];
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
    //获取城市
    public static function getCityNameForCity($city){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("security{$suffix}.sec_city")
            ->where("code=:code",array(":code"=>$city))->queryRow();
        return $row?$row["name"]:$city;
    }
    //获取公司资料
    public static function getCompanyListForID($id){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("code,name")
            ->from("swoper{$suffix}.swo_company")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        return $row?$row:array("code"=>$id,"name"=>$id);
    }
}
?>