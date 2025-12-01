<?php
class RptComplaint extends ReportData2 {
	public function fields() {
		return array(
            'city_name'=>array('label'=>Yii::t('app','City'),'width'=>12,'align'=>'C'),
			'entry_dt'=>array('label'=>Yii::t('followup','Date'),'width'=>18,'align'=>'C'),
			'type'=>array('label'=>Yii::t('followup','Type'),'width'=>10,'align'=>'C'),
			'pest_type_name'=>array('label'=>Yii::t('followup','Pest Type'),'width'=>12,'align'=>'L'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>22,'align'=>'L'),
			'address'=>array('label'=>Yii::t('customer','Address'),'width'=>40,'align'=>'L'),
			'content'=>array('label'=>Yii::t('followup','job content'),'width'=>20,'align'=>'L'),
			'job_report'=>array('label'=>Yii::t('followup','job report'),'width'=>20,'align'=>'L'),
			'cont_info'=>array('label'=>Yii::t('followup','Contact'),'width'=>23,'align'=>'L'),
			'resp_staff'=>array('label'=>Yii::t('followup','Resp. Sales'),'width'=>15,'align'=>'L'),
			'resp_tech'=>array('label'=>Yii::t('followup','Technician'),'width'=>15,'align'=>'L'),
			'mgr_notify'=>array('label'=>Yii::t('followup','Notify Manager'),'width'=>8,'align'=>'C'),
			'sch_dt'=>array('label'=>Yii::t('followup','Schedule Date'),'width'=>18,'align'=>'C'),
			'follow_staff'=>array('label'=>Yii::t('followup','Follow-up Tech.'),'width'=>15,'align'=>'L'),
			'leader'=>array('label'=>Yii::t('followup','Leader or above'),'width'=>8,'align'=>'C'),
			'follow_tech'=>array('label'=>Yii::t('followup','Previous Follow-up Tech.'),'width'=>15,'align'=>'L'),
			'fin_dt'=>array('label'=>Yii::t('followup','Finish Date'),'width'=>18,'align'=>'C'),
			'follow_action'=>array('label'=>Yii::t('followup','Follow-up Action'),'width'=>15,'align'=>'L'),
			'mgr_talk'=>array('label'=>Yii::t('followup','Update with Tech.'),'width'=>8,'align'=>'C'),
			'changex'=>array('label'=>Yii::t('followup','Change Follow-up Tech.'),'width'=>15,'align'=>'L'),
			'tech_notify'=>array('label'=>Yii::t('followup','Staff of Change Arrangement.'),'width'=>15,'align'=>'L'),
			'fp_fin_dt'=>array('label'=>Yii::t('followup','Finish Follow Up Date'),'width'=>18,'align'=>'C'),
			'fp_call_dt'=>array('label'=>Yii::t('followup','Follow up Date'),'width'=>18,'align'=>'C'),
			'fp_cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>15,'align'=>'L'),
			'fp_comment'=>array('label'=>Yii::t('followup','Comment'),'width'=>15,'align'=>'L'),
			'svc_next_dt'=>array('label'=>Yii::t('followup','Next Service Date'),'width'=>18,'align'=>'C'),
			'svc_call_dt'=>array('label'=>Yii::t('followup','Follow up Date'),'width'=>18,'align'=>'C'),
			'svc_cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>15,'align'=>'L'),
			'svc_comment'=>array('label'=>Yii::t('followup','Comment')."1",'width'=>15,'align'=>'L'),
			'mcard_remarks'=>array('label'=>Yii::t('followup','Contend of Update to Job Card'),'width'=>10,'align'=>'L'),
			'mcard_staff'=>array('label'=>Yii::t('followup','Staff of Update Job Card'),'width'=>10,'align'=>'L'),
			'date_diff'=>array('label'=>Yii::t('followup','Date Diff.'),'width'=>10,'align'=>'L'),
			'cust_sfn'=>array('label'=>Yii::t('followup','cust sfn'),'width'=>10,'align'=>'L'),
			'cust_vfn'=>array('label'=>Yii::t('followup','cust vfn'),'width'=>10,'align'=>'L'),
		);
	}

	public function header_structure() {
		return array(
			'city_name',
			'entry_dt',
			'type',
			'pest_type_name',
			'company_name',
			'address',
			'content',
			'job_report',
			'cont_info',
			'resp_staff',
			'resp_tech',
			'mgr_notify',
			'sch_dt',
			'follow_staff',
			'leader',
			'follow_tech',
			'fin_dt',
			'follow_action',
			'mgr_talk',
			'changex',
			'tech_notify',
			array(
				'label'=>Yii::t('followup','Call Back'),
				'child'=>array(
					array(
						'label'=>Yii::t('followup','Follow Up After Complaint'),
						'child'=>array(
							'fp_fin_dt',
							'fp_call_dt',
							'fp_cust_name',
							'fp_comment',
						),
					),
					array(
						'label'=>Yii::t('followup','Follow Up After Service'),
						'child'=>array(
							'svc_next_dt',
							'svc_call_dt',
							'svc_cust_name',
							'svc_comment',
						),
					),
				),
			),
			'mcard_remarks',
			'mcard_staff',
			'date_diff',
			'cust_sfn',
			'cust_vfn',

		);
	}
	
//	public function subsections() {
//		return array(
//			array(
//				'call_type'=>array('label'=>Yii::t('followup','Call Type'),'width'=>30,'align'=>'L'),
//				'call_dt'=>array('label'=>Yii::t('followup','Call Date'),'width'=>30,'align'=>'L'),
//				'cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>30,'align'=>'L'),
//				'comment'=>array('label'=>Yii::t('followup','Comment'),'width'=>30,'align'=>'L'),
//				'call_fin_dt'=>array('label'=>Yii::t('followup','Finish Date'),'width'=>30,'align'=>'L'),
//				'next_svc_dt'=>array('label'=>Yii::t('followup','Next Service Date'),'width'=>30,'align'=>'L'),
//			),
//		);
//	}
	
	public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }
		$sql = "select a.*
					from swo_followup a 
		";
		//$where = "where a.city='".$city."'";
		$where = "where a.city in ({$city_allow})";
		if (isset($this->criteria)) {
			if (isset($this->criteria->start_dt))
				$where .= (($where=='where') ? " " : " and ")."a.entry_dt>='".General::toDate($this->criteria->start_dt)." 00:00:00'";
			if (isset($this->criteria->end_dt))
				$where .= (($where=='where') ? " " : " and ")."a.entry_dt<='".General::toDate($this->criteria->end_dt)." 23:59:59'";
		}
		if ($where!='where') $sql .= $where;	
		$sql .= " order by a.city,a.entry_dt";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
                $temp['city_name'] = General::getCityName($row["city"]);
				$temp['entry_dt'] = General::toDate($row['entry_dt']);
				$temp['type'] = $row['type'];
				$temp['pest_type_name'] = $row['pest_type_name'];
                $companyRow = self::getCompanyRowForId($row["company_id"]);
				$temp['company_name'] = empty($companyRow)?$row['company_name']:$companyRow["code"].$companyRow["name"];
				$temp['address'] = empty($companyRow)?"":$companyRow["address"];

				$temp['content'] = $row['content'];
				$temp['job_report'] = $row['job_report'];
				$temp['cont_info'] = $row['cont_info'];
				$temp['resp_staff'] = $row['resp_staff'];
				$temp['resp_tech'] = $row['resp_tech'];
				$temp['mgr_notify'] = ($row['mgr_notify']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['sch_dt'] = General::toDate($row['sch_dt']);
				$temp['follow_staff'] = $row['follow_staff'];
				$temp['leader'] = ($row['leader']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['follow_tech'] = $row['follow_tech'];
				$temp['fin_dt'] = General::toDate($row['fin_dt']);
				$temp['follow_action'] = $row['follow_action'];
				$temp['mgr_talk'] = ($row['mgr_talk']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['changex'] = $row['changex'];
				$temp['tech_notify'] = $row['tech_notify'];
				$temp['fp_fin_dt'] = General::toDate($row['fp_fin_dt']);
				$temp['fp_call_dt'] = General::toDate($row['fp_call_dt']);
				$temp['fp_cust_name'] = $row['fp_cust_name'];
				$temp['fp_comment'] = $row['fp_comment'];
				$temp['svc_next_dt'] = General::toDate($row['svc_next_dt']);
				$temp['svc_call_dt'] = General::toDate($row['svc_call_dt']);
				$temp['svc_cust_name'] = $row['svc_cust_name'];
				$temp['svc_comment'] = $row['svc_comment'];
				$temp['mcard_remarks'] = $row['mcard_remarks'];
				$temp['mcard_staff'] = $row['mcard_staff'];
				$temp['cust_sfn'] = FollowupList::getCustSfnStrByKey($row['cust_sfn']);
				$temp['cust_vfn'] = $row['cust_vfn'];
				$temp['date_diff'] = (empty($temp['fp_call_dt']) || empty($temp['entry_dt'])) ? '' : (strtotime($temp['fp_call_dt'])-strtotime($temp['entry_dt']))/86400;
				$this->data[] = $temp;
			}
		}
		return true;
	}
	
	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityNameForList($this->criteria->city) : '';
		return parent::getReportName();
	}
    //获取客户类别1名字
    public static function getCompanyRowForId($company_id){
        $row = Yii::app()->db->createCommand()->select("*")->from("swo_company")
            ->where("id=:id",array(":id"=>$company_id))->queryRow();
        return $row?$row:array();
    }
}
?>