<?php
class RptKaSigned extends ReportData2{
    public $staff_list;//

    public function fields() {
        return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
            'status_dt'=>array('label'=>Yii::t('service','New Date'),'width'=>18,'align'=>'C'),
            'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>40,'align'=>'L'),
            'cust_type'=>array('label'=>Yii::t('customer','Customer Type'),'width'=>12,'align'=>'L'),
            'nature'=>array('label'=>Yii::t('customer','Nature'),'width'=>12,'align'=>'L'),
            'nature_two_name'=>array('label'=>Yii::t('service','Secondary field'),'width'=>12,'align'=>'L'),
            'service'=>array('label'=>Yii::t('service','Service'),'width'=>40,'align'=>'L'),
            'prepay_month'=>array('label'=>Yii::t('service','Prepay Month'),'width'=>12,'align'=>'R'),
            'amt_month'=>array('label'=>Yii::t('service','Monthly'),'width'=>15,'align'=>'C'),
            'amt_year'=>array('label'=>Yii::t('service','Yearly'),'width'=>15,'align'=>'C'),
            'amt_install'=>array('label'=>Yii::t('service','Installation Fee'),'width'=>15,'align'=>'C'),
            'need_install'=>array('label'=>Yii::t('service','Installation'),'width'=>10,'align'=>'C'),
            'salesman'=>array('label'=>Yii::t('service','Resp. Sales'),'width'=>20,'align'=>'L'),
            'othersalesman'=>array('label'=>Yii::t('service','OtherSalesman'),'width'=>20,'align'=>'L'),
            'sign_dt'=>array('label'=>Yii::t('service','Sign Date'),'width'=>18,'align'=>'C'),
            'ctrt_period'=>array('label'=>Yii::t('service','Contract Period'),'width'=>10,'align'=>'C'),
            'ctrt_end_dt'=>array('label'=>Yii::t('service','Contract End Date'),'width'=>18,'align'=>'C'),
            'cont_info'=>array('label'=>Yii::t('service','Contact'),'width'=>40,'align'=>'L'),
            'first_dt'=>array('label'=>Yii::t('service','First Service Date'),'width'=>18,'align'=>'C'),
            'first_tech'=>array('label'=>Yii::t('service','First Service Tech.'),'width'=>30,'align'=>'L'),
            'remarks'=>array('label'=>Yii::t('service','Remarks'),'width'=>40,'align'=>'L'),
            'equip_install_dt'=>array('label'=>Yii::t('service','Installation Date'),'width'=>18,'align'=>'C'),
            'diff_ctrt_dt'=>array('label'=>Yii::t('service','Diff. btw Contract Date'),'width'=>15,'align'=>'C'),
            'diff_first_dt'=>array('label'=>Yii::t('service','Diff. btw First Service Date'),'width'=>15,'align'=>'C'),
        );
    }

    public function getSelectString() {
        $rtn = parent::getSelectString();
        if (isset($this->criteria)) {
            if ($this->fieldExist('staff_list')&&!empty($this->criteria->staff_list)) {
                $rtn.= empty($rtn)?"":" ；\n";
                $rtn.= Yii::t('summary','Staff Name').': ';
                $staffList = json_decode($this->criteria->staff_list,true);
                $rtn.= self::getStaffNameForList($staffList);
            }
        }
        return $rtn;
    }

    public function retrieveData() {
        $suffix = Yii::app()->params['envSuffix'];
        $whereSql =" and a.status='N'";
        if (isset($this->criteria)) {
            if (isset($this->criteria->start_dt))
                $whereSql .= " and "."a.status_dt>='".General::toDate($this->criteria->start_dt)."'";
            if (isset($this->criteria->end_dt))
                $whereSql .= " and "."a.status_dt<='".General::toDate($this->criteria->end_dt)."'";
            if (isset($this->criteria->staff_list)){
                $staffList = json_decode($this->criteria->staff_list,true);
                $idSql = "'".implode("','",$staffList)."'";
                $whereSql .= " and a.salesman_id in ({$idSql})";
            }
        }
        $IARows = Yii::app()->db->createCommand()->select("a.*,f.name as nature_two_name, b.description as nature, c.description as customer_type_name")
            ->from("swo_service a")
            ->where("a.id>0 {$whereSql}")
            ->leftJoin("swo_nature b","a.nature_type=b.id")
            ->leftJoin("swo_nature_type f","a.nature_type_two=f.id")
            ->leftJoin("swo_customer_type c","a.cust_type=c.id")
            ->order("a.salesman_id desc,a.status_dt desc")
            ->queryAll();
        $IARows = $IARows?$IARows:array();
        $KARows = Yii::app()->db->createCommand()->select("a.*,f.name as nature_two_name, b.description as nature, c.description as customer_type_name")
            ->from("swo_service_ka a")
            ->where("a.id>0 {$whereSql}")
            ->leftJoin("swo_nature b","a.nature_type=b.id")
            ->leftJoin("swo_nature_type f","a.nature_type_two=f.id")
            ->leftJoin("swo_customer_type c","a.cust_type=c.id")
            ->order("a.salesman_id desc,a.status_dt desc")
            ->queryAll();
        $KARows = $KARows?$KARows:array();
        $IDRows = Yii::app()->db->createCommand()->select("a.*,CONCAT('M') as paid_type,CONCAT('') as nature_two_name, b.description as nature, c.description as customer_type_name")
            ->from("swo_serviceid a")
            ->where("a.id>0 {$whereSql}")
            ->leftJoin("swo_nature b","a.nature_type=b.id")
            ->leftJoin("swo_customer_type_id c","a.cust_type=c.id")
            ->order("a.salesman_id desc,a.status_dt desc")
            ->queryAll();
        $IDRows = $IDRows?$IDRows:array();
        $rows = array_merge($IARows,$KARows,$IDRows);
        $data=array();
        if(!empty($rows)){
            $cityList = array();
            $staffList = array();
            foreach ($rows as $row){
                if(!key_exists($row["city"],$cityList)){
                    $cityList[$row["city"]] = General::getCityName($row["city"]);
                }
                $temp = array();
                $temp['city_name'] = $cityList[$row["city"]];
                $temp['cust_type'] = $row['customer_type_name'];
                $temp['status_dt'] = General::toDate($row['status_dt']);
                $temp['company_name'] = $row['company_name'];
                $temp['nature'] = $row['nature'];
                $temp['nature'] = $row['nature'];
                $temp['service'] = $row['service'];
                $temp['amt_month'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
                    ($row['paid_type']=='M'?$row['amt_paid']:round($row['amt_paid']/($row['ctrt_period']>0?$row['ctrt_period']:1),2)))
                    ,2,'.','');
                $period = empty($row['ctrt_period'])?0:($row['ctrt_period']<12?$row['ctrt_period']:12);
                $temp['amt_year'] = number_format(($row['paid_type']=='1'?$row['amt_paid']:
                    ($row['paid_type']=='M'?$row['amt_paid']*$period:$row['amt_paid']))
                    ,2,'.','');
                $temp['amt_install'] = number_format($row['amt_install'],2,'.','');
                $temp['need_install'] = ($row['need_install']=='Y') ? Yii::t('misc','Yes') : Yii::t('misc','No');
                $temp['salesman'] = $row['salesman'];
                $temp['othersalesman'] = $row['othersalesman'];
                $temp['nature_two_name'] = $row['nature_two_name'];
                $temp['prepay_month'] = $row['prepay_month'];
                $temp['sign_dt'] = General::toDate($row['sign_dt']);
                $temp['ctrt_period'] = $row['ctrt_period'];
                $temp['ctrt_end_dt'] = General::toDate($row['ctrt_end_dt']);
                $temp['cont_info'] = $row['cont_info'];
                $temp['first_dt'] = General::toDate($row['first_dt']);
                $temp['first_tech'] = $row['first_tech'];
                $temp['remarks'] = $row['remarks2'];
                $temp['equip_install_dt'] = General::toDate($row['equip_install_dt']);
                $temp['diff_ctrt_dt'] = (empty($temp['equip_install_dt']) || empty($temp['sign_dt'])) ? '' :
                    (strtotime($row['equip_install_dt'])-strtotime($row['sign_dt']))/86400;
                $temp['diff_first_dt'] = (empty($temp['sign_dt']) || empty($temp['first_dt'])) ? '' :
                    (strtotime($temp['first_dt'])-strtotime($temp['sign_dt']))/86400;

                $data[]=$temp;
            }
        }
        $this->data = $data;
        return true;
    }

    protected function getStaffNameForList($staffJson){
        $suffix = Yii::app()->params['envSuffix'];
        $idSql = "'".implode("','",$staffJson)."'";
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("hr$suffix.hr_employee")
            ->where("id in ({$idSql})")
            ->queryAll();
        if($rows){
            $list = array();
            foreach ($rows as $row){
                $list[]=$row["name"]." ({$row["code"]})";
            }
            return implode(";",$list);
        }else{
            return $idSql;
        }
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
        return parent::getReportName();
    }
}
?>
