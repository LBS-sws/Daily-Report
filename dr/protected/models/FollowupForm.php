<?php

class FollowupForm extends CFormModel
{
	public $id = 0;
	public $entry_dt;
	public $type = '';
	public $company_id = 0;
	public $company_name;
	public $content;
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
			'company_name'=>Yii::t('followup','Customer'),
			'content'=>Yii::t('followup','Content'),
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
			'svc_comment'=>Yii::t('followup','Comment'),
			'fp_fin_dt'=>Yii::t('followup','Finish Date').' '.Yii::t('misc','(Y/M/D)'),
			'fp_call_dt'=>Yii::t('followup','Follow up Date').' '.Yii::t('misc','(Y/M/D)'),
			'fp_cust_name'=>Yii::t('followup','Customer Name'),
			'fp_comment'=>Yii::t('followup','Comment'),
			'mcard_remarks'=>Yii::t('followup','Contend of Update to Job Card'),
			'mcard_staff'=>Yii::t('followup','Staff of Update Job Card'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, resp_staff, resp_tech, mgr_notify, follow_staff, follow_tech, follow_action,
				mgr_talk, change, tech_notify, cont_info, type, mcard_remarks, mcard_staff,
				fp_cust_name, fp_comment, svc_comment, svc_cust_name, company_id, leader
				','safe'),
			array('company_name, content, entry_dt','required'),
			array('entry_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('sch_dt, fin_dt, fp_fin_dt, fp_call_dt, svc_next_dt, svc_call_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
		);
	}

	public function retrieveData($index)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN01') ? "" : "and lcu='$user'";
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_followup where id=$index and city in ($city) $allcond";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->entry_dt = General::toDate($row['entry_dt']);
				$this->company_id = $row['company_id'];
				$this->company_name = $row['company_name'];
				$this->type = $row['type'];
				$this->content = $row['content'];
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
				break;
			}
		}
		
		return true;
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
				$sql = "delete from swo_followup where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_followup(
							entry_dt, type, company_id, company_name, content, cont_info, 
							resp_staff, resp_tech, mgr_notify, sch_dt,
							follow_staff, leader, follow_tech, fin_dt, follow_action, mgr_talk, 
							changex, tech_notify, fp_fin_dt, fp_call_dt, fp_cust_name, fp_comment,
							svc_next_dt, svc_call_dt, svc_cust_name, svc_comment, 
							mcard_remarks, mcard_staff,
							city, luu, lcu
						) values (
							:entry_dt, :type, :company_id, :company_name, :content, :cont_info, 
							:resp_staff, :resp_tech, :mgr_notify, :sch_dt,
							:follow_staff, :leader, :follow_tech, :fin_dt, :follow_action, :mgr_talk, 
							:change, :tech_notify, :fp_fin_dt, :fp_call_dt, :fp_cust_name, :fp_comment,
							:svc_next_dt, :svc_call_dt, :svc_cust_name, :svc_comment, 
							:mcard_remarks, :mcard_staff,
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
							luu = :luu 
						where id = :id and city = :city
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
		if (strpos($sql,':cont_info')!==false)
			$command->bindParam(':cont_info',$this->cont_info,PDO::PARAM_STR);
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
}
