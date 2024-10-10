<?php

class FollowupForm extends CFormModel
{
	public $id = 0;
	public $entry_dt;
	public $type = '';
	public $company_id;
	public $company_name;
	public $content;
	public $job_report;
	public $cont_info;
	public $resp_staff;
	public $resp_tech;
	public $mgr_notify;
	public $sch_dt;
	public $follow_staff;
	public $leader = 'N';
	public $follow_tech;
	public $fin_dt;
	public $follow_action;
	public $mgr_talk;
	public $change;
	public $tech_notify;
	public $fp_fin_dt;
	public $fp_call_dt;
	public $fp_cust_name;
	public $fp_comment;
	public $svc_next_dt;
	public $svc_call_dt;
	public $svc_cust_name;
	public $svc_comment;
	public $mcard_remarks;
	public $mcard_staff;
	public $pest_type_id;
	public $pest_type_name;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('followup','Record ID'),
			'entry_dt'=>Yii::t('followup','Date').' '.Yii::t('misc','(Y/M/D)'),
			'type'=>Yii::t('followup','Type'),
			'company_id'=>Yii::t('followup','Customer'),
			'company_name'=>Yii::t('followup','Customer'),
			'content'=>Yii::t('followup','job content'),
			'job_report'=>Yii::t('followup','job report'),
			'cont_info'=>Yii::t('followup','Contact'),
			'resp_staff'=>Yii::t('followup','Resp. Sales'),
			'resp_tech'=>Yii::t('followup','Technician'),
			'mgr_notify'=>Yii::t('followup','Notify Manager'),
			'sch_dt'=>Yii::t('followup','Schedule Date').' '.Yii::t('misc','(Y/M/D)'),
			'follow_staff'=>Yii::t('followup','Follow-up Tech.'),
			'leader'=>Yii::t('followup','Leader or above'),
			'follow_tech'=>Yii::t('followup','Previous Follow-up Tech.'),
			'fin_dt'=>Yii::t('followup','Finish Date').' '.Yii::t('misc','(Y/M/D)'),
			'follow_action'=>Yii::t('followup','Follow-up Action'),
			'mgr_talk'=>Yii::t('followup','Update with Tech.'),
			'change'=>Yii::t('followup','Change Follow-up Tech.'),
			'tech_notify'=>Yii::t('followup','Staff of Change Arrangement.'),
			'svc_next_dt'=>Yii::t('followup','Next Service Date'),
			'svc_fin_dt'=>Yii::t('followup','Finish Date').' '.Yii::t('misc','(Y/M/D)'),
			'svc_call_dt'=>Yii::t('followup','Follow up Date').' '.Yii::t('misc','(Y/M/D)'),
			'svc_cust_name'=>Yii::t('followup','Customer Name'),
			'svc_comment'=>Yii::t('followup','Comment')."1",
			'fp_fin_dt'=>Yii::t('followup','Finish Date').' '.Yii::t('misc','(Y/M/D)'),
			'fp_call_dt'=>Yii::t('followup','Follow up Date').' '.Yii::t('misc','(Y/M/D)'),
			'fp_cust_name'=>Yii::t('followup','Customer Name'),
			'fp_comment'=>Yii::t('followup','Comment'),
			'mcard_remarks'=>Yii::t('followup','Contend of Update to Job Card'),
			'mcard_staff'=>Yii::t('followup','Staff of Update Job Card'),
			'pest_type_id'=>Yii::t('followup','Pest Type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, resp_staff, pest_type_id, pest_type_name, resp_tech,job_report, mgr_notify, follow_staff, follow_tech, follow_action,
				mgr_talk, change, tech_notify, cont_info, type, mcard_remarks, mcard_staff,
				fp_cust_name, fp_comment, svc_comment, svc_cust_name, company_id, leader
				','safe'),
			array('company_name, company_id, content, entry_dt','required'),
			array('entry_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('sch_dt, fin_dt, fp_fin_dt, fp_call_dt, svc_next_dt, svc_call_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
            array('pest_type_id','validatePestType'),
		);
	}

    public function validatePestType($attribute, $params) {
        $type = $this->type;
        $this->pest_type_name = null;
        $typeList = self::getServiceTypeListEx();
        if(key_exists($type,$typeList["options"])){
            $rpt = $typeList["options"][$type]["data-rpt"];
            //只有IB能选择害虫类型
            $this->pest_type_id = $rpt=="IB"?$this->pest_type_id:null;
            $id_str = $this->pest_type_id;
            if(!empty($id_str)){
                $this->pest_type_name = array();
                $list = PestTypeForm::getPestTypeList($id_str);
                foreach ($id_str as $id){
                    if(key_exists($id,$list)){
                        $this->pest_type_name[] = $list[$id];
                    }
                }
            }
        }else{
            $this->addError($attribute, "服务类型不存在，请刷新重试");
        }
    }

	public function retrieveData($index)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN01') ? "" : "and a.lcu='$user'";
		$city = Yii::app()->user->city_allow();
		$sql = "select a.*,concat(f.code,f.name) as company_name_str from swo_followup a
		LEFT JOIN swo_company f ON f.id=a.company_id 
        where a.id=$index and a.city in ($city) $allcond";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->entry_dt = General::toDate($row['entry_dt']);
				$this->company_id = empty($row['company_id'])?"":$row['company_id'];
				$this->company_name = empty($row['company_name_str'])?$row['company_name']:$row['company_name_str'];
				$this->type = $row['type'];
				$this->content = $row['content'];
				$this->job_report = $row['job_report'];
				$this->cont_info = $row['cont_info'];
				$this->resp_staff = $row['resp_staff'];
				$this->resp_tech = $row['resp_tech'];
				$this->mgr_notify = $row['mgr_notify'];
				$this->sch_dt = General::toDate($row['sch_dt']);
				$this->follow_staff = $row['follow_staff'];
				$this->leader = $row['leader'];
				$this->follow_tech = $row['follow_tech'];
				$this->fin_dt = General::toDate($row['fin_dt']);
				$this->follow_action = $row['follow_action'];
				$this->mgr_talk = $row['mgr_talk'];
				$this->change = $row['changex'];
				$this->tech_notify = $row['tech_notify'];
				$this->fp_fin_dt =  General::toDate($row['fp_fin_dt']);
				$this->fp_call_dt =  General::toDate($row['fp_call_dt']);
				$this->fp_cust_name = $row['fp_cust_name'];
				$this->fp_comment = $row['fp_comment'];
				$this->svc_next_dt =  General::toDate($row['svc_next_dt']);
				$this->svc_call_dt =  General::toDate($row['svc_call_dt']);
				$this->svc_cust_name = $row['svc_cust_name'];
				$this->svc_comment = $row['svc_comment'];
				$this->mcard_remarks = $row['mcard_remarks'];
				$this->mcard_staff = $row['mcard_staff'];
				$this->pest_type_id = empty($row['pest_type_id'])?null:explode(",",$row['pest_type_id']);
				$this->pest_type_name = $row['pest_type_name'];
				break;
			}
		}
		
		return true;
	}


    public static function getServiceTypeListEx()
    {
        $list = array('list'=>array(),'options'=>array());
        $sql = "select id,rpt_cat, description from swo_service_type order by description";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $list['list'][$row['description']] = $row['description'];
                $list['options'][$row['description']] = array("data-rpt"=>$row['rpt_cat']);
            }
        }
        return $list;
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveFollowup($connection);
			$this->updateDmsUnitedLink($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function updateDmsUnitedLink(&$connection) {
		if ($this->scenario=='delete') {
			$sql = "delete from swo_followup_dms_united where dms_id=".$this->id;
			$connection->createCommand($sql)->execute();
		}
	}

	protected function saveFollowup(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_followup where id = :id";
				break;
			case 'new':
				$sql = "insert into swo_followup(
							entry_dt, type, company_id, company_name, content,job_report, cont_info, 
							resp_staff, resp_tech, mgr_notify, sch_dt,
							follow_staff, leader, follow_tech, fin_dt, follow_action, mgr_talk, 
							changex, tech_notify, fp_fin_dt, fp_call_dt, fp_cust_name, fp_comment,
							svc_next_dt, svc_call_dt, svc_cust_name, svc_comment, 
							mcard_remarks, mcard_staff,pest_type_id,pest_type_name,
							city, luu, lcu
						) values (
							:entry_dt, :type, :company_id, :company_name, :content,:job_report, :cont_info, 
							:resp_staff, :resp_tech, :mgr_notify, :sch_dt,
							:follow_staff, :leader, :follow_tech, :fin_dt, :follow_action, :mgr_talk, 
							:change, :tech_notify, :fp_fin_dt, :fp_call_dt, :fp_cust_name, :fp_comment,
							:svc_next_dt, :svc_call_dt, :svc_cust_name, :svc_comment, 
							:mcard_remarks, :mcard_staff,:pest_type_id,:pest_type_name,
							:city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_followup set
							entry_dt = :entry_dt, 
							type = :type, 
							company_id = :company_id, 
							company_name = :company_name, 
							content = :content, 
							job_report = :job_report, 
							cont_info = :cont_info, 
							resp_staff = :resp_staff, 
							resp_tech = :resp_tech, 
							mgr_notify = :mgr_notify, 
							sch_dt = :sch_dt,
							follow_staff = :follow_staff, 
							leader = :leader,
							follow_tech = :follow_tech, 
							fin_dt = :fin_dt, 
							follow_action = :follow_action, 
							mgr_talk = :mgr_talk, 
							changex = :change, 
							tech_notify = :tech_notify, 
							fp_fin_dt = :fp_fin_dt,
							fp_call_dt = :fp_call_dt,
							fp_cust_name = :fp_cust_name,
							fp_comment = :fp_comment,
							svc_next_dt = :svc_next_dt,
							svc_call_dt = :svc_call_dt,
							svc_cust_name = :svc_cust_name,
							svc_comment = :svc_comment,
							mcard_remarks = :mcard_remarks,
							mcard_staff = :mcard_staff,
							pest_type_id = :pest_type_id,
							pest_type_name = :pest_type_name,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':entry_dt')!==false) {
			$edate = General::toMyDate($this->entry_dt);
			$command->bindParam(':entry_dt',$edate,PDO::PARAM_STR);
		}
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_STR);
		if (strpos($sql,':company_id')!==false)
			$command->bindParam(':company_id',$this->company_id,PDO::PARAM_INT);
		if (strpos($sql,':company_name')!==false)
			$command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
		if (strpos($sql,':content')!==false)
			$command->bindParam(':content',$this->content,PDO::PARAM_STR);
		if (strpos($sql,':job_report')!==false)
			$command->bindParam(':job_report',$this->job_report,PDO::PARAM_STR);
		if (strpos($sql,':cont_info')!==false)
			$command->bindParam(':cont_info',$this->cont_info,PDO::PARAM_STR);
		if (strpos($sql,':pest_type_id')!==false){
            $id_str = empty($this->pest_type_id)?null:implode(",",$this->pest_type_id);
            $command->bindParam(':pest_type_id',$id_str,PDO::PARAM_STR);
        }
		if (strpos($sql,':pest_type_name')!==false){
            $name_str = empty($this->pest_type_name)?null:implode("、",$this->pest_type_name);
            $command->bindParam(':pest_type_name',$name_str,PDO::PARAM_STR);
        }
		if (strpos($sql,':resp_staff')!==false)
			$command->bindParam(':resp_staff',$this->resp_staff,PDO::PARAM_STR);
		if (strpos($sql,':resp_tech')!==false)
			$command->bindParam(':resp_tech',$this->resp_tech,PDO::PARAM_STR);
		if (strpos($sql,':mgr_notify')!==false)
			$command->bindParam(':mgr_notify',$this->mgr_notify,PDO::PARAM_STR);
		if (strpos($sql,':sch_dt')!==false) {
			$sdate = General::toMyDate($this->sch_dt);
			$command->bindParam(':sch_dt',$sdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':follow_staff')!==false)
			$command->bindParam(':follow_staff',$this->follow_staff,PDO::PARAM_STR);
		if (strpos($sql,':leader')!==false)
			$command->bindParam(':leader',$this->leader,PDO::PARAM_STR);
		if (strpos($sql,':follow_tech')!==false)
			$command->bindParam(':follow_tech',$this->follow_tech,PDO::PARAM_STR);
		if (strpos($sql,':fin_dt')!==false) {
			$fdate = General::toMyDate($this->fin_dt);
			$command->bindParam(':fin_dt',$fdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':follow_action')!==false)
			$command->bindParam(':follow_action',$this->follow_action,PDO::PARAM_STR);
		if (strpos($sql,':mgr_talk')!==false)
			$command->bindParam(':mgr_talk',$this->mgr_talk,PDO::PARAM_STR);
		if (strpos($sql,':change')!==false)
			$command->bindParam(':change',$this->change,PDO::PARAM_STR);
		if (strpos($sql,':tech_notify')!==false)
			$command->bindParam(':tech_notify',$this->tech_notify,PDO::PARAM_STR);
		if (strpos($sql,':fp_fin_dt')!==false) {
			$ffdate = General::toMyDate($this->fp_fin_dt);
			$command->bindParam(':fp_fin_dt',$ffdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':fp_call_dt')!==false) {
			$fcdate = General::toMyDate($this->fp_call_dt);
			$command->bindParam(':fp_call_dt',$fcdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':fp_cust_name')!==false)
			$command->bindParam(':fp_cust_name',$this->fp_cust_name,PDO::PARAM_STR);
		if (strpos($sql,':fp_comment')!==false)
			$command->bindParam(':fp_comment',$this->fp_comment,PDO::PARAM_STR);
		if (strpos($sql,':svc_next_dt')!==false) {
			$sndate = General::toMyDate($this->svc_next_dt);
			$command->bindParam(':svc_next_dt',$sndate,PDO::PARAM_STR);
		}
		if (strpos($sql,':svc_call_dt')!==false) {
			$scdate = General::toMyDate($this->svc_call_dt);
			$command->bindParam(':svc_call_dt',$scdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':svc_cust_name')!==false)
			$command->bindParam(':svc_cust_name',$this->svc_cust_name,PDO::PARAM_STR);
		if (strpos($sql,':svc_comment')!==false)
			$command->bindParam(':svc_comment',$this->svc_comment,PDO::PARAM_STR);
		if (strpos($sql,':mcard_remarks')!==false)
			$command->bindParam(':mcard_remarks',$this->mcard_remarks,PDO::PARAM_STR);
		if (strpos($sql,':mcard_staff')!==false)
			$command->bindParam(':mcard_staff',$this->mcard_staff,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		return true;
	}

	//刷新旧数据
    public function resetCompany(){
	    echo "start:<br/>";
        $rows = Yii::app()->db->createCommand()->select("id,company_name,city")->from("swo_followup")
            ->where("company_id=0 or company_id is null")->queryAll();
        if($rows){
            foreach ($rows as $row){
                echo "reset ID:".$row["id"]."；companyName:".$row["company_name"]."；";
                $code = self::getCodeForStr($row["company_name"]);
                $company_id=0;
                if(!empty($code)){
                    $companyRow = Yii::app()->db->createCommand()->select("id")->from("swo_company")
                        ->where("code=:code and city=:city",array(":code"=>$code,":city"=>$row["city"]))->queryRow();
                    $company_id = $companyRow?$companyRow["id"]:0;
                }
                if(empty($company_id)){
                    $companyRow = Yii::app()->db->createCommand()->select("id")->from("swo_company")
                        ->where("name=:name and city=:city",array(":name"=>$row["company_name"],":city"=>$row["city"]))->queryRow();
                    $company_id = $companyRow?$companyRow["id"]:0;
                }
                if(empty($company_id)){
                    echo "companyID:0；error!";
                }else{
                    echo "companyID:{$company_id}；success!";
                    Yii::app()->db->createCommand()->update("swo_followup",array(
                        "company_id"=>$company_id
                    ),"id=".$row["id"]);
                }
                echo "<br/>";
            }
        }
	    echo "end<br/>";
    }

    public static function getCodeForStr($str){
        $code = "";
        if(!empty($str)){
            $arr = str_split($str);
            foreach ($arr as $item){
                if(preg_match('/\w/', $item)){
                    $code.=$item;
                }else{
                    break;
                }
            }
        }
        return $code;
    }
}
