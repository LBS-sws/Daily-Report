<?php
class RptChain extends ReportData2{
    public $company_status;//null：全部 A:生效中 T:停止服务 U:不明
    public $chain_num=20;//連鎖店數量

	public function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>18,'align'=>'L'),
			'company_code'=>array('label'=>Yii::t('customer','Customer Code'),'width'=>18,'align'=>'L'),
			'company_name'=>array('label'=>Yii::t('customer','Customer Name'),'width'=>30,'align'=>'L'),
			'company_status'=>array('label'=>Yii::t('customer','Status'),'width'=>18,'align'=>'L'),
			'group_id'=>array('label'=>Yii::t('customer','Group ID'),'width'=>18,'align'=>'L'),
			'group_name'=>array('label'=>Yii::t('customer','Group Name'),'width'=>18,'align'=>'L'),
			'status_dt'=>array('label'=>Yii::t('customer','Date'),'width'=>20,'align'=>'L'),
			'service_status'=>array('label'=>Yii::t('customer','Status'),'width'=>18,'align'=>'L'),
			'cust_type_desc'=>array('label'=>Yii::t('customer','Type'),'width'=>18,'align'=>'L'),
			'product_desc'=>array('label'=>Yii::t('customer','Product'),'width'=>18,'align'=>'L'),
			'ctrt_period'=>array('label'=>Yii::t('customer','Contract Period'),'width'=>20,'align'=>'R'),
			'm_amt_paid'=>array('label'=>Yii::t('service','Monthly'),'width'=>20,'align'=>'R'),
			'y_amt_paid'=>array('label'=>Yii::t('service','Yearly'),'width'=>20,'align'=>'R'),
		);
	}
    // Abstract: Define report detail with line structure
    public function report_structure() {
        return array("city_name","company_code","company_name","company_status","group_id","group_name",
            array("status_dt","service_status","cust_type_desc","product_desc","ctrt_period","m_amt_paid","y_amt_paid")
        );
    }
	
	public function retrieveData() {
        $city = $this->criteria->city;
        $this->chain_num = $this->criteria->chain_num;
        $this->company_status = $this->criteria->company_status;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $chainSQL = Yii::app()->db->createCommand()->select("group_id,group_name,count(id) as chain_num")
            ->from("swo_company")
            ->where("city in ({$city_allow}) and not(group_id='' and group_name='')")
            ->group("group_id,group_name")
            ->getText();
        $whereSql ="";
        if(!empty($this->company_status)){
            $whereSql.=" and f.status='$this->company_status'";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.code,a.name,a.city,a.group_id,a.group_name,f.status,g.name as city_name")
            ->from("swo_company a")
            ->leftJoin("({$chainSQL}) b","a.group_id = b.group_id and a.group_name = b.group_name")
            ->leftJoin("swo_company_status f","a.id = f.id")
            ->leftJoin("security$suffix.sec_city g","a.city = g.code")
            ->where("a.city in ({$city_allow}) and b.chain_num>=:chain_num {$whereSql}",array(":chain_num"=>$this->chain_num))
            ->order("a.name")
            ->queryAll();
        $data=array();
        if($rows){
            foreach ($rows as $row){
                $temp=array();
                $temp["city_name"]=$row["city_name"];
                $temp["company_code"]=$row["code"];
                $temp["company_name"]=$row["name"];
                $temp["group_id"]=$row["group_id"];
                $temp["group_name"]=$row["group_name"];
                $temp["company_status"]=self::getCompanyStatus($row["status"],true);
                $temp["detail"]=array();
                $infoRows = Yii::app()->db->createCommand()
                    ->select("a.status_dt,a.status,a.amt_paid,a.paid_type,a.ctrt_period,
                    c.description as cust_type_desc, d.description as product_desc")
                    ->from("swo_service a")
                    ->leftJoin("swo_service b","a.company_id=b.company_id and a.status_dt < b.status_dt and a.cust_type=b.cust_type")
                    ->leftJoin("swo_customer_type c","a.cust_type=c.id")
                    ->leftJoin("swo_product d","a.product_id=d.id")
                    ->where("b.id is null and a.city=:city and a.company_id=:id",
                        array(":city"=>$row["city"],":id"=>$row["id"])
                    )->queryAll();
                if($infoRows){
                    foreach ($infoRows as $infoRow){
                        $info=array();
                        $ctrt_period = empty($infoRow["ctrt_period"])?0:floatval($infoRow["ctrt_period"]);
                        $m_amt_paid = floatval($infoRow["amt_paid"]);
                        $y_amt_paid = $m_amt_paid;
                        if($infoRow["paid_type"]=="M"){//月金额
                            $y_amt_paid*=$ctrt_period;
                        }else{
                            $m_amt_paid = empty($ctrt_period)?0:round($m_amt_paid/$ctrt_period,2);
                        }
                        $info["status_dt"]=$infoRow["status_dt"];
                        $info["service_status"]=self::getServiceStatus($infoRow["status"],true);
                        $info["cust_type_desc"]=$infoRow["cust_type_desc"];
                        $info["product_desc"]=$infoRow["product_desc"];
                        $info["m_amt_paid"]=$m_amt_paid;
                        $info["y_amt_paid"]=$y_amt_paid;
                        $info["ctrt_period"]=$ctrt_period;
                        $temp["detail"][]=$info;
                    }
                }
                $data[] = $temp;
            }
        }
        $this->data = $data;
		return true;
	}

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName();
	}

    public static function getCompanyStatus($key='',$bool=false){
        $list = array(
            'A'=>Yii::t('customer','Active'),//服務中
            'T'=>Yii::t('customer','Terminated'),//停止
            'U'=>Yii::t('customer','Unknown')//不明
        );
        if($bool){
            if (key_exists($key,$list)){
                return $list[$key];
            }else{
                return $list["U"];
            }
        }
        return $list;
    }

    public static function getServiceStatus($key='',$bool=false){
        $list = array(
            'A'=>Yii::t('customer','Active'),//服務中
            'T'=>Yii::t('customer','Terminated'),//停止
            'U'=>Yii::t('customer','Unknown')//不明
        );
        if($bool){
            if (key_exists($key,$list)){
                return $list[$key];
            }else{
                return $list["U"];
            }
        }
        return $list;
    }
}
?>
