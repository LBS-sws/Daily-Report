<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class QcForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $entry_dt;
	public $job_staff;
	public $team;
	public $month;
	public $company_id;
	public $company_name;
	public $service_score;
	public $cust_score;
	public $cust_comment;
	public $qc_result;
	public $env_grade;
	public $qc_dt;
	public $cust_sign;
	public $qc_staff;
	public $remarks;
	public $service_type;

	public $no_of_attm = 0;
	public $docType = 'QC';
	public $files;
	public $removeFileId = 0;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('qc','Record ID'),
			'entry_dt'=>Yii::t('qc','Entry Date').' '.Yii::t('misc','(Y/M/D)'),
			'job_staff'=>Yii::t('qc','Resp. Staff'),
			'team'=>Yii::t('qc','Team'),
			'month'=>Yii::t('qc','Month'),
			'input_dt'=>Yii::t('qc','Input Date').' '.Yii::t('misc','(Y/M/D)'),
			'company_name'=>Yii::t('qc','Customer'),
			'service_score'=>Yii::t('qc','Service Score'),
			'cust_score'=>Yii::t('qc','Customer Score'),
			'cust_comment'=>Yii::t('qc','Customer Comment'),
			'qc_result'=>Yii::t('qc','QC Result'),
			'env_grade'=>Yii::t('qc','Env. Grade').' ABC',
			'qc_dt'=>Yii::t('qc','QC Date').' '.Yii::t('misc','(Y/M/D)'),
			'cust_sign'=>Yii::t('qc','Signature'),
			'qc_staff'=>Yii::t('qc','QC Staff'),
			'remarks'=>Yii::t('qc','Remarks'),
			'service_type'=>Yii::t('qc','Service Type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, team, month, env_grade, cust_sign, qc_staff, company_id, cust_comment, 
				remarks, service_type
				','safe'),
			array('docType, files, removeFileId, no_of_attm','safe'),
			array('job_staff, company_name','required'),
			array('entry_dt','date','allowEmpty'=>false,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('qc_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
			array('service_score, qc_result, cust_score','numerical','allowEmpty'=>true,'integerOnly'=>true),
		);
	}

	public function retrieveData($index)
	{
		$user = Yii::app()->user->id;
		$allcond = Yii::app()->user->validFunction('CN02') ? "" : "and lcu='$user'";
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql = "select *,docman$suffix.countdoc('QC',id) as no_of_attm from swo_qc where id=$index and city in ($city) $allcond ";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->entry_dt = General::toDate($row['entry_dt']);
				$this->job_staff = $row['job_staff'];
				$this->team = $row['team'];
				$this->month = $row['month'];
				$this->company_id = $row['company_id'];
				$this->company_name = $row['company_name'];
				$this->service_score = $row['service_score'];
				$this->cust_comment = $row['cust_comment'];
				$this->cust_score = $row['cust_score'];
				$this->qc_result = $row['qc_result'];
				$this->env_grade = $row['env_grade'];
				$this->qc_dt = General::toDate($row['qc_dt']);
				$this->cust_sign = $row['cust_sign'];
				$this->qc_staff = $row['qc_staff'];
				$this->remarks = $row['remarks'];
				$this->service_type = $row['service_type'];
				$this->no_of_attm = $row['no_of_attm'];
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
			$this->saveQc($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveQc(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_qc where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_qc(
							entry_dt, job_staff, team, month, input_dt, company_id, company_name, service_score,
							cust_comment, qc_result, env_grade, qc_dt, cust_sign, qc_staff, cust_score, remarks, 
							service_type, city, luu, lcu
						) values (
							:entry_dt, :job_staff, :team, :month, null, :company_id, :company_name, :service_score,
							:cust_comment, :qc_result, :env_grade, :qc_dt, :cust_sign, :qc_staff, :cust_score, :remarks, 
							:service_type, :city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_qc set
							entry_dt = :entry_dt, 
							job_staff = :job_staff, 
							team = :team, 
							month = :month, 
							company_id = :company_id, 
							company_name = :company_name, 
							service_score = :service_score,
							cust_score = :cust_score, 
							cust_comment = :cust_comment, 
							qc_result = :qc_result, 
							env_grade = :env_grade, 
							qc_dt = :qc_dt, 
							cust_sign = :cust_sign, 
							qc_staff = :qc_staff, 
							remarks = :remarks,
							service_type = :service_type,
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
		if (strpos($sql,':job_staff')!==false)
			$command->bindParam(':job_staff',$this->job_staff,PDO::PARAM_STR);
		if (strpos($sql,':team')!==false)
			$command->bindParam(':team',$this->team,PDO::PARAM_STR);
		if (strpos($sql,':month')!==false)
			$command->bindParam(':month',$this->month,PDO::PARAM_STR);
		if (strpos($sql,':company_id')!==false) {
			$cid = General::toMyNumber($this->company_id);
			$command->bindParam(':company_id',$cid,PDO::PARAM_INT);
		}
		if (strpos($sql,':company_name')!==false)
			$command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
		if (strpos($sql,':service_type')!==false)
			$command->bindParam(':service_type',$this->service_type,PDO::PARAM_STR);
		if (strpos($sql,':service_score')!==false)
			$command->bindParam(':service_score',$this->service_score,PDO::PARAM_STR);
		if (strpos($sql,':cust_score')!==false)
			$command->bindParam(':cust_score',$this->cust_score,PDO::PARAM_STR);
		if (strpos($sql,':cust_comment')!==false)
			$command->bindParam(':cust_comment',$this->cust_comment,PDO::PARAM_STR);
		if (strpos($sql,':qc_result')!==false)
			$command->bindParam(':qc_result',$this->qc_result,PDO::PARAM_STR);
		if (strpos($sql,':env_grade')!==false)
			$command->bindParam(':env_grade',$this->env_grade,PDO::PARAM_STR);
		if (strpos($sql,':qc_dt')!==false) {
			$qdate = General::toMyDate($this->qc_dt);
			$command->bindParam(':qc_dt',$qdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':cust_sign')!==false)
			$command->bindParam(':cust_sign',$this->cust_sign,PDO::PARAM_STR);
		if (strpos($sql,':qc_staff')!==false)
			$command->bindParam(':qc_staff',$this->qc_staff,PDO::PARAM_INT);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
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

	public function saveFiles() {
		$docman = new DocMan();
		foreach ($this->files as $file) {
			$docman->save($data);
		}
	}
}
