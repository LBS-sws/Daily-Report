<?php
class RptContractCom extends ReportData2{

    private $last_year=2022;
    private $now_year=2023;
    private $start_month=1;
    private $end_month=8;

	public function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>18,'align'=>'L'),
			'company_code'=>array('label'=>Yii::t('customer','Customer Code'),'width'=>18,'align'=>'L'),
			'company_name'=>array('label'=>Yii::t('customer','Customer Name'),'width'=>18,'align'=>'L'),
            'last_year_num'=>array('label'=>Yii::t('summary','contract number'),'width'=>18,'align'=>'L'),
            'last_year_money'=>array('label'=>Yii::t('summary','contract money'),'width'=>18,'align'=>'L'),
            'last_year_service'=>array('label'=>Yii::t('summary','contract service'),'width'=>18,'align'=>'L'),
            'now_year_num'=>array('label'=>Yii::t('summary','contract number'),'width'=>18,'align'=>'L'),
            'now_year_money'=>array('label'=>Yii::t('summary','contract money'),'width'=>18,'align'=>'L'),
            'now_year_service'=>array('label'=>Yii::t('summary','contract service'),'width'=>18,'align'=>'L'),
        );
	}

    public function header_structure() {
        return array(
            'city_name',
            'company_code',
            'company_name',
            array(
                'label'=>$this->last_year."年{$this->start_month} - {$this->end_month}月",
                'child'=>array(
                    'last_year_num',
                    'last_year_money',
                    'last_year_service'
                ),
            ),
            array(
                'label'=>$this->now_year."年{$this->start_month} - {$this->end_month}月",
                'child'=>array(
                    'now_year_num',
                    'now_year_money',
                    'now_year_service'
                ),
            ),
        );
    }

    private function getLastServiceRows($company_id){
        $lastStartDt = $this->last_year."/".$this->start_month."/01";
        $lastEndDt = $this->last_year."/".$this->end_month."/31";
        $sum_money = "case b.paid_type when 'M' then b.amt_paid * b.ctrt_period else b.amt_paid end";
        $lastRows = Yii::app()->db->createCommand()
            ->select("a.contract_no,b.status_dt,b.service,($sum_money) as sum_money")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","a.service_id = b.id")
            ->leftJoin("swo_customer_type g","b.cust_type = g.id")
            ->where("b.status='N' and b.company_id='{$company_id}' and b.status_dt BETWEEN '{$lastStartDt}' AND '{$lastEndDt}' and b.paid_type in ('M','Y') and g.rpt_cat in ('IA','IB')")
            ->queryAll();
        return $lastRows?$lastRows:array();
    }

	private function getServiceNCount($start_dt,$end_dt){
        $sum_money = "case b.paid_type when 'M' then b.amt_paid * b.ctrt_period else b.amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("b.company_id,b.city,GROUP_CONCAT(b.service) as group_service,count(b.id) as sum_num,sum({$sum_money}) as sum_money")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","a.service_id = b.id")
            ->leftJoin("swo_customer_type g","b.cust_type = g.id")
            ->where("b.status='N' and b.status_dt BETWEEN '{$start_dt}' AND '{$end_dt}' and b.paid_type in ('M','Y') and g.rpt_cat in ('IA','IB')")
            ->group("b.company_id,b.city")
            ->order("b.city")
            ->queryAll();
        return $rows?$rows:array();
    }

	private function getServiceARows($start_dt,$end_dt){
        $sum_money = "case b.paid_type when 'M' then b.amt_paid * b.ctrt_period else b.amt_paid end";
        $b4_sum_money = "case b.b4_paid_type when 'M' then b.b4_amt_paid * b.ctrt_period else b.b4_amt_paid end";
        $rows = Yii::app()->db->createCommand()
            ->select("a.contract_no,b.company_id,b.status_dt,b.city,b.service,({$sum_money}) as sum_money,({$b4_sum_money}) as b4_sum_money")
            ->from("swo_service_contract_no a")
            ->leftJoin("swo_service b","a.service_id = b.id")
            ->leftJoin("swo_customer_type g","b.cust_type = g.id")
            ->where("b.status='A' and b.status_dt BETWEEN '{$start_dt}' AND '{$end_dt}' and b.paid_type in ('M','Y') and g.rpt_cat in ('IA','IB')")
            ->order("b.city")
            ->queryAll();
        return $rows?$rows:array();
    }
	
	public function retrieveData() {
        $city = $this->criteria->city;
        $start_dt = $this->criteria->start_dt;
        $end_dt = $this->criteria->end_dt;
        $suffix = Yii::app()->params['envSuffix'];
        $this->now_year = date("Y",strtotime($end_dt));
        $this->start_month = date("m",strtotime($start_dt));
        $this->end_month = date("m",strtotime($end_dt));
        $lastStartDt = $this->last_year."/".$this->start_month."/01";
        $lastEndDt = $this->last_year."/".$this->end_month."/31";
        $data=array();
        //本年新增
        $serviceNowAdd = $this->getServiceNCount($start_dt,$end_dt);
        if(!empty($serviceNowAdd)){
            $nowCity='';
            $nowCityName='';
            foreach ($serviceNowAdd as $row){
                if ($nowCity!==$row["city"]){
                    $nowCity = $row["city"];
                    $nowCityName = General::getCityName($nowCity);
                }
                $company_id = $row["company_id"];
                if(!key_exists($company_id,$data)){
                    $data[$company_id] = $this->getTempData($company_id,$nowCityName);
                }
                $data[$company_id]["now_year_num"]+=$row["sum_num"];
                $data[$company_id]["now_year_money"]+=$row["sum_money"];
                $data[$company_id]["now_year_service"].=empty($data[$company_id]["now_year_service"])?"":",";
                $data[$company_id]["now_year_service"].=$row["group_service"];
            }
        }
        //本年更改
        $serviceNowUpdate = $this->getServiceARows($start_dt,$end_dt);
        if(!empty($serviceNowUpdate)){
            foreach ($serviceNowUpdate as $row){
                $company_id = $row["company_id"];
                if(!key_exists($company_id,$data)){
                    $nowCityName = General::getCityName($row["city"]);
                    $data[$company_id] = $this->getTempData($company_id,$nowCityName);
                }
                $lastRow = Yii::app()->db->createCommand()->select("b.id,b.status_dt")
                    ->from("swo_service_contract_no a")
                    ->leftJoin("swo_service b","a.service_id = b.id")
                    ->where("b.status='N' and b.status_dt<=:dt and a.contract_no=:no",
                        array(":no"=>$row["contract_no"],":dt"=>$row["status_dt"])
                    )->order("b.status_dt desc")->queryRow();
                $serviceDt = date("Y/m/d",strtotime($lastRow["status_dt"]));
                $bool = $serviceDt>=$lastStartDt&&$serviceDt<=$lastEndDt;
                $bool = $bool||($serviceDt>=$start_dt&&$serviceDt<=$end_dt);
                if($bool){ //如果更改前的新增在本年或者上一年内，则不需要增加合约次数
                    $data[$company_id]["now_year_money"]+=$row["sum_money"]-$row["b4_sum_money"];
                }else{
                    $data[$company_id]["now_year_num"]++;
                    $data[$company_id]["now_year_money"]+=$row["sum_money"];
                    $data[$company_id]["now_year_service"].=empty($data[$company_id]["now_year_service"])?"":",";
                    $data[$company_id]["now_year_service"].=$row["service"];
                }
            }
        }

        //不需要显示上一年为零的客户
        if(!empty($data)){
            foreach ($data as $key=>$item){
                if(empty($item["last_year_num"])){
                    unset($data[$key]);
                }
            }
        }
        $this->data = $data;
		return true;
	}

	private function getTempData($company_id,$city_name){
        $end_dt = $this->criteria->end_dt;
        $row = Yii::app()->db->createCommand()->select("code,name")->from("swo_company")
            ->where("id=:id",array(":id"=>$company_id))->queryRow();
        $row = $row?$row:array("code"=>'',"name"=>'');

        $last_year_num=0;
        $last_year_money=0;
        $last_year_service="";
        $now_year_num=0;
        $now_year_money=0;
        $now_year_service="";
        $lastRows = $this->getLastServiceRows($company_id);
        if($lastRows){
            foreach ($lastRows as $service){
                $stopRow = Yii::app()->db->createCommand()->select("b.id,b.status")
                    ->from("swo_service_contract_no a")
                    ->leftJoin("swo_service b","a.service_id = b.id")
                    ->where("b.status_dt>=:dt and b.status_dt<='{$end_dt}' and a.contract_no=:no",
                        array(":no"=>$service["contract_no"],":dt"=>$service["status_dt"])
                    )->order("b.status_dt desc")->queryRow();
                $last_year_num++;
                $last_year_money+=$service["sum_money"];
                $last_year_service=empty($last_year_service)?"":",";
                $last_year_service.=$service["service"];
                if($stopRow&&$stopRow["status"]!="T"){ //如果最后一条不是终止
                    $now_year_num++;
                    $now_year_money+=$service["sum_money"];
                    $now_year_service=empty($now_year_service)?"":",";
                    $now_year_service.=$service["service"];
                }else{
                    continue;
                }
            }
        }

	    return array(
            'city_name'=>$city_name,
            'company_id'=>$company_id,
            'company_code'=>$row["code"],
            'company_name'=>$row["name"],
            'last_year_num'=>$last_year_num,
            'last_year_money'=>$last_year_money,
            'last_year_service'=>$last_year_service,
            'now_year_num'=>$now_year_num,
            'now_year_money'=>$now_year_money,
            'now_year_service'=>$now_year_service,
        );
    }

	public function getReportName() {
		//$city_name = isset($this->criteria) ? $this->criteria->start_dt." ~ ".$this->criteria->end_dt : '';
		return parent::getReportName();
	}
}
?>
