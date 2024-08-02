<?php
class RptCross extends ReportData2{
    public $company_status;//

	public function fields() {
		return array(
			'old_city'=>array('label'=>Yii::t('service','City'),'width'=>18,'align'=>'L'),
			'table_type'=>array('label'=>Yii::t('summary','menu name'),'width'=>18,'align'=>'L'),
            'contract_no'=>array('label'=>Yii::t('service','Contract No'),'width'=>18,'align'=>'L'),
            'company_code'=>array('label'=>Yii::t('service','Customer code'),'width'=>18,'align'=>'L'),
            'company_name'=>array('label'=>Yii::t('service','Customer name'),'width'=>25,'align'=>'L'),
            'apply_date'=>array('label'=>Yii::t('service','Apply date'),'width'=>18,'align'=>'L'),
            'month_amt'=>array('label'=>Yii::t('service','Monthly'),'width'=>18,'align'=>'L'),
            'cross_type'=>array('label'=>Yii::t('service','Cross type'),'width'=>18,'align'=>'L'),
            'qualification_city'=>array('label'=>Yii::t('service','Qualification city'),'width'=>18,'align'=>'L'),
            'qualification_ratio'=>array('label'=>Yii::t('service','Qualification ratio'),'width'=>18,'align'=>'L'),
            'qualification_amt'=>array('label'=>Yii::t('service','Qualification Amt'),'width'=>18,'align'=>'L'),
            'cross_city'=>array('label'=>Yii::t('service','Cross city'),'width'=>18,'align'=>'L'),
            'rate_num'=>array('label'=>Yii::t('service','accept rate'),'width'=>18,'align'=>'L'),
            'cross_amt'=>array('label'=>Yii::t('service','accept amt'),'width'=>18,'align'=>'L'),
            'status_type'=>array('label'=>Yii::t('service','status type'),'width'=>18,'align'=>'L'),
		);
	}

    public function getSelectString() {
        $rtn = parent::getSelectString();
        if (isset($this->criteria)) {
            if ($this->fieldExist('company_status')&&!empty($this->criteria->company_status)) {
                $rtn.= empty($rtn)?"":" ；";
                $rtn.= Yii::t('service','status type').': ';
                $status = $this->criteria->company_status;
                if(!General::isJSON($status)){
                    $rtn.= CrossApplyList::getStatusStrForStatusType(array("status_type"=>$status,"cross_num"=>0));
                }else{
                    $statusList = json_decode($status,true);
                    foreach ($statusList as $item=>$value){
                        $rtn.= empty($item)?"":"、";
                        $rtn.= CrossApplyList::getStatusStrForStatusType(array("status_type"=>$value,"cross_num"=>0));
                    }
                }
            }
        }
        return $rtn;
    }
	
	public function retrieveData() {
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql ="";
        if (isset($this->criteria)) {
            if (isset($this->criteria->start_dt))
                $whereSql .= " and "."a.apply_date>='".General::toDate($this->criteria->start_dt)."'";
            if (isset($this->criteria->end_dt))
                $whereSql .= " and "."a.apply_date<='".General::toDate($this->criteria->end_dt)."'";
            if (isset($this->criteria->company_status)){
                $status = $this->criteria->company_status;
                if(!General::isJSON($status)){
                    $statusSql = strpos($status,"'")!==false?$status:"'{$status}'";
                }else{
                    $statusSql = json_decode($status,true);
                    $statusSql = "'".implode("','",$statusSql)."'";
                }
                $whereSql .= " and a.status_type in ({$statusSql})";
            }
        }
        $rows = Yii::app()->db->createCommand()->select("a.*")
            ->from("swo_cross a")
            ->where("a.id>0 {$whereSql}")
            ->order("a.apply_date desc,a.id desc")
            ->queryAll();
        $data=array();
        if($rows){
            $cityList = array();
            foreach ($rows as $row){
                $serviceList =CrossApplyForm::getServiceList($row["table_type"],$row["service_id"]);
                $temp=array();
                $temp["old_city"]=$this->getCityNameForList($cityList,$row["old_city"]);
                $temp["table_type"]=CrossApplyForm::getCrossTableTypeNameForKey($row["table_type"]);
                $temp["contract_no"]=empty($serviceList)?"":$serviceList["contract_no"];
                $temp["company_code"]=empty($serviceList)?"":$serviceList["company_code"];
                $temp["company_name"]=empty($serviceList)?"":$serviceList["company_name"];
                $temp["apply_date"]=General::toDate($row["apply_date"]);
                $temp["month_amt"]=floatval($row["month_amt"]);
                $temp["cross_type"]=CrossApplyForm::getCrossTypeStrToKey($row["cross_type"]);
                $temp["qualification_city"]=$this->getCityNameForList($cityList,$row["qualification_city"]);
                $temp["qualification_ratio"]=$row['qualification_ratio']===null?"":floatval($row['qualification_ratio'])."%";
                $temp["qualification_amt"]=floatval($row['qualification_amt']);
                $temp["cross_city"]=$this->getCityNameForList($cityList,$row["cross_city"]);
                $temp["rate_num"]=$row['rate_num']===null?"":floatval($row['rate_num'])."%";
                $temp["cross_amt"]=floatval($row['cross_amt']);
                $temp["status_type"]=CrossApplyList::getStatusStrForStatusType($row);

                $data[]=$temp;
            }
        }
        $this->data = $data;
		return true;
	}

	protected function getCityNameForList(&$cityList,$cityCode){
	    if(key_exists($cityCode,$cityList)){
	        return $cityList[$cityCode];
        }else{
	        $cityName = General::getCityName($cityCode);
	        $cityList[$cityCode] = $cityName;
	        return $cityName;
        }
    }

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName();
	}
}
?>
