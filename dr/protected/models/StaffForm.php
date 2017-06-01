<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class StaffForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $code;
	public $name;
	public $position;
	public $join_dt;
	public $ctrt_start_dt;
	public $ctrt_renew_dt;
	public $ctrt_period;
	public $email;
	public $leave_dt;
	public $leave_reason;
	public $remarks;
	public $staff_type;
	public $leader;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'code'=>Yii::t('staff','Code'),
			'name'=>Yii::t('staff','Name'),
			'position'=>Yii::t('staff','Position'),
			'join_dt'=>Yii::t('staff','Join Date').' '.Yii::t('misc','(Y/M/D)'),
			'ctrt_start_dt'=>Yii::t('staff','Cont. Start Date').' '.Yii::t('misc','(Y/M/D)'),
			'ctrt_renew_dt'=>Yii::t('staff','Cont. Renew Date').' '.Yii::t('misc','(Y/M/D)'),
			'ctrt_period'=>Yii::t('staff','Cont. Period'),
			'email'=>Yii::t('staff','Email'),
			'leave_dt'=>Yii::t('staff','Leave Date').' '.Yii::t('misc','(Y/M/D)'),
			'leave_reason'=>Yii::t('staff','Leave Reason'),
			'remarks'=>Yii::t('staff','Remarks'),
			'staff_type'=>Yii::t('staff','Staff Type'),
			'leader'=>Yii::t('staff','Team/Group Leader'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
			array('name','required'),
/*
			array('code','unique','allowEmpty'=>true,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'Staff',
					'on'=>'new',
				),
*/	
			array('code','validateCode'),
//			array('code','safe','on'=>'edit'),
			array('ctrt_period','numerical','allowEmpty'=>true,'integerOnly'=>true),
			array('ctrt_period','in','range'=>range(0,600)),
			array('join_dt, ctrt_start_dt, ctrt_renew_dt, leave_dt','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d','yyyy-M-d',),
			),
//			array('email','email','allowEmpty'=>true),
		);
	}

	public function validateCode($attribute, $params) {
		$code = $this->$attribute;
		$city = Yii::app()->user->city();
		if (!empty($code)) {
			switch ($this->scenario) {
				case 'new':
					if (Staff::model()->exists('code=? and city=?',array($code,$city))) {
						$this->addError($attribute, Yii::t('staff','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
				case 'edit':
					if (Staff::model()->exists('code=? and city=? and id<>?',array($code,$city,$this->id))) {
						$this->addError($attribute, Yii::t('staff','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
			}
		}
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city_allow();
		$sql = "select * from swo_staff where id=$index and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->name = $row['name'];
				$this->position = $row['position'];
				$this->join_dt = General::toDate($row['join_dt']);
				$this->ctrt_start_dt = General::toDate($row['ctrt_start_dt']);
				$this->ctrt_renew_dt = General::toDate($row['ctrt_renew_dt']);
				$this->ctrt_period = $row['ctrt_period'];
				$this->email = $row['email'];
				$this->leave_dt = General::toDate($row['leave_dt']);
				$this->leave_reason = $row['leave_reason'];
				$this->remarks = $row['remarks'];
				$this->staff_type = $row['staff_type'];
				$this->leader = $row['leader'];
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
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from swo_staff where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into swo_staff(
							name, code, position, join_dt, ctrt_start_dt, ctrt_renew_dt, ctrt_period, email, leave_dt,
							leave_reason, remarks, staff_type, leader, city, luu, lcu
						) values (
							:name, :code, :position, :join_dt, :ctrt_start_dt, :ctrt_renew_dt, :ctrt_period, :email, :leave_dt,
							:leave_reason, :remarks, :staff_type, :leader, :city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update swo_staff set
							name = :name, 
							code = :code, 
							position = :position,
							join_dt = :join_dt,
							ctrt_start_dt = :ctrt_start_dt, 
							ctrt_renew_dt = :ctrt_renew_dt, 
							ctrt_period = :ctrt_period,
							email = :email,
							leave_dt = :leave_dt,
							leave_reason = :leave_reason,
							remarks = :remarks,
							staff_type = :staff_type,
							leader = :leader,
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
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':code')!==false)
			$command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':position')!==false)
			$command->bindParam(':position',$this->position,PDO::PARAM_STR);
		if (strpos($sql,':join_dt')!==false) {
			$jdate = General::toMyDate($this->join_dt);
			$command->bindParam(':join_dt',$jdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_start_dt')!==false) {
			$csdate = General::toMyDate($this->ctrt_start_dt);
			$command->bindParam(':ctrt_start_dt',$csdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_renew_dt')!==false) {
			$crdate = General::toMyDate($this->ctrt_renew_dt);
			$command->bindParam(':ctrt_renew_dt',$crdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_period')!==false) {
			$cp = General::toMyNumber($this->ctrt_period);
			$command->bindParam(':ctrt_period',$cp,PDO::PARAM_INT);
		}
		if (strpos($sql,':email')!==false)
			$command->bindParam(':email',$this->email,PDO::PARAM_STR);
		if (strpos($sql,':leave_dt')!==false) {
			$ldate = General::toMyDate($this->leave_dt);
			$command->bindParam(':leave_dt',$ldate,PDO::PARAM_STR);
		}
		if (strpos($sql,':leave_reason')!==false)
			$command->bindParam(':leave_reason',$this->leave_reason,PDO::PARAM_STR);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
		if (strpos($sql,':staff_type')!==false)
			$command->bindParam(':staff_type',$this->staff_type,PDO::PARAM_STR);
		if (strpos($sql,':leader')!==false)
			$command->bindParam(':leader',$this->leader,PDO::PARAM_STR);
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
