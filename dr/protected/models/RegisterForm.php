<?php

class RegisterForm extends CFormModel
{
	/* User Fields */
	public $req_key;
	public $email;
	public $station_name;
	public $city;
	public $station_id;
	public $status;
	public $lcd;
	public $lud;
	public $lud2;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'req_key'=>Yii::t('register','Request Key'),
			'email'=>Yii::t('register','Email'),
			'station_name'=>Yii::t('register','Station Name'),
			'city'=>Yii::t('register','City'),
			'status'=>Yii::t('register','Status'),
			'lud'=>Yii::t('register','Register Date'),
			'lcd'=>Yii::t('register','Request Date'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('req_key, status, lud, station_id, lud2','safe'),
			array('email','email','allowEmpty'=>true),
			array('station_name,city','required'),
		);
	}

	public function retrieveData($index)
	{
		$sql = "select * from swo_station_request where req_key='".$index."'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			$timelimit = "-".Yii::app()->params['validRegDuration'];
			foreach ($rows as $row)
			{
				$sts = Yii::t('Register','Pending');
				if (!empty($row['station_id']) && $row['station_id'] != '--VOID--') $sts = Yii::t('Register','Complete');
				if (!empty($row['station_id']) && $row['station_id'] == '--VOID--') $sts = Yii::t('Register','Void');
				if (empty($row['station_id']) && strtotime($timelimit) > strtotime($row['lcd'])) $sts = Yii::t('Register','Expired');

				$this->req_key = $row['req_key'];
				$this->email = $row['email'];
				$this->station_name = $row['station_name'];
				$this->station_id = $row['station_id'];
				$this->city = $row['city'];
				$this->lcd = $row['lcd'];
				$this->lud = (empty($this->station_id)) ? '' : $row['lud'];
				$this->lud2 = $row['lud'];
				$this->status = $sts;
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
			$this->saveRegister($connection);
			$transaction->commit();
// Send email
//			if ($this->scenario=='new') {}
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveRegister(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'new':
				$sql = "insert into swo_station_request(
						req_key, email, station_name, city, lcu) values (
						:req_key, :email, :station_name, :city, :lcu)";
				break;
			case 'edit':
				$sql = "update swo_station_request set 
					station_name = :station_name, 
					city = :city,
					luu = :luu
					where req_key = :req_key and station_id is null";
				break;
			case 'void':
				$sql = "update swo_station_request set 
					station_id = '--VOID--', 
					luu = :luu
					where req_key = :req_key and station_id is null";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':req_key')!==false)
			$command->bindParam(':req_key',$this->req_key,PDO::PARAM_STR);
		if (strpos($sql,':email')!==false)
			$command->bindParam(':email',$this->email,PDO::PARAM_STR);
		if (strpos($sql,':station_name')!==false)
			$command->bindParam(':station_name',$this->station_name,PDO::PARAM_STR);
		if (strpos($sql,':station_id')!==false)
			$command->bindParam(':station_id',$this->station_id,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

		return true;
	}
}
